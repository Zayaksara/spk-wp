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
        <h3>Hasil Perhitungan WP</h3>
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
        <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 6px;">
            <strong>Kesimpulan:</strong> Alternatif terbaik adalah <strong><?= htmlspecialchars($hasil[0]['alternatif_nama']) ?></strong> 
            dengan nilai WP <?= number_format($hasil[0]['nilai_wp'], 4) ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>

