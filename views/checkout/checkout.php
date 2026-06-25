<!-- views/checkout/checkout.php -->
<div class="mb-8">
    <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Finalizar Compra</h1>
    <p class="text-lg text-slate-400">Escolha como deseja pagar seu pedido.</p>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-md mb-6 font-medium">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Formulário de Pagamento -->
    <div class="glass-panel p-8 rounded-2xl">
        <h2 class="text-2xl font-bold text-white mb-6">Pagamento</h2>
        
        <form action="/checkout" method="POST" class="space-y-6">
            <div>
                <label for="payment_method" class="block text-sm font-medium text-slate-300 mb-2">Método de Pagamento</label>
                <select name="payment_method" id="payment_method" required
                        class="w-full bg-surface-800 border border-white/10 rounded-md px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                    <option value="CREDIT_CARD">Cartão de Crédito</option>
                    <option value="PIX">PIX</option>
                </select>
            </div>

            <!-- Dados do Cartão (Simulação) -->
            <div id="credit_card_info" class="space-y-4">
                <div>
                    <label for="card_number" class="block text-sm font-medium text-slate-300 mb-2">Número do Cartão</label>
                    <input type="text" id="card_number" name="card_number" placeholder="0000 0000 0000 0000"
                           class="w-full bg-surface-800 border border-white/10 rounded-md px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-slate-300 mb-2">Validade</label>
                        <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/AA"
                               class="w-full bg-surface-800 border border-white/10 rounded-md px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                    <div>
                        <label for="cvv" class="block text-sm font-medium text-slate-300 mb-2">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123"
                               class="w-full bg-surface-800 border border-white/10 rounded-md px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white font-medium py-4 rounded-xl text-lg transition-all shadow-[0_0_15px_rgba(59,130,246,0.3)] hover:-translate-y-0.5 mt-8">
                Confirmar Pagamento
            </button>
        </form>
    </div>

    <!-- Resumo do Pedido -->
    <div class="glass-panel p-8 rounded-2xl h-fit">
        <h2 class="text-xl font-bold mb-6 text-white border-b border-white/10 pb-4">Resumo do Pedido</h2>
        
        <div class="space-y-4 mb-6">
            <?php foreach ($items as $item): ?>
                <div class="flex justify-between items-center">
                    <div class="flex-1">
                        <p class="text-white font-medium line-clamp-1"><?= htmlspecialchars($item['title']) ?></p>
                        <p class="text-sm text-slate-400">Qtd: <?= $item['quantity'] ?></p>
                    </div>
                    <span class="text-slate-300 font-medium ml-4">R$ <?= number_format($item['price'], 2, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="border-t border-white/10 pt-4 flex justify-between items-center">
            <span class="text-lg font-bold text-white">Total a Pagar</span>
            <span class="text-2xl font-bold text-brand-500">R$ <?= number_format($total, 2, ',', '.') ?></span>
        </div>
    </div>
    
</div>

<script>
    document.getElementById('payment_method').addEventListener('change', function(e) {
        const ccInfo = document.getElementById('credit_card_info');
        if (e.target.value === 'PIX') {
            ccInfo.style.display = 'none';
        } else {
            ccInfo.style.display = 'block';
        }
    });
</script>
