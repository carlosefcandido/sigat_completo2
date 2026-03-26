<!-- Lixeira Page -->
<div class="mb-4 animate-in">
    <h6 class="text-white fw-bold mb-3"><i class="fas fa-trash-alt me-2 text-sigat"></i>Itens Excluídos</h6>
    <ul class="nav sigat-tabs">
        <li class="nav-item"><a class="nav-link active" href="#" onclick="showTrashTab('benef',this)">Beneficiários</a>
        </li>
        <li class="nav-item"><a class="nav-link" href="#" onclick="showTrashTab('docs',this)">Documentos</a></li>
    </ul>
</div>
<div id="trashBenef" class="sigat-card p-0 overflow-hidden animate-in">
    <div class="table-responsive">
        <table class="sigat-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Excluído em</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody id="trashBenefBody">
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted-sigat">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div id="trashDocs" class="sigat-card p-0 overflow-hidden d-none animate-in">
    <div class="table-responsive">
        <table class="sigat-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Categoria</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody id="trashDocsBody">
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted-sigat">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', loadTrash);
    function showTrashTab(tab, el) { document.querySelectorAll('.sigat-tabs .nav-link').forEach(l => l.classList.remove('active')); el.classList.add('active'); document.getElementById('trashBenef').classList.toggle('d-none', tab !== 'benef'); document.getElementById('trashDocs').classList.toggle('d-none', tab !== 'docs'); }
    async function loadTrash() {
        const [b, d] = await Promise.all([apiCall('api/beneficiaries.php?deleted=1'), apiCall('api/documents.php?deleted=1')]);
        const bb = document.getElementById('trashBenefBody');
        if (!b || !b.length) { bb.innerHTML = '<tr><td colspan="4"><div class="empty-state py-3"><p class="mb-0 text-muted-sigat">Lixeira vazia</p></div></td></tr>'; }
        else { bb.innerHTML = b.map(x => `<tr><td>${x.id}</td><td>${x.name}</td><td>${formatDate(x.updated_at)}</td><td><button class="btn btn-sigat-outline btn-sm" onclick="restoreBenef('${x.id}')"><i class="fas fa-undo me-1"></i>Restaurar</button></td></tr>`).join(''); }
        const db = document.getElementById('trashDocsBody');
        if (!d || !d.length) { db.innerHTML = '<tr><td colspan="3"><div class="empty-state py-3"><p class="mb-0 text-muted-sigat">Lixeira vazia</p></div></td></tr>'; }
        else { db.innerHTML = d.map(x => `<tr><td>${x.title}</td><td>${x.category}</td><td><button class="btn btn-sigat-outline btn-sm" onclick="restoreDoc('${x.id}')"><i class="fas fa-undo me-1"></i>Restaurar</button></td></tr>`).join(''); }
    }
    async function restoreBenef(id) { await apiCall('api/beneficiaries.php?id=' + id, 'PUT', { is_deleted: 0 }); showToast('Restaurado!'); loadTrash(); }
    async function restoreDoc(id) { await apiCall('api/documents.php?id=' + id, 'PUT', { is_deleted: 0 }); showToast('Restaurado!'); loadTrash(); }
</script>