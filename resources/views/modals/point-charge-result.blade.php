<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>처리 결과</title>
</head>
<body>
<script>
    alert(@json($message));

    if (window.opener && !window.opener.closed) {
        window.opener.location.reload();
    }
</script>
</body>
</html>
