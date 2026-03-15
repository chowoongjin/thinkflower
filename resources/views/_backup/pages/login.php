<?php include_once '_header.sub.php';?>
<div id="page-login">
	<div id="login">
		<img src="./assets/img/logo.png" alt="logo">
		<form>
			<div class="field large">
				<label>아이디를 입력하세요</label>
				<input type="text" placeholder="아이디를 입력해 주세요">
			</div>
			<div class="field large">
				<label>비밀번호를 입력하세요</label>
				<div class="input-group-side">
					<input type="password" placeholder="비밀번호를 입력해 주세요">
					<span><button type="button" data-toggle="pw"><i class="bi bi-eye"></i></button>
				</div>
			</div>
			<div class="field">
				<div class="flex">
					<div class="flex__col">
						<div class="input-group-check">
							<input type="checkbox" name="" id="save_login"><label for="save_login">로그인 저장</label>
						</div>
					</div>
					<div class="flex__col">
						<a href="#none" class="color-gray300">비밀번호를 잊으셨나요?</a>
					</div>
				</div>
			</div>
			<div class="field large">
				<button type="submit" class="btn btn-primary btn-fluid">로그인</button>
				<a href="#none" class="btn btn-secondary btn-fluid">회원가입</a>
			</div>
		</form>
	</div>
</div>
<?php include_once '_footer.sub.php';?>