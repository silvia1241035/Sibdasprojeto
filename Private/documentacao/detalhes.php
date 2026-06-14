<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();?>

<?php include '../includes/header.php'; ?>
 
<?php include '../includes/nav.php'; ?>
 
    <?php include '../includes/sidebar.php'; ?>
 
    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
 
                    <h2 class="mb-4">
                        <strong><i class="fa-solid fa-file-medical fa-1x mb-3"></i> Detalhes do Documento</strong>
                    </h2>
                    <hr>
 
                    <!-- Linha 1: Tipo + Nome -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipo de documento</label>
                            <p class="form-control-plaintext">[Tipo de documento]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome do documento</label>
                            <p class="form-control-plaintext">[Nome do documento]</p>
                        </div>
                    </div>
 
                    <!-- Linha 2: Data + Validade -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data do documento</label>
                            <p class="form-control-plaintext">[Data do documento]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data de validade</label>
                            <!-- PHP escolhe o badge: badge-valido / badge-expirado / badge-semvalidade -->
                            <p class="form-control-plaintext">
                                <span class="badge badge-valido">[Validade]</span>
                            </p>
                        </div>
                    </div>
 
                    <!-- Linha 3: Equipamento associado + Fornecedor associado -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Equipamento associado</label>
                            <p class="form-control-plaintext">[Equipamento associado]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fornecedor associado</label>
                            <p class="form-control-plaintext">[Fornecedor associado]</p>
                        </div>
                    </div>
 
                    <!-- Linha 4: Ficheiro -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Ficheiro</label>
                            <p class="form-control-plaintext">
                                <!-- href para o caminho/link do ficheiro -->
                                <a href="#" target="_blank" style="color:#0077a8;text-decoration:none;">
                                    <i class="fa-solid fa-file-arrow-down me-1"></i>[Nome do ficheiro]
                                </a>
                            </p>
                        </div>
                    </div>
 
                </div>
 
                <div class="d-flex justify-content-end p-3">
                    <a href="listar.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </main>
 
 
    <!-- Menu Mobile -->
    <?php include '../includes/sidebarmobile.php'; ?>
 
<?php include '../includes/footer.php'; ?>
