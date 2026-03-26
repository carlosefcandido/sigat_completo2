<!-- Eventos Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <div class="d-flex gap-2">
        <button class="btn btn-sm badge-status badge-info" onclick="filterEvt('all')">Todos</button>
        <button class="btn btn-sm badge-status badge-active" onclick="filterEvt('AGENDADO')">Agendados</button>
        <button class="btn btn-sm badge-status badge-purple" onclick="filterEvt('REALIZADO')">Realizados</button>
        <button class="btn btn-sm badge-status badge-danger" onclick="filterEvt('CANCELADO')">Cancelados</button>
    </div>
    <button class="btn btn-sigat" onclick="openEvtModal()"><i class="fas fa-plus me-2"></i>Novo Evento</button>
</div>

<div class="sigat-card p-0 overflow-hidden animate-in">
    <div class="table-responsive">
        <table class="sigat-table">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Local</th>
                    <th>Organizador</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="evtBody">
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted-sigat">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="evtModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-calendar me-2 text-sigat"></i>Novo Evento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="evt_id">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Título *</label><input type="text"
                            class="form-control" id="evt_title"></div>
                    <div class="col-md-6"><label class="form-label">Data *</label><input type="date"
                            class="form-control" id="evt_date"></div>
                    <div class="col-md-6"><label class="form-label">Horário</label><input type="time"
                            class="form-control" id="evt_time"></div>
                    <div class="col-md-6"><label class="form-label">Local</label><input type="text" class="form-control"
                            id="evt_location"></div>
                    <div class="col-md-6"><label class="form-label">Organizador</label><input type="text"
                            class="form-control" id="evt_org"></div>
                    <div class="col-12"><label class="form-label">Descrição</label><textarea class="form-control"
                            id="evt_desc" rows="3"></textarea></div>
                    <div class="col-md-6"><label class="form-label">Status</label><select class="form-select"
                            id="evt_status">
                            <option value="AGENDADO">Agendado</option>
                            <option value="REALIZADO">Realizado</option>
                            <option value="CANCELADO">Cancelado</option>
                        </select></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="saveEvt()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>

<script>
    let allEvts = [];
    const evtModal = new bootstrap.Modal(document.getElementById('evtModal'));
    document.addEventListener('DOMContentLoaded', loadEvts);

    async function loadEvts() { const data = await apiCall('api/events.php'); allEvts = data || []; renderEvts(allEvts); }

    function renderEvts(list) {
        const body = document.getElementById('evtBody');
        if (!list.length) { body.innerHTML = '<tr><td colspan="7"><div class="empty-state py-4"><i class="fas fa-calendar"></i><h5>Nenhum evento</h5></div></td></tr>'; return; }
        const statusBadge = s => s === 'AGENDADO' ? 'badge-info' : s === 'REALIZADO' ? 'badge-active' : 'badge-danger';
        const statusLabel = s => s === 'AGENDADO' ? 'Agendado' : s === 'REALIZADO' ? 'Realizado' : 'Cancelado';
        body.innerHTML = list.map(e => `<tr>
        <td class="fw-medium text-white">${e.title}</td><td>${formatDate(e.date)}</td><td>${e.time || '-'}</td><td>${e.location || '-'}</td><td>${e.organizer || '-'}</td>
        <td><span class="badge badge-status ${statusBadge(e.status)}">${statusLabel(e.status)}</span></td>
        <td><button class="btn-icon btn-icon-edit me-1" onclick='editEvt(${JSON.stringify(e).replace(/'/g, "&apos;")})'><i class="fas fa-edit"></i></button><button class="btn-icon btn-icon-delete" onclick="deleteEvt('${e.id}')"><i class="fas fa-trash"></i></button></td>
    </tr>`).join('');
    }

    function filterEvt(status) { renderEvts(status === 'all' ? allEvts : allEvts.filter(e => e.status === status)); }
    function openEvtModal() { document.getElementById('evt_id').value = '';['evt_title', 'evt_date', 'evt_time', 'evt_location', 'evt_org', 'evt_desc'].forEach(i => document.getElementById(i).value = ''); document.getElementById('evt_status').value = 'AGENDADO'; evtModal.show(); }

    function editEvt(e) {
        document.getElementById('evt_id').value = e.id;
        document.getElementById('evt_title').value = e.title || '';
        document.getElementById('evt_date').value = e.date || '';
        document.getElementById('evt_time').value = e.time || '';
        document.getElementById('evt_location').value = e.location || '';
        document.getElementById('evt_org').value = e.organizer || '';
        document.getElementById('evt_desc').value = e.description || '';
        document.getElementById('evt_status').value = e.status || 'AGENDADO';
        evtModal.show();
    }

    async function saveEvt() {
        const id = document.getElementById('evt_id').value;
        const payload = { title: document.getElementById('evt_title').value, date: document.getElementById('evt_date').value, time: document.getElementById('evt_time').value, location: document.getElementById('evt_location').value, organizer: document.getElementById('evt_org').value, description: document.getElementById('evt_desc').value, status: document.getElementById('evt_status').value };
        if (!payload.title || !payload.date) { alert('Título e data são obrigatórios'); return; }
        const result = id ? await apiCall('api/events.php?id=' + id, 'PUT', payload) : await apiCall('api/events.php', 'POST', payload);
        if (result) { evtModal.hide(); showToast(id ? 'Evento atualizado!' : 'Evento criado!'); loadEvts(); }
    }

    async function deleteEvt(id) { if (!confirmDelete()) return; await apiCall('api/events.php?id=' + id, 'DELETE'); showToast('Evento excluído'); loadEvts(); }
</script>