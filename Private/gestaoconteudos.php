<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);?>

<?php include 'includes/header.php'; ?>

<?php include 'includes/nav.php'; ?>
    
<?php include 'includes/sidebar.php'; ?>       
        
            <main class="col-md-4 col-lg-10 p-4">
                <h1><strong>Bem-vindo à Área de Gestão de Conteúdos</strong></h1>
                <h6 class="mb-10">Aqui podes editar a página principal.</h6>

                <nav class="bng-navbar-gc">
                    <div class="logo-wrapper">
                            <img id="logo_principal" src="../assets/img/logo.png" alt="Logo InveMed" style="height: 50px;">
                            <input id="titulo_site" type="text" class="input-h1" value="InveMed">
                            <input type="file" id="logo_input" class="file-editavel">
                        </div>

                    <div class="container-navegacao-gc">
                        <input id="nav_inicio" type="text" class="input-titulo" value="Início" style="color:#0077a8">
                        <input id="nav_quemsomos" type="text" class="input-titulo" value="Sobre Nós" style="color:#0077a8">
                        <input id="nav_servicos" type="text" class="input-titulo" value="Serviços" style="color:#0077a8">
                        <input id="nav_contacto" type="text" class="input-titulo" value="Contacto" style="color:#0077a8">
                    </div>

                    <div class="nav-cliente-gc">
                        <input id="link_area_restrita" type="text" class="input-pass" value="Área Restrita" style="color:#0077a8" >
                    </div>
                </nav>

                <section id="inicio">

                    <div id="carrosselInveMed" class="carousel carousel-dark slide" data-bs-ride="carousel">

                        <div class="carousel-inner">

                            <!-- SLIDE 1 -->
                            <div class="carousel-item active" style="position: relative;">
                                <img src="../assets/img/Slide 1.png" class="d-block w-100">
                                <button class="btn-alterar-imagem" onclick="document.getElementById('slide1').click()">Alterar imagem</button>
                                <input type="file" id="slide1" class="file-editavel">
                            </div>

                            <!-- SLIDE 2 -->
                            <div class="carousel-item" style="position: relative;">
                                <img src="../assets/img/Slide 2.png" class="d-block w-100">
                                <button class="btn-alterar-imagem" onclick="document.getElementById('slide2').click()">Alterar imagem</button>
                                <input type="file" id="slide2" class="file-editavel">
                            </div>

                            <!-- SLIDE 3 -->
                            <div class="carousel-item" style="position: relative;">
                                <img src="../assets/img/Slide 3.png" class="d-block w-100">
                                <button class="btn-alterar-imagem" onclick="document.getElementById('slide3').click()">Alterar imagem</button>
                                <input type="file" id="slide3" class="file-editavel">
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
                        <input id="inicio_titulo" type="text" class="input-titulo" value="Bem-Vindo à InveMed">
                        <textarea id="inicio_texto" class="textarea-paragrafo" style="color:black">A solução de eleição para organizar os seus equipamentos médicos.</textarea>
                    </div>

                </section>

                <section id="quem-somos">
                    <div class="container">
                        <input id="sobre_titulo" type="text" class="input-titulo" value="SOBRE NÓS">

                        <div class="row">

                            <!-- CARD 1 -->
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="pessoa">
                                    <i class="fa-solid fa-user-doctor"></i>
                                    <input id="sobre_card1_titulo" type="text" class="input-titulo" value="Anos de experiência em organização médica" style="color:#0077a8">
                                    <textarea id="sobre_card1_texto" class="textarea-paragrafo">Na InveMed, acreditamos que a eficiência na saúde começa nos bastidores. Nascemos da necessidade de transformar a gestão de inventário num processo simples, inteligente e livre de falhas, permitindo que as equipas médicas se foquem no que realmente importa: salvar vidas.
                                    </textarea>
                                </div>
                            </div>

                            <!-- CARD 2 -->
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="pessoa">
                                    <i class="fa-solid fa-handshake"></i>
                                    <input id="sobre_card2_titulo" type="text" class="input-titulo" value="Os pilares da Empresa" style="color:#0077a8">
                                    <textarea id="sobre_card2_texto1" class="textarea-paragrafo">Tratamos o inventário dos vossos equipamentos com o mesmo nível de precisão e seriedade exigidos num bloco operatório.
                                    </textarea>
                                    <textarea id="sobre_card2_texto2" class="textarea-paragrafo">Trabalhamos com total transparência e responsabilidade para garantir a segurança dos vossos dados e bens.</textarea>
                                    <textarea id="sobre_card2_texto3" class="textarea-paragrafo">Procuramos sempre as melhores metodologias e soluções tecnológicas para simplicar a gestão logística hospitalar.
                                    </textarea>
                                </div>
                            </div>

                            <!-- CARD 3 -->
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="pessoa">
                                    <i class="fa-solid fa-users"></i>

                                    <input 
                                        type="text" 
                                        id="sobre_card3_titulo"
                                        class="input-h2"
                                        value="Para quem trabalhamos"
                                    >
                                    <!-- SUBTÍTULO 1 -->
                                    <input 
                                        type="text" 
                                        id="sobre_card3_sub1"
                                        class="input-h3"
                                        value="Hospitais Públicos e Privados"
                                        style="font-size: 1.6rem; margin-top: 10px;"
                                    >

                                    <!-- TEXTO 1 -->
                                    <textarea 
                                        id="sobre_card3_texto1"
                                        class="textarea-paragrafo"
                                    >Pretendemos fazer a gestão e catalogação de grandes volumes de ativos em múltiplos departamentos.</textarea>

                                    <!-- SUBTÍTULO 2 -->
                                    <input 
                                        type="text" 
                                        id="sobre_card3_sub2"
                                        class="input-h3"
                                        value="Clínicas Médicas e Dentárias"
                                        style="font-size: 1.6rem; margin-top: 20px;"
                                    >

                                    <!-- TEXTO 2 -->
                                    <textarea 
                                        id="sobre_card3_texto2"
                                        class="textarea-paragrafo"
                                    >Tencionamos organizar e controlar os stocks de pequenos equipamentos para garantir a fluidez do dia a dia.</textarea>

                                </div>
                            </div>
                        </div>
                <section id="servicos">
                    <div class="container">
                        <input type="text" class="input-titulo" value="SERVIÇOS">

                        <div class="row">

                            <!-- SERVIÇO 1 -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="servicos">
                                    <i class="fa-solid fa-laptop-medical"></i>
                                    <input id="servico1_titulo" type="text" class="input-titulo" value="Gestão dos equipamentos médicos" style="color:#0077a8">
                                    <textarea id="servico1_texto" class="textarea-paragrafo">Em vez de folhas de Excel dispersas, a InveMed oferece uma plataforma única para acompanhar o ciclo de vida dos dispositivos médicos.
                                    </textarea>
                                </div>
                            </div>

                            <!-- SERVIÇO 2 -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="servicos">
                                    <i class="fa-solid fa-file-shield"></i>
                                    <input id="servico2_titulo" type="text" class="input-titulo" value="Gestão de documentação" style="color:#0077a8">
                                    <textarea id="servico2_texto" class="textarea-paragrafo">Um dos grandes problemas hospitalares é perder manuais ou certificados. Com a InveMed, é garantido que a instituição está sempre pronta para inspeções.
                                    </textarea>
                                </div>
                            </div>

                            <!-- SERVIÇO 3 -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="servicos">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <input id="servico3_titulo" type="text" class="input-titulo" value="Mapeamento e Rastreabilidade Logística" style="color:#0077a8">
                                    <textarea id="servico3_texto" class="textarea-paragrafo"> Com a InveMed, consegue sempre saber onde está o equipamento para evitar perdas e otimizar o tempos das equipas médicas.
                                    </textarea>
                                </div>
                            </div>

                            <!-- SERVIÇO 4 -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="servicos">
                                    <i class="fa-solid fa-shield-heart"></i>
                                    <input type="text" class="input-titulo" value="Consultoria da Criticidade Clínica" style="color:#0077a8">
                                    <textarea class="textarea-paragrafo">
                A InveMed ajuda a classificar os equipamentos médicos de acordo com a sua criticidade e estado.
                                    </textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
                <section id="contacto-gc">
                    <input id="contacto_titulo" type="text" class="input-titulo" value="CONTACTO" style="font-size: 2rem;">
                    <textarea id="contacto_texto" class="textarea-paragrafo">Entre em contacto connosco para organizarmos a sua unidade de saúde.</textarea>

                    <form id="contactForm-gc">
                        <input type="text" class="input-titulo" value="Nome:">
                        <div class="caixa"></div>
                        <input type="text" class="input-titulo" value="Email:">
                        <div class="caixa"></div>
                        <input type="text" class="input-titulo" value="Mensagem:">
                        <div class="caixa"></div>
                        <input type="text" class="input-mensagem" value="Enviar mensagem" style="color:white; background-color:#0077a8; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; width: 70vw; text-align: center;">
                    </form>
                </section>
                <footer class="footer-container">

                    <div class="footer-section">
                        <input type="text" class="input-titulo" value="LOCALIZAÇÃO" style="color:white">
                        <textarea id="footer_localização" class="textarea-paragrafo" style="color:#d6d6d6">Rua xxxxxxxxxxx, Porto, Portugal</textarea>
                    </div>

                    <div class="footer-section">
                        <input type="text" class="input-titulo" value="HORÁRIO" style="color:white">
                        <textarea id="footer_horario1" class="textarea-paragrafo" style="color:#d6d6d6">2ª a Sábado: 8h-19h</textarea>
                        <textarea id="footer_horario2" class="textarea-paragrafo" style="color:#d6d6d6">Domingos e Feriados: Encerrado</textarea>
                    </div>

                    <div class="footer-section">
                        <input type="text" class="input-titulo" value="CONTACTOS" style="color:white">
                        <textarea id="footer_email" class="textarea-paragrafo" style="color:#d6d6d6">Email: geral@invemed.pt</textarea>
                        <textarea id="footer_telefone" class="textarea-paragrafo" style="color:#d6d6d6">Telefone: +351 9xx xxx xxx</textarea>
                    </div>

                </footer>   
        </main>
     
    <?php include 'includes/sidebarmobile.php'; ?>
    <?php include 'includes/footer.php'; ?>
