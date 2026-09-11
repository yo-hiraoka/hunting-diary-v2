@extends('layouts.app')

@section('title', '日誌の新規作成')

@section('content')
    @include('diaries.partials.form-style')

    <div class="card diary-form">
        <h2>日誌の新規作成</h2>

        <form method="POST" action="{{ route('diaries.store') }}">
            @csrf

            @include('diaries.partials.form-fields')

            <button class="save-button" type="submit">
                日誌を登録する
            </button>
        </form>
    </div>

    @include('diaries.partials.weather-script')
    @include('diaries.partials.form-script')
@endsection