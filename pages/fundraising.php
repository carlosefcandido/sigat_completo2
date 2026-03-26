<!-- Captação de Recursos Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <h6 class="text-white mb-0"><i class="fas fa-coins me-2 text-sigat"></i>Pipeline de Captação</h6>
    <button class="btn btn-sigat" onclick="openFundModal()"><i class="fas fa-plus me-2"></i>Nova Oportunidade</button>
</div>
<div class="sigat-card p-0 overflow-hidden animate-in">
    <div class="table-responsive">
        <table class="sigat-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Financiador</th>
                    <th>Prazo</th>
                    <th>Valor Total</th>
                    <th>Valor Solicitado</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="fundBody">
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted-sigat">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="fundModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-coins me-2 text-sigat"></i>Nova Oportunidade</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="fd_id">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Título *</label><input type="text"
                            class="form-control" id="fd_title"></div>
                    <div class="col-md-6"><label class="form-label">Financiador</label><input type="text"
                            class="form-control" id="fd_funder"></div>
                    <div class="col-md-6"><label class="form-label">Prazo</label><input type="date" class="form-control"
                            id="fd_deadline"></div>
                    <div class="col-md-6"><label class="form-label">Valor Total</label><input type="number"
                            class="form-control" id="fd_total" step="0.01"></div>
                    <div class="col-md-6"><label class="form-label">Valor Solicitado</label><input type="number"
                            class="form-control" id="fd_req" step="0.01"></div>
                    <div class="col-md-6"><label class="form-label">Status</label><select class="form-select"
                            id="fd_status">
                            <option>Identificado</option>
                            <option>Preparando</option>
                            <option>Enviado</option>
                            <option>Aprovado</option>
                            <option>Rejeitado</option>
                            <option>Expirado</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Link</label><input type="url" class="form-control"
                            id="fd_link"></div>
                    <div class="col-12"><label class="form-label">Descrição</label><textarea class="form-control"
                            id="fd_desc" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="saveFund()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>
<script>
    let allFund = []; const fundModal = new bootstrap.Modal(document.getElementById('fundModal'));
    document.addEventListener('DOMContentLoaded', loadFund);
    async function loadFund() { const d = await apiCall('api/fundraising.php'); allFund = d || []; renderFund(); }
    function renderFund() {
        const b = document.getElementById('fundBody'); if (!allFund.length) { b.innerHTML = '<tr><td colspan="7"><div class="empty-state py-4"><i class="fas fa-coins"></i><h5>Nenhuma oportunidade</h5></div></td></tr>'; return; }
        const sc = { 'Identificado': 'badge-slate', 'Preparando': 'badge-warning', 'Enviado': 'badge-info', 'Aprovado': 'badge-active', 'Rejeitado': 'badge-danger', 'Expirado': 'badge-danger' };
        b.innerHTML = allFund.map(f => `<tr><td class="fw-medium text-white">${f.title}</td><td>${f.funder || '-'}</td><td>${formatDate(f.deadline)}</td><td>${formatCurrency(f.total_value)}</td><td>${formatCurrency(f.requested_value)}</td><td><span class="badge badge-status ${sc[f.status] || 'badge-slate'}">${f.status}</span></td><td><button class="btn-icon btn-icon-edit me-1" onclick='editFund(${JSON.stringify(f).replace(/\x27/g, "&apos;")})'><i class="fas fa-edit"></i></button><button class="btn-icon btn-icon-delete" onclick="delFund('${f.id}')"><i class="fas fa-trash"></i></button></td></tr>`).join('');
    }
    function openFundModal() { document.getElementById('fd_id').value = '';['fd_title', 'fd_funder', 'fd_deadline', 'fd_total', 'fd_req', 'fd_link', 'fd_desc'].forEach(i => document.getElementById(i).value = ''); document.getElementById('fd_status').value = 'Identificado'; fundModal.show(); }
    function editFund(f) { document.getElementById('fd_id').value = f.id; document.getElementById('fd_title').value = f.title || ''; document.getElementById('fd_funder').value = f.funder || ''; document.getElementById('fd_deadline').value = f.deadline || ''; document.getElementById('fd_total').value = f.total_value || 0; document.getElementById('fd_req').value = f.requested_value || 0; document.getElementById('fd_status').value = f.status || 'Identificado'; document.getElementById('fd_link').value = f.link || ''; document.getElementById('fd_desc').value = f.description || ''; fundModal.show(); }
    async function saveFund() { const id = document.getElementById('fd_id').value; const p = { title: document.getElementById('fd_title').value, funder: document.getElementById('fd_funder').value, deadline: document.getElementById('fd_deadline').value, total_value: document.getElementById('fd_total').value || 0, requested_value: document.getElementById('fd_req').value || 0, status: document.getElementById('fd_status').value, link: document.getElementById('fd_link').value, description: document.getElementById('fd_desc').value }; if (!p.title) { alert('Título obrigatório'); return; } const r = id ? await apiCall('api/fundraising.php?id=' + id, 'PUT', p) : await apiCall('api/fundraising.php', 'POST', p); if (r) { fundModal.hide(); showToast(id ? 'Atualizado!' : 'Criado!'); loadFund(); } }
    async function delFund(id) { if (!confirmDelete()) return; await apiCall('api/fundraising.php?id=' + id, 'DELETE'); showToast('Removido'); loadFund(); }
</script>