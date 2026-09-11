@extends('layouts.guest')

@section('title', 'パスワード再設定')

@section('content')
    <h1>パスワード再設定</h1>

    <p>
        登録したメールアドレスを入力してください。
        パスワード再設定用のメールを送信します。
    </p>

    @if (session('status'))
        <div class="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field">
            <label for="email">メールアドレス</label>

            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">

            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">
            再設定メールを送信する
        </button>
    </form>

    <p class="link">
        <a href="{{ route('login') }}">
            ログイン画面へ戻る
        </a>
    </p>
@endsection