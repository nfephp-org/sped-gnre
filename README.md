[![Build Status](https://travis-ci.org/nfephp-org/sped-gnre.svg)](https://travis-ci.org/marabesi/gnrephp)
[![Coverage Status](https://coveralls.io/repos/marabesi/gnrephp/badge.svg)](https://coveralls.io/r/marabesi/gnrephp)
[![Total Downloads](https://poser.pugx.org/marabesi/gnre/downloads)](https://packagist.org/packages/marabesi/gnre)
[![Latest Stable Version](https://poser.pugx.org/marabesi/gnre/v/stable)](https://packagist.org/packages/marabesi/gnre)
[![Latest Unstable Version](https://poser.pugx.org/marabesi/gnre/v/unstable.png)](https://packagist.org/packages/marabesi/gnre)
[![License](https://poser.pugx.org/marabesi/gnre/license)](https://packagist.org/packages/marabesi/gnre)

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
 * [GD] (http://php.net/manual/pt_BR/book.image.php)

------

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
-----

Mais informações
-----
Site oficial do governo :     http://www.gnre.pe.gov.br/gnre/index.html

Site do Projeto : http://nfephp-org.github.io/sped-gnre/

E nossa **WIKI**, onde é possível encontrar maiores informações de como utilizar a API : https://github.com/nfephp-org/sped-gnre/wiki

