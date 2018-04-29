# 1. Um Início

## 1.1. Introdução

Olá! `o/`, fizemos este documento para explicar para você como é desenvolver uma aplicação MVC seguindo os melhores padrões da comunidade PHP, caso você já conheça, agora é um bom momento para revisar um pouquinho. 

Sabemos que costumar ser um pouco difícil entrar em uma nova equipe e utilizar ferramentas e tecnologias que nunca vimos. Por isso, antes de "botar as mãos na massa" queremos dar a você a oportunidade de praticar os conceitos de orientação a objetos e construção de software em camadas a partir daquilo que você já aprendeu na graduação.

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

O grande problema em construir um único arquivo está na dificuldade em dar manutenção e na baixa reusabilidade. A medida que o tempo passa (e novas modificações são feitas), vai ficando cada vez mais difícil entender o que o código faz, aonde é necessário mudar e se, ao mudar, todo o resto continuará funcionando.

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

Construiremos uma aplicação Mvc bastante simples seguido os padrões estabelecidos pela comunidade PHP, no entanto, como o objetivo é manter a didática, não construiremos um framework comercial, mas forneceremos a base para o entendimento de como eles funcionam (pelo menos alguns de seus aspectos mais importantes).

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
    src/
        Model/
        Controller/ 
        View/
    public/
        img/
        js/
        css/
        index.php # entry point
    routes.php
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

## 2.7. Configuração local

O arquivo **config.local.php** conterá variáveis de ambiente. Este arquivo costuma ser utilizado para adição de senhas e usuários para manipulação de banco de dados e flags para habilitar/desabilitar logs. Esse arquivo, em conjunto com a pasta **vendor** devem ser ignorados em VCS's<sup>[9](#vcs)</sup>.


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

A ([Figura 3.1](#fig3dot1)) apresenta a maneira padrão de definição de dependências da classe. Note que a classe  `ProductEntity` possui um namespace declarado no início do arquivo (`TutorialMvc\Model`) e a classe `ProductController` também (`TutorialMvc\Controller`), esta ainda define como será o uso de `ProductEntity` por meio do operador `use`<sup>[16](#useop)</sup>.


<sup id="fig3dot1"></sup>
```php
<?php
// src/Model/ProductEntity.php

namespace TutorialMvc\Model;

class ProductEntity
{
//..
```
```php

<?php
// src/Model/ProductController.php

namespace TutorialMvc\Controller;

use TutorialMvc\Model\ProductEntity;

class ProductController
{

    private $productEntity;

    public function __construct(ProductEntity $pe)
    {
        $this->productEntity = $pe;
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
<center><sup>Exemplo 3.2. Adições realizadas no arquivo composer.json</sup></center>

Note que após executar este procedimento, será criado o arquivo **composer.json** e a pasta **vendor** ([Figura 3.2](#fig3dot2)). A partir deste ponto todas as bibliotecas de terceiro instaladas pelo composer e as classes criadas por nós em **src** com namespace iniciado com  `TutorialMvc\`, quando usadas, serão incluídas corretamente.


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
<center><sup>Figura 3.2. Requição em uma aplicação com entry point</sup></center>

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

A definição da url, a escolha do método http e a ação a ser executada é definida pelo desenvolvedor, mas aconselha-se defini-los em confirmidade com o padrão REST<sup>[18](#restm)</sup>.


## 4.3 O Router é um Model, um Controller ou uma View?

O *router* não é parte do MVC e, portanto, não deve estar em nossa aplicação. Ele deve ser tratado com uma dependência externa, só é recomendada a construção de um, se sua aplicação possuir uma necessidade muito específica ou para fins didádicos (fora do escopo desse tutorial). Antes de realizar a instalação de um aconselha-se fortemente a leitura da [PSR-7](https://www.php-fig.org/psr/psr-7/).

## 4.4 Instalando o Router

Utilizaremos o composer para instalar um router. O ([Exemplo 4.1](#ex4dot1)) apresenta o processo de instalação.

<sup id="ex4dot1"></sup>
```sh
~/vhosts/app$ composer require nikic/fast-route
Using version ^1.3 for nikic/fast-route
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Installing nikic/fast-route (v1.3.0): Loading from cache
Writing lock file
Generating autoload files
~/vhosts/app$
```
<center><sup>Exemplo 4.1. Instalação do fast-route</sup></center>


## 4.5 Usando o *Fast Route*

---
<center>Notas de Rodapé</center>

<b id="httpm">17</b> *HTTP Request Methods*. [[saber mais](https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Methods)]

<b id="restm">18</b> *REST Methods*. [[saber mais](http://www.restapitutorial.com/lessons/httpmethods.html)]


---