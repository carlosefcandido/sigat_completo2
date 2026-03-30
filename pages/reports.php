<?php
/**
 * SIGAT - Relatórios Avançados
 */
?>
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        body {
            background: white !important;
            font-size: 11pt;
        }

        .no-print,
        .sigat-sidebar,
        .sigat-topbar,
        .sigat-footer {
            display: none !important;
        }

        main {
            padding: 0 !important;
            margin: 0 !important;
        }

        #reportPrintArea {
            display: block !important;
            position: static;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }

        .container-fluid {
            padding: 0 !important;
            width: 100% !important;
        }

        table {
            width: 100% !important;
            table-layout: fixed;
            word-wrap: break-word;
        }

        .progress {
            border: 1px solid #ddd;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .progress-bar {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    .report-header {
        display: none;
        margin-bottom: 30px;
        border-bottom: 2px solid #333;
        padding-bottom: 15px;
        color: #333;
    }

    #reportPrintArea:not(.d-none) .report-header {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    @media print {
        .report-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            border-bottom: 3px solid #000 !important;
            gap: 10px !important;
        }

        .report-header>div:nth-child(2) {
            min-width: 0;
            flex: 1;
        }

        .report-header h3 {
            font-size: 14pt !important;
            word-wrap: break-word;
        }

        .report-header p {
            font-size: 8pt !important;
            line-height: 1.1;
            margin-top: 4px !important;
        }

        .report-header img {
            max-height: 50px !important;
        }
    }

    .report-type-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 1rem;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .report-type-card:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--sigat-primary);
    }

    .report-type-card.active {
        background: rgba(34, 197, 94, 0.1);
        border-color: var(--sigat-primary);
    }

    .filter-panel {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 16px;
        padding: 20px;
    }

    .progress {
        background: #eee;
    }
</style>

<div class="row g-4 no-print animate-in">
    <div class="col-lg-4">
        <div class="sigat-card filter-panel h-100">
            <h6 class="text-white fw-bold mb-3"><i class="fas fa-filter me-2 text-sigat"></i>Configurar Relatório</h6>

            <label class="form-label small text-muted-sigat">Tipo de Relatório</label>
            <div class="d-flex flex-column gap-2 mb-4">
                <div class="report-type-card active" data-type="beneficiaries"
                    onclick="setReportType('beneficiaries', this)">
                    <i class="fas fa-users me-2 text-sigat"></i>Listagem de Beneficiários
                </div>
                <div class="report-type-card" data-type="finance" onclick="setReportType('finance', this)">
                    <i class="fas fa-wallet me-2 text-success"></i>Movimentação Financeira
                </div>
                <div class="report-type-card" data-type="attendance" onclick="setReportType('attendance', this)">
                    <i class="fas fa-calendar-check me-2 text-warning"></i>Assiduidade (Frequência)
                </div>
                <div class="report-type-card" data-type="projects" onclick="setReportType('projects', this)">
                    <i class="fas fa-bullseye me-2 text-info"></i>Projetos e Planos
                </div>
                <div class="report-type-card" data-type="portfolio" onclick="setReportType('portfolio', this)">
                    <i class="fas fa-images me-2 text-danger"></i>Portfólio e Impacto
                </div>
                <div class="report-type-card" data-type="fundraising" onclick="setReportType('fundraising', this)">
                    <i class="fas fa-hand-holding-dollar me-2 text-primary"></i>Captação de Recursos
                </div>
            </div>

            <div id="dynamicFilters">
                <div class="row g-2 mb-3 filter-group" id="periodFilter">
                    <div class="col-6">
                        <label class="form-label small text-muted-sigat">Data Início</label>
                        <input type="date" id="dateStart" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted-sigat">Data Fim</label>
                        <input type="date" id="dateEnd" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="mb-3 d-none filter-group" id="classFilter">
                    <label class="form-label small text-muted-sigat">Turma</label>
                    <select id="selectClass" class="form-select form-select-sm"></select>
                </div>
                <div class="mb-3 d-none filter-group" id="projectFilter">
                    <label class="form-label small text-muted-sigat">Projeto</label>
                    <select id="selectProject" class="form-select form-select-sm"></select>
                </div>
            </div>

            <button class="btn btn-sigat w-100 mt-3" onclick="generateReport()">
                <i class="fas fa-bolt me-2"></i>Gerar Relatório
            </button>
        </div>
    </div>

    <div class="col-lg-8">
        <div id="reportPreviewPlaceholder"
            class="sigat-card d-flex flex-column align-items-center justify-content-center text-center p-5"
            style="border-style: dashed; border-width: 2px; min-height: 400px; background: rgba(255,255,255,0.02);">
            <i class="fas fa-file-pdf fa-4x mb-3 text-muted" style="opacity: 0.2;"></i>
            <h5 class="text-muted">Aguardando configuração...</h5>
            <p class="text-muted-sigat small">Escolha os parâmetros à esquerda e clique em "Gerar Relatório".</p>
        </div>
    </div>
</div>

<!-- Print area moved outside to avoid nesting issues with .no-print -->
<div id="reportPrintArea" class="d-none bg-white text-dark rounded-3 mt-4" style="min-height: 20cm; padding: 1.5cm;">
    <div class="report-header">
        <img id="print_logo" src="" style="max-height: 50px;">
        <div class="flex-grow-1">
            <h3 class="fw-bold mb-0" id="print_org_name">-</h3>
            <p class="mb-0 small text-muted" id="print_org_info">-</p>
        </div>
        <div class="text-end border-start ps-3">
            <div class="fw-bold text-uppercase small" style="letter-spacing: 1px;">Relatório Oficial</div>
            <div class="small" id="print_date">00/00/0000</div>
        </div>
    </div>

    <div id="reportContent"></div>

    <div class="mt-5 pt-4 border-top no-print d-flex gap-2">
        <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimir /
            PDF</button>
        <button class="btn btn-outline-secondary" onclick="resetReport()"><i class="fas fa-times me-2"></i>Limpar
            Busca</button>
    </div>
</div>

<script>
    let currentType = 'beneficiaries';
    let orgData = null;

    document.addEventListener('DOMContentLoaded', async () => {
        loadOrg();
        loadSelects();
        // Default period: current month
        const now = new Date();
        const first = new Date(now.getFullYear(), now.getMonth(), 1);
        document.getElementById('dateStart').value = first.toISOString().split('T')[0];
        document.getElementById('dateEnd').value = now.toISOString().split('T')[0];
    });

    async function loadOrg() {
        orgData = await apiCall('api/organization.php');
        if (orgData) {
            document.getElementById('print_org_name').textContent = orgData.name;
            document.getElementById('print_org_info').textContent = `${orgData.cnpj || ''} | ${orgData.email || ''} | ${orgData.phone || ''}`;
            document.getElementById('print_logo').src = orgData.logo_url ? ('api/file.php?path=' + orgData.logo_url) : 'assets/img/logo-placeholder.png';
        }
    }

    async function loadSelects() {
        const [classes, projects] = await Promise.all([
            apiCall('api/classes.php'),
            apiCall('api/projects.php')
        ]);
        const sClass = document.getElementById('selectClass');
        sClass.innerHTML = '<option value="">Todas as Turmas</option>' + classes.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

        const sProj = document.getElementById('selectProject');
        sProj.innerHTML = projects.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
    }

    function setReportType(type, el) {
        currentType = type;
        document.querySelectorAll('.report-type-card').forEach(c => c.classList.remove('active'));
        el.classList.add('active');

        document.querySelectorAll('.filter-group').forEach(g => g.classList.add('d-none'));

        if (['finance', 'attendance', 'fundraising'].includes(type)) document.getElementById('periodFilter').classList.remove('d-none');
        if (type === 'attendance') document.getElementById('classFilter').classList.remove('d-none');
        if (['projects', 'portfolio'].includes(type)) document.getElementById('projectFilter').classList.remove('d-none');
    }

    async function generateReport() {
        if (!orgData) await loadOrg();
        const area = document.getElementById('reportPrintArea');
        const placeholder = document.getElementById('reportPreviewPlaceholder');
        const content = document.getElementById('reportContent');

        placeholder.classList.add('d-none');
        area.classList.remove('d-none');
        content.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Processando dados...</p></div>';

        document.getElementById('print_date').textContent = new Date().toLocaleDateString('pt-BR');
        const start = document.getElementById('dateStart').value;
        const end = document.getElementById('dateEnd').value;

        try {
            if (currentType === 'beneficiaries') {
                const data = await apiCall('api/beneficiaries.php');
                content.innerHTML = renderBeneficiaries(data);
            } else if (currentType === 'finance') {
                const data = await apiCall('api/transactions.php');
                const filtered = data.filter(t => t.date >= start && t.date <= end);
                content.innerHTML = renderFinance(filtered, start, end);
            } else if (currentType === 'attendance') {
                const classId = document.getElementById('selectClass').value;
                const [attendances, beneficiaries, classes] = await Promise.all([
                    apiCall('api/attendances.php'),
                    apiCall('api/beneficiaries.php'),
                    apiCall('api/classes.php')
                ]);
                let filtered = attendances.filter(a => a.date >= start && a.date <= end);
                if (classId) filtered = filtered.filter(a => a.class_id === classId);
                content.innerHTML = renderAttendance(filtered, beneficiaries, classes, start, end);
            } else if (currentType === 'projects') {
                const projId = document.getElementById('selectProject').value;
                const projects = await apiCall('api/projects.php');
                const p = projects.find(x => x.id === projId);
                const lessons = await apiCall('api/lesson_plans.php');
                const projLessons = lessons.filter(l => l.project_id === projId);
                content.innerHTML = renderProject(p, projLessons);
            } else if (currentType === 'portfolio') {
                const projId = document.getElementById('selectProject').value;
                const portfolio = await apiCall('api/portfolio.php');
                const item = portfolio.find(x => x.project_id === projId);
                content.innerHTML = renderPortfolio(item);
            } else if (currentType === 'fundraising') {
                const data = await apiCall('api/fundraising.php');
                const filtered = data.filter(f => f.created_at >= start && f.created_at <= end);
                content.innerHTML = renderFundraising(filtered, start, end);
            }
        } catch (e) {
            content.innerHTML = `<div class="alert alert-danger">Erro operacional: ${e.message}</div>`;
        }
    }

    function renderBeneficiaries(data) {
        return `
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-1">Listagem Geral de Beneficiários</h2>
                <div class="text-muted small">Censo Institucional — ${data.length} atendidos</div>
            </div>
            <table class="table table-bordered table-sm align-middle">
                <thead class="bg-light small">
                    <tr><th>Cód</th><th>Nome</th><th>Nascimento</th><th>Responsável</th><th>PCD</th><th>Raça/Cor</th></tr>
                </thead>
                <tbody class="small">
                    ${data.map(b => `
                        <tr>
                            <td class="text-center">${b.id}</td>
                            <td class="fw-bold">${b.name}</td>
                            <td class="text-center">${formatDate(b.birth_date)}</td>
                            <td>${b.responsible_name || '-'}</td>
                            <td class="text-center text-uppercase">${b.is_pcd ? 'Sim' : 'Não'}</td>
                            <td class="text-center">${b.race_color || '-'}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    }

    function renderFinance(data, start, end) {
        if (!Array.isArray(data) || data.length === 0) {
            return '<div class="alert alert-info text-center py-5">Nenhuma transação financeira encontrada para o período selecionado.</div>';
        }
        
        const incomeList = data.filter(t => t.type === 'RECEITA');
        const expenseList = data.filter(t => t.type === 'DESPESA');
        
        const incomeTotal = incomeList.reduce((s, t) => s + parseFloat(t.value || 0), 0);
        const expenseTotal = expenseList.reduce((s, t) => s + parseFloat(t.value || 0), 0);
        const balance = incomeTotal - expenseTotal;

        let html = `
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-1">Demonstrativo de Movimentação Financeira</h2>
                <div class="text-muted small">Período de ${formatDate(start)} a ${formatDate(end)}</div>
            </div>
            
            <div class="row g-0 mb-5 border rounded overflow-hidden">
                <div class="col-4 p-3 border-end text-center bg-light">
                    <small class="text-muted d-block uppercase fw-bold" style="font-size:10px">Total de Receitas</small>
                    <span class="fs-5 fw-bold text-success">${formatCurrency(incomeTotal)}</span>
                </div>
                <div class="col-4 p-3 border-end text-center bg-light">
                    <small class="text-muted d-block uppercase fw-bold" style="font-size:10px">Total de Débitos</small>
                    <span class="fs-5 fw-bold text-danger">${formatCurrency(expenseTotal)}</span>
                </div>
                <div class="col-4 p-3 text-center bg-light">
                    <small class="text-muted d-block uppercase fw-bold" style="font-size:10px">Saldo do Período</small>
                    <span class="fs-5 fw-bold ${balance >= 0 ? 'text-primary' : 'text-danger'}">${formatCurrency(balance)}</span>
                </div>
            </div>

            <div class="mb-5">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-success">
                    <i class="fas fa-plus-circle me-2"></i>Receitas (Entradas)
                </h5>
                <table class="table table-sm table-striped border">
                    <thead class="bg-success text-white small">
                        <tr>
                            <th width="150">Data de Recebimento</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th width="120" class="text-end">Valor</th>
                            <th width="100">Status</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        ${incomeList.length ? incomeList.map(t => `
                            <tr>
                                <td>${formatDate(t.date)}</td>
                                <td class="fw-bold">${t.description}</td>
                                <td>${t.category}</td>
                                <td class="text-end fw-bold text-success">${formatCurrency(t.value)}</td>
                                <td>${t.status}</td>
                            </tr>
                        `).join('') : '<tr><td colspan="5" class="text-center text-muted py-3">Nenhuma receita identificada.</td></tr>'}
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-danger">
                    <i class="fas fa-minus-circle me-2"></i>Débitos (Saídas)
                </h5>
                <table class="table table-sm table-striped border">
                    <thead class="bg-danger text-white small">
                        <tr>
                            <th width="150">Data de Pagamento</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th width="120" class="text-end">Valor</th>
                            <th width="100">Status</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        ${expenseList.length ? expenseList.map(t => `
                            <tr>
                                <td>${formatDate(t.date)}</td>
                                <td class="fw-bold">${t.description}</td>
                                <td>${t.category}</td>
                                <td class="text-end fw-bold text-danger">${formatCurrency(t.value)}</td>
                                <td>${t.status}</td>
                            </tr>
                        `).join('') : '<tr><td colspan="5" class="text-center text-muted py-3">Nenhum débito identificado.</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;
        return html;
    }

    function renderAttendance(attendances, beneficiaries, classes, start, end) {
        if (!Array.isArray(attendances) || attendances.length === 0) {
            return '<div class="alert alert-info text-center py-5">Nenhum registro de frequência encontrado para os critérios selecionados.</div>';
        }

        // Grouping by class
        const classGrouped = {};
        attendances.forEach(a => {
            if (!classGrouped[a.class_id]) classGrouped[a.class_id] = {};
            const group = classGrouped[a.class_id];
            const records = a.records || {};

            if (Array.isArray(records)) {
                records.forEach(r => {
                    const bid = r.beneficiary_id;
                    if (!bid) return;
                    if (!group[bid]) group[bid] = { p: 0, a: 0 };
                    if (r.status === 'Presente' || r.status === 'P') group[bid].p++;
                    else group[bid].a++;
                });
            } else {
                Object.entries(records).forEach(([bid, status]) => {
                    if (!group[bid]) group[bid] = { p: 0, a: 0 };
                    if (status === 'P' || status === 'Presente') group[bid].p++;
                    else group[bid].a++;
                });
            }
        });

        let html = `
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-1">Controle de Frequência e Assiduidade</h2>
                <div class="text-muted small">Período: ${formatDate(start)} — ${formatDate(end)}</div>
            </div>
        `;

        Object.keys(classGrouped).forEach(cid => {
            const className = classes.find(c => c.id === cid)?.name || `Turma ${cid}`;
            const freq = classGrouped[cid];

            html += `
                <div class="mb-4">
                    <h5 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-chalkboard-teacher me-2 text-sigat"></i>Turma: ${className}</h5>
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="bg-light small">
                            <tr><th>Beneficiário</th><th width="80">Presente</th><th width="80">Ausente</th><th width="100">Assiduidade</th><th>Desempenho Visual</th></tr>
                        </thead>
                        <tbody class="small">
                            ${Object.keys(freq).map(bid => {
                const b = Array.isArray(beneficiaries) ? beneficiaries.find(x => x.id === bid) : null;
                const total = freq[bid].p + freq[bid].a;
                const perc = total > 0 ? (freq[bid].p / total * 100).toFixed(1) : 0;
                return `
                                    <tr>
                                        <td class="fw-bold">${b ? b.name : 'Ex-aluno (' + bid + ')'}</td>
                                        <td class="text-center">${freq[bid].p}</td>
                                        <td class="text-center">${freq[bid].a}</td>
                                        <td class="text-center fw-bold text-primary">${perc}%</td>
                                        <td class="p-2">
                                            <div class="progress" style="height: 12px; border-radius: 6px;">
                                                <div class="progress-bar ${perc >= 75 ? 'bg-success' : (perc >= 50 ? 'bg-warning' : 'bg-danger')}" style="width: ${perc}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                `;
            }).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        });

        html += `
            <div class="mt-4 small p-3 bg-light rounded text-muted">
                <strong>Critério de Assiduidade:</strong> Consideramos 75% o mínimo recomendado para certificação e aproveitamento pedagógico pleno.
            </div>
        `;

        return html;
    }

    function renderProject(p, lessons) {
        if (!p) return '<div class="alert alert-warning">Selecione um projeto válido.</div>';
        return `
            <div class="text-center mb-4 border-bottom pb-3">
                <h2 class="fw-bold mb-1">Dossiê de Projeto</h2>
                <h4 class="text-uppercase text-primary">${p.name}</h4>
            </div>
            <div class="mb-4">
                <h6 class="fw-bold text-muted text-uppercase mb-2" style="font-size:12px">Objetivo Geral</h6>
                <p class="text-justify">${p.general_objective || 'N/A'}</p>
            </div>
            <div class="mb-4">
                <h6 class="fw-bold text-muted text-uppercase mb-2" style="font-size:12px">Metodologia Aplicada</h6>
                <p class="text-justify">${p.methodology || 'N/A'}</p>
            </div>
            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:12px">Planejamento de Aulas / Módulos</h6>
            ${lessons.length ? `
                <table class="table table-bordered table-sm small">
                    <thead class="bg-light"><tr><th>Mês/Ref</th><th>Objetivo Específico</th></tr></thead>
                    <tbody>${lessons.map(l => `<tr><td class="fw-bold">${l.month}</td><td>${l.objective}</td></tr>`).join('')}</tbody>
                </table>
            ` : '<p class="small text-muted italic">Nenhum plano de aula cadastrado para este projeto.</p>'}
        `;
    }

    function renderPortfolio(item) {
        if (!item) return '<div class="alert alert-warning text-center">Nenhum registro de portfólio disponível para este projeto.</div>';

        let photos = [];
        let results = [];
        try {
            photos = Array.isArray(item.photos) ? item.photos : JSON.parse(item.photos_json || '[]');
            results = Array.isArray(item.results) ? item.results : JSON.parse(item.results_json || '[]');
        } catch (e) { console.error("Error parsing portfolio JSON", e); }

        if (!Array.isArray(photos)) photos = [];
        if (!Array.isArray(results)) results = [];

        return `
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-1">Portfólio Institucional de Impacto</h2>
                <div class="text-muted small">Ano de Referência: ${item.year} — ${item.location || ''}</div>
            </div>
            <div class="mb-4">
                <h6 class="fw-bold text-muted text-uppercase mb-2" style="font-size:12px">Resumo do Impacto Social</h6>
                <p>${item.description}</p>
            </div>
            <div class="mb-4">
                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:12px">Evidências Fotográficas</h6>
                <div class="row g-3">
                    ${photos.map(p => `<div class="col-4"><img src="${p}" class="img-fluid rounded border shadow-sm" style="height:140px; width:100%; object-fit:cover;"></div>`).join('')}
                </div>
            </div>
            <div class="mb-4">
                <h6 class="fw-bold text-muted text-uppercase mb-2" style="font-size:12px">Principais Indicadores de Sucesso</h6>
                <div class="row g-2">
                    ${results.map(r => `<div class="col-6"><div class="p-2 border rounded small"><i class="fas fa-check text-success me-2"></i> ${r}</div></div>`).join('')}
                </div>
            </div>
        `;
    }

    function renderFundraising(data, start, end) {
        if (!Array.isArray(data) || data.length === 0) {
            return '<div class="alert alert-info text-center py-5">Nenhum registro de captação encontrado para o período.</div>';
        }
        return `
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-1">Relatórios de Captação e Parcerias</h2>
                <div class="text-muted small">Acompanhamento de Editais: ${formatDate(start)} a ${formatDate(end)}</div>
            </div>
            <table class="table table-bordered align-middle table-sm">
                <thead class="bg-dark text-white small">
                    <tr><th>Iniciativa</th><th>Financiador</th><th>Status</th><th>Valor Previsto</th><th>Realizado</th></tr>
                </thead>
                <tbody class="small">
                    ${data.map(f => `
                        <tr>
                            <td class="fw-bold">${f.title}</td>
                            <td>${f.funder}</td>
                            <td><span class="badge ${f.status === 'Aprovado' ? 'bg-success' : 'bg-primary'} font-monospace" style="font-size:9px">${f.status}</span></td>
                            <td class="text-end">${formatCurrency(f.requested_value)}</td>
                            <td class="text-end fw-bold text-success">${formatCurrency(f.total_value)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    }

    function resetReport() {
        document.getElementById('reportPrintArea').classList.add('d-none');
        document.getElementById('reportPreviewPlaceholder').classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>