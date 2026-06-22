<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);

// Campos de texto geridos por esta página: chave => valor por defeito
$camposTexto = [
    'titulo_site'          => 'InveMed',
    'nav_inicio'            => 'Início',
    'nav_quemsomos'         => 'Sobre Nós',
    'nav_servicos'          => 'Serviços',
    'nav_contacto'          => 'Contacto',
    'link_area_restrita'    => 'Área Restrita',
    'inicio_titulo'         => 'Bem-Vindo à InveMed',
    'inicio_texto'          => 'A solução de eleição para organizar os seus equipamentos médicos.',
    'sobre_titulo'          => 'SOBRE NÓS',
    'sobre_card1_titulo'    => 'Anos de experiência em organização médica',
    'sobre_card1_texto'     => 'Na InveMed, acreditamos que a eficiência na saúde começa nos bastidores. Nascemos da necessidade de transformar a gestão de inventário num processo simples, inteligente e livre de falhas, permitindo que as equipas médicas se foquem no que realmente importa: salvar vidas.',
    'sobre_card2_titulo'    => 'Os pilares da Empresa',
    'sobre_card2_texto1'    => 'Tratamos o inventário dos vossos equipamentos com o mesmo nível de precisão e seriedade exigidos num bloco operatório.',
    'sobre_card2_texto2'    => 'Trabalhamos com total transparência e responsabilidade para garantir a segurança dos vossos dados e bens.',
    'sobre_card2_texto3'    => 'Procuramos sempre as melhores metodologias e soluções tecnológicas para simplicar a gestão logística hospitalar.',
    'sobre_card3_titulo'    => 'Para quem trabalhamos',
    'sobre_card3_sub1'      => 'Hospitais Públicos e Privados',
    'sobre_card3_texto1'    => 'Pretendemos fazer a gestão e catalogação de grandes volumes de ativos em múltiplos departamentos.',
    'sobre_card3_sub2'      => 'Clínicas Médicas e Dentárias',
    'sobre_card3_texto2'    => 'Tencionamos organizar e controlar os stocks de pequenos equipamentos para garantir a fluidez do dia a dia.',
    'servicos_titulo'       => 'SERVIÇOS',
    'servico1_titulo'       => 'Gestão dos equipamentos médicos',
    'servico1_texto'        => 'Em vez de folhas de Excel dispersas, a InveMed oferece uma plataforma única para acompanhar o ciclo de vida dos dispositivos médicos.',
    'servico2_titulo'       => 'Gestão de documentação',
    'servico2_texto'        => 'Um dos grandes problemas hospitalares é perder manuais ou certificados. Com a InveMed, é garantido que a instituição está sempre pronta para inspeções.',
    'servico3_titulo'       => 'Mapeamento e Rastreabilidade Logística',
    'servico3_texto'        => 'Com a InveMed, consegue sempre saber onde está o equipamento para evitar perdas e otimizar o tempos das equipas médicas.',
    'servico4_titulo'       => 'Consultoria da Criticidade Clínica',
    'servico4_texto'        => 'A InveMed ajuda a classificar os equipamentos médicos de acordo com a sua criticidade e estado.',
    'contacto_titulo'       => 'CONTACTO',
    'contacto_texto'        => 'Entre em contacto connosco para organizarmos a sua unidade de saúde.',
    'footer_localizacao'    => 'Rua xxxxxxxxxxx, Porto, Portugal',
    'footer_horario1'       => '2ª a Sábado: 8h-19h',
    'footer_horario2'       => 'Domingos e Feriados: Encerrado',
    'footer_email'          => 'Email: geral@invemed.pt',
    'footer_telefone'       => 'Telefone: +351 9xx xxx xxx',
];

// Campos de imagem geridos por esta página: chave => caminho por defeito
$camposImagem = [
    'logo_principal' => BASE_URL . '/assets/img/logo.png',
];

// Slides do carrossel: nome do campo do formulário => ordem na slides_carousel
$slidesPorOrdem = ['slide1' => 1, 'slide2' => 2, 'slide3' => 3];

$extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$erro_sistema = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmtTexto = $ligacao->prepare("
            INSERT INTO conteudos_publicos (chave, conteudo, id_utilizador) VALUES (:chave, :conteudo, :uid)
            ON DUPLICATE KEY UPDATE conteudo = VALUES(conteudo), id_utilizador = VALUES(id_utilizador)
        ");
        foreach (array_keys($camposTexto) as $chave) {
            $valor = trim($_POST[$chave] ?? '');
            $stmtTexto->execute([
                ':chave'    => $chave,
                ':conteudo' => $valor,
                ':uid'      => null,
            ]);
        }

        $stmtImagem = $ligacao->prepare("
            INSERT INTO conteudos_publicos (chave, imagem_path, id_utilizador) VALUES (:chave, :imagem, :uid)
            ON DUPLICATE KEY UPDATE imagem_path = VALUES(imagem_path), id_utilizador = VALUES(id_utilizador)
        ");
        foreach (array_keys($camposImagem) as $chave) {
            if (!isset($_FILES[$chave]) || $_FILES[$chave]['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($_FILES[$chave]['error'] !== UPLOAD_ERR_OK) {
                continue;
            }
            $ext = strtolower(pathinfo($_FILES[$chave]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $extensoesPermitidas, true)) {
                continue;
            }
            $nomeFicheiro = uniqid($chave . '_') . '.' . $ext;
            $destino = __DIR__ . '/../uploads/conteudos/' . $nomeFicheiro;
            if (move_uploaded_file($_FILES[$chave]['tmp_name'], $destino)) {
                $caminho = BASE_URL . '/uploads/conteudos/' . $nomeFicheiro;
                $stmtImagem->execute([
                    ':chave'  => $chave,
                    ':imagem' => $caminho,
                    ':uid'    => null,
                ]);
            }
        }

        $stmtSlide = $ligacao->prepare("UPDATE slides_carousel SET imagem_path = :imagem WHERE ordem = :ordem");
        foreach ($slidesPorOrdem as $chave => $ordem) {
            if (!isset($_FILES[$chave]) || $_FILES[$chave]['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($_FILES[$chave]['error'] !== UPLOAD_ERR_OK) {
                continue;
            }
            $ext = strtolower(pathinfo($_FILES[$chave]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $extensoesPermitidas, true)) {
                continue;
            }
            $nomeFicheiro = uniqid($chave . '_') . '.' . $ext;
            $destino = __DIR__ . '/../uploads/conteudos/' . $nomeFicheiro;
            if (move_uploaded_file($_FILES[$chave]['tmp_name'], $destino)) {
                $caminho = BASE_URL . '/uploads/conteudos/' . $nomeFicheiro;
                $stmtSlide->execute([':imagem' => $caminho, ':ordem' => $ordem]);
            }
        }

        $_SESSION['success_message'] = 'Conteúdos atualizados com sucesso.';
        header('Location: gestaoconteudos.php');
        exit;
    } catch (PDOException $err) {
        $erro_sistema = "Erro ao guardar os conteúdos: " . $err->getMessage();
    }
    $ligacao = null;
}

$conteudos = carregar_conteudos();

$slidesPorOrdemValor = [];
foreach (carregar_slides() as $slide) {
    $slidesPorOrdemValor[(int)$slide->ordem] = $slide;
}

$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>

<?php include 'includes/header.php'; ?>

<?php include 'includes/nav.php'; ?>

<?php include 'includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 p-4">
                <h1><strong>Bem-vindo à Área de Gestão de Conteúdos</strong></h1>
                <h6 class="mb-10">Aqui podes editar a página principal e no final da página guardar as alterações.</h6>

                <?php if (!empty($success_message)) : ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>
                <?php if (!empty($erro_sistema)) : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" id="formConteudos">

                <div class="d-flex justify-content-end mb-3" style="position: sticky; top: 75px; z-index: 1050;">
                    <button type="submit" class="btn btn-lg shadow-sm" style="background-color: #0077a8; color:white; border-radius: 30px; padding: 10px 28px; transition: transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <i class="fa-regular fa-floppy-disk me-2"></i>Guardar alterações
                    </button>
                </div>

                <nav class="bng-navbar-gc">
                    <div class="logo-wrapper">
                            <img id="logo_principal_preview" src="<?= htmlspecialchars(conteudo_imagem($conteudos, 'logo_principal', $camposImagem['logo_principal'])) ?>" alt="Logo InveMed" style="height: 50px;">
                            <input id="titulo_site" name="titulo_site" type="text" class="input-h1" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'titulo_site', $camposTexto['titulo_site'])) ?>">
                            <input type="file" id="logo_input" name="logo_principal" class="file-editavel" accept="image/*" onchange="document.getElementById('logo_principal_preview').src = URL.createObjectURL(this.files[0])">
                        </div>

                    <div class="container-navegacao-gc">
                        <input id="nav_inicio" name="nav_inicio" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'nav_inicio', $camposTexto['nav_inicio'])) ?>" style="color:#0077a8">
                        <input id="nav_quemsomos" name="nav_quemsomos" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'nav_quemsomos', $camposTexto['nav_quemsomos'])) ?>" style="color:#0077a8">
                        <input id="nav_servicos" name="nav_servicos" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'nav_servicos', $camposTexto['nav_servicos'])) ?>" style="color:#0077a8">
                        <input id="nav_contacto" name="nav_contacto" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'nav_contacto', $camposTexto['nav_contacto'])) ?>" style="color:#0077a8">
                    </div>

                    <div class="nav-cliente-gc">
                        <input id="link_area_restrita" name="link_area_restrita" type="text" class="input-pass" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'link_area_restrita', $camposTexto['link_area_restrita'])) ?>" style="color:#0077a8" >
                    </div>
                </nav>

                <section id="inicio">

                    <div id="carrosselInveMed" class="carousel carousel-dark slide" data-bs-ride="carousel">

                        <div class="carousel-inner">

                            <!-- SLIDE 1 -->
                            <div class="carousel-item active" style="position: relative;">
                                <img id="slide1_preview" src="<?= htmlspecialchars($slidesPorOrdemValor[1]->imagem_path ?? BASE_URL . '/assets/img/Slide 1.png') ?>" class="d-block w-100">
                                <button type="button" class="btn-alterar-imagem" onclick="document.getElementById('slide1').click()">Alterar imagem</button>
                                <input type="file" id="slide1" name="slide1" class="file-editavel" accept="image/*" onchange="document.getElementById('slide1_preview').src = URL.createObjectURL(this.files[0])">
                            </div>

                            <!-- SLIDE 2 -->
                            <div class="carousel-item" style="position: relative;">
                                <img id="slide2_preview" src="<?= htmlspecialchars($slidesPorOrdemValor[2]->imagem_path ?? BASE_URL . '/assets/img/Slide 2.png') ?>" class="d-block w-100">
                                <button type="button" class="btn-alterar-imagem" onclick="document.getElementById('slide2').click()">Alterar imagem</button>
                                <input type="file" id="slide2" name="slide2" class="file-editavel" accept="image/*" onchange="document.getElementById('slide2_preview').src = URL.createObjectURL(this.files[0])">
                            </div>

                            <!-- SLIDE 3 -->
                            <div class="carousel-item" style="position: relative;">
                                <img id="slide3_preview" src="<?= htmlspecialchars($slidesPorOrdemValor[3]->imagem_path ?? BASE_URL . '/assets/img/Slide 3.png') ?>" class="d-block w-100">
                                <button type="button" class="btn-alterar-imagem" onclick="document.getElementById('slide3').click()">Alterar imagem</button>
                                <input type="file" id="slide3" name="slide3" class="file-editavel" accept="image/*" onchange="document.getElementById('slide3_preview').src = URL.createObjectURL(this.files[0])">
                            </div>

                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#carrosselInveMed" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>

                        <button class="carousel-control-next" type="button" data-bs-target="#carrosselInveMed" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>

                    </div>

                    <div class="inicio">
                        <input id="inicio_titulo" name="inicio_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'inicio_titulo', $camposTexto['inicio_titulo'])) ?>">
                        <textarea id="inicio_texto" name="inicio_texto" class="textarea-paragrafo" style="color:black"><?= htmlspecialchars(conteudo_texto($conteudos, 'inicio_texto', $camposTexto['inicio_texto'])) ?></textarea>
                    </div>

                </section>

                <section id="quem-somos">
                    <div class="container">
                        <input id="sobre_titulo" name="sobre_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_titulo', $camposTexto['sobre_titulo'])) ?>">

                        <div class="row">

                            <!-- CARD 1 -->
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="pessoa">
                                    <i class="fa-solid fa-user-doctor"></i>
                                    <input id="sobre_card1_titulo" name="sobre_card1_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card1_titulo', $camposTexto['sobre_card1_titulo'])) ?>" style="color:#0077a8">
                                    <textarea id="sobre_card1_texto" name="sobre_card1_texto" class="textarea-paragrafo"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card1_texto', $camposTexto['sobre_card1_texto'])) ?></textarea>
                                </div>
                            </div>

                            <!-- CARD 2 -->
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="pessoa">
                                    <i class="fa-solid fa-handshake"></i>
                                    <input id="sobre_card2_titulo" name="sobre_card2_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card2_titulo', $camposTexto['sobre_card2_titulo'])) ?>" style="color:#0077a8">
                                    <textarea id="sobre_card2_texto1" name="sobre_card2_texto1" class="textarea-paragrafo"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card2_texto1', $camposTexto['sobre_card2_texto1'])) ?></textarea>
                                    <textarea id="sobre_card2_texto2" name="sobre_card2_texto2" class="textarea-paragrafo"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card2_texto2', $camposTexto['sobre_card2_texto2'])) ?></textarea>
                                    <textarea id="sobre_card2_texto3" name="sobre_card2_texto3" class="textarea-paragrafo"><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card2_texto3', $camposTexto['sobre_card2_texto3'])) ?></textarea>
                                </div>
                            </div>

                            <!-- CARD 3 -->
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="pessoa">
                                    <i class="fa-solid fa-users"></i>

                                    <input
                                        type="text"
                                        id="sobre_card3_titulo"
                                        name="sobre_card3_titulo"
                                        class="input-h2"
                                        value="<?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_titulo', $camposTexto['sobre_card3_titulo'])) ?>"
                                    >
                                    <!-- SUBTÍTULO 1 -->
                                    <input
                                        type="text"
                                        id="sobre_card3_sub1"
                                        name="sobre_card3_sub1"
                                        class="input-h3"
                                        value="<?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_sub1', $camposTexto['sobre_card3_sub1'])) ?>"
                                        style="font-size: 1.6rem; margin-top: 10px;"
                                    >

                                    <!-- TEXTO 1 -->
                                    <textarea
                                        id="sobre_card3_texto1"
                                        name="sobre_card3_texto1"
                                        class="textarea-paragrafo"
                                    ><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_texto1', $camposTexto['sobre_card3_texto1'])) ?></textarea>

                                    <!-- SUBTÍTULO 2 -->
                                    <input
                                        type="text"
                                        id="sobre_card3_sub2"
                                        name="sobre_card3_sub2"
                                        class="input-h3"
                                        value="<?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_sub2', $camposTexto['sobre_card3_sub2'])) ?>"
                                        style="font-size: 1.6rem; margin-top: 20px;"
                                    >

                                    <!-- TEXTO 2 -->
                                    <textarea
                                        id="sobre_card3_texto2"
                                        name="sobre_card3_texto2"
                                        class="textarea-paragrafo"
                                    ><?= htmlspecialchars(conteudo_texto($conteudos, 'sobre_card3_texto2', $camposTexto['sobre_card3_texto2'])) ?></textarea>

                                </div>
                            </div>
                        </div>
                <section id="servicos">
                    <div class="container">
                        <input id="servicos_titulo" name="servicos_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'servicos_titulo', $camposTexto['servicos_titulo'])) ?>">

                        <div class="row">

                            <!-- SERVIÇO 1 -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="servicos">
                                    <i class="fa-solid fa-laptop-medical"></i>
                                    <input id="servico1_titulo" name="servico1_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'servico1_titulo', $camposTexto['servico1_titulo'])) ?>" style="color:#0077a8">
                                    <textarea id="servico1_texto" name="servico1_texto" class="textarea-paragrafo"><?= htmlspecialchars(conteudo_texto($conteudos, 'servico1_texto', $camposTexto['servico1_texto'])) ?></textarea>
                                </div>
                            </div>

                            <!-- SERVIÇO 2 -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="servicos">
                                    <i class="fa-solid fa-file-shield"></i>
                                    <input id="servico2_titulo" name="servico2_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'servico2_titulo', $camposTexto['servico2_titulo'])) ?>" style="color:#0077a8">
                                    <textarea id="servico2_texto" name="servico2_texto" class="textarea-paragrafo"><?= htmlspecialchars(conteudo_texto($conteudos, 'servico2_texto', $camposTexto['servico2_texto'])) ?></textarea>
                                </div>
                            </div>

                            <!-- SERVIÇO 3 -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="servicos">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <input id="servico3_titulo" name="servico3_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'servico3_titulo', $camposTexto['servico3_titulo'])) ?>" style="color:#0077a8">
                                    <textarea id="servico3_texto" name="servico3_texto" class="textarea-paragrafo"><?= htmlspecialchars(conteudo_texto($conteudos, 'servico3_texto', $camposTexto['servico3_texto'])) ?></textarea>
                                </div>
                            </div>

                            <!-- SERVIÇO 4 -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="servicos">
                                    <i class="fa-solid fa-shield-heart"></i>
                                    <input id="servico4_titulo" name="servico4_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'servico4_titulo', $camposTexto['servico4_titulo'])) ?>" style="color:#0077a8">
                                    <textarea id="servico4_texto" name="servico4_texto" class="textarea-paragrafo"><?= htmlspecialchars(conteudo_texto($conteudos, 'servico4_texto', $camposTexto['servico4_texto'])) ?></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
                <section id="contacto-gc">
                    <input id="contacto_titulo" name="contacto_titulo" type="text" class="input-titulo" value="<?= htmlspecialchars(conteudo_texto($conteudos, 'contacto_titulo', $camposTexto['contacto_titulo'])) ?>" style="font-size: 2rem;">
                    <textarea id="contacto_texto" name="contacto_texto" class="textarea-paragrafo"><?= htmlspecialchars(conteudo_texto($conteudos, 'contacto_texto', $camposTexto['contacto_texto'])) ?></textarea>

                    <div id="contactForm-gc">
                        <input type="text" class="input-titulo" value="Nome:" disabled>
                        <div class="caixa"></div>
                        <input type="text" class="input-titulo" value="Email:" disabled>
                        <div class="caixa"></div>
                        <input type="text" class="input-titulo" value="Mensagem:" disabled>
                        <div class="caixa"></div>
                        <input type="text" class="input-mensagem" value="Enviar mensagem" disabled style="color:white; background-color:#0077a8; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; width: 70vw; text-align: center;">
                    </div>
                </section>
                <footer class="footer-container">

                    <div class="footer-section">
                        <input type="text" class="input-titulo" value="LOCALIZAÇÃO" style="color:white" disabled>
                        <textarea id="footer_localizacao" name="footer_localizacao" class="textarea-paragrafo" style="color:#d6d6d6"><?= htmlspecialchars(conteudo_texto($conteudos, 'footer_localizacao', $camposTexto['footer_localizacao'])) ?></textarea>
                    </div>

                    <div class="footer-section">
                        <input type="text" class="input-titulo" value="HORÁRIO" style="color:white" disabled>
                        <textarea id="footer_horario1" name="footer_horario1" class="textarea-paragrafo" style="color:#d6d6d6"><?= htmlspecialchars(conteudo_texto($conteudos, 'footer_horario1', $camposTexto['footer_horario1'])) ?></textarea>
                        <textarea id="footer_horario2" name="footer_horario2" class="textarea-paragrafo" style="color:#d6d6d6"><?= htmlspecialchars(conteudo_texto($conteudos, 'footer_horario2', $camposTexto['footer_horario2'])) ?></textarea>
                    </div>

                    <div class="footer-section">
                        <input type="text" class="input-titulo" value="CONTACTOS" style="color:white" disabled>
                        <textarea id="footer_email" name="footer_email" class="textarea-paragrafo" style="color:#d6d6d6"><?= htmlspecialchars(conteudo_texto($conteudos, 'footer_email', $camposTexto['footer_email'])) ?></textarea>
                        <textarea id="footer_telefone" name="footer_telefone" class="textarea-paragrafo" style="color:#d6d6d6"><?= htmlspecialchars(conteudo_texto($conteudos, 'footer_telefone', $camposTexto['footer_telefone'])) ?></textarea>
                    </div>

                </footer>

                </form>
        </main>

    <?php include 'includes/sidebarmobile.php'; ?>
    <?php include 'includes/footer.php'; ?>
