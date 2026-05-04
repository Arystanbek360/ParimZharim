<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>3-D Secure</title>
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
            font-size: 18px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="panel">Открываем подтверждение 3-D Secure...</div>
</div>

<form name="threeDsForm" action="{{ $acsUrl }}" method="POST">
    <input type="hidden" name="PaReq" value="{{ $paReq }}">
    <input type="hidden" name="MD" value="{{ $transactionId }}">
    <input type="hidden" name="TermUrl" value="{{ $termUrl }}">
</form>

<script>
    window.onload = function () {
        document.forms.threeDsForm.submit();
    };
</script>
</body>
</html>
