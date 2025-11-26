<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Sistem Perhitungan WP' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <style>
        .top-nav {
            background: var(--primary);
            color: white;
            padding: 18px 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-brand {
            font-size: 22px;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-links {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .nav-link {
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-1px);
        }
        .nav-link.active {
            background: var(--accent);
            color: white;
            font-weight: 600;
        }
        .nav-link.active:hover {
            background: var(--accent-dark);
        }
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 15px;
            }
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            .nav-link {
                padding: 8px 12px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <?php if (!isset($hide_nav) || !$hide_nav): ?>
    <nav class="top-nav">
        <div class="nav-container">
            <a href="<?= BASE_URL ?>?action=dashboard" class="nav-brand">📊 Sistem Perhitungan WP</a>
            <div class="nav-links">
                <a href="<?= BASE_URL ?>?action=landing" class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'landing') || (!isset($_GET['action'])) ? 'active' : '' ?>">Beranda</a>
                <a href="<?= BASE_URL ?>?action=dashboard" class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'dashboard') ? 'active' : '' ?>">Dashboard</a>
                <a href="<?= BASE_URL ?>?action=index" class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'index') ? 'active' : '' ?>">Kelola Analisis</a>
                <a href="<?= BASE_URL ?>?action=create" class="nav-link" style="background: var(--accent);">+ Analisis Baru</a>
            </div>
        </div>
    </nav>
    <?php endif; ?>

