<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($storeName) ?> - Pemeliharaan Sistem</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, ::after, ::before { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #0b0f17;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            max-width: 480px;
            background: #131b2e;
            border: 1px solid #1e293b;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            text-align: center;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            color: #fbbf24;
            letter-spacing: 0.02em;
            margin-bottom: 1.5rem;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #f59e0b;
            border-radius: 50%;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #f8fafc;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
        }
        p.subtitle {
            font-size: 0.9375rem;
            color: #94a3b8;
            margin-bottom: 1.75rem;
        }
        .reason-box {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: left;
            margin-bottom: 1.75rem;
        }
        .reason-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-bottom: 0.35rem;
        }
        .reason-text {
            font-size: 0.875rem;
            color: #cbd5e1;
            font-weight: 500;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .btn-primary {
            background: #2563eb;
            color: #ffffff;
            border: none;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
        }
        .btn-secondary:hover {
            background: #334155;
            color: #f8fafc;
        }
        .footer-note {
            margin-top: 1.75rem;
            font-size: 0.75rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-badge">
            <span class="status-dot"></span>
            Toko Sedang Tutup
        </div>

        <h1><?= esc($storeName) ?> Dalam Pemeliharaan</h1>
        <p class="subtitle">Kami sedang melakukan pemeliharaan sistem rutin untuk meningkatkan kualitas layanan dan keamanan transaksi Anda.</p>

        <?php if (! empty($reason)): ?>
            <div class="reason-box">
                <div class="reason-title">Catatan Pengelola</div>
                <div class="reason-text"><?= esc($reason) ?></div>
            </div>
        <?php endif; ?>

        <div class="actions">
            <button onclick="window.location.reload()" class="btn btn-primary">
                🔄 Coba Muat Ulang Halaman
            </button>
            <?php if (! empty($waUrl)): ?>
                <a href="<?= esc($waUrl) ?>" target="_blank" rel="noopener" class="btn btn-secondary">
                    💬 Hubungi Layanan Pelanggan (WhatsApp)
                </a>
            <?php endif; ?>
        </div>

        <div class="footer-note">
            Terima kasih atas kesabaran Anda. Layanan akan kembali normal sesegera mungkin.
        </div>
    </div>
</body>
</html>
