<!-- views/catalog/detail.php -->
<div class="glass-panel flex flex-col md:flex-row gap-8 p-8 rounded-2xl shadow-lg">
    
    <div class="flex-1 rounded-xl overflow-hidden bg-black shadow-inner">
        <?php if ($product['image_url']): ?>
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="w-full h-auto block object-cover">
        <?php else: ?>
            <div class="w-full aspect-video flex items-center justify-center bg-slate-700 text-slate-400">
                Sem Imagem
            </div>
        <?php endif; ?>
    </div>

    <div class="flex-1 flex flex-col justify-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4 bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent"><?= htmlspecialchars($product['title']) ?></h1>
        
        <p class="text-slate-400 mb-6 whitespace-pre-wrap leading-relaxed"><?= htmlspecialchars($product['description'] ?? 'Nenhuma descrição fornecida.') ?></p>
        
        <div class="flex items-center gap-4 mb-8">
            <span class="text-4xl font-bold text-brand-500 tracking-tight">
                R$ <?= number_format($product['price'], 2, ',', '.') ?>
            </span>
            <?php if ($product['stock'] > 0): ?>
                <span class="bg-emerald-500/10 text-emerald-400 px-3 py-1 rounded-full text-sm font-medium border border-emerald-500/20">Em Estoque</span>
            <?php else: ?>
                <span class="bg-red-500/10 text-red-400 px-3 py-1 rounded-full text-sm font-medium border border-red-500/20">Esgotado</span>
            <?php endif; ?>
        </div>

        <form action="/cart" method="POST">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
            <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 disabled:bg-slate-600 disabled:cursor-not-allowed disabled:shadow-none text-white font-medium py-4 rounded-xl text-lg transition-all shadow-[0_0_20px_rgba(59,130,246,0.3)] hover:shadow-[0_0_25px_rgba(59,130,246,0.5)] hover:-translate-y-0.5" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                <?= $product['stock'] <= 0 ? 'Indisponível' : 'Adicionar ao Carrinho' ?>
            </button>
        </form>
    </div>

</div>
