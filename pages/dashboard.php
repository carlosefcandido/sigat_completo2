<!-- Dashboard Page -->
<div class="row g-4 mb-4 animate-in">
    <?php if (in_array($userRole, ['ADMIN', 'COORDENAÇÃO'])): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="sigat-card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Beneficiários</div>
                        <div class="stat-value" id="statBenef">-</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-sm-6 col-xl-<?php echo in_array($userRole, ['ADMIN', 'COORDENAÇÃO']) ? '3' : '6'; ?>">
        <div class="sigat-card-stat">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Turmas</div>
                    <div class="stat-value" id="statClasses">-</div>
                </div>
                <div class="stat-icon" style="background:rgba(168,85,247,0.15);color:#c084fc;">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
        </div>
    </div>
    <?php if (in_array($userRole, ['ADMIN', 'COORDENAÇÃO'])): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="sigat-card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Projetos</div>
                        <div class="stat-value" id="statProj">-</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;">
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (in_array($userRole, ['ADMIN', 'FINANCEIRO'])): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="sigat-card-stat">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Saldo Financeiro</div>
                        <div class="stat-value" id="statBalance" style="font-size:22px;">-</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(20,184,166,0.15);color:#2dd4bf;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="row g-4 animate-in">
    <?php if (in_array($userRole, ['ADMIN', 'COORDENAÇÃO', 'FINANCEIRO'])): ?>
        <div class="col-lg-<?php echo in_array($userRole, ['ADMIN', 'FINANCEIRO']) ? '4' : '12'; ?>">
            <div class="sigat-card">
                <h6 class="text-white fw-bold mb-3"><i class="fas fa-file-lines me-2 text-sigat"></i>Documentos por Status
                </h6>
                <div class="row g-3" id="docStatusCards">
                    <div class="col-4">
                        <div class="rounded-3 p-3 text-center" style="background:rgba(34,197,94,0.1);">
                            <div class="fw-bold text-success fs-4" id="docActive">0</div>
                            <small class="text-muted-sigat">Ativos</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="rounded-3 p-3 text-center" style="background:rgba(245,158,11,0.1);">
                            <div class="fw-bold text-warning fs-4" id="docWarning">0</div>
                            <small class="text-muted-sigat">Vencendo</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="rounded-3 p-3 text-center" style="background:rgba(239,68,68,0.1);">
                            <div class="fw-bold text-danger fs-4" id="docExpired">0</div>
                            <small class="text-muted-sigat">Vencidos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (in_array($userRole, ['ADMIN', 'FINANCEIRO'])): ?>
        <div class="col-lg-4">
            <div class="sigat-card">
                <h6 class="text-white fw-bold mb-3"><i class="fas fa-money-bill-transfer me-2 text-sigat"></i>Transações
                    (Mês Atual)
                </h6>
                <div class="row g-2" id="txStatusCards">
                    <div class="col-6">
                        <div class="rounded-3 p-3 text-center" style="background:rgba(34,197,94,0.1);">
                            <div class="fw-bold text-success fs-4" id="statTxIncome">0</div>
                            <small class="text-muted-sigat">Receitas</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded-3 p-3 text-center" style="background:rgba(239,68,68,0.1);">
                            <div class="fw-bold text-danger fs-4" id="statTxExpense">0</div>
                            <small class="text-muted-sigat">Despesas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="sigat-card">
                <h6 class="text-white fw-bold mb-3"><i class="fas fa-chart-pie me-2 text-sigat"></i>Receitas vs Despesas
                </h6>
                <div id="finSummary">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted-sigat">Receitas</span>
                        <span class="text-success fw-bold" id="finIncome">R$ 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted-sigat">Despesas</span>
                        <span class="text-danger fw-bold" id="finExpense">R$ 0</span>
                    </div>
                    <hr style="border-color:var(--sigat-border);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-white fw-bold">Saldo</span>
                        <span class="fw-bold" id="finBalance" style="color:var(--sigat-primary);">R$ 0</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="row g-4 mt-0 animate-in">
    <?php if (in_array($userRole, ['ADMIN', 'FINANCEIRO'])): ?>
        <div class="col-12">
            <div class="sigat-card">
                <h6 class="text-white fw-bold mb-3"><i class="fas fa-clock me-2 text-sigat"></i>Próximos Vencimentos (7
                    dias)</h6>
                <div id="upcomingTxList">
                    <div class="empty-state py-4"><i class="fas fa-check-circle text-success"></i>
                        <p class="mb-0 mt-2">Nenhuma conta vencendo nos próximos 7 dias</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="row g-4 mt-0 animate-in">
    <div class="col-lg-6">
        <div class="sigat-card">
            <h6 class="text-white fw-bold mb-3"><i class="fas fa-calendar me-2 text-sigat"></i>Próximos Eventos (Agenda
                da Diretoria)</h6>
            <div id="upcomingEvents">
                <div class="empty-state py-4"><i class="fas fa-calendar-xmark"></i>
                    <p class="mb-0 mt-2">Nenhum evento agendado</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="sigat-card">
            <h6 class="text-white fw-bold mb-3"><i class="fas fa-clock-rotate-left me-2 text-sigat"></i>Atividade
                Recente</h6>
            <div id="recentActivity">
                <div class="empty-state py-4"><i class="fas fa-list"></i>
                    <p class="mb-0 mt-2">Nenhuma atividade recente</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const userRole = '<?php echo $userRole; ?>';

        // Define calls based on role
        const calls = [
            apiCall('api/classes.php'),
            apiCall('api/events.php').catch(() => [])
        ];

        if (['ADMIN', 'COORDENAÇÃO'].includes(userRole)) {
            calls.push(apiCall('api/beneficiaries.php').catch(() => []));
            calls.push(apiCall('api/projects.php').catch(() => []));
        }

        if (['ADMIN', 'FINANCEIRO'].includes(userRole)) {
            calls.push(apiCall('api/transactions.php').catch(() => []));
        }

        if (['ADMIN', 'COORDENAÇÃO', 'FINANCEIRO'].includes(userRole)) {
            calls.push(apiCall('api/documents.php').catch(() => []));
        }

        // Wait for all allowed calls
        const results = await Promise.all(calls);

        // Map results back (order depends on pushes above, let's use a more robust approach)
        const data = {
            classes: results[0],
            events: results[1],
            benefs: ['ADMIN', 'COORDENAÇÃO'].includes(userRole) ? results[2] : null,
            projects: ['ADMIN', 'COORDENAÇÃO'].includes(userRole) ? results[3] : null,
            transactions: ['ADMIN', 'FINANCEIRO'].includes(userRole) ? (['ADMIN', 'COORDENAÇÃO'].includes(userRole) ? results[4] : results[2]) : null,
            documents: ['ADMIN', 'COORDENAÇÃO', 'FINANCEIRO'].includes(userRole) ? results[results.length - 1] : null
        };

        if (data.benefs && document.getElementById('statBenef')) document.getElementById('statBenef').textContent = data.benefs.length;
        if (data.classes && document.getElementById('statClasses')) document.getElementById('statClasses').textContent = data.classes.length;
        if (data.projects && document.getElementById('statProj')) document.getElementById('statProj').textContent = data.projects.length;

        // Finance
        if (Array.isArray(data.transactions) && document.getElementById('statBalance')) {
            const income = data.transactions.filter(t => t.type === 'RECEITA').reduce((s, t) => s + parseFloat(t.value || 0), 0);
            const expense = data.transactions.filter(t => t.type === 'DESPESA').reduce((s, t) => s + parseFloat(t.value || 0), 0);
            const balance = income - expense;
            document.getElementById('statBalance').textContent = formatCurrency(balance);
            if (document.getElementById('finIncome')) {
                document.getElementById('finIncome').textContent = formatCurrency(income);
                document.getElementById('finExpense').textContent = formatCurrency(expense);
                document.getElementById('finBalance').textContent = formatCurrency(balance);
                document.getElementById('finBalance').style.color = balance >= 0 ? '#4ade80' : '#f87171';
            }
        }

        // Documents
        if (Array.isArray(data.documents) && document.getElementById('docActive')) {
            const active = data.documents.filter(d => d.status === 'Ativo e Regular').length;
            const warning = data.documents.filter(d => d.status === 'Próximo do Vencimento').length;
            const expired = data.documents.filter(d => d.status === 'Vencido').length;
            document.getElementById('docActive').textContent = active;
            document.getElementById('docWarning').textContent = warning;
            document.getElementById('docExpired').textContent = expired;
        }

        // Monthly transaction counts
        if (Array.isArray(data.transactions) && document.getElementById('statTxIncome')) {
            const now = new Date();
            const currentMonth = now.getMonth();
            const currentYear = now.getFullYear();

            const monthTx = data.transactions.filter(t => {
                const d = new Date(t.date);
                return d.getMonth() === currentMonth && d.getFullYear() === currentYear;
            });

            document.getElementById('statTxIncome').textContent = monthTx.filter(t => t.type === 'RECEITA').length;
            document.getElementById('statTxExpense').textContent = monthTx.filter(t => t.type === 'DESPESA').length;

            // Upcoming transactions (7 days)
            const d7 = new Date(); d7.setDate(d7.getDate() + 7);
            const today = new Date(); today.setHours(0, 0, 0, 0);

            const upcomingTx = data.transactions.filter(t => {
                if (!t.due_date || t.status === 'Pago') return false;
                const dv = new Date(t.due_date);
                return dv >= today && dv <= d7;
            }).sort((a, b) => new Date(a.due_date) - new Date(b.due_date)).slice(0, 5);

            if (upcomingTx.length > 0) {
                document.getElementById('upcomingTxList').innerHTML = `
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead><tr><th>Vencimento</th><th>Descrição</th><th>Valor</th><th>Status</th></tr></thead>
                            <tbody>
                                ${upcomingTx.map(t => `
                                    <tr>
                                        <td class="text-warning fw-bold">${formatDate(t.due_date)}</td>
                                        <td class="text-white">${t.description}</td>
                                        <td class="${t.type === 'RECEITA' ? 'text-success' : 'text-danger'} fw-bold">${formatCurrency(t.value)}</td>
                                        <td><span class="badge badge-status ${t.status === 'Pendente' ? 'badge-warning' : 'badge-danger'}">${t.status}</span></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }
        }

        // Upcoming events
        if (Array.isArray(data.events) && data.events.length > 0) {
            const upcoming = data.events.filter(e => e.status === 'AGENDADO').slice(0, 4);
            if (upcoming.length > 0) {
                document.getElementById('upcomingEvents').innerHTML = upcoming.map(e => `
                <div class="d-flex align-items-center gap-3 p-2 rounded-3 mb-2" style="background:rgba(255,255,255,0.03);">
                    <div class="stat-icon" style="width:40px;height:40px;background:rgba(168,85,247,0.15);color:#c084fc;font-size:14px;border-radius:10px;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-white fw-medium" style="font-size:14px;">${e.title}</div>
                        <small class="text-muted-sigat">${formatDate(e.date)} — ${e.location || ''}</small>
                    </div>
                    <span class="badge badge-status badge-info">Agendado</span>
                </div>
            `).join('');
            }
        }
    });
</script>