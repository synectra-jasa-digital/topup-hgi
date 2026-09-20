<?php
/**
 * Friendly full-page message for visitors (404, 429). Standalone on purpose: it reads no settings
 * and touches no database, so it still renders when something else is wrong.
 *
 * @var string $code    HTTP status shown as a small label
 * @var string $heading
 * @var string $message
 * @var list<array{label: string, href: string, primary?: bool}> $actions
 */
$url = static fn (string $path = ''): string => function_exists('base_url') ? base_url($path) : '/' . $path;
$ring = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($heading) ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap">
    <link rel="stylesheet" href="<?= esc($url('assets/css/public.css')) ?>">
</head>
<body class="light-felt-pattern flex min-h-screen items-center justify-center px-4 py-10 font-sans text-slate-800 antialiased">
    <main class="w-full max-w-md text-center">
        <div class="rise-stagger space-y-6">
            <div class="space-y-3">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                    <span class="material-symbols-outlined text-[30px]" aria-hidden="true"><?= esc($icon ?? 'search_off') ?></span>
                </span>
                <?php if (! empty($code)): ?>
                    <p class="font-mono text-sm font-semibold text-slate-600"><?= esc($code) ?></p>
                <?php endif; ?>
                <h1 class="font-display text-3xl font-extrabold tracking-tight text-slate-950"><?= esc($heading) ?></h1>
                <p class="mx-auto max-w-sm text-base leading-relaxed text-slate-600"><?= esc($message) ?></p>
            </div>

            <div class="flex flex-col justify-center gap-3 sm:flex-row">
                <?php foreach ($actions as $action): ?>
                    <a class="inline-flex min-h-12 items-center justify-center rounded-xl px-6 py-3 font-display text-sm font-bold transition-all active:scale-[0.98] <?= ! empty($action['primary']) ? 'border border-blue-700 bg-blue-600 text-white shadow-sm hover:bg-blue-700' : 'border border-slate-300 bg-white text-slate-800 hover:bg-slate-50' ?> <?= $ring ?>" href="<?= esc($action['href']) ?>"><?= esc($action['label']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>
