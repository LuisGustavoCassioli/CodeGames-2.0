<!-- views/account/orders.php -->
<div class="mb-8">
    <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Meus Pedidos</h1>
    <p class="text-lg text-slate-400">Acompanhe o histórico de suas compras.</p>
</div>

<?php if (empty($orders)): ?>
    <div class="glass-panel p-12 text-center rounded-2xl">
        <p class="text-slate-400 mb-6 text-lg">Você ainda não possui nenhum pedido.</p>
        <a href="/" class="inline-block bg-brand-500 hover:bg-brand-600 text-white font-medium py-3 px-8 rounded-md transition-all shadow-[0_0_15px_rgba(59,130,246,0.3)] hover:-translate-y-0.5">Explorar Catálogo</a>
    </div>
<?php else: ?>
    <div class="space-y-6">
        <?php foreach ($orders as $order): ?>
            <div class="glass-panel rounded-2xl overflow-hidden shadow-md border border-white/5">
                
                <!-- Cabeçalho do Pedido -->
                <div class="bg-surface-800/80 p-6 border-b border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-400 mb-1">Pedido <span class="font-mono text-white">#<?= htmlspecialchars($order['id']) ?></span></p>
                        <p class="text-sm text-slate-400">Realizado em <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                    </div>
                    
                    <div class="flex flex-col md:items-end gap-2">
                        <span class="text-xl font-bold text-brand-500">R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></span>
                        
                        <?php 
                        $statusColors = [
                            'PENDING' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                            'PAID' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                            'COMPLETED' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                            'CANCELLED' => 'bg-red-500/10 text-red-400 border-red-500/20'
                        ];
                        $statusLabels = [
                            'PENDING' => 'Pendente',
                            'PAID' => 'Pago',
                            'COMPLETED' => 'Concluído',
                            'CANCELLED' => 'Cancelado'
                        ];
                        
                        $statusClass = $statusColors[$order['status']] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                        $statusLabel = $statusLabels[$order['status']] ?? $order['status'];
                        ?>
                        
                        <span class="px-3 py-1 rounded-full text-xs font-semibold border uppercase tracking-wider <?= $statusClass ?>">
                            <?= $statusLabel ?>
                        </span>

                        <?php if ($order['status'] === 'PENDING' && $order['payment_method'] === 'PIX'): ?>
                            <a href="/checkout/pix?id=<?= $order['id'] ?>" class="mt-2 text-center bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-4 rounded transition-colors">
                                Pagar PIX
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Itens do Pedido -->
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-slate-300 mb-4 uppercase tracking-wider">Itens</h3>
                    <ul class="space-y-4">
                        <?php foreach ($order['items'] as $item): ?>
                            <li class="flex justify-between items-start bg-black/20 p-4 rounded-lg">
                                <div class="flex-1">
                                    <p class="text-white font-medium line-clamp-1"><?= htmlspecialchars($item['title']) ?></p>
                                    <p class="text-sm text-slate-400 mt-1">Qtd: <?= $item['quantity'] ?> &times; R$ <?= number_format($item['price_at_time'] ?? 0, 2, ',', '.') ?></p>
                                    
                                    <?php if ($order['status'] === 'COMPLETED' && !empty($item['game_key'])): ?>
                                        <div class="mt-3 p-3 bg-surface-900 border border-brand-500/30 rounded-md inline-block">
                                            <p class="text-xs text-brand-400 mb-1 font-semibold uppercase">Chave de Ativação:</p>
                                            <p class="font-mono text-white tracking-widest text-sm"><?= htmlspecialchars($item['game_key']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span class="text-slate-200 font-medium ml-4 mt-1">
                                    R$ <?= number_format(($item['price_at_time'] ?? 0) * $item['quantity'], 2, ',', '.') ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
