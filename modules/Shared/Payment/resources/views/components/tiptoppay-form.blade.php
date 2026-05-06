<style>
    html,
    body {
        width: 100%;
        min-height: 100%;
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden;
        background: #435172;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    .ttp-page {
        width: 100vw;
        min-height: 100vh;
        margin-left: calc(50% - 50vw);
        margin-right: calc(50% - 50vw);
        background: #435172;
        color: #2c3446;
        font-family: Arial, sans-serif;
    }

    .ttp-spacer {
        height: 48px;
    }

    .ttp-modal {
        width: 100vw;
        max-width: 648px;
        min-height: 1040px;
        margin: 0 auto;
        background: #f5f8fc;
        padding: 22px 32px 38px;
    }

    .ttp-close {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 10px;
        color: #8f96a4;
        font-size: 34px;
        line-height: 1;
    }

    .ttp-title {
        margin: 0 0 14px;
        font-size: 24px;
        font-weight: 600;
        line-height: 1.2;
    }

    .ttp-amount {
        margin: 0 0 54px;
        font-size: 28px;
        font-weight: 600;
        line-height: 1;
    }

    .ttp-alert {
        margin: 16px 0 0;
        padding: 16px;
        border-radius: 12px;
        text-align: center;
        font-size: 16px;
        line-height: 1.35;
        font-weight: 700;
    }

    .ttp-alert-success {
        background: #e5f6ec;
        color: #116b35;
    }

    .ttp-alert-error {
        background: #fdecec;
        color: #9f1d1d;
    }

    #message {
        margin-top: 20px;
        text-align: center;
        font-size: 16px;
        font-weight: 700;
    }

    .ttp-secured {
        margin-top: 82px;
        color: #c7ceda;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
    }

    @media (max-width: 520px) {
        .ttp-modal {
            width: 100vw;
            max-width: none;
            min-height: 1040px;
            margin: 0;
        }
    }
</style>

<div class="ttp-page">
    <div class="ttp-spacer"></div>

    <div class="ttp-modal">
        <div class="ttp-close">&times;</div>

        <h1 class="ttp-title">{{ $paymentData['description'] }}</h1>
        <div class="ttp-amount">{{ number_format((float)$paymentData['amount'], 2, ',', ' ') }} ₸</div>

        @if($payment->status === \Modules\Shared\Payment\Domain\Models\PaymentStatus::COMPLETED)
            <div class="ttp-alert ttp-alert-success">
                Заказ успешно оплачен.
            </div>
        @else
            <div id="message"></div>
        @endif

        <div class="ttp-secured">Secured by TipTop Pay</div>
    </div>

    <div class="ttp-spacer"></div>
</div>

<script src="https://widget.tiptoppay.kz/bundles/widget.js"></script>
<script>
    @if($payment->status === \Modules\Shared\Payment\Domain\Models\PaymentStatus::CREATED)
    window.onload = function () {
        var widget = new tiptop.Widget({
            language: "ru-RU",
            applePaySupport: true,
            googlePaySupport: true,
        });
        var messageElement = document.getElementById('message');
        widget.pay('auth', {
            publicId: @json($paymentData['publicId']),
            description: @json($paymentData['description']),
            amount: @json($paymentData['amount']),
            currency: @json($paymentData['currency']),
            invoiceId: @json((string)$paymentData['invoiceId']),
            accountId: @json((string)$paymentData['accountId']),
            skin: "mini",
            data: {}
        }, {
            onSuccess: function (options) {
                messageElement.innerHTML = '<p style="color: green;">Оплата прошла успешно.</p>';
            },
            onFail: function (reason, options) {
                messageElement.innerHTML = '<p style="color: red;">Ошибка оплаты. Пожалуйста, попробуйте ещё раз.</p>';
            },
            onComplete: function (paymentResult, options) {
                messageElement.innerHTML = '<p style="color: green;">Заказ успешно оплачен.</p>';
            }
        });
    };
    @endif
</script>
