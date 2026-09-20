<?php
/**
 * Status badge, invoice number, and the four-step tracker. Shared by the invoice and the public status page.
 *
 * @var array $order needs: status, invoice_number, created_at
 */
$status     = (string) $order['status'];
$ui         = order_status_ui($status);
$stages     = order_status_stages();
$stageIndex = array_search($status, $stages, true); // false for gagal / dibatalkan: no tracker then
$fillClass  = ['w-0', 'w-1/3', 'w-2/3', 'w-full'][$stageIndex === false ? 0 : $stageIndex];
$createdAt  = ! empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : '';
?>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="order-status-title">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 id="order-status-title" class="text-sm font-semibold text-slate-600">Status Pesanan</h1>
            <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-bold ring-1 <?= $ui['class'] ?>">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true"><?= $ui['icon'] ?></span>
                <?= esc(order_status_label($status)) ?>
            </span>
        </div>
        <div class="sm:text-right">
            <span class="block text-sm font-semibold text-slate-600">No. Invoice</span>
            <span class="mt-1.5 block select-all font-mono text-base font-bold text-slate-950"><?= esc($order['invoice_number']) ?></span>
            <?php if ($createdAt !== ''): ?>
                <span class="block text-sm text-slate-600">Dibuat <?= esc($createdAt) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($stageIndex !== false): ?>
        <div class="relative mt-7">
            <div class="absolute left-[12.5%] right-[12.5%] top-[17px] h-0.5 bg-slate-200" aria-hidden="true">
                <div class="tracker-fill h-full origin-left bg-blue-600 <?= $fillClass ?>"></div>
            </div>
            <ol class="relative grid grid-cols-4 text-center" aria-label="Tahapan pesanan">
                <?php foreach ($stages as $i => $stage): ?>
                    <?php
                        $isCurrent = $i === $stageIndex;
                        $isDone    = $i < $stageIndex || ($isCurrent && $stage === 'selesai');
                        $nodeClass = $isDone
                            ? 'border-blue-600 bg-blue-600 text-white'
                            : ($isCurrent ? 'border-blue-600 bg-white text-blue-700' : 'border-slate-300 bg-white text-slate-600');
                    ?>
                    <li class="relative z-10 px-1"<?= $isCurrent ? ' aria-current="step"' : '' ?>>
                        <span class="<?= $isCurrent ? 'tracker-node-current ' : '' ?>mx-auto flex h-9 w-9 items-center justify-center rounded-full border-2 text-sm font-bold <?= $nodeClass ?>">
                            <?php if ($isDone): ?>
                                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">check</span>
                            <?php else: ?>
                                <span aria-hidden="true"><?= $i + 1 ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="mt-2 block text-xs font-semibold leading-tight sm:text-sm <?= $isCurrent ? 'text-slate-950' : 'text-slate-700' ?>">
                            <?= esc(order_status_label($stage)) ?>
                            <?php if ($isCurrent): ?><span class="sr-only">(tahap saat ini)</span><?php endif; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    <?php endif; ?>
</section>
