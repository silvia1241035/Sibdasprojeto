<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 col-lg-2 text-white p-3 min-vh-100 d-none d-lg-block">
                <div class="sidebar">
                    <h4 class="menu-title">Menu</h4>   

                    <nav class="menu-items">
                        <a href="../Index.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-chart-line"></i>  Dashboard</a>
                        <a href="../gestaoconteudos.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-sitemap"></i>  Gestão de conteúdos</a>
                        <a href="../equipamentos/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-laptop-medical"></i>  Equipamentos</a>
                        <a href="listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-map-location-dot"></i>  Localização</a> 
                        <a href="../fornecedores/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-truck"></i>  Fornecedores</a>
                        <a href="../documentacao/listar.html" class="menu-link" style="transition: background-color 0.3s ease;">  <i class="fa-solid fa-file-medical"></i>  Documentação</a>                 
                        <a href="../garantiacontrato/listar.html" class="menu-link" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-contract"></i>  Garantias e     Contratos</a>
                    </nav>
                </div>    
            </aside>
        </div>
    </div>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-pen fa-1x mb-3"></i> Atualizar localização</strong></h2>
                    <hr>
 
                    <form action="#" method="post" novalidate id="formLocalizacao">
 
                        <!-- Área de erros -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao atualizar a localização. Por favor, tente novamente.
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

    <div class="offcanvas offcanvas-start text-white" style="background-color:#0077a8;" id="menuMobile">
        <div class="offcanvas-header d-flex flex-column gap-4">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="offcanvas" style="background-color: #0077a8;"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column gap-4">
            <a href="../Index.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-chart-line"></i>  Dashboard</a>
            <a href="../gestaoconteudos.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-sitemap"></i>  Gestão de conteúdos</a>
            <a href="../equipamentos/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-gears"></i>  Gestão de Equipamentos</a>
            <a href="listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-map-location-dot"></i>  Localização</a>
            <a href="../fornecedores/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-truck"></i>  Gestão de Fornecedores</a>
            <a href="../documentacao/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-medical"></i>  Gestão de Documentação</a>
            <a href="../garantiacontrato/listar.html" class="text-white text-decoration-none link-light link-opacity-50-hover" style="transition: background-color 0.3s ease;"> <i class="fa-solid fa-file-contract"></i>  Garantias e Contratos</a>
        </div>
    </div>

 <?php include '../includes/footer.php'; ?>
