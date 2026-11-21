# sped-gnre 1.0 — Fase F5: `Standardize`, retorno TXT do 1.00 e `Factories\FromNFe`

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transformar os retornos do Portal GNRE (SOAP com prefixos `ns1:`) em `stdClass`/array/JSON **sem perder atributos**, ler o resultado TXT posicional do lote 1.00 puro e gerar guias de DIFAL e ST a partir do XML de uma NF-e autorizada.

**Architecture:**
- `Common\Standardize` segue a API do sped-mdfe/sped-nfe (`whichIs`, `toStd`, `toArray`, `toJson`, `__toString`), mas converte com um conversor DOM próprio, porque o `json_encode` do SimpleXML perde atributos de elementos com texto (D-17).
- `Common\RetornoTxtV1` lê o TXT do resultado do lote 1.00 com as posições do ACBr (`PCNGNRE/pgnreRetConsResLoteGNRE.pas`).
- `Factories\FromNFe` lê a NF-e e devolve `stdClass` prontos para o `Make`, além de dois atalhos (`guiaDifal`, `guiaSt`) que devolvem um `Make` já preenchido.

**Tech Stack:** igual às fases anteriores.

**Spec:** `docs/refatoracao/ROADMAP.md` (contratos `Standardize`; D-04, D-16, D-17). Fontes: XSDs de retorno em `schemes/PL_GNRE_200`; exemplo de retorno do ACBr `trunk2/Testes/Recursos/GNRe/GuiaGNRe_Editada_Adicionando_Valores_FCP.xml`; `legacy/exemplos/xml/resultado-consulta-gnre.xml` (TXT 1.00); `legacy/lib/Sped/Gnre/Helper/GnreHelper.php`; NF-e `D:/NfePhpDev/sped-nfe/tests/fixtures/xml/nfe_4.0.xml`.

## Global Constraints

- Tudo das F0–F4 continua valendo.
- **Conversão DOM → stdClass (D-17):**
  - elemento só com texto → `string`;
  - elemento com filhos → `stdClass`;
  - elemento com atributos → propriedade `attributes` (stdClass) **e**, se houver texto, propriedade `value`;
  - elementos irmãos com o mesmo nome → `array`;
  - nomes sempre **sem prefixo** de namespace;
  - declarações `xmlns` não viram atributo.
- Raízes reconhecidas pelo `whichIs`, nesta ordem: `TRetLote_GNRE`, `TResultLote_GNRE`, `TConfigUf`, `TRetorno_GNRE`, `TLote_GNRE`, `TLote_ConsultaGNRE`, `TConsLote_GNRE`, `TConsultaConfigUf`. A busca usa o nome local e é **agnóstica a namespace e prefixo**: o nó é encontrado dentro do envelope SOAP e de `gnreRespostaMsg`.
- Posições do TXT 1.00 (base 1, `[início, tamanho]`, tirados do ACBr):
  - header (`0`): `tipoIdentificadorSolicitante` [2,1], `identificadorSolicitante` [3,14], `numeroProtocoloLote` [17,10], `ambiente` [27,1];
  - guia (`1`): `sequencialGuia` [2,4], `situacaoGuia` [6,1], `ufFavorecida` [7,2], `receita` [9,6], `tipoDocEmitente` [15,1], `docEmitente` [16,16], `razaoSocialEmitente` [32,60], `enderecoEmitente` [92,60], `municipioEmitente` [152,50], `ufEmitente` [202,2], `cepEmitente` [204,8], `telefoneEmitente` [212,11], `tipoDocDestinatario` [223,1], `docDestinatario` [224,16], `municipioDestinatario` [240,50], `produto` [290,255], `docOrigem` [545,18], `convenio` [563,30], `informacoesComplementares` [593,300], `dataVencimento` [893,8], `dataLimitePagamento` [901,8], `periodoReferencia` [909,1], `mesAnoReferencia` [910,6], `parcela` [916,3], `valorPrincipal` [919,15], `atualizacaoMonetaria` [934,15], `juros` [949,15], `multa` [964,15], `representacaoNumerica` [979,48], `codigoBarras` [1027,44], `quantidadeVias` [1071,1], `numeroControle` [1072,16], `identificadorGuia` [1088,10], `guiaGeradaContingencia` [1098,1];
  - rejeição (`2`): `sequencialGuia` [2,4], `nomeCampo` [6,30], `codigoMotivo` [36,3], `descricaoMotivo` [39,355];
  - rodapé (`9`): linha inteira em `conteudo`.
  - Os valores (posições 919, 934, 949 e 964) vêm em centavos e são devolvidos como string `'0.00'`. Os demais campos passam por `trim`.
- `FromNFe`: o código do município é o `cMun` do IBGE **sem os 2 dígitos da UF** (`substr(cMun, 2)`). O endereço do emitente é `"{xLgr}, {nro} - {xBairro}"`, limitado a 60 caracteres. Fontes dos valores no `ICMSTot`: DIFAL = `vICMSUFDest` (tipo 11) e `vFCPUFDest` (tipo 12, se > 0); ST = `vST` (tipo 11) e `vFCPST` (tipo 12, se > 0).

## Review Focus

- Retorno com **prefixo** `ns1:` e dentro do envelope SOAP → o `toStd` encontra o nó e as propriedades saem sem prefixo (T5.1).
- `<valor tipo="12">1.82</valor>` → no `toStd` fica `{attributes: {tipo: '12'}, value: '1.82'}`, nunca só `'1.82'` (T5.1).
- Uma guia só no resultado → `resultado->guia` é `stdClass`; duas ou mais → `array` (é o mesmo comportamento do ecossistema, documentado no docblock) (T5.1).
- TXT com quebras CRLF, LF ou CR, linhas com indentação e linhas em branco → o mesmo resultado (T5.2).
- NF-e sem `protNFe` (não autorizada) → a chave vem do `Id` do `infNFe` (sem o prefixo `NFe`); XML que não é NF-e → `DocumentsException` (T5.3).

---

### Task T5.1: `Common\Standardize` (D-17)

**Files:**
- Create: `src/Common/Standardize.php`
- Create: `tests/fixtures/xml/retorno-recibo.xml`, `tests/fixtures/xml/retorno-resultado-v2.xml`, `tests/fixtures/xml/retorno-config-uf.xml`
- Test: `tests/Common/StandardizeTest.php`

**Interfaces:**
- Produces: `NFePHP\Gnre\Common\Standardize` com:
  - `public $node = ''`, `public $json = ''`, `public $key = ''`;
  - `__construct(?string $xml = null)`, `whichIs(string $xml): string`, `toStd(?string $xml = null): stdClass`, `toArray(?string $xml = null): array`, `toJson(?string $xml = null): string`, `__toString(): string` (o XML do nó, já sem prefixos).
  - String que não é XML → `DocumentsException::wrongDocument(6)`; XML sem raiz conhecida → `wrongDocument(7)`.

Fixtures (todas dentro de envelope SOAP 1.2 + `<gnreRespostaMsg xmlns="http://www.gnre.pe.gov.br/webservice/...">`, com os dados prefixados `ns1:` e `xmlns:ns1="http://www.gnre.pe.gov.br"`, como nos retornos reais):
- `retorno-recibo.xml`: `TRetLote_GNRE` com `ambiente` 2, `situacaoRecepcao` (`codigo` 100, `descricao` `Lote recebido com sucesso`) e `recibo` (`numero` `2512345678`, `dataHoraRecibo` `2026-11-22 10:00:00`, `tempoEstimadoProc` `500`), conforme `lote_gnre_recibo_v1.00.xsd`.
- `retorno-resultado-v2.xml`: o `TResultLote_GNRE` do exemplo do ACBr com as **duas** guias, cópia sem alteração e com a origem citada num comentário XML.
- `retorno-config-uf.xml`: `TConfigUf` mínimo válido no `config_uf_v1.00.xsd`, montado a partir do XSD.
- Cada fixture tem o nó de dados validado no seu XSD dentro do próprio teste (`testFixturesValidas`).

- [x] **Step 1: Escrever as fixtures e `tests/Common/StandardizeTest.php`:**
  - `testFixturesValidas` (provider fixture → raiz → XSD): o nó, extraído sem o envelope, valida no XSD de `PL_GNRE_200`.
  - `testWhichIs`: recibo → `TRetLote_GNRE`; resultado → `TResultLote_GNRE`; config → `TConfigUf`; lote do `Make` → `TLote_GNRE`.
  - `testRecibo`: `toStd()->recibo->numero === '2512345678'` e `situacaoRecepcao->codigo === '100'`.
  - `testResultadoDuasGuias`: `resultado->guia` é array de 2; `guia[1]->linhaDigitavel === '858100000013822599992000280100562200000000000000'`; `guia[0]->attributes->versao === '2.00'`.
  - `testAtributoComTexto`: no item da 2ª guia, `valor` é array de 7, `valor[1]->attributes->tipo === '12'` e `valor[1]->value === '1.82'`; `documentoOrigem->attributes->tipo === '22'`.
  - `testUmaGuiaViraObjeto`: resultado com uma guia (a fixture com a 2ª guia removida via DOM) → `resultado->guia` é `stdClass`.
  - `testToArrayEToJson`: `toArray()['recibo']['numero'] === '2512345678'`; o `toJson()` decodificado é igual ao `toStd()`.
  - `testToStringSemPrefixo`: `(string) $std` não contém `ns1:`.
  - `testNaoXml` (`'abc'`) e `testRaizDesconhecida` (`'<foo/>'`) → `DocumentsException`.
- [x] **Step 2: Rodar e ver falhar** → `Class "NFePHP\Gnre\Common\Standardize" not found`.
- [x] **Step 3: Implementar** com um conversor recursivo `protected function converter(DOMElement $no)`, que devolve `stdClass` ou `string` conforme as regras D-17, e uma cópia sem prefixos para `$node`.
- [x] **Step 4: Rodar** — `composer phpcs && composer stan && composer test` → verde.
- [x] **Step 5: Commit** `feat(common): Standardize sem perda de atributos (D-17)`

### Task T5.2: `Common\RetornoTxtV1` (resultado do lote 1.00 puro)

**Files:**
- Create: `src/Common/RetornoTxtV1.php`, `tests/fixtures/xml/retorno-resultado-v1.xml` (cópia do `legacy/exemplos/xml/resultado-consulta-gnre.xml`, que sai do repositório na F7)
- Test: `tests/Common/RetornoTxtV1Test.php`

**Interfaces:**
- Produces: `NFePHP\Gnre\Common\RetornoTxtV1::ler(string $txt): stdClass` → `{cabecalho: ?stdClass, guias: stdClass[], rejeicoes: stdClass[], rodape: ?stdClass}`, com os nomes de campos das Global Constraints. As rejeições também ficam ligadas à guia: `guia->rejeicoes` recebe as linhas `2` com o mesmo `sequencialGuia`.
- Uso: `(new Standardize($retorno))->toStd()->resultado` (string) → `RetornoTxtV1::ler(...)`.

- [x] **Step 1: Escrever `tests/Common/RetornoTxtV1Test.php`** com o `resultado` da fixture (via `Standardize`). Valores esperados, já conferidos com as posições do ACBr:
  - `cabecalho`: `numeroProtocoloLote` `0000070008`, `ambiente` `0`;
  - `guias[0]`: `sequencialGuia` `0001`, `situacaoGuia` `1`, `ufFavorecida` `AC`, `receita` `123456`, `razaoSocialEmitente` `TESTE RAZAO SOCIAL`, `ufEmitente` `SP`, `dataVencimento` `10102014`, `periodoReferencia` `3`, `mesAnoReferencia` `112014`, `parcela` `005`, `valorPrincipal` `0.00`, `quantidadeVias` `3`;
  - `rejeicoes[0]`: `sequencialGuia` `0001`, `nomeCampo` `c02_receita`, `codigoMotivo` `205`, `descricaoMotivo` `A UF favorecida nao gera GNRE para a Receita informada.`;
  - `guias[0]->rejeicoes` tem 1 item;
  - `rodape->conteudo` começa com `914066705180001`.
  - `testValorEmCentavos`: linha `1` sintética com `000000000012345` na posição 919 → `valorPrincipal === '123.45'`.
  - `testQuebrasDeLinha`: o mesmo TXT com `\r\n`, `\r` e linhas em branco extras → resultado igual.
  - `testTxtVazio` → `guias` e `rejeicoes` vazios, `cabecalho` null.
- [x] **Step 2: Rodar e ver falhar** → classe inexistente.
- [x] **Step 3: Implementar.** As posições ficam em constantes (arrays `campo => [inicio, tamanho]`), lidas com `substr($linha, $inicio - 1, $tamanho)`. Cada linha passa por `ltrim` dos espaços de indentação **antes** de ler o identificador (no exemplo, as linhas vêm indentadas dentro do XML).
- [x] **Step 4: Rodar** → verde.
- [x] **Step 5: Registrar no PROGRESSO** (divergências): o `Parser/SefazRetorno` da 0.x tinha posições deslocadas e sobrepostas (ex.: `dataLimitePagamento` 900/8 e `periodo` 908/909); a 1.0 usa as posições do ACBr.
- [x] **Step 6: Commit** `feat(common): leitor do resultado TXT do lote 1.00`

### Task T5.3: `Factories\FromNFe`

**Files:**
- Create: `src/Factories/FromNFe.php`
- Create: `tests/fixtures/xml/nfe-difal.xml`. É uma cópia de `D:/NfePhpDev/sped-nfe/tests/fixtures/xml/nfe_4.0.xml` com o CPF do destinatário trocado de `014345733812` (12 dígitos, inválido) para `90483926086`. A origem e a alteração ficam registradas em `tests/fixtures/xml/README.md`.
- Test: `tests/Factories/FromNFeTest.php`

**Interfaces:**
- Produces: `NFePHP\Gnre\Factories\FromNFe` com:
  - `__construct(string $xmlNFe)`: aceita `nfeProc` ou `NFe`; outro documento → `DocumentsException::wrongDocument(1)`;
  - `emitente(): stdClass` (campos do `tagEmitente`);
  - `destinatario(): stdClass` (campos do `tagDestinatario`);
  - `ufDestino(): string`;
  - `chave(): string` (`protNFe/infProt/chNFe` ou, sem protocolo, o `Id` sem o prefixo `NFe`);
  - `numero(): string` (`nNF`);
  - `valores(): stdClass` (`vNF`, `vICMSUFDest`, `vFCPUFDest`, `vST`, `vFCPST`, todos string com 2 casas);
  - `documentoOrigem(bool $difal = false): stdClass` → `{documentoOrigem, documentoOrigemTipo}`, usando `RegrasUf::tipoChaveDoc(ufDestino)` (chave ou número) e `RegrasUf::tipoDocOrigem(ufDestino, $difal)`;
  - `guiaDifal(stdClass $opcoes): Make` e `guiaSt(stdClass $opcoes): Make`. `$opcoes` tem `receita` (obrig.), `dataVencimento` (obrig.), `dataPagamento` (padrão = `dataVencimento`), `identificadorGuia`, `detalhamentoReceita` e `produto`.
  - Os atalhos devolvem um `Make` com 1 guia (UF favorecida = UF de destino; `valorGNRE` = soma dos valores), o emitente, 1 item (receita, documento de origem, vencimento, valores) e o destinatário. O usuário ainda pode chamar `tagCampoExtra`/`tagReferencia` no `Make` devolvido.
  - Valor zero na fonte (sem DIFAL ou sem ST) → `InvalidArgumentException` ("a NF-e não tem valor de ... a recolher").

- [x] **Step 1: Escrever `tests/Factories/FromNFeTest.php`** (estende `MakeTestCase`):
  - `testEmitente`: `CNPJ` `07670813000823`, `IE` `013581225`, `razaoSocial` `SEILA COMERCIO ELETRONICO S.A`, `endereco` `ROD BR-132, 1000 - VILA AMARO`, `municipio` `05101`, `uf` `ES`, `cep` `29143080`, `telefone` `195552323`.
  - `testDestinatarioCpf`: `CPF` `90483926086`, sem `CNPJ`, `razaoSocial` `CICLANO DA SILVA`, `municipio` `50308`; `ufDestino()` `SP`.
  - `testChaveENumero`: `chave()` `32191107670813000825550010059062251133037938`; `numero()` `5906225`; sem `protNFe` (removido via DOM) a chave continua a mesma, vinda do `Id`.
  - `testValores`: `vICMSUFDest` `2.45`, `vFCPUFDest` `0.00`, `vST` `0.00`.
  - `testDocumentoOrigemSp`: SP → `documentoOrigem` `5906225` (número), tipo `10`.
  - `testGuiaDifal`: `guiaDifal({receita: '100102', dataVencimento: '2026-12-15'})->getXML()` passa no `valida()` e contém `<ufFavorecida>SP</ufFavorecida>`, `<valor tipo="11">2.45</valor>`, `<valorGNRE>2.45</valorGNRE>` e `<documentoOrigem tipo="10">5906225</documentoOrigem>`, sem `tipo="12"`.
  - `testGuiaDifalComFcp`: fixture com `vFCPUFDest` = `0.50` (alterado via DOM) → contém `<valor tipo="12">0.50</valor>` e `<valorGNRE>2.95</valorGNRE>`.
  - `testGuiaStSemSt` → `InvalidArgumentException`; `testGuiaSt`: `vST` = `10.00` (via DOM) → `<valor tipo="11">10.00</valor>`.
  - `testUfComChave`: `dest/enderDest/UF` = `RJ` (via DOM) → `documentoOrigem` é a chave de 44 dígitos e o tipo é `24`.
  - `testNaoENfe`: `'<cteProc/>'` → `DocumentsException`.
  - `testOpcoesObrigatorias`: `guiaDifal({})` → `InvalidArgumentException`.
- [x] **Step 2: Rodar e ver falhar** → classe inexistente.
- [x] **Step 3: Implementar** com `DOMDocument` + `getElementsByTagName` (o namespace da NF-e é fixo).
- [x] **Step 4: Rodar** → verde.
- [x] **Step 5: Commit** `feat: Factories\FromNFe (guias de DIFAL e ST a partir da NF-e)`

### Task T5.4: Exemplos, documentação e fechamento da F5

**Files:**
- Create: `examples/standardize-retorno.php` (lê a fixture do resultado v2 e mostra o `toJson`; sem rede), `examples/guia-difal-da-nfe.php` (monta a guia de DIFAL a partir da `tests/fixtures/xml/nfe-difal.xml` e imprime o XML; sem certificado)
- Modify: `CHANGELOG.md`, `docs/refatoracao/ROADMAP.md` (D-17 e contrato do `Standardize`), `docs/refatoracao/PROGRESSO.md`

- [ ] **Step 1: Exemplos** + `php -l` + execução dos dois (saída não vazia; o XML do segundo passa no XSD).
- [ ] **Step 2: CHANGELOG** (`### Added`): `Standardize` (com atributos preservados), `RetornoTxtV1` e `FromNFe`.
- [ ] **Step 3: Push e CI** → 7 jobs `success`.
- [ ] **Step 4: Fechar a fase**: F5 ✅; a próxima ação passa a ser escrever o plano da F6 (DA). Registrar para a F6 que o `TResultLote_GNRE` 2.00 traz, por guia, `linhaDigitavel`, `codigoBarras`, `nossoNumero` e `dataLimitePagamento` (a entrada natural do `Dagnre`), e que o portal pode devolver o PDF pronto (`incluirPDFGuias`). Commit `docs: conclui F5`.
