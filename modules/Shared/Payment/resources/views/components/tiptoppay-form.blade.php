<div style="text-align: center; padding: 20px;">
    <div id="message" style="margin-top: 20px;"></div> <!-- Сообщение о статусе оплаты -->

    <script src="https://widget.tiptoppay.kz/bundles/widget.js"></script>
    <script>
        function launchPayment() {
            var widget = new tiptop.Widget();
            widget.oncomplete = function(paymentResult) {
                console.log('Процесс оплаты завершен', paymentResult);
            };

            widget.start({
                publicTerminalId: @json($paymentData['publicTerminalId']),
                description: @json($paymentData['description']),
                amount: @json((float)$paymentData['amount']),
                currency: @json($paymentData['currency']),
                externalId: @json($paymentData['externalId']),
                paymentSchema: 'Dual',
                culture: 'ru-RU',
                tokenize: true,
                retryPayment: false,
                userInfo: {
                    accountId: @json((string)$paymentData['accountId'])
                }
            }).then(function(paymentResult) {
                console.log('Оплата прошла успешно', paymentResult);
                document.getElementById('message').innerHTML = '<p style="color: green;">Заказ успешно оплачен.</p>';
            }).catch(function(error) {
                console.log('Ошибка оплаты', error);
                document.getElementById('message').innerHTML = '<p style="color: red;">Ошибка оплаты. Пожалуйста, попробуйте еще раз.</p>';
            });
        }

        window.onload = launchPayment; // Автоматически запускать процесс оплаты при загрузке страницы
    </script>
</div>
