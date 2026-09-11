<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">

    <title>出猟日誌一覧</title>

    <style>
        @font-face {
            font-family: "IPAexGothic";
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path(
                'app/fonts/IPAexfont00401/ipaexg.ttf'
            ) }}") format("truetype");
        }

        @font-face {
            font-family: "IPAexGothic";
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path(
                'app/fonts/IPAexfont00401/ipaexg.ttf'
            ) }}") format("truetype");
        }

        @page {
            margin: 9mm 8mm 11mm;
        }

        body {
            margin: 0;
            color: #222222;
            font-family: "IPAexGothic", sans-serif;
            font-size: 6.8pt;
            line-height: 1.25;
        }

        h1 {
            margin: 0 0 5px;
            color: #183d28;
            font-size: 15pt;
            text-align: center;
        }

        .meta {
            margin-bottom: 5px;
            color: #555555;
            text-align: right;
        }

        .conditions {
            margin-bottom: 6px;
            padding: 5px 7px;
            border: 1px solid #b7c8bd;
            background: #f2f7f3;
        }

        .conditions-title {
            font-weight: bold;
        }

        .summary {
            width: 100%;
            margin-bottom: 7px;
            border-collapse: collapse;
        }

        .summary th,
        .summary td {
            padding: 3px 4px;
            border: 1px solid #809087;
            text-align: center;
        }

        .summary th {
            background: #e7f0e9;
        }

        .list {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .list thead {
            display: table-header-group;
        }

        .list tr {
            page-break-inside: avoid;
        }

        .list th,
        .list td {
            padding: 3px;
            border: 1px solid #777777;
            overflow-wrap: anywhere;
            vertical-align: top;
        }

        .list th {
            background: #dfeae2;
            font-weight: bold;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .no-data {
            padding: 20px;
            text-align: center;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -8mm;
            left: 0;
            color: #666666;
            font-size: 6pt;
            text-align: center;
        }

        .footer::after {
            content: " - " counter(page) " - ";
        }
    </style>
</head>
<body>
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

        $sortLabels = [
            'date_desc' => '日付が新しい順',
            'date_asc' => '日付が古い順',
            'overall_asc' => '全体番号の昇順',
            'overall_desc' => '全体番号の降順',
            'type_asc' => '区分番号の昇順',
            'type_desc' => '区分番号の降順',
        ];

        $conditions = [];

        if (! empty($filters['fiscal_year'])) {
            $conditions[] =
                $filters['fiscal_year'].'年度';
        }

        if (! empty($filters['date_from'])) {
            $conditions[] =
                '開始日：'.$filters['date_from'];
        }

        if (! empty($filters['date_to'])) {
            $conditions[] =
                '終了日：'.$filters['date_to'];
        }

        if (! empty($filters['diary_type'])) {
            $conditions[] =
                '区分：'
                .($filters['diary_type'] === 'hunting'
                    ? '狩猟'
                    : '有害駆除');
        }

        if (! empty($filters['hunting_method'])) {
            $conditions[] =
                '猟種：'
                .$huntingMethodLabels[
                    $filters['hunting_method']
                ];
        }

        if (! empty($filters['activity'])) {
            $conditions[] =
                '活動：'
                .$activityLabels[$filters['activity']];
        }

        if (! empty($filters['transportation'])) {
            $conditions[] =
                '移動：'
                .$transportationLabels[
                    $filters['transportation']
                ];
        }

        foreach ([
            'has_capture' => '捕獲',
            'has_sighting' => '目撃',
            'has_gun' => '銃',
            'has_used_ammunition' => '弾使用',
        ] as $key => $label) {
            if (array_key_exists($key, $filters)) {
                $conditions[] =
                    $label.'：'
                    .($filters[$key] === '1' ? '有' : '無');
            }
        }

        if (! empty($filters['location'])) {
            $conditions[] =
                '場所：'.$filters['location'];
        }

        $conditions[] =
            '並び順：'
            .($sortLabels[
                $filters['sort'] ?? 'date_desc'
            ]);
    @endphp

    <div class="footer">
        出猟日誌一覧
    </div>

    <h1>出猟日誌一覧</h1>

    <div class="meta">
        ユーザー：{{ $user->name }}
        ／作成日時：{{ now()->format('Y年m月d日 H:i') }}
    </div>

    <div class="conditions">
        <span class="conditions-title">抽出条件：</span>
        {{ implode(' ／ ', $conditions) }}
    </div>

    <table class="summary">
        <thead>
            <tr>
                <th>日誌件数</th>
                <th>捕獲あり</th>
                <th>目撃あり</th>
                <th>サボット</th>
                <th>スラグ</th>
                <th>BS</th>
                <th>散弾</th>
                <th>弾合計</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ $summary['total_count'] }}件</td>
                <td>{{ $summary['capture_count'] }}件</td>
                <td>{{ $summary['sighting_count'] }}件</td>
                <td>{{ $summary['sabot_count'] }}</td>
                <td>{{ $summary['slug_count'] }}</td>
                <td>{{ $summary['bs_count'] }}</td>
                <td>{{ $summary['shot_count'] }}</td>
                <td>{{ $summary['ammunition_total'] }}</td>
            </tr>
        </tbody>
    </table>

    <table class="list">
        <thead>
            <tr>
                <th style="width: 8%;">全体番号</th>
                <th style="width: 8%;">区分番号</th>
                <th style="width: 7%;">日付</th>
                <th style="width: 6%;">区分</th>
                <th style="width: 12%;">場所</th>
                <th style="width: 6%;">天候</th>
                <th style="width: 9%;">猟種</th>
                <th style="width: 11%;">活動内容</th>
                <th style="width: 7%;">移動</th>
                <th style="width: 5%;">捕獲</th>
                <th style="width: 5%;">目撃</th>
                <th style="width: 5%;">銃</th>
                <th style="width: 11%;">使用弾</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($diaries as $diary)
                @php
                    $methods = collect(
                        $diary->hunting_methods ?? []
                    )->map(
                        fn ($value) =>
                            $huntingMethodLabels[$value] ?? $value
                    )->implode('、');

                    $activities = collect(
                        $diary->activities ?? []
                    )->map(
                        fn ($value) =>
                            $activityLabels[$value] ?? $value
                    )->implode('、');

                    $transportations = collect(
                        $diary->transportations ?? []
                    )->map(
                        fn ($value) =>
                            $transportationLabels[$value] ?? $value
                    )->implode('、');

                    $ammunition = [];

                    if ($diary->sabot_count > 0) {
                        $ammunition[] =
                            'サボット'.$diary->sabot_count;
                    }

                    if ($diary->slug_count > 0) {
                        $ammunition[] =
                            'スラグ'.$diary->slug_count;
                    }

                    if ($diary->bs_count > 0) {
                        $ammunition[] =
                            'BS'.$diary->bs_count;
                    }

                    if ($diary->shot_count > 0) {
                        $ammunition[] =
                            '散弾'.$diary->shot_count;
                    }
                @endphp

                <tr>
                    <td class="center">
                        {{ $diary->overall_display_number }}
                    </td>

                    <td class="center">
                        {{ $diary->type_display_number }}
                    </td>

                    <td class="center">
                        {{ $diary->activity_date
                            ->format('Y/m/d') }}
                    </td>

                    <td class="center">
                        {{ $diary->diary_type->label() }}
                    </td>

                    <td>{{ $diary->location }}</td>
                    <td class="center">{{ $diary->weather }}</td>
                    <td>{{ $methods ?: '-' }}</td>
                    <td>{{ $activities ?: '-' }}</td>
                    <td>{{ $transportations ?: '-' }}</td>

                    <td class="center">
                        {{ $diary->has_capture ? '有' : '無' }}
                    </td>

                    <td class="center">
                        {{ $diary->has_sighting ? '有' : '無' }}
                    </td>

                    <td class="center">
                        {{ $diary->has_gun ? '有' : '無' }}
                    </td>

                    <td>
                        {{ $ammunition
                            ? implode('、', $ammunition)
                            : 'なし' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="no-data" colspan="13">
                        条件に一致する日誌はありません。
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>