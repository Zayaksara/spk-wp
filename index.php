<?php
require_once 'config.php';
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$analisis_data = null;

if ($edit_id > 0) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM analisis WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $analisis_data = $stmt->get_result()->fetch_assoc();
    
    if ($analisis_data) {
        // Get alternatif
        $stmt = $conn->prepare("SELECT * FROM alternatif WHERE analisis_id = ? ORDER BY urutan");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $analisis_data['alternatif'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get kriteria
        $stmt = $conn->prepare("SELECT * FROM kriteria WHERE analisis_id = ? ORDER BY urutan");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $analisis_data['kriteria'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get nilai
        $stmt = $conn->prepare("SELECT * FROM nilai WHERE analisis_id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $nilai = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $nilaiOrganized = [];
        foreach ($nilai as $n) {
            $nilaiOrganized[$n['alternatif_id']][$n['kriteria_id']] = $n['nilai'];
        }
        $analisis_data['nilai'] = $nilaiOrganized;
    }
    closeDBConnection($conn);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_id > 0 ? 'Edit' : 'Tambah' ?> Analisis WP - Weighted Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php if ($edit_id > 0): ?>
        <div style="margin-bottom: 20px;">
            <a href="manage.php" style="color: #2196F3; text-decoration: none;">← Kembali ke Kelola Analisis</a>
        </div>
        <?php endif; ?>
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
                        <input type="number" class="kriteria-bobot" placeholder="0.00" step="0.01" min="0" max="1" required>
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
                <a href="manage.php" class="btn-continue" style="text-decoration: none; display: inline-block;">Kelola Analisis</a>
            </div>
        </div>
    </div>

    <footer>
        <p>Kelompok WP - UIN Siber Syekh Nurjati Cirebon</p>
    </footer>

    <script src="script.js"></script>
    <?php if ($analisis_data): ?>
    <script>
        // Load edit data
        const editData = <?= json_encode($analisis_data) ?>;
        loadEditData(editData);
        
        // If nilai exists, go to step 4
        if (editData.nilai && Object.keys(editData.nilai).length > 0) {
            buildNilaiForm();
            // Populate nilai inputs
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
            // Go to step 4 if kriteria exists
            showStep(4);
            buildNilaiForm();
        } else if (editData.alternatif && editData.alternatif.length > 0) {
            // Go to step 3 if alternatif exists
            showStep(3);
        } else {
            // Go to step 2 if basic info exists
            showStep(2);
        }
    </script>
    <?php endif; ?>
</body>
</html>

