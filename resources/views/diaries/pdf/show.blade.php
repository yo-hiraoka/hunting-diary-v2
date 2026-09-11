<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">

    <title>{{ $diary->type_display_number }}</title>

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
            margin: 18mm 15mm 20mm;
        }

        body {
            margin: 0;
            color: #222222;
            font-family: "IPAexGothic", sans-serif;
            font-size: 10pt;
            line-height: 1.5;
        }

        h1 {
            margin: 0 0 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #245c3b;
            color: #183d28;
            font-size: 20pt;
            text-align: center;
        }

        h2 {
            margin: 16px 0 6px;
            padding: 5px 8px;
            background: #e8f1eb;
            color: #183d28;
            font-size: 12pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 5px 7px;
            border: 1px solid #777777;
            vertical-align: top;
        }

        th {
            width: 22%;
            background: #f3f3f3;
            font-weight: normal;
            text-align: left;
        }

        .numbers {
            margin-bottom: 12px;
            text-align: center;
        }

        .number {
            display: inline-block;
            margin: 0 5px;
            padding: 4px 10px;
            border: 1px solid #245c3b;
            border-radius: 4px;
            color: #183d28;
        }

        .text {
            min-height: 30px;
            white-space: pre-wrap;
        }

        .ammunition th,
        .ammunition td {
            width: 20%;
            text-align: center;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -12mm;
            left: 0;
            color: #666666;
            font-size: 8pt;
            text-align: center;
        }

        .footer::after {
            content: " - " counter(page) " - ";
       
 }
 /* A4一枚に収めるためのコンパクト設定 */
@page {
    margin: 7mm 9mm 9mm;
}

body {
    font-size: 7.8pt;
    line-height: 1.25;
}

h1 {
    margin: 0 0 5px;
    padding-bottom: 4px;
    font-size: 15pt;
}

h2 {
    margin: 7px 0 3px;
    padding: 2px 6px;
    font-size: 9.5pt;
}

.numbers {
    margin-bottom: 5px;
}

.number {
    margin: 0 3px;
    padding: 2px 7px;
}

th,
td {
    padding: 2px 4px;
    line-height: 1.25;
}

.text {
    min-height: 0;
    line-height: 1.25;
}

.footer {
    bottom: -7mm;
    font-size: 7pt;
}

tr {
    page-break-inside: avoid;
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

        $huntingMethods = collect(
            $diary->hunting_methods ?? []
        )->map(
                fn($value) =>
                $huntingMethodLabels[$value] ?? $value
            )->implode('、');

        $activities = collect(
            $diary->activities ?? []
        )->map(
                fn($value) =>
                $activityLabels[$value] ?? $value
            )->implode('、');

        $transportations = collect(
            $diary->transportations ?? []
        )->map(
                fn($value) =>
                $transportationLabels[$value] ?? $value
            )->implode('、');

        $ammunitionTotal =
            $diary->sabot_count
            + $diary->slug_count
            + $diary->bs_count
            + $diary->shot_count;
    @endphp

    <div class="footer">
        出猟日誌
    </div>

    <h1>出猟日誌</h1>

    <div class="numbers">
        <span class="number">
            {{ $diary->overall_display_number }}
        </span>

        <span class="number">
            {{ $diary->type_display_number }}
        </span>
    </div>

    <h2>基本情報</h2>

    <table>
        <tr>
            <th>区分</th>
            <td>{{ $diary->diary_type->label() }}</td>
            <th>日付</th>
            <td>
                {{ $diary->activity_date->format('Y年m月d日') }}
            </td>
        </tr>
        <tr>
            <th>出発時刻</th>
            <td>{{ substr($diary->departure_time, 0, 5) }}</td>
            <th>帰宅時刻</th>
            <td>{{ substr($diary->return_time, 0, 5) }}</td>
        </tr>
    </table>

    <h2>天気</h2>

    <table>
        <tr>
            <th>取得地域</th>
            <td colspan="3">
                {{ $diary->weather_prefecture }}
                {{ $diary->weather_city }}
            </td>
        </tr>
        <tr>
            <th>天候</th>
            <td>{{ $diary->weather ?? '未入力' }}</td>
            <th>日の出</th>
            <td>
                {{ $diary->sunrise_time
    ? substr($diary->sunrise_time, 0, 5)
    : '未入力' }}
            </td>
        </tr>
        <tr>
            <th>日没</th>
            <td colspan="3">
                {{ $diary->sunset_time
    ? substr($diary->sunset_time, 0, 5)
    : '未入力' }}
            </td>
        </tr>
    </table>

    <h2>活動情報</h2>

    <table>
        <tr>
            <th>猟種</th>
            <td>{{ $huntingMethods ?: '未選択' }}</td>
        </tr>
        <tr>
            <th>活動内容</th>
            <td>{{ $activities ?: '未選択' }}</td>
        </tr>
        <tr>
            <th>移動手段</th>
            <td>{{ $transportations ?: '未選択' }}</td>
        </tr>
        <tr>
            <th>場所</th>
            <td>{{ $diary->location }}</td>
        </tr>
    </table>

    <h2>捕獲・目撃</h2>

    <table>
        <tr>
            <th>捕獲</th>
            <td>{{ $diary->has_capture ? '有' : '無' }}</td>
        </tr>
        <tr>
            <th>捕獲内容</th>
            <td class="text">
                {{ $diary->capture_details ?: 'なし' }}
            </td>
        </tr>
        <tr>
            <th>目撃</th>
            <td>{{ $diary->has_sighting ? '有' : '無' }}</td>
        </tr>
        <tr>
            <th>目撃内容</th>
            <td class="text">
                {{ $diary->sighting_details ?: 'なし' }}
            </td>
        </tr>
    </table>

    <h2>銃・弾</h2>

    <table>
        <tr>
            <th>銃の持ち出し</th>
            <td>{{ $diary->has_gun ? '有' : '無' }}</td>
            <th>弾の使用</th>
            <td>
                {{ $diary->has_used_ammunition ? '有' : '無' }}
            </td>
        </tr>
    </table>

    <table class="ammunition">
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
                <td>{{ $ammunitionTotal }}</td>
            </tr>
        </tbody>
    </table>

    <h2>注釈</h2>

    <div class="text">
        {{ $diary->notes ?: '注釈はありません。' }}
    </div>

    <h2>記録情報</h2>

    <table>
        <tr>
            <th>作成日時</th>
            <td>
                {{ $diary->created_at
    ->format('Y年m月d日 H:i') }}
            </td>
        </tr>
        <tr>
            <th>更新日時</th>
            <td>
                {{ $diary->updated_at
    ->format('Y年m月d日 H:i') }}
            </td>
       
 </tr>
    </table>
</body>
</html>