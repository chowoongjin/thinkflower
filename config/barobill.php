<?php

$mode = env('BAROBILL_MODE', 'production');

$faxWsdl = $mode === 'test'
    ? env('BAROBILL_FAX_WSDL', 'http://testws.baroservice.com/FAX.asmx?WSDL')
    : env('BAROBILL_FAX_WSDL', 'https://ws.baroservice.com/FAX.asmx?WSDL');

$kakaotalkWsdl = $mode === 'test'
    ? env('BAROBILL_KAKAOTALK_WSDL', 'http://testws.baroservice.com/KAKAOTALK.asmx?WSDL')
    : env('BAROBILL_KAKAOTALK_WSDL', 'https://ws.baroservice.com/KAKAOTALK.asmx?WSDL');

$buttonJson = (string) env('BAROBILL_KAKAOTALK_BUTTONS_JSON', '[]');
$buttons = json_decode($buttonJson, true);

return [
    'mode' => $mode,
    'cert_key' => env('BAROBILL_CERT_KEY'),
    'corp_num' => env('BAROBILL_CORP_NUM'),
    'sender_id' => env('BAROBILL_SENDER_ID'),

    'fax' => [
        'enabled' => env('BAROBILL_FAX_ENABLED', false),
        'wsdl' => $faxWsdl,
        'ftp_host' => env('BAROBILL_FAX_FTP_HOST'),
        'ftp_port' => (int) env('BAROBILL_FAX_FTP_PORT', 21),
        'ftp_username' => env('BAROBILL_FAX_FTP_USERNAME'),
        'ftp_password' => env('BAROBILL_FAX_FTP_PASSWORD'),
        'ftp_base_dir' => env('BAROBILL_FAX_FTP_BASE_DIR', '/'),
        'ftp_ssl' => env('BAROBILL_FAX_FTP_SSL', false),
        'from_number' => env('BAROBILL_FAX_FROM_NUMBER'),
        'filename_prefix' => env('BAROBILL_FAX_FILENAME_PREFIX', 'order-fax'),
        'poll_interval_seconds' => (int) env('BAROBILL_FAX_POLL_INTERVAL_SECONDS', 5),
        'poll_timeout_seconds' => (int) env('BAROBILL_FAX_POLL_TIMEOUT_SECONDS', 120),
    ],

    'kakaotalk' => [
        'enabled' => env('BAROBILL_KAKAOTALK_ENABLED', false),
        'wsdl' => $kakaotalkWsdl,
        'yellow_id' => env('BAROBILL_KAKAOTALK_YELLOW_ID'),
        'template_name' => env('BAROBILL_KAKAOTALK_TEMPLATE_NAME'),
        'sms_reply' => env('BAROBILL_KAKAOTALK_SMS_REPLY', 'N'),
        'sms_sender_num' => env('BAROBILL_KAKAOTALK_SMS_SENDER_NUM'),
        'title_template' => env('BAROBILL_KAKAOTALK_TITLE_TEMPLATE', ''),
        'message_template' => env('BAROBILL_KAKAOTALK_MESSAGE_TEMPLATE', ''),
        'sms_subject_template' => env('BAROBILL_KAKAOTALK_SMS_SUBJECT_TEMPLATE', ''),
        'sms_message_template' => env('BAROBILL_KAKAOTALK_SMS_MESSAGE_TEMPLATE', ''),
        'buttons' => is_array($buttons) ? $buttons : [],
    ],
];
