<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
 
        <!-- Título + botão nova -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fa-solid fa-list fa-1x mb-3"></i>
                <strong>Listagem de Localizações</strong>
            </h2>
            <a href="inserir.php" class="btn" style="background-color: #0077a8; color:white;">
                <i class="fa-solid fa-plus me-1"></i> Nova localização
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
                    <input type="text" id="searchAll" class="form-control" placeholder="Edifício, serviço, sala...">
                </div>
                <!-- Edifício — PHP gera as opções a partir dos edifícios existentes -->
                <div class="col-md-3">
                    <label class="form-label">Edifício</label>
                    <select id="filtroEdificio" class="form-select">
                        <option value="">Todos</option>
                        <option value="Edifício A">Edifício A</option>
                        <option value="Edifício B">Edifício B</option>
                        <option value="Edifício C">Edifício C</option>
                    </select>
                </div>
                <!-- Serviço — PHP gera as opções a partir dos serviços existentes -->
                <div class="col-md-3">
                    <label class="form-label">Serviço/Departamento</label>
                    <select id="filtroServico" class="form-select">
                        <option value="">Todos</option>
                        <option value="UCI">UCI</option>
                        <option value="Medicina">Medicina</option>
                        <option value="Urgência">Urgência</option>
                        <option value="Cardiologia">Cardiologia</option>
                        <option value="Bloco Operatório">Bloco Operatório</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ordenar por</label>
                    <select id="sortBy" class="form-select">
                        <option value="edificio">Edifício</option>
                        <option value="piso">Piso</option>
                        <option value="servico">Serviço</option>
                        <option value="sala">Sala</option>
                        <option value="nequipamentos">Nº Equipamentos</option>
                    </select>
                </div>
            </div>
        </div>
 
        <!-- Tabela -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Edifício</th>
                        <th>Piso</th>
                        <th>Serviço/Departamento</th>
                        <th>Sala/Gabinete</th>
                        <th class="text-center">Nº de Equipamentos</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="localizacoesTable">
                    <!--
                        PHP irá gerar as linhas dinamicamente.
                        O nº de equipamentos vem de uma query que conta os equipamentos
                        associados a esta localização (COUNT).
                        O badge é clicável e leva à listagem de equipamentos filtrada por esta localização.
                    -->
                    <tr
                        data-edificio="[Edifício]"
                        data-piso="[Piso]"
                        data-servico="[Serviço]"
                        data-sala="[Sala]"
                        data-nequipamentos="[Nº]">
                        <td>[Edifício]</td>
                        <td>[Piso]</td>
                        <td>[Serviço/Departamento]</td>
                        <td>[Sala/Gabinete]</td>
                        <td class="text-center">
                            <!-- href com filtro: ../equipamentos/listar.html?localizacao=[Serviço] -->
                            <a href="../equipamentos/listar.php" class="badge text-decoration-none text-dark" title="Ver equipamentos nesta localização">
                                [Nº] </i>
                            </a>
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-flex justify-content-center gap-3">
                                <a href="detalhes.php" class="acao-tabela acao-consultar" title="Ver detalhes">
                                    <i class="fa-solid fa-eye me-1"></i>Consultar
                                </a>
                                <a href="editar.php" class="acao-tabela acao-editar" title="Editar">
                                    <i class="fa-regular fa-pen-to-square me-1"></i>Editar
                                </a>
                                <a href="apagar.php" class="acao-tabela acao-eliminar" title="Eliminar">
                                    <i class="fa-solid fa-trash-can me-1"></i>Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p id="noResults" class="text-center text-muted mt-3" style="display: none;">
                <i class="fa-solid fa-magnifying-glass me-2"></i>Nenhuma localização encontrada com os critérios selecionados.
            </p>
        </div>
 
    </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
