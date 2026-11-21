# sped-gnre 1.0 — Fase F6: impressão da guia (`DA\Dagnre`, padrão sped-da)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Gerar o PDF da GNRE (3 vias por guia), no padrão dos documentos auxiliares do sped-da, a partir do resultado do lote, com código de barras de arrecadação e QR Code PIX quando houver.

**Architecture:**
- `DA\DadosGuia` normaliza as três origens possíveis num `stdClass` plano de impressão: a guia 2.00 do `TResultLote_GNRE`, a guia 1.00 desse mesmo resultado e a guia do TXT do 1.00 puro. Assim o layout não conhece as diferenças de layout da SEFAZ.
- `DA\Dagnre extends NFePHP\DA\Common\DaCommon` (D-03) recebe o XML do resultado, imprime só as guias processadas com sucesso e usa `NFePHP\DA\Legacy\Pdf` (FPDF) e o `Com\Tecnick\Barcode\Barcode`, como o `Damdfe`.
- Quando migrar para o sped-da (F8), a classe vira `NFePHP\DA\GNRE\Dagnre`, e o `DadosGuia` vai junto.

**Tech Stack:** `nfephp-org/sped-da` ^1.1 (traz o `tecnickcom/tc-lib-barcode`), `ext-gd`; em dev, `smalot/pdfparser` ^2.12 (o mesmo do sped-da) para ler o texto do PDF nos testes.

**Spec:** `docs/refatoracao/ROADMAP.md`, D-03 e D-09, mais este plano. Fontes:
- `schemes/PL_GNRE_200/lote_gnre_result_v2.00.xsd` (guia: `situacaoGuia`, grupos 1.00/2.00, `dataLimitePagamento`, `informacoesComplementares/informacao` 1–3, `nossoNumero`, `linhaDigitavel`, `codigoBarras`, `qrcodePayload`, `motivosRejeicao`);
- `D:/betoeu/sped-da/src/Common/DaCommon.php` e `src/MDFe/Damdfe.php`;
- o layout do ACBr `GNRE/Fortes/ACBrGNReGuiaRLRetrato.dfm`;
- o `legacy/templates/gnre.tpl`.

## Global Constraints

- Tudo das F0–F5 continua valendo.
- **Código de barras:** padrão Febraban de arrecadação, 44 dígitos, **2 de 5 intercalado** (`I25` no tc-lib-barcode). O ACBr usa `TRLBarcode` com `Ratio = 3`; o legado usava Code128, o que é registrado como divergência.
- **Linha digitável:** 48 dígitos exibidos em 4 blocos de 11 dígitos + DV, no formato `85850000002-9 43009999200-6 28010096220-0 00000000000-0` (o DV é o 12º dígito de cada bloco).
- **Campos de impressão** (`DadosGuia`, todos string; `''` quando ausente):
  - identificação da guia: `versao`, `situacaoGuia`, `identificadorGuia`, `ufFavorecida`, `receita`, `detalhamentoReceita`, `produto`, `documentoOrigem`, `convenio`;
  - emitente: `emitenteDocumento` (CNPJ/CPF formatado), `emitenteIE`, `emitenteRazaoSocial`, `emitenteEndereco`, `emitenteMunicipio`, `emitenteUF`, `emitenteCEP`, `emitenteTelefone`;
  - destinatário: `destinatarioDocumento`, `destinatarioIE`, `destinatarioRazaoSocial`, `destinatarioMunicipio`;
  - referência: `periodoReferencia` (`MM/AAAA`), `parcela`;
  - datas: `dataVencimento` e `dataLimitePagamento` (`DD/MM/AAAA`);
  - valores: `valorPrincipal`, `atualizacaoMonetaria`, `juros`, `multa`, `valorTotal` (`'1.234,56'`, ou `''` se zero);
  - pagamento: `informacoesComplementares` (linhas unidas com `"\n"`), `numeroControle` (`nossoNumero`), `linhaDigitavel` (só dígitos), `codigoBarras` (só dígitos), `qrcodePayload`.
- **Valores da guia 2.00:** soma de todos os itens; principal = tipos 11 + 12; atualização = 51 + 52; juros = 41 + 42; multa = 31 + 32; total = `valorGNRE`. Receita, documento de origem, vencimento e referência vêm do **primeiro** item.
- **Valores da guia 1.00 no resultado:** `c06_valorPrincipal`, `c07_atualizacaoMonetaria`, `c08_juros`, `c09_multa`, `c10_valorTotal`, `c29_dataLimitePagamento`, `c23_informacoes`, `c30_nossoNumero`.
- **Página:** A4 retrato, uma guia por página, 3 vias empilhadas (`1ª via - Banco`, `2ª via - Contribuinte`, `3ª via - Contribuinte/Fisco`) separadas por linha tracejada de corte. O rodapé traz os créditos do integrador e o "Powered by NFePHP", como no `Damdfe`.
- **Só são impressas as guias com `situacaoGuia` = `0`.** As demais ficam em `getGuiasNaoImpressas()`, com os motivos de rejeição.

## Review Focus

- Resultado só com guias rejeitadas, ou com `situacaoProcess` de erro → `DocumentsException` clara ("nenhuma guia processada com sucesso"), sem PDF em branco (T6.3).
- `codigoBarras` com tamanho diferente de 44 dígitos ou ausente → a guia sai com aviso no lugar do código de barras, nunca com um código de barras inválido impresso (T6.3).
- Razão social e informações complementares longas (60 e 3×100 caracteres) → o texto cabe na caixa (reduz a fonte ou quebra a linha) e não invade outro campo (T6.3).
- Resultado com várias guias → uma página por guia, cada uma com o seu código de barras (T6.3).
- Valores com separador de milhar (`1234.5`) → `1.234,50` (T6.2).

---

### Task T6.1: Dependências (sped-da, gd, pdfparser)

**Files:**
- Modify: `composer.json`, `.github/workflows/ci.yml` (a extensão `gd` já está), `README.md` (requisitos)

- [x] **Step 1:** acrescentar `"nfephp-org/sped-da": "^1.1"` e `"ext-gd": "*"` no `require`, e `"smalot/pdfparser": "^2.12"` no `require-dev`.
- [x] **Step 2:** `composer validate --strict && composer update --no-interaction` → sem erro; conferir com `composer show nfephp-org/sped-da tecnickcom/tc-lib-barcode smalot/pdfparser` que os três estão instalados.
- [x] **Step 3:** smoke test `tests/DA/DependenciasTest.php`: `testI25`, `(new Com\Tecnick\Barcode\Barcode())->getBarcodeObj('I25', str_repeat('8', 44), -1, -12, 'black')->getPngData()` começa com `"\x89PNG"`; `testPdf`, `new NFePHP\DA\Legacy\Pdf('P', 'mm', 'A4')` gera uma string que começa com `%PDF`.
- [x] **Step 4:** a cadeia `composer phpcs && composer stan && composer test` → verde.
- [x] **Step 5:** push e CI das 7 versões verdes **antes** de seguir (para descobrir cedo uma incompatibilidade do sped-da com o 7.4 ou o 8.5).
- [x] **Step 6: Commit** `build: sped-da (DA da guia) e pdfparser em dev`

### Task T6.2: `DA\DadosGuia` (normalização para impressão)

**Files:**
- Create: `src/DA/DadosGuia.php`
- Test: `tests/DA/DadosGuiaTest.php`

**Interfaces:**
- Produces: `NFePHP\Gnre\DA\DadosGuia` com:
  - `deResultado(stdClass $guia): stdClass`, onde `$guia` é um elemento de `toStd()->resultado->guia`, versão 2.00 ou 1.00, decidida por `attributes->versao`;
  - `deTxtV1(stdClass $guia): stdClass`, onde `$guia` é um item de `RetornoTxtV1::ler()->guias`;
  - `formatarLinhaDigitavel(string $linha): string`, `formatarDocumento(string $doc): string` (CNPJ `00.000.000/0000-00`, CPF `000.000.000-00`, demais sem alteração) e `formatarValor(string $valor): string`.
- Todos são métodos estáticos e devolvem os campos listados nas Global Constraints.

- [ ] **Step 1: Escrever `tests/DA/DadosGuiaTest.php`** com a fixture `retorno-resultado-v2.xml` (via `Standardize`):
  - `testGuia200Simples` (1ª guia): `ufFavorecida` RS, `receita` 100099, `documentoOrigem` `35200111111111111111550400000096111125788316`, `emitenteDocumento` `11.111.111/1111-11`, `emitenteRazaoSocial` `TESTES NF-E 4.00`, `destinatarioIE` `3333333333`, `dataVencimento` `28/01/2020`, `dataLimitePagamento` `28/01/2020`, `valorPrincipal` `243,00`, `valorTotal` `243,00`, `numeroControle` `0096220013500000`, `linhaDigitavel` `858500000029430099992006280100962200000000000000`, `codigoBarras` `85850000002430099992002801000000000000000000`.
  - `testGuia200ComFcp` (2ª guia): `valorPrincipal` `184,07` (182.25 + 1.82); `atualizacaoMonetaria` `8,00` (52); `juros` `5,11` (42); `multa` `5,11` (32); `valorTotal` `182,25` (`valorGNRE`).
  - `testGuia100NoResultado`: um `stdClass` montado no teste com `attributes->versao` = `1.00` e `c01_UfFavorecida` SP, `c02_receita` 100099, `c06_valorPrincipal` 10.00, `c07_atualizacaoMonetaria` 1.00, `c08_juros` 0.50, `c09_multa` 0.20, `c10_valorTotal` 11.70, `c14_dataVencimento` 2026-12-15, `c29_dataLimitePagamento` 2026-12-20, `c30_nossoNumero` 123, `c16_razaoSocialEmitente` X, `c03_idContribuinteEmitente` {CNPJ 06157250000116}, `c05_referencia` {mes 11, ano 2026}, `linhaDigitavel` e `codigoBarras` → campos correspondentes, `periodoReferencia` `11/2026`.
  - `testDeTxtV1`: guia da fixture TXT (`RetornoTxtV1`) → `ufFavorecida` AC, `receita` 123456, `dataVencimento` `10/10/2014`, `periodoReferencia` `11/2014`, `parcela` `005`.
  - `testFormatarLinhaDigitavel`: `858500000029430099992006280100962200000000000000` → `85850000002-9 43009999200-6 28010096220-0 00000000000-0`; tamanho diferente de 48 → devolve sem formatar.
  - `testFormatarValor`: `1234.5` → `1.234,50`; `0` → `''`; `''` → `''`.
  - `testInformacoesComplementares`: guia 2.00 com `informacoesComplementares->informacao` = `['A', 'B']` → `"A\nB"`; com uma só (string) → `'A'`.
- [ ] **Step 2: Rodar e ver falhar** → classe inexistente.
- [ ] **Step 3: Implementar.** Antes de mapear, `valor`/`item` são normalizados para array (lembrando que um único elemento vem como objeto). Os valores com atributo vêm como `{attributes, value}` (D-17).
- [ ] **Step 4:** a cadeia de verificação → verde.
- [ ] **Step 5: Commit** `feat(da): DadosGuia (normalizacao das guias para impressao)`

### Task T6.3: `DA\Dagnre`

**Files:**
- Create: `src/DA/Dagnre.php`
- Test: `tests/DA/DagnreTest.php`

**Interfaces:**
- Produces: `NFePHP\Gnre\DA\Dagnre extends NFePHP\DA\Common\DaCommon` com:
  - `__construct(string $xmlResultado)`: aceita o retorno SOAP completo ou só o `TResultLote_GNRE`. O `resultado` em TXT (1.00 puro) é lido pelo `RetornoTxtV1`. Documento que não é resultado → `DocumentsException::wrongDocument(1)`; nenhuma guia com situação `0` → `DocumentsException::wrongDocument(4, '...nenhuma guia processada com sucesso')`.
  - `render($logo = ''): string`, herdado (PDF bruto);
  - `getGuias(): array` (as guias impressas, no formato do `DadosGuia`) e `getGuiasNaoImpressas(): array` (com `situacaoGuia` e `motivosRejeicao`);
  - `protected monta($logo = '')`.
- O layout de cada via (altura ~95 mm, largura útil = `wPrint`) tem:
  - cabeçalho com a logo (opcional, `logoParameters`), o título `Guia Nacional de Recolhimento de Tributos Estaduais - GNRE` e o rótulo da via;
  - à esquerda, os blocos `Dados do Contribuinte Emitente`, `Dados do Destinatário` e `Informações à Fiscalização`, este com convênio, produto, informações complementares e a frase `Documento válido para pagamento até {dataLimitePagamento}`;
  - à direita, uma coluna com UF Favorecida, Código da Receita, Nº de Controle, Data de Vencimento, Nº do Documento de Origem, Período de Referência, Nº Parcela, Valor Principal, Atualização Monetária, Juros, Multa e Total a Recolher;
  - embaixo, a linha digitável formatada, o código de barras I25 (≈103 × 13 mm) e, se houver `qrcodePayload`, o QR Code PIX (`QRCODE,M`, ≈25 mm) com a legenda `Pagamento via PIX`.
  - Os textos usam `textBox` com fonte reduzida quando não cabem (`cellFitScale` ou `getNumLines`).

- [ ] **Step 1: Escrever `tests/DA/DagnreTest.php`** (helper `texto(string $pdf): string` com o `Smalot\PdfParser\Parser`):
  - `testPdfDoResultadoV2`: a fixture `retorno-resultado-v2.xml` gera um PDF que começa com `%PDF`, tem **2 páginas** e cujo texto contém `GNRE`, `1ª via - Banco`, `3ª via - Contribuinte/Fisco`, `TESTES NF-E 4.00`, `85850000002-9 43009999200-6 28010096220-0 00000000000-0` e `243,00`.
  - `testCodigoBarrasEhI25`: subclasse de teste que intercepta o desenho do código de barras (método `protected codigoBarras(string $codigo, ...)`) e registra o tipo e o código → `I25` e `85850000002430099992002801000000000000000000`.
  - `testQrCodePix`: fixture com `<qrcodePayload>00020126...</qrcodePayload>` acrescentado via DOM na 1ª guia → o texto contém `Pagamento via PIX`; a 2ª guia (sem payload) não contém.
  - `testSomenteRejeitadas`: as duas `situacaoGuia` trocadas para `1` → `DocumentsException`, cuja mensagem contém `nenhuma guia`.
  - `testGuiasNaoImpressas`: a 2ª guia com `situacaoGuia` `2` → o PDF tem 1 página e `getGuiasNaoImpressas()` tem 1 item.
  - `testCodigoBarrasInvalido`: `codigoBarras` com 10 dígitos → o PDF é gerado, o texto contém `Código de barras indisponível` e o interceptador não é chamado para essa guia.
  - `testResultadoV1Txt`: a fixture `retorno-resultado-v1.xml` tem a única guia com situação `1`, então dá `DocumentsException`. Com a situação trocada para `0` na linha TXT, gera 1 página que contém `AC`.
  - `testNaoEResultado`: o recibo (`retorno-recibo.xml`) → `DocumentsException`.
  - `testCreditos`: `creditsIntegratorFooter('MINHA EMPRESA')` → o texto contém `MINHA EMPRESA` e `Powered by NFePHP`.
- [ ] **Step 2: Rodar e ver falhar** → classe inexistente.
- [ ] **Step 3: Implementar** (`monta` → uma página por guia → 3 × `via($y, $guia, $rotulo)` + `rodape()`).
- [ ] **Step 4:** a cadeia de verificação → verde. Gerar o PDF da fixture em `scratchpad` e **conferir visualmente** (ler o PDF ou uma imagem dele), registrando no PROGRESSO o que foi conferido.
- [ ] **Step 5: Registrar no PROGRESSO** (divergências): o legado usava Code128 (fora do padrão Febraban de arrecadação), Smarty e Dompdf, não tinha PIX nem várias vias por página garantidas, e usava `templates/guia.tpl`, que não existe.
- [ ] **Step 6: Commit** `feat(da): Dagnre (PDF da GNRE em 3 vias, I25 e PIX)`

### Task T6.4: Exemplo, documentação e fechamento da F6

**Files:**
- Create: `examples/dagnre.php` (gera o PDF a partir da fixture do resultado v2, sem rede; com `php -d ... > guia.pdf` ou mostrado no navegador)
- Modify: `CHANGELOG.md`, `README.md` (seção "Impressão da guia", incluindo a alternativa `incluirPDFGuias`), `docs/refatoracao/ROADMAP.md`, `docs/refatoracao/PROGRESSO.md`

- [ ] **Step 1:** exemplo + `php -l` + execução (a saída começa com `%PDF`).
- [ ] **Step 2:** CHANGELOG (`### Added`: `DA\Dagnre` e `DA\DadosGuia`; `### Fixed`: código de barras I25); README.
- [ ] **Step 3:** push e CI → 7 jobs `success`.
- [ ] **Step 4: Fechar a fase**: F6 ✅; a próxima ação passa a ser escrever o plano da F7 (docs, limpeza, RC e homologação real). Commit `docs: conclui F6`.
