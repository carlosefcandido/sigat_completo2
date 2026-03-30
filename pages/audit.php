<!-- Auditoria Page -->
<div class="mb-4 animate-in">
    <h6 class="text-white fw-bold mb-3"><i class="fas fa-history me-2 text-sigat"></i>Log de Auditoria</h6>
    <div class="d-flex gap-3 flex-wrap align-items-end mb-3">
        <div>
            <label class="form-label small text-muted-sigat mb-1">Ação / Tipo de Registro</label>
            <select class="form-select form-select-sm" id="auditFilter" style="background:var(--sigat-surface);border-color:var(--sigat-border);color:var(--sigat-text);border-radius:8px;min-width:150px;">
                <option value="">Todas ações</option>
                <option value="AUTH_LOGIN">Login</option>
                <option value="AUTH_LOGOUT">Logout</option>
                <option value="BENEFICIARY_CREATE">Beneficiário Criado</option>
                <option value="BENEFICIARY_UPDATE">Beneficiário Editado</option>
                <option value="CLASS_CREATE">Turma Criada</option>
                <option value="PROJECT_CREATE">Projeto Criado</option>
                <option value="FINANCE_CREATE">Transação Criada</option>
                <option value="UPLOAD">Doc Enviado</option>
                <option value="USER_CREATE">Usuário Criado</option>
            </select>
        </div>
        <div>
            <label class="form-label small text-muted-sigat mb-1">Usuário / Responsável</label>
            <div class="position-relative">
                <i class="fas fa-search position-absolute text-muted" style="left:10px;top:50%;transform:translateY(-50%);font-size:12px;"></i>
                <input type="text" class="form-control form-control-sm" id="auditUserName" placeholder="Buscar por nome..." style="background:var(--sigat-surface);border-color:var(--sigat-border);color:var(--sigat-text);border-radius:8px;padding-left:30px;min-width:180px;">
            </div>
        </div>
        <div class="d-flex gap-2">
            <div>
                <label class="form-label small text-muted-sigat mb-1">Data Inicial</label>
                <input type="date" class="form-control form-control-sm" id="auditDateStart" style="background:var(--sigat-surface);border-color:var(--sigat-border);color:var(--sigat-text);border-radius:8px;color-scheme:dark;">
            </div>
            <div>
                <label class="form-label small text-muted-sigat mb-1">Data Final</label>
                <input type="date" class="form-control form-control-sm" id="auditDateEnd" style="background:var(--sigat-surface);border-color:var(--sigat-border);color:var(--sigat-text);border-radius:8px;color-scheme:dark;">
            </div>
        </div>
        <div class="ms-auto pb-1 d-flex gap-2">
            <button class="btn btn-sm btn-sigat rounded-3" onclick="filterAudit()">
                <i class="fas fa-search me-1"></i>Procurar
            </button>
            <button class="btn btn-sm text-light rounded-3" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);" onclick="clearAuditFilters()">
                <i class="fas fa-eraser me-1"></i>Limpar
            </button>
        </div>
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
                    <td colspan="5" class="text-center py-4 text-muted-sigat">Utilize os filtros acima e clique em "Procurar"</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => { 
        // Não renderizar os registros na carga inicial
        document.getElementById('auditBody').innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted-sigat"><i class="fas fa-search fa-2x mb-3 text-muted" style="opacity:0.3"></i><br>Informe os parâmetros de busca e clique em Procurar.</td></tr>';
    });
    
    function renderAudit(list) {
        const b = document.getElementById('auditBody'); 
        if (!list || !list.length) { 
            b.innerHTML = '<tr><td colspan="5"><div class="empty-state py-4"><i class="fas fa-history"></i><p class="mt-2 mb-0">Nenhum registro encontrado</p></div></td></tr>'; 
            return; 
        }
        b.innerHTML = list.map(l => { 
            const d = new Date(l.created_at); 
            return `<tr><td style="white-space:nowrap;">${d.toLocaleDateString('pt-BR')} ${d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}</td><td>${l.user_name || '-'}</td><td><span class="badge badge-status badge-info">${l.action}</span></td><td>${l.entity_type ? `${l.entity_type}:${l.entity_id || ''}` : '-'}</td><td class="text-muted-sigat">${l.details || '-'}</td></tr>`; 
        }).join('');
    }

    async function filterAudit() { 
        const actionFilter = document.getElementById('auditFilter').value;
        const dateStart = document.getElementById('auditDateStart').value;
        const dateEnd = document.getElementById('auditDateEnd').value;
        const userNameInfo = document.getElementById('auditUserName').value.trim();
        
        let queryParams = [];
        if (actionFilter) queryParams.push('action=' + encodeURIComponent(actionFilter));
        if (dateStart) queryParams.push('date_start=' + encodeURIComponent(dateStart));
        if (dateEnd) queryParams.push('date_end=' + encodeURIComponent(dateEnd));
        if (userNameInfo) queryParams.push('user_name=' + encodeURIComponent(userNameInfo));

        const queryString = queryParams.length ? '?' + queryParams.join('&') : '';
        
        // Estado de "Carregando"
        document.getElementById('auditBody').innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted-sigat"><i class="fas fa-spinner fa-spin fa-2x mb-3 text-sigat"></i><br>Buscando no banco de dados...</td></tr>';
        
        const results = await apiCall('api/audit.php' + queryString); 
        renderAudit(results || []);
    }

    function clearAuditFilters() {
        document.getElementById('auditFilter').value = '';
        document.getElementById('auditDateStart').value = '';
        document.getElementById('auditDateEnd').value = '';
        document.getElementById('auditUserName').value = '';
        // Limpa a tela novamente
        document.getElementById('auditBody').innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted-sigat"><i class="fas fa-search fa-2x mb-3 text-muted" style="opacity:0.3"></i><br>Informe os parâmetros de busca e clique em Procurar.</td></tr>';
    }
</script>