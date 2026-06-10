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
 
                    <h2 class="mb-4">
                        <strong><i class="fa-solid fa-file-medical fa-1x mb-3"></i> Detalhes do Documento</strong>
                    </h2>
                    <hr>
 
                    <!-- Linha 1: Tipo + Nome -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipo de documento</label>
                            <p class="form-control-plaintext">[Tipo de documento]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome do documento</label>
                            <p class="form-control-plaintext">[Nome do documento]</p>
                        </div>
                    </div>
 
                    <!-- Linha 2: Data + Validade -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data do documento</label>
                            <p class="form-control-plaintext">[Data do documento]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data de validade</label>
                            <!-- PHP escolhe o badge: badge-valido / badge-expirado / badge-semvalidade -->
                            <p class="form-control-plaintext">
                                <span class="badge badge-valido">[Validade]</span>
                            </p>
                        </div>
                    </div>
 
                    <!-- Linha 3: Equipamento associado + Fornecedor associado -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Equipamento associado</label>
                            <p class="form-control-plaintext">[Equipamento associado]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fornecedor associado</label>
                            <p class="form-control-plaintext">[Fornecedor associado]</p>
                        </div>
                    </div>
 
                    <!-- Linha 4: Ficheiro -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Ficheiro</label>
                            <p class="form-control-plaintext">
                                <!-- href para o caminho/link do ficheiro -->
                                <a href="#" target="_blank" style="color:#0077a8;text-decoration:none;">
                                    <i class="fa-solid fa-file-arrow-down me-1"></i>[Nome do ficheiro]
                                </a>
                            </p>
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
