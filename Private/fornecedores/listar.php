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

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">
                        <i class="fa-solid fa-list fa-1x mb-3"></i>
                        <strong>Listagem de Fornecedores</strong>
                    </h2>
                    <a href="inserir.html" class="btn" style="background-color: #0077a8; color:white;">
                        <i class="fa-solid fa-plus me-1"></i> Novo fornecedor
                    </a>
                </div>

                <div class="card p-3 mb-4 shadow-sm">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Pesquisar</label>
                            <input type="text" id="searchAll" class="form-control" placeholder="Nome, email, NIF...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ordenar por</label>
                            <select id="sortBy" class="form-select">
                                <option value="nome">Nome</option>
                                <option value="nif">NIF</option>
                                <option value="email">Email</option>
                                <option value="contacto">Contacto telefónico</option>
                                <option value="website">Website</option>
                                <option value="pessoa">Pessoa de contacto</option>
                                <option value="telefone">Telefone da pessoa de contacto</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>Nome da Empresa</th>
                                <th>NIF</th>
                                <th>Contacto telefónico</th>
                                <th>Email</th>
                                <th>Website</th>
                                <th>Pessoa de Contacto</th>
                                <th>Telefone da pessoa de contacto</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>

                        <tbody id="fornecedoresTable">
                            <tr
                                data-nome="[Nome da Empresa]"
                                data-nif="[NIF]"
                                data-email="[Email]"
                                data-telefone="[Contacto Telefónico]"
                                data-website="[Website]"
                                data-pessoa="[Pessoa de Contacto]"
                                data-telefone-pessoa="[Telefone da Pessoa de Contacto]">
                                <td >[Nome da Empresa]</td>
                                <td>[NIF]</td>
                                <td>[Contacto Telefónico]</td>
                                <td>[Email]</td>
                                <td>[Website]</td>
                                <td>[Pessoa de Contacto]</td>
                                <td>[Telefone da Pessoa de Contacto]</td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="detalhes.html" class="acao-tabela acao-consultar" title="Ver detalhes">
                                            <i class="fa-solid fa-eye me-1"></i>Consultar
                                        </a>
                                        <a href="editar.html" class="acao-tabela acao-editar" title="Editar">
                                            <i class="fa-regular fa-pen-to-square me-1"></i>Editar
                                        </a>
                                        <a href="apagar.html" class="acao-tabela acao-eliminar" title="Eliminar">
                                            <i class="fa-solid fa-trash-can me-1"></i>Eliminar
                                        </a>
                                    </div>
                                </td>    
                            </tr>
                        </tbody>

                    </table>
                    <p id="noResults" class="text-center text-muted mt-3" style="display: none;">
                        Nenhum fornecedor encontrado.
                    </p>
                </div>

            </main>

        </div>
    </div>
    


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
