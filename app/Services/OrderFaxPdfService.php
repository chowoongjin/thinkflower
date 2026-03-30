<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shop;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Str;

class OrderFaxPdfService
{
    public function __construct(
        protected ShopDisplayNameService $shopDisplayNameService
    ) {
    }

    public function generate(Order $order, Shop $receiverShop): string
    {
        $order->loadMissing(['ordererShop', 'receiverShop']);

        [$fontRegular, $fontBold, $fontFamily] = $this->resolveFontPaths();
        $dompdfRuntimeDir = $this->ensureRuntimeDirectories();

        $logoPath = public_path('assets/img/logo.png');
        $logoDataUri = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath))
            : null;

        $html = view('admin.documents.order-fax-v2', [
            'order' => $order,
            'ordererShop' => $order->ordererShop,
            'receiverShop' => $receiverShop,
            'ordererDisplayName' => $this->shopDisplayNameService->format($order->ordererShop),
            'receiverDisplayName' => $this->shopDisplayNameService->format($receiverShop),
            'fontRegular' => $fontRegular,
            'fontBold' => $fontBold,
            'fontFamily' => $fontFamily,
            'logoDataUri' => $logoDataUri,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', $fontRegular && $fontBold ? $fontFamily : 'DejaVu Sans');
        $options->setChroot(base_path());
        $options->set('fontDir', $dompdfRuntimeDir . '/fonts');
        $options->set('fontCache', $dompdfRuntimeDir . '/fonts');
        $options->set('tempDir', $dompdfRuntimeDir . '/temp');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();

        $directory = $dompdfRuntimeDir . '/output';

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('팩스 PDF 임시 디렉터리를 생성할 수 없습니다. path=' . $directory);
        }

        $path = $directory . '/' . $order->order_no . '_' . Str::uuid() . '.pdf';
        file_put_contents($path, $dompdf->output());

        return $path;
    }

    protected function resolveFontPaths(): array
    {
        $families = [
            [
                'family' => 'NotoSansKR',
                'regular' => [
                    public_path('assets/fonts/NotoSansKR-Regular.ttf'),
                    resource_path('fonts/NotoSansKR-Regular.ttf'),
                ],
                'bold' => [
                    public_path('assets/fonts/NotoSansKR-Bold.ttf'),
                    resource_path('fonts/NotoSansKR-Bold.ttf'),
                ],
            ],
            [
                // NotoSansKR 파일이 없으면 기존 한글 TTF를 같은 family 이름으로 매핑한다.
                'family' => 'NotoSansKR',
                'regular' => [
                    public_path('assets/fonts/NanumGothic-Regular.ttf'),
                    resource_path('fonts/NanumGothic-Regular.ttf'),
                ],
                'bold' => [
                    public_path('assets/fonts/NanumGothic-Bold.ttf'),
                    resource_path('fonts/NanumGothic-Bold.ttf'),
                ],
            ],
        ];

        foreach ($families as $candidates) {
            $fontRegular = $this->firstExistingPath($candidates['regular']);
            $fontBold = $this->firstExistingPath($candidates['bold']);

            if ($fontRegular) {
                return [$fontRegular, $fontBold ?: $fontRegular, $candidates['family']];
            }
        }

        return [null, null, 'DejaVu Sans'];
    }

    protected function firstExistingPath(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            $resolved = realpath($candidate);

            if ($resolved && is_file($resolved)) {
                return $resolved;
            }
        }

        return null;
    }

    protected function ensureRuntimeDirectories(): string
    {
        $runtimeDir = storage_path('app/dompdf');
        $directories = [
            $runtimeDir,
            $runtimeDir . '/fonts',
            $runtimeDir . '/temp',
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                throw new \RuntimeException('Dompdf 런타임 디렉터리를 생성할 수 없습니다. path=' . $directory);
            }
        }

        return $runtimeDir;
    }
}
