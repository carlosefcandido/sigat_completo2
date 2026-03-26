<!-- Auditoria Page -->
<div class="mb-4 animate-in">
    <h6 class="text-white fw-bold mb-3"><i class="fas fa-history me-2 text-sigat"></i>Log de Auditoria</h6>
    <div class="d-flex gap-2 flex-wrap">
        <select class="form-select form-select-sm" id="auditFilter"
            style="background:var(--sigat-surface);border-color:var(--sigat-border);color:var(--sigat-text);border-radius:10px;width:auto;"
            onchange="filterAudit()">
            <option value="">Todas ações</option>
            <option value="AUTH_LOGIN">Login</option>
            <option value="AUTH_LOGOUT">Logout</option>
            <option value="BENEFICIARY_CREATE">Beneficiário Criado</option>
            <option value="CLASS_CREATE">Turma Criada</option>
            <option value="PROJECT_CREATE">Projeto Criado</option>
            <option value="FINANCE_CREATE">Transação Criada</option>
            <option value="UPLOAD">Doc Enviado</option>
            <option value="USER_CREATE">Usuário Criado</option>
        </select>
    </div>
</div>
<div class="sigat-card p-0 overflow-hidden animate-in">
    <div class="table-responsive">
        <table class="sigat-table">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Entidade</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody id="auditBody">
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted-sigat">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
    let allAudit = [];
    document.addEventListener('DOMContentLoaded', async () => { const d = await apiCall('api/audit.php'); allAudit = d || []; renderAudit(allAudit); });
    function renderAudit(list) {
        const b = document.getElementById('auditBody'); if (!list.length) { b.innerHTML = '<tr><td colspan="5"><div class="empty-state py-4"><i class="fas fa-history"></i><p class="mt-2 mb-0">Nenhum registro</p></div></td></tr>'; return; }
        b.innerHTML = list.slice(0, 100).map(l => { const d = new Date(l.created_at); return `<tr><td style="white-space:nowrap;">${d.toLocaleDateString('pt-BR')} ${d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}</td><td>${l.user_name || '-'}</td><td><span class="badge badge-status badge-info">${l.action}</span></td><td>${l.entity_type ? `${l.entity_type}:${l.entity_id || ''}` : '-'}</td><td class="text-muted-sigat">${l.details || '-'}</td></tr>`; }).join('');
    }
    function filterAudit() { const f = document.getElementById('auditFilter').value; renderAudit(f ? allAudit.filter(l => l.action === f) : allAudit); }
</script>