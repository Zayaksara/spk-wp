<?php
$page_title = 'Dashboard - Sistem Perhitungan WP';
require_once __DIR__ . '/layouts/header.php';
?>
<style>
.dashboard-container {
    max-width: 1400px;
    margin: 40px auto;
    padding: 0 20px;
}

.dashboard-header {
    margin-bottom: 30px;
}

.dashboard-header h1 {
    color: var(--text-primary);
    margin-bottom: 10px;
}

.dashboard-header p {
    color: var(--text-secondary);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: var(--shadow);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.stat-card:hover::before {
    left: 100%;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 8px 25px rgba(35, 47, 93, 0.25);
}

.stat-card.primary:hover {
    box-shadow: 0 8px 25px rgba(35, 47, 93, 0.4);
}

.stat-card.success:hover {
    box-shadow: 0 8px 25px rgba(240, 147, 251, 0.4);
}

.stat-card.info:hover {
    box-shadow: 0 8px 25px rgba(66, 121, 180, 0.4);
}

.stat-card.warning:hover {
    box-shadow: 0 8px 25px rgba(67, 233, 123, 0.4);
}

.stat-card.primary {
    background: linear-gradient(135deg, #232F5D 0%, #1A2447 100%);
    color: white;
}

.stat-card.success {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-card.info {
    background: linear-gradient(135deg, #4279B4 0%, #3568A0 100%);
    color: white;
}

.stat-card.warning {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.stat-icon {
    font-size: 40px;
    margin-bottom: 15px;
    opacity: 0.9;
    transition: all 0.3s ease;
    display: inline-block;
}

.stat-card:hover .stat-icon {
    transform: scale(1.2) rotate(5deg);
    opacity: 1;
}

.stat-value {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

.stat-card:hover .stat-value {
    transform: scale(1.1);
    color: rgba(255, 255, 255, 1);
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
    transition: opacity 0.3s ease;
}

.stat-card:hover .stat-label {
    opacity: 1;
}

.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 40px;
}

@media (max-width: 968px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

.content-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(35, 47, 93, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
}

.content-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(35, 47, 93, 0.15);
    border-color: rgba(35, 47, 93, 0.1);
}

.content-card h2 {
    color: #232F5D;
    margin-bottom: 20px;
    font-size: 20px;
    border-bottom: 2px solid #232F5D;
    padding-bottom: 10px;
    transition: all 0.3s ease;
    position: relative;
}

.content-card:hover h2 {
    color: var(--accent);
    border-bottom-color: var(--accent);
}

.content-card h2::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--accent);
    transition: width 0.3s ease;
}

.content-card:hover h2::after {
    width: 100%;
}

.analisis-list {
    list-style: none;
    padding: 0;
}

.analisis-item {
    padding: 15px;
    border-bottom: 1px solid #E9ECEF;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-left: 3px solid transparent;
    border-radius: 4px;
    margin-bottom: 5px;
}

.analisis-item:hover {
    background: linear-gradient(90deg, #F0F4F8 0%, #F8F9FA 100%);
    border-left-color: var(--accent);
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(35, 47, 93, 0.1);
    padding-left: 20px;
}

.analisis-item:hover .analisis-actions {
    opacity: 1;
}

.analisis-actions .btn-view,
.analisis-actions .btn-edit {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.analisis-actions .btn-view:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 4px 12px rgba(251, 122, 46, 0.4);
}

.analisis-actions .btn-edit:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 4px 12px rgba(66, 121, 180, 0.4);
}

.analisis-item:last-child {
    border-bottom: none;
}

.analisis-title {
    font-weight: 600;
    color: #232F5D;
    margin-bottom: 5px;
    transition: color 0.3s ease;
}

.analisis-item:hover .analisis-title {
    color: var(--accent);
}

.analisis-meta {
    font-size: 12px;
    color: #6876DF;
    display: flex;
    gap: 15px;
    margin-top: 8px;
}

.analisis-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 10px;
    transition: all 0.3s ease;
    cursor: default;
}

.badge-success {
    background: #4279B4;
    color: white;
}

.badge-success:hover {
    background: #3568A0;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(66, 121, 180, 0.3);
}

.badge-warning {
    background: #FB7A2E;
    color: white;
}

.badge-warning:hover {
    background: #E6681F;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(251, 122, 46, 0.3);
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-btn {
    background: #FB7A2E;
    color: white;
    padding: 20px;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.action-btn:hover::before {
    width: 300px;
    height: 300px;
}

.action-btn:hover {
    background: #E6681F;
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 6px 20px rgba(251, 122, 46, 0.4);
}

.action-icon {
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.action-btn:hover .action-icon {
    transform: scale(1.2) rotate(10deg);
}

.action-label {
    position: relative;
    z-index: 1;
}

.action-btn.secondary {
    background: #4279B4;
}

.action-btn.secondary:hover {
    background: #3568A0;
    box-shadow: 0 6px 20px rgba(66, 121, 180, 0.4);
}

.action-btn.danger {
    background: #f44336;
}

.action-btn.danger:hover {
    background: #da190b;
}


.empty-state {
    text-align: center;
    padding: 40px;
    color: #6876DF;
    transition: all 0.3s ease;
}

.empty-state:hover {
    transform: scale(1.05);
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
    transition: all 0.3s ease;
    display: inline-block;
}

.empty-state:hover .empty-state-icon {
    opacity: 0.8;
    transform: rotate(10deg) scale(1.1);
}

.empty-state p {
    transition: color 0.3s ease;
}

.empty-state:hover p {
    color: var(--accent);
}
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <p>Selamat datang di Sistem Perhitungan WP - Weighted Product</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon">📊</div>
            <div class="stat-value"><?= $stats['total_analisis'] ?></div>
            <div class="stat-label">Total Analisis</div>
        </div>
        
        <div class="stat-card success">
            <div class="stat-icon">📅</div>
            <div class="stat-value"><?= $stats['analisis_bulan_ini'] ?></div>
            <div class="stat-label">Analisis Bulan Ini</div>
        </div>
        
        <div class="stat-card info">
            <div class="stat-icon">🎯</div>
            <div class="stat-value"><?= $stats['total_alternatif'] ?></div>
            <div class="stat-label">Total Alternatif</div>
        </div>
        
        <div class="stat-card warning">
            <div class="stat-icon">📋</div>
            <div class="stat-value"><?= $stats['total_kriteria'] ?></div>
            <div class="stat-label">Total Kriteria</div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Recent Analisis -->
        <div class="content-card">
            <h2>Analisis Terbaru</h2>
            <?php if (count($stats['analisis_terbaru']) > 0): ?>
            <ul class="analisis-list">
                <?php foreach ($stats['analisis_terbaru'] as $analisis): ?>
                <li class="analisis-item">
                    <div class="analisis-title">
                        <?= htmlspecialchars($analisis['judul']) ?>
                        <?php if ($analisis['has_results']): ?>
                            <span class="badge badge-success">Selesai</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Belum Dihitung</span>
                        <?php endif; ?>
                    </div>
                    <div class="analisis-meta">
                        <span>📅 <?= date('d/m/Y H:i', strtotime($analisis['created_at'])) ?></span>
                        <span>🎯 <?= $analisis['alternatif_count'] ?> Alternatif</span>
                        <span>📋 <?= $analisis['kriteria_count'] ?> Kriteria</span>
                    </div>
                    <div style="margin-top: 10px;">
                        <a href="<?= BASE_URL ?>?action=view&id=<?= $analisis['id'] ?>" class="btn-view" style="margin-right: 5px;">Lihat</a>
                        <a href="<?= BASE_URL ?>?action=edit&id=<?= $analisis['id'] ?>" class="btn-edit" style="margin-right: 5px;">Edit</a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📊</div>
                <p>Belum ada analisis. Buat analisis baru untuk memulai!</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="content-card">
            <h2>Quick Actions</h2>
            <div class="quick-actions">
                <a href="<?= BASE_URL ?>?action=create" class="action-btn">
                    <div class="action-icon">➕</div>
                    <div class="action-label">Analisis Baru</div>
                </a>
                
                <a href="<?= BASE_URL ?>?action=index" class="action-btn secondary">
                    <div class="action-icon">📋</div>
                    <div class="action-label">Kelola Analisis</div>
                </a>
                
                <a href="<?= BASE_URL ?>?action=index" class="action-btn">
                    <div class="action-icon">📊</div>
                    <div class="action-label">Lihat Semua</div>
                </a>
            </div>
            
            <div style="margin-top: 30px; padding: 20px; background: #F0F4F8; border-radius: 8px;">
                <h3 style="margin-bottom: 10px; color: #232F5D;">💡 Tips</h3>
                <ul style="margin: 0; padding-left: 20px; color: #6876DF; font-size: 14px;">
                    <li>Pastikan total bobot kriteria = 1</li>
                    <li>Minimal diperlukan 2 alternatif</li>
                    <li>Isi semua nilai sebelum menghitung</li>
                    <li>Gunakan koma (,) atau titik (.) untuk desimal</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

