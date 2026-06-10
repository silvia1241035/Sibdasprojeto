<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>      

            <main class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-center mt-4">
                    <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                        <div class="card-body">

                            <h2 class="mb-4">
                                <strong><i class="fa-solid fa-file-contract fa-1x mb-3"></i> Detalhes da Garantia / Contrato</strong>
                            </h2>
                            <hr>

                            <!-- Linha 1: Equipamento + Entidade -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Equipamento associado</label>
                                    <p class="form-control-plaintext">[Equipamento associado]</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Entidade responsável</label>
                                    <p class="form-control-plaintext">[Entidade responsável]</p>
                                </div>
                            </div>

                            <!-- Linha 2: Início + Fim + Estado -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Início da garantia</label>
                                    <p class="form-control-plaintext">[Início da garantia]</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fim da garantia</label>
                                    <p class="form-control-plaintext">[Fim da garantia]</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Estado da garantia</label>
                                    <!-- PHP escolhe: badge-garantia-valida / badge-garantia-expirar / badge-garantia-expirada -->
                                    <p class="form-control-plaintext">
                                        <span class="badge badge-garantia-valida">[Estado]</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Linha 3: Contrato + Tipo + Periodicidade -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Contrato de manutenção</label>
                                    <p class="form-control-plaintext">[Sim/Não]</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo de contrato</label>
                                    <p class="form-control-plaintext">[Tipo de contrato]</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Periodicidade da manutenção</label>
                                    <p class="form-control-plaintext">[Periodicidade]</p>
                                </div>
                            </div>

                            <!-- Linha 4: Observações -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Observações</label>
                                    <p class="form-control-plaintext">[Observações]</p>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end p-3">
                            <a href="listar.html" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <!-- Menu Mobile -->
    <?php include '../includes/sidebarmobile.php'; ?>
    
<?php include '../includes/footer.php'; ?>
