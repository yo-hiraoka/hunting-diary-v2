@extends('layouts.app')

@section('title', '固定地域の設定')

@section('content')
    <style>
        .settings-form {
            max-width: 600px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .field input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
        }

        .field input:focus {
            border-color: #166534;
            outline: 2px solid rgb(22 101 52 / 15%);
        }

        .description {
            color: #4b5563;
            line-height: 1.7;
        }

        .error {
            margin: 6px 0 0;
            color: #dc2626;
            font-size: 14px;
        }

        .status {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 6px;
            background: #dcfce7;
            color: #166534;
        }

        .save-button {
            padding: 10px 20px;
            border: 0;
            border-radius: 6px;
            background: #166534;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .save-button:hover {
            background: #14532d;
        }

        .coordinates {
            margin-top: 24px;
            padding: 16px;
            border-radius: 6px;
            background: #f9fafb;
        }
    </style>

    <div class="card settings-form">
        <h2>天気取得地域の設定</h2>

        <p class="description">
            日誌の天気、日の出、日没を取得するときに使用する
            固定地域を設定してください。
        </p>

        @if (session('status'))
            <div class="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('settings.location.update') }}">
            @csrf
            @method('PATCH')

            <div class="field">
                <label for="weather_prefecture">都道府県</label>

                <input id="weather_prefecture" type="text" name="weather_prefecture" value="{{ old(
        'weather_prefecture',
        $user->weather_prefecture
    ) }}" placeholder="例：北海道" required autofocus>

                @error('weather_prefecture')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="weather_city">市区町村</label>

                <input id="weather_city" type="text" name="weather_city" value="{{ old(
        'weather_city',
        $user->weather_city
    ) }}" placeholder="例：札幌市" required>

                @error('weather_city')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button class="save-button" type="submit">
                固定地域を保存する
            </button>
        </form>

        <div class="coordinates">
            <strong>保存されている座標</strong>

            @if (
                    $user->weather_latitude !== null &&
                    $user->weather_longitude !== null
                )
                <p>
                    緯度：{{ $user->weather_latitude }}<br>
                    経度：{{ $user->weather_longitude }}
                </p>
            @else
                <p>
                    座標はまだ取得されていません。
                    次の工程で天気APIと連携します。
                </p>
            @endif
        </div>
    </div>
@endsection