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

                <!-- Título + botão novo -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">
                        <i class="fa-solid fa-list fa-1x mb-3"></i>
                        <strong>Garantias e Contratos</strong>
                    </h2>
                    <a href="inserir.html" class="btn" style="background-color: #0077a8; color:white;">
                        <i class="fa-solid fa-plus me-1"></i> Novo registo
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
                            <input type="text" id="searchAll" class="form-control" placeholder="Equipamento, entidade...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado da garantia</label>
                            <select id="filtroEstado" class="form-select">
                                <option value="">Todas</option>
                                <option value="valida">Válida</option>
                                <option value="expirar">A expirar</option>
                                <option value="expirada">Expirada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contrato de manutenção</label>
                            <select id="filtroContrato" class="form-select">
                                <option value="">Todos</option>
                                <option value="Sim">Com contrato</option>
                                <option value="Não">Sem contrato</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Ordenar por</label>
                            <select id="sortBy" class="form-select">
                                <option value="equipamento">Equipamento</option>
                                <option value="inicio">Início</option>
                                <option value="fim">Fim da garantia</option>
                                <option value="entidade">Entidade</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tabela -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Equipamento</th>
                                <th>Início Garantia</th>
                                <th>Fim Garantia</th>
                                <th>Estado</th>
                                <th>Contrato</th>
                                <th>Entidade</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="garantiasTable">
                            <!--
                                PHP gera as linhas dinamicamente.
                                data-estadogarantia: "valida" | "expirar" | "expirada"
                                (o PHP calcula comparando a data de fim com a data de hoje)
                            -->
                            <tr
                                data-equipamento="[Equipamento]"
                                data-inicio="[Início]"
                                data-fim="[Fim]"
                                data-entidade="[Entidade]"
                                data-contrato="Sim"
                                data-estadogarantia="valida">
                                <td>[Equipamento]</td>
                                <td>[Início Garantia]</td>
                                <td>[Fim Garantia]</td>
                                <td>[Estado]
                                </td>
                                <td>[Sim/Não]</td>
                                <td>[Entidade]</td>
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
                        <i class="fa-solid fa-magnifying-glass me-2"></i>Nenhum registo encontrado com os critérios selecionados.
                    </p>
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
