<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>처리결과</title>
</head>
<body>
<script>
    alert(@json($message ?? '처리되었습니다.'));

    const returnUrl =
        window.sujuListReturnUrl ||
        (window.opener && window.opener.sujuListReturnUrl ? window.opener.sujuListReturnUrl : '') ||
        (window.opener && !window.opener.closed ? window.opener.location.href : '');

    try {
        if (window.opener && !window.opener.closed) {
            if (returnUrl && returnUrl.includes('/suju-list')) {
                window.opener.location.href = returnUrl;
            } else if (@json($redirectParentTo ?? null)) {
                window.opener.location.href = @json($redirectParentTo ?? null);
            } else {
                window.opener.location.reload();
            }
        }
    } catch (e) {}

    @if(!empty($redirectCurrentTo))
        window.location.href = @json($redirectCurrentTo);
    @elseif(!empty($closeWindow))
    window.close();
    @endif
</script>
</body>
</html>
