<?php
$page_title = 'Sistem Perhitungan WP - Weighted Product';
// Hide navigation on landing page
$hide_nav = true;
require_once __DIR__ . '/layouts/header.php';
?>
<style>
.landing-container {
    min-height: calc(100vh - 200px);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.landing-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.landing-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
}

.landing-logo {
    font-size: 80px;
    margin-bottom: 20px;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.landing-title {
    font-size: 48px;
    font-weight: bold;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.landing-subtitle {
    font-size: 24px;
    margin-bottom: 30px;
    opacity: 0.95;
}

.landing-description {
    font-size: 18px;
    margin-bottom: 40px;
    opacity: 0.9;
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 60px;
}

.cta-primary {
    background: white;
    color: #667eea;
    padding: 18px 40px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    font-size: 18px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

.cta-secondary {
    background: transparent;
    color: white;
    padding: 18px 40px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    font-size: 18px;
    border: 2px solid white;
    transition: all 0.3s ease;
}

.cta-secondary:hover {
    background: white;
    color: #667eea;
    transform: translateY(-3px);
}

.features-section {
    background: white;
    color: #333;
    padding: 80px 20px;
    margin-top: 0;
}

.features-container {
    max-width: 1200px;
    margin: 0 auto;
}

.features-title {
    text-align: center;
    font-size: 36px;
    margin-bottom: 50px;
    color: #333;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.feature-card {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.feature-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.feature-title {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #667eea;
}

.feature-description {
    color: #666;
    line-height: 1.6;
}

.stats-section {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 60px 20px;
}

.stats-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    text-align: center;
}

.stat-item {
    padding: 20px;
}

.stat-number {
    font-size: 42px;
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 16px;
    opacity: 0.9;
}

@media (max-width: 768px) {
    .landing-title {
        font-size: 32px;
    }
    
    .landing-subtitle {
        font-size: 18px;
    }
    
    .landing-description {
        font-size: 16px;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-primary,
    .cta-secondary {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<div class="landing-container">
    <div class="landing-content">
        <div class="landing-logo">📊</div>
        <h1 class="landing-title">Sistem Perhitungan WP</h1>
        <p class="landing-subtitle">Weighted Product Method</p>
        <p class="landing-description">
            Sistem pendukung keputusan yang menggunakan metode Weighted Product (WP) 
            untuk membantu Anda dalam pengambilan keputusan yang lebih baik dan terukur.
        </p>
        <div class="cta-buttons">
            <a href="<?= BASE_URL ?>?action=dashboard" class="cta-primary">
                🚀 Mulai Sekarang
            </a>
            <a href="<?= BASE_URL ?>?action=index" class="cta-secondary">
                📋 Lihat Analisis
            </a>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="features-container">
        <h2 class="features-title">Fitur Utama</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3 class="feature-title">Multi-Kriteria</h3>
                <p class="feature-description">
                    Dukung pengambilan keputusan dengan berbagai kriteria yang dapat disesuaikan 
                    dengan bobot dan tipe (benefit/cost) sesuai kebutuhan Anda.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">Perhitungan Otomatis</h3>
                <p class="feature-description">
                    Sistem akan menghitung secara otomatis menggunakan metode Weighted Product 
                    dan memberikan ranking alternatif terbaik untuk keputusan Anda.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">💾</div>
                <h3 class="feature-title">Penyimpanan Data</h3>
                <p class="feature-description">
                    Semua analisis dan hasil perhitungan tersimpan dengan aman di database, 
                    sehingga Anda dapat melihat kembali kapan saja.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">✏️</div>
                <h3 class="feature-title">Edit & Update</h3>
                <p class="feature-description">
                    Mudah mengedit dan memperbarui data analisis yang sudah ada tanpa harus 
                    membuat ulang dari awal.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <h3 class="feature-title">Dashboard Interaktif</h3>
                <p class="feature-description">
                    Dashboard yang informatif menampilkan statistik dan ringkasan analisis 
                    untuk monitoring yang lebih baik.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3 class="feature-title">Aman & Terpercaya</h3>
                <p class="feature-description">
                    Data Anda aman dan terjamin dengan sistem yang handal dan terstruktur 
                    menggunakan arsitektur MVC.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="stats-container">
        <div class="stat-item">
            <div class="stat-number">100%</div>
            <div class="stat-label">Akurat</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">Mudah</div>
            <div class="stat-label">Digunakan</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">Cepat</div>
            <div class="stat-label">Hasil</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">Gratis</div>
            <div class="stat-label">Selamanya</div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

