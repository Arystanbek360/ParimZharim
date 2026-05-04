<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Результат оплаты</title>
    <style>
        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
            background: #435172;
            font-family: Arial, sans-serif;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #2c3446;
        }

        .panel {
            width: 100%;
            max-width: 520px;
            padding: 28px;
            border-radius: 12px;
            background: #f5f8fc;
            text-align: center;
        }

        .message {
            margin: 0;
            font-size: 20px;
            line-height: 1.35;
            font-weight: 600;
            color: {{ $success ? '#116b35' : '#9f1d1d' }};
        }
    </style>
</head>
<body>
<div class="page">
    <div class="panel">
        <p class="message">{{ $message }}</p>
    </div>
</div>
</body>
</html>
