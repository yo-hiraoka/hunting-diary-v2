@php
    $diary = $diary ?? null;

    $diaryType = old(
        'diary_type',
        $diary?->diary_type?->value ?? 'hunting'
    );

    $huntingMethods = old(
        'hunting_methods',
        $diary?->hunting_methods ?? []
    );

    $activities = old(
        'activities',
        $diary?->activities ?? []
    );

    $transportations = old(
        'transportations',
        $diary?->transportations ?? []
    );

    $hasCapture = (string) old(
        'has_capture',
        $diary?->has_capture ? '1' : '0'
    );

    $hasSighting = (string) old(
        'has_sighting',
        $diary?->has_sighting ? '1' : '0'
    );

    $hasGun = (string) old(
        'has_gun',
        $diary?->has_gun ? '1' : '0'
    );

    $hasUsedAmmunition = (string) old(
        'has_used_ammunition',
        $diary?->has_used_ammunition ? '1' : '0'
    );
@endphp

@if ($errors->any())
    <div class="form-error-summary">
        <strong>入力内容を確認してください。</strong>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<fieldset>
    <legend>基本情報</legend>

    <div class="field">
        <label>日誌区分</label>

        <div class="choices">
            <label class="choice">
                <input type="radio" name="diary_type" value="hunting" @checked($diaryType === 'hunting')>
                狩猟
            </label>

            <label class="choice">
                <input type="radio" name="diary_type" value="control" @checked($diaryType === 'control')>
                有害駆除
            </label>
        </div>
    </div>

    <div class="time-grid">
        <div class="field">
            <label for="activity_date">日付</label>

            <input id="activity_date" type="date" name="activity_date" value="{{ old(
    'activity_date',
    $diary?->activity_date?->format('Y-m-d')
) }}" required>
        </div>

        <div class="field">
            <label for="departure_time">出発時刻</label>

            <input id="departure_time" type="time" name="departure_time" value="{{ old(
    'departure_time',
    $diary?->departure_time
    ? substr($diary->departure_time, 0, 5)
    : ''
) }}" required>
        </div>

        <div class="field">
            <label for="return_time">帰宅時刻</label>

            <input id="return_time" type="time" name="return_time" value="{{ old(
    'return_time',
    $diary?->return_time
    ? substr($diary->return_time, 0, 5)
    : ''
) }}" required>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>天気</legend>

    <p>
        取得地域：
        {{ auth()->user()->weather_prefecture }}
        {{ auth()->user()->weather_city }}
    </p>

    <input id="weather_was_fetched" type="hidden" name="weather_was_fetched" value="0">

    <div class="field">
        <button id="fetch-weather" class="weather-button" type="button">
            選択した日付の天気を取得
        </button>

        <p id="weather-message"></p>
    </div>

    <div class="field">
        <label for="weather">天候</label>

        <input id="weather" type="text" name="weather" value="{{ old('weather', $diary?->weather) }}">
    </div>

    <div class="time-grid">
        @foreach ([
                    'sunrise_time' => '日の出',
                    'sunset_time' => '日没',
                ] as $name => $label)
                @php
                    $time = $diary?->{$name};
                @endphp

                <div class="field">
                    <label for="{{ $name }}">{{ $label }}</label>

                    <input id="{{ $name }}" type="time" name="{{ $name }}" value="{{ old(
                $name,
                $time ? substr($time, 0, 5) : ''
            ) }}">
                </div>
        @endforeach
    </div>
</fieldset>

<fieldset>
    <legend>活動情報</legend>

    <div class="field">
        <label>猟種</label>

        <div class="choices">
            @foreach ([
                    'gun' => '銃猟',
                    'trap' => '罠猟',
                    'net' => '網猟',
                ] as $value => $label)
                <label class="choice">
                    <input type="checkbox" name="hunting_methods[]" value="{{ $value }}" @checked(
                        in_array(
                            $value,
                            $huntingMethods,
                            true
                        )
                    )>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="field">
        <label>活動内容</label>

        <div class="choices">
            @foreach ([
                    'feeding' => '餌撒き',
                    'patrol' => '見廻り',
                    'capture' => '捕獲',
                    'miss' => '空振り',
                ] as $value => $label)
                <label class="choice">
                    <input type="checkbox" name="activities[]" value="{{ $value }}" @checked(
                        in_array(
                            $value,
                            $activities,
                            true
                        )
                    )>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="field">
        <label>移動手段</label>

        <div class="choices">
            @foreach ([
                    'car' => '車',
                    'motorcycle' => 'バイク',
                ] as $value => $label)
                <label class="choice">
                    <input type="checkbox" name="transportations[]" value="{{ $value }}" @checked(
                        in_array(
                            $value,
                            $transportations,
                            true
                        )
                    )>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="field">
        <label for="location">場所</label>

        <input id="location" type="text" name="location" value="{{ old('location', $diary?->location) }}" required>
    </div>
</fieldset>

<fieldset>
    <legend>捕獲・目撃</legend>

    <div class="field">
        <label>捕獲</label>

        <div class="choices">
            @foreach (['1' => '有', '0' => '無'] as $value => $label)
                <label class="choice">
                    <input type="radio" name="has_capture" value="{{ $value }}" @checked($hasCapture === (string) $value)>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div id="capture-details-field" class="field">
        <label for="capture_details">捕獲内容</label>

        <textarea id="capture_details" name="capture_details">{{ old(
    'capture_details',
    $diary?->capture_details
) }}</textarea>
    </div>

    <div class="field">
        <label>目撃</label>

        <div class="choices">
            @foreach (['1' => '有', '0' => '無'] as $value => $label)
                <label class="choice">
                    <input type="radio" name="has_sighting" value="{{ $value }}" @checked($hasSighting === (string) $value)>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div id="sighting-details-field" class="field">
        <label for="sighting_details">目撃内容</label>

        <textarea id="sighting_details" name="sighting_details">{{ old(
    'sighting_details',
    $diary?->sighting_details
) }}</textarea>
    </div>
</fieldset>

<fieldset>
    <legend>銃・弾</legend>

    <div class="field">
        <label>銃の持ち出し</label>

        <div class="choices">
            @foreach (['1' => '有', '0' => '無'] as $value => $label)
                <label class="choice">
                    <input type="radio" name="has_gun" value="{{ $value }}" @checked($hasGun === (string) $value)>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div id="ammunition-use-field" class="field">
        <label>弾の使用</label>

        <div class="choices">
            @foreach (['1' => '有', '0' => '無'] as $value => $label)
                <label class="choice">
                    <input type="radio" name="has_used_ammunition" value="{{ $value }}" @checked(
                        $hasUsedAmmunition === (string) $value
                    )>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div id="ammunition-count-fields" class="ammunition-grid">
        @foreach ([
                    'sabot_count' => 'サボット',
                    'slug_count' => 'スラグ',
                    'bs_count' => 'BS',
                    'shot_count' => '散弾',
                ] as $name => $label)
                <div class="field">
                    <label for="{{ $name }}">{{ $label }}</label>

                    <div class="counter">
                        <button type="button" class="counter-button" data-counter-target="{{ $name }}"
                            data-counter-action="decrease" aria-label="{{ $label }}を1減らす">
                            −
                        </button>

                        <input id="{{ $name }}" class="counter-value" type="number" name="{{ $name }}" value="{{ old(
                $name,
                $diary?->{$name} ?? 0
            ) }}" min="0" max="99" readonly>

                        <button type="button" class="counter-button" data-counter-target="{{ $name }}"
                            data-counter-action="increase" aria-label="{{ $label }}を1増やす">
                            ＋
                        </button>
                    </div>

                    <p class="counter-unit">発</p>
                </div>
        @endforeach
    </div>

    @error('ammunition_counts')
        <p class="error">{{ $message }}</p>
    @enderror
</fieldset>

<fieldset>
    <legend>注釈</legend>

    <textarea id="notes" name="notes">{{ old('notes', $diary?->notes) }}</textarea>
</fieldset>