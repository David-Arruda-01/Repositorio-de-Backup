#Projeto de construção de um app de gestão de um Restaurante.

Foi solicitado a construção de um MER - modelo de entidade relacionamento para construção das tabelas do banoco de dados

CONFIGURAÇÃO INICIAIS:

Após as intalações dos ide (VS-Code), foi solicitada a instalação do Laragon - para inicialização de um cliente/servidor e o composer para gerenciamento das dependencias do php no projeto de forma mais automatica. https://laragon.org/download/ https://code.visualstudio.com/download https://getcomposer.org instale tambem o Git para poder gerenciar as alterações em seu projeto. https://git-scm.com/downloads

criação de uma pasta com o nome do projeto no diretorio raiz do laragon, na pasta www. com isso apos o laragon ser inicializado poderemos ter acesso a pasta pelo navegador usando o endereço www.'NOME_DA_Pasta'.test/

Abra a pasta no ide(VSCode)

Crie a pasta: *app/ - Seu código versionado no seu git. *framework/ - aqui serão colocados as rotas e os demais arquivos privados do projeto. *public/ - pasta onde ficaram os arquivos de acesso público do usuário e arquivos de terceiros.

No terminal do VsCode use o comando 'git init' para iniciar o monitoramento de seu arquivos. Ao ativar, será criado um pasta git dentro da pasta rais do seu app. nesta pasta crie o arquivo ".gitignore" e adicione as pasta que o git deverá iginorar quando vc for subir ela para o seu repositorio na web.

framework/ vendor/

Crie um arquivo chamado composer.json na raiz da sua aplicação. { "autoload": { "psr-4": { "App\":"app/", "Fmk\":"framework/" } }
} Digite o comando para gerar os autoloads da aplicação: "composer dump-autoload" com isso o composer controi a pasta vendor/ na sua aplicação.

Crie um arquivo chamado “application.php” dentro da pasta app;

Dentro do arquivo adicione requeira para a arquivo autoload do composer: require_once '../vendor/autoload.php';

Dentro da pasta framework adicione uma classe chamada Initialize;

Crie um método estático chamado run;

Dentro do arquivo “app/application.php” execute o método run da classe Initialize;

Na pasta public crie um arquivo “index.php”; Dentro desse arquivo requeira o arquivo “../app/application.php”;

Reinicie seu Laragon. Em um navegador digite o nome do diretório do seu projeto seguido de .test EX: restaurante.test e se a página aparecer em branco o autoload está funcionando.

CRIAÇÃO DE VIEWS E ROTAS:

Baixe um template de dastbord. Sugestão Usada: https://www.creative-tim.com/product/argon-dashboard-bs4

Coloque os arquivos descompactados na pasta public.

Dentro do vscode na pasta "app", crie uma pasta chamada "views" Na pasta modelo/pages procure o arquivo html de login e copie uma versão para pasta app/views.

Renomei esse arquivo para "login.views.php"

Crie uma pasta "MVC" na pasta "framework" Crie um arquivo "View.php" na pasta "MVC" Arquivo responsavel por cliar a classe e da render.

Apos esse arquivo agora pode imprimir na index na pasta public.

CONSTANTES Serve para guardar o caminho de uma pasta a partir de uma raiz

Crie uma pasta "Configs" na pasta "framework" e dentro um arquivo "constante.php"

vá para o arquivo "Initialize" e construa a função "loadConstante" e coloque ela para ativar a função run, tudo na class "initialize".

Agora no arquivo "constantes.php" vc pode instaciar as contantes. Começe indicando a pasta raiz com a constate APPLIACATION_PATH e depois as demais pasta a partir dela. Neste arquivo vc tambem pode criar constante que sintetise um pardão predefinido de instenssões. ex: samuel.tabela.php vc pode aplica uma constante e irar armazenar o ".tabela.php"

Agora instacie esses endereços em funções, para isso crie uma pasta "Helpers" na pasta "framework" e dentro crie um arquivo "view.php". lá você irar criar as funções.

apos criar não esquecer de criar uma função "loadHelpers" apra inicializar no arquivo "Initialise"

agora vc pode criar dentor do "Helpers/view.php" uma função que irar criar a sua view, onde ela irar criar e acionar o render apenas indicando o nome da view.

*separação da view e do template.

criar pasta "template" na pasta "app".

criar arquivo "blank.template.php" na pasta "template". Separe o template removendo todas informações html não relacionalda e no local escreva "{{VIEW}}". Indicando que naquele espaço será inceridas as views que forem solicitadas.

DROP DATABASE IF EXISTS restaurante; CREATE DATABASE IF NOT EXISTS restaurante; USE restaurante;
