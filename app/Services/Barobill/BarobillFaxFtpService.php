<?php

namespace App\Services\Barobill;

use Illuminate\Support\Str;

class BarobillFaxFtpService
{
    public function uploadPdf(string $localPath, string $orderNo): string
    {
        if (!is_file($localPath) || !is_readable($localPath)) {
            throw new \RuntimeException('업로드할 팩스 PDF 파일을 읽을 수 없습니다. path=' . $localPath);
        }

        $host = (string) config('barobill.fax.ftp_host');
        $port = (int) config('barobill.fax.ftp_port', 21);
        $username = (string) config('barobill.fax.ftp_username');
        $password = (string) config('barobill.fax.ftp_password');
        $baseDir = trim((string) config('barobill.fax.ftp_base_dir', '/'), '/');
        $useSsl = (bool) config('barobill.fax.ftp_ssl', false);
        $prefix = trim((string) config('barobill.fax.filename_prefix', 'order-fax'), '-_ ');

        if ($host === '' || $username === '' || $password === '') {
            throw new \RuntimeException('바로빌 팩스 FTP 설정이 비어 있습니다.');
        }

        $fileName = sprintf(
            '%s_%s_%s.pdf',
            $prefix !== '' ? $prefix : 'order-fax',
            $orderNo,
            Str::random(16)
        );

        $ftp = $useSsl
            ? @ftp_ssl_connect($host, $port, 30)
            : @ftp_connect($host, $port, 30);

        if (!$ftp) {
            throw new \RuntimeException('바로빌 팩스 FTP 서버에 연결하지 못했습니다.');
        }

        try {
            if (!@ftp_login($ftp, $username, $password)) {
                throw new \RuntimeException('바로빌 팩스 FTP 로그인에 실패했습니다.');
            }

            if (!@ftp_pasv($ftp, true)) {
                throw new \RuntimeException('바로빌 팩스 FTP passive 모드 설정에 실패했습니다.');
            }

            if ($baseDir !== '') {
                $this->ensureRemoteDirectory($ftp, $baseDir);

                if (!@ftp_chdir($ftp, $baseDir)) {
                    throw new \RuntimeException('바로빌 팩스 FTP 기본 디렉터리로 이동하지 못했습니다. baseDir=' . $baseDir);
                }
            }

            if (!@ftp_put($ftp, $fileName, $localPath, FTP_BINARY)) {
                throw new \RuntimeException('바로빌 팩스 FTP 파일 업로드에 실패했습니다. file=' . $fileName);
            }
        } finally {
            @ftp_close($ftp);
        }

        return $fileName;
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
                throw new \RuntimeException('바로빌 팩스 FTP 디렉터리 생성에 실패했습니다. dir=' . $part);
            }

            if (!@ftp_chdir($ftp, $part)) {
                throw new \RuntimeException('생성한 바로빌 팩스 FTP 디렉터리로 이동하지 못했습니다. dir=' . $part);
            }
        }

        if ($originalDir) {
            @ftp_chdir($ftp, $originalDir);
        }
    }
}
