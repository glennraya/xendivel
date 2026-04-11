<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment not completed</title>
    <style>
        :root {
            color-scheme: light;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background: #f5f7f8;
            color: #18181b;
        }

        main {
            width: min(100% - 32px, 560px);
            padding: 32px;
            border: 1px solid #d5d9dd;
            border-radius: 8px;
            background: #ffffff;
        }

        .eyebrow {
            margin: 0 0 12px;
            color: #9f1239;
            font-size: 14px;
            font-weight: 700;
        }

        h1 {
            margin: 0;
            font-size: 32px;
            line-height: 1.15;
        }

        p {
            margin: 16px 0 0;
            color: #3f3f46;
            font-size: 16px;
            line-height: 1.6;
        }

        a {
            display: inline-flex;
            margin-top: 24px;
            padding: 12px 16px;
            border-radius: 8px;
            background: #18181b;
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <main>
        <p class="eyebrow">Payment not completed</p>
        <h1>No payment was confirmed.</h1>
        <p>You can try again or choose another payment method.</p>
        <a href="{{ url('/') }}">Return home</a>
    </main>
</body>
</html>
