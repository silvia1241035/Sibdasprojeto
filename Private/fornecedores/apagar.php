<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-center mt-4">
                    <div class="card w-100 shadow rounded text-center p-4" style="max-width: 700px;">
                        
                        <div class="text-warning display-4 mb-3">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        
                        <p class="mb-2 fs-5">Deseja eliminar o fornecedor?</p>
                        
                        <h4 class="mb-4"><strong>[Nome do Fornecedor]</strong></h4>
                        
                        <div class="mb-4">
                            <span class="d-block mb-1"><i class="fa-solid fa-at me-2"></i><strong>[Email do Fornecedor]</strong></span> 
                            <span class="d-block"><i class="fa-solid fa-phone me-2"></i><strong>[Telefone do Fornecedor]</strong></span>
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="listar.html" class="btn btn-outline-secondary px-4">
                                <i class="fa-solid fa-xmark me-2"></i>Não
                            </a>
                            <a href="#" class="btn btn-danger px-4">
                                <i class="fa-solid fa-check me-2"></i>Sim
                            </a>
                        </div>
                    </div>
                </div>
            </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
