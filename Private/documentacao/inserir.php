<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1100px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar documentos</strong></h2>
                    <hr>

                    <form action="#" method="post" enctype="multipart/form-data" novalidate id="formDocumento">

                        <!-- Área de erros -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao inserir os documentos. Por favor, tente novamente.
                        </div>

                        <!-- Equipamento e fornecedor (partilhados por todos os documentos) -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="equipamento" class="form-label">Equipamento associado<span class="text-danger" title="Campo obrigatório">*</span></label>
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

                        <hr class="mb-4">

                        <!-- Cabeçalho da secção de documentos -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fa-solid fa-file-lines me-2"></i>Documentos</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAdicionarLinha">
                                <i class="fa-solid fa-plus me-1"></i> Adicionar documento
                            </button>
                        </div>

                        <!-- Tabela de linhas de documentos -->
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle" id="tabelaDocumentos">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:180px;">Tipo<span class="text-danger">*</span></th>
                                        <th style="min-width:200px;">Nome<span class="text-danger">*</span></th>
                                        <th style="min-width:150px;">Data<span class="text-danger">*</span></th>
                                        <th style="min-width:150px;">Validade</th>
                                        <th style="min-width:200px;">Ficheiro</th>
                                        <th class="text-center" style="width:50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="linhasDocumentos">
                                    <!-- Linha inicial -->
                                    <tr class="linha-documento">
                                        <td>
                                            <select class="form-select form-select-sm" name="tipo_documento[]" required>
                                                <option value="">Selecione...</option>
                                                <option value="Manual de utilizador">Manual de utilizador</option>
                                                <option value="Manual de serviço">Manual de serviço</option>
                                                <option value="Certificado de calibração">Certificado de calibração</option>
                                                <option value="Contrato de manutenção">Contrato de manutenção</option>
                                                <option value="Fatura/Guia de aquisição">Fatura/Guia de aquisição</option>
                                                <option value="Declaração de conformidade">Declaração de conformidade</option>
                                                <option value="Relatório técnico">Relatório técnico</option>
                                            </select>
                                            <div class="invalid-feedback">Obrigatório.</div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="nome_documento[]" required placeholder="Ex: Manual de utilizador v2">
                                            <div class="invalid-feedback">Obrigatório.</div>
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm" name="data_documento[]" required>
                                            <div class="invalid-feedback">Obrigatório.</div>
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm" name="validade_documento[]">
                                            <div class="form-text small">Quando aplicável.</div>
                                        </td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="ficheiro_documento[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                            <div class="form-text small">PDF, DOC, JPG, PNG.</div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remover-linha" title="Remover linha" disabled>
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 border-top">
                            <small class="text-muted">
                                <span class="text-danger">*</span> campos obrigatórios
                            </small>
                            <div class="d-flex gap-2">
                                <a href="listar.php" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
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


