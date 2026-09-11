@extends('layouts.app')

@section('title', '日誌の編集')

@section('content')
    @include('diaries.partials.form-style')

    <div class="card diary-form">
        <h2>日誌の編集</h2>

        <form method="POST" action="{{ route('diaries.update', $diary) }}">
            @csrf
            @method('PATCH')

            @include('diaries.partials.form-fields')

            <button class="save-button" type="submit">
                日誌を更新する
            </button>
        </form>
    </div>

    @include('diaries.partials.weather-script')
    @include('diaries.partials.form-script')
@endsection