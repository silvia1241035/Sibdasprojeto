<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
 
                <!-- Título + ações -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">
                        <i class="fa-solid fa-laptop-medical me-2"></i>
                        <strong>Detalhes do Equipamento</strong>
                    </h2>
                    <div class="d-flex gap-2">
                        <a href="listar.html" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                        <a href="editar.html" class="btn btn-primary" style="background-color: #0077a8; border-color: #0077a8;">
                            <i class="fa-regular fa-pen-to-square me-1"></i> Editar
                        </a>
                    </div>
                </div>
 
                <!-- Cartão de identificação (visível em todas as abas) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="text-muted small">Código interno</div>
                            <h3 class="mb-1">[Código interno]</h3>
                            <div class="fs-5">[Designação]</div>
                        </div>
                        <div class="text-end">
                            
                            <span class="badge badge-estado-ativo mb-2 fs-6">[Estado]</span><br>
                            
                            <span class="badge badge-critico-alta fs-6">Criticidade: [Criticidade]</span>
                        </div>
                    </div>
                </div>
 
                <!-- ABAS -->
                <ul class="nav nav-tabs" id="detalhesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-detalhes-btn" data-bs-toggle="tab" data-bs-target="#tab-detalhes" type="button" role="tab" aria-controls="tab-detalhes" aria-selected="true">
                            <i class="fa-solid fa-circle-info me-1"></i> Detalhes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-fornecedor-btn" data-bs-toggle="tab" data-bs-target="#tab-fornecedor" type="button" role="tab" aria-controls="tab-fornecedor" aria-selected="false">
                            <i class="fa-solid fa-truck me-1"></i> Fornecedor
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-localizacao-btn" data-bs-toggle="tab" data-bs-target="#tab-localizacao" type="button" role="tab" aria-controls="tab-localizacao" aria-selected="false">
                            <i class="fa-solid fa-map-location-dot me-1"></i> Localização
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-documentacao-btn" data-bs-toggle="tab" data-bs-target="#tab-documentacao" type="button" role="tab" aria-controls="tab-documentacao" aria-selected="false">
                            <i class="fa-solid fa-file-medical me-1"></i> Documentação
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-garantia-btn" data-bs-toggle="tab" data-bs-target="#tab-garantia" type="button" role="tab" aria-controls="tab-garantia" aria-selected="false">
                            <i class="fa-solid fa-file-contract me-1"></i> Garantia e Contrato
                        </button>
                    </li>
                </ul>
 
                <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white" id="detalhesTabsContent">
 
                    <!-- ABA 1: DETALHES DO EQUIPAMENTO -->
                    <div class="tab-pane fade show active" id="tab-detalhes" role="tabpanel" aria-labelledby="tab-detalhes-btn">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Código interno</div>
                                <div class="detalhe-valor">[Código interno]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Designação</div>
                                <div class="detalhe-valor">[Designação]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Categoria</div>
                                <div class="detalhe-valor">[Categoria]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Marca</div>
                                <div class="detalhe-valor">[Marca]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Modelo</div>
                                <div class="detalhe-valor">[Modelo]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Nº de série</div>
                                <div class="detalhe-valor">[Número de Série]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Fabricante</div>
                                <div class="detalhe-valor">[Fabricante]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Data de aquisição</div>
                                <div class="detalhe-valor">[Data de Aquisição]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Ano de fabrico</div>
                                <div class="detalhe-valor">[Ano de Fabrico]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Custo de aquisição</div>
                                <div class="detalhe-valor">[Custo de Aquisição] &euro;</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Tipo de entrada</div>
                                <div class="detalhe-valor">[Tipo de Entrada]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Estado</div>
                                <!-- PHP escolhe a classe do badge conforme o valor -->
                                <div><span class="badge badge-estado-ativo">[Estado]</span></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Criticidade</div>
                                <!-- PHP escolhe a classe do badge conforme o valor -->
                                <div><span class="badge badge-critico-alta">[Criticidade]</span></div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="detalhe-label">Observações</div>
                                <div class="detalhe-valor fw-normal">[Observações]</div>
                            </div>
                        </div>
                    </div>
 
                    <!-- ABA 2: FORNECEDOR -->
                    <div class="tab-pane fade" id="tab-fornecedor" role="tabpanel" aria-labelledby="tab-fornecedor-btn">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Nome do fornecedor</div>
                                <div class="detalhe-valor">[Nome do fornecedor]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">NIF</div>
                                <div class="detalhe-valor">[NIF]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Tipo</div>
                                <div class="detalhe-valor">[Tipo]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Contacto telefónico</div>
                                <div class="detalhe-valor">[Contacto telefónico]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Email</div>
                                <div class="detalhe-valor">[Email]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Website</div>
                                <div class="detalhe-valor">[Website]</div>
                            </div>
                            <div class="col-md-8 mb-3">
                                <div class="detalhe-label">Morada</div>
                                <div class="detalhe-valor fw-normal">[Morada]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Pessoa de contacto</div>
                                <div class="detalhe-valor">[Pessoa de contacto]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Telefone da pessoa de contacto</div>
                                <div class="detalhe-valor">[Telefone da pessoa de contacto]</div>
                            </div>
                        </div>
                        <hr>
                        <a href="../fornecedores/detalhes.html" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-up-right-from-square me-1"></i> Ver ficha completa do fornecedor
                        </a>
                    </div>
 
                    <!-- ABA 3: LOCALIZAÇÃO -->
                    <div class="tab-pane fade" id="tab-localizacao" role="tabpanel" aria-labelledby="tab-localizacao-btn">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Edifício</div>
                                <div class="detalhe-valor">[Edifício]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Piso</div>
                                <div class="detalhe-valor">[Piso]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Serviço / Departamento</div>
                                <div class="detalhe-valor">[Serviço / Departamento]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Sala / Gabinete</div>
                                <div class="detalhe-valor">[Sala / Gabinete]</div>
                            </div>
                        </div>
                        <hr>
                        <a href="../localizacao/detalhes.html" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-up-right-from-square me-1"></i> Ver localização
                        </a>
                    </div>
 
                    <!-- ABA 4: DOCUMENTAÇÃO DESTE EQUIPAMENTO -->
                    <div class="tab-pane fade" id="tab-documentacao" role="tabpanel" aria-labelledby="tab-documentacao-btn">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Documentos associados a este equipamento</span>
                            <a href="../documentacao/inserir.html" class="btn btn-sm btn-primary" style="background-color: #0077a8; border-color: #0077a8;">
                                <i class="fa-solid fa-plus me-1"></i> Adicionar documento
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Nome</th>
                                        <th>Data</th>
                                        <th>Validade</th>
                                        <th class="text-center">Ficheiro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <tr>
                                        <td>[Tipo de documento]</td>
                                        <td>[Nome do documento]</td>
                                        <td>[Data]</td>
                                        <td><span class="badge badge-validade-valido">[Validade]</span></td>
                                        <td class="text-center">
                                            <a href="#" class="text-decoration-none" style="color:#0077a8;" title="Descarregar">
                                                <i class="fa-solid fa-download me-1"></i>Descarregar
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <p id="semDocumentos" class="text-center text-muted mt-3" style="display: none;">
                                <i class="fa-solid fa-folder-open me-2"></i>Este equipamento ainda não tem documentos associados.
                            </p>
                        </div>
                    </div>
 
                    <!-- ABA 5: GARANTIA E CONTRATO -->
                    <div class="tab-pane fade" id="tab-garantia" role="tabpanel" aria-labelledby="tab-garantia-btn">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Entidade responsável</div>
                                <div class="detalhe-valor">[Entidade responsável]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Data de início da garantia</div>
                                <div class="detalhe-valor">[Data de início]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Data de fim da garantia</div>
                                <div class="detalhe-valor">[Data de fim]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Estado da garantia</div>
                                <!-- PHP escolhe a classe do badge conforme o valor -->
                                <div><span class="badge badge-garantia-valida">[Estado]</span></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Contrato de manutenção</div>
                                <div class="detalhe-valor">[Sim/Não]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Tipo de contrato</div>
                                <div class="detalhe-valor">[Tipo de contrato]</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="detalhe-label">Periodicidade</div>
                                <div class="detalhe-valor">[Periodicidade]</div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="detalhe-label">Observações</div>
                                <div class="detalhe-valor fw-normal">[Observações]</div>
                            </div>
                        </div>
                        <hr>
                        <a href="../garantiacontrato/detalhes.html" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-up-right-from-square me-1"></i> Ver ficha completa da garantia/contrato
                        </a>
                    </div>
 
                </div>
 
            </main>
        </div>
    </div>

    <?php include '../includes/sidebarmobile.php'; ?>
    
<?php include '../includes/footer.php'; ?>
