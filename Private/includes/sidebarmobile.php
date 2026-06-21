<?php $perfil = $_SESSION['perfil'] ?? ''; ?>
<div class="offcanvas offcanvas-start text-white" style="background-color:#0077a8;" id="menuMobile">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="offcanvas" style="background-color: #0077a8;" aria-label="Fechar"></button>
        </div>

        <div class="offcanvas-body">
            <nav class="menu-items">
                <a href="<?php echo BASE_URL; ?>/private/index.php" class="menu-link"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                <?php if ($perfil === 'Administrador') : ?>
                <a href="<?php echo BASE_URL; ?>/private/gestaoconteudos.php" class="menu-link"><i class="fa-solid fa-sitemap"></i> Gestão de conteúdos</a>
                <?php endif; ?>
                <?php if (in_array($perfil, ['Administrador', 'Gestor de Logística', 'Profissional de saúde'], true)) : ?>
                <a href="<?php echo BASE_URL; ?>/private/localizacao/listar.php" class="menu-link"><i class="fa-solid fa-map-location-dot"></i> Localização</a>
                <?php endif; ?>
                <?php if (in_array($perfil, ['Administrador', 'Gestor de Logística'], true)) : ?>
                <a href="<?php echo BASE_URL; ?>/private/fornecedores/listar.php" class="menu-link"><i class="fa-solid fa-truck"></i> Gestão de Fornecedores</a>
                <?php endif; ?>
                <?php if (in_array($perfil, ['Administrador', 'Técnico', 'Profissional de saúde'], true)) : ?>
                <a href="<?php echo BASE_URL; ?>/private/equipamentos/listar.php" class="menu-link"><i class="fa-solid fa-gears"></i> Gestão de Equipamentos</a>
                <?php endif; ?>
                <?php if (in_array($perfil, ['Administrador', 'Técnico'], true)) : ?>
                <a href="<?php echo BASE_URL; ?>/private/documentacao/listar.php" class="menu-link"><i class="fa-solid fa-file-medical"></i> Gestão de Documentação</a>
                <a href="<?php echo BASE_URL; ?>/private/garantiacontrato/listar.php" class="menu-link"><i class="fa-solid fa-file-contract"></i> Garantias e Contratos</a>
                <?php endif; ?>
            </nav>
        </div>
</div>