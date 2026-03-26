<!-- Quadro de Atividades Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <h6 class="text-white mb-0"><i class="fas fa-calendar-days me-2 text-sigat"></i>Quadro Semanal</h6>
    <button class="btn btn-sigat" onclick="openActModal()"><i class="fas fa-plus me-2"></i>Nova Atividade</button>
</div>

<div class="row g-3 animate-in" id="weekBoard"></div>

<!-- Modal -->
<div class="modal fade" id="actModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-calendar-plus me-2 text-sigat"></i>Nova Atividade
                </h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="act_id">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Nome *</label><input type="text" class="form-control"
                            id="act_name"></div>
                    <div class="col-md-6"><label class="form-label">Professor</label><input type="text"
                            class="form-control" id="act_teacher"></div>
                    <div class="col-md-6"><label class="form-label">Dia da Semana</label><select class="form-select"
                            id="act_day">
                            <option value="1">Segunda</option>
                            <option value="2">Terça</option>
                            <option value="3">Quarta</option>
                            <option value="4">Quinta</option>
                            <option value="5">Sexta</option>
                            <option value="6">Sábado</option>
                            <option value="0">Domingo</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Início</label><input type="time"
                            class="form-control" id="act_start"></div>
                    <div class="col-md-6"><label class="form-label">Fim</label><input type="time" class="form-control"
                            id="act_end"></div>
                    <div class="col-12"><label class="form-label">Local</label><input type="text" class="form-control"
                            id="act_location"></div>
                    <div class="col-12"><label class="form-label">Descrição</label><textarea class="form-control"
                            id="act_desc" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="saveAct()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>

<script>
    const dayNames = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
    const dayColors = ['#f87171', '#60a5fa', '#4ade80', '#fbbf24', '#c084fc', '#2dd4bf', '#fb923c'];
    let allActs = [];
    const actModal = new bootstrap.Modal(document.getElementById('actModal'));

    document.addEventListener('DOMContentLoaded', loadActs);

    async function loadActs() {
        const data = await apiCall('api/activities.php');
        allActs = data || [];
        renderBoard();
    }

    function renderBoard() {
        const board = document.getElementById('weekBoard');
        board.innerHTML = [1, 2, 3, 4, 5, 6, 0].map(day => {
            const acts = allActs.filter(a => parseInt(a.day_of_week) === day);
            return `<div class="col-md-6 col-xl-3" style="${day === 6 || day === 0 ? '' : ''}">
            <div class="sigat-card p-3 h-100">
                <h6 class="fw-bold mb-3" style="color:${dayColors[day]};font-size:14px;"><i class="fas fa-circle me-2" style="font-size:8px;"></i>${dayNames[day]}</h6>
                ${acts.length ? acts.map(a => `
                    <div class="rounded-3 p-3 mb-2" style="background:rgba(255,255,255,0.03);border:1px solid var(--sigat-border);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="fw-medium text-white" style="font-size:13px;">${a.name}</div>
                            <button class="btn-icon btn-icon-delete" style="width:28px;height:28px;font-size:12px;" onclick="deleteAct('${a.id}')"><i class="fas fa-times"></i></button>
                        </div>
                        <small class="text-muted-sigat d-block mt-1"><i class="fas fa-clock me-1"></i>${a.start_time || ''} - ${a.end_time || ''}</small>
                        ${a.teacher ? `<small class="text-muted-sigat d-block"><i class="fas fa-user me-1"></i>${a.teacher}</small>` : ''}
                        ${a.location ? `<small class="text-muted-sigat d-block"><i class="fas fa-map-marker-alt me-1"></i>${a.location}</small>` : ''}
                    </div>
                `).join('') : '<p class="text-muted-sigat text-center" style="font-size:12px;">Sem atividades</p>'}
            </div>
        </div>`;
        }).join('');
    }

    function openActModal() { document.getElementById('act_id').value = '';['act_name', 'act_teacher', 'act_start', 'act_end', 'act_location', 'act_desc'].forEach(i => document.getElementById(i).value = ''); actModal.show(); }

    async function saveAct() {
        const payload = { name: document.getElementById('act_name').value, teacher: document.getElementById('act_teacher').value, day_of_week: parseInt(document.getElementById('act_day').value), start_time: document.getElementById('act_start').value, end_time: document.getElementById('act_end').value, location: document.getElementById('act_location').value, description: document.getElementById('act_desc').value };
        if (!payload.name) { alert('Nome é obrigatório'); return; }
        const result = await apiCall('api/activities.php', 'POST', payload);
        if (result) { actModal.hide(); showToast('Atividade criada!'); loadActs(); }
    }

    async function deleteAct(id) { if (!confirmDelete()) return; await apiCall('api/activities.php?id=' + id, 'DELETE'); showToast('Atividade removida'); loadActs(); }
</script>