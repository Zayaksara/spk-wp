<?php
$page_title = 'Lihat Analisis - ' . htmlspecialchars($analisis['judul']);
require_once __DIR__ . '/layouts/header.php';
?>
<div class="view-container">
    <div class="view-header">
        <div style="margin-bottom: 15px;">
            <a href="<?= BASE_URL ?>?action=dashboard" class="btn-back" style="margin-right: 10px;">📊 Dashboard</a>
            <a href="<?= BASE_URL ?>?action=index" class="btn-back">← Kembali</a>
        </div>
        <h1><?= htmlspecialchars($analisis['judul']) ?></h1>
        <p>Metode: <?= htmlspecialchars($analisis['metode']) ?></p>
    </div>

    <!-- Alternatif -->
    <div class="info-section">
        <h3>Alternatif</h3>
        <ul>
            <?php foreach ($alternatif as $alt): ?>
            <li><?= htmlspecialchars($alt['nama']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Kriteria -->
    <div class="info-section">
        <h3>Kriteria</h3>
        <table class="values-table">
            <thead>
                <tr>
                    <th>Nama Kriteria</th>
                    <th>Bobot</th>
                    <th>Tipe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kriteria as $kri): ?>
                <tr>
                    <td><?= htmlspecialchars($kri['nama']) ?></td>
                    <td><?= number_format($kri['bobot'], 2) ?></td>
                    <td><?= ucfirst($kri['tipe']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Nilai -->
    <div class="info-section">
        <h3>Nilai Alternatif</h3>
        <table class="values-table">
            <thead>
                <tr>
                    <th>Alternatif</th>
                    <?php foreach ($kriteria as $kri): ?>
                    <th><?= htmlspecialchars($kri['nama']) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alternatif as $alt): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($alt['nama']) ?></strong></td>
                    <?php foreach ($kriteria as $kri): ?>
                    <td>
                        <?php 
                        $nilaiValue = $nilai[$alt['id']][$kri['id']]['nilai'] ?? '-';
                        echo is_numeric($nilaiValue) ? number_format($nilaiValue, 2) : $nilaiValue;
                        ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Hasil -->
    <?php if (count($hasil) > 0): ?>
    <div class="info-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Hasil Perhitungan WP</h3>
            <button onclick="toggleVisualization()" class="btn-visualize" style="background: var(--accent); color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                📊 Lihat Visualisasi Perhitungan
            </button>
        </div>
        <table class="results-table">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Alternatif</th>
                    <th>Nilai WP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hasil as $h): ?>
                <tr>
                    <td><span class="ranking-badge"><?= $h['ranking'] ?></span></td>
                    <td><strong><?= htmlspecialchars($h['alternatif_nama']) ?></strong></td>
                    <td><strong><?= number_format($h['nilai_wp'], 4) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="margin-top: 20px; padding: 15px; background: #F0F4F8; border-radius: 6px; border-left: 4px solid var(--accent);">
            <strong>Kesimpulan:</strong> Alternatif terbaik adalah <strong><?= htmlspecialchars($hasil[0]['alternatif_nama']) ?></strong> 
            dengan nilai WP <?= number_format($hasil[0]['nilai_wp'], 4) ?>
        </div>
    </div>
    
    <!-- Visualisasi Perhitungan -->
    <?php if ($calculationDetails): ?>
    <div id="visualization-section" class="info-section" style="display: none; margin-top: 30px;">
        <h3 style="color: var(--primary); margin-bottom: 20px;">📊 Visualisasi Perhitungan WP</h3>
        
        <!-- Step Navigation -->
        <div class="step-nav" style="display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;">
            <button class="step-btn active" onclick="showResultStep(1)">1. Data Awal</button>
            <button class="step-btn" onclick="showResultStep(2)">2. Normalisasi</button>
            <button class="step-btn" onclick="showResultStep(3)">3. Perhitungan WP</button>
            <button class="step-btn" onclick="showResultStep(4)">4. Ranking</button>
        </div>
        
        <!-- Step 1: Data Awal -->
        <div id="result-step-1" class="calc-step" style="display: block;">
            <h4>📋 Step 1: Data Awal</h4>
            <p style="color: var(--text-secondary); margin-bottom: 25px; line-height: 1.6;">
                Menampilkan data awal yang digunakan dalam perhitungan: alternatif, kriteria dengan bobot, dan nilai setiap alternatif.
            </p>
            
            <div class="value-card">
                <h5>📊 Kriteria & Bobot</h5>
                <table class="values-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Bobot</th>
                            <th>Tipe</th>
                            <th>Max/Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($calculationDetails['kriteria'] as $kri): ?>
                        <tr style="transition: all 0.3s;">
                            <td><strong style="font-size: 15px;"><?= htmlspecialchars($kri['nama']) ?></strong></td>
                            <td><strong style="color: var(--primary);"><?= number_format($kri['bobot'], 4) ?></strong></td>
                            <td>
                                <span style="padding: 6px 12px; border-radius: 6px; background: <?= $kri['tipe'] == 'benefit' ? 'linear-gradient(135deg, #4279B4 0%, #3568A0 100%)' : 'linear-gradient(135deg, #FB7A2E 0%, #E6681F 100%)' ?>; color: white; font-size: 12px; font-weight: 600; box-shadow: 0 2px 6px rgba(<?= $kri['tipe'] == 'benefit' ? '66, 121, 180' : '251, 122, 46' ?>, 0.3);">
                                    <?= ucfirst($kri['tipe']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($kri['tipe'] == 'benefit'): ?>
                                    <span style="color: var(--secondary); font-weight: 600;">Max: <?= number_format($calculationDetails['maxMin'][$kri['id']]['max'], 2) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--accent); font-weight: 600;">Min: <?= number_format($calculationDetails['maxMin'][$kri['id']]['min'], 2) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="value-card">
                <h5>📈 Nilai Alternatif</h5>
                <table class="values-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Alternatif</th>
                            <?php foreach ($calculationDetails['kriteria'] as $kri): ?>
                            <th><?= htmlspecialchars($kri['nama']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($calculationDetails['alternatif'] as $alt): ?>
                        <tr style="transition: all 0.3s;">
                            <td><strong style="font-size: 15px; color: var(--primary);"><?= htmlspecialchars($alt['nama']) ?></strong></td>
                            <?php foreach ($calculationDetails['kriteria'] as $kri): ?>
                            <td style="font-weight: 500; font-size: 14px;"><?= number_format($calculationDetails['nilai'][$alt['id']][$kri['id']] ?? 0, 2) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            </div>
            
        <!-- Step 2: Normalisasi -->
        <div id="result-step-2" class="calc-step" style="display: none;">
            <h4>🔄 Step 2: Normalisasi Nilai</h4>
            <p style="color: var(--text-secondary); margin-bottom: 25px; line-height: 1.6;">
                Normalisasi dilakukan untuk menyamakan skala nilai. Untuk kriteria <strong style="color: var(--secondary);">benefit</strong>: nilai dibagi dengan nilai maksimum. 
                Untuk kriteria <strong style="color: var(--accent);">cost</strong>: nilai minimum dibagi dengan nilai.
            </p>
            
            <div class="formula-highlight">
                <strong>Rumus Normalisasi:</strong><br>
                <?php foreach ($calculationDetails['kriteria'] as $kri): ?>
                    <?php if ($kri['tipe'] == 'benefit'): ?>
                        <strong><?= htmlspecialchars($kri['nama']) ?> (benefit):</strong> Normalisasi = Nilai ÷ <?= number_format($calculationDetails['maxMin'][$kri['id']]['max'], 2) ?><br>
                    <?php else: ?>
                        <strong><?= htmlspecialchars($kri['nama']) ?> (cost):</strong> Normalisasi = <?= number_format($calculationDetails['maxMin'][$kri['id']]['min'], 2) ?> ÷ Nilai<br>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <div class="value-card">
                    <table class="values-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Alternatif</th>
                                <?php foreach ($calculationDetails['kriteria'] as $kri): ?>
                                <th>
                                    <?= htmlspecialchars($kri['nama']) ?><br>
                                    <small style="font-weight: normal; opacity: 0.7;">
                                        <?php if ($kri['tipe'] == 'benefit'): ?>
                                            (÷ <?= number_format($calculationDetails['maxMin'][$kri['id']]['max'], 2) ?>)
                                        <?php else: ?>
                                            (<?= number_format($calculationDetails['maxMin'][$kri['id']]['min'], 2) ?> ÷ nilai)
                                        <?php endif; ?>
                                    </small>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($calculationDetails['alternatif'] as $alt): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($alt['nama']) ?></strong></td>
                                <?php foreach ($calculationDetails['kriteria'] as $kri): ?>
                                <td>
                                    <?php 
                                    $nilaiAwal = $calculationDetails['nilai'][$alt['id']][$kri['id']] ?? 0;
                                    $normalized = $calculationDetails['normalized'][$alt['id']][$kri['id']] ?? 0;
                                    ?>
                                    <div style="text-align: center;">
                                        <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 5px;">
                                            Nilai: <?= number_format($nilaiAwal, 2) ?>
                                        </div>
                                        <div style="font-size: 20px; font-weight: bold; color: var(--accent); padding: 8px; background: rgba(251, 122, 46, 0.1); border-radius: 6px;">
                                            <?= number_format($normalized, 4) ?>
                                        </div>
                                    </div>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        <!-- Step 3: Perhitungan WP -->
        <div id="result-step-3" class="calc-step" style="display: none;">
            <h4>🧮 Step 3: Perhitungan Weighted Product (WP)</h4>
            <p style="color: var(--text-secondary); margin-bottom: 25px; line-height: 1.6;">
                Rumus WP: <strong style="color: var(--accent);">WP = ∏(Nilai Normalisasi^Bobot)</strong> untuk setiap kriteria, dimana ∏ adalah perkalian semua kriteria.
            </p>
            
            <?php foreach ($calculationDetails['wpDetails'] as $detail): ?>
            <div class="wp-detail-card">
                <h5>
                    🎯 Alternatif: <strong><?= htmlspecialchars($detail['alternatif_nama']) ?></strong>
                </h5>
                
                <div class="calculation-box">
                    <code>
                        <?php 
                        $formulaParts = [];
                        foreach ($detail['steps'] as $step) {
                            $formulaParts[] = "(" . number_format($step['normalized'], 4) . "^" . number_format($step['bobot'], 4) . ")";
                        }
                        ?>
                        <div style="margin-bottom: 12px; color: var(--text-primary);">
                            <strong style="color: var(--primary);">WP =</strong> <?= implode(' × ', $formulaParts) ?>
                        </div>
                        <div style="margin-bottom: 12px; color: var(--text-secondary);">
                            <?php 
                            $calcParts = [];
                            foreach ($detail['steps'] as $step) {
                                $calcParts[] = number_format($step['powered'], 6);
                            }
                            ?>
                            <strong style="color: var(--primary);">WP =</strong> <?= implode(' × ', $calcParts) ?>
                        </div>
                        <div class="calculation-result">
                            <strong>WP = <?= number_format($detail['nilai_wp'], 6) ?></strong>
                        </div>
                    </code>
                </div>
                    
                    <table class="values-table" style="width: 100%; font-size: 13px;">
                        <thead>
                            <tr>
                                <th>Kriteria</th>
                                <th>Normalisasi</th>
                                <th>Bobot</th>
                                <th>Normalisasi^Bobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail['steps'] as $step): ?>
                            <tr style="transition: all 0.3s;">
                                <td><strong><?= htmlspecialchars($step['kriteria_nama']) ?></strong></td>
                                <td><?= number_format($step['normalized'], 4) ?></td>
                                <td><?= number_format($step['bobot'], 4) ?></td>
                                <td style="color: var(--accent); font-weight: bold; font-size: 14px;"><?= number_format($step['powered'], 6) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach; ?>
            </div>
            
        <!-- Step 4: Ranking -->
        <div id="result-step-4" class="calc-step" style="display: none;">
            <h4>🏆 Step 4: Ranking Hasil</h4>
            <p style="color: var(--text-secondary); margin-bottom: 25px; line-height: 1.6;">
                Alternatif diurutkan berdasarkan nilai WP dari tertinggi ke terendah. Alternatif dengan nilai WP tertinggi adalah pilihan terbaik.
            </p>
            
            <div class="value-card">
                    <table class="results-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Ranking</th>
                                <th>Alternatif</th>
                                <th>Nilai WP</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $maxWp = $calculationDetails['wpDetails'][0]['nilai_wp'];
                            foreach ($calculationDetails['wpDetails'] as $detail): 
                                $percentage = ($detail['nilai_wp'] / $maxWp) * 100;
                            ?>
                            <tr style="transition: all 0.3s;" onmouseover="this.style.background='var(--bg-gray)'; this.style.transform='scale(1.01)'" onmouseout="this.style.background='white'; this.style.transform='scale(1)'">
                                <td>
                                    <span class="ranking-badge" style="background: <?= $detail['ranking'] == 1 ? 'linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%)' : 'linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%)' ?>; box-shadow: 0 2px 8px rgba(<?= $detail['ranking'] == 1 ? '251, 122, 46' : '66, 121, 180' ?>, 0.3);">
                                        #<?= $detail['ranking'] ?>
                                    </span>
                                </td>
                                <td><strong style="font-size: 16px;"><?= htmlspecialchars($detail['alternatif_nama']) ?></strong></td>
                                <td><strong style="color: var(--accent); font-size: 18px;"><?= number_format($detail['nilai_wp'], 6) ?></strong></td>
                                <td>
                                    <div class="ranking-progress">
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" style="background: <?= $detail['ranking'] == 1 ? 'linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%)' : 'linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%)' ?>; width: <?= $percentage ?>%;"></div>
                                        </div>
                                        <span style="font-weight: 600; color: var(--text-primary); min-width: 50px; text-align: right;"><?= number_format($percentage, 1) ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
            </div>
            
            <div class="conclusion-box">
                <h3>🎯 Kesimpulan</h3>
                <p>
                    Alternatif terbaik adalah <strong><?= htmlspecialchars($calculationDetails['wpDetails'][0]['alternatif_nama']) ?></strong>
                    dengan nilai WP <strong><?= number_format($calculationDetails['wpDetails'][0]['nilai_wp'], 6) ?></strong>
                </p>
            </div>
        </div>
    </div>
    
    <style>
    .view-container {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .view-header {
        margin-bottom: 30px;
    }
    
    .view-header h1 {
        color: var(--primary);
        margin-bottom: 10px;
        font-size: 32px;
    }
    
    .view-header p {
        color: var(--text-secondary);
        font-size: 16px;
    }
    
    .info-section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
    }
    
    .info-section:hover {
        box-shadow: 0 4px 20px rgba(35, 47, 93, 0.15);
    }
    
    .info-section h3 {
        color: var(--primary);
        margin-bottom: 20px;
        font-size: 24px;
        border-bottom: 3px solid var(--accent);
        padding-bottom: 10px;
    }
    
    .step-nav {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        flex-wrap: wrap;
        background: var(--bg-gray);
        padding: 15px;
        border-radius: 8px;
    }
    
    .step-btn {
        background: white;
        color: var(--text-primary);
        border: 2px solid var(--border-light);
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 14px;
    }
    
    .step-btn:hover {
        background: var(--secondary);
        color: white;
        border-color: var(--secondary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(66, 121, 180, 0.3);
    }
    
    .step-btn.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(251, 122, 46, 0.3);
    }
    
    .calc-step {
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
    
    .btn-visualize {
        background: var(--accent) !important;
        color: white !important;
        border: none !important;
        padding: 12px 24px !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 8px rgba(251, 122, 46, 0.2) !important;
    }
    
    .btn-visualize:hover {
        background: var(--accent-dark) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 16px rgba(251, 122, 46, 0.4) !important;
    }
    
    .visualization-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: var(--shadow);
    }
    
    .calc-step h4 {
        color: var(--accent);
        margin-bottom: 15px;
        font-size: 22px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .calc-step > div {
        margin-bottom: 20px;
    }
    
    .calc-step h5 {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: 600;
    }
    
    .formula-highlight {
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        line-height: 1.8;
    }
    
    .value-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
        border-left: 4px solid var(--accent);
        transition: all 0.3s ease;
    }
    
    .value-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 16px rgba(251, 122, 46, 0.2);
    }
    
    .wp-detail-card {
        background: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
        border-left: 4px solid var(--accent);
        transition: all 0.3s ease;
    }
    
    .wp-detail-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(251, 122, 46, 0.25);
    }
    
    .wp-detail-card h5 {
        color: var(--primary);
        margin-bottom: 15px;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .calculation-box {
        background: linear-gradient(135deg, var(--bg-gray) 0%, #F0F4F8 100%);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        border: 2px solid var(--border-light);
    }
    
    .calculation-box code {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.8;
        color: var(--text-primary);
    }
    
    .calculation-result {
        font-size: 20px;
        font-weight: bold;
        color: var(--accent);
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid var(--border-light);
    }
    
    .ranking-progress {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 10px;
    }
    
    .progress-bar-container {
        flex: 1;
        background: var(--border-light);
        height: 24px;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    
    .progress-bar {
        height: 100%;
        border-radius: 12px;
        transition: width 0.8s ease;
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .conclusion-box {
        margin-top: 30px;
        padding: 25px;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        color: white;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(251, 122, 46, 0.3);
    }
    
    .conclusion-box h3 {
        margin: 0 0 15px 0;
        font-size: 24px;
        color: white;
        border: none;
        padding: 0;
    }
    
    .conclusion-box p {
        margin: 0;
        font-size: 18px;
        line-height: 1.6;
    }
    
    @media (max-width: 768px) {
        .view-container {
            padding: 0 15px;
        }
        
        .info-section {
            padding: 20px;
        }
        
        .step-nav {
            flex-direction: column;
        }
        
        .step-btn {
            width: 100%;
        }
    }
    </style>
    
    <script>
    function toggleVisualization() {
        const section = document.getElementById('visualization-section');
        const btn = document.querySelector('.btn-visualize');
        
        if (!section || !btn) {
            console.error('Visualization elements not found');
            return;
        }
        
        if (section.style.display === 'none' || !section.style.display) {
            section.style.display = 'block';
            btn.textContent = '✖️ Tutup Visualisasi';
            setTimeout(() => {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        } else {
            section.style.display = 'none';
            btn.textContent = '📊 Lihat Visualisasi Perhitungan';
        }
    }
    
    function showResultStep(stepNum) {
        // Hide all steps
        for (let i = 1; i <= 4; i++) {
            const stepEl = document.getElementById('result-step-' + i);
            if (stepEl) {
                stepEl.style.display = 'none';
            }
            const stepBtns = document.querySelectorAll('.step-nav .step-btn');
            if (stepBtns[i - 1]) {
                stepBtns[i - 1].classList.remove('active');
            }
        }
        
        // Show selected step
        const selectedStep = document.getElementById('result-step-' + stepNum);
        if (selectedStep) {
            selectedStep.style.display = 'block';
        }
        
        const stepBtns = document.querySelectorAll('.step-nav .step-btn');
        if (stepBtns[stepNum - 1]) {
            stepBtns[stepNum - 1].classList.add('active');
        }
    }
    
    // Make functions globally available
    window.toggleVisualization = toggleVisualization;
    window.showResultStep = showResultStep;
    </script>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>

