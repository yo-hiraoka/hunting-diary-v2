@extends('layouts.guest')

@section('title', '新しいパスワードの設定')

@section('content')
    <h1>新しいパスワードの設定</h1>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field">
            <label for="email">メールアドレス</label>

            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                autocomplete="email">

            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password">新しいパスワード</label>

            <input id="password" type="password" name="password" required autocomplete="new-password">

            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">
                新しいパスワードの確認
            </label>

            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password">
        </div>

        <button type="submit">
            パスワードを変更する
        </button>
    </form>
@endsection