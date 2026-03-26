<!-- Portfólio Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <h6 class="text-white mb-0"><i class="fas fa-images me-2 text-sigat"></i>Portfólio de Projetos</h6>
    <button class="btn btn-sigat" onclick="openPortModal()"><i class="fas fa-plus me-2"></i>Novo Item</button>
</div>

<div class="row g-4 animate-in" id="portCards">
    <div class="col-12">
        <div class="empty-state"><i class="fas fa-images"></i>
            <h5>Nenhum item no portfólio</h5>
            <p>Adicione itens para documentar os resultados dos projetos.</p>
        </div>
    </div>
</div>

<div class="modal fade" id="portModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-images me-2 text-sigat"></i>Novo Item do Portfólio
                </h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Projeto</label><select class="form-select"
                            id="pt_project">
                            <option value="">Selecione</option>
                        </select></div>
                    <div class="col-md-3"><label class="form-label">Ano</label><input type="text" class="form-control"
                            id="pt_year" value="<?= date('Y') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Nº Beneficiários</label><input type="text"
                            class="form-control" id="pt_count"></div>
                    <div class="col-md-6"><label class="form-label">Local</label><input type="text" class="form-control"
                            id="pt_location"></div>
                    <div class="col-12"><label class="form-label">Descrição</label><textarea class="form-control"
                            id="pt_desc" rows="3"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="savePort()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>

<script>
    const portModal = new bootstrap.Modal(document.getElementById('portModal'));
    document.addEventListener('DOMContentLoaded', async () => {
        const [items, projects] = await Promise.all([apiCall('api/portfolio.php'), apiCall('api/projects.php')]);
        const sel = document.getElementById('pt_project');
        (projects || []).forEach(p => sel.innerHTML += `<option value="${p.id}">${p.name}</option>`);
        renderPort(items || [], projects || []);
    });

    function renderPort(items, projects) {
        const c = document.getElementById('portCards');
        if (!items.length) { c.innerHTML = '<div class="col-12"><div class="empty-state"><i class="fas fa-images"></i><h5>Nenhum item no portfólio</h5></div></div>'; return; }
        c.innerHTML = items.map(i => {
            const proj = projects.find(p => p.id === i.project_id);
            return `<div class="col-md-6 col-xl-4"><div class="sigat-card h-100">
            <div class="d-flex justify-content-between mb-2"><h6 class="text-white fw-bold">${proj ? proj.name : 'Projeto'}</h6><span class="badge badge-status badge-purple">${i.year}</span></div>
            <p class="text-muted-sigat" style="font-size:13px;">${(i.description || '').substring(0, 120)}</p>
            <div class="d-flex gap-2 mt-2"><small class="text-muted-sigat"><i class="fas fa-map-marker-alt me-1"></i>${i.location || '-'}</small><small class="text-muted-sigat"><i class="fas fa-users me-1"></i>${i.beneficiaries_count || '-'} beneficiários</small></div>
            <div class="mt-3"><button class="btn-icon btn-icon-delete" onclick="deletePort('${i.id}')"><i class="fas fa-trash"></i></button></div>
        </div></div>`;
        }).join('');
    }

    function openPortModal() { portModal.show(); }
    async function savePort() {
        const payload = { project_id: document.getElementById('pt_project').value, year: document.getElementById('pt_year').value, location: document.getElementById('pt_location').value, beneficiaries_count: document.getElementById('pt_count').value, description: document.getElementById('pt_desc').value };
        const result = await apiCall('api/portfolio.php', 'POST', payload);
        if (result) { portModal.hide(); showToast('Item adicionado!'); location.reload(); }
    }
    async function deletePort(id) { if (!confirmDelete()) return; await apiCall('api/portfolio.php?id=' + id, 'DELETE'); showToast('Item removido'); location.reload(); }
</script>