<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Sistem Perhitungan WP' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <style>
        .top-nav {
            background: #2196F3;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
            font-size: 20px;
            font-weight: bold;
        }
        .nav-links {
            display: flex;
            gap: 15px;
        }
        .nav-link {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.2);
        }
        .nav-link.active {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <?php if (!isset($hide_nav) || !$hide_nav): ?>
    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-brand">📊 Sistem Perhitungan WP</div>
            <div class="nav-links">
                <a href="<?= BASE_URL ?>?action=landing" class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'landing') || (!isset($_GET['action'])) ? 'active' : '' ?>">Beranda</a>
                <a href="<?= BASE_URL ?>?action=dashboard" class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'dashboard') ? 'active' : '' ?>">Dashboard</a>
                <a href="<?= BASE_URL ?>?action=index" class="nav-link <?= (isset($_GET['action']) && $_GET['action'] == 'index') ? 'active' : '' ?>">Kelola Analisis</a>
                <a href="<?= BASE_URL ?>?action=create" class="nav-link">+ Analisis Baru</a>
            </div>
        </div>
    </nav>
    <?php endif; ?>

