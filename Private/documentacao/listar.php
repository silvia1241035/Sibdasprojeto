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
 
        <!-- Título + botão novo -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fa-solid fa-list fa-1x mb-3"></i>
                <strong>Listagem de Documentação</strong>
            </h2>
            <a href="inserir.html" class="btn" style="background-color: #0077a8; color:white;">
                <i class="fa-solid fa-plus me-1"></i> Novo documento
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
                    <input type="text" id="searchAll" class="form-control" placeholder="Nome do documento, equipamento...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo de documento</label>
                    <select id="filtroTipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="Manual de utilizador">Manual de utilizador</option>
                        <option value="Manual de serviço">Manual de serviço</option>
                        <option value="Certificado de calibração">Certificado de calibração</option>
                        <option value="Contrato de manutenção">Contrato de manutenção</option>
                        <option value="Fatura/Guia de aquisição">Fatura/Guia de aquisição</option>
                        <option value="Declaração de conformidade">Declaração de conformidade</option>
                        <option value="Relatório técnico">Relatório técnico</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Validade</label>
                    <select id="filtroValidade" class="form-select">
                        <option value="">Todas</option>
                        <option value="valido">Válidos</option>
                        <option value="expirado">Expirados</option>
                        <option value="sem">Sem validade</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ordenar por</label>
                    <select id="sortBy" class="form-select">
                        <option value="tipo">Tipo</option>
                        <option value="nome">Nome</option>
                        <option value="data">Data</option>
                        <option value="validade">Validade</option>
                        <option value="equipamento">Equipamento</option>
                    </select>
                </div>
            </div>
        </div>
 
        <!-- Tabela -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Tipo</th>
                        <th>Nome do Documento</th>
                        <th>Data</th>
                        <th>Validade</th>
                        <th>Equipamento Associado</th>
                        <th>Fornecedor Associado</th>
                        <th class="text-center">Ficheiro</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="documentosTable">
                    <!--
                        PHP irá gerar as linhas dinamicamente.
                        data-estadovalidade: "valido" | "expirado" | "sem"
                        (o PHP calcula comparando a data de validade com a data de hoje)
                    -->
                    <tr
                        data-tipo="[Tipo]"
                        data-nome="[Nome]"
                        data-data="[Data]"
                        data-validade="[Validade]"
                        data-equipamento="[Equipamento]"
                        data-fornecedor="[Fornecedor]"
                        data-estadovalidade="valido">
                        <td>[Tipo de documento]</td>
                        <td>[Nome do Documento]</td>
                        <td>[Data]</td>
                        <td>[Validade]
                        </td>
                        <td>[Equipamento Associado]</td>
                        <td>[Fornecedor Associado]</td>
                        <td class="text-center">
                            <!-- href para o caminho/link do ficheiro -->
                            <a href="#" target="_blank" style="color:#0077a8;" title="Abrir ficheiro">
                                <i class="fa-solid fa-file-arrow-down"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-3">
                                <a href="detalhes.html" class="acao-tabela acao-consultar text-decoration-none" title="Ver detalhes">
                                    <i class="fa-solid fa-eye me-1"></i>Consultar
                                </a>
                                <a href="editar.html" class="acao-tabela acao-editar text-decoration-none" title="Editar">
                                    <i class="fa-regular fa-pen-to-square me-1"></i>Editar
                                </a>
                                <a href="apagar.html" class="acao-tabela acao-eliminar text-decoration-none" title="Eliminar">
                                    <i class="fa-solid fa-trash-can me-1"></i>Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p id="noResults" class="text-center text-muted mt-3" style="display: none;">
                <i class="fa-solid fa-magnifying-glass me-2"></i>Nenhum documento encontrado com os critérios selecionados.
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
            <a href="../equipamentos/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-gears"></i>  Gestão de Equipamentos</a>
            <a href="../localizacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-map-location-dot"></i>  Localização</a>
            <a href="../fornecedores/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-truck"></i>  Gestão de Fornecedores</a>
            <a href="listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-medical"></i>  Gestão de Documentação</a>
            <a href="../garantiacontrato/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-contract"></i>  Garantias e Contratos</a>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
