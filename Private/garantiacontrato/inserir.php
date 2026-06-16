<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
<?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-center mt-4">
                    <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                        <div class="card-body">
                            <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar garantia / contrato</strong></h2>
                            <hr>

                            <form action="#" method="post" novalidate id="formGarantia">

                                <!-- Área de erros -->
                                <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                                    Erro ao inserir o registo. Por favor, tente novamente.
                                </div>

                                <!-- Linha 1: Equipamento + Entidade -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="equipamento" class="form-label">Equipamento associado<span class="text-danger" title="Campo obrigatório">*</span></label>
                                        <!-- PHP gera as opções a partir dos equipamentos registados -->
                                        <select class="form-select" id="equipamento" name="equipamento_garantia" required>
                                            <option value="">Selecione...</option>
                                            <option value="1">04.002.00 - Monitor Multiparamétrico</option>
                                            <option value="2">04.003.00 - Ventilador Pulmonar</option>
                                            <option value="3">04.004.00 - Desfibrilhador</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione o equipamento.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="entidade" class="form-label">Entidade responsável</label>
                                        <input type="text" class="form-control" id="entidade" name="entidade_garantia" placeholder="Ex: Philips Healthcare">
                                    </div>
                                </div>

                                <!-- Linha 2: Início + Fim da garantia -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="inicio" class="form-label">Data de início da garantia</label>
                                        <input type="date" class="form-control" id="inicio" name="inicio_garantia">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fim" class="form-label">Data de fim da garantia<span class="text-danger" title="Campo obrigatório">*</span></label>
                                        <input type="date" class="form-control" id="fim" name="fim_garantia" required>
                                        <div class="invalid-feedback">Por favor, insira a data de fim da garantia.</div>
                                    </div>
                                </div>

                                <!-- Linha 3: Contrato + Tipo + Periodicidade -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="contrato" class="form-label">Contrato de manutenção</label>
                                        <select class="form-select" id="contrato" name="contrato_garantia">
                                            <option value="">Selecione...</option>
                                            <option value="Sim">Sim</option>
                                            <option value="Não">Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="tipocontrato" class="form-label">Tipo de contrato</label>
                                        <select class="form-select" id="tipocontrato" name="tipocontrato_garantia">
                                            <option value="">Selecione...</option>
                                            <option value="Preventiva">Manutenção preventiva</option>
                                            <option value="Corretiva">Manutenção corretiva</option>
                                            <option value="Completa">Completa (preventiva + corretiva)</option>
                                            <option value="Outro">Outro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="periodicidade" class="form-label">Periodicidade</label>
                                        <select class="form-select" id="periodicidade" name="periodicidade_garantia">
                                            <option value="">Selecione...</option>
                                            <option value="Mensal">Mensal</option>
                                            <option value="Trimestral">Trimestral</option>
                                            <option value="Semestral">Semestral</option>
                                            <option value="Anual">Anual</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Linha 4: Observações -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="observacoes" class="form-label">Observações</label>
                                        <textarea class="form-control" id="observacoes" name="observacoes_garantia" rows="3" placeholder="Notas adicionais sobre a garantia ou o contrato..."></textarea>
                                    </div>
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
        </div>
    </div>

<?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
