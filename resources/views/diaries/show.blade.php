@extends('layouts.app')

@section('title', '日誌詳細')

@section('content')
    @php
        $huntingMethodLabels = [
            'gun' => '銃猟',
            'trap' => '罠猟',
            'net' => '網猟',
        ];

        $activityLabels = [
            'feeding' => '餌撒き',
            'patrol' => '見廻り',
            'capture' => '捕獲',
            'miss' => '空振り',
        ];

        $transportationLabels = [
            'car' => '車',
            'motorcycle' => 'バイク',
        ];
    @endphp

    <style>
        .page-header {
            display: flex;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-header h2 {
            margin: 0;
        }

        .back-link {
            color: #166534;
            font-weight: 600;
        }

        .number-area {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .number {
            padding: 8px 12px;
            border-radius: 6px;
            background: #dcfce7;
            color: #14532d;
            font-weight: 700;
        }

        .section {
            margin-bottom: 28px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section h3 {
            padding-bottom: 8px;
            border-bottom: 2px solid #166534;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,
                    minmax(220px, 1fr));
            gap: 16px;
        }

        .detail-item {
            padding: 12px;
            border-radius: 6px;
            background: #f9fafb;
        }

        .detail-label {
            display: block;
            margin-bottom: 5px;
            color: #6b7280;
            font-size: 14px;
        }

        .text-content {
    margin: 8px 0 0;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

        .ammunition-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ammunition-table th,
        .ammunition-table td {
            padding: 10px;
            border: 1px solid #d1d5db;
            text-align: center;
        }
        .delete-button {
    padding: 8px 14px;
    border: 0;
    border-radius: 6px;
    background: #dc2626;
    color: #ffffff;
    font-weight: 600;
    cursor: pointer;
}

.delete-button:hover {
    background: #b91c1c;
}
.pdf-link {
    padding: 8px 14px;
    border-radius: 6px;
    background: #0369a1;
    color: #ffffff;
    font-weight: 600;
    text-decoration: none;
}

.pdf-link:hover {
    background: #075985;
}
.detail-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
}

.detail-actions form {
    margin: 0;
}

@media (max-width: 600px) {
    .detail-actions {
        width: 100%;
    }
}
    </style>

    <div class="page-header">
    <h2>日誌詳細</h2>

    <div style="
        display: flex;
        gap: 12px;
        align-items: center;
    " class="detail-actions">
        <a
            class="back-link"
            href="{{ route('diaries.edit', $diary) }}"
        >
            編集
        </a>

        <a
            class="back-link"
            href="{{ route('diaries.index') }}"
        >
            一覧へ戻る
        </a>
        <a
    class="pdf-link"
    href="{{ route('diaries.pdf', $diary) }}"
>
    PDF保存
</a>

<a
    href="{{ route('diaries.pdf', ['diary' => $diary, 'print' => 1]) }}"
    target="_blank"
    rel="noopener"
    class="pdf-link"
>
    印刷する
</a>

        <form
            method="POST"
            action="{{ route('diaries.destroy', $diary) }}"
            onsubmit="
                return confirm(
                    'この日誌をゴミ箱へ移動しますか？'
                );
            "
        >
            @csrf
            @method('DELETE')

            <button class="delete-button" type="submit">
                ゴミ箱へ移動
            </button>
        </form>
    </div>
</div>

    <div class="card">
        <div class="number-area">
            <span class="number">
                {{ $diary->overall_display_number }}
            </span>

            <span class="number">
                {{ $diary->type_display_number }}
            </span>
        </div>

        <section class="section">
            <h3>基本情報</h3>

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">区分</span>
                    {{ $diary->diary_type->label() }}
                </div>

                <div class="detail-item">
                    <span class="detail-label">日付</span>
                    {{ $diary->activity_date
        ->format('Y年m月d日') }}
                </div>

                <div class="detail-item">
                    <span class="detail-label">出発時刻</span>
                    {{ substr($diary->departure_time, 0, 5) }}
                </div>

                <div class="detail-item">
                    <span class="detail-label">帰宅時刻</span>
                    {{ substr($diary->return_time, 0, 5) }}
                </div>
            </div>
        </section>

        <section class="section">
            <h3>天気</h3>

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">取得地域</span>
                    {{ $diary->weather_prefecture }}
                    {{ $diary->weather_city }}
                </div>

                <div class="detail-item">
                    <span class="detail-label">天候</span>
                    {{ $diary->weather ?? '未入力' }}
                </div>

                <div class="detail-item">
                    <span class="detail-label">日の出</span>
                    {{ $diary->sunrise_time
        ? substr($diary->sunrise_time, 0, 5)
        : '未入力' }}
                </div>

                <div class="detail-item">
                    <span class="detail-label">日没</span>
                    {{ $diary->sunset_time
        ? substr($diary->sunset_time, 0, 5)
        : '未入力' }}
                </div>
            </div>
        </section>

        <section class="section">
            <h3>活動情報</h3>

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">猟種</span>
                    @forelse (
                            $diary->hunting_methods ?? []
                            as $method
                        )
                        {{ $huntingMethodLabels[$method] ?? $method }}
                        @unless ($loop->last)、@endunless
                    @empty
                        未選択
                    @endforelse
                </div>

                <div class="detail-item">
                    <span class="detail-label">活動内容</span>
                    @forelse (
                            $diary->activities ?? []
                            as $activity
                        )
                        {{ $activityLabels[$activity] ?? $activity }}
                        @unless ($loop->last)、@endunless
                    @empty
                        未選択
                    @endforelse
                </div>

                <div class="detail-item">
                    <span class="detail-label">移動手段</span>
                    @forelse (
                                        $diary->transportations ?? []
                                        as $transportation
                                    )
                                    {{ $transportationLabels[
                            $transportation
                        ] ?? $transportation }}
                                    @unless ($loop->last)、@endunless
                    @empty
                        未選択
                    @endforelse
                </div>

                <div class="detail-item">
                    <span class="detail-label">場所</span>
                    {{ $diary->location }}
                </div>
            </div>
        </section>

        <section class="section">
            <h3>捕獲・目撃</h3>

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">捕獲</span>
                    {{ $diary->has_capture ? '有' : '無' }}

                    @if ($diary->has_capture)
                        <p class="text-content">{{ $diary->capture_details }}</p>
                    @endif
                </div>

                <div class="detail-item">
                    <span class="detail-label">目撃</span>
                    {{ $diary->has_sighting ? '有' : '無' }}

                    @if ($diary->has_sighting)
                        <p class="text-content">{{ $diary->sighting_details }}</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="section">
            <h3>銃・弾</h3>

            <p>
                銃の持ち出し：
                {{ $diary->has_gun ? '有' : '無' }}
            </p>

            <p>
                弾の使用：
                {{ $diary->has_used_ammunition ? '有' : '無' }}
            </p>

            @if ($diary->has_used_ammunition)
                <table class="ammunition-table">
                    <thead>
                        <tr>
                            <th>サボット</th>
                            <th>スラグ</th>
                            <th>BS</th>
                            <th>散弾</th>
                            <th>合計</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>{{ $diary->sabot_count }}</td>
                            <td>{{ $diary->slug_count }}</td>
                            <td>{{ $diary->bs_count }}</td>
                            <td>{{ $diary->shot_count }}</td>
                            <td>
                                {{
                    $diary->sabot_count
                + $diary->slug_count
                + $diary->bs_count
                + $diary->shot_count
                                        }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </section>

        <section class="section">
            <h3>注釈</h3>

            <div class="text-content">{{ $diary->notes ?: '注釈はありません。' }}</div>
        </section>
    </div>@if (session('status'))
    <div style="
        margin-bottom: 20px;
        padding: 12px;
        border-radius: 6px;
        background: #dcfce7;
        color: #166534;
    ">
        {{ session('status') }}
    </div>
@endif
@endsection