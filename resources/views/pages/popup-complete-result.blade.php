<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>처리결과</title>
</head>
<body>
<script>
    alert(@json($message ?? '상품 배송이 완료되었습니다.'));

    const returnUrl =
        window.sujuListReturnUrl ||
        (window.opener && window.opener.sujuListReturnUrl ? window.opener.sujuListReturnUrl : '') ||
        (window.opener && window.opener.opener && window.opener.opener.location ? window.opener.opener.location.href : '');

    try {
        if (window.opener && window.opener.opener && !window.opener.opener.closed) {
            if (returnUrl) {
                window.opener.opener.location.href = returnUrl;
            } else {
                window.opener.opener.location.reload();
            }
        }
    } catch (e) {}

    try {
        if (window.opener && !window.opener.closed) {
            window.opener.location.reload();
        }
    } catch (e) {}

    window.close();
</script>
</body>
</html>
