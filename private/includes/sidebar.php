<!--side bar-->
<?php $perfil = $_SESSION['perfil'] ?? ''; ?>

<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 text-white p-3 d-none d-lg-block">
            <div class="sidebar">
                <h4 class="menu-title">Menu</h4>

                <nav class="menu-items">
                    <a href="<?php echo BASE_URL; ?>/private/index.php" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                    <?php if ($perfil === 'Administrador') : ?>
                    <a href="<?php echo BASE_URL; ?>/private/utilizadores/listar.php" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-users-gear"></i> Gestão de Utilizadores</a>
                    <a href="<?php echo BASE_URL; ?>/private/gestaoconteudos.php" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-sitemap"></i> Gestão de conteúdos</a>
                    <a href="<?php echo BASE_URL; ?>/private/logs.php" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-clipboard-list"></i> Registo de Eventos</a>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Gestor de Logística', 'Profissional de saúde'], true)) : ?>
                    <a href="<?php echo BASE_URL; ?>/private/localizacao/listar.php" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-map-location-dot"></i> Localização</a>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Gestor de Logística'], true)) : ?>
                    <a href="<?php echo BASE_URL; ?>/private/fornecedores/listar.php" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-truck"></i>  Fornecedores</a>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Técnico', 'Profissional de saúde'], true)) : ?>
                    <a href="<?php echo BASE_URL; ?>/private/equipamentos/listar.php" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-laptop-medical"></i> Equipamentos</a>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Técnico', 'Profissional de saúde'], true)) : ?>
                    <a href="<?php echo BASE_URL; ?>/private/documentacao/listar.php" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-file-medical"></i> Documentação</a>
                    <?php endif; ?>
                    <?php if (in_array($perfil, ['Administrador', 'Técnico'], true)) : ?>
                    <a href="<?php echo BASE_URL; ?>/private/garantiacontrato/listar.php" class="menu-link" style="transition: background-color 0.3s ease;"><i class="fa-solid fa-file-contract"></i> Garantias e Contratos</a>
                    <?php endif; ?>
                </nav>
            </div>
        </aside>
    </div>
</div>

  