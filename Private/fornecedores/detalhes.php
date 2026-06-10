<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 col-lg-2 text-white p-3 min-vh-100 d-none d-lg-block">
                <div class="sidebar">
                    <h4 class="menu-title">Menu</h4>   

                    <nav class="menu-items">
                        <a href="../Index.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-chart-line"></i>  Dashboard</a>
                        <a href="../gestaoconteudos.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-sitemap"></i>  Gestão de conteúdos</a>
                        <a href="../equipamentos/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-laptop-medical"></i>  Equipamentos</a>
                        <a href="../localizacao/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-map-location-dot"></i>  Localização</a> 
                        <a href="listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-truck"></i>  Fornecedores</a>
                        <a href="../documentacao/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-file-medical"></i>  Documentação</a>                 
                        <a href="../garantiacontrato/listar.html" class="menu-link" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-contract"></i>  Garantias e     Contratos</a>
                    </nav>
                </div>    
            </aside>
        </div>
    </div>

    <main class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-center mt-4">
                <div class="card w-100 shadow rounded" style="max-width: 900px;">
                    <div class="card-body">
                        
                        <h2 class="mb-4">
                            <strong><i class="fa-solid fa-truck fa-1x mb-3"></i> Detalhes do Fornecedor</strong> </h2>
                        <hr>
 
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do fornecedor</label>
                            <p class="form-control-plaintext">[Nome do Fornecedor]</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIF</label>
                            <p class="form-control-plaintext">[NIF do Fornecedor]</p>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Contacto Telefónico</label>
                                <p class="form-control-plaintext">[Contacto Telefónico]</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Email</label>
                                <p class="form-control-plaintext">[Email do Fornecedor]</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Website</label>
                                <p class="form-control-plaintext">[Website do Fornecedor]</p>
                            </div>
                        </div>
                        <div class="row mb-3">    
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Morada</label>
                                <p class="form-control-plaintext">[Morada do Fornecedor]</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipo de fornecedor</label>
                                <p class="form-control-plaintext">[Tipo de fornecedor]</p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Pessoa de Contacto</label>
                                <p class="form-control-plaintext">[Pessoa de Contacto]</p>
                            </div>
                        
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Telefone da pessoa de contacto</label>
                                <p class="form-control-plaintext">[Telefone da pessoa de contacto]</p>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Observações</label>
                                    <p class="form-control-plaintext">[Observações]</p>
                                </div>
                            </div>
                        </div>

                    </div>    
                    <div class="d-flex justify-content-end">
                        <a href="listar.html" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </main>






    <div class="offcanvas offcanvas-start text-white" style="background-color:#0077a8;" id="menuMobile">
        <div class="offcanvas-header d-flex flex-column gap-4">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="offcanvas" style="background-color: #0077a8;"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column gap-4">
            <a href="../Index.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-chart-line"></i>  Dashboard</a>
            <a href="../gestaoconteudos.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-sitemap"></i>  Gestão de conteúdos</a>
            <a href="../equipamentos/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-gears"></i>  Gestão de Equipamentos</a>
            <a href="../localizacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-map-location-dot"></i>  Localização</a>
            <a href="listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-truck"></i>  Gestão de Fornecedores</a>
            <a href="../documentacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-medical"></i>  Gestão de Documentação</a>
            <a href="../garantiacontrato/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-contract"></i>  Garantias e Contratos</a>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
