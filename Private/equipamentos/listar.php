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
                        <a href="listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-laptop-medical"></i>  Equipamentos</a>
                        <a href="../localizacao/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-map-location-dot"></i>  Localização</a> 
                        <a href="../fornecedores/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-truck"></i>  Fornecedores</a>
                        <a href="../documentacao/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-file-medical"></i>  Documentação</a>                 
                        <a href="../garantiacontrato/listar.html" class="menu-link" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-contract"></i>  Garantias e     Contratos</a>
                    </nav>
                </div>    
            </aside>
        </div>
    </div>
    
    <main class="col-md-9 col-lg-10 p-4">
 
        <!-- Título + botão novo -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fa-solid fa-list fa-3"></i>
                <strong>Listagem de Equipamentos</strong>
            </h2>
            <a href="inserir.html" class="btn" style="background-color: #0077a8; color:white;">
                <i class="fa-solid fa-plus me-1"></i> Novo equipamento
            </a>
        </div>
 
        <!-- Mensagem de sucesso/erro — PHP remove d-none e preenche conforme necessário -->
        <div class="alert alert-success alert-dismissible fade show d-none" id="alertaSucesso" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <span id="alertaSucessoMsg">Operação realizada com sucesso.</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <div class="alert alert-danger alert-dismissible fade show d-none" id="alertaErro" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <span id="alertaErroMsg">Ocorreu um erro. Por favor, tente novamente.</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
 
        <!-- Painel de filtros -->
        <div class="card p-3 mb-4 shadow-sm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pesquisar</label>
                    <input type="text" id="searchAll" class="form-control" placeholder="Código, designação, marca, modelo, nº série...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select id="filtroEstado" class="form-select">
                        <option value="">Todos</option>
                        <option value="Ativo">Ativo</option>
                        <option value="Em manutenção">Em manutenção</option>
                        <option value="Inativo">Inativo</option>
                        <option value="Em calibração">Em calibração</option>
                        <option value="Em quarentena">Em quarentena</option>
                        <option value="Abatido">Abatido</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Criticidade</label>
                    <select id="filtroCriticidade" class="form-select">
                        <option value="">Todas</option>
                        <option value="Baixa">Baixa</option>
                        <option value="Média">Média</option>
                        <option value="Alta">Alta</option>
                        <option value="Suporte de vida">Suporte de vida</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Categoria</label>
                    <select id="filtroCategoria" class="form-select">
                        <option value="">Todas</option>
                        <option value="Monitorização">Monitorização</option>
                        <option value="Suporte de vida">Suporte de vida</option>
                        <option value="Terapia">Terapia</option>
                        <option value="Diagnóstico">Diagnóstico</option>
                        <option value="Laboratório">Laboratório</option>
                        <option value="Esterilização">Esterilização</option>
                        <option value="Reabilitação">Reabilitação</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Localização</label>
                    <select id="filtroLocalizacao" class="form-select">
                        <option value="">Todas</option>
                        <option value="UCI">UCI</option>
                        <option value="Medicina">Medicina</option>
                        <option value="Urgência">Urgência</option>
                        <option value="Cardiologia">Cardiologia</option>
                        <option value="Bloco Operatório">Bloco Operatório</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fornecedor</label>
                    <select id="filtroFornecedor" class="form-select">
                        <option value="">Todos</option>
                        <option value="Philips">Philips</option>
                        <option value="Dräger">Dräger</option>
                        <option value="B. Braun">B. Braun</option>
                        <option value="Zoll">Zoll</option>
                        <option value="GE Healthcare">GE Healthcare</option>
                        <option value="Tuttnauer">Tuttnauer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ordenar por</label>
                    <select id="sortBy" class="form-select">
                        <option value="codigo">Código</option>
                        <option value="designacao">Designação</option>
                        <option value="marca">Marca</option>
                        <option value="modelo">Modelo</option>
                        <option value="categoria">Categoria</option>
                        <option value="servico">Localização</option>
                        <option value="nserie">Nº de Série</option>
                        <option value="estado">Estado</option>
                        <option value="criticidade-ordem">Criticidade</option>
                    </select>
                </div>
            </div>
        </div>
 
        <!-- Tabela -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Designação</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Categoria</th>
                        <th>Localização</th>
                        <th>Nº de Série</th>
                        <th>Estado</th>
                        <th>Criticidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="equipamentosTable">
                            <tr
                                data-codigo="[Código interno]"
                                data-designacao="[Designação]"
                                data-marca="[Marca]"
                                data-modelo="[Modelo]"
                                data-categoria="[Categoria]"
                                data-servico="[Localização]"
                                data-nserie="[Nº de Série]"
                                data-estado="[Estado]"
                                data-criticidade="[Criticidade]"
                                data-criticidade-ordem="[1-4]"
                                data-fornecedor="[Fornecedor]">
                                <td >[Código interno]</td>
                                <td>[Designação]</td>
                                <td>[Marca]</td>
                                <td>[Modelo]</td>
                                <td>[Categoria]</td>
                                <td>[Localização]</td>
                                <td>[Nº de Série]</td>
                                <td>[Estado]</td>
                                <td>[Criticidade]</td>
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
                        Nenhum equipamento encontrado.
                    </p>
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
            <a href="listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-gears"></i>  Gestão de Equipamentos</a>
            <a href="../localizacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-map-location-dot"></i>  Localização</a>
            <a href="../fornecedores/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-truck"></i>  Gestão de Fornecedores</a>
            <a href="../documentacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-medical"></i>  Gestão de Documentação</a>
            <a href="../garantiacontrato/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-contract"></i>  Garantias e Contratos</a>
        </div>
    </div>
    
<?php include '../includes/footer.php'; ?>
