<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<?php
$isEdit = isset($product) && $product !== null;
$actionUrl = $isEdit ? "/admin/products/update?id=" . urlencode($product['id']) : "/admin/products/store";
$title = $isEdit ? "Editar Produto" : "Novo Produto";
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="/admin/products" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-3xl font-bold text-white tracking-tight"><?= $title ?></h1>
        </div>

        <form action="<?= $actionUrl ?>" method="POST" class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 md:p-8 space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Nome do Jogo</label>
                <input type="text" id="title" name="title" required value="<?= htmlspecialchars($product['title'] ?? '') ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-gray-300 mb-2">Slug (URL)</label>
                <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($product['slug'] ?? '') ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                <p class="text-xs text-gray-500 mt-1">Deixe em branco para gerar automaticamente baseado no nome.</p>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Descrição</label>
                <textarea id="description" name="description" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Preço (R$)</label>
                    <input type="number" id="price" name="price" step="0.01" required value="<?= htmlspecialchars($product['price'] ?? '') ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label for="original_price" class="block text-sm font-medium text-gray-300 mb-2">Preço Original (R$ - Opcional)</label>
                    <input type="number" id="original_price" name="original_price" step="0.01" value="<?= htmlspecialchars($product['original_price'] ?? '') ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-300 mb-2">Estoque</label>
                    <input type="number" id="stock" name="stock" required value="<?= htmlspecialchars($product['stock'] ?? 0) ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="image_url" class="block text-sm font-medium text-gray-300 mb-2">URL da Imagem</label>
                    <input type="url" id="image_url" name="image_url" value="<?= htmlspecialchars($product['image_url'] ?? '') ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label for="platform" class="block text-sm font-medium text-gray-300 mb-2">Plataforma</label>
                    <select id="platform" name="platform" class="w-full bg-[#0a0a0a] border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        <option value="Steam" <?= ($product['platform'] ?? '') === 'Steam' ? 'selected' : '' ?>>Steam</option>
                        <option value="Epic Games" <?= ($product['platform'] ?? '') === 'Epic Games' ? 'selected' : '' ?>>Epic Games</option>
                        <option value="Ubisoft" <?= ($product['platform'] ?? '') === 'Ubisoft' ? 'selected' : '' ?>>Ubisoft</option>
                        <option value="Origin" <?= ($product['platform'] ?? '') === 'Origin' ? 'selected' : '' ?>>Origin</option>
                        <option value="GOG" <?= ($product['platform'] ?? '') === 'GOG' ? 'selected' : '' ?>>GOG</option>
                        <option value="Xbox" <?= ($product['platform'] ?? '') === 'Xbox' ? 'selected' : '' ?>>Xbox</option>
                        <option value="PlayStation" <?= ($product['platform'] ?? '') === 'PlayStation' ? 'selected' : '' ?>>PlayStation</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-4 border-t border-white/10">
                <a href="/admin/products" class="px-6 py-3 rounded-xl font-medium text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                    Cancelar
                </a>
                <button type="submit" class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white font-medium py-3 px-8 rounded-xl transition-all shadow-lg hover:shadow-purple-500/30">
                    Salvar Produto
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
