// Simulasi WP Calculation - Client-side interactive calculation
let calculationData = {
    kriteria: [],
    alternatif: [],
    nilai: [],
    normalized: [],
    wpScores: [],
    ranking: []
};

let wpChart = null;

// Parse input data
function parseInput() {
    const kriteriaInput = document.getElementById('kriteriaInput').value.trim();
    const alternatifInput = document.getElementById('alternatifInput').value.trim();
    
    // Clear previous error
    hideError();
    
    if (!kriteriaInput || !alternatifInput) {
        showError('Mohon isi data kriteria dan alternatif!');
        return false;
    }
    
    // Parse Kriteria
    const kriteriaLines = kriteriaInput.split('\n').filter(line => line.trim());
    calculationData.kriteria = [];
    
    for (let i = 0; i < kriteriaLines.length; i++) {
        const parts = kriteriaLines[i].split('|').map(p => p.trim());
        if (parts.length !== 3) {
            showError(`Format kriteria baris ${i + 1} salah! Format: Nama|Bobot|Tipe`);
            return false;
        }
        
        const bobot = parseFloat(parts[1]);
        if (isNaN(bobot) || bobot <= 0 || bobot > 1) {
            showError(`Bobot kriteria "${parts[0]}" harus antara 0 dan 1!`);
            return false;
        }
        
        if (parts[2].toLowerCase() !== 'benefit' && parts[2].toLowerCase() !== 'cost') {
            showError(`Tipe kriteria "${parts[0]}" harus "benefit" atau "cost"!`);
            return false;
        }
        
        calculationData.kriteria.push({
            nama: parts[0],
            bobot: bobot,
            tipe: parts[2].toLowerCase()
        });
    }
    
    // Validate total bobot
    const totalBobot = calculationData.kriteria.reduce((sum, k) => sum + k.bobot, 0);
    if (Math.abs(totalBobot - 1) > 0.01) {
        showError(`Total bobot harus sama dengan 1! Total saat ini: ${totalBobot.toFixed(4)}`);
        return false;
    }
    
    // Parse Alternatif
    const alternatifLines = alternatifInput.split('\n').filter(line => line.trim());
    calculationData.alternatif = [];
    calculationData.nilai = [];
    
    for (let i = 0; i < alternatifLines.length; i++) {
        const parts = alternatifLines[i].split('|').map(p => p.trim());
        if (parts.length < 2) {
            showError(`Format alternatif baris ${i + 1} salah! Format: Nama|Nilai1|Nilai2|...`);
            return false;
        }
        
        if (parts.length - 1 !== calculationData.kriteria.length) {
            showError(`Alternatif "${parts[0]}" harus memiliki ${calculationData.kriteria.length} nilai (sesuai jumlah kriteria)!`);
            return false;
        }
        
        const nilai = [];
        for (let j = 1; j < parts.length; j++) {
            const val = parseFloat(parts[j]);
            if (isNaN(val) || val < 0) {
                showError(`Nilai alternatif "${parts[0]}" untuk kriteria ${j} tidak valid!`);
                return false;
            }
            nilai.push(val);
        }
        
        calculationData.alternatif.push(parts[0]);
        calculationData.nilai.push(nilai);
    }
    
    if (calculationData.alternatif.length < 2) {
        showError('Minimal diperlukan 2 alternatif!');
        return false;
    }
    
    return true;
}

// Calculate WP
function hitungWP() {
    console.log('hitungWP called');
    try {
        console.log('Parsing input...');
        if (!parseInput()) {
            console.log('Input parsing failed');
            return;
        }
        
        console.log('Calculation data:', calculationData);
        
        // Step 1: Normalize values
        console.log('Normalizing values...');
        normalizeValues();
        console.log('Normalized:', calculationData.normalized);
        
        // Step 2: Calculate WP scores
        console.log('Calculating WP scores...');
        calculateWPScores();
        console.log('WP Scores:', calculationData.wpScores);
        
        // Step 3: Rank alternatives
        console.log('Ranking alternatives...');
        rankAlternatives();
        console.log('Ranking:', calculationData.ranking);
        
        // Render all steps
        console.log('Rendering steps...');
        renderStep1();
        renderStep2();
        renderStep3();
        renderStep4();
        console.log('All steps rendered');
        
        // Show step indicator and first step
        const stepIndicator = document.getElementById('stepIndicator');
        if (stepIndicator) {
            stepIndicator.style.display = 'flex';
            console.log('Step indicator shown');
        } else {
            console.error('Step indicator not found');
        }
        
        showStep(1);
        console.log('hitungWP completed successfully');
    } catch (error) {
        console.error('Error in hitungWP:', error);
        console.error('Error stack:', error.stack);
        showError('Terjadi kesalahan saat menghitung: ' + error.message);
    }
}

// Normalize values
function normalizeValues() {
    calculationData.normalized = [];
    
    for (let altIndex = 0; altIndex < calculationData.alternatif.length; altIndex++) {
        const normalizedRow = [];
        
        for (let kriIndex = 0; kriIndex < calculationData.kriteria.length; kriIndex++) {
            const kriteria = calculationData.kriteria[kriIndex];
            const nilai = calculationData.nilai[altIndex][kriIndex];
            let normalized = 0;
            
            if (kriteria.tipe === 'benefit') {
                // Benefit: divide by max
                const max = Math.max(...calculationData.nilai.map(row => row[kriIndex]));
                normalized = max > 0 ? nilai / max : 0;
            } else {
                // Cost: divide min by value
                const min = Math.min(...calculationData.nilai.map(row => row[kriIndex]));
                normalized = (min > 0 && nilai > 0) ? min / nilai : 0;
            }
            
            normalizedRow.push(normalized);
        }
        
        calculationData.normalized.push(normalizedRow);
    }
}

// Calculate WP scores
function calculateWPScores() {
    calculationData.wpScores = [];
    
    for (let altIndex = 0; altIndex < calculationData.alternatif.length; altIndex++) {
        let wp = 1;
        const steps = [];
        
        for (let kriIndex = 0; kriIndex < calculationData.kriteria.length; kriIndex++) {
            const normalized = calculationData.normalized[altIndex][kriIndex];
            const bobot = calculationData.kriteria[kriIndex].bobot;
            const powered = Math.pow(normalized, bobot);
            wp *= powered;
            
            steps.push({
                kriteria: calculationData.kriteria[kriIndex].nama,
                normalized: normalized,
                bobot: bobot,
                powered: powered
            });
        }
        
        calculationData.wpScores.push({
            alternatif: calculationData.alternatif[altIndex],
            wp: wp,
            steps: steps
        });
    }
}

// Rank alternatives
function rankAlternatives() {
    // Sort by WP descending
    calculationData.ranking = [...calculationData.wpScores].sort((a, b) => b.wp - a.wp);
    
    // Add rank
    calculationData.ranking.forEach((item, index) => {
        item.rank = index + 1;
    });
}

// Render Step 1: Data Awal
function renderStep1() {
    // Kriteria Info
    const kriteriaInfoEl = document.getElementById('kriteriaInfo');
    if (!kriteriaInfoEl) {
        console.error('Element kriteriaInfo not found');
        return;
    }
    
    let kriteriaHtml = '<table style="width: 100%; color: white;"><thead><tr><th>Kriteria</th><th>Bobot</th><th>Tipe</th></tr></thead><tbody>';
    calculationData.kriteria.forEach(k => {
        kriteriaHtml += `<tr>
            <td><strong>${k.nama}</strong></td>
            <td>${k.bobot.toFixed(4)}</td>
            <td><span style="padding: 4px 10px; background: rgba(255,255,255,0.2); border-radius: 4px;">${k.tipe}</span></td>
        </tr>`;
    });
    kriteriaHtml += '</tbody></table>';
    kriteriaInfoEl.innerHTML = kriteriaHtml;
    
    // Matrix Table
    const matrixTableEl = document.getElementById('matrixTable');
    if (!matrixTableEl) {
        console.error('Element matrixTable not found');
        return;
    }
    
    let matrixHtml = '<table><thead><tr><th>Alternatif</th>';
    calculationData.kriteria.forEach(k => {
        matrixHtml += `<th>${k.nama}</th>`;
    });
    matrixHtml += '</tr></thead><tbody>';
    
    calculationData.alternatif.forEach((alt, altIndex) => {
        matrixHtml += `<tr><td><strong>${alt}</strong></td>`;
        calculationData.nilai[altIndex].forEach(nilai => {
            matrixHtml += `<td>${nilai.toFixed(2)}</td>`;
        });
        matrixHtml += '</tr>';
    });
    matrixHtml += '</tbody></table>';
    matrixTableEl.innerHTML = matrixHtml;
}

// Render Step 2: Normalisasi
function renderStep2() {
    const normalisasiFormulaEl = document.getElementById('normalisasiFormula');
    const normalizedTableEl = document.getElementById('normalizedTable');
    
    if (!normalisasiFormulaEl || !normalizedTableEl) {
        console.error('Elements for step 2 not found');
        return;
    }
    
    // Formula
    let formulaHtml = '<p style="font-size: 16px; margin: 10px 0;">';
    calculationData.kriteria.forEach((k, index) => {
        if (k.tipe === 'benefit') {
            const max = Math.max(...calculationData.nilai.map(row => row[index]));
            formulaHtml += `<strong>${k.nama} (benefit):</strong> Normalisasi = Nilai ÷ ${max.toFixed(2)}<br>`;
        } else {
            const min = Math.min(...calculationData.nilai.map(row => row[index]));
            formulaHtml += `<strong>${k.nama} (cost):</strong> Normalisasi = ${min.toFixed(2)} ÷ Nilai<br>`;
        }
    });
    formulaHtml += '</p>';
    normalisasiFormulaEl.innerHTML = formulaHtml;
    
    // Normalized Table
    let normalizedHtml = '<table><thead><tr><th>Alternatif</th>';
    calculationData.kriteria.forEach(k => {
        normalizedHtml += `<th>${k.nama}</th>`;
    });
    normalizedHtml += '</tr></thead><tbody>';
    
    calculationData.alternatif.forEach((alt, altIndex) => {
        normalizedHtml += `<tr><td><strong>${alt}</strong></td>`;
        calculationData.normalized[altIndex].forEach(norm => {
            normalizedHtml += `<td style="color: var(--accent); font-weight: bold;">${norm.toFixed(4)}</td>`;
        });
        normalizedHtml += '</tr>';
    });
    normalizedHtml += '</tbody></table>';
    normalizedTableEl.innerHTML = normalizedHtml;
    
    // Trigger MathJax render
    if (window.MathJax && window.MathJax.typesetPromise) {
        MathJax.typesetPromise();
    }
}

// Render Step 3: Perhitungan WP
function renderStep3() {
    const wpFormulaEl = document.getElementById('wpFormula');
    const wpCalculationTableEl = document.getElementById('wpCalculationTable');
    
    if (!wpFormulaEl || !wpCalculationTableEl) {
        console.error('Elements for step 3 not found');
        return;
    }
    
    // Formula
    let formulaHtml = '<p style="font-size: 16px; margin: 10px 0;">';
    formulaHtml += '<strong>Rumus WP:</strong> WP = ∏(Nilai Normalisasi^Bobot) untuk setiap kriteria<br>';
    formulaHtml += 'Dimana ∏ adalah perkalian semua kriteria</p>';
    wpFormulaEl.innerHTML = formulaHtml;
    
    // WP Calculation Table
    let wpHtml = '<table><thead><tr><th>Alternatif</th><th>Perhitungan</th><th>Nilai WP</th></tr></thead><tbody>';
    
    calculationData.wpScores.forEach(item => {
        let formula = '';
        let calculation = '';
        const parts = [];
        
        item.steps.forEach((step, index) => {
            const part = `(${step.normalized.toFixed(4)}^${step.bobot.toFixed(4)})`;
            parts.push(part);
            formula += part;
            if (index < item.steps.length - 1) formula += ' × ';
            
            calculation += step.powered.toFixed(6);
            if (index < item.steps.length - 1) calculation += ' × ';
        });
        
        wpHtml += `<tr>
            <td><strong>${item.alternatif}</strong></td>
            <td style="font-family: monospace; font-size: 12px;">
                <div>${formula}</div>
                <div style="color: var(--text-secondary); margin-top: 5px;">= ${calculation}</div>
            </td>
            <td style="color: var(--accent); font-weight: bold; font-size: 18px;">${item.wp.toFixed(6)}</td>
        </tr>`;
    });
    
    wpHtml += '</tbody></table>';
    wpCalculationTableEl.innerHTML = wpHtml;
    
    // Trigger MathJax render
    if (window.MathJax && window.MathJax.typesetPromise) {
        MathJax.typesetPromise();
    }
}

// Render Step 4: Ranking
function renderStep4() {
    const rankingTableEl = document.getElementById('rankingTable');
    const conclusionEl = document.getElementById('conclusion');
    
    if (!rankingTableEl || !conclusionEl) {
        console.error('Elements for step 4 not found');
        return;
    }
    
    // Ranking Table
    let rankingHtml = '<table><thead><tr><th>Ranking</th><th>Alternatif</th><th>Nilai WP</th><th>Persentase</th></tr></thead><tbody>';
    
    const maxWp = calculationData.ranking[0].wp;
    
    calculationData.ranking.forEach(item => {
        const percentage = (item.wp / maxWp) * 100;
        const rankClass = item.rank === 1 ? 'ranking-1' : 
                         (item.rank <= 3 ? `ranking-${item.rank}` : 'ranking-other');
        
        rankingHtml += `<tr>
            <td><span class="ranking-badge ${rankClass}">#${item.rank}</span></td>
            <td><strong>${item.alternatif}</strong></td>
            <td style="color: var(--accent); font-weight: bold;">${item.wp.toFixed(6)}</td>
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="flex: 1; background: var(--border-light); height: 20px; border-radius: 10px; overflow: hidden;">
                        <div style="background: ${item.rank === 1 ? 'var(--accent)' : 'var(--secondary)'}; height: 100%; width: ${percentage}%; transition: width 0.5s;"></div>
                    </div>
                    <span style="font-weight: 600;">${percentage.toFixed(1)}%</span>
                </div>
            </td>
        </tr>`;
    });
    
    rankingHtml += '</tbody></table>';
    rankingTableEl.innerHTML = rankingHtml;
    
    // Conclusion
    const best = calculationData.ranking[0];
    conclusionEl.innerHTML = `
        <p style="font-size: 18px; margin: 0;">
            Alternatif terbaik adalah <strong>${best.alternatif}</strong> 
            dengan nilai WP <strong>${best.wp.toFixed(6)}</strong>
        </p>
    `;
    
    // Chart
    renderChart();
}

// Render Chart
function renderChart() {
    const ctx = document.getElementById('wpChart');
    
    if (!ctx) {
        console.error('Chart canvas not found');
        return;
    }
    
    if (wpChart) {
        wpChart.destroy();
    }
    
    if (typeof Chart === 'undefined') {
        console.error('Chart.js library not loaded');
        return;
    }
    
    const labels = calculationData.ranking.map(item => item.alternatif);
    const data = calculationData.ranking.map(item => item.wp);
    const colors = calculationData.ranking.map(item => 
        item.rank === 1 ? '#FB7A2E' : 
        (item.rank <= 3 ? '#4279B4' : '#6876DF')
    );
    
    wpChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai WP',
                data: data,
                backgroundColor: colors,
                borderColor: colors.map(c => c + 'DD'),
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'WP: ' + context.parsed.y.toFixed(6);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toFixed(4);
                        }
                    }
                }
            }
        }
    });
}

// Show step
function showStep(stepNum) {
    // Hide all steps
    for (let i = 1; i <= 4; i++) {
        const stepEl = document.getElementById(`step${i}`);
        if (stepEl) {
            stepEl.classList.remove('active');
        }
        const stepBtns = document.querySelectorAll('.step-btn');
        if (stepBtns[i - 1]) {
            stepBtns[i - 1].classList.remove('active');
        }
    }
    
    // Show selected step
    const selectedStep = document.getElementById(`step${stepNum}`);
    if (selectedStep) {
        selectedStep.classList.add('active');
        selectedStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    const stepBtns = document.querySelectorAll('.step-btn');
    if (stepBtns[stepNum - 1]) {
        stepBtns[stepNum - 1].classList.add('active');
    }
}

// Load dummy data
function loadDummyData() {
    const kriteriaInput = document.getElementById('kriteriaInput');
    const alternatifInput = document.getElementById('alternatifInput');
    
    if (!kriteriaInput || !alternatifInput) {
        console.error('Input elements not found');
        showError('Elemen input tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    kriteriaInput.value = `Harga|0.4|cost
Kualitas|0.3|benefit
Layanan|0.3|benefit`;
    
    alternatifInput.value = `A|10|80|70
B|20|90|60
C|15|85|65`;
    
    hideError();
    console.log('Dummy data loaded successfully');
}

// Clear data
function clearData() {
    const kriteriaInput = document.getElementById('kriteriaInput');
    const alternatifInput = document.getElementById('alternatifInput');
    
    if (kriteriaInput) kriteriaInput.value = '';
    if (alternatifInput) alternatifInput.value = '';
    
    // Hide all result sections
    for (let i = 1; i <= 4; i++) {
        const stepEl = document.getElementById(`step${i}`);
        if (stepEl) {
            stepEl.classList.remove('active');
        }
    }
    
    const stepIndicator = document.getElementById('stepIndicator');
    if (stepIndicator) {
        stepIndicator.style.display = 'none';
    }
    
    hideError();
    
    if (wpChart) {
        wpChart.destroy();
        wpChart = null;
    }
    
    // Reset calculation data
    calculationData = {
        kriteria: [],
        alternatif: [],
        nilai: [],
        normalized: [],
        wpScores: [],
        ranking: []
    };
}

// Show error
function showError(message) {
    const errorEl = document.getElementById('errorMessage');
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.add('show');
        errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        console.error('Error message element not found:', message);
        alert(message);
    }
}

// Hide error
function hideError() {
    const errorEl = document.getElementById('errorMessage');
    if (errorEl) {
        errorEl.classList.remove('show');
    }
}

// Initialize MathJax config
window.MathJax = {
    tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']]
    }
};

// Debug: Check if page is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Simulasi page loaded');
    console.log('Elements check:');
    console.log('- kriteriaInput:', document.getElementById('kriteriaInput') ? 'Found' : 'NOT FOUND');
    console.log('- alternatifInput:', document.getElementById('alternatifInput') ? 'Found' : 'NOT FOUND');
    console.log('- stepIndicator:', document.getElementById('stepIndicator') ? 'Found' : 'NOT FOUND');
    console.log('- step1:', document.getElementById('step1') ? 'Found' : 'NOT FOUND');
    console.log('- step2:', document.getElementById('step2') ? 'Found' : 'NOT FOUND');
    console.log('- step3:', document.getElementById('step3') ? 'Found' : 'NOT FOUND');
    console.log('- step4:', document.getElementById('step4') ? 'Found' : 'NOT FOUND');
    console.log('- Chart.js:', typeof Chart !== 'undefined' ? 'Loaded' : 'NOT LOADED');
    
    // Make functions globally available
    window.hitungWP = hitungWP;
    window.loadDummyData = loadDummyData;
    window.clearData = clearData;
    window.showStep = showStep;
    
    console.log('Functions registered globally');
});

