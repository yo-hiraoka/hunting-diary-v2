@extends('layouts.guest')

@section('title', 'ログイン')

@section('content')
    <h1>ログイン</h1>

    @if (session('status'))
        <div class="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">メールアドレス</label>

            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">

            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password">パスワード</label>

            <input id="password" type="password" name="password" required autocomplete="current-password">

            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field checkbox">
            <input id="remember" type="checkbox" name="remember">

            <label for="remember">ログイン状態を保持する</label>
        </div>

        <p style="text-align: right;">
            <a href="{{ route('password.request') }}">
                パスワードを忘れた方
            </a>
        </p>

        <button type="submit">ログイン</button>
    </form>

    <p class="link">
        アカウントをお持ちでない方は
        <a href="{{ route('register') }}">ユーザー登録</a>
    </p>
@endsection