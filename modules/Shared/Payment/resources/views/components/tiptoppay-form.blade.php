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

    .ttp-section-title {
        margin: 0 0 38px;
        font-size: 24px;
        font-weight: 600;
        line-height: 1.2;
    }

    .ttp-cards {
        display: grid;
        gap: 12px;
        margin-bottom: 36px;
    }

    .ttp-card-button {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        min-height: 82px;
        padding: 20px 24px;
        border: 0;
        border-radius: 14px;
        background: #e9edf4;
        color: #2c3446;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        font-size: 20px;
        font-weight: 600;
        text-align: left;
    }

    .ttp-card-button:active {
        background: #dde4ef;
    }

    .ttp-card-main {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ttp-card-meta {
        flex: 0 0 auto;
        color: #8f96a4;
        font-size: 16px;
        font-weight: 600;
    }

    .ttp-primary-button {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        min-height: 84px;
        border: 0;
        border-radius: 10px;
        background: #8ea8f4;
        color: #fff;
        font-size: 22px;
        font-weight: 600;
    }

    .ttp-primary-button:active {
        background: #7f99e8;
    }

    .ttp-divider {
        display: none;
        margin: 30px 0 36px;
        height: 1px;
        background: #dfe5ef;
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

    .ttp-note {
        margin: 64px 0 0;
        color: #2c3446;
        font-size: 18px;
        line-height: 1.35;
        font-weight: 600;
    }

    .ttp-terms {
        margin: 28px 0 0;
        color: #2c3446;
        font-size: 18px;
        line-height: 1.35;
        font-weight: 600;
    }

    .ttp-terms a {
        color: #4776ff;
        text-decoration: none;
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
            @if(count($savedCards) > 0)
                <h2 class="ttp-section-title">Оплатить картой</h2>

                <div class="ttp-cards">
                    @foreach($savedCards as $savedCard)
                        <button
                            type="button"
                            wire:click="payWithSavedCard({{ $savedCard['id'] }})"
                            wire:loading.attr="disabled"
                            class="ttp-card-button"
                        >
                            <span class="ttp-card-main">
                                {{ $savedCard['card_type'] ?: 'Карта' }}
                                {{ $savedCard['card_mask'] ?: '**** ' . ($savedCard['card_last_four'] ?: '----') }}
                            </span>
                            <span class="ttp-card-meta">
                                {{ $savedCard['card_exp_date'] ?: '' }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <button
                    type="button"
                    onclick="launchPayment()"
                    class="ttp-primary-button"
                >
                    Оплатить новой картой
                </button>

                <div class="ttp-note">При оплате данные карты сохранятся</div>

                <div class="ttp-terms">
                    Нажимая «Оплатить», вы принимаете условия
                    <a href="#">Оферты</a> и даете
                    <a href="#">Согласие на обработку персональных данных</a>.
                </div>

                <div class="ttp-secured">Secured by TipTop Pay</div>
            @endif

            @if($savedCardPaymentSuccess)
                <div class="ttp-alert ttp-alert-success">
                    {{ $savedCardPaymentSuccess }}
                </div>
            @endif

            @if($savedCardPaymentError)
                <div class="ttp-alert ttp-alert-error">
                    {{ $savedCardPaymentError }}
                </div>
            @endif

            <div id="message"></div> <!-- Сообщение о статусе оплаты -->
        @endif

        <script src="https://widget.tiptoppay.kz/bundles/widget.js"></script>
        <script>
            function launchPayment() {
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
                    onSuccess: function(options) {
                        console.log('Оплата прошла успешно');
                        messageElement.innerHTML = '<p style="color: green;">Оплата прошла успешно.</p>';
                    },
                    onFail: function(reason, options) {
                        console.log('Ошибка оплаты', reason);
                        messageElement.innerHTML = '<p style="color: red;">Ошибка оплаты. Пожалуйста, попробуйте еще раз.</p>';
                    },
                    onComplete: function(paymentResult, options) {
                        console.log('Процесс оплаты завершен', paymentResult);
                        messageElement.innerHTML = '<p style="color: green;">Заказ успешно оплачен.</p>';
                    }
                });
            }

            @if(count($savedCards) === 0 && $payment->status === \Modules\Shared\Payment\Domain\Models\PaymentStatus::CREATED)
                window.onload = launchPayment; // Автоматически запускать процесс оплаты при загрузке страницы
            @endif
        </script>
    </div>

    <div class="ttp-spacer"></div>
</div>
