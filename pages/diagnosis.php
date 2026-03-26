<!-- Diagnóstico (SWOT) Page -->
<div class="mb-4 animate-in">
    <h6 class="text-white fw-bold mb-3"><i class="fas fa-chart-line me-2 text-sigat"></i>Análise SWOT por Projeto</h6>
    <select class="form-select" id="diagProject"
        style="background:var(--sigat-surface);border-color:var(--sigat-border);color:var(--sigat-text);border-radius:10px;max-width:400px;"
        onchange="loadDiagnosis()">
        <option value="">Selecione um projeto...</option>
    </select>
</div>
<div id="swotGrid" class="d-none animate-in sigat-form">
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="swot-card swot-strengths">
                <h6 class="fw-bold mb-2" style="color:#4ade80;"><i class="fas fa-arrow-up me-2"></i>Forças</h6><textarea
                    class="form-control" id="sw_s" rows="4"
                    style="background:transparent;border-color:rgba(34,197,94,0.2);color:#e2e8f0;"></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="swot-card swot-weaknesses">
                <h6 class="fw-bold mb-2" style="color:#f87171;"><i class="fas fa-arrow-down me-2"></i>Fraquezas</h6>
                <textarea class="form-control" id="sw_w" rows="4"
                    style="background:transparent;border-color:rgba(239,68,68,0.2);color:#e2e8f0;"></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="swot-card swot-opportunities">
                <h6 class="fw-bold mb-2" style="color:#60a5fa;"><i class="fas fa-lightbulb me-2"></i>Oportunidades</h6>
                <textarea class="form-control" id="sw_o" rows="4"
                    style="background:transparent;border-color:rgba(59,130,246,0.2);color:#e2e8f0;"></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="swot-card swot-threats">
                <h6 class="fw-bold mb-2" style="color:#fbbf24;"><i class="fas fa-exclamation-triangle me-2"></i>Ameaças
                </h6><textarea class="form-control" id="sw_t" rows="4"
                    style="background:transparent;border-color:rgba(245,158,11,0.2);color:#e2e8f0;"></textarea>
            </div>
        </div>
    </div>
    <button class="btn btn-sigat" onclick="saveDiag()"><i class="fas fa-save me-2"></i>Salvar</button>
</div>
<script>
    let allDiag = [];
    document.addEventListener('DOMContentLoaded', async () => {
        const [p, d] = await Promise.all([apiCall('api/projects.php'), apiCall('api/diagnosis.php')]);
        allDiag = d || []; const sel = document.getElementById('diagProject');
        (p || []).forEach(x => sel.innerHTML += `<option value="${x.id}">${x.name}</option>`);
    });
    function loadDiagnosis() { const pid = document.getElementById('diagProject').value; if (!pid) { document.getElementById('swotGrid').classList.add('d-none'); return; } document.getElementById('swotGrid').classList.remove('d-none'); const d = allDiag.find(x => x.project_id === pid); document.getElementById('sw_s').value = d ? d.strengths || '' : ''; document.getElementById('sw_w').value = d ? d.weaknesses || '' : ''; document.getElementById('sw_o').value = d ? d.opportunities || '' : ''; document.getElementById('sw_t').value = d ? d.threats || '' : ''; }
    async function saveDiag() { const pid = document.getElementById('diagProject').value; if (!pid) return; const r = await apiCall('api/diagnosis.php', 'POST', { project_id: pid, strengths: document.getElementById('sw_s').value, weaknesses: document.getElementById('sw_w').value, opportunities: document.getElementById('sw_o').value, threats: document.getElementById('sw_t').value }); if (r) { showToast('Diagnóstico salvo!'); allDiag = allDiag.filter(d => d.project_id !== pid); allDiag.push(r); } }
</script>