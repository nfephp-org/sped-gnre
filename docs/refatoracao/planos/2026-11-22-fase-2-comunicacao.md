# sped-gnre 1.0 — Fase F2: Comunicação com o Portal GNRE

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Entregar `Common\Webservices`, `Common\Tools` e `Tools`, cobrindo os **5 serviços** do Portal GNRE (lote, resultado do lote, config. UF, lote de consulta de guia e resultado da consulta) sobre o `NFePHP\Common\Soap\SoapCurl`, com os envelopes conferidos por teste.

**Architecture:** Igual ao sped-mdfe e ao sped-nfe:
- `Common\Tools` lê o config JSON, guarda o `Certificate`, resolve o serviço em `storage/wsgnre.json`, valida a mensagem no XSD e monta e envia o envelope (`gnreCabecMsg` + `gnreDadosMsg`).
- `Tools` tem um método `sefaz*` por serviço e devolve o retorno SOAP bruto (a padronização do retorno é a F5).
- Os testes trocam o `SoapCurl` por um `SoapFake` que grava os parâmetros de `send()`.

**Tech Stack:** PHP 7.4–8.5, nfephp-org/sped-common ^5.1 (`Certificate`, `Soap\SoapCurl`, `Soap\SoapBase`, `Validator`, `Strings`), PHPUnit 9.6.

**Spec:** `docs/refatoracao/ROADMAP.md` — contratos (seção "Contratos públicos") e decisões D-04, D-05, D-06, D-14 e D-15.

## Global Constraints

- Tudo das Global Constraints da F0/F1 continua valendo (PHP ≥ 7.4 sem sintaxe PHP 8, LF, PSR-12, phpstan nível 5, branch `refactor/v1`).
- Namespace de todo serviço: `http://www.gnre.pe.gov.br/webservice/<Servico>`, **igual nos dois ambientes**. É usado como xmlns do `gnreCabecMsg`, como xmlns do `gnreDadosMsg` e, entre aspas, como `action` (D-15).
- URLs: produção `https://www.gnre.pe.gov.br/gnreWS/services/<Servico>`; homologação `https://www.testegnre.pe.gov.br/gnreWS/services/<Servico>`.
- `tpAmb`: 1 = produção, 2 = homologação. `versao` ∈ {`1.00`, `2.00`}.
- Pasta de XSD por versão: `1.00` → `schemes/PL_GNRE_100/`; `2.00` → `schemes/PL_GNRE_200/`.
- Namespace dos dados: `http://www.gnre.pe.gov.br`.
- Certificado de teste: `tests/fixtures/certs/cert_cnpj_06157250000116_senha_minhasenha.pfx` (cópia do `D:/NfePhpDev/sped-nfe/tests/fixtures/certs/novo_cert_cnpj_06157250000116_senha_minhasenha.pfx`), senha `minhasenha`, CNPJ `06157250000116`.
- Nenhum teste faz rede: todo teste de `Tools` usa `tests/Common/SoapFake.php`.

| Serviço (`wsgnre.json`) | método SOAP | `versaoDados` | Mensagem (raiz) | XSD |
|---|---|---|---|---|
| `GnreLoteRecepcao` | `processar` | `versao` do config | `TLote_GNRE` | `lote_gnre_v{versao}.xsd` |
| `GnreResultadoLote` | `consultar` | `versao` do config | `TConsLote_GNRE` | `lote_gnre_consulta_v1.00.xsd` (pasta da versão) |
| `GnreConfigUF` | `consultar` | `versao` do config | `TConsultaConfigUf` | `consulta_config_uf_v1.00.xsd` (pasta da versão) |
| `GnreLoteRecepcaoConsulta` | `processar` | `2.00` fixo | `TLote_ConsultaGNRE` | `PL_GNRE_200/lote_consulta_guia_v2.00.xsd` |
| `GnreResultadoLoteConsulta` | `consultar` | `2.00` fixo | `TConsLote_GNRE` | `PL_GNRE_200/lote_gnre_consulta_v1.00.xsd` |

## Review Focus

- XML de entrada com declaração `<?xml ...?>` (o usuário passa o retorno de `getXML()` ou um arquivo) → a declaração é removida antes de envelopar; senão o body fica inválido (T2.3).
- XML inválido no XSD → `NFePHP\Common\Exception\ValidatorException` **antes** de qualquer envio (o `SoapFake` não registra chamada) (T2.3).
- Recibo vazio ou com espaços → `InvalidArgumentException` sem envio; o formato (10 ou 14 dígitos) fica com o XSD (T2.4).
- `versao` 1.00 no config e chamada aos serviços de consulta de guia → continuam usando a pasta `PL_GNRE_200` e `versaoDados` `2.00` (T2.5).
- Nenhum `loadSoapClass()` antes de um `sefaz*` → um `SoapCurl` é criado com o certificado (mesmo comportamento do `checkSoap()` do sped-mdfe) (T2.2).

---

### Task T2.1: `storage/wsgnre.json` e `Common\Webservices`

**Files:**
- Create: `storage/wsgnre.json`, `src/Common/Webservices.php`
- Test: `tests/Common/WebservicesTest.php`

**Interfaces:**
- Produces: `NFePHP\Gnre\Common\Webservices::__construct(string $json)`, `get(string $servico, int $tpAmb): \stdClass` com as propriedades `url`, `namespace`, `action`, `metodo` (string) e `versaoDados` (`?string`, null = usar a do config). Serviço ou ambiente desconhecido → `NFePHP\Gnre\Exception\RuntimeException`.

`storage/wsgnre.json` (conteúdo exato):
```json
{
    "namespace": "http://www.gnre.pe.gov.br/webservice/",
    "url": {
        "1": "https://www.gnre.pe.gov.br/gnreWS/services/",
        "2": "https://www.testegnre.pe.gov.br/gnreWS/services/"
    },
    "servicos": {
        "GnreLoteRecepcao": {"metodo": "processar", "versaoDados": null},
        "GnreResultadoLote": {"metodo": "consultar", "versaoDados": null},
        "GnreConfigUF": {"metodo": "consultar", "versaoDados": null},
        "GnreLoteRecepcaoConsulta": {"metodo": "processar", "versaoDados": "2.00"},
        "GnreResultadoLoteConsulta": {"metodo": "consultar", "versaoDados": "2.00"}
    }
}
```

- [ ] **Step 1: Escrever `tests/Common/WebservicesTest.php`** (carrega o `storage/wsgnre.json` real):
  - `testLoteRecepcaoProducao`: `get('GnreLoteRecepcao', 1)` → `url` `https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao`, `namespace` `http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao`, `action` `"http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao"` (com as aspas), `metodo` `processar`, `versaoDados` null.
  - `testHomologacaoMudaSoUrl`: `get('GnreResultadoLote', 2)` → `url` `https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote` e `namespace` `http://www.gnre.pe.gov.br/webservice/GnreResultadoLote` (sem `teste`).
  - `testConsultaGuiaTemVersaoFixa`: `get('GnreLoteRecepcaoConsulta', 2)->versaoDados === '2.00'` e `get('GnreResultadoLoteConsulta', 1)->metodo === 'consultar'`.
  - `testServicoDesconhecido`: `expectException(RuntimeException::class)` em `get('Xpto', 1)`.
  - `testAmbienteInvalido`: `expectException(RuntimeException::class)` em `get('GnreConfigUF', 3)`.
- [ ] **Step 2: Rodar e ver falhar** — Run: `vendor/bin/phpunit --filter WebservicesTest` → Expected: `Class "NFePHP\Gnre\Common\Webservices" not found`.
- [ ] **Step 3: Criar o `storage/wsgnre.json` e `src/Common/Webservices.php`** (decodifica com `json_decode(..., false)`; JSON inválido → `RuntimeException`).
- [ ] **Step 4: Rodar** — `vendor/bin/phpunit --filter WebservicesTest` → Expected: `OK (5 tests`. Depois `composer phpcs && composer stan` → sem erros.
- [ ] **Step 5: Commit** `feat(common): Webservices e storage/wsgnre.json (D-15)`

### Task T2.2: `Common\Tools` (config, ambiente, versão, soap, validação)

**Files:**
- Create: `src/Common/Tools.php`, `tests/Common/SoapFake.php`, `tests/fixtures/certs/cert_cnpj_06157250000116_senha_minhasenha.pfx`, `tests/ToolsTestCase.php`
- Test: `tests/Common/ToolsTest.php`

**Interfaces:**
- Consumes: `Webservices::get()` (T2.1).
- Produces (os testes da T2.3–T2.5 dependem disto):
  - `NFePHP\Gnre\Common\Tools` com as propriedades públicas `\stdClass $config`, `string $lastRequest = ''`, `string $lastResponse = ''`, `int $tpAmb = 2`, `string $versao = '2.00'` e `?SoapInterface $soap = null`.
  - `__construct(string $configJson, Certificate $certificate)`. Config inválido → `DocumentsException::wrongDocument(8, '<motivo>')`. Motivos: JSON inválido; `tpAmb` ∉ {1, 2}; `versao` ∉ {`1.00`, `2.00`}; `siglaUF` fora das 27 UFs; nem `cnpj` nem `cpf`.
  - `setEnvironment(int $tpAmb = 2): void` (valor inválido → `InvalidArgumentException`); `version(string $versao = ''): string` (`''` só lê; valor inválido → `InvalidArgumentException`); `loadSoapClass(SoapInterface $soap): void` (chama `$soap->loadCertificate($this->certificate)`).
  - `protected servico(string $servico, ?int $tpAmb = null): void` → preenche `urlService`, `urlNamespace`, `urlAction`, `urlMethod`, `urlVersion` (`versaoDados` fixo do serviço ou `$this->versao`).
  - `protected sendRequest(string $request): string`, que:
    - chama `checkSoap()`;
    - grava `lastRequest = $request`;
    - monta `$body = "<gnreDadosMsg xmlns=\"{$this->urlNamespace}\">$request</gnreDadosMsg>"` e `$header = new \SoapHeader($this->urlNamespace, 'gnreCabecMsg', ['versaoDados' => $this->urlVersion])`;
    - chama `$this->soap->send($this->urlService, $this->urlMethod, $this->urlAction, SOAP_1_2, [], self::SOAP_NAMESPACES, $body, $header)`;
    - grava e devolve `lastResponse`.
  - `protected isValid(string $xml, string $xsd, ?string $versao = null): bool` → `Validator::isValid($xml, <pasta da versao>/$xsd)`. XSD inexistente → `RuntimeException`.
  - `protected checkSoap(): void` → cria um `new SoapCurl($this->certificate)` se `$this->soap === null`.
  - Constantes: `public const PORTAL = 'http://www.gnre.pe.gov.br'`; `public const UFS` (as 27 siglas, sem AN/SVCAN/SVCRS/EX); `protected const SOAP_NAMESPACES = ['xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance', 'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema', 'xmlns:soap' => 'http://www.w3.org/2003/05/soap-envelope']`.
- Produces (testes): `NFePHP\Gnre\Tests\Common\SoapFake` (espelho de `D:/NfePhpDev/sped-nfe/tests/Common/SoapFake.php`, com `setReturnValue(string)`, `getSendParams(): array` e contador `getCalls(): int`). `NFePHP\Gnre\Tests\ToolsTestCase extends TestCase` tem:
  - `protected function config(array $sobrescreve = []): string` → JSON de `{"tpAmb":2,"razaosocial":"EMPRESA TESTE","cnpj":"06157250000116","siglaUF":"SP","versao":"2.00"}` mesclado com `$sobrescreve`;
  - `protected function certificado(): Certificate`;
  - `protected function golden(string $arquivo, string $raiz): string` → C14N do nó `$raiz` do golden em `tests/fixtures/legacy/`;
  - `protected function c14n(string $xml): string` → carrega com `preserveWhiteSpace = false` e devolve `documentElement->C14N()`.
  - Os golden foram gravados com indentação, por isso **toda comparação de XML nos testes usa `c14n()` dos dois lados**. O `golden()` carrega com `preserveWhiteSpace = false`.

- [ ] **Step 1: Copiar o certificado de teste e escrever o `SoapFake` e o `ToolsTestCase`.**
- [ ] **Step 2: Escrever `tests/Common/ToolsTest.php`:**
  - `testConfigValido`: `tpAmb === 2`, `versao === '2.00'`, `config->siglaUF === 'SP'`.
  - provider `configInvalidoProvider` → `testConfigInvalido` com `expectException(DocumentsException::class)` para: `'{'` (JSON quebrado), `tpAmb: 3`, `versao: '3.00'`, `siglaUF: 'AN'`, e `cnpj` e `cpf` ausentes (`config` sem as duas chaves).
  - `testCpfNoLugarDoCnpj`: config sem `cnpj` e com `cpf: '90483926086'` → não lança.
  - `testSetEnvironment`: `setEnvironment(1)` → `tpAmb === 1`; `setEnvironment(5)` → `InvalidArgumentException`.
  - `testVersion`: `version()` → `'2.00'`; `version('1.00')` → `'1.00'`; `version('9.99')` → `InvalidArgumentException`.
  - `testLoadSoapClass`: depois de `loadSoapClass($fake)`, `$tools->soap === $fake`.
  - `testCheckSoapCriaSoapCurl`: subclasse anônima de teste que expõe `checkSoap()` → `assertInstanceOf(SoapCurl::class, $tools->soap)`.
- [ ] **Step 3: Rodar e ver falhar** — `vendor/bin/phpunit --filter 'Common\\ToolsTest'` → Expected: `Class "NFePHP\Gnre\Common\Tools" not found`.
- [ ] **Step 4: Implementar `src/Common/Tools.php`** conforme as Interfaces. Caminhos: `$this->pathwsfiles = realpath(__DIR__ . '/../../storage') . '/'` e `schemes` com `realpath(__DIR__ . '/../../schemes')`.
- [ ] **Step 5: Rodar** — `composer test` → `OK`; `composer phpcs && composer stan` → sem erros.
- [ ] **Step 6: Commit** `feat(common): Tools base (config, ambiente, versao, soap, XSD)`

### Task T2.3: `Tools::sefazEnviaLote` (layouts 2.00 e 1.00)

**Files:**
- Create: `src/Tools.php`
- Test: `tests/ToolsEnviaLoteTest.php`

**Interfaces:**
- Consumes: `servico()`, `isValid()`, `sendRequest()` (T2.2); `ToolsTestCase` e `SoapFake` (T2.2).
- Produces: `NFePHP\Gnre\Tools extends Common\Tools` com `sefazEnviaLote(string $xmlLote): string`. Fluxo:
  1. `$request = Strings::clearXmlString($xmlLote, true)`, que remove a declaração e as quebras de linha;
  2. `isValid($request, "lote_gnre_v{$this->versao}.xsd")`;
  3. `servico('GnreLoteRecepcao')`;
  4. `return sendRequest($request)`.

- [ ] **Step 1: Escrever `tests/ToolsEnviaLoteTest.php`** (`extends ToolsTestCase`; `SoapFake` com `setReturnValue('<retorno/>')`):
  - `testEnviaLote200`: `$xml = $this->golden('lote-v2-valido-prod.xml', 'TLote_GNRE')`, depois `sefazEnviaLote($xml)`. Verifica:
    - retorno `'<retorno/>'`;
    - `sendParams['url']` = `https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao`;
    - `operation` = `processar`;
    - `action` = `"http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao"`;
    - `soapheader->namespace` = `http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao`, `soapheader->name` = `gnreCabecMsg`, `soapheader->data['versaoDados']` = `2.00`;
    - `request` começa com `<gnreDadosMsg xmlns="http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao"><TLote_GNRE`;
    - C14N de `lastRequest` = `$xml`.
  - `testEnviaLote100`: config `versao: '1.00'` e golden `lote-v1-valido.xml` → `versaoDados` `1.00` e C14N igual.
  - `testRemoveDeclaracaoXml`: `'<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml` → `lastRequest` sem `<?xml`, e o envio acontece.
  - `testXmlInvalidoNaoEnvia`: golden `lote-v2-completo-prod.xml` (dados inválidos) → `expectException(ValidatorException::class)` e, num `finally`, `assertSame(0, $fake->getCalls())`.
  - `testProducao`: `setEnvironment(1)` → `url` `https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao` e o mesmo namespace.
- [ ] **Step 2: Rodar e ver falhar** — `vendor/bin/phpunit --filter ToolsEnviaLoteTest` → Expected: `Class "NFePHP\Gnre\Tools" not found`.
- [ ] **Step 3: Implementar `sefazEnviaLote`** em `src/Tools.php`.
- [ ] **Step 4: Rodar** — `composer test`, `composer phpcs`, `composer stan` → verdes.
- [ ] **Step 5: Registrar no PROGRESSO** (tabela de divergências): a linha "namespace em homologação" vira a regra D-15, com o resumo de como era no legado (`gnreCabecMsg` em `wsdl/processar`, `gnreDadosMsg` em `testegnre` na homologação) e de como fica na 1.0.
- [ ] **Step 6: Commit** `feat: Tools::sefazEnviaLote (layouts 1.00 e 2.00)`

### Task T2.4: `sefazConsultaLote` e `sefazConsultaConfigUf`

**Files:**
- Modify: `src/Tools.php`
- Test: `tests/ToolsConsultasTest.php`

**Interfaces:**
- Produces:
  - `sefazConsultaLote(string $recibo, array $opcoes = []): string`. Monta `<TConsLote_GNRE xmlns="http://www.gnre.pe.gov.br"><ambiente>{tpAmb}</ambiente><numeroRecibo>{recibo}</numeroRecibo>` e acrescenta, nesta ordem e só se presentes, `incluirPDFGuias`, `incluirArquivoPagamento` e `incluirNoticias`, fechando com `</TConsLote_GNRE>`. Valida em `lote_gnre_consulta_v1.00.xsd` (pasta da versão) e usa o serviço `GnreResultadoLote`. Recibo vazio após `trim` → `InvalidArgumentException`. Chave de opção desconhecida ou valor diferente de `S`/`N` → `InvalidArgumentException`. Opções com `versao` 1.00 → `InvalidArgumentException` ("opções exigem layout 2.00").
  - `sefazConsultaConfigUf(string $uf, string $receita = '', bool $courier = false): string`. Monta `<TConsultaConfigUf xmlns="http://www.gnre.pe.gov.br"><ambiente>{tpAmb}</ambiente><uf>{UF}</uf>`; se `$receita !== ''`, acrescenta `<receita>{receita}</receita>` (com `courier="S"` se `$courier`); fecha com `</TConsultaConfigUf>`. Valida em `consulta_config_uf_v1.00.xsd` e usa o serviço `GnreConfigUF`. UF fora de `UFS` → `InvalidArgumentException`. `$courier` sem receita → `InvalidArgumentException`.

- [ ] **Step 1: Escrever `tests/ToolsConsultasTest.php`:**
  - `testConsultaLoteIgualAoGolden`: config `tpAmb: 1` e `sefazConsultaLote('1234567890')` → C14N de `lastRequest` = `golden('consulta-lote-prod.xml', 'TConsLote_GNRE')`; `operation` `consultar`; header namespace `http://www.gnre.pe.gov.br/webservice/GnreResultadoLote`.
  - `testConsultaLoteComPdf`: `sefazConsultaLote('2512345678', ['incluirPDFGuias' => 'S'])` → `lastRequest` contém `<incluirPDFGuias>S</incluirPDFGuias>` logo após `</numeroRecibo>`.
  - `testReciboVazio` (`'  '`), `testOpcaoInvalida` (`['incluirPDF' => 'S']`), `testValorOpcaoInvalido` (`['incluirNoticias' => 'X']`) e `testOpcaoNoLayout100` (config `versao: '1.00'` com `['incluirPDFGuias' => 'N']`) → todos `InvalidArgumentException` e `getCalls() === 0`.
  - `testReciboFormatoInvalido`: `'12345'` → `ValidatorException` (o XSD exige 10 ou 14 dígitos).
  - `testConfigUfIgualAoGolden`: config `tpAmb: 1` e `sefazConsultaConfigUf('PR', '100099')` → C14N igual a `golden('config-uf-prod.xml', 'TConsultaConfigUf')`; header namespace `http://www.gnre.pe.gov.br/webservice/GnreConfigUF`; `versaoDados` `2.00`.
  - `testConfigUfCourier`: `('SP', '100099', true)` → contém `<receita courier="S">100099</receita>`.
  - `testConfigUfSemReceita`: `('BA')` → não contém `<receita`.
  - `testUfInvalida` (`'XX'`) e `testCourierSemReceita` (`('SP', '', true)`) → `InvalidArgumentException`.
- [ ] **Step 2: Rodar e ver falhar** — `vendor/bin/phpunit --filter ToolsConsultasTest` → Expected: `Call to undefined method NFePHP\Gnre\Tools::sefazConsultaLote()`.
- [ ] **Step 3: Implementar** os dois métodos.
- [ ] **Step 4: Rodar** — `composer test`, `composer phpcs`, `composer stan` → verdes.
- [ ] **Step 5: Commit** `feat: Tools::sefazConsultaLote e sefazConsultaConfigUf`

### Task T2.5: Consulta de guia (D-14): `sefazEnviaLoteConsulta` e `sefazConsultaLoteConsulta`

**Files:**
- Modify: `src/Tools.php`
- Create: `tests/fixtures/xml/lote-consulta-guia.xml`
- Test: `tests/ToolsConsultaGuiaTest.php`

**Interfaces:**
- Produces:
  - `sefazEnviaLoteConsulta(string $xmlLoteConsulta): string` → `clearXmlString`, depois `isValid($request, 'lote_consulta_guia_v2.00.xsd', '2.00')` e o serviço `GnreLoteRecepcaoConsulta`.
  - `sefazConsultaLoteConsulta(string $recibo): string` → `<TConsLote_GNRE xmlns="http://www.gnre.pe.gov.br"><ambiente>{tpAmb}</ambiente><numeroRecibo>{recibo}</numeroRecibo></TConsLote_GNRE>`, validado em `lote_gnre_consulta_v1.00.xsd` da pasta `2.00`, serviço `GnreResultadoLoteConsulta`. Recibo vazio → `InvalidArgumentException`.

`tests/fixtures/xml/lote-consulta-guia.xml` (conteúdo exato, já validado contra o XSD 2.17):
```xml
<TLote_ConsultaGNRE xmlns="http://www.gnre.pe.gov.br" versao="2.00"><consulta><uf>SP</uf><emitenteId><CNPJ>06157250000116</CNPJ></emitenteId><codBarras>85800000000123456789012345678901234567890123</codBarras><idConsulta>1</idConsulta><tipoConsulta>C</tipoConsulta></consulta></TLote_ConsultaGNRE>
```

- [ ] **Step 1: Escrever `tests/ToolsConsultaGuiaTest.php`:**
  - `testEnviaLoteConsulta`: fixture → `url` `https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcaoConsulta`; `operation` `processar`; header namespace `http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcaoConsulta`; `versaoDados` `2.00`; C14N de `lastRequest` = C14N da fixture.
  - `testEnviaLoteConsultaComLayout100`: config `versao: '1.00'` com a mesma fixture → envia, `versaoDados` `2.00`.
  - `testEnviaLoteConsultaInvalido`: a fixture sem `<tipoConsulta>C</tipoConsulta>` → `ValidatorException` e `getCalls() === 0`.
  - `testConsultaLoteConsulta`: `sefazConsultaLoteConsulta('25123456789012')` (14 dígitos) → `url` `.../GnreResultadoLoteConsulta`; `operation` `consultar`; `lastRequest` = `<TConsLote_GNRE xmlns="http://www.gnre.pe.gov.br"><ambiente>2</ambiente><numeroRecibo>25123456789012</numeroRecibo></TConsLote_GNRE>`.
  - `testConsultaLoteConsultaReciboVazio` → `InvalidArgumentException`.
- [ ] **Step 2: Rodar e ver falhar** — `vendor/bin/phpunit --filter ToolsConsultaGuiaTest` → Expected: `Call to undefined method ...sefazEnviaLoteConsulta()`.
- [ ] **Step 3: Implementar** os dois métodos.
- [ ] **Step 4: Rodar** — `composer test`, `composer phpcs`, `composer stan` → verdes.
- [ ] **Step 5: Commit** `feat: consulta de guia (GnreLoteRecepcaoConsulta e GnreResultadoLoteConsulta)`

### Task T2.6: Exemplos, CI e fechamento da F2

**Files:**
- Create: `examples/config.json.dist`, `examples/bootstrap.php`, `examples/consulta-config-uf.php`, `examples/envia-lote.php`, `examples/consulta-lote.php`, `examples/consulta-guia.php`
- Modify: `CHANGELOG.md`, `docs/refatoracao/ROADMAP.md`, `docs/refatoracao/PROGRESSO.md`, `.gitignore` (`/examples/config.json`, `/examples/*.pfx`)

- [ ] **Step 1: Exemplos** no estilo `D:/NfePhpDev/sped-mdfe/examples`: o `bootstrap.php` lê o `config.json` (copiado de `config.json.dist`) e o `.pfx` apontados por variáveis no topo, cria `new Tools($config, Certificate::readPfx(...))` e cada exemplo faz `header('Content-type: text/xml'); echo $tools->sefaz...;` dentro de `try/catch`, imprimindo `$e->getMessage()`. Nenhum certificado ou senha real versionado.
- [ ] **Step 2: Conferir a sintaxe dos exemplos** — Run: `for f in examples/*.php; do php -l $f; done` → Expected: `No syntax errors detected` em todos.
- [ ] **Step 3: CHANGELOG** (`### Added`): os 5 serviços via `Tools`, `storage/wsgnre.json` e a consulta de guia.
- [ ] **Step 4: Push e CI** — Run: `git push && gh run watch --repo betoeu/sped-gnre --exit-status $(gh run list --repo betoeu/sped-gnre --branch refactor/v1 --limit 1 --json databaseId --jq '.[0].databaseId')` → Expected: 7 jobs `success`.
- [ ] **Step 5: Fechar a fase**: F2 ✅ no ROADMAP; PROGRESSO → "Próxima ação: escrever o plano da F3"; registrar em "Pendências" o checklist de homologação real para a F7: os 5 serviços com certificado do usuário e a conferência de D-15 e do `versaoDados` do ConfigUF. Commit `docs: conclui F2`.
