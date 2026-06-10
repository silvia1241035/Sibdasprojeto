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
                    <div class="card w-100 shadow rounded text-center p-4" style="max-width: 700px;">

                        <div class="text-warning display-4 mb-3">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <p class="mb-2 fs-5">Deseja eliminar este registo de garantia / contrato?</p>

                        <h4 class="mb-4"><strong>[Equipamento Associado]</strong></h4>

                        <div class="mb-4">
                            <span class="d-block mb-1"><i class="fa-solid fa-calendar-xmark me-2"></i>Fim da garantia: <strong>[Fim da Garantia]</strong></span>
                            <span class="d-block"><i class="fa-solid fa-building me-2"></i>Entidade: <strong>[Entidade Responsável]</strong></span>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="listar.html" class="btn btn-outline-secondary px-4">
                                <i class="fa-solid fa-xmark me-2"></i>Não
                            </a>
                            <a href="#" class="btn btn-danger px-4">
                                <i class="fa-solid fa-check me-2"></i>Sim
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
