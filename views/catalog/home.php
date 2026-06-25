<!-- views/catalog/home.php -->

<div class="container mx-auto px-4 max-w-7xl py-8">
    <?php if (empty($search)): ?>
    <!-- Hero Section -->
    <section class="relative rounded-3xl overflow-hidden mb-12 border border-white/5 shadow-2xl group">
        <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-[#0a0a0a]/80 to-transparent z-10"></div>
        <!-- Replace with a dynamic top game image if available -->
        <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop" alt="Featured Game" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        
        <div class="relative z-20 p-8 md:p-16 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-500/20 text-brand-400 text-xs font-bold tracking-wider mb-4 border border-brand-500/30">
                <span class="w-2 h-2 rounded-full bg-brand-400 animate-pulse"></span>
                DESTAQUE DA SEMANA
            </span>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 leading-tight tracking-tight">
                Cyberpunk <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">2077</span>
            </h1>
            <p class="text-gray-300 text-lg md:text-xl mb-8 leading-relaxed">
                Bem-vindo a Night City, a megalópole obcecada por poder, glamour e modificações corporais.
            </p>
            <div class="flex items-center gap-4">
                <a href="#" class="px-8 py-3.5 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-xl transition-all shadow-[0_0_20px_rgba(59,130,246,0.4)] hover:shadow-[0_0_30px_rgba(59,130,246,0.6)] flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Comprar Agora
                </a>
                <div class="flex flex-col">
                    <span class="text-gray-400 text-sm line-through">R$ 199,90</span>
                    <span class="text-2xl font-bold text-white">R$ 99,90</span>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="space-y-12 pb-12">

        <!-- Section: Lançamentos / Mais Vendidos -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <?= !empty($search) ? 'Resultados para: <span class="text-brand-400">"' . htmlspecialchars($search) . '"</span>' : 'Mais Vendidos' ?>
                </h2>
                <?php if (empty($search)): ?>
                <a href="#" class="text-sm font-medium text-brand-400 hover:text-brand-300 transition-colors flex items-center gap-1">
                    Ver todos <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
                <?php else: ?>
                <a href="/" class="text-sm font-medium text-gray-400 hover:text-white transition-colors flex items-center gap-1">
                    Limpar Busca
                </a>
                <?php endif; ?>
            </div>

            <?php if (empty($products)): ?>
                <div class="bg-white/5 border border-white/10 p-8 text-center rounded-2xl">
                    <p class="text-gray-400">Nenhum produto disponível no momento.</p>
                </div>
            <?php else: ?>
                <div class="relative group">
                    <!-- Horizontally Scrollable Container (or Grid if searching) -->
                    <div class="flex <?= !empty($search) ? 'flex-wrap' : 'overflow-x-auto hide-scrollbar snap-x snap-mandatory' ?> gap-4 pb-4">
                        
                        <?php foreach ($products as $product): ?>
                            <?php 
                                // Calc discount
                                $discount = 0;
                                if (!empty($product['original_price']) && $product['original_price'] > $product['price']) {
                                    $discount = round((($product['original_price'] - $product['price']) / $product['original_price']) * 100);
                                }
                            ?>
                            <div class="flex-none w-[200px] sm:w-[240px] snap-start">
                                <div class="bg-[#121212] border border-white/5 rounded-2xl overflow-hidden hover:-translate-y-1.5 hover:shadow-[0_10px_20px_-3px_rgba(0,0,0,0.5),_0_0_15px_rgba(59,130,246,0.3)] hover:border-brand-500/40 transition-all duration-300 group/card h-full flex flex-col relative">
                                    
                                    <!-- Tags -->
                                    <div class="absolute top-2 left-2 right-2 flex justify-between items-start z-10 pointer-events-none">
                                        <?php if ($discount > 0): ?>
                                            <div class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-md shadow-lg shadow-red-500/20">
                                                -<?= $discount ?>%
                                            </div>
                                        <?php else: ?>
                                            <div></div>
                                        <?php endif; ?>
                                        
                                        <div class="bg-black/60 backdrop-blur-md border border-white/10 text-gray-300 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">
                                            <?= htmlspecialchars($product['platform'] ?? 'Steam') ?>
                                        </div>
                                    </div>

                                    <!-- Image -->
                                    <a href="/product?slug=<?= htmlspecialchars($product['slug']) ?>" class="block aspect-[3/4] bg-black overflow-hidden relative w-full">
                                        <?php if ($product['image_url']): ?>
                                            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover/card:scale-105">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-500">
                                                Sem Imagem
                                            </div>
                                        <?php endif; ?>
                                        <!-- Overlay hover gradient -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#121212] via-transparent to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity"></div>
                                    </a>

                                    <!-- Content -->
                                    <div class="p-4 flex flex-col flex-grow">
                                        <a href="/product?slug=<?= htmlspecialchars($product['slug']) ?>" class="mb-2">
                                            <h2 class="text-sm font-semibold text-white group-hover/card:text-brand-400 transition-colors line-clamp-2 leading-tight"><?= htmlspecialchars($product['title']) ?></h2>
                                        </a>
                                        
                                        <div class="mt-auto">
                                            <div class="flex items-end gap-2 mb-3">
                                                <span class="text-lg font-bold text-white">R$ <?= number_format($product['price'], 2, ',', '.') ?></span>
                                                <?php if ($discount > 0): ?>
                                                    <span class="text-xs text-gray-500 line-through mb-0.5">R$ <?= number_format($product['original_price'], 2, ',', '.') ?></span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <form action="/cart" method="POST" class="w-full">
                                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                                                <button type="submit" class="w-full bg-white/10 hover:bg-brand-500 text-white py-2 text-sm font-medium rounded-xl border border-white/5 hover:border-transparent transition-all flex items-center justify-center gap-2 group/btn">
                                                    <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                    Comprar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($search)): ?>
                        <!-- Spacer para evitar que o último card fique grudado na borda direita -->
                        <div class="flex-none w-4 sm:w-8"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
