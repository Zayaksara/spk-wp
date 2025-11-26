<?php
$page_title = 'Simulasi Perhitungan WP - Belajar Weighted Product';
require_once __DIR__ . '/layouts/header.php';
?>
<style>
    .simulasi-container {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .simulasi-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .simulasi-header h1 {
        color: var(--primary);
        font-size: 36px;
        margin-bottom: 10px;
    }
    
    .simulasi-header p {
        color: var(--text-secondary);
        font-size: 16px;
    }
    
    .input-section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: var(--shadow);
    }
    
    .input-section h2 {
        color: var(--primary);
        margin-bottom: 20px;
        font-size: 24px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-primary);
        font-weight: 600;
    }
    
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid var(--secondary);
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        resize: vertical;
        transition: border-color 0.3s;
    }
    
    .form-group textarea:focus {
        outline: none;
        border-color: var(--accent);
    }
    
    .btn-group {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 20px;
    }
    
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }
    
    .btn-primary {
        background: var(--accent);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--accent-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(251, 122, 46, 0.3);
    }
    
    .btn-secondary {
        background: var(--secondary);
        color: white;
    }
    
    .btn-secondary:hover {
        background: var(--secondary-dark);
        transform: translateY(-2px);
    }
    
    .btn-outline {
        background: white;
        color: var(--primary);
        border: 2px solid var(--primary);
    }
    
    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }
    
    .result-section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: var(--shadow);
        display: none;
    }
    
    .result-section.active {
        display: block !important;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .result-section h2 {
        color: var(--primary);
        margin-bottom: 20px;
        font-size: 24px;
        border-bottom: 3px solid var(--accent);
        padding-bottom: 10px;
    }
    
    .step-indicator {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    
    .step-btn {
        padding: 10px 20px;
        background: var(--bg-gray);
        color: var(--text-primary);
        border: 2px solid var(--border-light);
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .step-btn:hover {
        background: var(--secondary);
        color: white;
        border-color: var(--secondary);
    }
    
    .step-btn.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }
    
    .table-container {
        overflow-x: auto;
        margin-bottom: 20px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    
    table th {
        background: var(--secondary);
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: 600;
    }
    
    table td {
        padding: 12px;
        border-bottom: 1px solid var(--border-light);
    }
    
    table tr:hover {
        background: var(--bg-gray);
    }
    
    .ranking-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    
    .ranking-1 {
        background: var(--accent);
        color: white;
    }
    
    .ranking-2, .ranking-3 {
        background: var(--secondary);
        color: white;
    }
    
    .ranking-other {
        background: var(--bg-gray);
        color: var(--text-primary);
    }
    
    .formula-box {
        background: var(--bg-gray);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid var(--accent);
    }
    
    .formula-box h4 {
        color: var(--primary);
        margin-bottom: 10px;
    }
    
    .chart-container {
        margin-top: 30px;
        padding: 20px;
        background: var(--bg-gray);
        border-radius: 8px;
    }
    
    .info-box {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .info-box h3 {
        margin-bottom: 10px;
    }
    
    .error-message {
        background: #f44336;
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
    }
    
    .error-message.show {
        display: block;
    }
    
    @media (max-width: 768px) {
        .simulasi-container {
            padding: 0 15px;
        }
        
        .input-section, .result-section {
            padding: 20px;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
        }
    }
</style>

<div class="simulasi-container">
    <div class="simulasi-header">
        <h1>📊 Simulasi Perhitungan WP</h1>
        <p>Belajar metode Weighted Product secara interaktif dengan contoh data</p>
    </div>
    
    <!-- Input Section -->
    <div class="input-section">
        <h2>📝 Input Data</h2>
        
        <div class="error-message" id="errorMessage"></div>
        
        <div class="form-group">
            <label for="kriteriaInput">Kriteria (Format: Nama|Bobot|Tipe)</label>
            <textarea id="kriteriaInput" rows="5" placeholder="Contoh:&#10;Harga|0.4|cost&#10;Kualitas|0.3|benefit&#10;Layanan|0.3|benefit"></textarea>
            <small style="color: var(--text-secondary);">Satu kriteria per baris. Format: Nama|Bobot|Tipe (benefit/cost)</small>
        </div>
        
        <div class="form-group">
            <label for="alternatifInput">Alternatif (Format: Nama|Nilai1|Nilai2|...)</label>
            <textarea id="alternatifInput" rows="5" placeholder="Contoh:&#10;A|10|80|70&#10;B|20|90|60&#10;C|15|85|65"></textarea>
            <small style="color: var(--text-secondary);">Satu alternatif per baris. Jumlah nilai harus sama dengan jumlah kriteria.</small>
        </div>
        
        <div class="btn-group">
            <button class="btn btn-primary" onclick="loadDummyData()">📦 Load Data Contoh</button>
            <button class="btn btn-secondary" onclick="hitungWP()">🧮 Hitung WP</button>
            <button class="btn btn-outline" onclick="clearData()">🗑️ Bersihkan</button>
        </div>
    </div>
    
    <!-- Step Navigation -->
    <div class="step-indicator" id="stepIndicator" style="display: none;">
        <button class="step-btn" onclick="showStep(1)">1. Data Awal</button>
        <button class="step-btn" onclick="showStep(2)">2. Normalisasi</button>
        <button class="step-btn" onclick="showStep(3)">3. Perhitungan WP</button>
        <button class="step-btn" onclick="showStep(4)">4. Ranking</button>
    </div>
    
    <!-- Step 1: Data Awal -->
    <div class="result-section" id="step1" style="display: none;">
        <h2>📋 Step 1: Data Awal</h2>
        <div class="info-box">
            <h3>Informasi Kriteria</h3>
            <div id="kriteriaInfo"></div>
        </div>
        <div class="table-container">
            <h3 style="color: var(--primary); margin-bottom: 15px;">Matriks Nilai Awal</h3>
            <div id="matrixTable"></div>
        </div>
    </div>
    
    <!-- Step 2: Normalisasi -->
    <div class="result-section" id="step2" style="display: none;">
        <h2>🔄 Step 2: Normalisasi Nilai</h2>
        <div class="formula-box">
            <h4>Rumus Normalisasi:</h4>
            <div id="normalisasiFormula"></div>
        </div>
        <div class="table-container">
            <h3 style="color: var(--primary); margin-bottom: 15px;">Matriks Normalisasi</h3>
            <div id="normalizedTable"></div>
        </div>
    </div>
    
    <!-- Step 3: Perhitungan WP -->
    <div class="result-section" id="step3" style="display: none;">
        <h2>🧮 Step 3: Perhitungan Weighted Product</h2>
        <div class="formula-box">
            <h4>Rumus WP:</h4>
            <div id="wpFormula"></div>
        </div>
        <div class="table-container">
            <h3 style="color: var(--primary); margin-bottom: 15px;">Detail Perhitungan WP</h3>
            <div id="wpCalculationTable"></div>
        </div>
    </div>
    
    <!-- Step 4: Ranking -->
    <div class="result-section" id="step4" style="display: none;">
        <h2>🏆 Step 4: Ranking Hasil</h2>
        <div class="table-container">
            <h3 style="color: var(--primary); margin-bottom: 15px;">Tabel Ranking</h3>
            <div id="rankingTable"></div>
        </div>
        <div class="chart-container">
            <h3 style="color: var(--primary); margin-bottom: 15px;">Grafik Nilai WP</h3>
            <canvas id="wpChart"></canvas>
        </div>
        <div class="info-box" id="conclusionBox" style="margin-top: 20px;">
            <h3>🎯 Kesimpulan</h3>
            <div id="conclusion"></div>
        </div>
    </div>
</div>

<!-- MathJax for formula rendering -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<!-- Chart.js for bar chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Simulasi JS -->
<script src="<?= BASE_URL ?>assets/js/simulasi.js"></script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

