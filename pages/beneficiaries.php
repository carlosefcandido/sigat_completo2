<!-- Beneficiários Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <div class="position-relative flex-grow-1" style="max-width:400px;">
        <i class="fas fa-search position-absolute"
            style="left:14px;top:50%;transform:translateY(-50%);color:var(--sigat-text-muted);"></i>
        <input type="text" class="sigat-search" id="searchBenef" placeholder="Buscar beneficiário..."
            oninput="filterBenef()">
    </div>
    <button class="btn btn-sigat" onclick="openBenefModal()">
        <i class="fas fa-plus me-2"></i>Novo Beneficiário
    </button>
</div>

<div class="sigat-card animate-in p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="sigat-table" id="benefTable">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Beneficiário</th>
                    <th>Nascimento</th>
                    <th>Responsável</th>
                    <th>Telefone</th>
                    <th>PCD</th>
                    <th>CadÚnico</th>
                    <th style="width:120px;">Ações</th>
                </tr>
            </thead>
            <tbody id="benefBody">
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted-sigat">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Beneficiário -->
<div class="modal fade" id="benefModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="benefModalTitle"><i
                        class="fas fa-user-plus me-2 text-sigat"></i>Novo Beneficiário</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs sigat-tabs px-3 pt-3" id="benefTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-white" id="cadastro-tab" data-bs-toggle="tab" data-bs-target="#cadastro" type="button" role="tab">Dados Cadastrais</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-white" id="beneficios-tab" data-bs-toggle="tab" data-bs-target="#beneficios" type="button" role="tab" disabled>Benefícios Recebidos</button>
                    </li>
                </ul>
                <div class="tab-content border-top border-secondary p-3" id="benefTabsContent">
                    <div class="tab-pane fade show active" id="cadastro" role="tabpanel">
                <form id="benefForm">
                    <input type="hidden" id="bf_id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="bf_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Data de Nascimento *</label>
                            <input type="date" class="form-control" id="bf_birth" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CPF ou RG</label>
                            <input type="text" class="form-control" id="bf_cpf">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="bf_phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nome do Responsável</label>
                            <input type="text" class="form-control" id="bf_resp_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CPF do Responsável</label>
                            <input type="text" class="form-control" id="bf_resp_cpf">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Endereço</label>
                            <input type="text" class="form-control" id="bf_address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Escola</label>
                            <input type="text" class="form-control" id="bf_school">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Série/Ano</label>
                            <input type="text" class="form-control" id="bf_grade">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Religião</label>
                            <select class="form-select" id="bf_religion">
                                <option value="Sem religião">Sem religião</option>
                                <option value="Católica Apostólica Romana">Católica</option>
                                <option value="Evangélica">Evangélica</option>
                                <option value="Espírita">Espírita</option>
                                <option value="Umbanda e Candomblé">Umbanda e Candomblé</option>
                                <option value="Outra religião">Outra</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Raça/Cor</label>
                            <select class="form-select" id="bf_race">
                                <option value="Prefere não declarar">Prefere não declarar</option>
                                <option value="Branca">Branca</option>
                                <option value="Preta">Preta</option>
                                <option value="Parda">Parda</option>
                                <option value="Amarela">Amarela</option>
                                <option value="Indígena">Indígena</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="bf_pcd" onchange="togglePcd()">
                                <label class="form-check-label text-muted-sigat">PCD</label>
                            </div>
                        </div>
                        <div class="col-md-8 d-none" id="pcdFields">
                            <label class="form-label">Tipo de Deficiência</label>
                            <input type="text" class="form-control" id="bf_pcd_type">
                        </div>
                        <div class="col-12 d-none" id="pcdDescField">
                            <label class="form-label">Descrição da Deficiência</label>
                            <textarea class="form-control" id="bf_pcd_desc" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="bf_followup">
                                <label class="form-check-label text-muted-sigat">Acompanhamento</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Observações Médicas</label>
                            <textarea class="form-control" id="bf_medical" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="bf_cadunico" onchange="toggleCad()">
                                <label class="form-check-label text-muted-sigat">CadÚnico</label>
                            </div>
                        </div>
                        <div class="col-md-8 d-none" id="cadFields">
                            <label class="form-label">Número NIS</label>
                            <input type="text" class="form-control" id="bf_nis">
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="text-sigat mb-3"><i class="fas fa-file-upload me-2"></i>Arquivos e Termos</h6>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label d-block text-muted-sigat">Foto do Beneficiário</label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="photoPreviewContainer" class="rounded-circle d-flex align-items-center justify-content-center bg-dark text-secondary" style="width: 70px; height: 70px; border: 2px solid rgba(255,255,255,0.1); overflow: hidden;">
                                    <i class="fas fa-user fa-2x" id="photoPlaceholder"></i>
                                    <img id="bf_photo_preview" src="" class="d-none w-100 h-100 object-fit-cover">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control form-control-sm" id="bf_photo" accept="image/*" onchange="previewPhoto(this)">
                                    <div id="bf_photo_link" class="mt-2 text-sm d-none"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Termo de Imagem</label>
                            <input type="file" class="form-control form-control-sm" id="bf_image_term" accept=".pdf,image/*,.doc,.docx">
                            <div id="bf_image_term_link" class="mt-2 text-sm d-none"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Termo de Saída</label>
                            <input type="file" class="form-control form-control-sm" id="bf_exit_term" accept=".pdf,image/*,.doc,.docx">
                            <div id="bf_exit_term_link" class="mt-2 text-sm d-none"></div>
                        </div>
                    </div>
                </form>
                    </div>
                    <div class="tab-pane fade" id="beneficios" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-white mb-0">Histórico de Benefícios</h6>
                            <button class="btn btn-sm btn-sigat" onclick="toggleAddBenefit()"><i class="fas fa-plus me-2"></i>Adicionar</button>
                        </div>
                        
                        <div id="addBenefitDiv" class="sigat-card bg-dark border border-secondary mb-3 d-none">
                            <h6 class="text-white mb-2" style="font-size:14px;">Novo Benefício</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-sm" id="bb_name" placeholder="Nome do benefício (ex: Cesta Básica)">
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control form-control-sm" id="bb_date">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-sigat w-100" onclick="saveBenefitLog()"><i class="fas fa-save"></i></button>
                                </div>
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-sm" id="bb_obs" placeholder="Observações (opcional)">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="sigat-table" style="font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Benefício</th>
                                        <th>Observações</th>
                                        <th style="width: 50px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="benefitsListBody">
                                    <tr><td colspan="4" class="text-center text-muted">Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <button class="btn btn-sigat" id="btnSaveBenef" onclick="saveBenef()">
                    <i class="fas fa-save me-2"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let allBenefs = [];
    let benefModal;
    let currentUploads = { photo: null, image_term: null, exit_term: null };
    let currentBeneficiaryId = null;

    document.addEventListener('DOMContentLoaded', () => {
        const modalEl = document.getElementById('benefModal');
        if (modalEl) benefModal = new bootstrap.Modal(modalEl);
        
        // Tab setup
        const tabBen = document.getElementById('beneficios-tab');
        if (tabBen) {
            tabBen.addEventListener('shown.bs.tab', function (e) {
                document.getElementById('btnSaveBenef').classList.add('d-none');
                loadBeneficiaryBenefits();
            });
        }
        const tabCad = document.getElementById('cadastro-tab');
        if (tabCad) {
            tabCad.addEventListener('shown.bs.tab', function (e) {
                document.getElementById('btnSaveBenef').classList.remove('d-none');
            });
        }

        loadBenefs();
    });
    async function loadBenefs() {
        const data = await apiCall('api/beneficiaries.php');
        if (data) { allBenefs = data; renderBenefs(data); }
    }

    function renderBenefs(list) {
        const body = document.getElementById('benefBody');
        if (!list.length) {
            body.innerHTML = '<tr><td colspan="8"><div class="empty-state py-5"><i class="fas fa-users"></i><h5>Nenhum beneficiário</h5><p>Clique em "Novo Beneficiário" para cadastrar.</p></div></td></tr>';
            return;
        }
        body.innerHTML = list.map(b => `
        <tr>
            <td><span class="badge badge-status badge-info">${b.id}</span></td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    ${b.photo_url 
                        ? `<img src="api/file.php?path=${b.photo_url}" class="rounded-circle object-fit-cover" style="width:32px; height:32px; border:1px solid rgba(255,255,255,0.2);">` 
                        : `<div class="rounded-circle d-flex align-items-center justify-content-center bg-dark text-secondary" style="width:32px; height:32px; border:1px solid rgba(255,255,255,0.1);"><i class="fas fa-user fa-sm"></i></div>`
                     }
                    <span class="fw-medium text-white">${b.name}</span>
                </div>
            </td>
            <td>${formatDate(b.birth_date)}</td>
            <td>${b.responsible_name || '-'}</td>
            <td>${b.phone || '-'}</td>
            <td>${b.is_pcd == 1 ? '<span class="badge badge-status badge-warning">Sim</span>' : '<span class="text-muted-sigat">Não</span>'}</td>
            <td>${b.has_cad_unico == 1 ? '<span class="badge badge-status badge-active">Sim</span>' : '<span class="text-muted-sigat">Não</span>'}</td>
            <td>
                <button class="btn-icon btn-icon-edit me-1" onclick='editBenef(${JSON.stringify(b).replace(/'/g, "\\'")})'><i class="fas fa-edit"></i></button>
                <button class="btn-icon btn-icon-delete" onclick="deleteBenef('${b.id}')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');
    }

    function filterBenef() {
        const q = document.getElementById('searchBenef').value.toLowerCase();
        const filtered = allBenefs.filter(b => b.name.toLowerCase().includes(q) || b.id.toLowerCase().includes(q));
        renderBenefs(filtered);
    }

    function togglePcd() {
        const show = document.getElementById('bf_pcd').checked;
        document.getElementById('pcdFields').classList.toggle('d-none', !show);
        document.getElementById('pcdDescField').classList.toggle('d-none', !show);
    }

    function toggleCad() {
        const show = document.getElementById('bf_cadunico').checked;
        document.getElementById('cadFields').classList.toggle('d-none', !show);
    }

    function previewPhoto(input) {
        const preview = document.getElementById('bf_photo_preview');
        const placeholder = document.getElementById('photoPlaceholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            if (currentUploads.photo) {
                preview.src = 'api/file.php?path=' + currentUploads.photo;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
            } else {
                preview.src = '';
                preview.classList.add('d-none');
                placeholder.classList.remove('d-none');
            }
        }
    }

    function setupFileLink(elementId, url, label) {
        const el = document.getElementById(elementId);
        if (url) {
            const secureUrl = 'api/file.php?path=' + url;
            el.innerHTML = `<a href="${secureUrl}" target="_blank" class="text-sigat text-decoration-none"><i class="fas fa-external-link-alt me-1"></i>${label}</a>`;
            el.classList.remove('d-none');
        } else {
            el.innerHTML = '';
            el.classList.add('d-none');
        }
    }

    async function uploadFile(fileInputId, folder) {
        const input = document.getElementById(fileInputId);
        if (!input.files || input.files.length === 0) return null;
        
        const file = input.files[0];
        const formData = new FormData();
        formData.append('file', file);
        formData.append('folder', folder);

        try {
            const response = await fetch('api/upload.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) return data.url;
            return null;
        } catch (e) {
            console.error('Upload falhou', e);
            return null;
        }
    }

    function toggleAddBenefit() {
        const div = document.getElementById('addBenefitDiv');
        div.classList.toggle('d-none');
        document.getElementById('bb_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('bb_name').value = '';
        document.getElementById('bb_obs').value = '';
    }

    async function loadBeneficiaryBenefits() {
        const bBody = document.getElementById('benefitsListBody');
        bBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted-sigat">Carregando...</td></tr>';
        if (!currentBeneficiaryId) return;

        const data = await apiCall('api/beneficiary_benefits.php?beneficiary_id=' + currentBeneficiaryId);
        if (!data || data.error) {
            bBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-danger">Erro ao carregar benefícios.</td></tr>';
            return;
        }

        if (data.length === 0) {
            bBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted-sigat">Nenhum benefício registrado para este aluno.</td></tr>';
        } else {
            bBody.innerHTML = data.map(b => `
                <tr>
                    <td>${formatDate(b.date_received)}</td>
                    <td class="text-white">${b.benefit_name}</td>
                    <td class="text-muted-sigat">${b.observations || '-'}</td>
                    <td>
                        <button class="btn-icon btn-icon-delete" onclick="deleteBenefitLog('${b.id}')"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }
    }

    async function saveBenefitLog() {
        const payload = {
            beneficiary_id: currentBeneficiaryId,
            benefit_name: document.getElementById('bb_name').value,
            date_received: document.getElementById('bb_date').value,
            observations: document.getElementById('bb_obs').value
        };

        if (!payload.benefit_name || !payload.date_received) {
            alert('Nome e data são obrigatórios.');
            return;
        }

        const res = await apiCall('api/beneficiary_benefits.php', 'POST', payload);
        if (res && !res.error) {
            showToast('Benefício adicionado!');
            document.getElementById('addBenefitDiv').classList.add('d-none');
            loadBeneficiaryBenefits();
        }
    }

    async function deleteBenefitLog(id) {
        if (!confirmDelete('Deseja excluir este benefício? O histórico será perdido.')) return;
        const res = await apiCall('api/beneficiary_benefits.php?id=' + id, 'DELETE');
        if (res && !res.error) {
            showToast('Benefício removido!');
            loadBeneficiaryBenefits();
        }
    }

    function openBenefModal(data = null) {
        document.getElementById('benefForm').reset();
        currentBeneficiaryId = null;
        document.getElementById('beneficios-tab').disabled = true;
        
        const tabEl = document.getElementById('cadastro-tab');
        if(tabEl) new bootstrap.Tab(tabEl).show();

        document.getElementById('bf_id').value = '';
        currentUploads = { photo: null, image_term: null, exit_term: null };
        document.getElementById('bf_photo_preview').src = '';
        document.getElementById('bf_photo_preview').classList.add('d-none');
        document.getElementById('photoPlaceholder').classList.remove('d-none');
        setupFileLink('bf_photo_link', null, '');
        setupFileLink('bf_image_term_link', null, '');
        setupFileLink('bf_exit_term_link', null, '');
        document.getElementById('pcdFields').classList.add('d-none');
        document.getElementById('pcdDescField').classList.add('d-none');
        document.getElementById('cadFields').classList.add('d-none');
        document.getElementById('benefModalTitle').innerHTML = '<i class="fas fa-user-plus me-2 text-sigat"></i>Novo Beneficiário';
        benefModal.show();
    }

    function editBenef(b) {
        currentBeneficiaryId = b.id;
        document.getElementById('beneficios-tab').disabled = false;
        
        const tabEl = document.getElementById('cadastro-tab');
        if(tabEl) new bootstrap.Tab(tabEl).show();

        document.getElementById('benefModalTitle').innerHTML = '<i class="fas fa-user-edit me-2 text-sigat"></i>Editar Beneficiário';
        document.getElementById('bf_id').value = b.id;
        document.getElementById('bf_name').value = b.name;
        document.getElementById('bf_birth').value = b.birth_date;
        document.getElementById('bf_cpf').value = b.cpf_rg || '';
        document.getElementById('bf_phone').value = b.phone || '';
        document.getElementById('bf_resp_name').value = b.responsible_name || '';
        document.getElementById('bf_resp_cpf').value = b.responsible_cpf || '';
        document.getElementById('bf_address').value = b.address || '';
        document.getElementById('bf_school').value = b.school || '';
        document.getElementById('bf_grade').value = b.grade || '';
        document.getElementById('bf_religion').value = b.religion || 'Sem religião';
        document.getElementById('bf_race').value = b.race_color || 'Prefere não declarar';
        document.getElementById('bf_pcd').checked = b.is_pcd == 1;
        document.getElementById('bf_pcd_type').value = b.pcd_type || '';
        document.getElementById('bf_pcd_desc').value = b.pcd_description || '';
        document.getElementById('bf_followup').checked = b.needs_follow_up == 1;
        document.getElementById('bf_medical').value = b.medical_notes || '';
        document.getElementById('bf_cadunico').checked = b.has_cad_unico == 1;
        document.getElementById('bf_nis').value = b.nis_number || '';
        currentUploads = { photo: b.photo_url, image_term: b.image_term_url, exit_term: b.exit_term_url };
        if (b.photo_url) {
            document.getElementById('bf_photo_preview').src = 'api/file.php?path=' + b.photo_url;
            document.getElementById('bf_photo_preview').classList.remove('d-none');
            document.getElementById('photoPlaceholder').classList.add('d-none');
        } else {
            document.getElementById('bf_photo_preview').src = '';
            document.getElementById('bf_photo_preview').classList.add('d-none');
            document.getElementById('photoPlaceholder').classList.remove('d-none');
        }
        setupFileLink('bf_photo_link', b.photo_url, 'Ver Foto Atual');
        setupFileLink('bf_image_term_link', b.image_term_url, 'Ver Termo de Imagem');
        setupFileLink('bf_exit_term_link', b.exit_term_url, 'Ver Termo de Saída');
        togglePcd(); toggleCad();
        benefModal.show();
    }

    async function saveBenef() {
        const id = document.getElementById('bf_id').value;
        const nameVal = document.getElementById('bf_name').value;
        const birthVal = document.getElementById('bf_birth').value;
        
        if (!nameVal || !birthVal) { alert('Nome e data de nascimento são obrigatórios'); return; }

        const btn = document.getElementById('btnSaveBenef');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
        btn.disabled = true;

        try {
            const photoUrl = await uploadFile('bf_photo', 'images') || currentUploads.photo;
            const imageTermUrl = await uploadFile('bf_image_term', 'documents') || currentUploads.image_term;
            const exitTermUrl = await uploadFile('bf_exit_term', 'documents') || currentUploads.exit_term;

        const payload = {
            name: nameVal,
            birth_date: birthVal,
            cpf_rg: document.getElementById('bf_cpf').value,
            phone: document.getElementById('bf_phone').value,
            responsible_name: document.getElementById('bf_resp_name').value,
            responsible_cpf: document.getElementById('bf_resp_cpf').value,
            address: document.getElementById('bf_address').value,
            school: document.getElementById('bf_school').value,
            grade: document.getElementById('bf_grade').value,
            religion: document.getElementById('bf_religion').value,
            race_color: document.getElementById('bf_race').value,
            is_pcd: document.getElementById('bf_pcd').checked ? 1 : 0,
            pcd_type: document.getElementById('bf_pcd_type').value,
            pcd_description: document.getElementById('bf_pcd_desc').value,
            needs_follow_up: document.getElementById('bf_followup').checked ? 1 : 0,
            medical_notes: document.getElementById('bf_medical').value,
            has_cad_unico: document.getElementById('bf_cadunico').checked ? 1 : 0,
            nis_number: document.getElementById('bf_nis').value,
            photo_url: photoUrl,
            image_term_url: imageTermUrl,
            exit_term_url: exitTermUrl
        };

        if (!payload.name || !payload.birth_date) { alert('Nome e data de nascimento são obrigatórios'); return; }

        let result;
        if (id) {
            result = await apiCall('api/beneficiaries.php?id=' + id, 'PUT', payload);
        } else {
            result = await apiCall('api/beneficiaries.php', 'POST', payload);
        }

            if (result) {
                benefModal.hide();
                showToast(id ? 'Beneficiário atualizado!' : 'Beneficiário cadastrado!');
                loadBenefs();
            }
        } catch(e) {
            console.error(e);
            alert('Erro inesperado: ' + e.message);
        } finally {
            if(btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    }

    async function deleteBenef(id) {
        if (!confirmDelete('Deseja realmente excluir este beneficiário?')) return;
        const result = await apiCall('api/beneficiaries.php?id=' + id, 'PUT', { is_deleted: 1 });
        if (result) { showToast('Beneficiário movido para lixeira'); loadBenefs(); }
    }
</script>