<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>처리완료</title>
</head>
<body>
<script>
    alert(@json($message));

    @if (!empty($redirectParentTo))
    if (window.opener && !window.opener.closed) {
        window.opener.location.href = @json($redirectParentTo);
    }
    @endif

        @if (!empty($redirectCurrentTo))
        window.location.href = @json($redirectCurrentTo);
    @elseif (!empty($closeWindow))
    window.close();
    @endif
</script>
</body>
</html>
