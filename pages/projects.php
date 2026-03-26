<!-- Projetos Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <div class="position-relative flex-grow-1" style="max-width:400px;">
        <i class="fas fa-search position-absolute"
            style="left:14px;top:50%;transform:translateY(-50%);color:var(--sigat-text-muted);"></i>
        <input type="text" class="sigat-search" id="searchProj" placeholder="Buscar projeto..."
            oninput="filterProjects()">
    </div>
    <button class="btn btn-sigat" onclick="openProjModal()"><i class="fas fa-plus me-2"></i>Novo Projeto</button>
</div>

<div class="row g-4 animate-in" id="projCards">
    <div class="col-12 text-center py-5 text-muted-sigat">Carregando...</div>
</div>

<!-- Modal -->
<div class="modal fade" id="projModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="projModalTitle"><i
                        class="fas fa-bullseye me-2 text-sigat"></i>Novo Projeto</h5><button type="button"
                    class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pj_id">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Nome do Projeto *</label><input type="text"
                            class="form-control" id="pj_name" required></div>
                    <div class="col-12"><label class="form-label">Objetivo Geral</label><textarea class="form-control"
                            id="pj_obj" rows="2"></textarea></div>
                    <div class="col-12"><label class="form-label">Justificativa</label><textarea class="form-control"
                            id="pj_just" rows="2"></textarea></div>
                    <div class="col-12"><label class="form-label">Objetivos Específicos</label><textarea
                            class="form-control" id="pj_spec" rows="2"></textarea></div>
                    <div class="col-md-6"><label class="form-label">Metodologia</label><textarea class="form-control"
                            id="pj_method" rows="2"></textarea></div>
                    <div class="col-md-6"><label class="form-label">Plano de Comunicação</label><textarea
                            class="form-control" id="pj_comm" rows="2"></textarea></div>
                    <div class="col-12"><label class="form-label">Plano de Sustentabilidade</label><textarea
                            class="form-control" id="pj_sust" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="saveProject()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>

<script>
    let allProj = [];
    const projModal = new bootstrap.Modal(document.getElementById('projModal'));

    document.addEventListener('DOMContentLoaded', loadProjects);

    async function loadProjects() {
        const data = await apiCall('api/projects.php');
        if (data) { allProj = data; renderProjects(data); }
    }

    function renderProjects(list) {
        const c = document.getElementById('projCards');
        if (!list.length) { c.innerHTML = '<div class="col-12"><div class="empty-state"><i class="fas fa-bullseye"></i><h5>Nenhum projeto</h5></div></div>'; return; }
        c.innerHTML = list.map(p => {
            const budget = (p.budget || []).reduce((s, b) => s + (b.value || 0), 0);
            return `<div class="col-md-6 col-xl-4"><div class="sigat-card h-100">
            <div class="d-flex justify-content-between mb-3"><h6 class="text-white fw-bold mb-0">${p.name}</h6><span class="badge badge-status ${p.status === 'FINAL' ? 'badge-active' : 'badge-warning'}">${p.status || 'DRAFT'}</span></div>
            <p class="text-muted-sigat mb-2" style="font-size:13px;">${(p.general_objective || '').substring(0, 100)}${(p.general_objective || '').length > 100 ? '...' : ''}</p>
            <div class="mb-2"><small class="text-muted-sigat"><i class="fas fa-wallet me-2"></i>Orçamento: ${formatCurrency(budget)}</small></div>
            <div class="mb-3"><small class="text-muted-sigat"><i class="fas fa-calendar me-2"></i>${formatDate(p.created_at)}</small></div>
            <div class="d-flex gap-2">
                <button class="btn-icon btn-icon-edit" onclick='editProject(${JSON.stringify(p).replace(/'/g, "&apos;")})'><i class="fas fa-edit"></i></button>
                <button class="btn-icon btn-icon-delete" onclick="deleteProject('${p.id}')"><i class="fas fa-trash"></i></button>
            </div>
        </div></div>`;
        }).join('');
    }

    function filterProjects() { const q = document.getElementById('searchProj').value.toLowerCase(); renderProjects(allProj.filter(p => p.name.toLowerCase().includes(q))); }
    function openProjModal() { document.getElementById('pj_id').value = ''; document.getElementById('projModalTitle').innerHTML = '<i class="fas fa-bullseye me-2 text-sigat"></i>Novo Projeto';['pj_name', 'pj_obj', 'pj_just', 'pj_spec', 'pj_method', 'pj_comm', 'pj_sust'].forEach(id => document.getElementById(id).value = ''); projModal.show(); }

    function editProject(p) {
        document.getElementById('pj_id').value = p.id;
        document.getElementById('projModalTitle').innerHTML = '<i class="fas fa-edit me-2 text-sigat"></i>Editar Projeto';
        document.getElementById('pj_name').value = p.name || '';
        document.getElementById('pj_obj').value = p.general_objective || '';
        document.getElementById('pj_just').value = p.justification || '';
        document.getElementById('pj_spec').value = p.specific_objectives || '';
        document.getElementById('pj_method').value = p.methodology || '';
        document.getElementById('pj_comm').value = p.communication_plan || '';
        document.getElementById('pj_sust').value = p.sustainability_plan || '';
        projModal.show();
    }

    async function saveProject() {
        const id = document.getElementById('pj_id').value;
        const payload = { name: document.getElementById('pj_name').value, general_objective: document.getElementById('pj_obj').value, justification: document.getElementById('pj_just').value, specific_objectives: document.getElementById('pj_spec').value, methodology: document.getElementById('pj_method').value, communication_plan: document.getElementById('pj_comm').value, sustainability_plan: document.getElementById('pj_sust').value };
        if (!payload.name) { alert('Nome é obrigatório'); return; }
        const result = id ? await apiCall('api/projects.php?id=' + id, 'PUT', payload) : await apiCall('api/projects.php', 'POST', payload);
        if (result) { projModal.hide(); showToast(id ? 'Projeto atualizado!' : 'Projeto criado!'); loadProjects(); }
    }

    async function deleteProject(id) { if (!confirmDelete()) return; await apiCall('api/projects.php?id=' + id, 'DELETE'); showToast('Projeto excluído'); loadProjects(); }
</script>