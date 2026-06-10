<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

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

                            <h2 class="mb-4">
                                <strong><i class="fa-solid fa-file-contract fa-1x mb-3"></i> Detalhes da Garantia / Contrato</strong>
                            </h2>
                            <hr>

                            <!-- Linha 1: Equipamento + Entidade -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Equipamento associado</label>
                                    <p class="form-control-plaintext">[Equipamento associado]</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Entidade responsável</label>
                                    <p class="form-control-plaintext">[Entidade responsável]</p>
                                </div>
                            </div>

                            <!-- Linha 2: Início + Fim + Estado -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Início da garantia</label>
                                    <p class="form-control-plaintext">[Início da garantia]</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fim da garantia</label>
                                    <p class="form-control-plaintext">[Fim da garantia]</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Estado da garantia</label>
                                    <!-- PHP escolhe: badge-garantia-valida / badge-garantia-expirar / badge-garantia-expirada -->
                                    <p class="form-control-plaintext">
                                        <span class="badge badge-garantia-valida">[Estado]</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Linha 3: Contrato + Tipo + Periodicidade -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Contrato de manutenção</label>
                                    <p class="form-control-plaintext">[Sim/Não]</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo de contrato</label>
                                    <p class="form-control-plaintext">[Tipo de contrato]</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Periodicidade da manutenção</label>
                                    <p class="form-control-plaintext">[Periodicidade]</p>
                                </div>
                            </div>

                            <!-- Linha 4: Observações -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Observações</label>
                                    <p class="form-control-plaintext">[Observações]</p>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end p-3">
                            <a href="listar.html" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                            </a>
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
    
<?php include '../includes/footer.php'; ?>
