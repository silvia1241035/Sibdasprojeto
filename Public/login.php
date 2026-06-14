<?php
session_start();

// Recolhe mensagens de erro guardadas na sessão (vindas do processamento)
$validation_errors = [];
if (!empty($_SESSION['validation_errors'])) {
    $validation_errors = $_SESSION['validation_errors'];
    unset($_SESSION['validation_errors']);
}

$server_error = '';
if (!empty($_SESSION['server_error'])) {
    $server_error = $_SESSION['server_error'];
    unset($_SESSION['server_error']);
}

 require_once __DIR__ . '/../Private/includes/header.php'; ?>


    <div class="container-fluid p-0 position-relative" style="min-height: 100vh; display: flex; justify-content: center; align-items: center; background: linear-gradient(to bottom, #0077a8, #ffffff);">
                <div style="width: 100%; max-width: 350px;">
                            <form action="<?php echo BASE_URL; ?>/private/processa_login.php" method="post">
                                
                                <div class="card p-4 shadow-lg" style="border-radius: 15px;">
                                    <h4>Aceder à Área reservada</h4>
                                    <div class="mb-3">
                                    <!-- Utilizador -->
                                        <label for="text_username" class="form-label">Utilizador</label>
                                        <input type="email" name="text_username" id="text_username" class="form-control" placeholder="exemplo@gmail.com" required>
                                    </div>
                                    <div class="mb-3">
                                    <!-- Password -->
                                        <label for="text_password" class="form-label">Password</label>
                                        <input type="password" name="text_password" id="text_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3 text-center">
                                    <!-- Submit -->
                                        <button type="submit" class="btn btn-login w-100">
                                            Entrar <i class="fa-solid fa-right-to-bracket ms-2"></i>
                                        </button>
                                    </div>
                                    <p class="text-center mt-3">
                                        <a href="<?php echo BASE_URL; ?>/public/index.php" class="text-black text-decoration-none">
                                            Clique aqui para voltar à página inicial.
                                        </a>
                                    </p>
                                    <!-- Erros -->
                                    <?php if (!empty($validation_errors)) : ?>
                                        <div class="alert alert-danger p-2 text-center">
                                            <?php foreach ($validation_errors as $error) : ?>
                                                <div><?= htmlspecialchars($error) ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Erro de servidor (ex: login inválido) -->
                                    <?php if (!empty($server_error)) : ?>
                                        <div class="alert alert-danger p-2 text-center">
                                            <div><?= htmlspecialchars($server_error) ?></div>
                                        </div>
                                    <?php endif; ?> 
                                    
                                    </div>
                                </div>
                            </form>
                       
                             
    <?php require_once __DIR__ . '/../Private/includes/footer.php'; ?>
