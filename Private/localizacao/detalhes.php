<?php include '../includes/header.php'; ?>

<body class="dashboard-page">
    
    <header class="container-fluid text-dark topbar fixed-top w-100" style="background-color: #f5f7fa; border-bottom: 2px solid #0077a8;">
        <div class="row align-items-center justify-content-between">
            <div class="col-6 d-flex aligh-items-center p-3">
            
            <a href="index.html">
                <img alt="Logo do InveMed" height="50" src="../../assets/img/logo.png"   class="me-3"> 
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
                            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-key me-2" style="color: #0077a8;"></i>Alterar password</a></li> <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../login/login.html"><i class="fa-solid fa-right-from-bracket me-2" style="color: #0077a8;"></i>Sair</a></li> </ul>
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
                        <a href="../Index.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-chart-line"></i>  Dashboard</a>
                        <a href="../gestaoconteudos.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-sitemap"></i>  Gestão de conteúdos</a>
                        <a href="../equipamentos/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-laptop-medical"></i>  Equipamentos</a>
                        <a href="listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-map-location-dot"></i>  Localização</a> 
                        <a href="../fornecedores/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-truck"></i>  Fornecedores</a>
                        <a href="../documentacao/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-file-medical"></i>  Documentação</a>                 
                        <a href="../garantiacontrato/listar.html" class="menu-link" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-contract"></i>  Garantias e     Contratos</a>
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
                        <strong><i class="fa-solid fa-map-location-dot fa-1x mb-3"></i> Detalhes da Localização</strong>
                    </h2>
                    <hr>
 
                    <!-- Linha 1: Edifício + Piso -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Edifício</label>
                            <p class="form-control-plaintext">[Edifício]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Piso</label>
                            <p class="form-control-plaintext">[Piso]</p>
                        </div>
                    </div>
 
                    <!-- Linha 2: Serviço/Departamento + Sala/Gabinete -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Serviço/Departamento</label>
                            <p class="form-control-plaintext">[Serviço/Departamento]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sala/Gabinete</label>
                            <p class="form-control-plaintext">[Sala/Gabinete]</p>
                        </div>
                    </div>
 
                    <hr class="my-4">
 
                    <!-- Equipamentos nesta localização (a relação) -->
                    <h5 class="mb-3">
                        <i class="fa-solid fa-laptop-medical me-2" style="color:#0077a8;"></i>
                        Equipamentos nesta localização
                        <span class="badge bg-primary ms-1">[Nº]</span>
                    </h5>
 
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Designação</th>
                                    <th>Marca / Modelo</th>
                                    <th>Estado</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--
                                    PHP gera estas linhas a partir dos equipamentos
                                    associados a esta localização.
                                    Se não houver nenhum, mostra a mensagem em baixo.
                                -->
                                <tr>
                                    <td>[Código]</td>
                                    <td>[Designação]</td>
                                    <td>[Marca/Modelo]</td>
                                    <td>[Estado]</td>
                                    <td class="text-center">
                                        <a href="../equipamentos/detalhes.html" class="text-decoration-none" style="color:#0077a8;" title="Ver equipamento">
                                            <i class="fa-solid fa-eye me-1"></i>Consultar
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Mostrar isto quando a localização não tem equipamentos -->
                        <p class="text-center text-muted mt-3 d-none" id="semEquipamentos">
                            <i class="fa-solid fa-circle-info me-2"></i>Esta localização não tem equipamentos associados.
                        </p>
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





    <div class="offcanvas offcanvas-start text-white" style="background-color:#0077a8;" id="menuMobile">
        <div class="offcanvas-header d-flex flex-column gap-4">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="offcanvas" style="background-color: #0077a8;"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column gap-4">
            <a href="../Index.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-chart-line"></i>  Dashboard</a>
            <a href="../gestaoconteudos.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-sitemap"></i>  Gestão de conteúdos</a>
            <a href="../equipamentos/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-gears"></i>  Gestão de Equipamentos</a>
            <a href="listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-map-location-dot"></i>  Localização</a>
            <a href="../fornecedores/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-truck"></i>  Gestão de Fornecedores</a>
            <a href="../documentacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-medical"></i>  Gestão de Documentação</a>
            <a href="../garantiacontrato/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-contract"></i>  Garantias e Contratos</a>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
