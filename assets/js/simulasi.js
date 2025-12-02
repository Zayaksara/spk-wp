// Simulasi WP Calculation - Client-side interactive calculation
let calculationData = {
    kriteria: [],
    alternatif: [],
    nilai: [],
    normalizedBobot: [],
    signedBobot: [],
    sVector: [],
    vVector: [],
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
    
    // Validate total bobot (tidak perlu strict = 1, akan dinormalisasi)
    const totalBobot = calculationData.kriteria.reduce((sum, k) => sum + k.bobot, 0);
    if (totalBobot <= 0) {
        showError(`Total bobot harus lebih besar dari 0! Total saat ini: ${totalBobot.toFixed(4)}`);
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
        
        // Step 1: Normalize bobot
        console.log('Normalizing bobot...');
        normalizeBobot();
        console.log('Normalized Bobot:', calculationData.normalizedBobot);
        console.log('Signed Bobot:', calculationData.signedBobot);
        
        // Step 2: Calculate S Vector (langsung dari nilai asli)
        console.log('Calculating S Vector...');
        calculateSVector();
        console.log('S Vector:', calculationData.sVector);
        
        // Step 3: Calculate V Vector
        console.log('Calculating V Vector...');
        calculateVVector();
        console.log('V Vector:', calculationData.vVector);
        
        // Step 4: Rank alternatives
        console.log('Ranking alternatives...');
        rankAlternatives();
        console.log('Ranking:', calculationData.ranking);
        
        // Render all steps
        console.log('Rendering steps...');
        renderStep1();
        renderStep2();
        renderStep3();
        renderStep4();
        renderStep5();
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

// Normalize bobot
function normalizeBobot() {
    const totalBobot = calculationData.kriteria.reduce((sum, k) => sum + k.bobot, 0);
    calculationData.normalizedBobot = [];
    calculationData.signedBobot = [];
    
    for (let i = 0; i < calculationData.kriteria.length; i++) {
        const kriteria = calculationData.kriteria[i];
        let normalized;
        
        if (Math.abs(totalBobot - 1.0) > 0.0001) {
            // Normalisasi jika total != 1
            normalized = kriteria.bobot / totalBobot;
        } else {
            // Sudah ternormalisasi (total = 1)
            normalized = kriteria.bobot;
        }
        
        calculationData.normalizedBobot.push(normalized);
        
        // Tentukan bobot bertanda (benefit = +, cost = -)
        if (kriteria.tipe === 'benefit') {
            calculationData.signedBobot.push(+normalized); // Positif
        } else {
            calculationData.signedBobot.push(-normalized); // Negatif
        }
    }
}

// Calculate S Vector (langsung dari nilai asli, tanpa normalisasi min/max)
function calculateSVector() {
    calculationData.sVector = [];
    
    for (let altIndex = 0; altIndex < calculationData.alternatif.length; altIndex++) {
        let s = 1;
        const steps = [];
        
        for (let kriIndex = 0; kriIndex < calculationData.kriteria.length; kriIndex++) {
            const nilai = calculationData.nilai[altIndex][kriIndex];
            const wj = calculationData.signedBobot[kriIndex]; // Bobot bertanda
            // Langsung pakai nilai asli, tidak perlu normalisasi min/max
            let powered = 0;
            if (nilai > 0) {
                powered = Math.pow(nilai, wj);
                s *= powered;
            } else {
                s = 0; // Jika nilai 0 atau negatif, hasilnya 0
            }
            
            steps.push({
                kriteria: calculationData.kriteria[kriIndex].nama,
                nilai_asli: nilai,
                bobot_original: calculationData.kriteria[kriIndex].bobot,
                bobot_normalized: calculationData.normalizedBobot[kriIndex],
                bobot_signed: wj,
                powered: powered
            });
        }
        
        calculationData.sVector.push({
            alternatif: calculationData.alternatif[altIndex],
            nilai_s: s,
            steps: steps
        });
    }
}

// Calculate V Vector (normalisasi S vector)
function calculateVVector() {
    const totalS = calculationData.sVector.reduce((sum, item) => sum + item.nilai_s, 0);
    calculationData.vVector = [];
    
    for (let i = 0; i < calculationData.sVector.length; i++) {
        const sItem = calculationData.sVector[i];
        const v = totalS > 0 ? sItem.nilai_s / totalS : 0;
        
        calculationData.vVector.push({
            alternatif: sItem.alternatif,
            nilai_s: sItem.nilai_s,
            nilai_v: v,
            steps: sItem.steps
        });
    }
}

// Rank alternatives
function rankAlternatives() {
    // Sort by V Vector descending
    calculationData.ranking = [...calculationData.vVector].sort((a, b) => b.nilai_v - a.nilai_v);
    
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
    
    const totalBobot = calculationData.kriteria.reduce((sum, k) => sum + k.bobot, 0);
    let kriteriaHtml = '<table style="width: 100%; color: white;"><thead><tr><th>Kriteria</th><th>Bobot Awal</th><th>Tipe</th></tr></thead><tbody>';
    calculationData.kriteria.forEach(k => {
        kriteriaHtml += `<tr>
            <td><strong>${k.nama}</strong></td>
            <td>${k.bobot.toFixed(4)}</td>
            <td><span style="padding: 4px 10px; background: rgba(255,255,255,0.2); border-radius: 4px;">${k.tipe}</span></td>
        </tr>`;
    });
    kriteriaHtml += `<tr style="background: rgba(255,255,255,0.1);"><td><strong>Total</strong></td><td><strong>${totalBobot.toFixed(4)}</strong></td><td></td></tr>`;
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

// Render Step 2: Normalisasi Bobot
function renderStep2() {
    const normalisasiBobotEl = document.getElementById('normalisasiBobot');
    if (!normalisasiBobotEl) {
        console.error('Element normalisasiBobot not found');
        return;
    }
    
    const totalBobot = calculationData.kriteria.reduce((sum, k) => sum + k.bobot, 0);
    const needsNormalization = Math.abs(totalBobot - 1.0) > 0.0001;
    
    let html = '<p style="font-size: 16px; margin: 10px 0; color: var(--text-secondary);">';
    if (needsNormalization) {
        html += `Total bobot = ${totalBobot.toFixed(4)} ≠ 1, maka dilakukan normalisasi:<br>`;
        html += '<strong>Wj_normalized = Wj / Σ(Wj)</strong><br><br>';
    } else {
        html += `Total bobot = ${totalBobot.toFixed(4)} = 1, bobot sudah ternormalisasi.<br><br>`;
    }
    html += '</p>';
    
    html += '<table><thead><tr><th>Kriteria</th><th>Bobot Awal</th><th>Bobot Normalisasi</th><th>Tipe</th><th>Bobot Bertanda</th></tr></thead><tbody>';
    calculationData.kriteria.forEach((k, index) => {
        const signed = calculationData.signedBobot[index];
        const sign = signed >= 0 ? '+' : '';
        html += `<tr>
            <td><strong>${k.nama}</strong></td>
            <td>${k.bobot.toFixed(4)}</td>
            <td>${calculationData.normalizedBobot[index].toFixed(4)}</td>
            <td><span style="padding: 4px 10px; background: rgba(66, 121, 180, 0.2); border-radius: 4px;">${k.tipe}</span></td>
            <td style="color: ${signed >= 0 ? 'var(--accent)' : '#e74c3c'}; font-weight: bold; font-size: 16px;">${sign}${signed.toFixed(4)}</td>
        </tr>`;
    });
    html += '</tbody></table>';
    
    normalisasiBobotEl.innerHTML = html;
}

// Render Step 3: Perhitungan S Vector (langsung dari nilai asli)
function renderStep3() {
    const sVectorFormulaEl = document.getElementById('sVectorFormula');
    const sVectorTableEl = document.getElementById('sVectorTable');
    
    if (!sVectorFormulaEl || !sVectorTableEl) {
        console.error('Elements for step 3 not found');
        return;
    }
    
    // Formula
    let formulaHtml = '<p style="font-size: 16px; margin: 10px 0; line-height: 1.8;">';
    formulaHtml += '<strong style="color: var(--primary);">Rumus S Vector:</strong><br>';
    formulaHtml += 'Si = ∏(Nilai Asli^Wj_final) untuk setiap kriteria<br>';
    formulaHtml += 'Dimana:<br>';
    formulaHtml += '• Wj_final adalah bobot bertanda (benefit = +, cost = -)<br>';
    formulaHtml += '• ∏ adalah perkalian semua kriteria<br>';
    formulaHtml += '• <strong style="color: var(--accent);">Tidak ada normalisasi min/max</strong>, langsung pakai nilai asli<br><br>';
    formulaHtml += '<strong style="color: var(--accent);">Catatan:</strong><br>';
    formulaHtml += '• Cost dengan bobot negatif akan membuat nilai yang lebih kecil menjadi lebih baik<br>';
    formulaHtml += '• Benefit dengan bobot positif akan membuat nilai yang lebih besar menjadi lebih baik</p>';
    sVectorFormulaEl.innerHTML = formulaHtml;
    
    // S Vector Calculation Table - Detail untuk setiap alternatif
    let sHtml = '';
    
    if (!calculationData.sVector || calculationData.sVector.length === 0) {
        sVectorTableEl.innerHTML = '<p style="color: red;">Data S Vector tidak ditemukan. Silakan hitung ulang.</p>';
        return;
    }
    
    calculationData.sVector.forEach(item => {
        if (!item.steps || item.steps.length === 0) {
            console.error('Item steps is empty for:', item.alternatif);
            return;
        }
        
        sHtml += `<div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">`;
        sHtml += `<h4 style="color: var(--primary); margin-bottom: 15px;">🎯 Alternatif: <strong>${item.alternatif}</strong></h4>`;
        
        // Formula perhitungan
        let formula = '';
        let calculation = '';
        
        item.steps.forEach((step, index) => {
            if (!step || !step.hasOwnProperty('bobot_signed')) {
                console.error('Step data incomplete:', step);
                return;
            }
            const sign = step.bobot_signed >= 0 ? '+' : '';
            const part = `(${step.nilai_asli.toFixed(4)}^${sign}${step.bobot_signed.toFixed(4)})`;
            formula += part;
            if (index < item.steps.length - 1) formula += ' × ';
            
            calculation += step.powered.toFixed(6);
            if (index < item.steps.length - 1) calculation += ' × ';
        });
        
        sHtml += `<div style="background: var(--bg-gray); padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--accent);">`;
        sHtml += `<div style="font-family: monospace; font-size: 14px; margin-bottom: 8px;">`;
        sHtml += `<strong style="color: var(--primary);">S_${item.alternatif} =</strong> ${formula}`;
        sHtml += `</div>`;
        sHtml += `<div style="font-family: monospace; font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">`;
        sHtml += `<strong style="color: var(--primary);">S_${item.alternatif} =</strong> ${calculation}`;
        sHtml += `</div>`;
        sHtml += `<div style="font-size: 18px; font-weight: bold; color: var(--accent);">`;
        sHtml += `<strong>S_${item.alternatif} = ${item.nilai_s.toFixed(6)}</strong>`;
        sHtml += `</div>`;
        sHtml += `</div>`;
        
        // Tabel detail per kriteria
        sHtml += `<table style="width: 100%; margin-top: 15px;"><thead><tr>`;
        sHtml += `<th style="background: var(--secondary); color: white; padding: 10px; text-align: left;">Kriteria</th>`;
        sHtml += `<th style="background: var(--secondary); color: white; padding: 10px; text-align: left;">Nilai Asli</th>`;
        sHtml += `<th style="background: var(--secondary); color: white; padding: 10px; text-align: left;">Bobot Bertanda</th>`;
        sHtml += `<th style="background: var(--secondary); color: white; padding: 10px; text-align: left;">Nilai^Bobot</th>`;
        sHtml += `</tr></thead><tbody>`;
        
        item.steps.forEach(step => {
            const sign = step.bobot_signed >= 0 ? '+' : '';
            const color = step.bobot_signed >= 0 ? 'var(--accent)' : '#e74c3c';
            sHtml += `<tr style="border-bottom: 1px solid var(--border-light);">`;
            sHtml += `<td style="padding: 10px;"><strong>${step.kriteria}</strong></td>`;
            sHtml += `<td style="padding: 10px;">${step.nilai_asli.toFixed(4)}</td>`;
            sHtml += `<td style="padding: 10px; color: ${color}; font-weight: bold;">${sign}${step.bobot_signed.toFixed(4)}</td>`;
            sHtml += `<td style="padding: 10px; color: var(--accent); font-weight: bold;">${step.powered.toFixed(6)}</td>`;
            sHtml += `</tr>`;
        });
        
        sHtml += `</tbody></table>`;
        sHtml += `</div>`;
    });
    
    sVectorTableEl.innerHTML = sHtml;
    
    // Trigger MathJax render
    if (window.MathJax && window.MathJax.typesetPromise) {
        MathJax.typesetPromise();
    }
}

// Render Step 4: Perhitungan V Vector
function renderStep4() {
    const sVectorFormulaEl = document.getElementById('sVectorFormula');
    const sVectorTableEl = document.getElementById('sVectorTable');
    
    if (!sVectorFormulaEl || !sVectorTableEl) {
        console.error('Elements for step 4 not found');
        console.error('sVectorFormulaEl:', sVectorFormulaEl);
        console.error('sVectorTableEl:', sVectorTableEl);
        return;
    }
    
    // Check if data exists
    if (!calculationData.sVector || calculationData.sVector.length === 0) {
        console.error('S Vector data is empty');
        sVectorFormulaEl.innerHTML = '<p style="color: red;">Data S Vector tidak ditemukan. Silakan hitung ulang.</p>';
        sVectorTableEl.innerHTML = '<p style="color: red;">Data tidak tersedia.</p>';
        return;
    }
    
    // Formula
    let formulaHtml = '<p style="font-size: 16px; margin: 10px 0; line-height: 1.8;">';
    formulaHtml += '<strong style="color: var(--primary);">Rumus S Vector:</strong><br>';
    formulaHtml += 'Si = ∏(Nilai Asli^Wj_final) untuk setiap kriteria<br>';
    formulaHtml += 'Dimana:<br>';
    formulaHtml += '• Wj_final adalah bobot bertanda (benefit = +, cost = -)<br>';
    formulaHtml += '• ∏ adalah perkalian semua kriteria<br><br>';
    formulaHtml += '<strong style="color: var(--accent);">Catatan:</strong><br>';
    formulaHtml += '• Cost dengan bobot negatif akan membuat nilai yang lebih kecil menjadi lebih baik<br>';
    formulaHtml += '• Benefit dengan bobot positif akan membuat nilai yang lebih besar menjadi lebih baik</p>';
    sVectorFormulaEl.innerHTML = formulaHtml;
    
    // S Vector Calculation Table - Detail untuk setiap alternatif
    let sHtml = '';
    
    calculationData.sVector.forEach(item => {
        if (!item.steps || item.steps.length === 0) {
            console.error('Item steps is empty for:', item.alternatif);
            return;
        }
        
        sHtml += `<div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">`;
        sHtml += `<h4 style="color: var(--primary); margin-bottom: 15px;">🎯 Alternatif: <strong>${item.alternatif}</strong></h4>`;
        
        // Formula perhitungan
        let formula = '';
        let calculation = '';
        
        item.steps.forEach((step, index) => {
            if (!step || !step.hasOwnProperty('bobot_signed')) {
                console.error('Step data incomplete:', step);
                return;
            }
            const sign = step.bobot_signed >= 0 ? '+' : '';
            const part = `(${step.nilai_asli.toFixed(4)}^${sign}${step.bobot_signed.toFixed(4)})`;
            formula += part;
            if (index < item.steps.length - 1) formula += ' × ';
            
            calculation += step.powered.toFixed(6);
            if (index < item.steps.length - 1) calculation += ' × ';
        });
        
        sHtml += `<div style="background: var(--bg-gray); padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid var(--accent);">`;
        sHtml += `<div style="font-family: monospace; font-size: 14px; margin-bottom: 8px;">`;
        sHtml += `<strong style="color: var(--primary);">S_${item.alternatif} =</strong> ${formula}`;
        sHtml += `</div>`;
        sHtml += `<div style="font-family: monospace; font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">`;
        sHtml += `<strong style="color: var(--primary);">S_${item.alternatif} =</strong> ${calculation}`;
        sHtml += `</div>`;
        sHtml += `<div style="font-size: 18px; font-weight: bold; color: var(--accent);">`;
        sHtml += `<strong>S_${item.alternatif} = ${item.nilai_s.toFixed(6)}</strong>`;
        sHtml += `</div>`;
        sHtml += `</div>`;
        
        // Tabel detail per kriteria
        sHtml += `<table style="width: 100%; margin-top: 15px;"><thead><tr>`;
        sHtml += `<th style="background: var(--secondary); color: white; padding: 10px; text-align: left;">Kriteria</th>`;
        sHtml += `<th style="background: var(--secondary); color: white; padding: 10px; text-align: left;">Nilai Asli</th>`;
        sHtml += `<th style="background: var(--secondary); color: white; padding: 10px; text-align: left;">Bobot Bertanda</th>`;
        sHtml += `<th style="background: var(--secondary); color: white; padding: 10px; text-align: left;">Nilai^Bobot</th>`;
        sHtml += `</tr></thead><tbody>`;
        
        item.steps.forEach(step => {
            const sign = step.bobot_signed >= 0 ? '+' : '';
            const color = step.bobot_signed >= 0 ? 'var(--accent)' : '#e74c3c';
            sHtml += `<tr style="border-bottom: 1px solid var(--border-light);">`;
            sHtml += `<td style="padding: 10px;"><strong>${step.kriteria}</strong></td>`;
            sHtml += `<td style="padding: 10px;">${step.nilai_asli.toFixed(4)}</td>`;
            sHtml += `<td style="padding: 10px; color: ${color}; font-weight: bold;">${sign}${step.bobot_signed.toFixed(4)}</td>`;
            sHtml += `<td style="padding: 10px; color: var(--accent); font-weight: bold;">${step.powered.toFixed(6)}</td>`;
            sHtml += `</tr>`;
        });
        
        sHtml += `</tbody></table>`;
        sHtml += `</div>`;
    });
    
    sVectorTableEl.innerHTML = sHtml;
    
    // Trigger MathJax render
    if (window.MathJax && window.MathJax.typesetPromise) {
        MathJax.typesetPromise();
    }
}

// Render Step 5: Ranking
function renderStep5() {
    const rankingTableEl = document.getElementById('rankingTable');
    const conclusionEl = document.getElementById('conclusion');
    
    if (!rankingTableEl || !conclusionEl) {
        console.error('Elements for step 6 not found');
        return;
    }
    
    // Ranking Table
    let rankingHtml = '<table><thead><tr><th>Ranking</th><th>Alternatif</th><th>Nilai V</th><th>Persentase</th></tr></thead><tbody>';
    
    const maxV = calculationData.ranking[0].nilai_v;
    
    calculationData.ranking.forEach(item => {
        const percentage = (item.nilai_v / maxV) * 100;
        const rankClass = item.rank === 1 ? 'ranking-1' : 
                         (item.rank <= 3 ? `ranking-${item.rank}` : 'ranking-other');
        
        rankingHtml += `<tr>
            <td><span class="ranking-badge ${rankClass}">#${item.rank}</span></td>
            <td><strong>${item.alternatif}</strong></td>
            <td style="color: var(--accent); font-weight: bold;">${item.nilai_v.toFixed(6)}</td>
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
            dengan nilai V <strong>${best.nilai_v.toFixed(6)}</strong>
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
    const data = calculationData.ranking.map(item => item.nilai_v);
    const colors = calculationData.ranking.map(item => 
        item.rank === 1 ? '#FB7A2E' : 
        (item.rank <= 3 ? '#4279B4' : '#6876DF')
    );
    
    wpChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai V',
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
                            return 'V: ' + context.parsed.y.toFixed(6);
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
    for (let i = 1; i <= 5; i++) {
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
    for (let i = 1; i <= 5; i++) {
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
        normalizedBobot: [],
        signedBobot: [],
        sVector: [],
        vVector: [],
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

