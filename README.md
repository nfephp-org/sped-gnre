# sped-gnre

[![Pipeline](https://github.com/betoeu/sped-gnre/actions/workflows/ci.yml/badge.svg?branch=refactor/v1)](https://github.com/betoeu/sped-gnre/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/nfephp-org/sped-gnre/v/stable)](https://packagist.org/packages/nfephp-org/sped-gnre)
[![Total Downloads](https://poser.pugx.org/nfephp-org/sped-gnre/downloads)](https://packagist.org/packages/nfephp-org/sped-gnre)
[![License](https://poser.pugx.org/nfephp-org/sped-gnre/license)](https://packagist.org/packages/nfephp-org/sped-gnre)

API para geração e comunicação da **GNRE** (Guia Nacional de Recolhimento de Tributos Estaduais)
com o Portal GNRE (SEFAZ-PE). Faz parte do projeto [NFePHP](https://github.com/nfephp-org).

> ⚠️ **A versão 1.0 está em desenvolvimento** na branch `refactor/v1`. Ela segue o padrão de
> sped-nfe, sped-cte e sped-mdfe (namespace `NFePHP\Gnre`, `nfephp-org/sped-common`, config em JSON)
> e **não é compatível** com a 0.x. Para continuar usando a API antiga (`Sped\Gnre`), fixe a
> versão `^0.3` no seu `composer.json`.
>
> Planejamento, decisões e andamento: [`docs/refatoracao/ROADMAP.md`](docs/refatoracao/ROADMAP.md).

## Requisitos

- PHP **7.4 a 8.5**
- Extensões: `dom`, `curl`, `openssl`, `soap`, `json`, `libxml`, `simplexml`, `mbstring`
- Certificado digital A1 (o mesmo usado para emitir NF-e)

## Instalação

```sh
composer require nfephp-org/sped-gnre
```

## Layout e schemas

Layout de lote **2.00**, com os schemas do **Manual de Integração do Contribuinte GNRE v2.17**
em `schemes/PL_GNRE_200/` (origem registrada em [`schemes/README.md`](schemes/README.md)).

## Antes de usar a API

- Verifique se seu certificado digital não foi expedido através da [certisign](https://www.certisign.com.br), pois existe um problema na cadeia do certificado que impossibilita a emissão de guias GNRE. Certificados expedidos através do [SERASA](https://serasa.certificadodigital.com.br/) funcionam normalmente para a emissão (até agora nenhum erro foi relatado).

- É permitido utilizar o mesmo certificado utilizado para emitir NF-e.

- É necessário entrar em contato com a SEFAZ de cada estado pedindo liberação do serviço de emissão de GNRE.

- Leia todos os tópicos no FAQ oficial em http://www.gnre.pe.gov.br/gnre/portal/faq.jsp. Os tópicos abordados são muito úteis para quem está começando nesse serviço.

## Desenvolvimento

```sh
composer install
composer phpcs   # PSR-12 + PHPCompatibility (PHP 7.4+)
composer stan    # PHPStan nível 5
composer test    # PHPUnit
```

## Créditos

Projeto criado por [Matheus Marabesi](https://github.com/marabesi) e refatorado para o padrão
NFePHP (versão 1.0) por [Alberto Leal](https://github.com/betoeu). Mantido pela
[Comunidade NFePHP](https://github.com/nfephp-org/sped-gnre/graphs/contributors).

## Licença

Este pacote está disponibilizado sob LGPLv3, GPLv3 ou MIT License (MIT), à sua escolha.
Leia o [Arquivo de Licença](LICENSE.md) para maiores informações.
