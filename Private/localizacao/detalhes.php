<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
 
                    <h2 class="mb-4">
                        <strong><i class="fa-solid fa-map-location-dot fa-1x mb-3"></i> Detalhes da Localização</strong>
                    </h2>
                    <hr>
 
                    <!-- Linha 1: Edifício + Piso -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Edifício</label>
                            <p class="form-control-plaintext">[Edifício]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Piso</label>
                            <p class="form-control-plaintext">[Piso]</p>
                        </div>
                    </div>
 
                    <!-- Linha 2: Serviço/Departamento + Sala/Gabinete -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Serviço/Departamento</label>
                            <p class="form-control-plaintext">[Serviço/Departamento]</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sala/Gabinete</label>
                            <p class="form-control-plaintext">[Sala/Gabinete]</p>
                        </div>
                    </div>
 
                    <hr class="my-4">
 
                    <!-- Equipamentos nesta localização (a relação) -->
                    <h5 class="mb-3">
                        <i class="fa-solid fa-laptop-medical me-2" style="color:#0077a8;"></i>
                        Equipamentos nesta localização
                        <span class="badge bg-primary ms-1">[Nº]</span>
                    </h5>
 
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Designação</th>
                                    <th>Marca / Modelo</th>
                                    <th>Estado</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--
                                    PHP gera estas linhas a partir dos equipamentos
                                    associados a esta localização.
                                    Se não houver nenhum, mostra a mensagem em baixo.
                                -->
                                <tr>
                                    <td>[Código]</td>
                                    <td>[Designação]</td>
                                    <td>[Marca/Modelo]</td>
                                    <td>[Estado]</td>
                                    <td class="text-center">
                                        <a href="../equipamentos/detalhes.html" class="text-decoration-none" style="color:#0077a8;" title="Ver equipamento">
                                            <i class="fa-solid fa-eye me-1"></i>Consultar
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Mostrar isto quando a localização não tem equipamentos -->
                        <p class="text-center text-muted mt-3 d-none" id="semEquipamentos">
                            <i class="fa-solid fa-circle-info me-2"></i>Esta localização não tem equipamentos associados.
                        </p>
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

<?php include '../includes/sidebarmobile.php'; ?>    

<?php include '../includes/footer.php'; ?>
