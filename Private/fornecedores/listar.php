<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <?php include '../includes/sidebar.php'; ?>        
            <main class="col-md-9 col-lg-10 p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">
                        <i class="fa-solid fa-list fa-1x mb-3"></i>
                        <strong>Listagem de Fornecedores</strong>
                    </h2>
                    <a href="inserir.php" class="btn" style="background-color: #0077a8; color:white;">
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
                        Nenhum fornecedor encontrado.
                    </p>
                </div>

            </main>

        </div>
    </div>
    


    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
