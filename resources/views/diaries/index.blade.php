@extends('layouts.app')

@section('title', '日誌一覧')

@section('content')
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

        .primary-link {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            background: #166534;
            color: #ffffff;
            text-decoration: none;
        }

        .primary-link:hover {
            background: #14532d;
        }

        .status {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 6px;
            background: #dcfce7;
            color: #166534;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            white-space: nowrap;
        }

        th {
            background: #f9fafb;
        }

        .type-hunting {
            color: #166534;
            font-weight: 700;
        }

        .type-control {
            color: #b45309;
            font-weight: 700;
        }

        .detail-link {
            color: #166534;
            font-weight: 600;
        }

        .empty-message {
            padding: 40px 16px;
            text-align: center;
            color: #6b7280;
        }

        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 24px;
        }

        .pagination a {
            padding: 8px 14px;
            border: 1px solid #166534;
            border-radius: 6px;
            color: #166534;
            text-decoration: none;
        }

        .filter-card {
            margin-bottom: 24px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,
                    minmax(160px, 1fr));
            gap: 16px;
        }

        .filter-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .filter-field input,
        .filter-field select {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #ffffff;
            font-size: 15px;
        }

        .filter-actions {
            display: flex;
            gap: 16px;
            align-items: center;
            margin-top: 20px;
        }

        .search-button {
            padding: 10px 22px;
            border: 0;
            border-radius: 6px;
            background: #166534;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .reset-link {
            color: #4b5563;
        }

        .filter-error {
            color: #dc2626;
        }

        .pdf-button {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            background: #0369a1;
            color: #ffffff;
            font-weight: 600;
            text-decoration: none;
        }

        .pdf-button:hover {
            background: #075985;
        }

        .pagination-disabled {
            padding: 8px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            color: #9ca3af;
        }
    </style>

    @if (session('status'))
        <div class="status">
            {{ session('status') }}
        </div>
    @endif

    <div class="page-header">
        <h2>日誌一覧</h2>

        <a class="primary-link" href="{{ route('diaries.create') }}">
            新しい日誌を作成
        </a>
    </div>
    <div class="card filter-card">
        <form method="GET" action="{{ route('diaries.index') }}">
            <div class="filter-grid">
                <div class="filter-field">
                    <label for="fiscal_year">年度</label>

                    <select id="fiscal_year" name="fiscal_year">
                        <option value="">すべて</option>

                        @foreach ($fiscalYears as $year)
                            <option value="{{ $year }}" @selected(
                                (string) request('fiscal_year')
                                === (string) $year
                            )>
                                {{ $year }}年度
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label for="date_from">開始日</label>

                    <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="filter-field">
                    <label for="date_to">終了日</label>

                    <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="filter-field">
                    <label for="diary_type">区分</label>

                    <select id="diary_type" name="diary_type">
                        <option value="">すべて</option>
                        <option value="hunting" @selected(
                            request('diary_type') === 'hunting'
                        )>
                            狩猟
                        </option>
                        <option value="control" @selected(
                            request('diary_type') === 'control'
                        )>
                            有害駆除
                        </option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="hunting_method">猟種</label>

                    <select id="hunting_method" name="hunting_method">
                        <option value="">すべて</option>
                        <option value="gun" @selected(
                            request('hunting_method') === 'gun'
                        )>
                            銃猟
                        </option>
                        <option value="trap" @selected(
                            request('hunting_method') === 'trap'
                        )>
                            罠猟
                        </option>
                        <option value="net" @selected(
                            request('hunting_method') === 'net'
                        )>
                            網猟
                        </option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="activity">活動内容</label>

                    <select id="activity" name="activity">
                        <option value="">すべて</option>
                        <option value="feeding" @selected(
                            request('activity') === 'feeding'
                        )>
                            餌撒き
                        </option>
                        <option value="patrol" @selected(
                            request('activity') === 'patrol'
                        )>
                            見廻り
                        </option>
                        <option value="capture" @selected(
                            request('activity') === 'capture'
                        )>
                            捕獲
                        </option>
                        <option value="miss" @selected(
                            request('activity') === 'miss'
                        )>
                            空振り
                        </option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="transportation">移動手段</label>

                    <select id="transportation" name="transportation">
                        <option value="">すべて</option>
                        <option value="car" @selected(
                            request('transportation') === 'car'
                        )>
                            車
                        </option>
                        <option value="motorcycle" @selected(
                            request('transportation')
                            === 'motorcycle'
                        )>
                            バイク
                        </option>
                    </select>
                </div>

                @foreach ([
                        'has_capture' => '捕獲',
                        'has_sighting' => '目撃',
                        'has_gun' => '銃の持ち出し',
                        'has_used_ammunition' => '弾の使用',
                    ] as $name => $label)
                    <div class="filter-field">
                        <label for="{{ $name }}">{{ $label }}</label>

                        <select id="{{ $name }}" name="{{ $name }}">
                            <option value="">すべて</option>
                            <option value="1" @selected(request($name) === '1')>
                                有
                            </option>
                            <option value="0" @selected(request($name) === '0')>
                                無
                            </option>
                        </select>
                    </div>
                @endforeach

                <div class="filter-field">
                    <label for="location">場所</label>

                    <input id="location" type="text" name="location" value="{{ request('location') }}" placeholder="場所を検索">
                </div>

                <div class="filter-field">
                    <label for="sort">並び順</label>

                    <select id="sort" name="sort">
                        @foreach ([
                                'date_asc' => '日付が古い順',
                                'date_desc' => '日付が新しい順',
                                'overall_asc' => '全体番号の昇順',
                                'overall_desc' => '全体番号の降順',
                                'type_asc' => '区分番号の昇順',
                                'type_desc' => '区分番号の降順',
                            ] as $value => $label)
                            <option value="{{ $value }}" @selected(
                                request('sort', 'date_asc')
                                === $value
                            )>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @error('date_to')
                <p class="filter-error">{{ $message }}</p>
            @enderror

            <div class="filter-actions">
                <button class="search-button" type="submit">
                    検索する
                </button>

                <a class="pdf-button" href="{{ route(
        'diaries.pdf.list',
        request()->except('page')
    ) }}">
                    検索結果をPDF保存
                </a>

                <a href="{{ route('diaries.pdf.list', array_merge(request()->query(), ['print' => 1])) }}" target="_blank"
                    rel="noopener" class="pdf-button">
                    検索結果を印刷
                </a>

                <a class="reset-link" href="{{ route('diaries.index') }}">
                    条件を解除
                </a>
            </div>
        </form>
    </div>
    <p>
        検索結果：
        <strong>{{ $diaries->total() }}件</strong>

        @if ($diaries->total() > 0)
            （{{ $diaries->firstItem() }}
            〜
            {{ $diaries->lastItem() }}件目を表示）
        @endif
    </p>
    <div class="card">
        @if ($diaries->isEmpty())
            <div class="empty-message">
                <p>まだ日誌が登録されていません。</p>

                <a href="{{ route('diaries.create') }}">
                    最初の日誌を作成する
                </a>
            </div>
        @else
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>全体番号</th>
                            <th>区分番号</th>
                            <th>日付</th>
                            <th>区分</th>
                            <th>場所</th>
                            <th>天候</th>
                            <th>捕獲</th>
                            <th>目撃</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($diaries as $diary)
                                    <tr>
                                        <td>
                                            {{ $diary->overall_display_number }}
                                        </td>

                                        <td>
                                            {{ $diary->type_display_number }}
                                        </td>

                                        <td>
                                            {{ $diary->activity_date
                                ->format('Y年m月d日') }}
                                        </td>

                                        <td>
                                            <span
                                                class="
                                                                                                                                                                                                                                                                                                                                                                                                                {{ $diary->diary_type->value
                                === 'hunting'
                                ? 'type-hunting'
                                : 'type-control' }}
                                                                                                                                                                                                                                                                                                                                                                                                            ">
                                                {{ $diary->diary_type->label() }}
                                            </span>
                                        </td>

                                        <td>{{ $diary->location }}</td>

                                        <td>
                                            {{ $diary->weather ?? '未入力' }}
                                        </td>

                                        <td>
                                            {{ $diary->has_capture
                                ? '有'
                                : '無' }}
                                        </td>

                                        <td>
                                            {{ $diary->has_sighting
                                ? '有'
                                : '無' }}
                                        </td>

                                        <td>
                                            <a class="detail-link" href="{{ route(
                                'diaries.show',
                                $diary
                            ) }}">
                                                詳細
                                            </a>

                                            <a href="{{ route('diaries.pdf', ['diary' => $diary, 'print' => 1]) }}" target="_blank"
                                                rel="noopener">
                                                印刷
                                            </a>
                                        </td>
                                    </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                @if ($diaries->hasPages())
                    <nav class="pagination" aria-label="日誌一覧のページ送り">
                        @if ($diaries->onFirstPage())
                            <span class="pagination-disabled">
                                前へ
                            </span>
                        @else
                            <a href="{{ $diaries->previousPageUrl() }}">
                                前へ
                            </a>
                        @endif

                        <span>
                            {{ $diaries->currentPage() }}
                            /
                            {{ $diaries->lastPage() }}ページ
                        </span>

                        @if ($diaries->hasMorePages())
                            <a href="{{ $diaries->nextPageUrl() }}">
                                次へ
                            </a>
                        @else
                            <span class="pagination-disabled">
                                次へ
                            </span>
                        @endif
                    </nav>
                @endif

            </div>
        @endif
    </div>
@endsection