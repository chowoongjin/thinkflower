@extends('layouts.sub')

@section('content')
    @if ($errors->any())
        <script>
            alert(@json($errors->first()));
        </script>
    @endif

    @if (session('success'))
        <script>
            alert(@json(session('success')));
        </script>
    @endif
    <div id="page-login">
        <div id="login">
            <img src="{{ asset('assets/img/logo.png') }}" alt="logo">

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="field large">
                    <label>아이디를 입력하세요</label>
                    <input
                        type="text"
                        name="login_id"
                        value="{{ old('login_id', request()->cookie('saved_login_id')) }}"
                        placeholder="아이디를 입력해 주세요"
                    >
                    @error('login_id')
                    <small class="color-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field large">
                    <label>비밀번호를 입력하세요</label>
                    <div class="input-group-side">
                        <input
                            type="password"
                            name="password"
                            placeholder="비밀번호를 입력해 주세요"
                        >
                        <span>
                        <button type="button" data-toggle="pw">
                            <i class="bi bi-eye"></i>
                        </button>
                    </span>
                    </div>
                    @error('password')
                    <small class="color-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field">
                    <div class="flex">
                        <div class="flex__col">
                            <div class="input-group-check">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    id="save_login"
                                    value="1"
                                    {{ old('remember', request()->cookie('saved_login_id') ? 1 : 0) ? 'checked' : '' }}
                                >
                                <label for="save_login">로그인 저장</label>
                            </div>
                        </div>
                        <div class="flex__col">
                            <a href="{{ route('password.request') }}" class="color-gray300">비밀번호를 잊으셨나요?</a>
                        </div>
                    </div>
                </div>

                <div class="field large">
                    <button type="submit" class="btn btn-primary btn-fluid">로그인</button>
                    <a href="{{ route('register') }}" class="btn btn-secondary btn-fluid">회원가입</a>
                </div>
            </form>
        </div>
    </div>
@endsection
