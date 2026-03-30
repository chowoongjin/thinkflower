<?php

namespace App\Services\Barobill;

use App\Models\Order;
use App\Models\Shop;
use App\Services\ShopDisplayNameService;

class BarobillKakaotalkService extends BarobillSoapService
{
    public function __construct(
        protected ShopDisplayNameService $shopDisplayNameService
    ) {
    }

    public function sendAssignmentNotice(Order $order, Shop $receiverShop, string $refKey): array
    {
        if (!(bool) config('barobill.kakaotalk.enabled')) {
            return [
                'status' => 'skipped',
                'reason' => 'disabled',
            ];
        }

        $certKey = (string) config('barobill.cert_key');
        $corpNum = $this->digitsOnly((string) config('barobill.corp_num'));
        $senderId = (string) config('barobill.sender_id');
        $yellowId = (string) config('barobill.kakaotalk.yellow_id');
        $templateName = (string) config('barobill.kakaotalk.template_name');
        $messageTemplate = (string) config('barobill.kakaotalk.message_template');
        $wsdl = (string) config('barobill.kakaotalk.wsdl');
        $receiverNum = $this->digitsOnly($receiverShop->main_phone);

        if (
            $certKey === '' || $corpNum === '' || $senderId === '' || $yellowId === ''
            || $templateName === '' || $messageTemplate === '' || $wsdl === '' || $receiverNum === ''
        ) {
            throw new \RuntimeException('바로빌 알림톡 설정이 비어 있거나 수주사 휴대폰 번호가 없습니다.');
        }

        $tokens = $this->buildTokens($order, $receiverShop);

        $message = [
            'ReceiverNum' => $receiverNum,
            'ReceiverName' => $receiverShop->owner_name ?: $receiverShop->shop_name,
            'Title' => $this->replaceTokens((string) config('barobill.kakaotalk.title_template', ''), $tokens),
            'Message' => $this->replaceTokens($messageTemplate, $tokens),
            'SmsSubject' => $this->replaceTokens((string) config('barobill.kakaotalk.sms_subject_template', ''), $tokens),
            'SmsMessage' => $this->replaceTokens((string) config('barobill.kakaotalk.sms_message_template', ''), $tokens),
        ];

        $buttons = $this->buildButtons($tokens);

        if ($buttons !== []) {
            $message['Buttons'] = [
                'KakaotalkButton' => $buttons,
            ];
        }

        $response = $this->call($wsdl, 'SendATKakaotalkEx', [
            'CERTKEY' => $certKey,
            'CorpNum' => $corpNum,
            'SenderID' => $senderId,
            'YellowId' => $yellowId,
            'TemplateName' => $templateName,
            'SendDT' => '',
            'SmsReply' => (string) config('barobill.kakaotalk.sms_reply', 'N'),
            'SmsSenderNum' => $this->digitsOnly((string) config('barobill.kakaotalk.sms_sender_num')),
            'KakaotalkMessage' => $message,
        ]);

        $result = (string) $this->extractResult($response, 'SendATKakaotalkExResult');

        if (is_numeric($result) && (int) $result < 0) {
            throw new \RuntimeException('바로빌 알림톡 발송에 실패했습니다. result=' . $result);
        }

        if ($result === '') {
            throw new \RuntimeException('바로빌 알림톡 전송키를 받지 못했습니다.');
        }

        return [
            'status' => 'sent',
            'send_key' => $result,
            'payload' => [
                'receiver_num' => $receiverNum,
                'template_name' => $templateName,
                'yellow_id' => $yellowId,
                'ref_key' => $refKey,
            ],
        ];
    }

    protected function buildTokens(Order $order, Shop $receiverShop): array
    {
        $order->loadMissing('ordererShop');

        $deliveryTime = sprintf(
            '%02d:%02d',
            (int) ($order->delivery_hour ?? 0),
            (int) ($order->delivery_minute ?? 0)
        );

        $deliveryDate = $order->delivery_date?->format('Y-m-d');
        $deliveryAddress = trim(implode(' ', array_filter([
            $order->delivery_addr1,
            $order->delivery_addr2,
        ])));

        return [
            '{{order_no}}' => (string) $order->order_no,
            '{{orderer_shop_name}}' => (string) optional($order->ordererShop)->shop_name,
            '{{receiver_shop_name}}' => (string) $receiverShop->shop_name,
            '{{receiver_shop_display_name}}' => $this->shopDisplayNameService->format($receiverShop),
            '{{product_name}}' => (string) $order->product_name,
            '{{product_detail}}' => (string) $order->product_detail,
            '{{recipient_name}}' => (string) $order->recipient_name,
            '{{recipient_phone}}' => $this->digitsOnly((string) $order->recipient_phone),
            '{{delivery_date}}' => (string) $deliveryDate,
            '{{delivery_time}}' => $deliveryTime,
            '{{delivery_type}}' => (string) $order->delivery_time_type,
            '{{delivery_address}}' => $deliveryAddress,
            '{{request_note}}' => (string) $order->request_note,
            '{{sender_name}}' => (string) $order->sender_name,
        ];
    }

    protected function replaceTokens(string $template, array $tokens): string
    {
        return strtr($template, $tokens);
    }

    protected function buildButtons(array $tokens): array
    {
        $buttons = config('barobill.kakaotalk.buttons', []);

        if (!is_array($buttons)) {
            return [];
        }

        return collect($buttons)
            ->filter(fn ($button) => is_array($button))
            ->map(function (array $button) use ($tokens) {
                return [
                    'Name' => $this->replaceTokens((string) ($button['name'] ?? ''), $tokens),
                    'ButtonType' => (string) ($button['button_type'] ?? ''),
                    'Url1' => $this->replaceTokens((string) ($button['url1'] ?? ''), $tokens),
                    'Url2' => $this->replaceTokens((string) ($button['url2'] ?? ''), $tokens),
                ];
            })
            ->filter(fn (array $button) => $button['Name'] !== '' && $button['ButtonType'] !== '')
            ->values()
            ->all();
    }
}
