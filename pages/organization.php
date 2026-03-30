<!-- Organização Page -->
<div class="sigat-card animate-in sigat-form">
        <h5 class="text-white fw-bold mb-4"><i class="fas fa-building me-2 text-sigat"></i>Perfil da Organização</h5>
        <div class="row g-4 align-items-center mb-4">
                <div class="col-md-3 text-center">
                        <div class="mb-3">
                                <img id="logo_preview" src="assets/img/logo-placeholder.png"
                                        class="img-thumbnail rounded-3"
                                        style="max-height:150px; background:rgba(255,255,255,0.05); border-color:rgba(255,255,255,0.1);">
                        </div>
                        <button class="btn btn-sm btn-outline-light"
                                onclick="document.getElementById('logo_input').click()">
                                <i class="fas fa-upload me-1"></i> Alterar Logo
                        </button>
                        <input type="file" id="logo_input" class="d-none" accept="image/*"
                                onchange="handleLogoUpload(this)">
                        <input type="hidden" id="org_logo_url">
                </div>
                <div class="col-md-9">
                        <div class="row g-3" id="orgForm">
                                <div class="col-md-8"><label class="form-label">Nome</label><input type="text"
                                                class="form-control" id="org_name"></div>
                                <div class="col-md-4"><label class="form-label">CNPJ</label><input type="text"
                                                class="form-control" id="org_cnpj"></div>
                                <div class="col-md-4"><label class="form-label">Ano de Fundação</label><input
                                                type="text" class="form-control" id="org_year"></div>
                                <div class="col-md-4"><label class="form-label">Email</label><input type="email"
                                                class="form-control" id="org_email"></div>
                                <div class="col-md-4"><label class="form-label">Telefone</label><input type="text"
                                                class="form-control" id="org_phone"></div>
                                <div class="col-12"><label class="form-label">Endereço</label><input type="text"
                                                class="form-control" id="org_address"></div>
                                <div class="col-md-6"><label class="form-label">Território de Atuação</label><input
                                                type="text" class="form-control" id="org_territory"></div>
                                <div class="col-md-6"><label class="form-label">Público-Alvo</label><input type="text"
                                                class="form-control" id="org_audience"></div>
                                <div class="col-md-3"><label class="form-label">Nº Beneficiários</label><input
                                                type="text" class="form-control" id="org_bcount"></div>
                                <div class="col-md-3"><label class="form-label">Tamanho da Equipe</label><input
                                                type="text" class="form-control" id="org_team"></div>
                                <div class="col-md-6"><label class="form-label">Missão</label><textarea
                                                class="form-control" id="org_mission" rows="2"></textarea></div>
                                <div class="col-md-6"><label class="form-label">Visão</label><textarea
                                                class="form-control" id="org_vision" rows="2"></textarea></div>
                                <div class="col-md-6"><label class="form-label">Valores</label><textarea
                                                class="form-control" id="org_values" rows="2"></textarea></div>
                                <div class="col-md-6"><label class="form-label">Histórico</label><textarea
                                                class="form-control" id="org_history" rows="2"></textarea></div>
                                <div class="col-12"><button class="btn btn-sigat" onclick="saveOrg()"><i
                                                        class="fas fa-save me-2"></i>Salvar
                                                Alterações</button></div>
                        </div>
                </div>
        </div>

        <script>
                document.addEventListener('DOMContentLoaded', async () => {
                        const org = await apiCall('api/organization.php');
                        if (org) {
                                document.getElementById('org_logo_url').value = org.logo_url || '';
                                if (org.logo_url) document.getElementById('logo_preview').src = 'api/file.php?path=' + org.logo_url;
                                document.getElementById('org_name').value = org.name || '';
                                document.getElementById('org_cnpj').value = org.cnpj || '';
                                document.getElementById('org_year').value = org.foundation_year || '';
                                document.getElementById('org_email').value = org.email || '';
                                document.getElementById('org_phone').value = org.phone || '';
                                document.getElementById('org_address').value = org.address || '';
                                document.getElementById('org_territory').value = org.territory || '';
                                document.getElementById('org_audience').value = org.audience || '';
                                document.getElementById('org_bcount').value = org.beneficiaries_count || '';
                                document.getElementById('org_team').value = org.team_size || '';
                                document.getElementById('org_mission').value = org.mission || '';
                                document.getElementById('org_vision').value = org.vision || '';
                                document.getElementById('org_values').value = org.org_values || '';
                                document.getElementById('org_history').value = org.history || '';
                        }
                });

                async function handleLogoUpload(input) {
                        if (!input.files || !input.files[0]) return;
                        const formData = new FormData();
                        formData.append('file', input.files[0]);

                        try {
                                const res = await fetch('api/upload.php', { method: 'POST', body: formData });
                                const data = await res.json();
                                if (data.url) {
                                        document.getElementById('org_logo_url').value = data.url;
                                        document.getElementById('logo_preview').src = 'api/file.php?path=' + data.url;
                                        showToast('Logo carregada!');
                                } else {
                                        alert(data.error || 'Erro no upload');
                                }
                        } catch (err) { alert('Erro de conexão'); }
                }

                async function saveOrg() {
                        const payload = {
                                name: document.getElementById('org_name').value,
                                logo_url: document.getElementById('org_logo_url').value,
                                cnpj: document.getElementById('org_cnpj').value,
                                foundation_year: document.getElementById('org_year').value,
                                email: document.getElementById('org_email').value,
                                phone: document.getElementById('org_phone').value,
                                address: document.getElementById('org_address').value,
                                territory: document.getElementById('org_territory').value,
                                audience: document.getElementById('org_audience').value,
                                beneficiaries_count: document.getElementById('org_bcount').value,
                                team_size: document.getElementById('org_team').value,
                                mission: document.getElementById('org_mission').value,
                                vision: document.getElementById('org_vision').value,
                                org_values: document.getElementById('org_values').value,
                                history: document.getElementById('org_history').value
                        };
                        const result = await apiCall('api/organization.php', 'PUT', payload);
                        if (result) showToast('Organização atualizada!');
                }
        </script>