<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">Gerenciar Cupons</h1>
        <a href="/admin/coupons/create" class="bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors shadow-lg flex items-center gap-2 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Novo Cupom
        </a>
    </div>

    <div class="glass-panel rounded-xl overflow-hidden shadow-2xl border border-white/10">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800/50 border-b border-white/10">
                        <th class="py-4 px-6 text-slate-300 font-semibold uppercase text-sm tracking-wider">Código</th>
                        <th class="py-4 px-6 text-slate-300 font-semibold uppercase text-sm tracking-wider">Tipo/Valor</th>
                        <th class="py-4 px-6 text-slate-300 font-semibold uppercase text-sm tracking-wider">Validade</th>
                        <th class="py-4 px-6 text-slate-300 font-semibold uppercase text-sm tracking-wider">Usos</th>
                        <th class="py-4 px-6 text-slate-300 font-semibold uppercase text-sm tracking-wider text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    <?php if(empty($coupons)): ?>
                        <tr>
                            <td colspan="5" class="py-8 px-6 text-center text-slate-400">Nenhum cupom cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($coupons as $coupon): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="py-4 px-6">
                                    <span class="inline-block bg-slate-800 px-3 py-1 rounded-md text-brand-400 font-bold tracking-wider text-sm border border-white/5">
                                        <?= htmlspecialchars($coupon['code']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    <?php if($coupon['discount_type'] === 'PERCENTAGE'): ?>
                                        <?= (float)$coupon['discount_value'] ?>%
                                    <?php else: ?>
                                        R$ <?= number_format($coupon['discount_value'], 2, ',', '.') ?>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-sm">
                                    <?= $coupon['valid_until'] ? date('d/m/Y H:i', strtotime($coupon['valid_until'])) : 'Sem limite' ?>
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-sm">
                                    <?= $coupon['used_count'] ?> / <?= $coupon['usage_limit'] ?: '∞' ?>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="/admin/coupons/edit?id=<?= $coupon['id'] ?>" class="text-blue-400 hover:text-blue-300 bg-blue-500/10 hover:bg-blue-500/20 px-3 py-1.5 rounded text-sm font-medium transition-colors">
                                            Editar
                                        </a>
                                        <form action="/admin/coupons/delete" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este cupom?');" class="inline">
                                            <input type="hidden" name="id" value="<?= $coupon['id'] ?>">
                                            <button type="submit" class="text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded text-sm font-medium transition-colors">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
