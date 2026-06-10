<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar novo documento</strong></h2>
                    <hr>
 
                    <!-- enctype="multipart/form-data" é necessário para o upload de ficheiros.
                         Se usares a versão simplificada (link/caminho), podes remover o enctype. -->
                    <form action="#" method="post" enctype="multipart/form-data" novalidate id="formDocumento">
 
                        <!-- Área de erros -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao inserir o documento. Por favor, tente novamente.
                        </div>
 
                        <!-- Linha 1: Tipo de documento + Nome -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo de documento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <select class="form-select" id="tipo" name="tipo_documento" required>
                                    <option value="">Selecione...</option>
                                    <option value="Manual de utilizador">Manual de utilizador</option>
                                    <option value="Manual de serviço">Manual de serviço</option>
                                    <option value="Certificado de calibração">Certificado de calibração</option>
                                    <option value="Contrato de manutenção">Contrato de manutenção</option>
                                    <option value="Fatura/Guia de aquisição">Fatura/Guia de aquisição</option>
                                    <option value="Declaração de conformidade">Declaração de conformidade</option>
                                    <option value="Relatório técnico">Relatório técnico</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o tipo de documento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome do documento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome_documento" required placeholder="Ex: Manual de utilizador do monitor X">
                                <div class="invalid-feedback">Por favor, insira o nome do documento.</div>
                            </div>
                        </div>
 
                        <!-- Linha 2: Data do documento + Data de validade -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="data" class="form-label">Data do documento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="date" class="form-control" id="data" name="data_documento" required>
                                <div class="invalid-feedback">Por favor, insira a data do documento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="validade" class="form-label">Data de validade</label>
                                <input type="date" class="form-control" id="validade" name="validade_documento">
                                <div class="form-text">Preencher apenas quando aplicável (ex: certificados que expiram).</div>
                            </div>
                        </div>
 
                        <!-- Linha 3: Equipamento associado + Fornecedor associado -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="equipamento" class="form-label">Equipamento associado<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <!-- PHP gera as opções a partir dos equipamentos registados -->
                                <select class="form-select" id="equipamento" name="equipamento_documento" required>
                                    <option value="">Selecione...</option>
                                    <option value="1">04.002.00 - Monitor Multiparamétrico</option>
                                    <option value="2">04.003.00 - Ventilador Pulmonar</option>
                                    <option value="3">04.004.00 - Desfibrilhador</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o equipamento associado.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="fornecedor" class="form-label">Fornecedor associado</label>
                                <!-- Opcional ("se necessário"). PHP gera as opções a partir dos fornecedores registados -->
                                <select class="form-select" id="fornecedor" name="fornecedor_documento">
                                    <option value="">Nenhum / Selecione...</option>
                                    <option value="Philips">Philips</option>
                                    <option value="Dräger">Dräger</option>
                                    <option value="B. Braun">B. Braun</option>
                                    <option value="Zoll">Zoll</option>
                                    <option value="GE Healthcare">GE Healthcare</option>
                                    <option value="Tuttnauer">Tuttnauer</option>
                                </select>
                            </div>
                        </div>
 
                        <!-- Linha 4: Ficheiro -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="ficheiro" class="form-label">Ficheiro</label>
                                <input type="file" class="form-control" id="ficheiro" name="ficheiro_documento">
                                <div class="form-text">Formatos aceites: PDF, DOC, DOCX, JPG, PNG.</div>
                                
                            </div>
                        </div>
 
                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 border-top">
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
