<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto bg-gray-800 rounded-lg shadow-lg border border-gray-700 overflow-hidden">
        <div class="p-6 md:p-8">
            <h1 class="text-3xl font-bold text-white mb-2 text-center">Pagamento via PIX</h1>
            <p class="text-gray-400 text-center mb-8">Escaneie o QR Code ou utilize a chave Copia e Cola para finalizar sua compra.</p>

            <div class="bg-gray-900 rounded-lg p-6 flex flex-col items-center justify-center mb-8 border border-gray-700">
                <div class="bg-white p-4 rounded-lg shadow-inner mb-6">
                    <!-- Gerando um QR Code falso via api.qrserver.com, usando o ID do pedido como dado -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=PIX_MOCK_<?= urlencode($order['id']) ?>" alt="QR Code PIX" class="w-48 h-48">
                </div>
                
                <h3 class="text-xl text-white font-semibold mb-2">Total a pagar: R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></h3>
                <p class="text-gray-400 text-sm mb-4">O pagamento será aprovado em instantes.</p>

                <div class="w-full">
                    <label class="block text-gray-400 text-sm font-medium mb-2">PIX Copia e Cola</label>
                    <div class="flex items-center">
                        <input type="text" readonly value="00020101021126580014br.gov.bcb.pix0136<?= $order['id'] ?>5204000053039865405<?= number_format($order['total_amount'], 2, '.', '') ?>5802BR5909CodeGames6009Sao Paulo62070503***6304" class="w-full bg-gray-700 border border-gray-600 text-gray-300 rounded-l-lg py-3 px-4 focus:outline-none text-sm" id="pixCode">
                        <button type="button" onclick="copyPix()" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-r-lg transition-colors border border-purple-600 hover:border-purple-700">
                            Copiar
                        </button>
                    </div>
                    <p id="copyFeedback" class="text-green-400 text-xs mt-2 hidden">Código copiado com sucesso!</p>
                </div>
            </div>

            <div class="bg-blue-900/30 border border-blue-800 rounded-lg p-4 mb-8 text-sm text-blue-300">
                <strong>Aviso (Mock):</strong> Isso é apenas uma simulação. Clique no botão abaixo para simular que o pagamento foi processado pelo banco e as chaves foram liberadas.
            </div>

            <div class="flex justify-center">
                <form action="/checkout/pix/process?id=<?= $order['id'] ?>" method="POST">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                        Simular Pagamento Confirmado
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function copyPix() {
        var copyText = document.getElementById("pixCode");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Mobile
        navigator.clipboard.writeText(copyText.value).then(() => {
            const feedback = document.getElementById("copyFeedback");
            feedback.classList.remove("hidden");
            setTimeout(() => {
                feedback.classList.add("hidden");
            }, 3000);
        });
    }
</script>
