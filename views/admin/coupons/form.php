<?php 
$isEdit = isset($coupon) && $coupon !== null;
$title = $isEdit ? 'Editar Cupom' : 'Novo Cupom';
$action = $isEdit ? '/admin/coupons/update?id=' . $coupon['id'] : '/admin/coupons/store';

require_once __DIR__ . '/../../layouts/header.php'; 
?>

<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8 flex items-center justify-between border-b border-white/10 pb-6">
        <h1 class="text-3xl font-bold text-white"><?= $title ?></h1>
        <a href="/admin/coupons" class="text-slate-400 hover:text-white transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Voltar
        </a>
    </div>

    <div class="glass-panel p-8 rounded-2xl shadow-xl border border-white/10">
        <form action="<?= $action ?>" method="POST" class="space-y-6">
            
            <div>
                <label for="code" class="block text-sm font-medium text-slate-300 mb-2">Código do Cupom</label>
                <input type="text" id="code" name="code" value="<?= $isEdit ? htmlspecialchars($coupon['code']) : '' ?>" required 
                    class="w-full bg-slate-900/50 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all uppercase"
                    placeholder="EXEMPLO10">
                <p class="text-xs text-slate-500 mt-1">Sempre será salvo em maiúsculas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-slate-300 mb-2">Tipo de Desconto</label>
                    <select id="discount_type" name="discount_type" required 
                        class="w-full bg-slate-900/50 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all">
                        <option value="PERCENTAGE" <?= $isEdit && $coupon['discount_type'] === 'PERCENTAGE' ? 'selected' : '' ?>>Porcentagem (%)</option>
                        <option value="FIXED" <?= $isEdit && $coupon['discount_type'] === 'FIXED' ? 'selected' : '' ?>>Fixo (R$)</option>
                    </select>
                </div>

                <div>
                    <label for="discount_value" class="block text-sm font-medium text-slate-300 mb-2">Valor do Desconto</label>
                    <input type="number" step="0.01" min="0" id="discount_value" name="discount_value" value="<?= $isEdit ? $coupon['discount_value'] : '' ?>" required 
                        class="w-full bg-slate-900/50 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all"
                        placeholder="10.00">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="valid_until" class="block text-sm font-medium text-slate-300 mb-2">Data de Validade (Opcional)</label>
                    <input type="datetime-local" id="valid_until" name="valid_until" value="<?= $isEdit && $coupon['valid_until'] ? date('Y-m-d\TH:i', strtotime($coupon['valid_until'])) : '' ?>" 
                        class="w-full bg-slate-900/50 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all [color-scheme:dark]">
                </div>

                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-slate-300 mb-2">Limite de Usos (Opcional)</label>
                    <input type="number" min="1" id="usage_limit" name="usage_limit" value="<?= $isEdit ? $coupon['usage_limit'] : '' ?>" 
                        class="w-full bg-slate-900/50 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all"
                        placeholder="Deixe em branco para uso ilimitado">
                </div>
            </div>

            <div class="pt-6 border-t border-white/10 flex justify-end gap-4">
                <a href="/admin/coupons" class="px-6 py-3 rounded-lg text-slate-300 hover:text-white hover:bg-white/5 font-medium transition-colors border border-transparent hover:border-white/10">
                    Cancelar
                </a>
                <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white px-8 py-3 rounded-lg font-medium transition-all shadow-[0_0_15px_rgba(59,130,246,0.3)] hover:-translate-y-0.5">
                    Salvar Cupom
                </button>
            </div>
            
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
