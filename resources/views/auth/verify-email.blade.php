@extends('layouts.guest')

@section('title', 'メールアドレスの確認')

@section('content')
    <h1>メールアドレスの確認</h1>

    <p>
        ご登録ありがとうございます。
        送信されたメール内のリンクをクリックして、
        メールアドレスの確認を完了してください。
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="status">
            新しい確認メールを送信しました。
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        <button type="submit">
            確認メールを再送する
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="margin-top: 16px;">
        @csrf

        <button type="submit">
            ログアウト
        </button>
    </form>
@endsection