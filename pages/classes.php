<!-- Turmas Page -->
<div class="animate-in mb-4">
    <ul class="nav sigat-tabs">
        <li class="nav-item"><a class="nav-link active" href="#" onclick="showClassTab('cards',this)">Minhas Turmas</a>
        </li>
        <li class="nav-item"><a class="nav-link" href="#" onclick="showClassTab('schedule',this)">Agenda Semanal</a>
        </li>
    </ul>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <div class="position-relative flex-grow-1" style="max-width:400px;">
        <i class="fas fa-search position-absolute"
            style="left:14px;top:50%;transform:translateY(-50%);color:var(--sigat-text-muted);"></i>
        <input type="text" class="sigat-search" id="searchClass" placeholder="Buscar turma..."
            oninput="filterClasses()">
    </div>
    <?php if (in_array($userRole, ['ADMIN', 'COORDENAÇÃO'])): ?>
        <button class="btn btn-sigat" onclick="openClassModal()">
            <i class="fas fa-plus me-2"></i>Nova Turma
        </button>
    <?php endif; ?>
</div>

<div id="classCardsView" class="animate-in">
    <div class="row g-4" id="classCards">
        <div class="col-12 text-center py-5 text-muted-sigat">Carregando...</div>
    </div>
</div>

<div id="classScheduleView" class="d-none animate-in">
    <div class="sigat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="sigat-table mb-0">
                <thead>
                    <tr>
                        <th style="width:150px;">Dia</th>
                        <th>Turmas</th>
                    </tr>
                </thead>
                <tbody id="scheduleBody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nova Turma -->
<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="classModalTitle"><i
                        class="fas fa-chalkboard me-2 text-sigat"></i>Nova Turma</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cl_id">
                <div class="mb-3">
                    <label class="form-label">Nome da Turma *</label>
                    <input type="text" class="form-control" id="cl_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Projeto</label>
                    <select class="form-select" id="cl_project">
                        <option value="">Selecione...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Professor</label>
                    <select class="form-select" id="cl_teacher">
                        <option value="">Selecione...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Horário</label>
                    <input type="text" class="form-control" id="cl_schedule" placeholder="Ex: 14:00 - 16:00">
                </div>
                <div class="mb-0">
                    <label class="form-label d-block">Dias da Semana</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php
                        $weekDays = [
                            ['id' => 'seg', 'label' => 'Segund'],
                            ['id' => 'ter', 'label' => 'Terça'],
                            ['id' => 'qua', 'label' => 'Quarta'],
                            ['id' => 'qui', 'label' => 'Quinta'],
                            ['id' => 'sex', 'label' => 'Sexta'],
                            ['id' => 'sab', 'label' => 'Sábado']
                        ];
                        foreach ($weekDays as $wd): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cl-day" type="checkbox" value="<?php echo $wd['id']; ?>"
                                    id="wd_<?php echo $wd['id']; ?>">
                                <label class="form-check-label text-muted-sigat"
                                    for="wd_<?php echo $wd['id']; ?>"><?php echo $wd['label']; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label d-block">Alunos (Beneficiários)</label>
                    <div id="cl_beneficiaries_list" class="p-3 border rounded-3 bg-dark-subtle overflow-auto"
                        style="max-height: 200px; border-color: var(--sigat-border) !important;">
                        <small class="text-muted-sigat">Carregando alunos...</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-sigat" onclick="saveClass()"><i class="fas fa-save me-2"></i>Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title text-white"><i class="fas fa-check-double me-2 text-sigat"></i>Chamada</h5>
                    <small class="text-muted-sigat" id="att_class_name"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <label class="form-label mb-0 fw-bold text-white">Data da Aula:</label>
                        <input type="date" class="form-control" id="att_date" style="max-width:200px;"
                            onchange="checkAndLoadAttendance()">
                    </div>
                    <small id="att_allowed_days" class="text-warning mt-2 d-none"><i class="fas fa-info-circle me-1"></i>Dias de aula: </small>
                </div>
                <div class="sigat-card p-0 overflow-hidden">
                    <table class="sigat-table mb-0">
                        <thead>
                            <tr>
                                <th>Aluno (Beneficiário)</th>
                                <th class="text-center" style="width:120px;">Presença</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceList"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <button class="btn btn-sigat" onclick="saveAttendance()"><i class="fas fa-save me-2"></i>Salvar
                    Chamada</button>
            </div>
        </div>
    </div>
</div>

<script>
    let allClasses = [], allProjects = [], allTeachers = [], allBeneficiaries = [];
    let classModal, attendanceModal;
    let currentAttClass = null;

    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('classModal');
        if (el) classModal = new bootstrap.Modal(el);
        const attEl = document.getElementById('attendanceModal');
        if (attEl) attendanceModal = new bootstrap.Modal(attEl);

        loadClasses();
    });

    function showClassTab(tab, el) {
        document.querySelectorAll('.sigat-tabs .nav-link').forEach(l => l.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('classCardsView').classList.toggle('d-none', tab !== 'cards');
        document.getElementById('classScheduleView').classList.toggle('d-none', tab !== 'schedule');
    }
    async function loadClasses() {
        const userRole = '<?php echo $userRole; ?>';
        const canManage = ['ADMIN', 'COORDENAÇÃO'].includes(userRole);

        const promises = [apiCall('api/classes.php')];
        if (canManage) {
            promises.push(apiCall('api/projects.php'));
            promises.push(apiCall('api/users.php').catch(() => []));
            promises.push(apiCall('api/beneficiaries.php').catch(() => []));
        }

        const results = await Promise.all(promises);
        allClasses = results[0] || [];

        if (canManage) {
            allProjects = results[1] || [];
            allTeachers = (results[2] || []).filter(u => u.perfil === 'PROFESSOR' || u.perfil === 'ADMIN');
            allBeneficiaries = results[3] || [];

            // Fill selects
            const projSel = document.getElementById('cl_project');
            projSel.innerHTML = '<option value="">Selecione...</option>';
            allProjects.forEach(p => { projSel.innerHTML += `<option value="${p.id}">${p.name}</option>`; });

            const teachSel = document.getElementById('cl_teacher');
            teachSel.innerHTML = '<option value="">Selecione...</option>';
            allTeachers.forEach(t => { teachSel.innerHTML += `<option value="${t.id}">${t.nome}</option>`; });

            const benefList = document.getElementById('cl_beneficiaries_list');
            benefList.innerHTML = allBeneficiaries.map(b => `
                <div class="form-check mb-1">
                    <input class="form-check-input cl-benef" type="checkbox" value="${b.id}" id="clb_${b.id}">
                    <label class="form-check-label text-muted-sigat small" for="clb_${b.id}">${b.name}</label>
                </div>
            `).join('') || '<small class="text-muted-sigat">Nenhum aluno disponível.</small>';
        }

        renderClasses(allClasses);
    }

    function renderClasses(list) {
        const container = document.getElementById('classCards');
        const scheduleBody = document.getElementById('scheduleBody');

        if (!list.length) {
            container.innerHTML = '<div class="col-12"><div class="empty-state"><i class="fas fa-chalkboard"></i><h5>Nenhuma turma</h5><p>Não há turmas vinculadas ao seu perfil.</p></div></div>';
            scheduleBody.innerHTML = '<tr><td colspan="2" class="text-center py-4 text-muted-sigat">Nenhuma aula programada.</td></tr>';
            return;
        }

        const dayNames = {
            'seg': 'Segunda', 'ter': 'Terça', 'qua': 'Quarta',
            'qui': 'Quinta', 'sex': 'Sexta', 'sab': 'Sábado'
        };

        // Render Cards
        container.innerHTML = list.map(c => {
            const classDays = (c.days_of_week || []).map(d => dayNames[d] || d).join(', ');
            return `
        <div class="col-md-6 col-xl-4">
            <div class="sigat-card h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="text-white fw-bold mb-0">${c.name}</h6>
                    <span class="badge badge-status badge-purple">${c.id}</span>
                </div>
                <div class="mb-2"><small class="text-muted-sigat"><i class="fas fa-bullseye me-2"></i>${c.project_name || '-'}</small></div>
                <div class="mb-2"><small class="text-muted-sigat"><i class="fas fa-user me-2"></i>${c.teacher_name || '-'}</small></div>
                <div class="mb-2"><small class="text-muted-sigat"><i class="fas fa-calendar-day me-2"></i>${classDays || '-'}</small></div>
                <div class="mb-2"><small class="text-muted-sigat"><i class="fas fa-clock me-2"></i>${c.schedule || '-'}</small></div>
                <div class="mb-3"><small class="text-muted-sigat"><i class="fas fa-users me-2"></i>${(c.beneficiary_ids || []).length} alunos</small></div>
                <div class="mt-auto d-flex gap-2">
                    <button class="btn btn-sigat btn-sm flex-grow-1" onclick="openAttendance('${c.id}')"><i class="fas fa-check-double me-1"></i>Chamada</button>
                    <a href="?page=lessons&class_id=${c.id}" class="btn btn-sigat-outline btn-sm flex-grow-1"><i class="fas fa-book-open me-1"></i>Aulas</a>
                    <?php if (in_array($userRole, ['ADMIN', 'COORDENAÇÃO'])): ?>
                    <button class="btn btn-outline-warning btn-sm" onclick="editClass('${c.id}')" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteClass('${c.id}')" title="Excluir"><i class="fas fa-trash"></i></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>`;
        }).join('');

        // Render Schedule Table
        const daysOrder = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab'];
        scheduleBody.innerHTML = daysOrder.map(dayKey => {
            const dayClasses = list.filter(c => (c.days_of_week || []).includes(dayKey));
            if (dayClasses.length === 0) return '';

            return `
            <tr>
                <td class="fw-bold text-sigat">${dayNames[dayKey]}</td>
                <td>
                    <div class="d-flex flex-wrap gap-2">
                        ${dayClasses.map(c => `
                            <div class="p-2 rounded-3" style="background:rgba(255,255,255,0.03); border: 1px solid var(--sigat-border); min-width:200px;">
                                <div class="text-white fw-medium small">${c.name}</div>
                                <div class="text-muted-sigat" style="font-size:11px;">
                                    <i class="fas fa-clock me-1"></i>${c.schedule}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </td>
            </tr>
            `;
        }).join('') || '<tr><td colspan="2" class="text-center py-4 text-muted-sigat">Nenhuma aula programada nos dias selecionados.</td></tr>';
    }

    function filterClasses() {
        const q = document.getElementById('searchClass').value.toLowerCase();
        renderClasses(allClasses.filter(c => c.name.toLowerCase().includes(q)));
    }

    function openClassModal(classId = null) {
        const title = document.getElementById('classModalTitle');
        document.getElementById('cl_id').value = classId || '';
        document.getElementById('cl_name').value = '';
        document.getElementById('cl_project').value = '';
        document.getElementById('cl_teacher').value = '';
        document.getElementById('cl_schedule').value = '';
        document.querySelectorAll('.cl-day').forEach(cb => cb.checked = false);
        document.querySelectorAll('.cl-benef').forEach(cb => cb.checked = false);

        if (classId) {
            const c = allClasses.find(i => i.id === classId);
            if (c) {
                title.innerHTML = '<i class="fas fa-edit me-2 text-sigat"></i>Editar Turma';
                document.getElementById('cl_name').value = c.name;
                document.getElementById('cl_project').value = c.project_id || '';
                document.getElementById('cl_teacher').value = c.teacher_id || '';
                document.getElementById('cl_schedule').value = c.schedule || '';
                (c.days_of_week || []).forEach(d => {
                    const cb = document.getElementById('wd_' + d);
                    if (cb) cb.checked = true;
                });
                (c.beneficiary_ids || []).forEach(bId => {
                    const cb = document.getElementById('clb_' + bId);
                    if (cb) cb.checked = true;
                });
            }
        } else {
            title.innerHTML = '<i class="fas fa-chalkboard me-2 text-sigat"></i>Nova Turma';
        }
        classModal.show();
    }

    function editClass(id) {
        openClassModal(id);
    }

    async function deleteClass(id) {
        if (!confirm('Tem certeza que deseja excluir esta turma?')) return;
        const result = await apiCall('api/classes.php?id=' + id, 'DELETE');
        if (result) {
            showToast('Turma excluída!');
            loadClasses();
        }
    }

    async function saveClass() {
        const id = document.getElementById('cl_id').value;
        const payload = {
            name: document.getElementById('cl_name').value,
            project_id: document.getElementById('cl_project').value || null,
            teacher_id: document.getElementById('cl_teacher').value || null,
            schedule: document.getElementById('cl_schedule').value,
            days_of_week: Array.from(document.querySelectorAll('.cl-day:checked')).map(cb => cb.value),
            beneficiary_ids: Array.from(document.querySelectorAll('.cl-benef:checked')).map(cb => cb.value)
        };
        if (!payload.name) { alert('Nome é obrigatório'); return; }

        const method = id ? 'PUT' : 'POST';
        const url = id ? 'api/classes.php?id=' + id : 'api/classes.php';

        const result = await apiCall(url, method, payload);
        if (result) {
            classModal.hide();
            showToast(id ? 'Turma atualizada!' : 'Turma criada!');
            loadClasses();
        }
    }

    // Attendance Logic
    function getMostRecentAllowedDate() {
        if (!currentAttClass || !currentAttClass.days_of_week || currentAttClass.days_of_week.length === 0) {
            const today = new Date();
            const tzOffset = today.getTimezoneOffset() * 60000;
            return new Date(today.getTime() - tzOffset).toISOString().split('T')[0];
        }
        
        const dayMap = { 'dom': 0, 'seg': 1, 'ter': 2, 'qua': 3, 'qui': 4, 'sex': 5, 'sab': 6 };
        const allowedDays = currentAttClass.days_of_week.map(d => dayMap[d]);
        
        const today = new Date();
        for (let i = 0; i < 7; i++) {
            const tempDate = new Date(today);
            tempDate.setDate(today.getDate() - i);
            if (allowedDays.includes(tempDate.getDay())) {
                const tzOffset = tempDate.getTimezoneOffset() * 60000;
                return new Date(tempDate.getTime() - tzOffset).toISOString().slice(0, 10);
            }
        }
        const tzOffset = today.getTimezoneOffset() * 60000;
        return new Date(today.getTime() - tzOffset).toISOString().split('T')[0];
    }

    function isDateAllowed(dateString) {
        if (!currentAttClass || !currentAttClass.days_of_week || currentAttClass.days_of_week.length === 0) return true;
        const parts = dateString.split('-');
        const date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        const dayOfWeek = date.getDay();
        const dayMap = { 'dom': 0, 'seg': 1, 'ter': 2, 'qua': 3, 'qui': 4, 'sex': 5, 'sab': 6 };
        return currentAttClass.days_of_week.map(d => dayMap[d]).includes(dayOfWeek);
    }

    async function checkAndLoadAttendance() {
        const dateInput = document.getElementById('att_date');
        const saveBtn = document.querySelector('#attendanceModal .btn-sigat');
        if (!isDateAllowed(dateInput.value)) {
            alert('A data solicitada não faz parte dos dias da semana configurados para esta turma.');
            document.getElementById('attendanceList').innerHTML = '<tr><td colspan="2" class="text-center py-4 text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Por favor, selecione uma data válida para esta turma.</td></tr>';
            if(saveBtn) saveBtn.disabled = true;
            return;
        }
        if(saveBtn) saveBtn.disabled = false;
        await loadAttendanceForDate();
    }

    async function openAttendance(classId) {
        currentAttClass = allClasses.find(c => c.id === classId);
        if (!currentAttClass) return;

        document.getElementById('att_class_name').textContent = currentAttClass.name;
        
        const dayNames = {
            'seg': 'Segunda', 'ter': 'Terça', 'qua': 'Quarta',
            'qui': 'Quinta', 'sex': 'Sexta', 'sab': 'Sábado', 'dom': 'Domingo'
        };
        const allowedDaysEl = document.getElementById('att_allowed_days');
        if (currentAttClass.days_of_week && currentAttClass.days_of_week.length > 0) {
            const names = currentAttClass.days_of_week.map(d => dayNames[d]).join(', ');
            allowedDaysEl.innerHTML = '<i class="fas fa-info-circle me-1"></i>Dias de aula: ' + names;
            allowedDaysEl.classList.remove('d-none');
        } else {
            allowedDaysEl.classList.add('d-none');
        }

        document.getElementById('att_date').value = getMostRecentAllowedDate();

        await checkAndLoadAttendance();
        attendanceModal.show();
    }

    async function loadAttendanceForDate() {
        const date = document.getElementById('att_date').value;
        const listBody = document.getElementById('attendanceList');
        listBody.innerHTML = '<tr><td colspan="2" class="text-center py-3 text-muted-sigat">Carregando alunos...</td></tr>';

        // Load beneficiaries details if not present
        if (!currentAttClass.beneficiaries) {
            const allBenefs = await apiCall('api/beneficiaries.php');
            currentAttClass.beneficiaries = allBenefs.filter(b => currentAttClass.beneficiary_ids.includes(b.id));
        }

        // Load existing attendance record for this day
        const attRecords = await apiCall(`api/attendances.php?class_id=${currentAttClass.id}&date=${date}`);
        const attendanceData = attRecords && attRecords.length > 0 ? attRecords[0].records : {};

        listBody.innerHTML = currentAttClass.beneficiaries.map(b => `
            <tr>
                <td class="text-white">${b.name} <br> <small class="text-muted-sigat" style="font-size:10px;">${b.id}</small></td>
                <td class="text-center">
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input att-check" type="checkbox" data-id="${b.id}" ${attendanceData[b.id] === 'P' ? 'checked' : ''}>
                    </div>
                </td>
            </tr>
        `).join('') || '<tr><td colspan="2" class="text-center py-4 text-muted-sigat">Nenhum aluno matriculado nesta turma.</td></tr>';
    }

    async function saveAttendance() {
        const date = document.getElementById('att_date').value;
        const records = {};
        document.querySelectorAll('.att-check').forEach(input => {
            records[input.dataset.id] = input.checked ? 'P' : 'F';
        });

        const result = await apiCall('api/attendances.php', 'POST', {
            class_id: currentAttClass.id,
            date: date,
            records: records
        });

        if (result) {
            showToast('Frequência salva!');
            attendanceModal.hide();
        }
    }
</script>