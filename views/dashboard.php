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
    color: #333;
    margin-bottom: 10px;
}

.dashboard-header p {
    color: #666;
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
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.stat-card.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-card.success {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-card.info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
}

.stat-value {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
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
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.content-card h2 {
    color: #333;
    margin-bottom: 20px;
    font-size: 20px;
    border-bottom: 2px solid #2196F3;
    padding-bottom: 10px;
}

.analisis-list {
    list-style: none;
    padding: 0;
}

.analisis-item {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
    transition: background 0.3s ease;
}

.analisis-item:hover {
    background: #f5f5f5;
}

.analisis-item:last-child {
    border-bottom: none;
}

.analisis-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.analisis-meta {
    font-size: 12px;
    color: #666;
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
}

.badge-success {
    background: #4CAF50;
    color: white;
}

.badge-warning {
    background: #FF9800;
    color: white;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-btn {
    background: #2196F3;
    color: white;
    padding: 20px;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.action-btn:hover {
    background: #1976D2;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
}

.action-btn.secondary {
    background: #4CAF50;
}

.action-btn.secondary:hover {
    background: #45a049;
}

.action-btn.danger {
    background: #f44336;
}

.action-btn.danger:hover {
    background: #da190b;
}

.action-icon {
    font-size: 32px;
}

.action-label {
    font-weight: 600;
    font-size: 14px;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #666;
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
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
            
            <div style="margin-top: 30px; padding: 20px; background: #e3f2fd; border-radius: 8px;">
                <h3 style="margin-bottom: 10px; color: #1976D2;">💡 Tips</h3>
                <ul style="margin: 0; padding-left: 20px; color: #666; font-size: 14px;">
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

