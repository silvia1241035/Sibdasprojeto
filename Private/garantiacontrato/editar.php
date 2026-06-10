<?php include '../includes/header.php'; ?>

<body class="dashboard-page">

    <header class="container-fluid text-dark topbar fixed-top w-100" style="background-color: #f5f7fa; border-bottom: 2px solid #0077a8;">
        <div class="row align-items-center justify-content-between">
            <div class="col-6 d-flex align-items-center p-3">
                <a href="../index.html">
                    <img alt="Logo do InveMed" height="50" src="../../assets/img/logo.png" class="me-3">
                </a>
                <h2 class="mt-2">InveMed</h2>
                <button class="btn d-lg-none" style="color:#0077a8" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuMobile">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            <div class="col-6 text-end p-3 mb-3">
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #0077a8; border: 1px solid #0077a8; border-radius: 20px;">
                        <i class="fa-regular fa-user me-2"></i> Utilizador
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fa-solid fa-key me-2" style="color:#0077a8"></i>Alterar password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../login/login.html"><i class="fa-solid fa-right-from-bracket me-2" style="color:#0077a8"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 col-lg-2 text-white p-3 min-vh-100 d-none d-lg-block">
                <div class="sidebar">
                    <h4 class="menu-title">Menu</h4>
                    <nav class="menu-items">
                        <a href="../Index.html" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-chart-line fa-fw"></i>Dashboard</a>
                        <a href="../gestaoconteudos.html" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-sitemap fa-fw"></i>Gestão de conteúdos</a>
                        <a href="../equipamentos/listar.html" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-laptop-medical fa-fw"></i>Equipamentos</a>
                        <a href="../localizacao/listar.html" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-map-location-dot fa-fw"></i>Localização</a>
                        <a href="../fornecedores/listar.html" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-truck fa-fw"></i>Fornecedores</a>
                        <a href="../documentacao/listar.html" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-file-medical fa-fw"></i>Documentação</a>
                        <a href="listar.html" class="menu-link active" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-file-contract fa-fw"></i>Garantias e Contratos</a>
                    </nav>
                </div>
            </aside>
        </div>
    </div>        

            <main class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-center mt-4">
                    <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                        <div class="card-body">
                            <h2 class="mb-4"><strong><i class="fa-solid fa-pen fa-1x mb-3"></i> Atualizar garantia / contrato</strong></h2>
                            <hr>

                            <form action="#" method="post" novalidate id="formGarantia">

                                <!-- Área de erros -->
                                <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                                    Erro ao atualizar o registo. Por favor, tente novamente.
                                </div>

                                <!-- Linha 1: Equipamento + Entidade -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="equipamento" class="form-label">Equipamento associado<span class="text-danger" title="Campo obrigatório">*</span></label>
                                        <select class="form-select" id="equipamento" name="equipamento_garantia" required>
                                            <option value="">Selecione...</option>
                                            <option value="1">04.002.00 - Monitor Multiparamétrico</option>
                                            <option value="2">04.003.00 - Ventilador Pulmonar</option>
                                            <option value="3">04.004.00 - Desfibrilhador</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione o equipamento.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="entidade" class="form-label">Entidade responsável</label>
                                        <input type="text" class="form-control" id="entidade" name="entidade_garantia" placeholder="Ex: Philips Healthcare">
                                    </div>
                                </div>

                                <!-- Linha 2: Início + Fim da garantia -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="inicio" class="form-label">Data de início da garantia</label>
                                        <input type="date" class="form-control" id="inicio" name="inicio_garantia">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fim" class="form-label">Data de fim da garantia<span class="text-danger" title="Campo obrigatório">*</span></label>
                                        <input type="date" class="form-control" id="fim" name="fim_garantia" required>
                                        <div class="invalid-feedback">Por favor, insira a data de fim da garantia.</div>
                                    </div>
                                </div>

                                <!-- Linha 3: Contrato + Tipo + Periodicidade -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="contrato" class="form-label">Contrato de manutenção</label>
                                        <select class="form-select" id="contrato" name="contrato_garantia">
                                            <option value="">Selecione...</option>
                                            <option value="Sim">Sim</option>
                                            <option value="Não">Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="tipocontrato" class="form-label">Tipo de contrato</label>
                                        <select class="form-select" id="tipocontrato" name="tipocontrato_garantia">
                                            <option value="">Selecione...</option>
                                            <option value="Preventiva">Manutenção preventiva</option>
                                            <option value="Corretiva">Manutenção corretiva</option>
                                            <option value="Completa">Completa (preventiva + corretiva)</option>
                                            <option value="Outro">Outro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="periodicidade" class="form-label">Periodicidade</label>
                                        <select class="form-select" id="periodicidade" name="periodicidade_garantia">
                                            <option value="">Selecione...</option>
                                            <option value="Mensal">Mensal</option>
                                            <option value="Trimestral">Trimestral</option>
                                            <option value="Semestral">Semestral</option>
                                            <option value="Anual">Anual</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Linha 4: Observações -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="observacoes" class="form-label">Observações</label>
                                        <textarea class="form-control" id="observacoes" name="observacoes_garantia" rows="3" placeholder="Notas adicionais sobre a garantia ou o contrato..."></textarea>
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
        </div>
    </div>


    <!-- Menu Mobile -->
    <div class="offcanvas offcanvas-start text-white" style="background-color:#0077a8;" id="menuMobile">
        <div class="offcanvas-header d-flex flex-column gap-4">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column gap-4">
            <a href="../Index.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-chart-line fa-fw"></i>Dashboard</a>
            <a href="../gestaoconteudos.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-sitemap fa-fw"></i>Gestão de conteúdos</a>
            <a href="../equipamentos/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-laptop-medical fa-fw"></i>Equipamentos</a>
            <a href="../localizacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-map-location-dot fa-fw"></i>Localização</a>
            <a href="../fornecedores/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-truck fa-fw"></i>Fornecedores</a>
            <a href="../documentacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-file-medical fa-fw"></i>Documentação</a>
            <a href="listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover"><i class="fa-solid fa-file-contract fa-fw"></i>Garantias e Contratos</a>
        </div>
    </div>


    
    <script src="../../assets/bootstrap/1241035.js"></script>
    <script src="../../assets/js/1241035.js"></script>
</body>
</html>
