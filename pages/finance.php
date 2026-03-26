<!-- Financeiro Page -->
<div class="row g-4 mb-4 animate-in">
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
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <div class="d-flex gap-2">
        <button class="btn btn-sm badge-status badge-info" onclick="filterTx('all')">Todos</button>
        <button class="btn btn-sm badge-status badge-active" onclick="filterTx('RECEITA')">Receitas</button>
        <button class="btn btn-sm badge-status badge-danger" onclick="filterTx('DESPESA')">Despesas</button>
    </div>
    <button class="btn btn-sigat" onclick="openTxModal()"><i class="fas fa-plus me-2"></i>Nova Transação</button>
</div>
<div class="sigat-card p-0 overflow-hidden animate-in">
    <div class="table-responsive">
        <table class="sigat-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Categoria</th>
                    <th>Valor</th>
                    <th>Método</th>
                    <th>Data</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="txBody">
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted-sigat">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="txModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-wallet me-2 text-sigat"></i>Nova Transação</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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
<script>
    const incCats = ['Doações', 'Editais e Financiamentos', 'Eventos', 'Parcerias', 'Venda de Produtos', 'Contribuições Voluntárias'];
    const expCats = ['Alimentação', 'Transporte', 'Material Pedagógico', 'Manutenção da Sede', 'Recursos Humanos', 'Comunicação e Marketing', 'Eventos', 'Equipamentos'];
    let allTx = []; const txModal = new bootstrap.Modal(document.getElementById('txModal'));
    document.addEventListener('DOMContentLoaded', () => { updateTxCats(); loadTx(); });
    function toggleRecurrence() { document.getElementById('recurrence_div').classList.toggle('d-none', !document.getElementById('tx_recurring').checked); }
    function updateTxCats() { const t = document.getElementById('tx_type').value; const sel = document.getElementById('tx_cat'); sel.innerHTML = (t === 'RECEITA' ? incCats : expCats).map(c => `<option>${c}</option>`).join(''); }
    async function loadTx() { const d = await apiCall('api/transactions.php'); allTx = d || []; renderTx(allTx); updateStats(); }
    function updateStats() { const inc = allTx.filter(t => t.type === 'RECEITA').reduce((s, t) => s + parseFloat(t.value || 0), 0); const exp = allTx.filter(t => t.type === 'DESPESA').reduce((s, t) => s + parseFloat(t.value || 0), 0); const bal = inc - exp; document.getElementById('fIncome').textContent = formatCurrency(inc); document.getElementById('fExpense').textContent = formatCurrency(exp); document.getElementById('fBalance').textContent = formatCurrency(bal); document.getElementById('fBalance').style.color = bal >= 0 ? '#4ade80' : '#f87171'; }
    function renderTx(list) {
        const b = document.getElementById('txBody'); if (!list.length) { b.innerHTML = '<tr><td colspan="9"><div class="empty-state py-4"><i class="fas fa-wallet"></i><h5>Nenhuma transação</h5></div></td></tr>'; return; }
        b.innerHTML = list.map(t => {
            const isRec = parseInt(t.is_recurring) ? '<i class="fas fa-sync-alt ms-1 text-info" title="Recorrente: ' + t.recurrence_period + '"></i>' : '';
            return `<tr><td class="fw-medium text-white">${t.description} ${isRec}</td><td><span class="badge badge-status ${t.type === 'RECEITA' ? 'badge-active' : 'badge-danger'}">${t.type}</span></td><td>${t.category || '-'}</td><td class="fw-bold ${t.type === 'RECEITA' ? 'text-success' : 'text-danger'}">${formatCurrency(t.value)}</td><td>${t.payment_method || '-'}</td><td>${formatDate(t.date)}</td><td class="${t.status === 'Vencido' ? 'text-danger fw-bold' : ''}">${t.due_date ? formatDate(t.due_date) : '-'}</td><td><span class="badge badge-status ${t.status === 'Pago' ? 'badge-active' : t.status === 'Pendente' ? 'badge-warning' : 'badge-danger'}">${t.status}</span></td><td><button class="btn-icon btn-icon-delete" onclick="delTx('${t.id}')"><i class="fas fa-trash"></i></button></td></tr>`;
        }).join('');
    }
    function filterTx(type) { renderTx(type === 'all' ? allTx : allTx.filter(t => t.type === type)); }
    function openTxModal() { ['tx_desc', 'tx_value', 'tx_date', 'tx_due_date', 'tx_obs'].forEach(i => document.getElementById(i).value = ''); document.getElementById('tx_date').value = new Date().toISOString().split('T')[0]; document.getElementById('tx_recurring').checked = false; toggleRecurrence(); txModal.show(); }
    async function saveTx() { const p = { description: document.getElementById('tx_desc').value, type: document.getElementById('tx_type').value, category: document.getElementById('tx_cat').value, value: document.getElementById('tx_value').value, payment_method: document.getElementById('tx_method').value, date: document.getElementById('tx_date').value, due_date: document.getElementById('tx_due_date').value || null, is_recurring: document.getElementById('tx_recurring').checked ? 1 : 0, recurrence_period: document.getElementById('tx_recurring').checked ? document.getElementById('tx_recurrence_period').value : null, status: document.getElementById('tx_status').value, observations: document.getElementById('tx_obs').value }; if (!p.description || !p.value) { alert('Descrição e valor obrigatórios'); return; } const r = await apiCall('api/transactions.php', 'POST', p); if (r) { txModal.hide(); showToast('Transação criada!'); loadTx(); } }
    async function delTx(id) { if (!confirmDelete()) return; await apiCall('api/transactions.php?id=' + id, 'DELETE'); showToast('Removida'); loadTx(); }
</script>