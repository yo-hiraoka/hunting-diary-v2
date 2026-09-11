@extends('layouts.guest')

@section('title', 'ユーザー登録')

@section('content')
    <h1>ユーザー登録</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label for="name">ユーザー名</label>

            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">

            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="email">メールアドレス</label>

            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">

            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password">パスワード</label>

            <input id="password" type="password" name="password" required autocomplete="new-password">

            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">パスワード確認</label>

            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password">
        </div>

        <button type="submit">登録する</button>
    </form>

    <p class="link">
        登録済みの方は
        <a href="{{ route('login') }}">ログイン</a>
    </p>
@endsection