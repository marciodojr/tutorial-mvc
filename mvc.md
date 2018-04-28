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

<b id="mvc">3</b> *Model-View-Controller*. [[saber mais](https://en.wikipedia.org/wiki/Software_development_kit)]

---

# 2. Conceitos iniciais

## 2.1. Observações

Construir uma aplicação com uma arquitetura de camadas facilita o entendimento de como cada parte da aplicação comunica com as demais, reduz a complexidade dos testes e também facilita a entrada de novos membros na equipe. Uma equipe que segue padrões claros e bem conhecidos no mercado deve conseguir, com menor esforço, realizar treinamentos para novos integrantes.

## 2.2. Aviso importante

Construiremos uma aplicação Mvc bastante simples seguido os padrões estabelecidos pela comunidade PHP, no entanto, como o objetivo é manter a didática, não construiremos um framework comercial, mas forneceremos a base para o entendimento de como eles funcionam (pelo menos alguns de seus aspectos mais importantes).

## 2.3. Requisitos

O requisitos são:

* php (versão 7 ou a mais nova que conseguir)
* Um bom editor de código (vscode é uma dica)
* composer <sup id="backComposer">[4](#composer)</sup>

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

Uma aplicação feita em **C** ou **C++** contém um ponto de entrada chamado `main`, em **Java** o ponto de entrada é um método também chamado `main`.  O arquivo **index.php** em nossa estrutura, atuará como um *entry point*<sup>[5](#entrypoint)</sup> da aplicação, toda requisição será direcionada a ele, de forma que atue como um *Front Controller*<sup>[6](#fcontroller)</sup>.


## 2.6. Gestão de dependências

Cada linguagem de programação costumar ter seu proprio gerenciador de dependências, Um gerenciador de dependências facilita a adição de bibliotecas de terceiros a nossa aplicação, permitindo definir versões e realizar atualizações automaticamente. Ao desenvolver uma aplicação em geral não estamos interessados em criar todo o código que utilizaremos, é bastante comum utilizar bibliotecas livres e bastante estáveis, isso acelera o desenvolvimento e reduz a quantidade de testes necessários. Para o **php** utilizaremos o **Composer**<sup>[7](#wcomposer)</sup>. Os arquivos **composer.json**, **composer.lock** e a pasta **vendor** serão utilizados pelo Composer.

## 2.7. Configuração local

O arquivo **config.local.php** conterá variáveis de ambiente. Este arquivo costuma ser utilizado para adição de senhas e usuários para manipulação de banco de dados e flags para habilitar/desabilitar logs. Esse arquivo, em conjunto com a pasta **vendor** devem ser ignorados em VCS's<sup>[8](#vcs)</sup>.


## 2.8. Rodando a aplicação

Para rodar a aplicação utilizaremos o servidor web embutido do php<sup>[9](#phpcli)</sup>. Crie a estrutura da ([Figura 2.2](#fig2dot2)) e no arquivo **index.php** coloque o código do ([Exemplo 2.1](#ex2dot1)). Com o terminal dentro da pasta **app** rode o comando `php -S localhost:4200 -t public` e acesse no navegador pela url [localhost:4200](http://localhost:4200).


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

---
<center> Notas de Rodapé </center>

<b id="composer">4</b> Como baixar o composer. [[saber mais](https://getcomposer.org/download/)]

<b id="entrypoint">5</b> O que é um *Entry point*? [[saber mais](https://en.wikipedia.org/wiki/Entry_point)]

<b id="fcontroller">6</b> O que é o padrão *Front Controller*? [[saber mais](https://en.wikipedia.org/wiki/Front_controller)]

<b id="vcs">7</b> O que é o Composer? [[saber mais](https://getcomposer.org/doc/)]

<b id="vcs">8</b> O que é um Sistema de Controle de Versão?. [[saber mais](https://en.wikipedia.org/wiki/Version_control)]

<b id="phpcli">8</b> *PHP Built-in Server*. [[saber mais](https://secure.php.net/manual/pt_BR/features.commandline.webserver.php)]

---

# 3. Classes e Objetos

## 3.1. Uma história de como incluir classes

Era bastante comum (e, infelizmente, ainda é) utilizar diretamente os construtores de linguagem `require`, `require_once`,  `include` e `include_once`. Apesar de ser possível incluir arquivos de classes dessa forma, o php possui formas mais adequadas de incluir as dependências de classes. Em 2004 (versão 5.0), foi introduzida a função mágica<sup>[10](#phpmagic)</sup> `__autoload`<sup>[11](#fautoload)</sup>, esta função, quando declarada, é chamada automaticamente sempre que uma classe ainda não incluída é usada. Em 2005 (versão 5.1), foi introduzida uma família de funções `spl_autoload*`<sup>[12](#fsplautoload)</sup> para substutir a função `__autoload`, permitindo o registro de múltiplas funções de inclusão de classes e, consequentemente, facilitando a utilização de bibliotecas de terceiros (a partir do uso de funções de inclusão de classe de terceiros). 
Para evitar conflitos de classes com mesmo nome, em 2009 (versão 5.3), o php adicionou o recurso de namespace<sup>[13](#namespaces)</sup>. Com o surgimento do Composer em 2012 e a formalização das propostas de *autoload*, padronizou-se o modelo de carregamentos de classes conforme PSR-4<sup>[14](#psr4)</sup>.


---
<center>Notas de Rodapé</center>


<b id="phpmagic">10</b> O que é função mágica? [[saber mais](http://php.net/manual/pt_BR/language.oop5.magic.php)]

<b id="fautoload">11</b> O que é a função __autoload? [[saber mais](http://php.net/manual/pt_BR/function.autoload.php)]

<b id="fsplautoload">12</b> O que são as funções spl_autoload? [[saber mais](http://php.net/manual/pt_BR/function.spl-autoload.php)]

<b id="namespaces">13</b> O que é namespace? [[saber mais](http://php.net/manual/pt_BR/language.oop5.magic.php)]

<b id="psr4">14</b> *PSR-4: Autoloader* [[saber mais](https://www.php-fig.org/psr/psr-4/)]

---