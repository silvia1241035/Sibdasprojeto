<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <?php include '../includes/sidebar.php'; ?>
    <main class="col-md-9 col-lg-10 p-4">

        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1200px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar novo fornecedor</strong></h2> 
                    <hr>
                    
                    <form action="#" method="post" novalidate id="formFornecedor">

    <!-- Área de erros — no topo do formulário -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao inserir o fornecedor. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Nome + NIF -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="texto_nome" class="form-label">Nome do fornecedor<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="texto_nome" name="nome_fornecedor" required placeholder="Ex: MedTech Solutions">
                                <div class="invalid-feedback">Por favor, insira o nome do fornecedor.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="texto_nif" class="form-label">NIF<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="texto_nif" name="nif_fornecedor" required placeholder="Ex: 123456789">
                                <div class="invalid-feedback">Por favor, insira o NIF do fornecedor.</div>
                            </div>
                        </div>

                        <!-- Linha 2: Contacto + Email + Website + Morada -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="texto_contacto" class="form-label">Contacto Telefónico</label>
                                <input type="text" class="form-control" id="texto_contacto" name="contacto_fornecedor" placeholder="Ex: 912345678">
                            </div>
                            <div class="col-md-3">
                                <label for="texto_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="texto_email" name="email_fornecedor" placeholder="Ex: contato@medtech.com">
                            </div>
                            <div class="col-md-3">
                                <label for="texto_website" class="form-label">Website</label>
                                <input type="text" class="form-control" id="texto_website" name="website_fornecedor" placeholder="Ex: https://www.medtech.com">
                            </div>
                            <div class="col-md-3">
                                <label for="texto_morada" class="form-label">Morada</label>
                                <input type="text" class="form-control" id="texto_morada" name="morada_fornecedor" placeholder="Ex: Rua Exemplo, 123">
                            </div>
                        </div>

                        <!-- Linha 3: Tipo + Pessoa + Telefone -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="texto_tipo" class="form-label">Tipo de fornecedor</label>
                                <select class="form-select" id="texto_tipo" name="tipo_fornecedor">
                                    <option value="">Selecione...</option>
                                    <option value="Fabricante">Fabricante</option>
                                    <option value="Distribuidor">Distribuidor / fornecedor comercial</option>
                                    <option value="Assistência técnica">Assistência técnica</option>
                                    <option value="Consumíveis">Fornecedor de consumíveis ou acessórios</option>
                                    <option value="Outro">Outro (Escrever qual é o tipo de fornecedor nas observações)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="texto_pessoa" class="form-label">Pessoa de Contacto</label>
                                <input type="text" class="form-control" id="texto_pessoa" name="pessoa_fornecedor" placeholder="Ex: João Silva">
                            </div>
                            <div class="col-md-3">
                                <label for="texto_pessoa_telefone" class="form-label">Telefone da pessoa de contacto</label>
                                <input type="text" class="form-control" id="texto_pessoa_telefone" name="telefone_pessoa_fornecedor" placeholder="Ex: 912345678">
                            </div>
                        </div>

                        <!-- Linha 4: Observações -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="texto_observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="texto_observacoes" name="observacoes_fornecedor" rows="3" placeholder="Notas adicionais sobre o fornecedor..."></textarea>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 mt-3 border-top">
                            <small class="text-muted">
                                <span class="text-danger">*</span> campos obrigatórios
                            </small>
                            <div class="d-flex gap-2">
                                <a href="listar.html" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar" disabled>
                                    <i class="fa-regular fa-floppy-disk me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>


    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>