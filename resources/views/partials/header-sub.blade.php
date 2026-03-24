<!DOCTYPE html>
<html lang="ko">
<head>
    <title>정직한플라워</title>
    <meta name="title" lang="ko" content="Flord">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="robots" content="noindex,nofollow">

    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}?v={{ filemtime(public_path('assets/css/common.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}?v={{ filemtime(public_path('assets/css/layout.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages.css') }}?v={{ filemtime(public_path('assets/css/pages.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/add.css') }}?v={{ filemtime(public_path('assets/css/add.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pretendard.css') }}?v={{ filemtime(public_path('assets/css/pretendard.css')) }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}?v={{ filemtime(public_path('assets/js/script.js')) }}"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="//t1.kakaocdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
</head>
<body>

<div id="modal">
    <div id="ajax-modal"></div>
</div>
