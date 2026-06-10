<?php include 'includes/header.php'; ?>

<body class="dashboard-page">
    
    <header class="container-fluid text-dark topbar fixed-top w-100" style="background-color: #f5f7fa; border-bottom: 2px solid #0077a8;">
        <div class="row align-items-center">
            <div class="col-6 d-flex aligh-items-center p-3">
            
            <a href="index.html">
                <img alt="Logo do InveMed" height="50" src="../assets/img/logo.png"   class="me-3"> 
            </a>
            <h2 class="mt-2">InveMed</h2>
            </div>
            
            <div class="col-6 text-end p-3 mb-3">
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #0077a8; border: 1px solid #0077a8; border-radius: 20px;">
                        <i class="fa-regular fa-user me-2"></i> Utilizador
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fa-solid fa-key me-2" style="color: #0077a8;"></i>Alterar password</a></li> <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="login/login.html"><i class="fa-solid fa-right-from-bracket me-2" style="color: #0077a8;"></i>Sair</a></li> </ul>
                </div>
            </div>
        </div>
        
    </header>
            <main class="col-md-4 col-lg-10 p-4">
                <h1><strong>Bem-vindo à Área Privada da InveMed</strong></h1>
                <h6 class="mb-10">Aqui em baixo podes observar os dados do nosso sistema e aceder rapidamente às principais secções.</h6>
                <br>
                <h3 class="mb-5"> <i class="fa-solid fa-chart-line"></i> &ensp; <strong>Dashboard</strong></h3>
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded shadow text-center card-hover">
                            
                                <h6 class="text-muted">Total de Equipamentos</h6>
                                <h2 style="color:#0077a8;font-weight: 700; letter-spacing: -1px;">245</h2>
                                
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded shadow text-center card-hover">
                            
                                <h6 class="text-muted">Equipamentos Ativos</h6>
                                <h2 class="text-success" style="font-weight: 700; letter-spacing: -1px;">198</h2>
                              
                        </div>
                    </div>

                    <div class="col-md-4" style="color: #fff8e6;">
                        <div class="p-4 rounded shadow text-center card-hover" style="background-color: #fff8e6;">
                            
                            <h6 class="text-muted">Equipamentos em manutenção</h6>
                            <h2 class="text-warning" style="font-weight: 700; letter-spacing: -1px;">32</h2>
                        </div>
                    </div>
                    <div class="row g-4 mt-1">
                        <div class="col-md-4">
                            <div class="p-4 rounded shadow text-center card-hover" style="background-color: #fff5f5;" >
                                
                                    <h6 class="text-muted">Equipamentos inativos</h6>
                                    <h2 class="text-danger" style="font-weight: 700; letter-spacing: -1px;">15</h2>
                                    
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 rounded shadow text-center card-hover" style="background-color: #fff5f5;" >
                                
                                    <h6 class="text-muted ">Garantia expirada</h6>
                                    <h2 class="text-danger" style="font-weight: 700; letter-spacing: -1px;">12</h2>
                                    
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 rounded shadow text-center card-hover" style="background-color: #fff8e6;">
                                
                                    <h6 class="text-muted">Sem documentação</h6>
                                    <h2 class="text-warning" style="font-weight: 700; letter-spacing: -1px;">08</h2>
                                    
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 rounded shadow text-center card-hover" style="background-color: #fff5f5;">
                            
                                <h6 class="mb-3 text-center ">Equipamentos de criticidade elevada</h6>
                                <h2 class="text-danger" style="font-weight: 700; letter-spacing: -1px;">34</h2>
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 rounded shadow text-center card-hover" style="background-color: #fff5f5;">
                                
                                    <h6 class="mb-3 text-center ">Equipamentos com garantia a expirar nos próximos 30 dias</h6>
                                    <h2 class="text-danger" style="font-weight: 700; letter-spacing: -1px;">12</h2>
                                    
                            </div>
                        </div>

                    <div class="row g-4 mt-1 justify-content-center">

                        <div class="col-md-6 col-lg-4 d-flex justify-content-center">
                            <div class="p-4 bg-light rounded shadow text-center card-hover" style="width: 280px">
                                <h6 class="mb-3 text-center">Equipamentos por Serviço</h6>
                                <div class="grafico">
                                    <canvas id="equipamentosPorServico"></canvas>
                                </div>
                            </div>            
                        </div>

                        <div class="col-md-6 col-lg-4 d-flex justify-content-center">
                            <div class="p-4 bg-light rounded shadow text-center card-hover" style="width: 260px;">
                                <h6 class="mb-3 text-center">Equipamentos de Suporte de Vida por Serviço</h6>
                                <div class="grafico">
                                    <canvas id="suporteVidaServico"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4 d-flex justify-content-center">
                            <div class="p-4 bg-light rounded shadow text-center card-hover">
                                <h6 class="mb-3 text-center">Localização dos Equipamentos</h6>
                                <div class="chart-wrapper">
                                    <canvas id="distribuicaoLocalizacao"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>        
                                 
                <h3 class="mt-5"> <i class="fa-solid fa-bolt"></i> &ensp; <strong>Acesso Rápido às Principais Secções</strong></h3>
                
                <div class="row g-4">
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="gestaoconteudos.html" class="text-decoration-none text-dark">
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-sitemap fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Gestão de conteúdos</h4>
                                    <p class="text-muted mb-0">
                                        Atualização de páginas, textos e imagens do site
                                    </p>
                                </div>
                            </a>
                        </div>    
                    </div>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="equipamentos/listar.html" class="text-decoration-none text-dark"> 
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-laptop-medical fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Equipamentos</h4>
                                    <p class="text-muted mb-0">
                                        Gestão de Equipamentos
                                    </p>
                                </div>
                            </a>
                        </div>    
                    </div>  
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="localizacao/listar.html" class="text-decoration-none text-dark">    
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-map-location-dot fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Localização</h4>
                                    <p class="text-muted mb-0">
                                        Localização dos equipamentos
                                    </p>
                                </div>
                            </a>
                        </div>    
                    </div>  
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="fornecedores/listar.html" class="text-decoration-none text-dark">  
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-truck fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Fornecedores</h4>
                                    <p class="text-muted mb-0">
                                        Gestão de fornecedores
                                    </p>
                                </div> 
                            </a>
                        </div>    
                    </div>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="documentacao/listar.html" class="text-decoration-none text-dark"> 
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-file-medical fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Documentação</h4>
                                    <p class="text-muted mb-0">
                                        Documentação de equipamentos e fornecedores
                                    </p>
                                </div> 
                            </a>
                        </div>    
                    </div>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div class="card-menu">
                            <a href="garantiacontrato/listar.html" class="text-decoration-none text-dark">
                                <div class="text-center p-4 bg-light rounded shadow h-100 card-hover" style="max-width: 350px;">
                                    <i class="fa-solid fa-file-contract fa-3x mb-3" style="color:#0077a8"></i>
                                    <h4 class="mb-2">Garantias e Contratos</h4>
                                    <p class="text-muted mb-0">
                                        Garantias e Contratos dos equipamentos
                                    </p>
                                </div>
                            </a>
                        </div>    
                    </div>        
                </div> 
            </main>
        
    <script src="../assets/js/chart.umd.min.js"></script>
    <script src="../assets/js/1241035.js"></script>
    <script src="../assets/bootstrap/1241035.js"></script>
</body>
