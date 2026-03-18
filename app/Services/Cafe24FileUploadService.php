<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class Cafe24FileUploadService
{
    public const TYPE_BUSINESS_LICENSE = 'business-license';
    public const TYPE_PRODUCT_IMAGE = 'product-image';
    public const TYPE_DELIVERY_PHOTO = 'delivery-photo';
    public const TYPE_PHOTO_SHARE = 'photo_share';
    public const TYPE_BANNER_NOTICE = 'banner_notice';
    public const TYPE_BANNER_POPUP = 'banner_popup';

    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/svg+xml',
        'application/pdf',
    ];

    public function upload(UploadedFile $file, string $type): array
    {
        $this->validateType($file, $type);

        $tmpOriginalPath = null;
        $tmpUploadPath = null;

        $originalMimeType = $file->getMimeType();
        $originalExtension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'tmp');
        $originalName = $file->getClientOriginalName();
        $isPdf = $originalMimeType === 'application/pdf';

        try {
            $tmpOriginalPath = $this->moveToTemp($file);

            if ($isPdf) {
                $tmpUploadPath = $this->convertPdfFirstPageToJpg($tmpOriginalPath);
                $tmpUploadPath = $this->resizeImageIfNeeded($tmpUploadPath);
                $extension = 'jpg';
                $mimeType = 'image/jpeg';
            } else {
                $tmpUploadPath = $this->resizeImageIfNeeded($tmpOriginalPath);
                $extension = $originalExtension;
                $mimeType = $originalMimeType;
            }

            $remoteRelativePath = $this->buildRemotePath($type, $extension);
            $this->uploadToFtp($tmpUploadPath, $remoteRelativePath);

            return [
                'disk' => 'cafe24',
                'type' => $type,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'relative_path' => $remoteRelativePath,
                'url' => $this->buildCdnUrl($remoteRelativePath),
            ];
        } finally {
            $this->deleteLocalFile($tmpOriginalPath);

            if ($tmpUploadPath && $tmpUploadPath !== $tmpOriginalPath) {
                $this->deleteLocalFile($tmpUploadPath);
            }
        }
    }

    public function storeTempUpload(UploadedFile $file, string $type): array
    {
        $this->validateType($file, $type);

        $originalMimeType = $file->getMimeType();
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'tmp');

        $tempDir = storage_path('app/tmp_uploads/' . date('Y/m/d'));

        if (!is_dir($tempDir) && !mkdir($tempDir, 0775, true) && !is_dir($tempDir)) {
            throw new \RuntimeException('임시 업로드 디렉터리를 생성할 수 없습니다. path=' . $tempDir);
        }

        $tempFilename = Str::uuid()->toString() . '.' . $extension;
        $storedFile = $file->move($tempDir, $tempFilename);

        return [
            'local_path' => $storedFile->getPathname(),
            'original_name' => $originalName,
            'original_mime_type' => $originalMimeType,
        ];
    }

    public function uploadFromLocalPath(
        string $localPath,
        string $type,
        ?string $originalName = null,
        ?string $originalMimeType = null
    ): array {
        if (!is_file($localPath) || !is_readable($localPath)) {
            throw new RuntimeException('업로드할 로컬 파일을 읽을 수 없습니다. path=' . $localPath);
        }

        $tmpOriginalPath = $localPath;
        $tmpUploadPath = null;

        $originalMimeType = $originalMimeType ?: mime_content_type($localPath) ?: 'application/octet-stream';
        $originalExtension = strtolower(pathinfo($localPath, PATHINFO_EXTENSION) ?: 'tmp');
        $originalName = $originalName ?: basename($localPath);
        $isPdf = $originalMimeType === 'application/pdf';

        try {
            if ($isPdf) {
                $tmpUploadPath = $this->convertPdfFirstPageToJpg($tmpOriginalPath);
                $tmpUploadPath = $this->resizeImageIfNeeded($tmpUploadPath);
                $extension = 'jpg';
                $mimeType = 'image/jpeg';
            } else {
                $tmpUploadPath = $this->resizeImageIfNeeded($tmpOriginalPath);
                $extension = $originalExtension;
                $mimeType = $originalMimeType;
            }

            $remoteRelativePath = $this->buildRemotePath($type, $extension);
            $this->uploadToFtp($tmpUploadPath, $remoteRelativePath);

            return [
                'disk' => 'cafe24',
                'type' => $type,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'relative_path' => $remoteRelativePath,
                'url' => $this->buildCdnUrl($remoteRelativePath),
            ];
        } finally {
            $this->deleteLocalFile($tmpOriginalPath);

            if ($tmpUploadPath && $tmpUploadPath !== $tmpOriginalPath) {
                $this->deleteLocalFile($tmpUploadPath);
            }
        }
    }

    protected function resizeImageIfNeeded(string $imagePath): string
    {
        if (!extension_loaded('imagick')) {
            throw new RuntimeException('이미지 리사이즈를 하려면 imagick 확장이 필요합니다.');
        }

        if (!is_file($imagePath) || !is_readable($imagePath)) {
            throw new RuntimeException('리사이즈할 이미지 파일을 읽을 수 없습니다. path=' . $imagePath);
        }

        $imagick = new \Imagick($imagePath);

        try {
            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();
            $maxSize = 1500;

            if ($width <= $maxSize && $height <= $maxSize) {
                return $imagePath;
            }

            $imagick->setImageOrientation(\Imagick::ORIENTATION_UNDEFINED);
            $imagick->thumbnailImage($maxSize, $maxSize, true, true);

            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            $outputPath = storage_path('app/tmp_uploads/' . \Illuminate\Support\Str::uuid() . '.' . $extension);

            if (in_array($extension, ['jpg', 'jpeg'], true)) {
                $imagick->setImageFormat('jpeg');
                $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
                $imagick->setImageCompressionQuality(90);
            } elseif ($extension === 'png') {
                $imagick->setImageFormat('png');
                $imagick->setOption('png:compression-level', '9');
            } elseif ($extension === 'webp') {
                $imagick->setImageFormat('webp');
                $imagick->setImageCompressionQuality(90);
            }

            $imagick->writeImage($outputPath);

            return $outputPath;
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }
    }

    protected function validateType(UploadedFile $file, string $type): void
    {
        $allowedTypes = [
            self::TYPE_BUSINESS_LICENSE,
            self::TYPE_PRODUCT_IMAGE,
            self::TYPE_DELIVERY_PHOTO,
            self::TYPE_PHOTO_SHARE,
            self::TYPE_BANNER_NOTICE,
            self::TYPE_BANNER_POPUP,
        ];

        if (!in_array($type, $allowedTypes, true)) {
            throw new RuntimeException('지원하지 않는 업로드 유형입니다.');
        }

        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, $this->allowedMimeTypes, true)) {
            throw new RuntimeException('이미지 또는 PDF 파일만 업로드할 수 있습니다.');
        }
    }

    protected function moveToTemp(UploadedFile $file): string
    {
        $tempDir = storage_path('app/tmp_uploads');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'tmp');
        $tempPath = $tempDir . '/' . Str::uuid() . '.' . $extension;

        $file->move($tempDir, basename($tempPath));

        return $tempPath;
    }

    protected function convertPdfFirstPageToJpg(string $pdfPath): string
    {
        if (!extension_loaded('imagick')) {
            throw new RuntimeException('PDF를 이미지로 변환하려면 imagick 확장이 필요합니다.');
        }

        $outputPath = storage_path('app/tmp_uploads/' . Str::uuid() . '.jpg');

        $imagick = new \Imagick();
        $imagick->setResolution(200, 200);
        $imagick->readImage($pdfPath . '[0]');
        $imagick->setImageBackgroundColor('white');
        $imagick = $imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
        $imagick->setImageFormat('jpg');
        $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $imagick->setImageCompressionQuality(90);
        $imagick->writeImage($outputPath);
        $imagick->clear();
        $imagick->destroy();

        return $outputPath;
    }

    protected function buildRemotePath(string $type, string $extension): string
    {
        $folder = match ($type) {
            self::TYPE_BUSINESS_LICENSE => 'business-license',
            self::TYPE_PRODUCT_IMAGE => 'product-image',
            self::TYPE_DELIVERY_PHOTO => 'delivery-photo',
            self::TYPE_PHOTO_SHARE => 'photo_share',
            self::TYPE_BANNER_NOTICE => 'banner/notice',
            self::TYPE_BANNER_POPUP => 'banner/popup',
        };

        $datePath = now()->format('Y/m');
        $filename = now()->format('YmdHis') . '_' . Str::random(20) . '.' . $extension;

        return trim($folder . '/' . $datePath . '/' . $filename, '/');
    }

    protected function buildCdnUrl(string $relativePath): string
    {
        $base = config('cafe24.cdn_url');

        return $base . '/' . ltrim($relativePath, '/');
    }

    protected function uploadToFtp(string $localPath, string $remoteRelativePath): void
    {
        $host = config('cafe24.host');
        $username = config('cafe24.username');
        $password = config('cafe24.password');
        $baseDir = trim((string) config('cafe24.base_dir', ''), '/');
        $useSsl = (bool) config('cafe24.ssl', false);

        $ftp = $useSsl ? @ftp_ssl_connect($host) : @ftp_connect($host);

        if (!$ftp) {
            throw new RuntimeException('카페24 FTP 서버에 연결하지 못했습니다.');
        }

        try {
            if (!@ftp_login($ftp, $username, $password)) {
                throw new RuntimeException('카페24 FTP 로그인에 실패했습니다.');
            }

            if (!@ftp_pasv($ftp, true)) {
                throw new RuntimeException('FTP passive 모드 설정에 실패했습니다.');
            }

            $currentDir = @ftp_pwd($ftp);

            if ($baseDir !== '') {
                $this->ensureRemoteDirectory($ftp, $baseDir);

                if (!@ftp_chdir($ftp, $baseDir)) {
                    throw new RuntimeException('FTP 기본 디렉터리로 이동하지 못했습니다. baseDir=' . $baseDir);
                }
            }

            $remoteDir = trim(dirname($remoteRelativePath), '/');
            $remoteFile = basename($remoteRelativePath);

            if ($remoteDir !== '' && $remoteDir !== '.') {
                $this->ensureRemoteDirectory($ftp, $remoteDir);

                if (!@ftp_chdir($ftp, $remoteDir)) {
                    throw new RuntimeException('업로드 디렉터리로 이동하지 못했습니다. remoteDir=' . $remoteDir);
                }
            }

            if (!is_file($localPath) || !is_readable($localPath)) {
                throw new RuntimeException('로컬 업로드 대상 파일이 존재하지 않거나 읽을 수 없습니다. localPath=' . $localPath);
            }

            if (!@ftp_put($ftp, $remoteFile, $localPath, FTP_BINARY)) {
                $pwd = @ftp_pwd($ftp);
                throw new RuntimeException(
                    'FTP 파일 업로드에 실패했습니다. '
                    . 'pwd=' . ($pwd ?: 'unknown')
                    . ', remoteFile=' . $remoteFile
                    . ', localPath=' . $localPath
                );
            }

            if ($currentDir) {
                @ftp_chdir($ftp, $currentDir);
            }
        } finally {
            @ftp_close($ftp);
        }
    }

    protected function ensureRemoteDirectory($ftp, string $directory): void
    {
        $directory = trim($directory, '/');

        if ($directory === '' || $directory === '.') {
            return;
        }

        $parts = explode('/', $directory);
        $originalDir = @ftp_pwd($ftp);

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            if (@ftp_chdir($ftp, $part)) {
                continue;
            }

            if (!@ftp_mkdir($ftp, $part)) {
                throw new RuntimeException('FTP 디렉터리 생성에 실패했습니다. dir=' . $part);
            }

            if (!@ftp_chdir($ftp, $part)) {
                throw new RuntimeException('생성된 FTP 디렉터리로 이동하지 못했습니다. dir=' . $part);
            }
        }

        if ($originalDir) {
            @ftp_chdir($ftp, $originalDir);
        }
    }

    protected function deleteLocalFile(?string $path): void
    {
        if ($path && is_file($path)) {
            @unlink($path);
        }
    }
}
