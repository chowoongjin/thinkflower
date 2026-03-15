<?php
	include_once '_common.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<title>정직한플라워</title>
	<meta name="title" lang="ko" content="Flord">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
	<meta name="robots" content="noindex,nofollow">
	
	<!-- Load a CSS file -->
	<link rel="stylesheet" href="<?php echo ROOT_URL;?>/assets/css/common.css?v=<?php echo date('YmdHis');?>">
	<link rel="stylesheet" href="<?php echo ROOT_URL;?>/assets/css/layout.css?v=<?php echo date('YmdHis');?>">
	<link rel="stylesheet" href="<?php echo ROOT_URL;?>/assets/css/pages.css?v=<?php echo date('YmdHis');?>">
	<link rel="stylesheet" href="<?php echo ROOT_URL;?>/assets/css/pretendard.css">
	
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
	
	<!-- Load a JS file -->
	<script src="<?php echo ROOT_URL;?>/assets/js/jquery-3.6.0.min.js"></script>
	<script src="<?php echo ROOT_URL;?>/assets/js/script.js?v=<?php echo date('YmdHis');?>"></script>
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>
	
	<!-- ========================================
		Modal
	========================================= -->
	<div id="modal">
		<div id="ajax-modal"></div>
	</div>
