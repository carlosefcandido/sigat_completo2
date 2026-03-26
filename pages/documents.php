<!-- Documentos Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <div class="position-relative flex-grow-1" style="max-width:400px;">
        <i class="fas fa-search position-absolute"
            style="left:14px;top:50%;transform:translateY(-50%);color:var(--sigat-text-muted);"></i>
        <input type="text" class="sigat-search" id="searchDoc" placeholder="Buscar documento..." oninput="filterDocs()">
    </div>
    <div class="d-flex gap-2">
        <select class="form-select form-select-sm" id="filterCat"
            style="background:var(--sigat-surface);border-color:var(--sigat-border);color:var(--sigat-text);border-radius:10px;width:auto;"
            onchange="filterDocs()">
            <option value="">Todas categorias</option>
            <option value="Jurídico">Jurídico</option>
            <option value="Certidões">Certidões</option>
            <option value="Financeiro">Financeiro</option>
            <option value="Projetos e Editais">Projetos e Editais</option>
            <option value="RH e Voluntariado">RH e Voluntariado</option>
            <option value="Administração Interna">Administração Interna</option>
            <option value="Documentos de Beneficiários">Documentos de Beneficiários</option>
        </select>
        <button class="btn btn-sigat" onclick="openDocModal()"><i class="fas fa-plus me-2"></i>Novo Documento</button>
    </div>
</div>

<div class="sigat-card p-0 overflow-hidden animate-in">
    <div class="table-responsive">
        <table class="sigat-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Categoria</th>
                    <th>Emissão</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="docBody">
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted-sigat">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="docModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-file-lines me-2 text-sigat"></i>Novo Documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Título *</label><input type="text"
                            class="form-control" id="doc_title"></div>
                    <div class="col-md-6"><label class="form-label">Categoria</label><select class="form-select"
                            id="doc_cat">
                            <option value="Jurídico">Jurídico</option>
                            <option value="Certidões">Certidões</option>
                            <option value="Financeiro">Financeiro</option>
                            <option value="Projetos e Editais">Projetos e Editais</option>
                            <option value="RH e Voluntariado">RH e Voluntariado</option>
                            <option value="Administração Interna">Administração Interna</option>
                            <option value="Documentos de Beneficiários">Documentos de Beneficiários</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Tipo</label><select class="form-select"
                            id="doc_type">
                            <option value="pdf">PDF</option>
                            <option value="image">Imagem</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Data de Emissão</label><input type="date"
                            class="form-control" id="doc_issue"></div>
                    <div class="col-md-6"><label class="form-label">Data de Vencimento</label><input type="date"
                            class="form-control" id="doc_expiry"></div>
                    <div class="col-12"><label class="form-label">Observações</label><textarea class="form-control"
                            id="doc_obs" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="saveDoc()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>

<script>
    let allDocs = [];
    const docModal = new bootstrap.Modal(document.getElementById('docModal'));
    document.addEventListener('DOMContentLoaded', loadDocs);

    async function loadDocs() { const data = await apiCall('api/documents.php'); allDocs = data || []; renderDocs(allDocs); }

    function renderDocs(list) {
        const body = document.getElementById('docBody');
        if (!list.length) { body.innerHTML = '<tr><td colspan="6"><div class="empty-state py-4"><i class="fas fa-file-lines"></i><h5>Nenhum documento</h5></div></td></tr>'; return; }
        const statusBadge = s => s === 'Ativo e Regular' ? 'badge-active' : s === 'Próximo do Vencimento' ? 'badge-warning' : 'badge-danger';
        body.innerHTML = list.map(d => `<tr>
        <td class="fw-medium text-white">${d.title}</td>
        <td><span class="badge badge-status badge-purple">${d.category}</span></td>
        <td>${formatDate(d.issue_date)}</td><td>${formatDate(d.expiry_date)}</td>
        <td><span class="badge badge-status ${statusBadge(d.status)}">${d.status}</span></td>
        <td><button class="btn-icon btn-icon-delete" onclick="deleteDoc('${d.id}')"><i class="fas fa-trash"></i></button></td>
    </tr>`).join('');
    }

    function filterDocs() {
        const q = document.getElementById('searchDoc').value.toLowerCase();
        const cat = document.getElementById('filterCat').value;
        renderDocs(allDocs.filter(d => (!q || d.title.toLowerCase().includes(q)) && (!cat || d.category === cat)));
    }

    function openDocModal() { docModal.show(); }

    async function saveDoc() {
        const payload = { title: document.getElementById('doc_title').value, category: document.getElementById('doc_cat').value, file_type: document.getElementById('doc_type').value, issue_date: document.getElementById('doc_issue').value, expiry_date: document.getElementById('doc_expiry').value, observations: document.getElementById('doc_obs').value };
        if (!payload.title) { alert('Título é obrigatório'); return; }
        const result = await apiCall('api/documents.php', 'POST', payload);
        if (result) { docModal.hide(); showToast('Documento cadastrado!'); loadDocs(); }
    }

    async function deleteDoc(id) { if (!confirmDelete()) return; await apiCall('api/documents.php?id=' + id, 'PUT', { is_deleted: 1 }); showToast('Documento movido para lixeira'); loadDocs(); }
</script>