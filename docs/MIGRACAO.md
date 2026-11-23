# Migração 0.x → 1.0

A 1.0 é uma reescrita no padrão dos demais pacotes NFePHP (sped-nfe, sped-cte, sped-mdfe): namespace
`NFePHP\Gnre`, `nfephp-org/sped-common` para certificado, SOAP e validação, e configuração em JSON.
**Não há camada de compatibilidade com a 0.x.** Para continuar na API antiga, fixe `"nfephp-org/sped-gnre": "^0.3"`.

## Requisitos

| | 0.x | 1.0 |
|---|---|---|
| PHP | `>=7.3` declarado, mas usava sintaxe do PHP 8 em algumas classes | **7.4 a 8.5**, testado na CI |
| Dependências | Smarty, Dompdf, laminas, sabberworm, sped-nfe e Guzzle (não declarado) | só `nfephp-org/sped-common` (+ `nfephp-org/sped-da`, opcional, para imprimir) |

## Equivalência de classes

| 0.x (`Sped\Gnre\...`) | 1.0 |
|---|---|
| `Configuration\Setup` (classe abstrata) + `CertificatePfx` + `FileOperation` | config JSON + `NFePHP\Common\Certificate::readPfx()` (a chave privada não é mais gravada em disco pelo pacote) |
| `Webservice\Connection` / `ConnectionFactory` + `Sefaz\Send` | `NFePHP\Gnre\Tools` (usa o `SoapCurl` do sped-common) |
| `Sefaz\Lote` (layout 1.00) | `NFePHP\Gnre\MakeV1` (`FORMATO_LOTE_100`, ou `FORMATO_LOTE_200` para a guia 1.00 dentro do lote 2.00) |
| `Sefaz\LoteV2` (layout 2.00) + `Sefaz\Guia` | `NFePHP\Gnre\Make` |
| `Send::sefaz($lote)` | `Tools::sefazEnviaLote($make->getXML())` |
| `Sefaz\Consulta` + `Send` | `Tools::sefazConsultaLote($recibo, $opcoes)` |
| `Sefaz\ConfigUf` + `Send` | `Tools::sefazConsultaConfigUf($uf, $receita, $courier)` |
| — (não existia) | `Tools::sefazEnviaLoteConsulta()` + `MakeConsulta` e `Tools::sefazConsultaLoteConsulta()` (consulta de guias emitidas) |
| `Parser\SefazRetorno` (TXT do lote 1.00) | `NFePHP\Gnre\Common\Standardize` + `NFePHP\Gnre\Common\RetornoTxtV1` |
| `Render\Html` + `Render\Pdf` + `Render\Barcode128` + `templates/gnre.tpl` | `NFePHP\DA\GNRE\Dagnre` no **sped-da** |
| `Helper\GnreHelper` | `NFePHP\Gnre\Factories\FromNFe` (`guiaDifal()`, `guiaSt()`) |
| `LoteV2::getCodigoDoc()` / `getNumDoc()` | `NFePHP\Gnre\Common\RegrasUf::tipoDocOrigem()` / `tipoChaveDoc()` |
| `Sefaz\EstadoFactory` + `Sefaz\Estados\*` | removidos: as regras da UF vêm do `sefazConsultaConfigUf()` |

## Configuração e envio

0.x:

```php
class MinhaConfig extends \Sped\Gnre\Configuration\Setup { /* 12 métodos abstratos */ }

$lote = new \Sped\Gnre\Sefaz\LoteV2();
$lote->addGuia($guia);
$send = new \Sped\Gnre\Sefaz\Send(new MinhaConfig());
$send->setConnectionFactory(new \Sped\Gnre\Webservice\ConnectionFactory());
$retorno = $send->sefaz($lote);
```

1.0:

```php
use NFePHP\Common\Certificate;
use NFePHP\Gnre\Make;
use NFePHP\Gnre\Tools;

$config = json_encode([
    'tpAmb' => 2,                 // 1 produção, 2 homologação
    'razaosocial' => 'EMPRESA LTDA',
    'cnpj' => '99999999000191',   // ou 'cpf'
    'siglaUF' => 'SP',
    'versao' => '2.00',           // ou '1.00' para o lote 1.00 puro
]);
$tools = new Tools($config, Certificate::readPfx(file_get_contents('certificado.pfx'), 'senha'));

$make = new Make();
$make->tagGuia((object) ['ufFavorecida' => 'SP', 'valorGNRE' => 12.52, 'dataPagamento' => '2026-12-15']);
$make->tagEmitente((object) ['CNPJ' => '99999999000191', 'razaoSocial' => 'EMPRESA LTDA']);
$make->tagItem((object) ['receita' => '100099', 'dataVencimento' => '2026-12-15']);
$make->tagItemValor((object) ['tipo' => '11', 'valor' => 12.52]);

$retorno = $tools->sefazEnviaLote($make->getXML());           // retorno SOAP bruto
$std = (new \NFePHP\Gnre\Common\Standardize())->toStd($retorno);
$recibo = $std->recibo->numero;
```

## Campos da guia: `Sefaz\Guia` (0.x) → `Make` (layout 2.00)

| 0.x | 1.0 (`Make`) |
|---|---|
| `c01_UfFavorecida` | `tagGuia->ufFavorecida` |
| `c02_receita` | `tagItem->receita` |
| `c25_detalhamentoReceita` | `tagItem->detalhamentoReceita` |
| `c26_produto` | `tagItem->produto` |
| `c27_tipoIdentificacaoEmitente` + `c03_idContribuinteEmitente` | `tagEmitente->CNPJ` ou `->CPF` |
| `c17_inscricaoEstadualEmitente` | `tagEmitente->IE` |
| `c16_razaoSocialEmitente`, `c18_enderecoEmitente`, `c19_municipioEmitente`, `c20_ufEnderecoEmitente`, `c21_cepEmitente`, `c22_telefoneEmitente` | `tagEmitente->razaoSocial`, `endereco`, `municipio`, `uf`, `cep`, `telefone` |
| `c28_tipoDocOrigem` + `c04_docOrigem` | `tagItem->documentoOrigemTipo` + `documentoOrigem` |
| `c06_valorPrincipal` | `tagItemValor` com `tipo` `11` (e, se a UF exigir, também o total `21`) |
| `c10_valorTotal` | `tagGuia->valorGNRE` |
| `c14_dataVencimento` | `tagItem->dataVencimento` |
| `c15_convenio` | `tagItem->convenio` |
| `c33_dataPagamento` | `tagGuia->dataPagamento` |
| `periodo`, `mes`, `ano`, `parcela` | `tagReferencia` |
| `c34_tipoIdentificacaoDestinatario` + `c35_idContribuinteDestinatario`, `c36`, `c37`, `c38` | `tagDestinatario->CNPJ`/`CPF`, `IE`, `razaoSocial`, `municipio` |
| `c39_camposExtras` | `tagCampoExtra` (até 3 por item; no 2.00 não há `tipo`) |
| `c42_identificadorGuia` | `tagGuia->identificadorGuia` |
| `retorno*` (preenchidos pelo parser) | vêm no retorno: `Standardize::toStd($retorno)->resultado->guia` |

No layout 1.00 (`MakeV1`), os nomes `c01_...c42_` continuam os mesmos da 0.x.

## Formatos de dados (validados no XSD oficial 2.17)

Os exemplos da 0.x usavam formatos que o XSD rejeita. Na 1.0:

- **datas** no formato `AAAA-MM-DD` (não `DD/MM/AAAA`);
- **município** com o código IBGE **sem** os 2 dígitos da UF (5 dígitos: `3550308` → `50308`);
- **receita** e **detalhamento** com 6 dígitos;
- **valores** numéricos com ponto (`'10.99'` ou `10.99`; `'10,99'` gera erro);
- **recibos** com 10 ou 14 dígitos (os atuais têm 14).

## Mudanças de comportamento (correções)

- O lote 2.00 da 0.x **descartava** `detalhamentoReceita`, `produto`, a IE do emitente e o `convenio`; a 1.0 envia.
- A 0.x enviava o valor principal **duas vezes** (tipos 11 e 21); a 1.0 envia só os valores informados.
- `identificadorGuia` e `referencia/periodo` deixaram de ser fixos (`1` e `0`).
- No layout 1.00, o período `0` (mensal) não é mais descartado.
- O retorno TXT do lote 1.00 é lido com as posições corretas (as da 0.x eram deslocadas).
- O envelope SOAP segue o WSDL oficial (como o ACBr), igual em produção e homologação.
- A impressão usa código de barras **2 de 5 intercalado** (padrão Febraban de arrecadação) em vez de Code128,
  e inclui o QR Code PIX quando a SEFAZ o informa.
- Erros de preenchimento aparecem em `Make::getErrors()`, e o `getXML()` se recusa a gerar o lote com erro;
  XML inválido no XSD gera `NFePHP\Common\Exception\ValidatorException` **antes** do envio.

## Impressão

```sh
composer require nfephp-org/sped-da
```

```php
$dagnre = new \NFePHP\DA\GNRE\Dagnre($tools->sefazConsultaLote($recibo));
$pdf = $dagnre->render();
```
