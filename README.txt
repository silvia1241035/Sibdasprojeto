InveMed
=======

Projeto: InveMed - Sistema Web de Apoio ao Inventario Hospitalar de Equipamentos Medicos
Unidade curricular: Sistemas de Informacao e Base de Dados Aplicados a Saude (SIBDAS)
Ano letivo: 2025/2026
Estudante: Silvia Magalhaes
Numero de estudante: 1241035


1. Descricao da aplicacao
-------------------------

O InveMed e uma aplicacao web desenvolvida para simular um sistema de gestao do inventario hospitalar de equipamentos medicos.

A aplicacao esta organizada em duas areas:

- Area publica: website institucional da empresa InveMed, com pagina inicial, informacao sobre a empresa, servicos, contactos e formulario de contacto.
- Area privada: backoffice protegido por autenticacao, onde sao geridos equipamentos, localizacoes, fornecedores, documentacao, garantias/contratos, utilizadores, mensagens, logs e conteudos publicos.

O objetivo principal e centralizar informacao sobre equipamentos medicos, incluindo localizacao, fornecedor, documentacao tecnica, garantia/contrato, estado operacional e criticidade clinica.


2. Tecnologias utilizadas
------------------------

- HTML5
- CSS3
- Bootstrap
- JavaScript
- jQuery
- DataTables
- Chart.js
- Font Awesome
- PHP
- MySQL
- DBML / dbdiagram.io
- Git


3. Estrutura de diretorios
--------------------------

invemed/
|-- assets/
|   |-- bootstrap/
|   |-- css/
|   |-- datatables/
|   |-- fontawesome/
|   |-- img/
|   |-- jQuery/
|   |-- js/
|   `-- fonte_texto/
|-- base de dados/
|   |-- invemed.dbml
|   |-- invemed.sql
|   `-- exemplos.sql
|-- config/
|   `-- config.php
|-- private/
|   |-- documentacao/
|   |-- equipamentos/
|   |-- fornecedores/
|   |-- garantiacontrato/
|   |-- includes/
|   |-- localizacao/
|   |-- utilizadores/
|   |-- index.php
|   |-- logs.php
|   |-- mensagens.php
|   `-- processa_login.php
|-- public/
|   |-- index.php
|   |-- login.php
|   |-- logout.php
|   `-- processa_contacto.php
|-- uploads/
|   |-- conteudos/
|   `-- documentacao/
|-- commits.txt
`-- README.txt


4. Instalacao e execucao
------------------------
Instale o Laragon ou outro ambiente LAMP/WAMP.
Clone ou copie o projeto para a pasta www.
Configure a base de dados em config/config.php.
Certifique-se que as permissões das pastas estão corretas.

2. Iniciar o servidor web local, por exemplo Laragon/Apache.

3. Confirmar a configuracao da base de dados em:

   config/config.php

   A configuracao atual aponta para o servidor usado nas aulas praticas:

   Host: vsgate-s1.dei.isep.ipp.pt
   Porta: 10464
   Base de dados: db1241035
   Utilizador: 1241035

4. Importar a base de dados, se necessario, usando o ficheiro:

   base de dados/invemed.sql

   Este ficheiro cria as tabelas, relacoes, restrições e dados iniciais necessarios ao funcionamento da aplicacao.

5. Aceder a aplicacao no browser:

   Area publica:
   http://127.0.0.1/sibdas/1241035/invemed/public/index.php

   Login / area privada:
   http://127.0.0.1/sibdas/1241035/invemed/public/login.php


5. Credenciais de acesso
------------------------


| Perfil                  | Email                     | Password    |
|-------------------------|---------------------------|-------------|
| Administrador           | admin@invemed.pt          | Wudtywb%70  | 
| Tecnico                 | carlos.mendes@gmail.com   | Cfzjhxr$22  |
| Gestor de Logistica     | marta.oliveira@gmail.com  | Kfytpcd$84  |
| Profissional de saude   | ana.ferreira@gmail.com    | Wqmsciw%72  |

Nota: a password encontra-se guardada na base de dados atraves de hash gerado com password_hash().


6. Perfis e permissoes
----------------------

- Administrador: acesso geral a gestao de utilizadores, conteudos publicos, logs, equipamentos, documentacao, garantias/contratos, fornecedores e localizacoes.
- Tecnico: acesso aos modulos tecnicos de equipamentos, documentacao e garantias/contratos.
- Gestor de Logistica: acesso aos modulos de fornecedores e localizacoes.
- Profissional de saude: acesso de consulta a informacao relevante, como equipamentos, documentacao e localizacoes.

O controlo de acesso e feito em PHP atraves das funcoes redirect_if_not_logged() e require_perfil().


7. Principais funcionalidades
-----------------------------

Area publica:

- Pagina institucional com conteudos dinamicos.
- Carousel de imagens.
- Secoes "Inicio", "Sobre Nos", "Servicos" e "Contacto".
- Formulario de contacto com validacao.
- Gestao dos conteudos publicos atraves da area privada.

Area privada:

- Autenticacao com sessao PHP.
- Dashboard com indicadores e graficos.
- CRUD de equipamentos.
- CRUD de fornecedores.
- CRUD de localizacoes.
- CRUD de documentacao.
- CRUD de garantias e contratos.
- CRUD de utilizadores.
- Consulta de mensagens recebidas pelo formulario publico.
- Registo de logs de eventos.
- Pesquisa, filtros, paginacao e exportacao de tabelas.
- Upload de documentos e imagens.
- Encriptacao de IDs nos URLs com OpenSSL.
- Soft delete em varios modulos.


8. Base de dados
----------------

A base de dados esta definida nos seguintes ficheiros:

- base de dados/invemed.dbml
- base de dados/invemed.sql
- base de dados/exemplos.sql

O modelo foi representado em DBML e em modelo relacional com notacao Crow's Foot.

A tabela central e equipamentos. A partir dela relacionam-se:

- localizacoes: uma localizacao pode ter varios equipamentos.
- acessorios: um equipamento pode ter varios acessorios.
- documentacao: um equipamento pode ter varios documentos.
- garantias_contratos: um equipamento pode ter varias garantias/contratos.
- fornecedores: relacao N:M com equipamentos, atraves da tabela equipamento_fornecedor.

Principais restricoes implementadas na base de dados:

- email unico em utilizadores.
- NIF unico em fornecedores.
- codigo interno unico em equipamentos.
- documento unico por nome e equipamento.
- localizacao unica por edificio e servico.
- ordem unica dos slides.
- chaves estrangeiras entre equipamentos, localizacoes, fornecedores, documentacao, acessorios, garantias/contratos, logs e conteudos publicos.

O modelo encontra-se em 3NF, uma vez que as tabelas representam entidades bem definidas, os atributos dependem das respetivas chaves primarias e as relacoes muitos-para-muitos foram resolvidas atraves de tabelas associativas.


9. Consultas SQL relevantes
---------------------------

Consulta com juncao de tabelas, usada em private/documentacao/listar.php:

SELECT d.*,
       e.designacao AS equipamento_nome,
       f.nome AS fornecedor_nome
FROM documentacao d
JOIN equipamentos e
     ON d.id_equipamento = e.id_equipamento
LEFT JOIN fornecedores f
     ON d.id_fornecedor = f.id_fornecedor
ORDER BY d.tipo, d.nome;

Esta consulta lista os documentos e junta a informacao do equipamento associado e, quando existir, do fornecedor associado.

Consulta com subconsulta, usada em private/equipamentos/listar.php:

SELECT e.*,
       l.servico,
       (
           SELECT f.nome
           FROM fornecedores f
           JOIN equipamento_fornecedor ef
                ON ef.id_fornecedor = f.id_fornecedor
           WHERE ef.id_equipamento = e.id_equipamento
           LIMIT 1
       ) AS fornecedor_nome
FROM equipamentos e
LEFT JOIN localizacoes l
     ON l.id_localizacao = e.id_localizacao
ORDER BY e.codigo_interno;

Esta consulta lista os equipamentos, mostra o servico/localizacao e usa uma subconsulta para obter um fornecedor associado a cada equipamento.


10. Testes principais
---------------------

Para testar a aplicacao:

1. Abrir a area publica e verificar o carousel, os conteudos e o formulario de contacto.
2. Submeter uma mensagem no formulario de contacto.
3. Entrar como Administrador e consultar as mensagens recebidas.
4. Verificar o dashboard da area privada.
5. Inserir um equipamento com localizacao, fornecedor, acessorios, documentacao e garantia.
6. Editar o equipamento e confirmar que o codigo interno e o numero de serie ficam bloqueados.
7. Abater um equipamento e confirmar que deixa de permitir edicao e que a documentacao/garantias ficam inativas.
8. Inserir, editar, desativar e reativar fornecedores.
9. Inserir, editar, desativar e reativar localizacoes.
10. Inserir documentos com ficheiro e testar os filtros da listagem.
11. Inserir garantias/contratos e testar a separacao entre ativos e historico.
12. Testar acessos com diferentes perfis para confirmar as permissoes.
13. Verificar os logs apos operacoes de login, insercao, edicao e eliminacao.
14. Testar exportacao das tabelas em formatos disponibilizados pelo DataTables.


11. Validacoes e regras de negocio
----------------------------------

Foram implementadas validacoes no lado do servidor em PHP, incluindo:

- email valido.
- passwords com hash.
- campos obrigatorios.
- nomes/designacoes nao compostos apenas por numeros.
- datas validas e nao futuras quando aplicavel.
- ano de fabrico dentro de intervalo valido.
- custo de aquisicao positivo.
- fornecedores e localizacoes ativos para novas associacoes.
- documentos com ficheiro obrigatorio.
- tipos de ficheiro permitidos.
- validade obrigatoria para certificados de calibracao e contratos de manutencao.
- data de inicio de garantia anterior ou igual a data de fim.
- numero de serie nao repetido para o mesmo fabricante e modelo.

Algumas regras sao funcionais da aplicacao, ou seja, sao suportadas pela base de dados mas aplicadas pelo PHP. Exemplos:

- ao apagar um fornecedor/localizacao/utilizador/documento/garantia, o registo e desativado em vez de removido fisicamente;
- ao abater um equipamento, o estado passa a "Abatido";
- equipamentos abatidos nao podem ser editados;
- documentacao e garantias/contratos ativos sao desativados quando o equipamento e abatido;
- paginas privadas so podem ser acedidas por perfis autorizados.


12. Ficheiros importantes
-------------------------

- config/config.php: configuracao global, ligacao a base de dados e constantes da aplicacao.
- private/includes/funcoes.php: funcoes reutilizaveis de sessao, permissoes, encriptacao de IDs, conteudos publicos, slides e logs.
- base de dados/invemed.sql: script de criacao e povoamento inicial da base de dados.
- base de dados/invemed.dbml: modelo relacional em DBML.
- commits.txt: historico de commits gerado atraves de Git.
- assets/css/1241035.css: estilos proprios do projeto.
- assets/js/1241035.js: scripts proprios do projeto.


13. Observacoes adicionais
--------------------------

- O projeto usa soft delete para manter historico.
- Os IDs enviados por URL sao encriptados com OpenSSL.
- As passwords nao sao guardadas em texto simples.
- A aplicacao usa DataTables para pesquisa, paginacao e exportacao de dados.
- O dashboard usa consultas a base de dados para apresentar indicadores reais.
- Os documentos carregados sao guardados na pasta uploads/documentacao/.
- As imagens de conteudos publicos sao guardadas na pasta uploads/conteudos/.
