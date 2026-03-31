<!-- Financeiro Page -->
<ul class="nav sigat-tabs mb-4 animate-in">
    <li class="nav-item"><a class="nav-link active" href="#" onclick="showFinTab('transactions',this)">Transações</a></li>
    <li class="nav-item"><a class="nav-link" href="#" onclick="showFinTab('accounts',this)">Contas Bancárias</a></li>
</ul>

<!-- ═══════════════════════════════════════════ -->
<!-- TAB: TRANSAÇÕES                             -->
<!-- ═══════════════════════════════════════════ -->
<div id="finTransactionsTab" class="animate-in">
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="sigat-card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total Receitas</div>
                        <div class="stat-value" id="fIncome" style="color:#4ade80;font-size:22px;">-</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i
                            class="fas fa-arrow-up"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sigat-card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total Despesas</div>
                        <div class="stat-value" id="fExpense" style="color:#f87171;font-size:22px;">-</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i
                            class="fas fa-arrow-down"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sigat-card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Saldo</div>
                        <div class="stat-value" id="fBalance" style="font-size:22px;">-</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(20,184,166,0.15);color:#2dd4bf;"><i
                            class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-sm badge-status badge-info" onclick="filterTx('all')">Todos</button>
            <button class="btn btn-sm badge-status badge-active" onclick="filterTx('RECEITA')">Receitas</button>
            <button class="btn btn-sm badge-status badge-danger" onclick="filterTx('DESPESA')">Despesas</button>
        </div>
        <button class="btn btn-sigat" onclick="openTxModal()"><i class="fas fa-plus me-2"></i>Nova Transação</button>
    </div>
    <div class="sigat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="sigat-table">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Tipo</th>
                        <th>Categoria</th>
                        <th>Valor</th>
                        <th>Projeto</th>
                        <th>Conta</th>
                        <th>Método</th>
                        <th>Data</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="txBody">
                    <tr>
                        <td colspan="11" class="text-center py-4 text-muted-sigat">Carregando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════ -->
<!-- TAB: CONTAS BANCÁRIAS                       -->
<!-- ═══════════════════════════════════════════ -->
<div id="finAccountsTab" class="d-none animate-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h6 class="text-white mb-0"><i class="fas fa-university me-2 text-sigat"></i>Contas Bancárias</h6>
        <button class="btn btn-sigat" onclick="openBaModal()"><i class="fas fa-plus me-2"></i>Nova Conta</button>
    </div>
    <div class="row g-4" id="baCards">
        <div class="col-12 text-center py-4 text-muted-sigat">Carregando...</div>
    </div>
</div>

<!-- ═══════════════════════════════════════════ -->
<!-- MODAL: NOVA / EDITAR TRANSAÇÃO              -->
<!-- ═══════════════════════════════════════════ -->
<div class="modal fade" id="txModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="txModalTitle"><i class="fas fa-wallet me-2 text-sigat"></i><span id="txModalTitleText">Nova Transação</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tx_id">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Descrição *</label><input type="text"
                            class="form-control" id="tx_desc"></div>
                    <div class="col-md-6"><label class="form-label">Tipo</label><select class="form-select" id="tx_type"
                            onchange="updateTxCats()">
                            <option value="RECEITA">Receita</option>
                            <option value="DESPESA">Despesa</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Categoria</label><select class="form-select"
                            id="tx_cat"></select></div>
                    <div class="col-md-6"><label class="form-label">Valor *</label><input type="number"
                            class="form-control" id="tx_value" step="0.01"></div>
                    <div class="col-md-6"><label class="form-label">Método</label><select class="form-select"
                            id="tx_method">
                            <option>PIX</option>
                            <option>Transferência Bancária</option>
                            <option>Dinheiro</option>
                            <option>Cartão</option>
                            <option>Doação Recorrente</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Projeto</label><select class="form-select"
                            id="tx_project">
                            <option value="">Nenhum</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Conta Bancária</label><select class="form-select"
                            id="tx_bank_account">
                            <option value="">Nenhuma</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Data Transação</label><input type="date"
                            class="form-control" id="tx_date"></div>
                    <div class="col-md-6"><label class="form-label">Data Vencimento</label><input type="date"
                            class="form-control" id="tx_due_date"></div>
                    <div class="col-12">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="tx_recurring"
                                onchange="toggleRecurrence()">
                            <label class="form-check-label text-white" for="tx_recurring">Transação Recorrente</label>
                        </div>
                        <div id="recurrence_div" class="d-none animate-in">
                            <label class="form-label">Período de Recorrência</label>
                            <select class="form-select" id="tx_recurrence_period">
                                <option>Semanal</option>
                                <option selected>Mensal</option>
                                <option>Anual</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="form-label">Status</label><select class="form-select"
                            id="tx_status">
                            <option>Pendente</option>
                            <option>Pago</option>
                            <option>Vencido</option>
                        </select></div>
                    <div class="col-12"><label class="form-label">Observações</label><textarea class="form-control"
                            id="tx_obs" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="saveTx()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════ -->
<!-- MODAL: NOVA / EDITAR CONTA BANCÁRIA         -->
<!-- ═══════════════════════════════════════════ -->
<div class="modal fade" id="baModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="baModalTitle"><i class="fas fa-university me-2 text-sigat"></i><span id="baModalTitleText">Nova Conta Bancária</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ba_id">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Banco *</label><input type="text"
                            class="form-control" id="ba_bank_name" placeholder="Ex: Banco do Brasil"></div>
                    <div class="col-md-3"><label class="form-label">Agência</label><input type="text"
                            class="form-control" id="ba_agency" placeholder="0001"></div>
                    <div class="col-md-3"><label class="form-label">Conta *</label><input type="text"
                            class="form-control" id="ba_account_number" placeholder="12345-6"></div>
                    <div class="col-md-6"><label class="form-label">Tipo de Conta</label><select class="form-select"
                            id="ba_account_type">
                            <option>Corrente</option>
                            <option>Poupança</option>
                            <option>Pagamento</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Projeto Vinculado</label><select class="form-select"
                            id="ba_project">
                            <option value="">Nenhum</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Titular</label><input type="text"
                            class="form-control" id="ba_holder_name" placeholder="Nome do titular"></div>
                    <div class="col-md-6"><label class="form-label">CPF/CNPJ do Titular</label><input type="text"
                            class="form-control" id="ba_holder_document" placeholder="000.000.000-00"></div>
                    <div class="col-md-6"><label class="form-label">Chave PIX</label><input type="text"
                            class="form-control" id="ba_pix_key" placeholder="Email, telefone, CPF ou chave aleatória"></div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="ba_is_active" checked>
                            <label class="form-check-label text-white" for="ba_is_active">Conta Ativa</label>
                        </div>
                    </div>
                    <div class="col-12"><label class="form-label">Observações</label><textarea class="form-control"
                            id="ba_obs" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="saveBa()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════ -->
<!-- MODAL: VISUALIZAR CONTA BANCÁRIA            -->
<!-- ═══════════════════════════════════════════ -->
<div class="modal fade" id="baViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-eye me-2 text-sigat"></i>Detalhes da Conta Bancária</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="baViewBody"></div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <button class="btn btn-sigat" onclick="switchBaToEdit()"><i class="fas fa-edit me-2"></i>Editar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CONFIRMAÇÃO DE EXCLUSÃO CONTA -->
<div class="modal fade" id="baDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-trash me-2 text-danger"></i>Excluir Conta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-white mb-1">Tem certeza que deseja excluir esta conta bancária?</p>
                <p class="text-muted-sigat small mb-0" id="baDeleteInfo"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger" id="baDeleteConfirmBtn" onclick="confirmDeleteBa()"><i class="fas fa-trash me-2"></i>Excluir</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ── Constants ────────────────────────────────────────
    const incCats = ['Doações', 'Editais e Financiamentos', 'Eventos', 'Parcerias', 'Venda de Produtos', 'Contribuições Voluntárias'];
    const expCats = ['Alimentação', 'Transporte', 'Material Pedagógico', 'Manutenção da Sede', 'Recursos Humanos', 'Comunicação e Marketing', 'Eventos', 'Equipamentos'];

    // ── State ───────────────────────────────────────────
    let allTx = [], allBa = [], allFinProjects = [];
    let currentViewBa = null, baToDeleteId = null;

    // ── Modals ──────────────────────────────────────────
    const txModal = new bootstrap.Modal(document.getElementById('txModal'));
    const baModal = new bootstrap.Modal(document.getElementById('baModal'));
    const baViewModal = new bootstrap.Modal(document.getElementById('baViewModal'));
    const baDeleteModal = new bootstrap.Modal(document.getElementById('baDeleteModal'));

    // ── Init ────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        updateTxCats();
        loadFinanceData();
    });

    // Reset modals on close
    document.getElementById('txModal').addEventListener('hidden.bs.modal', () => {
        document.getElementById('tx_id').value = '';
        document.getElementById('txModalTitleText').textContent = 'Nova Transação';
        ['tx_desc', 'tx_value', 'tx_date', 'tx_due_date', 'tx_obs'].forEach(i => document.getElementById(i).value = '');
        document.getElementById('tx_project').value = '';
        document.getElementById('tx_bank_account').value = '';
        document.getElementById('tx_recurring').checked = false;
        toggleRecurrence();
    });
    document.getElementById('baModal').addEventListener('hidden.bs.modal', () => {
        document.getElementById('ba_id').value = '';
        document.getElementById('baModalTitleText').textContent = 'Nova Conta Bancária';
        ['ba_bank_name', 'ba_agency', 'ba_account_number', 'ba_holder_name', 'ba_holder_document', 'ba_pix_key', 'ba_obs'].forEach(i => document.getElementById(i).value = '');
        document.getElementById('ba_account_type').value = 'Corrente';
        document.getElementById('ba_project').value = '';
        document.getElementById('ba_is_active').checked = true;
    });

    // ── Tab navigation ──────────────────────────────────
    function showFinTab(tab, el) {
        document.querySelectorAll('.sigat-tabs .nav-link').forEach(l => l.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('finTransactionsTab').classList.toggle('d-none', tab !== 'transactions');
        document.getElementById('finAccountsTab').classList.toggle('d-none', tab !== 'accounts');
    }

    // ── Load all data ───────────────────────────────────
    async function loadFinanceData() {
        const [tx, ba, projects] = await Promise.all([
            apiCall('api/transactions.php'),
            apiCall('api/bank_accounts.php'),
            apiCall('api/projects.php')
        ]);
        allTx = tx || [];
        allBa = ba || [];
        allFinProjects = projects || [];

        populateProjectSelects();
        populateBankAccountSelect();
        renderTx(allTx);
        updateStats();
        renderBaCards();
    }

    function populateProjectSelects() {
        ['tx_project', 'ba_project'].forEach(selId => {
            const sel = document.getElementById(selId);
            sel.innerHTML = '<option value="">Nenhum</option>';
            allFinProjects.forEach(p => sel.innerHTML += `<option value="${p.id}">${p.name}</option>`);
        });
    }

    function populateBankAccountSelect() {
        const sel = document.getElementById('tx_bank_account');
        sel.innerHTML = '<option value="">Nenhuma</option>';
        allBa.filter(a => parseInt(a.is_active)).forEach(a => {
            const label = `${a.bank_name} — ${a.account_number}${a.project_name ? ' (' + a.project_name + ')' : ''}`;
            sel.innerHTML += `<option value="${a.id}">${label}</option>`;
        });
    }

    // ═══════════════════════════════════════════════════
    // TRANSAÇÕES
    // ═══════════════════════════════════════════════════

    function toggleRecurrence() { document.getElementById('recurrence_div').classList.toggle('d-none', !document.getElementById('tx_recurring').checked); }
    function updateTxCats() { const t = document.getElementById('tx_type').value; const sel = document.getElementById('tx_cat'); sel.innerHTML = (t === 'RECEITA' ? incCats : expCats).map(c => `<option>${c}</option>`).join(''); }

    function updateStats() {
        const inc = allTx.filter(t => t.type === 'RECEITA').reduce((s, t) => s + parseFloat(t.value || 0), 0);
        const exp = allTx.filter(t => t.type === 'DESPESA').reduce((s, t) => s + parseFloat(t.value || 0), 0);
        const bal = inc - exp;
        document.getElementById('fIncome').textContent = formatCurrency(inc);
        document.getElementById('fExpense').textContent = formatCurrency(exp);
        document.getElementById('fBalance').textContent = formatCurrency(bal);
        document.getElementById('fBalance').style.color = bal >= 0 ? '#4ade80' : '#f87171';
    }

    function renderTx(list) {
        const b = document.getElementById('txBody');
        if (!list.length) {
            b.innerHTML = '<tr><td colspan="11"><div class="empty-state py-4"><i class="fas fa-wallet"></i><h5>Nenhuma transação</h5></div></td></tr>';
            return;
        }
        b.innerHTML = list.map(t => {
            const isRec = parseInt(t.is_recurring) ? '<i class="fas fa-sync-alt ms-1 text-info" title="Recorrente: ' + t.recurrence_period + '"></i>' : '';
            const projBadge = t.project_name ? `<span class="badge bg-secondary bg-opacity-25 text-info" style="font-size:11px;">${t.project_name}</span>` : '<span class="text-muted-sigat">-</span>';
            const accLabel = t.bank_account_name ? `<span class="text-white" style="font-size:12px;">${t.bank_account_name}</span>` : '<span class="text-muted-sigat">-</span>';
            return `<tr>
                <td class="fw-medium text-white">${t.description} ${isRec}</td>
                <td><span class="badge badge-status ${t.type === 'RECEITA' ? 'badge-active' : 'badge-danger'}">${t.type}</span></td>
                <td>${t.category || '-'}</td>
                <td class="fw-bold ${t.type === 'RECEITA' ? 'text-success' : 'text-danger'}">${formatCurrency(t.value)}</td>
                <td>${projBadge}</td>
                <td>${accLabel}</td>
                <td>${t.payment_method || '-'}</td>
                <td>${formatDate(t.date)}</td>
                <td class="${t.status === 'Vencido' ? 'text-danger fw-bold' : ''}">${t.due_date ? formatDate(t.due_date) : '-'}</td>
                <td><span class="badge badge-status ${t.status === 'Pago' ? 'badge-active' : t.status === 'Pendente' ? 'badge-warning' : 'badge-danger'}">${t.status}</span></td>
                <td><button class="btn-icon btn-icon-delete" onclick="delTx('${t.id}')"><i class="fas fa-trash"></i></button></td>
            </tr>`;
        }).join('');
    }

    function filterTx(type) { renderTx(type === 'all' ? allTx : allTx.filter(t => t.type === type)); }

    function openTxModal() {
        document.getElementById('txModalTitleText').textContent = 'Nova Transação';
        ['tx_desc', 'tx_value', 'tx_date', 'tx_due_date', 'tx_obs'].forEach(i => document.getElementById(i).value = '');
        document.getElementById('tx_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('tx_project').value = '';
        document.getElementById('tx_bank_account').value = '';
        document.getElementById('tx_recurring').checked = false;
        toggleRecurrence();
        txModal.show();
    }

    async function saveTx() {
        const id = document.getElementById('tx_id').value;
        const isEdit = !!id;
        const p = {
            description: document.getElementById('tx_desc').value,
            type: document.getElementById('tx_type').value,
            category: document.getElementById('tx_cat').value,
            value: document.getElementById('tx_value').value,
            payment_method: document.getElementById('tx_method').value,
            project_id: document.getElementById('tx_project').value || null,
            bank_account_id: document.getElementById('tx_bank_account').value || null,
            date: document.getElementById('tx_date').value,
            due_date: document.getElementById('tx_due_date').value || null,
            is_recurring: document.getElementById('tx_recurring').checked ? 1 : 0,
            recurrence_period: document.getElementById('tx_recurring').checked ? document.getElementById('tx_recurrence_period').value : null,
            status: document.getElementById('tx_status').value,
            observations: document.getElementById('tx_obs').value
        };
        if (!p.description || !p.value) { alert('Descrição e valor obrigatórios'); return; }
        const r = await apiCall('api/transactions.php', 'POST', p);
        if (r) { txModal.hide(); showToast('Transação criada!'); loadFinanceData(); }
    }

    async function delTx(id) {
        if (!confirmDelete()) return;
        await apiCall('api/transactions.php?id=' + id, 'DELETE');
        showToast('Removida');
        loadFinanceData();
    }

    // ═══════════════════════════════════════════════════
    // CONTAS BANCÁRIAS
    // ═══════════════════════════════════════════════════

    function renderBaCards() {
        const c = document.getElementById('baCards');
        if (!allBa.length) {
            c.innerHTML = '<div class="col-12"><div class="empty-state py-5"><i class="fas fa-university"></i><h5>Nenhuma conta bancária</h5><p class="text-muted-sigat">Clique em "Nova Conta" para cadastrar</p></div></div>';
            return;
        }
        c.innerHTML = allBa.map(a => {
            const active = parseInt(a.is_active);
            const statusBadge = active
                ? '<span class="badge badge-status badge-active">Ativa</span>'
                : '<span class="badge badge-status badge-danger">Inativa</span>';
            return `<div class="col-md-6 col-xl-4">
                <div class="sigat-card h-100" style="${!active ? 'opacity:0.6;' : ''}">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white fw-bold mb-1"><i class="fas fa-university me-2 text-sigat"></i>${a.bank_name}</h6>
                            <small class="text-muted-sigat">Ag: ${a.agency || '-'} | Conta: ${a.account_number}</small>
                        </div>
                        ${statusBadge}
                    </div>
                    <div class="mb-2">
                        <small class="text-muted-sigat"><i class="fas fa-id-card me-1"></i> ${a.account_type}</small>
                    </div>
                    ${a.project_name ? `<div class="mb-2"><span class="badge bg-secondary bg-opacity-25 text-info" style="font-size:11px;"><i class="fas fa-bullseye me-1"></i>${a.project_name}</span></div>` : ''}
                    ${a.pix_key ? `<div class="mb-2"><small class="text-muted-sigat"><i class="fas fa-key me-1"></i> PIX: ${a.pix_key}</small></div>` : ''}
                    ${a.holder_name ? `<div class="mb-2"><small class="text-muted-sigat"><i class="fas fa-user me-1"></i> ${a.holder_name}</small></div>` : ''}
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-sm btn-outline-info" title="Visualizar" onclick="viewBa('${a.id}')"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-warning" title="Editar" onclick="editBa('${a.id}')"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Excluir" onclick="deleteBa('${a.id}')"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    function getBy(arr, id) { return arr.find(a => a.id === id); }

    // ── View ────────────────────────────────────────────
    function viewBa(id) {
        const a = getBy(allBa, id);
        if (!a) return;
        currentViewBa = a;
        const f = (label, value) => value ? `<div class="mb-3"><label class="form-label text-muted-sigat small mb-1">${label}</label><div class="text-white">${value}</div></div>` : '';
        document.getElementById('baViewBody').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">${f('Banco', a.bank_name)}</div>
                <div class="col-md-3">${f('Agência', a.agency || '-')}</div>
                <div class="col-md-3">${f('Conta', a.account_number)}</div>
                <div class="col-md-6">${f('Tipo de Conta', a.account_type)}</div>
                <div class="col-md-6">${f('Status', parseInt(a.is_active) ? '✅ Ativa' : '❌ Inativa')}</div>
                ${a.project_name ? `<div class="col-md-6">${f('Projeto Vinculado', a.project_name)}</div>` : ''}
                ${a.holder_name ? `<div class="col-md-6">${f('Titular', a.holder_name)}</div>` : ''}
                ${a.holder_document ? `<div class="col-md-6">${f('CPF/CNPJ', a.holder_document)}</div>` : ''}
                ${a.pix_key ? `<div class="col-md-6">${f('Chave PIX', a.pix_key)}</div>` : ''}
                ${a.observations ? `<div class="col-12">${f('Observações', a.observations)}</div>` : ''}
                <div class="col-12 border-top border-secondary pt-2 mt-1">
                    <small class="text-muted-sigat">Criado em: ${formatDate(a.created_at)}${a.created_by ? ' por ' + a.created_by : ''}</small>
                </div>
            </div>`;
        baViewModal.show();
    }

    function switchBaToEdit() {
        if (!currentViewBa) return;
        baViewModal.hide();
        setTimeout(() => editBa(currentViewBa.id), 350);
    }

    // ── Create / Edit ───────────────────────────────────
    function openBaModal() {
        document.getElementById('baModalTitleText').textContent = 'Nova Conta Bancária';
        baModal.show();
    }

    function editBa(id) {
        const a = getBy(allBa, id);
        if (!a) return;
        document.getElementById('ba_id').value = a.id;
        document.getElementById('ba_bank_name').value = a.bank_name || '';
        document.getElementById('ba_agency').value = a.agency || '';
        document.getElementById('ba_account_number').value = a.account_number || '';
        document.getElementById('ba_account_type').value = a.account_type || 'Corrente';
        document.getElementById('ba_project').value = a.project_id || '';
        document.getElementById('ba_holder_name').value = a.holder_name || '';
        document.getElementById('ba_holder_document').value = a.holder_document || '';
        document.getElementById('ba_pix_key').value = a.pix_key || '';
        document.getElementById('ba_is_active').checked = parseInt(a.is_active);
        document.getElementById('ba_obs').value = a.observations || '';
        document.getElementById('baModalTitleText').textContent = 'Editar Conta Bancária';
        baModal.show();
    }

    async function saveBa() {
        const id = document.getElementById('ba_id').value;
        const isEdit = !!id;
        const payload = {
            bank_name: document.getElementById('ba_bank_name').value,
            agency: document.getElementById('ba_agency').value,
            account_number: document.getElementById('ba_account_number').value,
            account_type: document.getElementById('ba_account_type').value,
            project_id: document.getElementById('ba_project').value || null,
            holder_name: document.getElementById('ba_holder_name').value,
            holder_document: document.getElementById('ba_holder_document').value,
            pix_key: document.getElementById('ba_pix_key').value,
            is_active: document.getElementById('ba_is_active').checked ? 1 : 0,
            observations: document.getElementById('ba_obs').value
        };
        if (!payload.bank_name || !payload.account_number) { alert('Banco e número da conta são obrigatórios'); return; }
        const url = isEdit ? `api/bank_accounts.php?id=${id}` : 'api/bank_accounts.php';
        const method = isEdit ? 'PUT' : 'POST';
        const r = await apiCall(url, method, payload);
        if (r) { baModal.hide(); showToast(isEdit ? 'Conta atualizada!' : 'Conta criada!'); loadFinanceData(); }
    }

    // ── Delete ──────────────────────────────────────────
    function deleteBa(id) {
        const a = getBy(allBa, id);
        if (!a) return;
        baToDeleteId = id;
        document.getElementById('baDeleteInfo').textContent = `${a.bank_name} — Conta: ${a.account_number}`;
        baDeleteModal.show();
    }

    async function confirmDeleteBa() {
        if (!baToDeleteId) return;
        const btn = document.getElementById('baDeleteConfirmBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Excluindo...';
        const r = await apiCall(`api/bank_accounts.php?id=${baToDeleteId}`, 'DELETE');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash me-2"></i>Excluir';
        if (r) {
            baDeleteModal.hide();
            showToast('Conta excluída!');
            baToDeleteId = null;
            loadFinanceData();
        }
    }
</script>