<?php
require_once __DIR__ . '/../private/includes/funcoes.php';
$conteudos = carregar_conteudos();
$slides = carregar_slides();
if (empty($slides)) {
    $slides = [
        (object)['ordem' => 1, 'imagem_path' => BASE_URL . '/assets/img/Slide 1.png', 'alt_text' => 'Primeiro slide'],
        (object)['ordem' => 2, 'imagem_path' => BASE_URL . '/assets/img/Slide 2.png', 'alt_text' => 'Segundo slide'],
        (object)['ordem' => 3, 'imagem_path' => BASE_URL . '/assets/img/Slide 3.png', 'alt_text' => 'Terceiro slide'],
    ];
}
?>
<?php include '../private/includes/header.php'; ?>
<body>
        <nav class="bng-navbar">
            <div>
                <img id="logo_principal" src="<?= htmlspecialchars(conteudo_imagem($conteudos, 'logo_principal', BASE_URL . '/assets/img/logo.png')) ?>" alt="Logo InveMed">
                <h1 id="titulo_site"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'titulo_site', 'InveMed')) ?></strong></h1>
            </div>
            <div class="container-navegacao">
                <a id="nav_inicio" href="#inicio"><?= htmlspecialchars(conteudo_texto($conteudos, 'nav_inicio', 'Início')) ?></a>
                <a id="nav_quemsomos" href="#quem-somos"><?= htmlspecialchars(conteudo_texto($conteudos, 'nav_quemsomos', 'Sobre Nós')) ?></a>
                <a id="nav_servicos" href="#servicos"><?= htmlspecialchars(conteudo_texto($conteudos, 'nav_servicos', 'Serviços')) ?></a>
                <a id="nav_contacto" href="#contacto"><?= htmlspecialchars(conteudo_texto($conteudos, 'nav_contacto', 'Contacto')) ?></a>
            </div>

            <div class="nav-cliente">
                <a id="link_area_restrita" href="login.php" target="_blank"><?= htmlspecialchars(conteudo_texto($conteudos, 'link_area_restrita', 'Área Restrita')) ?></a>

            </div>
        </nav>

        <section id="inicio">
            <div id="carrosselInveMed" class="carousel carousel-dark slide" data-bs-ride="carousel">

                <div class="carousel-indicators">
                    <?php foreach ($slides as $i => $slide) : ?>
                        <button type="button" data-bs-target="#carrosselInveMed" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" <?= $i === 0 ? 'aria-current="true"' : '' ?> aria-label="Slide <?= $i + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-inner">
                    <?php foreach ($slides as $i => $slide) : ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img id="slide<?= $slide->ordem ?>" src="<?= htmlspecialchars($slide->imagem_path) ?>" class="d-block w-100" alt="<?= htmlspecialchars($slide->alt_text ?? '') ?>">
                    </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carrosselInveMed" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carrosselInveMed" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Seguinte</span>
                </button>

            </div>
            <div class="inicio">
                <h2 id="inicio_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'inicio_titulo', 'Bem-Vindo à InveMed')) ?></strong></h2>
                <p id="inicio_texto"><?= htmlspecialchars(conteudo_texto($conteudos, 'inicio_texto', 'A solução de eleição para organizar os seus equipamentos médicos.')) ?></p>
            </div>
        </section>

        <section id="quem-somos">
            <div class="container">
                <h1 id="sobre_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_titulo', 'SOBRE NÓS')) ?></strong></h1>
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="pessoa">
                                <i class="fa-solid fa-user-doctor"></i>
                                <h2 id="sobre_card1_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card1_titulo', 'Anos de experiência em organização médica')) ?></strong></h2>
                                <p id="sobre_card1_texto"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card1_texto', 'Na InveMed, acreditamos que a eficiência na saúde começa nos bastidores.')) ?></p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="border-start border-secundary border-0.5 ps-3">
                                    <div class="pessoa">
                                        <i class="fa-solid fa-handshake"></i>
                                        <h2 id="sobre_card2_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card2_titulo', 'Os pilares da Empresa')) ?></strong></h2>
                                        <p id="sobre_card2_texto1"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card2_texto1', '')) ?></p>
                                        <p id="sobre_card2_texto2"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card2_texto2', '')) ?></p>
                                        <p id="sobre_card2_texto3"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card2_texto3', '')) ?></p>
                                    </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="border-start border-secondary border-0.5 ps-3">
                                    <div class="pessoa">
                                       <i class="fa-solid fa-users"></i>
                                        <h2 id="sobre_card3_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_titulo', 'Para quem trabalhamos')) ?></strong></h2>
                                        <h3 id="sobre_card3_sub1"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_sub1', 'Hospitais Públicos e Privados')) ?></strong></h3>
                                        <p id="sobre_card3_texto1"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_texto1', '')) ?></p>
                                        <h3 id="sobre_card3_sub2"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_sub2', 'Clínicas Médicas e Dentárias')) ?></strong></h3>
                                        <p id="sobre_card3_texto2"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_texto2', '')) ?></p>
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="servicos">
            <div class="container">
                <h1 id="servicos_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'servicos_titulo', 'SERVIÇOS')) ?></strong></h1>
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="servicos">
                            <i class="fa-solid fa-laptop-medical"></i>
                            <h2 id="servico1_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'servico1_titulo', 'Gestão dos equipamentos médicos')) ?></strong></h2>
                            <p id="servico1_texto"><?= htmlspecialchars(conteudo_texto($conteudos, 'servico1_texto', '')) ?></p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                            <div class="border-start border-secundary border-0.5 ps-3">
                                <div class="servicos">
                                <i class="fa-solid fa-file-shield"></i>
                                <h2 id="servico2_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'servico2_titulo', 'Gestão de documentação')) ?></strong></h2>
                                <p id="servico2_texto"><?= htmlspecialchars(conteudo_texto($conteudos, 'servico2_texto', '')) ?></p>
                                </div>
                            </div>

                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="border-start border-secundary border-0.5 ps-3">
                            <div class="servicos">
                                <i class="fa-solid fa-location-dot"></i>
                                <h2 id="servico3_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'servico3_titulo', 'Mapeamento e Rastreabilidade Logística')) ?></strong></h2>
                                <p id="servico3_texto"><?= htmlspecialchars(conteudo_texto($conteudos, 'servico3_texto', '')) ?></p>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="border-start border-secundary border-0.5 ps-3">
                            <div class="servicos">
                                <i class="fa-solid fa-shield-heart"></i>
                                <h2 id="servico4_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'servico4_titulo', 'Consultoria da Criticidade Clínica')) ?></strong></h2>
                                <p id="servico4_texto"><?= htmlspecialchars(conteudo_texto($conteudos, 'servico4_texto', '')) ?></p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="contacto">
            <h1 id="contacto_titulo"><strong><?= htmlspecialchars(conteudo_texto($conteudos, 'contacto_titulo', 'CONTACTO')) ?></strong></h1>
            <p id="contacto_texto"><?= htmlspecialchars(conteudo_texto($conteudos, 'contacto_texto', '')) ?></p>
            <form id="contactForm">
                <label for="nome"><?= htmlspecialchars(conteudo_texto($conteudos, 'contacto_label_nome', 'Nome:')) ?></label>
                <input type="text" id="nome" name="nome" required>

                <label for="email"><?= htmlspecialchars(conteudo_texto($conteudos, 'contacto_label_email', 'Email:')) ?></label>
                <input type="email" id="email" name="email" required>

                <label for="mensagem"><?= htmlspecialchars(conteudo_texto($conteudos, 'contacto_label_mensagem', 'Mensagem:')) ?></label>
                <textarea id="mensagem" name="mensagem" rows="4" required></textarea> <button type="submit"><?= htmlspecialchars(conteudo_texto($conteudos, 'contacto_botao_enviar', 'Enviar mensagem')) ?></button>
            </form>

        </section>

        <footer class="footer-container">
            <br>
            <div class="footer-section">
                <strong>LOCALIZAÇÃO</strong>
                <p id="footer_localizacao"><?= nl2br(htmlspecialchars(conteudo_texto($conteudos, 'footer_localizacao', 'Rua xxxxxxxxxxx, Porto, Portugal'))) ?></p>
            </div>
            <div class="footer-section">
                <strong>HORÁRIO</strong>
                <p id="footer_horario1"><?= htmlspecialchars(conteudo_texto($conteudos, 'footer_horario1', '2ª a Sábado: 8h-19h')) ?></p>
                <p id="footer_horario2"><?= htmlspecialchars(conteudo_texto($conteudos, 'footer_horario2', 'Domingos e Feriados: Encerrado')) ?></p>
            </div>
            <div class="footer-section">
                <strong>CONTACTOS</strong>
                <p id="footer_email"><?= htmlspecialchars(conteudo_texto($conteudos, 'footer_email', 'Email: geral@invemed.pt')) ?></p>
                <p id="footer_telefone"><?= htmlspecialchars(conteudo_texto($conteudos, 'footer_telefone', 'Telefone: +351 9xx xxx xxx')) ?></p>
            </div>
        </footer>

   <?php include '../private/includes/footer.php'; ?>
