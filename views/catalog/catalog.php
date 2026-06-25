<!-- views/catalog/catalog.php -->

<div class="container mx-auto px-4 max-w-7xl py-8">
    <!-- Main Content -->
    <div class="space-y-12 pb-12">
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Catálogo Completo
                </h2>
                <a href="/" class="text-sm font-medium text-gray-400 hover:text-white transition-colors flex items-center gap-1">
                    Voltar
                </a>
            </div>

            <?php if (empty($products)): ?>
                <div class="bg-white/5 border border-white/10 p-8 text-center rounded-2xl">
                    <p class="text-gray-400">Nenhum produto disponível no momento.</p>
                </div>
            <?php else: ?>
                <div class="relative group">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 pb-4">
                        
                        <?php foreach ($products as $product): ?>
                            <?php 
                                // Calc discount
                                $discount = 0;
                                if (!empty($product['original_price']) && $product['original_price'] > $product['price']) {
                                    $discount = round((($product['original_price'] - $product['price']) / $product['original_price']) * 100);
                                }
                            ?>
                            <div class="flex-none">
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
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
