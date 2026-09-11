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
                -apple-system, BlinkMacSystemFont, "Segoe UI",
                "Hiragino Kaku Gothic ProN", "Yu Gothic", sans-serif;
        }

        .container {
            width: min(100% - 32px, 460px);
            margin: 60px auto;
        }

        .card {
            padding: 32px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgb(0 0 0 / 8%);
        }

        h1 {
            margin: 0 0 24px;
            font-size: 24px;
            text-align: center;
        }

        .field {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
        }

        input:focus {
            border-color: #166534;
            outline: 2px solid rgb(22 101 52 / 15%);
        }

        .checkbox {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .checkbox label {
            margin: 0;
            font-weight: normal;
        }

        button {
            width: 100%;
            padding: 11px 16px;
            border: 0;
            border-radius: 6px;
            background: #166534;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #14532d;
        }

        .link {
            margin-top: 20px;
            text-align: center;
        }

        a {
            color: #166534;
        }

        .error {
            margin: 6px 0 0;
            color: #dc2626;
            font-size: 14px;
        }

        .status {
            margin-bottom: 18px;
            padding: 10px;
            border-radius: 6px;
            background: #dcfce7;
            color: #166534;
        }

        @media (max-width: 520px) {
            .container {
                width: min(100% - 20px, 460px);
                margin: 20px auto;
            }

            .card {
                padding: 20px 16px;
            }

            h1 {
                font-size: 21px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <main class="container">
        <div class="card">
            @yield('content')
        </div>
    </main>
</body>

</html>