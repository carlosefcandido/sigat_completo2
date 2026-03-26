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
                    </tr>
                </thead>
                <tbody id="plansBody">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted-sigat">Carregando...</td>
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

<!-- Plan Modal -->
<div class="modal fade" id="planModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-book-open me-2 text-sigat"></i>Novo Plano de Aula
                </h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="savePlan()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>

<script>
    let lessonPlans = [], lessonReports = [], lessonClasses = [];
    const planModal = new bootstrap.Modal(document.getElementById('planModal'));

    document.addEventListener('DOMContentLoaded', loadLessons);

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
        lessonClasses.forEach(c => sel.innerHTML += `<option value="${c.id}">${c.name}</option>`);
        renderPlans(); renderReports();
    }

    function renderPlans() {
        const body = document.getElementById('plansBody');
        if (!lessonPlans.length) { body.innerHTML = '<tr><td colspan="5"><div class="empty-state py-4"><i class="fas fa-book-open"></i><p class="mt-2 mb-0">Nenhum plano</p></div></td></tr>'; return; }
        body.innerHTML = lessonPlans.map(p => `<tr><td>${p.class_name || p.class_id}</td><td>${p.month}</td><td class="text-truncate" style="max-width:200px;">${p.objective || '-'}</td><td class="text-truncate" style="max-width:200px;">${p.content || '-'}</td><td>${formatDate(p.created_at)}</td></tr>`).join('');
    }

    function renderReports() {
        const body = document.getElementById('reportsBody');
        if (!lessonReports.length) { body.innerHTML = '<tr><td colspan="4"><div class="empty-state py-4"><i class="fas fa-file-alt"></i><p class="mt-2 mb-0">Nenhum relatório</p></div></td></tr>'; return; }
        body.innerHTML = lessonReports.map(r => `<tr><td>${r.class_name || r.class_id}</td><td>${r.month}</td><td>${(r.entries || []).length} registros</td><td>${formatDate(r.created_at)}</td></tr>`).join('');
    }

    function openPlanModal() { planModal.show(); }
    function openReportModal() { alert('Modal de relatório em desenvolvimento'); }

    async function savePlan() {
        const payload = { class_id: document.getElementById('lp_class').value, month: document.getElementById('lp_month').value, objective: document.getElementById('lp_objective').value, content: document.getElementById('lp_content').value, methodology: document.getElementById('lp_methodology').value, materials: document.getElementById('lp_materials').value, observations: document.getElementById('lp_obs').value };
        if (!payload.class_id || !payload.month) { alert('Turma e mês são obrigatórios'); return; }
        const result = await apiCall('api/lesson_plans.php', 'POST', payload);
        if (result) { planModal.hide(); showToast('Plano criado!'); loadLessons(); }
    }
</script>