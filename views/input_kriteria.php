<?php
$page_title = (isset($analisis) && $analisis) ? 'Edit Analisis WP' : 'Tambah Analisis WP';
require_once __DIR__ . '/layouts/header.php';

$edit_id = isset($analisis) && $analisis ? $analisis['id'] : 0;
$analisis_data = null;

if ($edit_id > 0 && isset($analisis)) {
    $analisis_data = $analisis;
    // Get alternatif, kriteria, nilai from controller
}
?>
<div class="container">
    <div style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
        <a href="<?= BASE_URL ?>?action=dashboard" style="color: #2196F3; text-decoration: none;">📊 Dashboard</a>
        <span>|</span>
        <a href="<?= BASE_URL ?>?action=index" style="color: #2196F3; text-decoration: none;">📋 Kelola Analisis</a>
        <?php if ($edit_id > 0): ?>
        <span>|</span>
        <span style="color: #666;">Edit Analisis</span>
        <?php endif; ?>
    </div>
    
    <!-- Progress Indicator -->
    <div class="progress-indicator">
        <div class="step active" data-step="1">
            <div class="step-circle">1</div>
            <div class="step-label">Info Dasar</div>
        </div>
        <div class="step" data-step="2">
            <div class="step-circle">2</div>
            <div class="step-label">Alternatif</div>
        </div>
        <div class="step" data-step="3">
            <div class="step-circle">3</div>
            <div class="step-label">Kriteria</div>
        </div>
        <div class="step" data-step="4">
            <div class="step-circle">4</div>
            <div class="step-label">Nilai</div>
        </div>
    </div>

    <!-- Step 1: Basic Information -->
    <div class="form-card step-content active" id="step1">
        <h2>Informasi Dasar</h2>
        <p class="instruction">Masukkan judul dan pilih metode analisis</p>
        <form id="basicInfoForm">
            <input type="hidden" id="analisisId" value="<?= $edit_id ?>">
            <div class="form-group">
                <label for="judulAnalisis">Judul Analisis</label>
                <input type="text" id="judulAnalisis" name="judulAnalisis" placeholder="Masukkan judul analisis" value="<?= $analisis_data ? htmlspecialchars($analisis_data['judul']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label>Metode Analisis</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="metode" value="WP" checked>
                        <span>WP - Weighted Product</span>
                    </label>
                </div>
            </div>
            <button type="button" class="btn-continue" onclick="nextStep(2)">Lanjut</button>
        </form>
    </div>

    <!-- Step 2: Alternatives -->
    <div class="form-card step-content" id="step2">
        <h2>Alternatif</h2>
        <p class="instruction">Masukkan alternatif yang akan dinilai</p>
        <div id="alternatifContainer">
            <div class="form-group alternatif-item">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="flex: 1;">
                        <label>Alternatif 1</label>
                        <input type="text" class="alternatif-input" placeholder="Nama alternatif" required>
                    </div>
                    <button type="button" class="btn-add" onclick="removeAlternatif(this)" style="background: #f44336; margin-top: 25px; padding: 12px 16px; display: none;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn-add" onclick="addAlternatif()">+ Tambah Alternatif</button>
        <div class="button-group">
            <button type="button" class="btn-back" onclick="prevStep(1)">Kembali</button>
            <button type="button" class="btn-continue" onclick="nextStep(3)">Lanjut</button>
        </div>
    </div>

    <!-- Step 3: Criteria -->
    <div class="form-card step-content" id="step3">
        <h2>Kriteria</h2>
        <p class="instruction">Masukkan kriteria penilaian dan bobot</p>
        <div id="kriteriaContainer">
            <div class="kriteria-item">
                <div class="form-group">
                    <label>Nama Kriteria</label>
                    <input type="text" class="kriteria-nama" placeholder="Nama kriteria" required>
                </div>
                <div class="form-group">
                    <label>Bobot</label>
                    <input type="text" class="kriteria-bobot" placeholder="0,00 atau 0.00" pattern="[0-9]+([,\.][0-9]+)?" required>
                </div>
                <div class="form-group">
                    <label>Tipe</label>
                    <select class="kriteria-tipe">
                        <option value="benefit">Benefit</option>
                        <option value="cost">Cost</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="button" class="btn-add" onclick="addKriteria()">+ Tambah Kriteria</button>
        <div class="button-group">
            <button type="button" class="btn-back" onclick="prevStep(2)">Kembali</button>
            <button type="button" class="btn-continue" onclick="nextStep(4)">Lanjut</button>
        </div>
    </div>

    <!-- Step 4: Values -->
    <div class="form-card step-content" id="step4">
        <h2>Nilai</h2>
        <p class="instruction">Masukkan nilai untuk setiap alternatif pada setiap kriteria</p>
        <div id="nilaiContainer"></div>
        <div class="button-group">
            <button type="button" class="btn-back" onclick="prevStep(3)">Kembali</button>
            <button type="button" class="btn-continue" onclick="calculateWP()">Hitung</button>
        </div>
    </div>

    <!-- Results -->
    <div class="form-card step-content" id="results">
        <h2>Hasil Perhitungan WP</h2>
        <div id="resultsContent"></div>
        <div class="button-group">
            <button type="button" class="btn-back" onclick="resetForm()">Mulai Lagi</button>
            <a href="<?= BASE_URL ?>" class="btn-continue" style="text-decoration: none; display: inline-block;">Kelola Analisis</a>
        </div>
    </div>
</div>

<?php if ($analisis_data): ?>
<script>
    // Load edit data
    const editData = <?= json_encode($analisis_data) ?>;
    loadEditData(editData);
    
    // If nilai exists, go to step 4
    if (editData.nilai && Object.keys(editData.nilai).length > 0) {
        buildNilaiForm();
        setTimeout(() => {
            Object.keys(formData.nilai).forEach(altIndex => {
                Object.keys(formData.nilai[altIndex]).forEach(kriIndex => {
                    const input = document.querySelector(`.nilai-input[data-alt="${altIndex}"][data-kri="${kriIndex}"]`);
                    if (input) {
                        input.value = formData.nilai[altIndex][kriIndex];
                    }
                });
            });
        }, 100);
    } else if (editData.kriteria && editData.kriteria.length > 0) {
        showStep(4);
        buildNilaiForm();
    } else if (editData.alternatif && editData.alternatif.length > 0) {
        showStep(3);
    } else {
        showStep(2);
    }
</script>
<?php endif; ?>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>

