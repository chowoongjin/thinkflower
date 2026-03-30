<?php

namespace App\Services\Barobill;

use SoapClient;
use SoapFault;

abstract class BarobillSoapService
{
    protected function call(string $wsdl, string $method, array $parameters): mixed
    {
        if (!extension_loaded('soap')) {
            throw new \RuntimeException('바로빌 연동을 위해 soap 확장이 필요합니다.');
        }

        try {
            $client = new SoapClient($wsdl, [
                'trace' => false,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'encoding' => 'UTF-8',
            ]);

            return $client->__soapCall($method, [$parameters]);
        } catch (SoapFault $e) {
            throw new \RuntimeException('바로빌 SOAP 호출에 실패했습니다. method=' . $method . ', message=' . $e->getMessage(), 0, $e);
        }
    }

    protected function extractResult(mixed $response, string $field): mixed
    {
        if (is_object($response) && property_exists($response, $field)) {
            return $response->{$field};
        }

        if (is_array($response) && array_key_exists($field, $response)) {
            return $response[$field];
        }

        throw new \RuntimeException('바로빌 응답 구조를 해석할 수 없습니다. field=' . $field);
    }

    protected function digitsOnly(?string $value): string
    {
        return preg_replace('/[^0-9]/', '', (string) $value) ?: '';
    }
}
