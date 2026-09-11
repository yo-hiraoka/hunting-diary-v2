<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', '出猟日誌')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f4f6;
            color: #1f2937;
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                "Hiragino Kaku Gothic ProN",
                "Yu Gothic",
                sans-serif;
        }

        header {
            background: #14532d;
            color: #ffffff;
        }

        .header-inner {
            width: min(100% - 32px, 1100px);
            min-height: 64px;
            margin: auto;
            display: flex;
            gap: 24px;
            align-items: center;
            justify-content: space-between;
        }

        .site-title {
            flex-shrink: 0;
            margin: 0;
            font-size: 20px;
        }

        .site-title a {
            color: #ffffff;
            text-decoration: none;
        }

        .user-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            justify-content: flex-end;
        }

        .user-menu form {
            margin: 0;
        }

        .header-link {
            color: #ffffff;
            text-decoration: none;
        }

        .header-link:hover,
        .header-link:focus {
            text-decoration: underline;
        }

        .user-name {
            padding-left: 16px;
            border-left: 1px solid rgb(255 255 255 / 40%);
        }

        .logout-button {
            padding: 8px 14px;
            border: 1px solid #ffffff;
            border-radius: 6px;
            background: transparent;
            color: #ffffff;
            cursor: pointer;
        }

        .logout-button:hover {
            background: rgb(255 255 255 / 15%);
        }

        main {
            width: min(100% - 32px, 1100px);
            margin: 32px auto;
        }

        .card {
            padding: 24px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgb(0 0 0 / 8%);
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        @media (max-width: 760px) {
            .header-inner {
                padding: 14px 0;
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .user-menu {
                width: 100%;
                gap: 10px 14px;
                justify-content: flex-start;
            }

            .user-name {
                width: 100%;
                padding: 8px 0 0;
                border-top: 1px solid rgb(255 255 255 / 30%);
                border-left: 0;
            }

            main {
                width: min(100% - 20px, 1100px);
                margin: 18px auto;
            }

            .card {
                padding: 16px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header>div {
                display: flex;
                flex-wrap: wrap;
            }

            .filter-grid,
            .time-grid,
            .ammunition-grid,
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .filter-actions,
            .actions {
                flex-wrap: wrap;
            }

            fieldset {
                padding: 14px;
            }

            .table-wrapper {
                margin-right: -16px;
                margin-left: -16px;
                padding: 0 16px;
            }

            table {
                font-size: 14px;
            }

            th,
            td {
                padding: 9px;
            }
        }

        @media (max-width: 440px) {

            .header-link,
            .logout-button {
                font-size: 14px;
            }

            .choices {
                flex-direction: column;
                align-items: flex-start;
                gap: 9px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="date"],
            input[type="time"],
            input[type="number"],
            select,
            textarea {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-inner">
            <h1 class="site-title">
                <a href="{{ route('diaries.index') }}">
                    出猟日誌
                </a>
            </h1>

            <nav class="user-menu" aria-label="メインメニュー">
                <a class="header-link" href="{{ route('diaries.index') }}">
                    日誌一覧
                </a>

                <a class="header-link" href="{{ route('diaries.create') }}">
                    新規作成
                </a>

                <a class="header-link" href="{{ route('diaries.trash') }}">
                    ゴミ箱
                </a>

                <a class="header-link" href="{{ route('settings.location.edit') }}">
                    地域設定
                </a>

                <span class="user-name">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="logout-button" type="submit">
                        ログアウト
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>