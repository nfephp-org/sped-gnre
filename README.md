[![Build Status](https://travis-ci.org/nfephp-org/sped-gnre.svg?branch=master)](https://travis-ci.org/nfephp-org/sped-gnre)
[![Coverage Status](https://coveralls.io/repos/marabesi/gnrephp/badge.svg)](https://coveralls.io/r/nfephp-org/sped-gnre)
[![Total Downloads](https://poser.pugx.org/marabesi/gnre/downloads)](https://packagist.org/packages/nfephp-org/sped-gnre)
[![Latest Stable Version](https://poser.pugx.org/marabesi/gnre/v/stable)](https://packagist.org/packages/nfephp-org/sped-gnre)
[![Latest Unstable Version](https://poser.pugx.org/marabesi/gnre/v/unstable.png)](https://packagist.org/packages/nfephp-org/sped-gnre)
[![License](https://poser.pugx.org/marabesi/gnre/license)](https://packagist.org/packages/nfephp-org/sped-gnre)

Atenção!!
=================
Caso encontre algum estado que possua uma regra especial para gerar uma GNRE por favor informar abrindo uma **issue**.
Dessa forma podemos manter a a API atualizada e ajudar a todos que utlizam a GNRE PHP

Antes de usar a API
=================

Verifique se seu certificado digital não foi expedido através da [certisign](https://www.certisign.com.br), pois existe um problema na cadeia do certificado que impossibilita a emissão de guias GNRE. Certificados expedidos através do [SERASA](https://serasa.certificadodigital.com.br/) funcionam normalmente para a emissão (até agora nenhum erro foi relatado).

GNRE PHP
=================

Objetivo
-----
 API possibilita a comunicação com a SEFAZ para a emissão da nota GNRE (Guia Nacional de Recolhimento de Tributos Estaduais). 
 A API GNRE tem como maior inspiração a API NFEPHP que você pode encontrar através do link https://github.com/nfephp

Dependências
-------
* [Apache](http://httpd.apache.org/) / [Nginx](http://nginx.org/)
* [PHP 5.3+](http://php.net)
* Extensões PHP
 * [DOMDocument](http://br2.php.net/manual/en/domdocument.construct.php)
 * [cURL](http://br2.php.net/manual/book.curl.php)
 * [GD (Utilizada para renderizar o código de barras)] (http://php.net/manual/pt_BR/book.image.php)

------

Informações úteis
-----

|Descrição|Endereço|
|---------|--------|
|Grupo de discussão | https://groups.google.com/forum/#!forum/gnrephp|
|Site oficial do governo | http://www.gnre.pe.gov.br/gnre/index.html|
|Site do Projeto | http://nfephp-org.github.io/sped-gnre/|
|Wiki, onde é possível encontrar maiores informações de como utilizar a API | https://github.com/nfephp-org/sped-gnre/wiki|
|Site oficial da SEFAZ de todo os estados|http://www.gnre.pe.gov.br/gnre/portal/linksUteis.jsp|

1. Antes de gerar qualquer guia GNRE com o seu certificado, tenha **CERTEZA** que você possui autorização para isso. A geração de
GNRE depende de cada estado, ou seja, se você deseja gerar a guia para o Acre (com destino ao Acre) tenha certeza que 
já pediu a liberação do certificado no SEFAZ Acre e repita esse processo para cada estado.

Documentação
------
* Documentação da GNRE PHP gerada com o PHPDOC pode ser visualizada [aqui](http://nfephp-org.github.io/sped-gnre//doc/namespaces/Gnre.html)

* Nosso wiki de como utilizar a API e gerar as GNRES está disponível [aqui no github](https://github.com/nfephp-org/sped-gnre/wiki)

Instalação via composer
------
Adicionando a GNRE PHP em um projeto existente com o composer

Caso você não possua o composer veja [esse link](https://getcomposer.org/doc/01-basic-usage.md) antes de prosseguir

Adicione a dependência da GNRE PHP no arquivo composer.json :

``` json
{
    "marabesi/gnre": "dev-master"
}
```

Atualize suas depedências existentes no composer :

``` terminal
composer update
```
-----
Possíveis erros
-----

Erro : **unable to use client certificate (no key found or wrong pass phrase?)**

Se você está obtendo essa mensagem após enviar a requisição para o web service da SEFAZ verifique a senha que você está utilizando, pois esse erro ocorre quando a senha informada não bate com a senha do certificado utilizado

Erro: **[InvalidArgumentException]                                                                                                                 
Could not find package marabesi/gnre at any version for your minimum-stability (stable). Check the package spelling or your minimum-stability**

Esse problema ocorre pois não estamos informando ao composer qual a versão mínima que queremos utilizar, para resolver esse problema basta adicionar a seguinte linha no seu arquivo composer.json

``` json
{
    "minimum-stability": "dev" 
}
```
-----
Quick start
-----
Clone o repositório do projeto
``` terminal
git clone https://github.com/nfephp-org/sped-gnre.git
```
Vá para a pasta de exemplos
```
cd exemplos/
```
Rode o servidor built-in do PHP
```
php -S localhost:8181
```
Abra o seu navegador e digite a seguinte URL
```
http://localhost:8181/gerar-xml.php
```
-----

Caso queira ver outros exemplos utilizados pela API acesse esse link https://github.com/nfephp-org/sped-gnre/tree/master/exemplos
