# Changelog

Todas as mudanças relevantes deste pacote são registradas aqui.

Projeto criado por Matheus Marabesi; refatoração para o padrão NFePHP (1.0) por Alberto Leal.

O formato segue o [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/) e o versionamento segue o [SemVer](https://semver.org/lang/pt-BR/).

## [1.0.0] - Unreleased

### Added
- `NFePHP\Gnre\Tools` com os 5 serviços do Portal GNRE sobre o `SoapCurl` do sped-common:
  `sefazEnviaLote`, `sefazConsultaLote` (com as opções `incluirPDFGuias`, `incluirArquivoPagamento`
  e `incluirNoticias`), `sefazConsultaConfigUf`, `sefazEnviaLoteConsulta` e `sefazConsultaLoteConsulta`.
- Consulta de guias já emitidas (serviços `GnreLoteRecepcaoConsulta` e `GnreResultadoLoteConsulta`), que não existia na 0.x.
- Suporte aos layouts de lote 1.00 e 2.00, escolhidos pelo `versao` do config JSON.
- `storage/wsgnre.json` com URLs, namespaces e versões de cada serviço; os envelopes SOAP seguem o WSDL oficial.
- `NFePHP\Gnre\Make`: monta o lote 2.00 com `tagGuia`, `tagEmitente`, `tagItem`, `tagReferencia`,
  `tagItemValor`, `tagDestinatario` e `tagCampoExtra`, que podem ser chamadas em qualquer ordem
  (o XML sai na sequência do XSD); suporta várias guias por lote e vários itens por guia.
- `NFePHP\Gnre\MakeConsulta`: monta o lote de consultas de guia (7 tipos de consulta).
- `NFePHP\Gnre\Common\RegrasUf`: sugestões de tipo de documento de origem por UF (tabela da 0.x).
- `NFePHP\Gnre\MakeV1`: guias no layout 1.00 (campos `c01_..c42_`) em dois formatos:
  a guia 1.00 dentro do lote 2.00 (`FORMATO_LOTE_200`, padrão, aceito pelo pacote oficial 2.17) e o
  lote 1.00 puro (`FORMATO_LOTE_100`, como na 0.x).
- `NFePHP\Gnre\Common\Standardize`: converte os retornos (envelope SOAP com prefixo `ns1:`) em stdClass/array/JSON
  **preservando os atributos**: `<valor tipo="12">` vira `{attributes: {tipo: "12"}, value: ...}`.
- `NFePHP\Gnre\Common\RetornoTxtV1`: lê o resultado TXT posicional do lote 1.00 puro.
- `NFePHP\Gnre\Factories\FromNFe`: dados da guia a partir do XML da NF-e e atalhos `guiaDifal()`/`guiaSt()`.
- Exemplos em `examples/` (`make-lote.php`, `make-lote-v1.php`, `standardize-retorno.php`,
  `guia-difal-da-nfe.php` rodam sem certificado; `dagnre.php` exige o sped-da).

### Fixed
- Campos que a 0.x descartava no lote 2.00 (`detalhamentoReceita`, `produto`, IE do emitente e `convenio`) agora são enviados.
- O valor principal não é mais duplicado como `tipo=11` e `tipo=21`; só os valores informados vão no XML.
- `identificadorGuia` e `referencia/periodo` deixam de ser fixos (`1` e `0`).
- No layout 1.00, o período `0` (mensal) não é mais descartado.
- Leitura do resultado TXT do lote 1.00 com as posições corretas (a 0.x tinha campos deslocados e sobrepostos).
- O código de barras da guia passa a ser 2 de 5 intercalado (padrão Febraban de arrecadação; a 0.x usava Code128), agora no sped-da.

### Changed
- Namespace `Sped\Gnre` substituído por `NFePHP\Gnre` (pasta `src/`).
- Requer PHP >= 7.4 (testado de 7.4 a 8.5) e `nfephp-org/sped-common` ^5.1.
- Schemas atualizados para o pacote oficial do Manual de Integração GNRE v2.17 (`schemes/PL_GNRE_200`).
- Licença alinhada ao ecossistema NFePHP: LGPL-3.0-or-later, GPL-3.0-or-later ou MIT.
- CI migrada do Travis para o GitHub Actions.

### Removed
- Impressão da guia (Smarty + Dompdf): o PDF passa a ser gerado pelo `nfephp-org/sped-da` (`NFePHP\DA\GNRE\Dagnre`),
  sugerido em `suggest`; o sped-gnre não depende mais de bibliotecas de PDF ou código de barras.
- Dependências `smarty/smarty` (5 vulnerabilidades conhecidas na 3.1.46), `dompdf/dompdf`, `laminas/*`, `sabberworm/php-css-parser`, `nfephp-org/sped-nfe` e o uso não declarado de `guzzlehttp/guzzle`.
- Código 0.x (`Sped\Gnre`). Guia de migração em `docs/MIGRACAO.md`.
