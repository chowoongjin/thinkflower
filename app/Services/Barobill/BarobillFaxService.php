<?php

namespace App\Services\Barobill;

class BarobillFaxService extends BarobillSoapService
{
    public function sendFromFtp(
        string $fileName,
        string $toNumber,
        string $receiveCorp,
        string $receiveName,
        string $refKey
    ): string {
        $certKey = (string) config('barobill.cert_key');
        $corpNum = $this->digitsOnly((string) config('barobill.corp_num'));
        $senderId = (string) config('barobill.sender_id');
        $fromNumber = $this->digitsOnly((string) config('barobill.fax.from_number'));
        $resolvedToNumber = $this->digitsOnly($toNumber);
        $wsdl = (string) config('barobill.fax.wsdl');

        if ($certKey === '' || $corpNum === '' || $senderId === '' || $fromNumber === '' || $resolvedToNumber === '' || $wsdl === '') {
            throw new \RuntimeException('바로빌 팩스 SOAP 설정이 비어 있습니다.');
        }

        $response = $this->call($wsdl, 'SendFaxFromFTP', [
            'CERTKEY' => $certKey,
            'CorpNum' => $corpNum,
            'SenderID' => $senderId,
            'FileName' => $fileName,
            'FromNumber' => $fromNumber,
            'ToNumber' => $resolvedToNumber,
            'ReceiveCorp' => $receiveCorp,
            'ReceiveName' => $receiveName,
            'SendDT' => '',
            'RefKey' => $refKey,
        ]);

        $result = (string) $this->extractResult($response, 'SendFaxFromFTPResult');

        if (is_numeric($result) && (int) $result < 0) {
            throw new \RuntimeException($this->resolveErrorMessage((int) $result, $wsdl));
        }

        if ($result === '') {
            throw new \RuntimeException('바로빌 팩스 전송키를 받지 못했습니다.');
        }

        return $result;
    }

    public function waitUntilCompleted(string $sendKey): array
    {
        $pollInterval = max(1, (int) config('barobill.fax.poll_interval_seconds', 5));
        $timeout = max(5, (int) config('barobill.fax.poll_timeout_seconds', 120));
        $startedAt = microtime(true);

        do {
            $message = $this->getFaxMessageEx2($sendKey);

            if (!empty($message['EndDT'])) {
                $sendPageCount = (int) ($message['SendPageCount'] ?? 0);
                $successPageCount = (int) ($message['SuccessPageCount'] ?? 0);

                if ($sendPageCount > 0 && $sendPageCount === $successPageCount) {
                    return $message;
                }

                $reason = (string) ($message['SendResult'] ?? '');
                throw new \RuntimeException($reason !== '' ? $reason : '바로빌 팩스 전송이 실패했습니다.');
            }

            if ((microtime(true) - $startedAt) >= $timeout) {
                throw new \RuntimeException('바로빌 팩스 전송 결과 대기 시간이 초과되었습니다.');
            }

            sleep($pollInterval);
        } while (true);
    }

    protected function getFaxMessageEx2(string $sendKey): array
    {
        $certKey = (string) config('barobill.cert_key');
        $corpNum = $this->digitsOnly((string) config('barobill.corp_num'));
        $wsdl = (string) config('barobill.fax.wsdl');

        $response = $this->call($wsdl, 'GetFaxMessageEx2', [
            'CERTKEY' => $certKey,
            'CorpNum' => $corpNum,
            'SendKey' => $sendKey,
        ]);

        $result = $this->extractResult($response, 'GetFaxMessageEx2Result');
        $payload = json_decode(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true) ?: [];

        if (isset($payload['SendState']) && is_numeric((string) $payload['SendState']) && (int) $payload['SendState'] < 0) {
            throw new \RuntimeException($this->resolveErrorMessage((int) $payload['SendState'], $wsdl));
        }

        return $payload;
    }

    protected function resolveErrorMessage(int $errorCode, string $wsdl): string
    {
        try {
            $response = $this->call($wsdl, 'GetErrString', [
                'CERTKEY' => (string) config('barobill.cert_key'),
                'ErrCode' => $errorCode,
            ]);

            $message = (string) $this->extractResult($response, 'GetErrStringResult');

            return $message !== '' ? $message : ('바로빌 오류코드: ' . $errorCode);
        } catch (\Throwable) {
            return '바로빌 오류코드: ' . $errorCode;
        }
    }
}
