# sped-gnre

[![Pipeline](https://github.com/nfephp-org/sped-gnre/actions/workflows/ci.yml/badge.svg)](https://github.com/nfephp-org/sped-gnre/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/nfephp-org/sped-gnre/v/stable)](https://packagist.org/packages/nfephp-org/sped-gnre)
[![Total Downloads](https://poser.pugx.org/nfephp-org/sped-gnre/downloads)](https://packagist.org/packages/nfephp-org/sped-gnre)
[![License](https://poser.pugx.org/nfephp-org/sped-gnre/license)](https://packagist.org/packages/nfephp-org/sped-gnre)

API para geração e comunicação da **GNRE** (Guia Nacional de Recolhimento de Tributos Estaduais)
com o Portal GNRE (SEFAZ-PE). Faz parte do projeto [NFePHP](https://github.com/nfephp-org) e segue o
mesmo padrão de sped-nfe, sped-cte e sped-mdfe: namespace `NFePHP\Gnre`,
[`nfephp-org/sped-common`](https://github.com/nfephp-org/sped-common) para certificado, SOAP e validação,
e configuração em JSON.

> ⚠️ **1.0 em release candidate.** A 1.0 **não é compatível** com a 0.x (`Sped\Gnre`); veja o
> [guia de migração](docs/MIGRACAO.md). Para continuar na API antiga, fixe `^0.3`.
>
> O envelope SOAP segue o WSDL oficial e o ACBr, mas **ainda não foi validado no ambiente de
> homologação** do Portal GNRE. Se você puder ajudar, execute o [roteiro de homologação](docs/HOMOLOGACAO.md)
> e informe o resultado.

## Requisitos

- PHP **7.4 a 8.5**
- Extensões: `dom`, `curl`, `openssl`, `soap`, `json`, `libxml`, `simplexml`, `mbstring`
- Certificado digital A1 (o mesmo usado para emitir NF-e) e o serviço GNRE liberado pela SEFAZ da UF

## Instalação

```sh
composer require nfephp-org/sped-gnre
```

## Uso

### Configuração e serviços (`Tools`)

```php
use NFePHP\Common\Certificate;
use NFePHP\Gnre\Tools;

$config = json_encode([
    'tpAmb' => 2,                  // 1 produção, 2 homologação
    'razaosocial' => 'EMPRESA LTDA',
    'cnpj' => '99999999000191',    // ou 'cpf'
    'siglaUF' => 'SP',
    'versao' => '2.00',            // '1.00' só para o lote 1.00 puro
]);
$tools = new Tools($config, Certificate::readPfx(file_get_contents('certificado.pfx'), 'senha'));

$tools->sefazConsultaConfigUf('PR', '100099');            // regras da UF/receita
$tools->sefazEnviaLote($xmlLote);                          // recibo
$tools->sefazConsultaLote($recibo, ['incluirPDFGuias' => 'S']);
$tools->sefazEnviaLoteConsulta($xmlLoteConsulta);          // consulta de guias já emitidas
$tools->sefazConsultaLoteConsulta($reciboConsulta);
```

Todos os métodos validam a mensagem no XSD **antes** do envio (`NFePHP\Common\Exception\ValidatorException`)
e devolvem a resposta SOAP bruta. A mensagem enviada fica em `$tools->lastRequest`.

### Montar o lote 2.00 (`Make`)

```php
use NFePHP\Gnre\Make;

$make = new Make();
$make->tagGuia((object) ['ufFavorecida' => 'SP', 'valorGNRE' => 12.52, 'dataPagamento' => '2026-12-15']);
$make->tagEmitente((object) ['CNPJ' => '99999999000191', 'razaoSocial' => 'EMPRESA LTDA']);
$make->tagItem((object) [
    'receita' => '100099',
    'documentoOrigem' => '5656',
    'documentoOrigemTipo' => '10',
    'dataVencimento' => '2026-12-15',
]);
$make->tagReferencia((object) ['periodo' => '0', 'mes' => '11', 'ano' => '2026']);
$make->tagItemValor((object) ['tipo' => '11', 'valor' => 12.52]);   // 11 = principal ICMS
$make->tagDestinatario((object) ['CNPJ' => '86268158000162', 'IE' => '10809181']);

$xmlLote = $make->getXML();   // RuntimeException com a lista de getErrors() se faltar algo
```

As tags podem ser chamadas em qualquer ordem, e o XML sai na sequência do XSD. Cada `tagGuia` abre uma
nova guia (várias por lote), e cada `tagItem` abre um novo item (até 100 por guia).

### Layout 1.00 (`MakeV1`)

Campos `c01_..c42_`, em dois formatos:

- `new MakeV1()`: **guia 1.00 dentro do lote 2.00**, aceito pelo pacote oficial 2.17 (config `versao` 2.00);
- `new MakeV1(MakeV1::FORMATO_LOTE_100)`: lote 1.00 puro, como na 0.x (config `versao` 1.00).

### Consulta de guias (`MakeConsulta`)

```php
$consulta = new \NFePHP\Gnre\MakeConsulta();
$consulta->tagConsulta((object) ['uf' => 'SP', 'codBarras' => $codigoDeBarras, 'tipoConsulta' => 'C']);
$tools->sefazEnviaLoteConsulta($consulta->getXML());
```

### Ler os retornos (`Standardize` e `RetornoTxtV1`)

```php
$std = (new \NFePHP\Gnre\Common\Standardize())->toStd($tools->sefazConsultaLote($recibo));
$std->situacaoProcess->codigo;            // 402 = processado com sucesso
$guias = $std->resultado->guia;           // um objeto (1 guia) ou array (várias)
$guia = is_array($guias) ? $guias[0] : $guias;
$guia->linhaDigitavel;
$guia->itensGNRE->item->valor;            // {attributes: {tipo: '11'}, value: '12.52'} (ou array)
```

Os atributos são preservados (`attributes` + `value`). No lote 1.00 puro, o `resultado` vem em TXT
posicional: use `\NFePHP\Gnre\Common\RetornoTxtV1::ler($std->resultado)`.

### Guia a partir da NF-e (`FromNFe`)

```php
$nfe = new \NFePHP\Gnre\Factories\FromNFe($xmlNfeAutorizada);
$make = $nfe->guiaDifal((object) ['receita' => '100102', 'dataVencimento' => '2026-12-15']);
// ou $nfe->guiaSt(...); o Make devolvido ainda aceita tags (ex.: tagCampoExtra)
```

O emitente vai com a `IEST` (inscrição na UF favorecida) quando a NF-e a tiver. O documento de origem
segue as sugestões de `NFePHP\Gnre\Common\RegrasUf`, e a regra oficial de cada UF vem do `sefazConsultaConfigUf()`.

### Impressão da guia

O PDF fica no [nfephp-org/sped-da](https://github.com/nfephp-org/sped-da), junto com DANFE, DACTE e DAMDFE:

```sh
composer require nfephp-org/sped-da
```

```php
$dagnre = new \NFePHP\DA\GNRE\Dagnre($tools->sefazConsultaLote($recibo));
$pdf = $dagnre->render();
```

São 3 vias por guia, com código de barras de arrecadação (2 de 5 intercalado) e QR Code PIX quando a
SEFAZ o informa. Sem o sped-da, use `sefazConsultaLote($recibo, ['incluirPDFGuias' => 'S'])`: o portal
devolve o PDF pronto em `resultado->pdfGuias` (base64).

Mais exemplos em [`examples/`](examples/).

## Formatos e dicas

- **Datas** no formato `AAAA-MM-DD`.
- **Município**: código IBGE **sem** os 2 dígitos da UF (5 dígitos: `3550308` → `50308`).
- **Valores** numéricos com ponto (`10.99`).
- **Recibos** com 10 ou 14 dígitos (os atuais têm 14).
- Algumas UFs exigem o **valor total** (tipo `21`) além do principal (`11`). Confira no ConfigUF.
- O portal recusa mensagens acima de **600 KB**; a `Tools` avisa antes de enviar.
- Schemas do **Manual de Integração do Contribuinte GNRE v2.17** em `schemes/PL_GNRE_200/`
  (origem em [`schemes/README.md`](schemes/README.md)).

## Antes de usar a API

- Verifique se seu certificado digital não foi expedido através da [certisign](https://www.certisign.com.br), pois existe um problema na cadeia do certificado que impossibilita a emissão de guias GNRE. Certificados expedidos através do [SERASA](https://serasa.certificadodigital.com.br/) funcionam normalmente para a emissão (até agora nenhum erro foi relatado).

- É permitido utilizar o mesmo certificado utilizado para emitir NF-e.

- É necessário entrar em contato com a SEFAZ de cada estado pedindo liberação do serviço de emissão de GNRE.

- Leia todos os tópicos no FAQ oficial em http://www.gnre.pe.gov.br/gnre/portal/faq.jsp. Os tópicos abordados são muito úteis para quem está começando nesse serviço.

## Documentação

- [Guia de migração 0.x → 1.0](docs/MIGRACAO.md)
- [Roteiro de homologação](docs/HOMOLOGACAO.md)
- [Histórico da refatoração (decisões e andamento)](docs/refatoracao/ROADMAP.md)
- [CHANGELOG](CHANGELOG.md)

## Desenvolvimento

```sh
composer install
composer phpcs   # PSR-12 + PHPCompatibility (PHP 7.4+)
composer stan    # PHPStan nível 5
composer test    # PHPUnit (sem rede: usa um SoapFake)
```

## Créditos

Projeto criado por [Matheus Marabesi](https://github.com/marabesi) e refatorado para o padrão
NFePHP (versão 1.0) por [Alberto Leal](https://github.com/betoeu). Mantido pela
[Comunidade NFePHP](https://github.com/nfephp-org/sped-gnre/graphs/contributors).

## Licença

Este pacote está disponibilizado sob LGPLv3, GPLv3 ou MIT License (MIT), à sua escolha.
Leia o [Arquivo de Licença](LICENSE.md) para maiores informações.
