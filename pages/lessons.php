<!-- Plano de Aula / Relatórios Page -->
<ul class="nav sigat-tabs mb-4 animate-in">
    <li class="nav-item"><a class="nav-link active" href="#" onclick="showLessonTab('plans',this)">Planos de Aula</a>
    </li>
    <li class="nav-item"><a class="nav-link" href="#" onclick="showLessonTab('reports',this)">Relatórios</a></li>
</ul>

<div id="lessonPlansTab" class="animate-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h6 class="text-white mb-0"><i class="fas fa-book-open me-2 text-sigat"></i>Planos de Aula</h6>
        <button class="btn btn-sigat" onclick="openPlanModal()"><i class="fas fa-plus me-2"></i>Novo Plano</button>
    </div>
    <div class="sigat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="sigat-table">
                <thead>
                    <tr>
                        <th>Turma</th>
                        <th>Mês</th>
                        <th>Objetivo</th>
                        <th>Conteúdo</th>
                        <th>Criado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="plansBody">
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted-sigat">Carregando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="lessonReportsTab" class="d-none animate-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h6 class="text-white mb-0"><i class="fas fa-file-alt me-2 text-sigat"></i>Relatórios de Aula</h6>
        <button class="btn btn-sigat" onclick="openReportModal()"><i class="fas fa-plus me-2"></i>Novo
            Relatório</button>
    </div>
    <div class="sigat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="sigat-table">
                <thead>
                    <tr>
                        <th>Turma</th>
                        <th>Mês</th>
                        <th>Entradas</th>
                        <th>Criado</th>
                    </tr>
                </thead>
                <tbody id="reportsBody">
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted-sigat">Carregando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Plan Create / Edit Modal -->
<div class="modal fade" id="planModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="planModalTitle"><i class="fas fa-book-open me-2 text-sigat"></i><span id="planModalTitleText">Novo Plano de Aula</span>
                </h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="lp_id">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Turma *</label><select class="form-select"
                            id="lp_class">
                            <option value="">Selecione</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Mês *</label><input type="month"
                            class="form-control" id="lp_month"></div>
                    <div class="col-12"><label class="form-label">Objetivo</label><textarea class="form-control"
                            id="lp_objective" rows="2"></textarea></div>
                    <div class="col-12"><label class="form-label">Conteúdo</label><textarea class="form-control"
                            id="lp_content" rows="2"></textarea></div>
                    <div class="col-md-6"><label class="form-label">Metodologia</label><textarea class="form-control"
                            id="lp_methodology" rows="2"></textarea></div>
                    <div class="col-md-6"><label class="form-label">Materiais</label><textarea class="form-control"
                            id="lp_materials" rows="2"></textarea></div>
                    <div class="col-12"><label class="form-label">Observações</label><textarea class="form-control"
                            id="lp_obs" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" id="planSaveBtn" onclick="savePlan()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>

<!-- Plan View Modal -->
<div class="modal fade" id="planViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-eye me-2 text-sigat"></i>Visualizar Plano de Aula</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="planViewBody">
                <!-- populated dynamically -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <button class="btn btn-sigat" id="planViewEditBtn" onclick="switchToEdit()"><i class="fas fa-edit me-2"></i>Editar</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="planDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-trash me-2 text-danger"></i>Excluir Plano</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-white mb-1">Tem certeza que deseja excluir o plano de aula?</p>
                <p class="text-muted-sigat small mb-0" id="planDeleteInfo"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger" id="planDeleteConfirmBtn" onclick="confirmDeletePlan()"><i class="fas fa-trash me-2"></i>Excluir</button>
            </div>
        </div>
    </div>
</div>

<script>
    let lessonPlans = [], lessonReports = [], lessonClasses = [];
    let currentViewPlan = null, planToDeleteId = null;
    const planModal = new bootstrap.Modal(document.getElementById('planModal'));
    const planViewModal = new bootstrap.Modal(document.getElementById('planViewModal'));
    const planDeleteModal = new bootstrap.Modal(document.getElementById('planDeleteModal'));

    document.addEventListener('DOMContentLoaded', loadLessons);

    // Reset plan modal when it closes
    document.getElementById('planModal').addEventListener('hidden.bs.modal', () => {
        document.getElementById('lp_id').value = '';
        document.getElementById('lp_class').value = '';
        document.getElementById('lp_month').value = '';
        document.getElementById('lp_objective').value = '';
        document.getElementById('lp_content').value = '';
        document.getElementById('lp_methodology').value = '';
        document.getElementById('lp_materials').value = '';
        document.getElementById('lp_obs').value = '';
        document.getElementById('planModalTitleText').textContent = 'Novo Plano de Aula';
        document.getElementById('lp_class').disabled = false;
    });

    function showLessonTab(tab, el) {
        document.querySelectorAll('.sigat-tabs .nav-link').forEach(l => l.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('lessonPlansTab').classList.toggle('d-none', tab !== 'plans');
        document.getElementById('lessonReportsTab').classList.toggle('d-none', tab !== 'reports');
    }

    async function loadLessons() {
        const [plans, reports, classes] = await Promise.all([
            apiCall('api/lesson_plans.php'),
            apiCall('api/lesson_reports.php'),
            apiCall('api/classes.php')
        ]);
        lessonPlans = plans || []; lessonReports = reports || []; lessonClasses = classes || [];
        const sel = document.getElementById('lp_class');
        // Keep the default "Selecione" and re-build options
        sel.innerHTML = '<option value="">Selecione</option>';
        lessonClasses.forEach(c => sel.innerHTML += `<option value="${c.id}">${c.name}</option>`);
        renderPlans(); renderReports();
    }

    function renderPlans() {
        const body = document.getElementById('plansBody');
        if (!lessonPlans.length) {
            body.innerHTML = '<tr><td colspan="6"><div class="empty-state py-4"><i class="fas fa-book-open"></i><p class="mt-2 mb-0">Nenhum plano cadastrado</p></div></td></tr>';
            return;
        }
        body.innerHTML = lessonPlans.map(p => `
            <tr>
                <td>${p.class_name || p.class_id}</td>
                <td>${formatMonth(p.month)}</td>
                <td class="text-truncate" style="max-width:180px;" title="${escapeHtml(p.objective || '')}">${p.objective || '-'}</td>
                <td class="text-truncate" style="max-width:180px;" title="${escapeHtml(p.content || '')}">${p.content || '-'}</td>
                <td>${formatDate(p.created_at)}</td>
                <td class="text-center">
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-sm btn-outline-info" title="Visualizar" onclick="viewPlan('${p.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" title="Editar" onclick="editPlan('${p.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Excluir" onclick="deletePlan('${p.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`).join('');
    }

    function renderReports() {
        const body = document.getElementById('reportsBody');
        if (!lessonReports.length) {
            body.innerHTML = '<tr><td colspan="4"><div class="empty-state py-4"><i class="fas fa-file-alt"></i><p class="mt-2 mb-0">Nenhum relatório</p></div></td></tr>';
            return;
        }
        body.innerHTML = lessonReports.map(r => `<tr><td>${r.class_name || r.class_id}</td><td>${formatMonth(r.month)}</td><td>${(r.entries || []).length} registros</td><td>${formatDate(r.created_at)}</td></tr>`).join('');
    }

    function formatMonth(val) {
        if (!val) return '-';
        const [y, m] = val.split('-');
        if (!y || !m) return val;
        const months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
        return `${months[parseInt(m,10)-1]}/${y}`;
    }

    function escapeHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function getPlanById(id) {
        return lessonPlans.find(p => p.id === id);
    }

    // ── VIEW ────────────────────────────────────────────────
    function viewPlan(id) {
        const p = getPlanById(id);
        if (!p) return;
        currentViewPlan = p;
        const field = (label, value) => value
            ? `<div class="mb-3"><label class="form-label text-muted-sigat small mb-1">${label}</label><div class="text-white">${escapeHtml(value)}</div></div>`
            : '';

        document.getElementById('planViewBody').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">${field('Turma', p.class_name || p.class_id)}</div>
                <div class="col-md-6">${field('Mês', formatMonth(p.month))}</div>
                ${p.objective ? `<div class="col-12"><label class="form-label text-muted-sigat small mb-1">Objetivo</label><div class="text-white" style="white-space:pre-wrap;">${escapeHtml(p.objective)}</div></div>` : ''}
                ${p.content ? `<div class="col-12"><label class="form-label text-muted-sigat small mb-1">Conteúdo</label><div class="text-white" style="white-space:pre-wrap;">${escapeHtml(p.content)}</div></div>` : ''}
                ${p.methodology ? `<div class="col-md-6"><label class="form-label text-muted-sigat small mb-1">Metodologia</label><div class="text-white" style="white-space:pre-wrap;">${escapeHtml(p.methodology)}</div></div>` : ''}
                ${p.materials ? `<div class="col-md-6"><label class="form-label text-muted-sigat small mb-1">Materiais</label><div class="text-white" style="white-space:pre-wrap;">${escapeHtml(p.materials)}</div></div>` : ''}
                ${p.observations ? `<div class="col-12"><label class="form-label text-muted-sigat small mb-1">Observações</label><div class="text-white" style="white-space:pre-wrap;">${escapeHtml(p.observations)}</div></div>` : ''}
                <div class="col-12 border-top border-secondary pt-2 mt-1">
                    <small class="text-muted-sigat">Criado em: ${formatDate(p.created_at)}</small>
                </div>
            </div>`;
        planViewModal.show();
    }

    function switchToEdit() {
        if (!currentViewPlan) return;
        planViewModal.hide();
        setTimeout(() => editPlan(currentViewPlan.id), 350);
    }

    // ── EDIT ────────────────────────────────────────────────
    function openPlanModal() {
        document.getElementById('planModalTitleText').textContent = 'Novo Plano de Aula';
        planModal.show();
    }

    function editPlan(id) {
        const p = getPlanById(id);
        if (!p) return;
        document.getElementById('lp_id').value = p.id;
        document.getElementById('lp_class').value = p.class_id;
        document.getElementById('lp_class').disabled = true; // turma não muda na edição
        document.getElementById('lp_month').value = p.month;
        document.getElementById('lp_objective').value = p.objective || '';
        document.getElementById('lp_content').value = p.content || '';
        document.getElementById('lp_methodology').value = p.methodology || '';
        document.getElementById('lp_materials').value = p.materials || '';
        document.getElementById('lp_obs').value = p.observations || '';
        document.getElementById('planModalTitleText').textContent = 'Editar Plano de Aula';
        planModal.show();
    }

    async function savePlan() {
        const id = document.getElementById('lp_id').value;
        const isEdit = !!id;

        const payload = {
            class_id: document.getElementById('lp_class').value,
            month: document.getElementById('lp_month').value,
            objective: document.getElementById('lp_objective').value,
            content: document.getElementById('lp_content').value,
            methodology: document.getElementById('lp_methodology').value,
            materials: document.getElementById('lp_materials').value,
            observations: document.getElementById('lp_obs').value
        };

        if (!isEdit && !payload.class_id) { alert('Turma é obrigatória'); return; }
        if (!payload.month) { alert('Mês é obrigatório'); return; }

        const url = isEdit ? `api/lesson_plans.php?id=${id}` : 'api/lesson_plans.php';
        const method = isEdit ? 'PUT' : 'POST';
        const result = await apiCall(url, method, payload);
        if (result) {
            planModal.hide();
            showToast(isEdit ? 'Plano atualizado!' : 'Plano criado!');
            loadLessons();
        }
    }

    // ── DELETE ─────────────────────────────────────────────
    function deletePlan(id) {
        const p = getPlanById(id);
        if (!p) return;
        planToDeleteId = id;
        document.getElementById('planDeleteInfo').textContent =
            `${p.class_name || p.class_id} — ${formatMonth(p.month)}`;
        planDeleteModal.show();
    }

    async function confirmDeletePlan() {
        if (!planToDeleteId) return;
        const btn = document.getElementById('planDeleteConfirmBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Excluindo...';
        const result = await apiCall(`api/lesson_plans.php?id=${planToDeleteId}`, 'DELETE');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash me-2"></i>Excluir';
        if (result) {
            planDeleteModal.hide();
            showToast('Plano excluído!');
            planToDeleteId = null;
            loadLessons();
        }
    }

    function openReportModal() { alert('Modal de relatório em desenvolvimento'); }
</script>