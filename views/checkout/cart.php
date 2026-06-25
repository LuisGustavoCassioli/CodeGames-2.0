<!-- views/checkout/cart.php -->
<div class="mb-8">
    <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Seu Carrinho</h1>
    <p class="text-lg text-slate-400">Revise os itens antes de finalizar a compra.</p>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-md mb-6 font-medium">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (empty($items)): ?>
    <div class="glass-panel p-12 text-center rounded-2xl">
        <p class="text-slate-400 mb-6 text-lg">Seu carrinho está vazio.</p>
        <a href="/" class="inline-block bg-brand-500 hover:bg-brand-600 text-white font-medium py-3 px-8 rounded-md transition-all shadow-[0_0_15px_rgba(59,130,246,0.3)] hover:-translate-y-0.5">Voltar para a Loja</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Lista de Itens -->
        <div class="lg:col-span-2 flex flex-col gap-4">
            <?php foreach ($items as $item): ?>
                <div class="glass-panel p-4 rounded-xl flex items-center justify-between shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-black rounded-lg overflow-hidden shrink-0 border border-white/5">
                            <?php if ($item['image_url']): ?>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-slate-700 text-xs text-slate-400">S/ Img</div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <a href="/product?slug=<?= htmlspecialchars($item['slug']) ?>" class="font-semibold text-lg text-white hover:text-brand-400 transition-colors line-clamp-1">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                            <div class="flex items-center gap-2 mt-2">
                                <form action="/cart/update" method="POST" class="inline">
                                    <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['item_id']) ?>">
                                    <input type="hidden" name="current_quantity" value="<?= $item['quantity'] ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="w-6 h-6 flex items-center justify-center bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md transition-colors text-sm font-bold border border-white/10" <?= $item['quantity'] <= 1 ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>-</button>
                                </form>
                                <span class="text-sm font-medium text-white min-w-[1rem] text-center"><?= $item['quantity'] ?></span>
                                <form action="/cart/update" method="POST" class="inline">
                                    <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['item_id']) ?>">
                                    <input type="hidden" name="current_quantity" value="<?= $item['quantity'] ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="w-6 h-6 flex items-center justify-center bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md transition-colors text-sm font-bold border border-white/10">+</button>
                                </form>
                            </div>
                            <p class="font-bold text-brand-500 mt-1">R$ <?= number_format($item['price'], 2, ',', '.') ?></p>
                        </div>
                    </div>
                    
                    <form action="/cart/remove" method="POST" class="ml-4">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['item_id']) ?>">
                        <button type="submit" class="text-red-400 hover:text-red-300 hover:bg-red-500/10 px-3 py-2 rounded-md transition-colors text-sm font-medium border border-transparent hover:border-red-500/20">
                            Remover
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Resumo do Pedido -->
        <div class="glass-panel p-8 rounded-2xl h-fit lg:sticky lg:top-24">
            <h2 class="text-xl font-bold mb-6 text-white">Resumo do Pedido</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-md mb-4 text-sm font-medium">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Formulário de Cupom -->
            <div class="mb-6 pb-6 border-b border-white/10">
                <?php if ($coupon): ?>
                    <div class="flex items-center justify-between bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-3">
                        <div class="flex flex-col">
                            <span class="text-emerald-400 font-bold tracking-wide uppercase text-sm"><?= htmlspecialchars($coupon['code']) ?></span>
                            <span class="text-emerald-500/70 text-xs">Cupom aplicado</span>
                        </div>
                        <form action="/cart/remove-coupon" method="POST">
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-medium p-2">Remover</button>
                        </form>
                    </div>
                <?php else: ?>
                    <form action="/cart/apply-coupon" method="POST" class="flex gap-2">
                        <input type="text" name="coupon_code" placeholder="Código de desconto" class="w-full bg-slate-900/50 border border-white/10 rounded-lg px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-sm uppercase">
                        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white font-medium px-4 py-2 rounded-lg transition-colors border border-white/10 text-sm whitespace-nowrap">Aplicar</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="flex justify-between items-center mb-3">
                <span class="text-slate-400">Subtotal</span>
                <span class="font-medium text-white">R$ <?= number_format($totals['subtotal'], 2, ',', '.') ?></span>
            </div>

            <?php if ($totals['discount'] > 0): ?>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-emerald-400">Desconto</span>
                    <span class="font-medium text-emerald-400">- R$ <?= number_format($totals['discount'], 2, ',', '.') ?></span>
                </div>
            <?php endif; ?>
            
            <div class="flex justify-between items-center mb-8 pt-4 border-t border-white/10 mt-1">
                <span class="text-lg font-bold text-white">Total</span>
                <span class="text-2xl font-bold text-brand-500">R$ <?= number_format($totals['total'], 2, ',', '.') ?></span>
            </div>

            <a href="/checkout" class="block w-full text-center bg-brand-500 hover:bg-brand-600 text-white font-medium py-4 rounded-xl text-lg transition-all shadow-[0_0_15px_rgba(59,130,246,0.3)] hover:-translate-y-0.5">Finalizar Compra</a>
            <a href="/" class="block w-full text-center mt-4 text-slate-300 hover:text-white hover:bg-white/5 font-medium py-3 rounded-xl transition-all border border-white/10">Continuar Comprando</a>
        </div>
    </div>
<?php endif; ?>
