@extends('layouts.app')

@section('title', 'ゴミ箱')

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

        .back-link {
            color: #166534;
            font-weight: 600;
        }

        .status {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 6px;
            background: #dcfce7;
            color: #166534;
        }

        .description {
            color: #4b5563;
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

        .actions {
            display: flex;
            gap: 8px;
        }

        .restore-button,
        .force-delete-button {
            padding: 7px 12px;
            border: 0;
            border-radius: 5px;
            color: #ffffff;
            font-weight: 600;
            cursor: pointer;
        }

        .restore-button {
            background: #166534;
        }

        .force-delete-button {
            background: #dc2626;
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

        .pagination-disabled {
            padding: 8px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            color: #9ca3af;
        }
    </style>

    <div class="page-header">
        <h2>ゴミ箱</h2>

        <a class="back-link" href="{{ route('diaries.index') }}">
            日誌一覧へ戻る
        </a>
    </div>

    @if (session('status'))
        <div class="status">
            {{ session('status') }}
        </div>
    @endif

    <div class="card">
        <p class="description">
            ゴミ箱の日誌は自動削除されません。
            完全削除すると元に戻せなくなります。
        </p>

        @if ($diaries->isEmpty())
            <div class="empty-message">
                ゴミ箱は空です。
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
                            <th>削除日時</th>
                            <th>操作</th>
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
                                            {{ $diary->diary_type->label() }}
                                        </td>

                                        <td>{{ $diary->location }}</td>

                                        <td>
                                            {{ $diary->deleted_at
                                ->format('Y年m月d日 H:i') }}
                                        </td>

                                        <td>
                                            <div class="actions">
                                                <form method="POST" action="{{ route(
                                'diaries.restore',
                                $diary
                            ) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button class="restore-button" type="submit">
                                                        元に戻す
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route(
                                'diaries.force-delete',
                                $diary
                            ) }}" onsubmit="
                                                                                                            return confirm(
                                                                                                                '完全に削除します。'
                                                                                                                + '元に戻せませんが'
                                                                                                                + 'よろしいですか？'
                                                                                                            );
                                                                                                        ">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="
                                                                                                                force-delete-button
                                                                                                            " type="submit">
                                                        完全削除
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                @if ($diaries->hasPages())
                    <nav class="pagination">
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

                <span>
                    {{ $diaries->currentPage() }}ページ
                </span>

                @if ($diaries->nextPageUrl())
                    <a href="{{ $diaries->nextPageUrl() }}">
                        次へ
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection