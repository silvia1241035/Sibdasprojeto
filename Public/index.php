<?php include '../Private/includes/header.php'; ?>
<body>
        <nav class="bng-navbar">
            <div>
                <img id="logo_principal" src="../assets/img/logo.png" alt="Logo InveMed">
                <h1 id="titulo_site"><strong>InveMed</strong></h1>
            </div>
            <div class="container-navegacao">
                <a id="nav_inicio" href="#inicio">Início</a>
                <a id="nav_quemsomos" href="#quem-somos">Sobre Nós</a>
                <a id="nav_servicos" href="#servicos">Serviços</a>
                <a id="nav_contacto" href="#contacto">Contacto</a>
            </div>

            <div class="nav-cliente">
                <a id="link_area_restrita" href="login.php" target="_blank">Área Restrita</a>

            </div>
        </nav>
        
        <section id="inicio">
            <div id="carrosselInveMed" class="carousel carousel-dark slide" data-bs-ride="carousel">

                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carrosselInveMed" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carrosselInveMed" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carrosselInveMed" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>

                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img id="slide1" src="../assets/img/Slide 1.png" class="d-block w-100" alt="Primeiro slide">
                    </div>

                    <div class="carousel-item">
                        <img id="slide2" src="../assets/img/Slide 2.png" class="d-block w-100" alt="Segundo slide">
                    </div>

                    <div class="carousel-item">
                        <img id="slide3" src="../assets/img/Slide 3.png" class="d-block w-100" alt="Terceiro slide">
                    </div>

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
                <h2 id="inicio_titulo"><strong> Bem-Vindo à InveMed</strong></h2>
                <p id="inicio_texto"> A solução de eleição para organizar os seus equipamentos médicos.</p>                    
            </div> 
        </section>      
               
        <section id="quem-somos">
            <div class="container">
                <h1 id="sobre_titulo"><strong> SOBRE NÓS</strong></h1>
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="pessoa">
                                <i class="fa-solid fa-user-doctor"></i>
                                <h2 id="sobre_card1_titulo"><strong>Anos de experiência em organização médica</strong></h2>
                                <p id="sobre_card1_texto"> Na InveMed, acreditamos que a eficiência na saúde começa nos bastidores. Nascemos da necessidade de transformar a gestão de inventário num processo simples, inteligente e livre de falhas, permitindo que as equipas médicas se foquem no que realmente importa: salvar vidas.</p>
                            </div>        
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">  
                            <div class="border-start border-secundary border-0.5 ps-3">          
                                    <div class="pessoa">
                                        <i class="fa-solid fa-handshake"></i>
                                        <h2 id="sobre_card2_titulo"><strong>Os pilares da Empresa</strong></h2>
                                        <p id="sobre_card2_texto1">Tratamos o inventário dos vossos equipamentos com o mesmo nível de precisão e seriedade exigidos num bloco operatório.</p>
                                        <p id="sobre_card2_texto2">Trabalhamos com total transparência e responsabilidade para garantir a segurança dos vossos dados e bens.</p>
                                        <p id="sobre_card2_texto3">Procuramos sempre as melhores metodologias e soluções tecnológicas para simplicar a gestão logística hospitalar.</p>
                                    </div>
                                
                            </div>
                        </div>         
                        <div class="col-lg-4 col-md-6 col-12"> 
                            <div class="border-start border-secondary border-0.5 ps-3">     
                                    <div class="pessoa">
                                       <i class="fa-solid fa-users"></i>
                                        <h2 id="sobre_card3_titulo"><strong>Para quem trabalhamos</strong></h2>
                                        <h3 id="sobre_card3_sub1"><strong>Hospitais Públicos e Privados</strong></h3>
                                        <p id="sobre_card3_texto1">Pretendemos fazer a gestão e catalogação de grandes volumes de ativos em múltiplos departamentos.</p>
                                        <h3 id="sobre_card3_sub2"><strong>Clínicas Médicas e Dentárias</strong></h3>
                                        <p id="sobre_card3_texto2">Tencionamos organizar e controlar os stocks de pequenos equipamentos para garantir a fluidez do dia a dia.</p>
                                    </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>                        
        </section>

        <section id="servicos">
            <div class="container">
                <h1 id="servicos_titulo"><strong>SERVIÇOS</strong></h1>
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="servicos">
                            <i class="fa-solid fa-laptop-medical"></i>
                            <h2 id="servico1_titulo"><strong>Gestão dos equipamentos médicos</strong></h2>                               
                            <p id="servico1_texto">Em vez de folhas de Excel dispersas, a InveMed oferece uma plataforma única para acompanhar o ciclo de vida dos dispositivos médicos.</p>
                        </div>
                    </div>        
                    <div class="col-lg-3 col-md-6 col-12"> 
                            <div class="border-start border-secundary border-0.5 ps-3">
                                <div class="servicos">
                                <i class="fa-solid fa-file-shield"></i>
                                <h2 id="servico2_titulo"><strong>Gestão de documentação</strong></h2>
                                <p id="servico2_texto">Um dos grandes problemas hospitalares é perder manuais ou certificados. Com a InveMed, é garantido que a instituição está sempre pronta para inspeções.</p>
                                </div>
                            </div>
                        
                    </div>        
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="border-start border-secundary border-0.5 ps-3"> 
                            <div class="servicos">  
                                <i class="fa-solid fa-location-dot"></i>
                                <h2 id="servico3_titulo"><strong>Mapeamento e Rastreabilidade Logística</strong></h2>
                                <p id="servico3_texto"> Com a InveMed, consegue sempre saber onde está o equipamento para evitar perdas e otimizar o tempos das equipas médicas.</p>
                            </div>
                           
                        </div>
                    </div>            
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="border-start border-secundary border-0.5 ps-3">   
                            <div class="servicos"> 
                                <i class="fa-solid fa-shield-heart"></i>
                                <h2 id="servico4_titulo"><strong>Consultoria da Criticidade Clínica</strong></h2>
                                <p id="servico4_texto">A InveMed ajuda a classificar os equipamentos médicos de acordo com a sua criticidade e estado. </p>
                            </div>    
                            
                        </div>     
                    </div>
                </div>
            </div>        
        </section>

        <section id="contacto">
            <h1 id="contacto_titulo"><strong>CONTACTO</strong></h1>
            <p id="contacto_texto">Entre em contacto connosco para organizarmos a sua unidade de saúde.</p>
            <form id="contactForm">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="mensagem">Mensagem:</label>
                <textarea id="mensagem" name="mensagem" rows="4" required></textarea> <button type="submit">Enviar mensagem</button>
            </form>
        
        </section>

        <footer class="footer-container">
            <br>
            <div class="footer-section">
                <strong>LOCALIZAÇÃO</strong>
                <p id="footer_localização">Rua xxxxxxxxxxx <br> 4xxx-xxx, Porto <br> Portugal</p>               
            </div>
            <div class="footer-section">
                <strong>HORÁRIO</strong>
                <p id="footer_horario1">2ª a Sábado: 8h-19h</p>
                <p id="footer_horario2">Domingos e Feriados: Encerrado</p>                
            </div>
            <div class="footer-section">
                <strong>CONTACTOS</strong>
                <p id="footer_email">Email:geral@invemed.pt</p>                    
                <p id="footer_telefone">Telefone: +351 9xx xxx xxx</p>
            </div>
        </footer>
    
   <?php include '../Private/includes/footer.php'; ?> 