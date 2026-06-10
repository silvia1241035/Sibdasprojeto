<?php include '../../Private/includes/header.php'; ?>

<body >
    <div class="container-fluid p-0 position-relative" style="min-height: 100vh; display: flex; justify-content: center; align-items: center; background: linear-gradient(to bottom, #0077a8, #ffffff);">
                <div style="width: 100%; max-width: 350px;">
                            <form action="Public/index.php" method="post">
                                
                                <div class="card p-4 shadow-lg" style="border-radius: 15px;">
                                    <h4>Aceder à Área reservada</h4>
                                    <div class="mb-3">
                                    <!-- Utilizador -->
                                        <label for="email" class="form-label">Utilizador</label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="exemplo@gmail.com" required>
                                    </div>
                                    <div class="mb-3">
                                    <!-- Password -->
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" name="password" id="password" class="form-control" required>
                                    </div>
                                    <div class="mb-3 text-center">
                                    <!-- Submit -->
                                        <button type="button" id="btnEntrar" class="btn btn-login w-100">
                                            Entrar <i class="fa-solid fa-right-to-bracket ms-2"></i>
                                        </button>
                                    </div>
                                    <p class="text-center mt-3">
                                        <a href="../../Public/index.php" class="text-black text-decoration-none">
                                            Clique aqui para voltar à página inicial.
                                        </a>
                                    </p>
                                    <!-- Erros -->
                                    <div id="mensagemErro" class="alert alert-danger p-2 text-center d-none">
                                        Erro: Utilizador não registado.
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>    
                </div>
            </div> 
        </div>
        <div style="position: absolute; top: 24px; left: 24px; display: flex; align-items: center; z-index: 999;">
            <img src="../../assets/img/logo.png" height="40" alt="InveMed Logo">
            <h2 class="ms-2 mb-0 text-dark" style="font-family: sans-serif;"><strong>InveMed</strong></h2> 
        </div>                     
    <?php include '../../Private/includes/footer.php'; ?>
