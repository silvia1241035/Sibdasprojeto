<?php
require_once '../includes/funcoes.php';
redirect_if_not_logged();?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-center mt-4">
                <div class="card w-100 shadow rounded" style="max-width: 900px;">
                    <div class="card-body">
                        
                        <h2 class="mb-4">
                            <strong><i class="fa-solid fa-truck fa-1x mb-3"></i> Detalhes do Fornecedor</strong> </h2>
                        <hr>
 
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do fornecedor</label>
                            <p class="form-control-plaintext">[Nome do Fornecedor]</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIF</label>
                            <p class="form-control-plaintext">[NIF do Fornecedor]</p>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Contacto Telefónico</label>
                                <p class="form-control-plaintext">[Contacto Telefónico]</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Email</label>
                                <p class="form-control-plaintext">[Email do Fornecedor]</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Website</label>
                                <p class="form-control-plaintext">[Website do Fornecedor]</p>
                            </div>
                        </div>
                        <div class="row mb-3">    
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Morada</label>
                                <p class="form-control-plaintext">[Morada do Fornecedor]</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipo de fornecedor</label>
                                <p class="form-control-plaintext">[Tipo de fornecedor]</p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Pessoa de Contacto</label>
                                <p class="form-control-plaintext">[Pessoa de Contacto]</p>
                            </div>
                        
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Telefone da pessoa de contacto</label>
                                <p class="form-control-plaintext">[Telefone da pessoa de contacto]</p>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Observações</label>
                                    <p class="form-control-plaintext">[Observações]</p>
                                </div>
                            </div>
                        </div>

                    </div>    
                    <div class="d-flex justify-content-end">
                        <a href="listar.php" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </main>

    <?php include '../includes/sidebarmobile.php'; ?>    

<?php include '../includes/footer.php'; ?>
