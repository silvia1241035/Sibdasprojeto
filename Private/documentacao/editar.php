<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 col-lg-2 text-white p-3 min-vh-100 d-none d-lg-block">
                <div class="sidebar">
                    <h4 class="menu-title">Menu</h4>
                    <nav class="menu-items">
                        <a href="../Index.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-chart-line fa-fw"></i>  Dashboard</a>
                        <a href="../gestaoconteudos.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-sitemap fa-fw"></i>  Gestão de conteúdos</a>
                        <a href="../equipamentos/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-laptop-medical fa-fw"></i>Equipamentos</a>
                        <a href="../localizacao/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-map-location-dot fa-fw"></i> Localização</a> 
                        <a href="../fornecedores/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-truck fa-fw"></i>Fornecedores</a>
                        <a href="listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-file-medical fa-fw"></i> Documentação</a>                 
                        <a href="../garantiacontrato/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-file-contract fa-fw"></i>   Garantias e Contratos</a>
                    </nav>
                </div>
            </aside>
        </div>
    </div>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-regular fa-pen fa-1x mb-3"></i> Atualizar documento</strong></h2>
                    <hr>

                    <!-- enctype="multipart/form-data" é necessário para o upload de ficheiros.-->
                    <form action="#" method="post" enctype="multipart/form-data" novalidate id="formDocumento">

                        <!-- Área de erros -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao atualizar o documento. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Tipo de documento + Nome -->
                        <!-- PHP: marca com selected a opção atual e preenche os value dos inputs -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo de documento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <select class="form-select" id="tipo" name="tipo_documento" required>
                                    <option value="">Selecione...</option>
                                    <option value="Manual de utilizador">Manual de utilizador</option>
                                    <option value="Manual de serviço">Manual de serviço</option>
                                    <option value="Certificado de calibração">Certificado de calibração</option>
                                    <option value="Contrato de manutenção">Contrato de manutenção</option>
                                    <option value="Fatura/Guia de aquisição">Fatura/Guia de aquisição</option>
                                    <option value="Declaração de conformidade">Declaração de conformidade</option>
                                    <option value="Relatório técnico">Relatório técnico</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o tipo de documento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome do documento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome_documento" required placeholder="Ex: Manual de utilizador do monitor X">
                                <div class="invalid-feedback">Por favor, insira o nome do documento.</div>
                            </div>
                        </div>

                        <!-- Linha 2: Data do documento + Data de validade -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="data" class="form-label">Data do documento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="date" class="form-control" id="data" name="data_documento" required>
                                <div class="invalid-feedback">Por favor, insira a data do documento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="validade" class="form-label">Data de validade</label>
                                <input type="date" class="form-control" id="validade" name="validade_documento">
                                <div class="form-text">Preencher apenas quando aplicável (ex: certificados que expiram).</div>
                            </div>
                        </div>

                        <!-- Linha 3: Equipamento associado + Fornecedor associado -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="equipamento" class="form-label">Equipamento associado<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <!-- PHP gera as opções a partir dos equipamentos registados -->
                                <select class="form-select" id="equipamento" name="equipamento_documento" required>
                                    <option value="">Selecione...</option>
                                    <option value="1">04.002.00 - Monitor Multiparamétrico</option>
                                    <option value="2">04.003.00 - Ventilador Pulmonar</option>
                                    <option value="3">04.004.00 - Desfibrilhador</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o equipamento associado.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="fornecedor" class="form-label">Fornecedor associado</label>
                                <!-- Opcional ("se necessário"). PHP gera as opções a partir dos fornecedores registados -->
                                <select class="form-select" id="fornecedor" name="fornecedor_documento">
                                    <option value="">Nenhum / Selecione...</option>
                                    <option value="Philips">Philips</option>
                                    <option value="Dräger">Dräger</option>
                                    <option value="B. Braun">B. Braun</option>
                                    <option value="Zoll">Zoll</option>
                                    <option value="GE Healthcare">GE Healthcare</option>
                                    <option value="Tuttnauer">Tuttnauer</option>
                                </select>
                            </div>
                        </div>

                        <!-- Linha 4: Ficheiro -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="ficheiro" class="form-label">Ficheiro</label>
                                <!-- Mostra o ficheiro atual (PHP preenche o nome e o link) -->
                                <div class="mb-2">
                                    <i class="fa-solid fa-paperclip me-1" style="color:#0077a8;"></i>
                                    Ficheiro atual:
                                    <a href="#" target="_blank" style="color:#0077a8;">[nome_do_ficheiro_atual]</a>
                                </div>
                                <input type="file" class="form-control" id="ficheiro" name="ficheiro_documento">
                                <div class="form-text">Escolha um novo ficheiro apenas se quiser substituir o atual.</div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 border-top">
                            <small class="text-muted">
                                <span class="text-danger">*</span> campos obrigatórios
                            </small>
                            <div class="d-flex gap-2">
                                <a href="listar.html" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
                                    <i class="fa-regular fa-floppy-disk me-1"></i> Guardar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </main>


    <!-- Menu Mobile -->
    <div class="offcanvas offcanvas-start text-white" style="background-color:#0077a8;" id="menuMobile">
        <div class="offcanvas-header d-flex flex-column gap-4">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column gap-4">
            <a href="../Index.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-chart-line"></i>Dashboard</a>
            <a href="../gestaoconteudos.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-sitemap"></i>Gestão de conteúdos</a>
            <a href="../equipamentos/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-laptop-medical"></i>Equipamentos</a>
            <a href="../localizacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-map-location-dot"></i>Localização</a>
            <a href="../fornecedores/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-truck"></i>Fornecedores</a>
            <a href="listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-file-medical"></i>Documentação</a>
            <a href="../garantiacontrato/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-file-contract"></i>Garantias e Contratos</a>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
