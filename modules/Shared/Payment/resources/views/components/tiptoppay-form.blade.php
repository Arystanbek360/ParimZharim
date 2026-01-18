<div style="text-align: center; padding: 20px;">
    <div id="message" style="margin-top: 20px;"></div> <!-- Сообщение о статусе оплаты -->

    <script src="https://widget.tiptoppay.kz/bundles/widget.js"></script>
    <script>
        function launchPayment() {
            var widget = new tiptop.Widget({
                language: "ru-RU",
                applePaySupport: true,
                googlePaySupport: true,
            });
            widget.pay('auth', {
                publicId: '{{ $paymentData['publicId'] }}',
                description: '{{ $paymentData['description'] }}',
                amount: {{ $paymentData['amount'] }},
                currency: '{{ $paymentData['currency'] }}',
                invoiceId: '{{ $paymentData['invoiceId'] }}',
                accountId: '{{ $paymentData['accountId'] }}',
                skin: "mini",
                data: {}
            }, {
                onSuccess: function(options) {
                    console.log('Оплата прошла успешно');
                    document.getElementById('message').innerHTML = '<p style="color: green;">Оплата прошла успешно.</p>';
                },
                onFail: function(reason, options) {
                    console.log('Ошибка оплаты', reason);
                    document.getElementById('message').innerHTML = '<p style="color: red;">Ошибка оплаты. Пожалуйста, попробуйте еще раз.</p>';
                },
                onComplete: function(paymentResult, options) {
                    console.log('Процесс оплаты завершен', paymentResult);
                    document.getElementById('message').innerHTML = '<p style="color: green;">Заказ успешно оплачен.</p>';
                }
            });
        }

        window.onload = launchPayment; // Автоматически запускать процесс оплаты при загрузке страницы
    </script>
</div>
