// Data storage
let formData = {
    id: null,
    judulAnalisis: '',
    metode: 'WP',
    alternatif: [],
    kriteria: [],
    nilai: {},
    alternatifIds: [],
    kriteriaIds: []
};

// Helper function to convert comma to dot and parse float
function parseNumber(value) {
    if (typeof value === 'string') {
        // Replace comma with dot
        value = value.replace(',', '.');
    }
    const num = parseFloat(value);
    return isNaN(num) ? 0 : num;
}

// Helper function to format number for display (dot as decimal)
function formatNumber(num, decimals = 2) {
    return num.toFixed(decimals).replace('.', ',');
}

// Step navigation
function showStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(step => {
        step.classList.remove('active');
    });
    
    // Show selected step
    const stepElement = document.getElementById('step' + stepNumber);
    if (stepElement) {
        stepElement.classList.add('active');
    }
    
    // Update progress indicator
    document.querySelectorAll('.step').forEach((step, index) => {
        if (index + 1 <= stepNumber) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });
}

function nextStep(stepNumber) {
    // Validate current step
    if (stepNumber === 2) {
        const judul = document.getElementById('judulAnalisis').value;
        if (!judul.trim()) {
            alert('Mohon masukkan judul analisis');
            return;
        }
        formData.judulAnalisis = judul;
        formData.metode = document.querySelector('input[name="metode"]:checked').value;
    } else if (stepNumber === 3) {
        const alternatif = getAlternatif();
        if (alternatif.length < 2) {
            alert('Minimal harus ada 2 alternatif');
            return;
        }
        formData.alternatif = alternatif;
        // Preserve IDs if editing
        if (!formData.alternatifIds) {
            formData.alternatifIds = [];
        }
    } else if (stepNumber === 4) {
        const kriteria = getKriteria();
        if (kriteria.length < 1) {
            alert('Minimal harus ada 1 kriteria');
            return;
        }
        // Validate bobot sum
        const totalBobot = kriteria.reduce((sum, k) => sum + parseNumber(k.bobot), 0);
        if (Math.abs(totalBobot - 1) > 0.01) {
            alert('Total bobot harus sama dengan 1. Total saat ini: ' + totalBobot.toFixed(2).replace('.', ','));
            return;
        }
        formData.kriteria = kriteria;
        // Preserve IDs if editing
        if (!formData.kriteriaIds) {
            formData.kriteriaIds = [];
        }
        buildNilaiForm();
    }
    
    showStep(stepNumber);
}

function prevStep(stepNumber) {
    showStep(stepNumber);
}

// Alternatif management
function addAlternatif() {
    const container = document.getElementById('alternatifContainer');
    const count = container.children.length + 1;
    
    const div = document.createElement('div');
    div.className = 'form-group alternatif-item';
    div.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="flex: 1;">
                <label>Alternatif ${count}</label>
                <input type="text" class="alternatif-input" placeholder="Nama alternatif" required>
            </div>
            <button type="button" class="btn-add" onclick="removeAlternatif(this)" style="background: #f44336; margin-top: 25px; padding: 12px 16px;">Hapus</button>
        </div>
    `;
    
    container.appendChild(div);
    updateAlternatifLabels();
}

function removeAlternatif(button) {
    const container = document.getElementById('alternatifContainer');
    const currentCount = container.children.length;
    if (currentCount > 1) {
        button.closest('.alternatif-item').remove();
        updateAlternatifLabels();
    } else {
        alert('Minimal harus ada 1 alternatif. Untuk perhitungan WP, minimal diperlukan 2 alternatif.');
    }
}

function updateAlternatifLabels() {
    const container = document.getElementById('alternatifContainer');
    const items = container.querySelectorAll('.alternatif-item');
    items.forEach((item, index) => {
        const label = item.querySelector('label');
        if (label) {
            label.textContent = `Alternatif ${index + 1}`;
        }
        // Show/hide delete button based on count
        const deleteBtn = item.querySelector('button[onclick*="removeAlternatif"]');
        if (deleteBtn) {
            deleteBtn.style.display = items.length > 1 ? 'block' : 'none';
        }
    });
}

function getAlternatif() {
    const inputs = document.querySelectorAll('.alternatif-input');
    const alternatif = [];
    inputs.forEach(input => {
        if (input.value.trim()) {
            alternatif.push(input.value.trim());
        }
    });
    return alternatif;
}

// Kriteria management
function addKriteria() {
    const container = document.getElementById('kriteriaContainer');
    
    const div = document.createElement('div');
    div.className = 'kriteria-item';
    div.innerHTML = `
        <div class="form-group">
            <label>Nama Kriteria</label>
            <input type="text" class="kriteria-nama" placeholder="Nama kriteria" required>
        </div>
        <div class="form-group">
            <label>Bobot</label>
            <input type="text" class="kriteria-bobot" placeholder="0,00 atau 0.00" pattern="^\d+([,\.]\d{0,2})?$" required>
        </div>
        <div class="form-group">
            <label>Tipe</label>
            <select class="kriteria-tipe">
                <option value="benefit">Benefit</option>
                <option value="cost">Cost</option>
            </select>
        </div>
        <button type="button" class="btn-add" onclick="removeKriteria(this)" style="background: #f44336; margin-top: 10px;">Hapus</button>
    `;
    
    container.appendChild(div);
}

function removeKriteria(button) {
    const container = document.getElementById('kriteriaContainer');
    if (container.children.length > 1) {
        button.parentElement.remove();
    } else {
        alert('Minimal harus ada 1 kriteria');
    }
}

function getKriteria() {
    const items = document.querySelectorAll('.kriteria-item');
    const kriteria = [];
    items.forEach(item => {
        const nama = item.querySelector('.kriteria-nama').value.trim();
        const bobotInput = item.querySelector('.kriteria-bobot');
        const bobot = parseNumber(bobotInput.value);
        const tipe = item.querySelector('.kriteria-tipe').value;
        
        if (nama && bobot > 0) {
            kriteria.push({ nama, bobot, tipe });
        }
    });
    return kriteria;
}

// Build nilai form
function buildNilaiForm() {
    const container = document.getElementById('nilaiContainer');
    const { alternatif, kriteria, nilai } = formData;
    
    let html = '<table class="values-table">';
    html += '<thead><tr><th>Alternatif</th>';
    kriteria.forEach(k => {
        html += `<th>${k.nama}<br><small>(${k.tipe === 'benefit' ? 'Benefit' : 'Cost'})</small></th>`;
    });
    html += '</tr></thead><tbody>';
    
    alternatif.forEach((alt, altIndex) => {
        html += `<tr><td><strong>${alt}</strong></td>`;
        kriteria.forEach((kri, kriIndex) => {
            const existingValue = nilai[altIndex] && nilai[altIndex][kriIndex] ? nilai[altIndex][kriIndex] : '';
            const displayValue = existingValue ? existingValue.toString().replace('.', ',') : '';
            html += `<td><input type="text" class="nilai-input" data-alt="${altIndex}" data-kri="${kriIndex}" placeholder="0" pattern="^\d+([,\.]\d{0,2})?$" value="${displayValue}" required></td>`;
        });
        html += '</tr>';
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
}

// Calculate WP
function calculateWP() {
    // Get all nilai
    const inputs = document.querySelectorAll('.nilai-input');
    const nilai = {};
    
    inputs.forEach(input => {
        const altIndex = parseInt(input.dataset.alt);
        const kriIndex = parseInt(input.dataset.kri);
        const value = parseNumber(input.value);
        
        if (value === 0 && !input.value.trim()) {
            alert('Mohon lengkapi semua nilai');
            return;
        }
        
        if (!nilai[altIndex]) {
            nilai[altIndex] = {};
        }
        nilai[altIndex][kriIndex] = value;
    });
    
    formData.nilai = nilai;
    
    // Save to database first
    saveToDatabase().then(() => {
        // Perform WP calculation
        const results = performWPCalculation();
        
        // Save results to database
        saveResults(results);
        
        // Show results
        showResults(results);
    }).catch(error => {
        console.error('Error saving:', error);
        const errorMsg = error.message || 'Gagal menyimpan data. Silakan coba lagi.';
        alert(errorMsg);
    });
}

// Save data to database
async function saveToDatabase() {
    try {
        const analisisId = document.getElementById('analisisId')?.value || null;
        const data = {
            id: analisisId ? parseInt(analisisId) : null,
            judul: formData.judulAnalisis,
            metode: formData.metode,
            alternatif: formData.alternatif,
            kriteria: formData.kriteria,
            nilai: formData.nilai
        };
        
        // Save draft to localStorage
        localStorage.setItem('wp_draft', JSON.stringify(data));
        
        // Convert nilai format for API
        const nilaiFormatted = {};
        Object.keys(data.nilai).forEach(altIndex => {
            Object.keys(data.nilai[altIndex]).forEach(kriIndex => {
                // Get actual IDs from formData if editing
                const altId = formData.alternatifIds ? formData.alternatifIds[altIndex] : null;
                const kriId = formData.kriteriaIds ? formData.kriteriaIds[kriIndex] : null;
                
                if (altId && kriId) {
                    if (!nilaiFormatted[altId]) {
                        nilaiFormatted[altId] = {};
                    }
                    nilaiFormatted[altId][kriId] = data.nilai[altIndex][kriIndex];
                } else {
                    // For new entries, use index
                    if (!nilaiFormatted[altIndex]) {
                        nilaiFormatted[altIndex] = {};
                    }
                    nilaiFormatted[altIndex][kriIndex] = data.nilai[altIndex][kriIndex];
                }
            });
        });
        data.nilai = nilaiFormatted;
        
        const url = 'controllers/InputController.php?action=save';
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        
        const result = await response.json();
        
        if (result.success) {
            // Clear draft on success
            localStorage.removeItem('wp_draft');
            if (result.id) {
                formData.id = result.id;
                const analisisIdInput = document.getElementById('analisisId');
                if (analisisIdInput) {
                    analisisIdInput.value = result.id;
                }
            }
            return result;
        } else {
            const errorMsg = result.error || 'Gagal menyimpan data ke database';
            throw new Error(errorMsg);
        }
    } catch (error) {
        console.error('Save error:', error);
        throw error;
    }
}

// Save results to database
async function saveResults(results) {
    if (!formData.id) return;
    
    try {
        // Save results via CalculateController
        const hasilResponse = await fetch('controllers/CalculateController.php?action=calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                analisis_id: formData.id
            })
        });
        
        if (!hasilResponse.ok) {
            throw new Error('Network response was not ok: ' + hasilResponse.status);
        }
        
        return await hasilResponse.json();
    } catch (error) {
        console.error('Error saving results:', error);
        throw error;
    }
}

function performWPCalculation() {
    const { alternatif, kriteria, nilai } = formData;
    const results = [];
    
    // Step 1: Normalize values
    const normalized = {};
    alternatif.forEach((alt, altIndex) => {
        normalized[altIndex] = {};
        kriteria.forEach((kri, kriIndex) => {
            const value = nilai[altIndex][kriIndex];
            let normalizedValue = 0;
            
            if (kri.tipe === 'benefit') {
                // For benefit: divide by max
                const max = Math.max(...alternatif.map((a, ai) => nilai[ai][kriIndex]));
                normalizedValue = value / max;
            } else {
                // For cost: divide min by value
                const min = Math.min(...alternatif.map((a, ai) => nilai[ai][kriIndex]));
                normalizedValue = min / value;
            }
            
            normalized[altIndex][kriIndex] = normalizedValue;
        });
    });
    
    // Step 2: Calculate WP (Weighted Product)
    alternatif.forEach((alt, altIndex) => {
        let wp = 1;
        kriteria.forEach((kri, kriIndex) => {
            wp *= Math.pow(normalized[altIndex][kriIndex], kri.bobot);
        });
        
        results.push({
            alternatif: alt,
            nilai: nilai[altIndex],
            normalized: normalized[altIndex],
            wp: wp
        });
    });
    
    // Step 3: Sort by WP (descending)
    results.sort((a, b) => b.wp - a.wp);
    
    // Step 4: Add ranking
    results.forEach((result, index) => {
        result.ranking = index + 1;
    });
    
    return results;
}

function showResults(results) {
    const { judulAnalisis, kriteria } = formData;
    
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(step => {
        step.classList.remove('active');
    });
    
    // Show results
    const resultsDiv = document.getElementById('results');
    resultsDiv.classList.add('active');
    
    // Build results HTML
    let html = `<h3 style="margin-bottom: 20px;">${judulAnalisis}</h3>`;
    html += '<table class="results-table">';
    html += '<thead><tr><th>Ranking</th><th>Alternatif</th>';
    kriteria.forEach(k => {
        html += `<th>${k.nama}</th>`;
    });
    html += '<th>Nilai WP</th></tr></thead><tbody>';
    
    results.forEach(result => {
        html += '<tr>';
        html += `<td><span class="ranking-badge">${result.ranking}</span></td>`;
        html += `<td><strong>${result.alternatif}</strong></td>`;
        kriteria.forEach((kri, kriIndex) => {
            html += `<td>${result.nilai[kriIndex].toFixed(2)}</td>`;
        });
        html += `<td><strong>${result.wp.toFixed(4)}</strong></td>`;
        html += '</tr>';
    });
    
    html += '</tbody></table>';
    
    // Add summary
    html += '<div style="margin-top: 30px; padding: 20px; background: #e3f2fd; border-radius: 6px;">';
    html += '<h4 style="margin-bottom: 10px;">Kesimpulan</h4>';
    html += `<p><strong>Alternatif terbaik:</strong> ${results[0].alternatif} dengan nilai WP ${results[0].wp.toFixed(4)}</p>`;
    html += '</div>';
    
    document.getElementById('resultsContent').innerHTML = html;
}

function resetForm() {
    formData = {
        id: null,
        judulAnalisis: '',
        metode: 'WP',
        alternatif: [],
        kriteria: [],
        nilai: {},
        alternatifIds: [],
        kriteriaIds: []
    };
    
    // Reset form
    document.getElementById('basicInfoForm').reset();
    document.getElementById('alternatifContainer').innerHTML = `
        <div class="form-group alternatif-item">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="flex: 1;">
                    <label>Alternatif 1</label>
                    <input type="text" class="alternatif-input" placeholder="Nama alternatif" required>
                </div>
                <button type="button" class="btn-add" onclick="removeAlternatif(this)" style="background: #f44336; margin-top: 25px; padding: 12px 16px; display: none;">Hapus</button>
            </div>
        </div>
    `;
    document.getElementById('kriteriaContainer').innerHTML = `
        <div class="kriteria-item">
            <div class="form-group">
                <label>Nama Kriteria</label>
                <input type="text" class="kriteria-nama" placeholder="Nama kriteria" required>
            </div>
            <div class="form-group">
                <label>Bobot</label>
                <input type="text" class="kriteria-bobot" placeholder="0,00 atau 0.00" pattern="^\d+([,\.]\d{0,2})?$" required>
            </div>
            <div class="form-group">
                <label>Tipe</label>
                <select class="kriteria-tipe">
                    <option value="benefit">Benefit</option>
                    <option value="cost">Cost</option>
                </select>
            </div>
        </div>
    `;
    document.getElementById('nilaiContainer').innerHTML = '';
    document.getElementById('resultsContent').innerHTML = '';
    
    showStep(1);
}

// Load data for editing
function loadEditData(data) {
    formData.id = data.id;
    formData.judulAnalisis = data.judul;
    formData.metode = data.metode;
    
    // Load alternatif
    if (data.alternatif && data.alternatif.length > 0) {
        const container = document.getElementById('alternatifContainer');
        container.innerHTML = '';
        formData.alternatifIds = [];
        
        data.alternatif.forEach((alt, index) => {
            const div = document.createElement('div');
            div.className = 'form-group alternatif-item';
            const showDeleteBtn = data.alternatif.length > 1 ? '' : 'display: none;';
            div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="flex: 1;">
                        <label>Alternatif ${index + 1}</label>
                        <input type="text" class="alternatif-input" placeholder="Nama alternatif" value="${alt.nama}" required>
                    </div>
                    <button type="button" class="btn-add" onclick="removeAlternatif(this)" style="background: #f44336; margin-top: 25px; padding: 12px 16px; ${showDeleteBtn}">Hapus</button>
                </div>
            `;
            container.appendChild(div);
            formData.alternatif.push(alt.nama);
            formData.alternatifIds.push(alt.id);
        });
    }
    
    // Load kriteria
    if (data.kriteria && data.kriteria.length > 0) {
        const container = document.getElementById('kriteriaContainer');
        container.innerHTML = '';
        formData.kriteriaIds = [];
        
        data.kriteria.forEach((kri, index) => {
            const div = document.createElement('div');
            div.className = 'kriteria-item';
            div.innerHTML = `
                <div class="form-group">
                    <label>Nama Kriteria</label>
                    <input type="text" class="kriteria-nama" placeholder="Nama kriteria" value="${kri.nama}" required>
                </div>
                <div class="form-group">
                    <label>Bobot</label>
                    <input type="text" class="kriteria-bobot" placeholder="0,00 atau 0.00" pattern="[0-9]+([,\.][0-9]+)?" value="${kri.bobot.toString().replace('.', ',')}" required>
                </div>
                <div class="form-group">
                    <label>Tipe</label>
                    <select class="kriteria-tipe">
                        <option value="benefit" ${kri.tipe === 'benefit' ? 'selected' : ''}>Benefit</option>
                        <option value="cost" ${kri.tipe === 'cost' ? 'selected' : ''}>Cost</option>
                    </select>
                </div>
                ${data.kriteria.length > 1 ? '<button type="button" class="btn-add" onclick="removeKriteria(this)" style="background: #f44336; margin-top: 10px;">Hapus</button>' : ''}
            `;
            container.appendChild(div);
            formData.kriteria.push({ nama: kri.nama, bobot: parseNumber(kri.bobot), tipe: kri.tipe });
            formData.kriteriaIds.push(kri.id);
        });
    }
    
    // Load nilai
    if (data.nilai) {
        formData.nilai = {};
        Object.keys(data.nilai).forEach(altId => {
            const altIndex = formData.alternatifIds.indexOf(parseInt(altId));
            if (altIndex >= 0) {
                formData.nilai[altIndex] = {};
                Object.keys(data.nilai[altId]).forEach(kriId => {
                    const kriIndex = formData.kriteriaIds.indexOf(parseInt(kriId));
                    if (kriIndex >= 0) {
                        formData.nilai[altIndex][kriIndex] = parseNumber(data.nilai[altId][kriId]);
                    }
                });
            }
        });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    showStep(1);
    
    // Load draft from localStorage if exists
    const draft = localStorage.getItem('wp_draft');
    if (draft && !document.getElementById('analisisId')?.value) {
        try {
            const draftData = JSON.parse(draft);
            if (confirm('Ada draft yang tersimpan. Ingin melanjutkan?')) {
                // Load draft data ke form
                if (draftData.judul) {
                    document.getElementById('judulAnalisis').value = draftData.judul;
                    formData.judulAnalisis = draftData.judul;
                }
                if (draftData.metode) {
                    const radio = document.querySelector(`input[name="metode"][value="${draftData.metode}"]`);
                    if (radio) radio.checked = true;
                    formData.metode = draftData.metode;
                }
                // Note: Alternatif, kriteria, dan nilai bisa di-load lebih lanjut jika diperlukan
            } else {
                localStorage.removeItem('wp_draft');
            }
        } catch (e) {
            console.error('Error loading draft:', e);
            localStorage.removeItem('wp_draft');
        }
    }
    
    // Add event listeners for number inputs to handle comma
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('kriteria-bobot') || e.target.classList.contains('nilai-input')) {
            // Allow both comma and dot, but validate format
            let value = e.target.value;
            // Remove any non-numeric characters except comma and dot
            value = value.replace(/[^0-9,.]/g, '');
            // Only allow one decimal separator
            const parts = value.split(/[,.]/);
            if (parts.length > 2) {
                value = parts[0] + (parts[1] ? (',' + parts.slice(1).join('')) : '');
            }
            if (e.target.value !== value) {
                e.target.value = value;
            }
        }
    });
});

