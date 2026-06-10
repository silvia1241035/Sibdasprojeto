<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar nova localização</strong></h2>
                    <hr>
 
                    <form action="#" method="post" novalidate id="formLocalizacao">
 
                        <!-- Área de erros -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao inserir a localização. Por favor, tente novamente.
                        </div>
 
                        <!-- Linha 1: Edifício + Piso -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edificio" class="form-label">Edifício<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="edificio" name="edificio_localizacao" required placeholder="Ex: Edifício A">
                                <div class="invalid-feedback">Por favor, insira o edifício.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="piso" class="form-label">Piso</label>
                                <input type="text" class="form-control" id="piso" name="piso_localizacao" placeholder="Ex: Piso 1 / R/C">
                            </div>
                        </div>
 
                        <!-- Linha 2: Serviço/Departamento + Sala/Gabinete -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="servico" class="form-label">Serviço/Departamento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <!-- PHP pode gerar as opções a partir dos serviços do hospital -->
                                <select class="form-select" id="servico" name="servico_localizacao" required>
                                    <option value="">Selecione...</option>
                                    <option value="UCI">UCI</option>
                                    <option value="Medicina">Medicina</option>
                                    <option value="Urgência">Urgência</option>
                                    <option value="Cardiologia">Cardiologia</option>
                                    <option value="Bloco Operatório">Bloco Operatório</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o serviço/departamento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="sala" class="form-label">Sala/Gabinete</label>
                                <input type="text" class="form-control" id="sala" name="sala_localizacao" placeholder="Ex: Sala 101">
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
