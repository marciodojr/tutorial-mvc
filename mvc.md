# 1. Um Início

## 1.1. Introdução

Olá! `o/`, fizemos este documento para explicar para você como é desenvolver uma aplicação MVC seguindo os melhores padrões da comunidade PHP, caso você já conheça, agora é um bom momento para revisar um pouquinho. 

Sabemos que costuma ser um pouco difícil entrar em uma nova equipe e utilizar ferramentas e tecnologias que nunca vimos. Por isso, antes de "botar as mãos na massa" queremos dar a você a oportunidade de praticar os conceitos de orientação a objetos e construção de software em camadas a partir daquilo que você já aprendeu na graduação.

## 1.2. Definindo nosso escopo

Para evitar qualquer ambiguidade no desenvolvimento, vamos combinar que quando falamos de uma aplicação MVC com PHP, estamos nos referindo a uma aplicação *client/server* (possivelmente utilizada por vários clientes e distribuida em vários servidores), onde o cliente tem acesso à aplicação via browser, aplicativo, aplicação instalada no computador, linha de comando ou via sdk<sup id="backSdk">[1](#sdk)</sup>.


## 1.3. Aplicação de arquivo único

Quando nossa aplicação é muito simples, um único arquivo basta, se a aplicação não é tão simples, um único arquivo basta para ter muita dor de cabeça. Quando criamos uma aplicação que comunica com banco de dados, realiza operações nos dados e exibe informações para um cliente, tudo em um único arquivo, ficamos com um arquivo bem difícil de entender, grande, pouco reusável e bastante bagunçado. Nosso arquivo poderia ter, por exemplo, a estrutura do ([Exemplo 1.1](#ex1)).

<sup id="ex1"></sup>
```html
<?php
function connect() {/* ... */}
function getSomethingFromDatabase($conn) {/* ... */}
function filterSomethingFromDatabase($something) {/* ... */}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <!-- tags, tags, tags ... -->
    <!-- uso de filterSomethingFromDatabase   -->
    <!-- tags, tags, tags ... -->
    <!-- js scripts -->
</body>
</html>

```
<center><sup>Exemplo 1.1. Possível estrutura de uma aplicação de arquivo único</sup></center>

O grande problema em construir um único arquivo está na dificuldade em dar manutenção e na baixa reusabilidade. A medida que o tempo passa (e novas modificações são feitas), vai ficando cada vez mais difícil entender o que o código faz, onde é necessário mudar e se, ao mudar, todo o resto continuará funcionando.

## 1.4. Variações do arquivo único

Se a aplicação iniciou com um único arquivo, e começou a crescer, é bem natural que surja a ideia de reutilizar o cabeçalho, o rodapé, as funções de conexão com o banco de dados e coisas similares. Nesse caso, a aplicação poderia ser organizada como no ([Exemplo 1.2](#ex2)).

<sup id="ex2"></sup>
```html
<?php 
    require_once 'funcoes.php'; 
    require_once 'header.php';
?>

<!-- tags, tags, tags ... -->
<!-- uso de filterSomethingFromDatabase   -->
<!-- tags, tags, tags ... -->
<!-- js scripts -->

<?php require_once 'footer.php'; ?>
```
<center><sup>Exemplo 1.2. Reorganização para reaproveitamento do cabeçalho, rodapé e funções</sup></center>

Essa organização ajuda bastante, no entanto, é provável que se a aplicação agora possui mais de um arquivo de exibição de informações, ela também possui um certo conjunto de funções em `funcoes.php` que é maior do que antes. O grande problema em utilizar funções para tudo, está na dificuldade em saber suas dependências. Ao abrir um arquivo cheio de funções é necessário passar em cada função para saber se alguma delas dependem de outras (são usadas por outras). Neste ponto, para evitar esses problemas costuma-se utilizar os conceitos de orientação a objetos<sup id="backOOD">[2](#ood)</sup> em conjunto com o conceito de MVC<sup id="backMvc">[3](#mvc)</sup> e suas variações.


---
<center>Notas de Rodapé</center>

<b id="sdk">1</b> *Software Development Kit*. [[saber mais](https://en.wikipedia.org/wiki/Software_development_kit)]

<b id="ood">2</b> *S.O.L.I.D*. [[saber mais](http://butunclebob.com/ArticleS.UncleBob.PrinciplesOfOod)]

<b id="mvc">3</b> *Model-View-Controller*. [[saber mais](https://en.wikipedia.org/wiki/Model_view_controller)]

---

# 2. Conceitos iniciais

## 2.1. Observações

Construir uma aplicação com uma arquitetura de camadas facilita o entendimento de como cada parte da aplicação comunica com as demais, reduz a complexidade dos testes e também facilita a entrada de novos membros na equipe. Uma equipe que segue padrões claros e bem conhecidos no mercado deve conseguir, com menor esforço, realizar treinamentos para novos integrantes e utilizar bibliotecas de terceiros com maior facilidade. Vale a pena mencionar que, em relação ao php, desde 2009 existe um grupo, PHP-FIG<sup>[4](#phpfig)</sup>, responsável por definir os padrões de desenvolvimento do php, não relacionado diretamente com a construção da linguagem, mas sim, sua utilização de modo adequado para construção de bibliotecas e aplicações.

## 2.2. Aviso importante

Construiremos uma aplicação Mvc bastante simples seguindo os padrões estabelecidos pela comunidade PHP, no entanto, como o objetivo é manter a didática, não construiremos um framework comercial, mas forneceremos a base para o entendimento de como eles funcionam (pelo menos alguns de seus aspectos mais importantes).

## 2.3. Requisitos

O requisitos são:

* php (versão 7 ou a mais nova que conseguir)
* Um bom editor de código (vscode é uma dica)
* composer <sup id="backComposer">[5](#composer)</sup>

## 2.4. Estrutura de pastas

Em nosso projeto utilizaremos a seguinte estrutura da ([Figura 2.1](#fig2dot1)). Alguns dos arquivos serão gerados automaticamente via composer:


<sup id="fig2dot1"></sup>
```sh
app/
    config/
        config.local.php
        config.php
    src/
        Model/
        Controller/ 
        View/
        routes.php
    public/
        img/
        js/
        css/
        index.php # entry point
    composer.json
    composer.lock
    vendor/
```
<center><sup>Figura 2.1. Estrutura base do projeto</sup></center>

Os elementos da estrutura de pasta serão detalhados a medida que a aplicação estiver sendo construída.


## 2.5. *Entry point*

Uma aplicação feita em **C** ou **C++** contém um ponto de entrada chamado `main`, em **Java** o ponto de entrada é um método também chamado `main`.  O arquivo **index.php** em nossa estrutura, atuará como um *entry point*<sup>[6](#entrypoint)</sup> da aplicação, toda requisição será direcionada a ele, de forma que atue como um *Front Controller*<sup>[7](#fcontroller)</sup>.


## 2.6. Gestão de dependências

Cada linguagem de programação costumar ter seu proprio gerenciador de dependências, Um gerenciador de dependências facilita a adição de bibliotecas de terceiros a nossa aplicação, permitindo definir versões e realizar atualizações automaticamente. Ao desenvolver uma aplicação em geral não estamos interessados em criar todo o código que utilizaremos, é bastante comum utilizar bibliotecas livres e bastante estáveis, isso acelera o desenvolvimento e reduz a quantidade de testes necessários. Para o **php** utilizaremos o **Composer**<sup>[8](#wcomposer)</sup>. Os arquivos **composer.json**, **composer.lock** e a pasta **vendor** serão utilizados pelo Composer.

## 2.7. Configurações

A pasta **config** contém arquivos de configuração geral (que não muda entre ambientes) e local (que mudam dependendo do ambiente). O arquivo **config.local.php** costuma ser utilizado para adição de senhas e usuários para manipulação de banco de dados e flags para habilitar/desabilitar logs. Esse arquivo, em conjunto com a pasta **vendor** devem ser ignorados em VCS's<sup>[9](#vcs)</sup>.


## 2.8. Rodando a aplicação

Para rodar a aplicação utilizaremos o servidor web embutido do php<sup>[10](#phpcli)</sup>. Crie a estrutura da ([Figura 2.2](#fig2dot2)) e no arquivo **index.php** coloque o código do ([Exemplo 2.1](#ex2dot1)). Com o terminal dentro da pasta **app** rode o comando `php -S localhost:4200 -t public` e acesse no navegador pela url [localhost:4200](http://localhost:4200).

<sup id="fig2dot2"></sup>
```sh
app/
    public/
        index.php # entry point
```
<center><sup>Figura 2.2. Primeira estrutura</sup></center>

<sup id="ex2dot1"></sup>
```php
<?php

echo $_SERVER['REQUEST_URI'];


```
<center><sup>Exemplo 2.1. Conteúdo do entry point</sup></center>


Tudo que for digitado após localhost:4200 (por exemplo, **localhost:4200/testando/aaa**) no navegador será exibido na página (por exemplo, **/testando/aaa**). Isso permitirá, futuramente, que a aplicação trabalhe a requisição feita e retorne o recurso correto.

## 2.9. Codificação

Antes de iniciar a construção dos códigos é recomendada a leitura das [PSR-1](https://www.php-fig.org/psr/psr-1/) e [PSR-2](https://www.php-fig.org/psr/psr-2/).

---
<center> Notas de Rodapé </center>

<b id="phpfig">4</b> *PHP Framework Interop Froup*. [[saber mais](https://www.php-fig.org/)]

<b id="composer">5</b> Como baixar o composer. [[saber mais](https://getcomposer.org/download/)]

<b id="entrypoint">6</b> O que é um *Entry point*? [[saber mais](https://en.wikipedia.org/wiki/Entry_point)]

<b id="fcontroller">7</b> O que é o padrão *Front Controller*? [[saber mais](https://en.wikipedia.org/wiki/Front_controller)]

<b id="wcomposer">8</b> O que é o Composer? [[saber mais](https://getcomposer.org/doc/)]

<b id="vcs">9</b> O que é um Sistema de Controle de Versão?. [[saber mais](https://en.wikipedia.org/wiki/Version_control)]

<b id="phpcli">10</b> *PHP Built-in Server*. [[saber mais](https://secure.php.net/manual/pt_BR/features.commandline.webserver.php)]

---

# 3. Classes, objetos e carregamento

## 3.1. Uma história de como incluir classes

Era bastante comum (e, infelizmente, ainda é) utilizar diretamente os construtores de linguagem `require`, `require_once`,  `include` e `include_once`. Apesar de ser possível incluir arquivos de classes dessa forma, o php possui formas mais adequadas de incluir as dependências de classes. Em 2004 (versão 5.0), foi introduzida a função mágica<sup>[11](#phpmagic)</sup> `__autoload`<sup>[12](#fautoload)</sup>, esta função, quando declarada, é chamada automaticamente sempre que uma classe ainda não incluída é usada. Em 2005 (versão 5.1), foi introduzida uma família de funções `spl_autoload*`<sup>[13](#fsplautoload)</sup> para substutir a função `__autoload`, permitindo o registro de múltiplas funções de inclusão de classes e, consequentemente, facilitando a utilização de bibliotecas de terceiros (a partir do uso de funções de inclusão de classe de terceiros). 
Para evitar conflitos de classes com mesmo nome, em 2009 (versão 5.3), o php adicionou o recurso de namespace<sup>[14](#namespaces)</sup>. Com o surgimento do Composer em 2012 e a formalização das propostas de *autoload*, padronizou-se o modelo de carregamentos de classes conforme PSR-4<sup>[15](#psr4)</sup>.

A ([Figura 3.1](#fig3dot1)) apresenta a maneira padrão de definição de dependências da classe. Note que a classe  `Product` possui um namespace declarado no início do arquivo (`TutorialMvc\Model`) e a classe `ProductController` também (`TutorialMvc\Controller`), esta ainda define como será o uso de `Product` por meio do operador `use`<sup>[16](#useop)</sup>.


<sup id="fig3dot1"></sup>
```php
<?php
// src/Model/Product.php

namespace TutorialMvc\Model;

class Product
{
//..
```
```php

<?php
// src/Model/ProductController.php

namespace TutorialMvc\Controller;

use TutorialMvc\Model\Product;

class ProductController
{

    private $product;

    public function __construct(Product $p)
    {
        $this->product = $p;
    }
    // ...
```
<center><sup>Figura 3.1. Uso correto de namespaces</sup></center>

## 3.2. Utilizando o PSR-4 via Composer

Utilizaremos o *autoload* fornecido pelo Composer, assim, não precisaremos definir nossas próprias funções com `spl_autoload_register` (o Composer fará isso por nós). Antes de iniciar o exemplo, é fortemente aconselhado que seja feita a leitura da [PSR-4](https://www.php-fig.org/psr/psr-4/). O ([Exemplo 3.1](#ex3dot1)) indica como inicializar o composer e o ([Exemplo 3.2](#ex3dot2)) mostra como indicar que o projeto utiliza o PSR-4.

<sup id="ex3dot1"></sup>
```sh
~/vhosts/app$ composer init
Welcome to the Composer config generator
This command will guide you through creating your composer.json config.
Package name (<vendor>/<name>) [marciodojr/app]: mdojr/tutorial-mvc
Description []: Criando uma aplicação mvc com php
Author [Márcio Dias <marciojr91@gmail.com>, n to skip]:
Minimum Stability []:
Package Type (e.g. library, project, metapackage, composer-plugin) []: project
License []: MIT

Define your dependencies.

Would you like to define your dependencies (require) interactively [yes]? no
Would you like to define your dev dependencies (require-dev) interactively [yes]? no
{
    "name": "mdojr/tutorial-mvc",
    "description": "Criando uma aplicação mvc com php",
    "type": "project",
    "license": "MIT",
    "authors": [
        {
            "name": "Márcio Dias",
            "email": "marciojr91@gmail.com"
        }
    ],
    "require": {}
}

Do you confirm generation [yes]?
~/vhosts/app$
```

<center><sup>Exemplo 3.1. Inicializando o projeto com o composer</sup></center>

<sup id="ex3dot2"></sup>
 ```json
{
    // ...
    "require": {},
    "autoload": {
        "psr-4": {
            "TutorialMvc\\": "src"
        }
    }
}
 ```

 ```sh
 ~/vhosts/app$ composer update
 ```

```php
<?php
// app/public/index.php

require '../vendor/autoload.php'; // autoloader do Composer
// ...

```

<center><sup>Exemplo 3.2. Adições realizadas no arquivo composer.json</sup></center>

Note que após executar este procedimento, será criado o arquivo **composer.json** e a pasta **vendor** ([Figura 3.2](#fig3dot2)), é dentro da pasta vendor que as dependências e o *autoloader* ficam. A partir deste ponto, todas as bibliotecas de terceiros e, as classes criadas por nós com namespace iniciado com  `TutorialMvc\`, quando usadas, serão incluídas corretamente.


<sup id="fig3dot2"></sup>
```sh
app/
    public/
        index.php # entry point
    composer.json # criado após executar o composer init
    vendor/
```
<center><sup>Figura 3.2. Estrutura após executar o comando composer init e composer update</sup></center>


---
<center>Notas de Rodapé</center>

<b id="phpmagic">11</b> O que é função mágica? [[saber mais](http://php.net/manual/pt_BR/language.oop5.magic.php)]

<b id="fautoload">12</b> O que é a função `__autoload`? [[saber mais](http://php.net/manual/pt_BR/function.autoload.php)]

<b id="fsplautoload">13</b> O que são as funções `spl_autoload*`? [[saber mais](http://php.net/manual/pt_BR/function.spl-autoload.php)]

<b id="namespaces">14</b> O que é namespace? [[saber mais](http://php.net/manual/pt_BR/language.namespaces.rationale.php)]

<b id="psr4">15</b> *PSR-4: Autoloader* [[saber mais](https://www.php-fig.org/psr/psr-4/)]

<b id="useop">16</b> O operador `use` [[saber mais](http://php.net/manual/pt_BR/language.namespaces.importing.php)]

---

# 4. Roteamento

## 4.1. introdução

Como toda requisição da nossa aplicação passará pelo *entry point*, é necessário definir, a partir dele, quem será responsável por receber as requisições, direcionar para os *controllers* e retornar as respostas. O elemento responsável por realizar essa tarefa é chamado de  ***Router***  e as urls registradas nele, **rotas**. Algumas das vantagens de se utilizar um *Router* são: o uso de urls amigáveis (de fácil memorização e entendimento), ocultamento da estrutura de pastas e arquivos, facilidade em modificar as rotas (em comparação com mudança nos nomes de arquivos e diretórios). A ([Figura 4.1](#fig4dot1)) mostra uma aplicação que não utiliza um *Router* e a ([Figura 4.2](#fig4dot2)) uma aplicação que utiliza.

<sup id="fig4dot1"></sup>
```
Client:                  
GET /produto.php?p=1
--------------------->
                      
            Server:
            file mapping: produto.php
            ---------------------------------->
                                    
                                             PHP/File system:
                              find/run produto.php $_GET['p']
                        <------------------------------------
                                    
                                                       Server:
                                 return response: produto.php
<------------------------------------------------------------
```
<center><sup>Figura 4.1. Requisição em uma aplicação sem entry point</sup></center>

<sup id="fig4dot2"></sup>
```
Client:                  
GET /produto/1
--------------------->
                      
            Server:
            file mapping: index.php (always)
            ---------------------------------->
                                    
                                                    PHP/Router:
                        parse request: $_SERVER, $_REQUEST, ...
                                match route and call controller
                                                 build response
                        <--------------------------------------
                                    
                                                        Server:
                                                return response
<--------------------------------------------------------------
```
<center><sup>Figura 4.2. Requisição em uma aplicação com entry point</sup></center>

## 4.2. Exemplos de rotas

É bastante comum, ao utilizar um *Router*, definir rotas pela escolha de um padrão (url ou família de urls), um método http de requisição<sup>[17](#httpm)</sup> e uma ação. Por exemplo:

* GET /customers
    * Possível ação: retornar todos os clientes cadastrados
* GET /customers?page=1&length=10
    * Possível ação: retornar os dez primeiros clientes cadastrados 
* PUT /customers/:id
    * Possível ação: atualizar os dados do cliente id
* PATCH /customers/:id
    * Possível ação: atualizar parcialmente os dados do cliente id
* POST /customers/1/pagamentos
    * Possível ação: cadastrar um pagamento do cliente 1

<sup>Observação: o termo ":id" representa um  valor variável, possívelmente um valor númerico. ":id" é chamado de parâmetro de url.</sup>

A definição da url, a escolha do método http e a ação a ser executada é definida pelo desenvolvedor, mas aconselha-se defini-los em conformidade com o padrão REST<sup>[18](#restm)</sup>.


## 4.3. O Router é um Model, um Controller ou uma View?

O *router* não é parte do MVC e, portanto, não deve estar em nossa aplicação. Ele deve ser tratado com uma dependência externa, só é recomendada a construção de um, se sua aplicação possuir uma necessidade muito específica ou para fins didádicos (fora do escopo desse tutorial). Antes de realizar a instalação de um aconselha-se fortemente a leitura da [PSR-7](https://www.php-fig.org/psr/psr-7/).

## 4.4. Instalando o Router

Utilizaremos o composer para instalar um router. O ([Exemplo 4.1](#ex4dot1)) apresenta o processo de instalação.

<sup id="ex4dot1"></sup>
```sh
~/vhosts/app$ composer require slim/slim
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 6 installs, 0 updates, 0 removals
  - Installing psr/container (1.0.0): Loading from cache
  - Installing container-interop/container-interop (1.2.0): Loading from cache
  - Installing nikic/fast-route (v1.3.0): Loading from cache
  - Installing psr/http-message (1.0.1): Loading from cache
  - Installing pimple/pimple (v3.2.3): Loading from cache
  - Installing slim/slim (3.10.0): Downloading (100%)
Writing lock file
Generating autoload files
~/vhosts/app$
```
<center><sup>Exemplo 4.1. Instalação do Slim</sup></center>

## 4.5. Usando

Vamos criar uma rota simples para demonstrar o funcionamento do router. Primeiro, crie um arquivo chamado **routes.php** com a declaração de uma rota em **app/src/** depois, inclua-o no arquivo index.php. Por fim, execute o comando para rodar o servidor embutido do php e digite a url no navegador [localhost:4200/produtos](http://localhost:4200/produtos). O ([Exemplo 4.2](#ex4dot2)) apresenta o conteúdo adicionado em cada arquivo.

<sup id="ex4dot2"></sup>
```sh
# estrutura de pastas
app/
    public/
        index.php
    src/
        routes.php # crie este arquivo
    composer.json
    composer.lock
```
```php
<?php
// app/public/index.php

require '../vendor/autoload.php';
$router = new Slim\App();
require '../src/routes.php';

$router->run();
```
```php
<?php
// app/src/routes.php

// GET /produtos: retorna uma lista de produtos no formato json
$router->get('/produtos', function ($request, $response) {    
    
    $data = [
        ['id' =>1, 'name' => 'batom'],
        ['id' =>2, 'name' => 'perfume'],
        ['id' =>3, 'name' => 'bolacha'],
        ['id' =>4, 'name' => 'Tomate'],
        ['id' =>5, 'name' => 'Felicidade'],
        ['id' =>6, 'name' => 'Conhecimento']
    ];

    return $response->withJson($data);
});
```
<center><sup>Exemplo 4.2. Criando a primeira rota</sup></center>

Observação: Note que, como estamos utilizando o *autoload* do Composer, ao instalar o *slim* não foi necessário registrá-lo e nem incluí-lo via `require` ou similar.

---
<center>Notas de Rodapé</center>

<b id="httpm">17</b> *HTTP Request Methods*. [[saber mais](https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Methods)]

<b id="restm">18</b> *REST Methods*. [[saber mais](http://www.restapitutorial.com/lessons/httpmethods.html)]


---

# 5. Controllers e Containers

## 5.1. Introdução

Finalmente estamos prontos para criar a primeira camada da aplicação MV**C**, mas antes vamos ajustar algumas definições. Ao estudarmos a arquitetura MVC, aprendemos que os *controllers* formam a "camada do meio", passando parâmetros das *views* para os *models* e retornando informações dos *models* para as *views*. Em aplicações web isso é traduzido como: a função do *controller* é retornar respostas (preferivelmente uma resposta HTTP) baseado em requisições (preferivelmente uma requisição HTTP).


## 5.2. Criando um controller

No nosso caso, quando definimos a ação da rota `/produtos`, nós já realizamos o trabalho  de um *controller* (ou parte do trabalho). Para organizar corretamente a aplicação, vamos criar o controller `TutorialMvc\Controller\ProductController` em **app/src/Controller/ProductController.php**. O ([Exemplo 5.1](#ex5dot1)) apresenta o código e a estrutura de pastas.


<sup id="ex5dot1"></sup>
```sh
# estrutura de pastas
app/
    public/
        index.php
    src/
        Controller/
            ProductController.php # crie este arquivo
        routes.php
    composer.json
    composer.lock
```
```php
<?php
// app/src/routes.php

// GET /produtos: retorna uma lista de produtos no formato json
$router->get('/produtos', 'TutorialMvc\Controller\ProductController:fetch');
```
```php
<?php
// app/src/Controller/ProductController.php

namespace TutorialMvc\Controller;

class ProductController
{
    public function __construct()
    {
    }

    public function fetch($request, $response)
    {
        $data = [
            ['id' =>1, 'name' => 'batom'],
            ['id' =>2, 'name' => 'perfume'],
            ['id' =>3, 'name' => 'bolacha'],
            ['id' =>4, 'name' => 'Tomate'],
            ['id' =>5, 'name' => 'Felicidade'],
            ['id' =>6, 'name' => 'Conhecimento']
        ];

        return $response->withJson($data);
    }
}
```
<center><sup>Exemplo 5.1. Criando um controller</sup></center>

Os métodos de um *controller* que são utilizados no *router* recebem o nome de *actions*.

## 5.3. Injeção de dependências

A classe `ProductController` não possui até agora nenhuma dependência, no entanto, é responsável por retornar os produtos existentes. A lista de produtos poderia vir de uma consulta banco de dados ou de uma requisição em um servidor de API<sup>[19](#apis)</sup>, vamos supor que a classe `TutorialMvc\Model\Product` seja responsável por buscar os produtos do banco de dados. A ([Figura 5.3.1](#fig5dot3dot1)) mostra três possíveis formas de satisfazer essa dependência.


<sup id="fig5dot3dot1"></sup>
```php
<?php
// app/src/Controller/ProductController.php

namespace TutorialMvc\Controller;

use TutorialMvc\Model\Product;

class ProductController
{

    private $prodEnt;

    public function __construct(Product $p)
    {
        $this->prodEnt = $p;
    }

    public function fetch($request, $response)
    {
        $data = $this->prodEnt->fetch();
        return $response->withJson($data);
    }
}
```

```php
<?php
// app/src/Controller/ProductController.php

namespace TutorialMvc\Controller;

use TutorialMvc\Model\Product;

class ProductController
{

    private $prod;

    public function __construct()
    {
    }

    public function fetch($request, $response)
    {
        $data = $this->prod->fetch();
        return $response->withJson($data);
    }

    public function setProd(Product $p)
    {
        $this->prod = $p;
    }

}
```

```php
<?php
// app/src/Controller/ProductController.php

namespace TutorialMvc\Controller;

use TutorialMvc\Model\Product;

class ProductController
{

    public function __construct()
    {
    }

    public function fetch($request, $response)
    {
        // resolving Product dependency
        // ...
        // Product instance
        $prodEnt = new Product($dependency);

        $data = $prodEnt->fetch();
        return $response->withJson($data);
    }
}
```

<center><sup>Figura 5.3.1. Exemplos de definição de dependências</sup></center>

No primeiro caso, a dependência é fornecida pelo **construtor**, no segundo via método **set** e na terceira é criada internamente no método **fetch**. A definição via método set de dependências obrigatórias não é aconselhada, devido a possibilidade de esquecimento, do desenvolvedor, de definir a dependência antes de chamar o método fetch. A definição de dependências dentro do método também não é aconselhada, pois aumenta a responsabilidade de método (além de buscar os produtos ele também deve saber como criar o manipulador de produtos) e dificulta a criação de testes para `ProductController`. Assim, a primeira forma de injeção de dependências é a mais aconselhada. Antes de continuar aconselha-se a leitura da [PSR-11](https://www.php-fig.org/psr/psr-11/), [IoC](https://pt.wikipedia.org/wiki/Invers%C3%A3o_de_controle) e [DI](http://best-practice-software-engineering.ifs.tuwien.ac.at/patterns/dependency_injection.html).

## 5.4 Usando *Containers*

O Slim possui suporte a utilização de *containers* por meio do **Pimple** <sup>[20](#pimple)</sup>. O ([Exemplo 5.4.1](#ex5dot4dot1)) mostra a utilização de containers para satisfazer as dependências de `ProductController` e `Product`. Observação: lembre-se de criar o banco de dados e inserir as credenciais corretas.

<sup id="ex5dot4dot1"></sup>
```sh
app/
    config/
        config.local.php # crie este arquivo
        config.php # crie este arquivo
    public/
        index.php
    src/
        Model/
            Product.php # crie este arquivo
        Controller/
            ProductController.php
        routes.php
        dependencies.php # crie este arquivo
    composer.json
    composer.lock
```
```php
<?php
// app/config/config.local.php

return [
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'db_name' => 'tutorial_mvc',
        'db_user' => 'root',
        'db_pass' => 'root',
    ],
];
```
```php
<?php
// app/config/config.php

$config = [
    'database' => [
        'driver' => 'pdo_mysql',
        'charset' => 'utf8',
    ],
];

return array_merge_recursive(
    $config,
    require_once __DIR__ . '/config.local.php'
);
```
```php
<?php
// app/src/Model/Product.php

namespace TutorialMvc\Model;

use PDO;

class Product
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function fetch()
    {
        $data = [
            ['id' =>1, 'name' => 'batom'],
            ['id' =>2, 'name' => 'perfume'],
            ['id' =>3, 'name' => 'bolacha'],
            ['id' =>4, 'name' => 'Tomate'],
            ['id' =>5, 'name' => 'Felicidade'],
            ['id' =>6, 'name' => 'Conhecimento']
        ];

        return $data;
    }
}
```
```php
<?php
// app/src/Controller/ProductController.php

namespace TutorialMvc\Controller;

use TutorialMvc\Model\Product;

class ProductController
{
    private $prod;

    public function __construct(Product $p)
    {
        $this->prod = $p;
    }

    public function fetch($request, $response)
    {
        $data = $this->prodEnt->fetch();
        return $response->withJson($data);
    }
}
```
```php
<?php
// app/src/dependencies.php

use TutorialMvc\Controller\ProductController;
use TutorialMvc\Model\Product;

$container = $router->getContainer();

$config = require __DIR__ . '/../config/config.php';
$container['config'] = $config;

$container[PDO::class] = function($c) {
    $config = $c->get('config')['database'];
    return new PDO(
        'mysql:host='.$config['host'].';dbname='.$config['db_name'].';charset=' . $config['charset'],
        $config['db_user'],
        $config['db_pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
};

$container[Product::class] = function($c) {
    $pdo = $c->get(PDO::class);
    return new Product($pdo);
};

$container[ProductController::class] = function($c) {
    $p = $c->get(Product::class);
    return new ProductController($p);
};
```
```php
<?php
// app/public/index.php

require '../vendor/autoload.php';

$router = new Slim\App();

require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/routes.php';

$router->run();
```

<center><sup>Exemplo 5.4.1. Utilização do Pimple para satisfazer dependências da aplicação</sup></center>

---

<center>Notas de Rodapé</center>

<b id="apis">19</b> *Web API*. [[saber mais](https://en.wikipedia.org/wiki/Web_API)]

<b id="pimple">20</b> Documentação do Pimple. [[saber mais](https://pimple.symfony.com/)]

---

# 6. Model

# 6.1. Um modelo de verdade

No capítulo anterior concluímos a construção de uma ação de um *controller* e tivemos de criar um falso *model* para fazer a injeção de dependência. Vamos agora construir a consulta com o banco de dados, antes de iniciar, é aconselhada a leitura da documentação do PDO<sup>[21](#pdodoc)</sup>. O ([Exemplo 6.1.1](#ex6dot1dot1)) apresenta o código de criação da tabela de produtos, código para popular a tabela e modificações necessárias no *model*.

<sup id="ex6dot1dot1"></sup>
```sql
create table products(
    id int auto_increment primary key,
    name varchar(50) not null,
    created_at datetime default CURRENT_TIMESTAMP,
    updated_at datetime default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
);

insert into products(name) values 
('batom'),
('perfume'),
('bolacha'),
('Tomate'),
('Felicidade'),
('Conhecimento');
```

```php
<?php
// app/src/Model/Product.php

namespace TutorialMvc\Model;

use PDO;

class Product
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function fetch()
    {
        $stm = $this->conn->query('select * from products');

        if($stm) {
            return $stm->fetchAll();
        }

        return [];
    }
}
```
<center><sup>Exemplo 6.1.1. Comunicação com o banco de dados utilizando o PDO</sup></center>

## 6.2. Mais do que listar

Com exceção da *view*, conseguimos concluir o caminho completo de uma funcionalidade da aplicação. Vamos ampliar as funcionalidades para que seja possível criar, remover, atualizar e visualizar um produto específico. O primeiro passo é criar novas rotas. O ([Exemplo 6.2.1](#ex6dot2dot1)) exibe as modificações necessárias. Note que nossas rotas seguem o padrão REST. Observações: recomenda-se o uso do Postman<sup>[22](#postman)</sup> para excução dos exemplos e, ao testar a rota de atualização de produtos, deve-se marcar a opção `x-www-form-urlencoded`.

<sup id="ex6dot2dot1"></sup>
 ```php
 <?php
// app/src/routes.php

$router->group('/produtos', function(){
    // GET /produtos: retorna uma lista de produtos no formato json
    $this->get('', 'TutorialMvc\Controller\ProductController:fetch');
    // GET /produtos/{id}: retorna o produto cujo id é "id"
    $this->get('/{id}', 'TutorialMvc\Controller\ProductController:find');
    // POST /produtos: cria um novo produto
    $this->post('', 'TutorialMvc\Controller\ProductController:create');
    // PUT /produtos/{id}: atualiza o produto cujo id é "id"
    $this->put('/{id}', 'TutorialMvc\Controller\ProductController:update');
    // DELETE /produtos/{id}: remove o produto cujo id é "id"
    $this->delete('/{id}', 'TutorialMvc\Controller\ProductController:delete');    
});
 ```
<center><sup>Exemplo 6.2.1. Rotas para o CRUD de produtos</sup></center>

O segundo passo é adicionar novas ações em `TutorialMvc\Controller\ProductController`, o ([Exemplo 6.2.2](#ex6dot2dot2)) mostra as modificações. Neste ponto podemos ver que, como a dependência é fornecida via construtor ao invés de instanciada internamente, nossas ações possuem poucas linhas de código e são de fácil entendimento.

<sup id="ex6dot2dot2"></sup>
```php
<?php
// app/src/Controller/ProductController.php

namespace TutorialMvc\Controller;

use TutorialMvc\Model\Product;

class ProductController
{
    private $prod;

    public function __construct(Product $p)
    {
        $this->prod = $p;
    }

    public function fetch($request, $response)
    {
        $data = $this->prod->fetch();
        return $response->withJson($data);
    }

    public function create($request, $response)
    {
        $name = $request->getParam('name');
        $product = $this->prod->create($name);
        return $response->withJson($product);
    }

    public function find($request, $response, $args)
    {
        $product = $this->prod->find($args['id']);
        return $response->withJson($product);
    }

    public function update($request, $response, $args)
    {
        $name = $request->getParam('name');
        $product = $this->prod->update($args['id'], $name);
        return $response->withJson($product);
    }

    public function delete($request, $response, $args)
    {
        $product = $this->prod->delete($args['id']);
        return $response->withJson($product);
    }
}
```
<center><sup>Exemplo 6.2.2. Novas ações no controller</sup></center>

Finalmente, no *model* `Product` construímos os métodos necessários para executar as operações no banco de dados. O ([Exemplo 6.2.3](#ex6dot2dot3)) apresenta as modificações finais.

<sup id="ex6dot2dot3"></sup>
```php
<?php
// app/src/Model/Product.php

namespace TutorialMvc\Model;

use PDO;

class Product
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function fetch()
    {
        $stm = $this->conn->query('select * from products');

        if($stm) {
            return $stm->fetchAll();
        }

        return [];
    }

    public function find($id)
    {
        $stm = $this->conn->prepare('select * from products where id=?');
        if($stm->execute([$id])) {
            $product = $stm->fetch();
            if($product) {
                return $product;
            }
        }

        return [];
    }

    public function create($name)
    {
        $stm = $this->conn->prepare('insert into products(name) value(?)');
        if($stm->execute([$name])) {
            return $this->find($this->conn->lastInsertId());
        }

        return [];
    }

    public function update($id, $name)
    {
        $stm = $this->conn->prepare('update products set name=? where id=?');
        if($stm->execute([$name, $id])) {
            return $this->find($id);
        }

        return [];
    }

    public function delete($id)
    {
        $product = $this->find($id);

        if($product) {
            $stm = $this->conn->prepare('delete from products where id=?');
            if($stm->execute([$id])) {
                return $product;
            }
        }

        return [];
    }
}
```
<center><sup>Exemplo 6.2.2. Métodos para consulta, alteração e remoção de produtos</sup></center>

---

<center>Notas de Rodapé</center>

<b id="pdodoc">21</b> *PDO Doc*. [[saber mais](http://php.net/manual/pt_BR/book.pdo.php)]

<b id="pdodoc">22</b> *Postman Doc*. [[saber mais](https://www.getpostman.com/docs/v6/)]

---