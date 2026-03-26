<!-- Gestão de Usuários Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 animate-in">
    <h6 class="text-white mb-0"><i class="fas fa-user-plus me-2 text-sigat"></i>Gestão de Usuários</h6>
    <button class="btn btn-sigat" onclick="openUserModal()"><i class="fas fa-plus me-2"></i>Novo Usuário</button>
</div>
<div class="sigat-card p-0 overflow-hidden animate-in">
    <div class="table-responsive">
        <table class="sigat-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th>Último Login</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="usrBody">
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted-sigat">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sigat-modal">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fas fa-user-plus me-2 text-sigat"></i>Novo Usuário</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Nome *</label><input type="text" class="form-control"
                            id="usr_name"></div>
                    <div class="col-12"><label class="form-label">Email *</label><input type="email"
                            class="form-control" id="usr_email"></div>
                    <div class="col-md-6"><label class="form-label">Senha *</label><input type="password"
                            class="form-control" id="usr_pass"></div>
                    <div class="col-md-6"><label class="form-label">Perfil</label><select class="form-select"
                            id="usr_role">
                            <option value="PROFESSOR">Professor</option>
                            <option value="COORDENAÇÃO">Coordenação</option>
                            <option value="FINANCEIRO">Financeiro</option>
                            <option value="ADMIN">Administrador</option>
                        </select></div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button class="btn btn-sigat" onclick="saveUser()"><i
                        class="fas fa-save me-2"></i>Salvar</button></div>
        </div>
    </div>
</div>
<script>
    let userModal;
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('userModal');
        if (el) userModal = new bootstrap.Modal(el);
        loadUsers();
    });
    async function loadUsers() {
        const d = await apiCall('api/users.php'); if (!d) return;
        const b = document.getElementById('usrBody');
        const roleBadge = r => r === 'ADMIN' ? 'badge-danger' : r === 'COORDENAÇÃO' ? 'badge-info' : r === 'FINANCEIRO' ? 'badge-warning' : 'badge-active';
        b.innerHTML = d.map(u => `<tr><td class="fw-medium text-white">${u.nome}</td><td>${u.email}</td><td><span class="badge badge-status ${roleBadge(u.perfil)}">${u.perfil}</span></td><td>${u.ativo == 1 ? '<span class="badge badge-status badge-active">Ativo</span>' : '<span class="badge badge-status badge-slate">Inativo</span>'}</td><td>${u.last_login ? formatDate(u.last_login) : '-'}</td><td>${u.perfil !== 'ADMIN' ? `<button class="btn-icon btn-icon-delete" onclick="toggleUser(${u.id},${u.ativo})"><i class="fas fa-${u.ativo == 1 ? 'ban' : 'check'}"></i></button>` : ''}</td></tr>`).join('');
    }
    function openUserModal() { ['usr_name', 'usr_email', 'usr_pass'].forEach(i => document.getElementById(i).value = ''); userModal.show(); }
    async function saveUser() { const p = { nome: document.getElementById('usr_name').value, email: document.getElementById('usr_email').value, senha: document.getElementById('usr_pass').value, perfil: document.getElementById('usr_role').value }; if (!p.nome || !p.email || !p.senha) { alert('Preencha todos os campos'); return; } const r = await apiCall('api/users.php', 'POST', p); if (r) { userModal.hide(); showToast('Usuário criado!'); loadUsers(); } }
    async function toggleUser(id, active) { const r = await apiCall('api/users.php?id=' + id, 'PUT', { ativo: active == 1 ? 0 : 1 }); if (r) { showToast('Status atualizado'); loadUsers(); } }
</script>