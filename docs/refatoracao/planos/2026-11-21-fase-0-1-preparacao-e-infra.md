# sped-gnre 1.0 — Fases F0 e F1: preparação, linha de base e infraestrutura

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Deixar o fork numa branch limpa, registrar o comportamento atual em XMLs de referência (golden) e entregar o esqueleto 1.0 (`NFePHP\Gnre`, PHP ≥ 7.4) com phpcs, phpstan, phpunit e CI verdes.

**Architecture:** O código antigo é congelado em `legacy/`, sem autoload. O novo `src/` nasce vazio, contendo só as exceções, com a mesma configuração de ferramentas do sped-nfe. Os XSDs passam para `schemes/PL_GNRE_100` e `schemes/PL_GNRE_200`.

**Tech Stack:** PHP 7.4–8.5, Composer 2, nfephp-org/sped-common ^5.1, PHPUnit 9.6, PHP_CodeSniffer 3 (PSR-12 + PHPCompatibility), PHPStan 1.x nível 5, GitHub Actions.

**Spec:** `docs/refatoracao/ROADMAP.md` (decisões D-01..D-10 e contratos).

## Global Constraints

- Repositório: `D:\betoeu\sped-gnre`; origin `betoeu/sped-gnre`, upstream `nfephp-org/sped-gnre`.
- Branch de trabalho: `refactor/v1`. Nunca commitar direto no `master`.
- `"php": ">=7.4"`. Nenhuma sintaxe PHP 8 em `src/` e `tests/` (lista em D-01).
- Namespace `NFePHP\Gnre\` → `src/`; testes `NFePHP\Gnre\Tests\` → `tests/`.
- Licença igual à dos demais projetos: `["LGPL-3.0-or-later", "GPL-3.0-or-later", "MIT"]` e LICENSE.md do sped-nfe (D-11).
- Finais de linha LF em todo o repositório.
- Sem `composer.lock` versionado (biblioteca, igual ao sped-nfe).
- Ao fim de cada tarefa: marcar os checkboxes aqui, atualizar `docs/refatoracao/PROGRESSO.md` e fazer o commit.
- Mensagens de commit terminam com `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>` quando feitas pelo Claude.

## Review Focus

- CRLF volta ao repositório por causa do Windows → a CI falha se `git ls-files --eol` mostrar `w/crlf` (T1.4).
- Sintaxe PHP 8 nos testes (o phpcs do sped-nfe só olha `src/`) → phpcs roda em `src/` e `tests/` com PHPCompatibility `testVersion 7.4-` (T1.3).
- Golden não determinístico (datas, caminhos absolutos, ordem) → rodar o gerador duas vezes deve dar diff vazio (T0.4).
- Um `xs:include` ou `xs:import` quebrado depois de mover os XSDs → `SchemesTest::testIncludesExistem` (T1.3).
- Dependência resolvida no 8.3 local que não instala no 7.4 → sem lock versionado; a matriz da CI instala cada versão do zero (T1.4).

---

## Fase F0 — Preparação e linha de base

### Task T0.1: Proteger o fork e criar a branch

**Files:** nenhum arquivo de código.

- [x] **Step 1: Guardar os 2 commits locais numa branch só local** — `git branch backup/master-local-2024-08 master` (D-12)
- [x] **Step 2: Sincronizar o master local com o remoto oficial** — `git fetch --all && git reset --hard origin/master`
  Resultado (2026-11-21): `master` = `origin/master` = `upstream/master` = `189fee1`.
- [x] **Step 3: Criar a branch**
  Run: `git -C D:/betoeu/sped-gnre checkout -b refactor/v1`
- [x] **Step 4: Commitar os documentos do projeto**
  ```bash
  git add docs/refatoracao
  git commit -m "docs: roadmap e plano da refatoracao 1.0"
  git push -u origin refactor/v1
  ```

### Task T0.2: Normalizar os finais de linha

**Files:**
- Create: `.gitattributes`

- [x] **Step 1: Criar `.gitattributes`** com a primeira linha `* text=auto eol=lf`, `*.pdf binary`, `*.zip binary`, `*.jpg binary`, `*.png binary`, `*.pfx binary`, seguidas do bloco `export-ignore` do sped-nfe (`D:/NfePhpDev/sped-nfe/.gitattributes`) acrescido de `/legacy export-ignore` e `/docs export-ignore`.
- [x] **Step 2: Renormalizar**
  Run: `git add --renormalize . && git status --short | wc -l`
- [x] **Step 3: Verificar que só mudaram os finais de linha**
  Run: `git diff --cached --ignore-cr-at-eol --stat | tail -1`
  Expected: só `.gitattributes` aparece com conteúdo real.
- [x] **Step 4: Verificar que não sobrou CRLF**
  Run: `git ls-files --eol | grep -c "i/crlf"` → Expected: `0`
- [x] **Step 5: Commit** `chore: normaliza finais de linha para LF`

### Task T0.3: Linha de base da suíte antiga

**Files:**
- Modify: `composer.lock` (o lock só existe até a T1.2)
- Modify: `docs/refatoracao/PROGRESSO.md`

- [x] **Step 1: Reconciliar o lock no PHP 8.3 local**
  Run: `composer update --no-interaction --no-progress`
  Expected: termina sem erro. Se falhar, registrar o erro no PROGRESSO e usar `composer update --ignore-platform-req=php`.
- [x] **Step 2: Rodar a suíte antiga**
  Run: `vendor/bin/phpunit --testdox 2>&1 | tail -40`
- [x] **Step 3: Registrar no PROGRESSO** (seção nova "Linha de base F0"): contagem de testes, falhas e erros, e o nome de cada teste que falhou com a mensagem em uma linha. **Não corrigir nada.**
- [x] **Step 4: Commit** `chore: atualiza lock legado e registra linha de base`

### Task T0.4: XMLs de referência (golden) do código antigo

**Files:**
- Create: `tests/fixtures/legacy/gerar.php` (roda só com o código antigo, antes da T1.1)
- Create: `tests/fixtures/legacy/*.xml`, `tests/fixtures/legacy/validos.txt`

**Interfaces:**
- Produces: os arquivos abaixo, usados por F2, F3 e F4 para comparação.

| Arquivo | Como é gerado |
|---|---|
| `lote-v1-completo.xml` | `Sped\Gnre\Sefaz\Lote` + a guia de `exemplos/gerar-xml.php` → `toXml()` |
| `lote-v1-cpf-sem-extras.xml` | igual, com `c27=2`, `c03=` CPF `52162389004`, `c34=2`, `c35=` CPF `12345678909`, sem `c39_camposExtras` |
| `lote-v2-completo-prod.xml` | `(new Sped\Gnre\Sefaz\LoteV2())->setAmbienteDeTesteV2(false)` + `addGuia(mesma guia)` → `toXml()` |
| `lote-v2-completo-homolog.xml` | igual, com `setAmbienteDeTesteV2(true)` |
| `consulta-lote-prod.xml` | `new Sped\Gnre\Sefaz\Consulta()`; `setRecibo('1234567890')`, `setEnvironment(1)` → `toXml()` (o oficial não tem construtor; ambiente de produção = padrão, `utilizarAmbienteDeTeste(false)`) |
| `config-uf-prod.xml` | `new Sped\Gnre\Sefaz\ConfigUf()`, `setEnvironment(1)`, `setEstado('PR')`, `setReceita(100099)` → `toXml()` |
| `lote-v1-valido.xml` | `Lote` + `guiaValida()` (acrescentado na execução, ver R-03) |
| `lote-v2-valido-prod.xml` | `LoteV2` produção + `guiaValida()` (acrescentado na execução, ver R-03) |

- [x] **Step 1: Escrever `gerar.php`**: `require __DIR__.'/../../../vendor/autoload.php'`, monta cada caso da tabela e grava com `DOMDocument::loadXML` + `formatOutput = true` (saída estável e legível) em `__DIR__`. Uma função `guiaExemplo(): Sped\Gnre\Sefaz\Guia` com os valores de `exemplos/gerar-xml.php`.
- [x] **Step 2: Gerar**
  Run: `php tests/fixtures/legacy/gerar.php && ls tests/fixtures/legacy/*.xml | wc -l` → Expected: `6` → resultado `8` (R-03)
- [x] **Step 3: Conferir o determinismo**
  Run: `php tests/fixtures/legacy/gerar.php && git status --short tests/fixtures/legacy` (após um `git add` do Step 2) → Expected: nenhuma modificação. ✔
- [x] **Step 4: Validar o conteúdo interno nos XSDs**. Formato do `validos.txt` alterado para `arquivo;nome-do-xsd`, só o nome do arquivo (R-02).
- [x] **Step 5: Registrar no PROGRESSO** quais goldens falharam no XSD e a primeira mensagem do libxml (são os primeiros candidatos à tabela de divergências).
- [x] **Step 6: Commit** `test: golden XMLs do comportamento legado`

### Task T0.5: Fork do sped-da (ação do usuário)

- [x] **Step 1 (usuário):** fork criado em github.com/betoeu/sped-da (2026-11-21).
- [x] **Step 2:** clonado em `D:/betoeu/sped-da` com o remote `upstream` → nfephp-org/sped-da.
- [x] **Step 3:** `master` = `upstream/master` = `6e4b3e7`.
- [x] **Step 4 (usuário):** `gh auth login` feito (conta betoeu).
- [x] **Step 5:** marcar F0 ✅ no ROADMAP e commitar `docs: conclui F0`.

---

## Fase F1 — Infraestrutura e esqueleto

### Task T1.1: Congelar o legado e reorganizar as pastas

**Files:** só movimentações (`git mv`).

- [x] **Step 0: Resolver a pendência P-01 (pacote de schemas, ver PROGRESSO)** antes de mover. O `xsd/v2` do repositório está quebrado (inclui `dados_gnre_v1.00.xsd` e `tiposBasicoGNRE_v1.00.xsd`, que não estão na pasta) e diverge do zip do manual nos dois sentidos.
  - Se o pacote vigente puder ser baixado do Portal GNRE: ele vira `schemes/PL_GNRE_200/` (pasta plana, todos os arquivos).
  - Se não puder: `schemes/PL_GNRE_200/` = os 13 XSDs de `exemplos/docs/Manual_de_Integracao_Contribuintes_GNRE_v2.00.zip`, aplicando por cima o padrão `[0-9]{2}BR[0-9]{11}` do `tiposBasicoGNRE_v2.00.xsd` do repositório; `schemes/PL_GNRE_100/` = `xsd/v1` do repositório.
  - Registrar a origem de cada arquivo em `schemes/README.md`.
  - Nesse caso, a linha `git mv xsd/v2 …` do Step 1 é trocada pela cópia do pacote escolhido mais `git rm -r xsd/v2`.
- [x] **Step 1: Mover**
  ```bash
  mkdir -p legacy docs schemes
  git mv exemplos/docs docs/manuais
  git mv lib legacy/lib && git mv testes legacy/testes && git mv exemplos legacy/exemplos
  git mv templates legacy/templates && git mv wsdl legacy/wsdl && git mv certs legacy/certs
  git mv xsd/v1 schemes/PL_GNRE_100 && git mv xsd/v2 schemes/PL_GNRE_200
  git mv tests/fixtures/legacy/gerar.php legacy/gerar-golden.php
  git rm -q .travis.yml .coveralls.yml Dockerfile docker-compose.yml composer.lock phpunit.xml.dist .github/workflows/php.yml
  ```
- [x] **Step 2: Criar `legacy/README.md`** (3 linhas): código 0.x congelado só para referência, sem autoload, removido na F7.
- [x] **Step 3: Atualizar `.gitignore`**: acrescentar `composer.lock`, `/build/`, `.phpunit.cache/`, `/legacy/certs/*.pfx`.
- [x] **Step 4: Verificar**
  Run: `ls` → Expected: `docs legacy schemes tests LICENSE.md README.md composer.json .gitattributes .gitignore .github`
- [x] **Step 5: Commit** `refactor!: congela codigo 0.x em legacy/ e adota estrutura padrao`

### Task T1.2: Novo `composer.json`

**Files:**
- Modify: `composer.json` (reescrito)

- [x] **Step 1: Escrever o `composer.json`** no formato do `D:/NfePhpDev/sped-nfe/composer.json`, com estes valores:
  - `name` `nfephp-org/sped-gnre`, `type` `library`, `description` `"API para geração e comunicação da GNRE com o Portal GNRE (SEFAZ-PE)."`, `keywords` `["gnre","sped","nfephp","icms","difal"]`, `homepage` `https://github.com/nfephp-org/sped-gnre`, `license` `["LGPL-3.0-or-later","GPL-3.0-or-later","MIT"]`
  - `authors`: Matheus Marabesi (autor original), Comunidade NFePHP (`/graphs/contributors`)
  - `require`: `php >=7.4`, `nfephp-org/sped-common ^5.1`, `ext-dom`, `ext-curl`, `ext-openssl`, `ext-soap`, `ext-json`, `ext-libxml`, `ext-simplexml`, `ext-mbstring`
  - `require-dev`: `phpunit/phpunit ^9.6`, `squizlabs/php_codesniffer ^3.7`, `phpstan/phpstan ^1.10`, `phpcompatibility/php-compatibility ^10.0@alpha` (R-07), `dealerdirect/phpcodesniffer-composer-installer ^1.0`
  - `autoload` `NFePHP\\Gnre\\` → `src/`; `autoload-dev` `NFePHP\\Gnre\\Tests\\` → `tests/`
  - `scripts`: `test` `vendor/bin/phpunit -c phpunit.xml.dist`, `phpcs` `vendor/bin/phpcs`, `phpcbf` `vendor/bin/phpcbf`, `stan` `vendor/bin/phpstan analyse`, `precommit` `["@phpcs","@stan","@test"]`
  - `extra.branch-alias` `{"dev-refactor/v1": "1.0.x-dev"}`; `minimum-stability` `stable`; `config.allow-plugins` `{"dealerdirect/phpcodesniffer-composer-installer": true}`
- [x] **Step 2: Validar** — Run: `composer validate --strict` → Expected: `./composer.json is valid`
- [x] **Step 3: Instalar** — Run: `composer install --no-interaction` → Expected: sem erro; `vendor/nfephp-org/sped-common` presente.
- [x] **Step 4: Commit** `build!: composer 1.0 (php>=7.4, sped-common, sem smarty/dompdf/laminas)`

### Task T1.3: Esqueleto do `src/`, configuração das ferramentas e primeiro teste

**Files:**
- Create: `src/Exception/ExceptionInterface.php`, `InvalidArgumentException.php`, `RuntimeException.php`, `DocumentsException.php` (espelhar `D:/NfePhpDev/sped-mdfe/src/Exception/*`, trocando o namespace para `NFePHP\Gnre\Exception`)
- Create: `phpcs.xml`, `phpstan.neon.dist` (R-08), `phpunit.xml.dist`, `.editorconfig` (copiar do sped-nfe), `bootstrap.php`, `storage/.gitkeep`
- Test: `tests/SchemesTest.php`

**Interfaces:**
- Produces: `NFePHP\Gnre\Exception\DocumentsException::wrongDocument(int $code, string $msg = ''): self` com o mesmo mapa de códigos do mdfe (os códigos GNRE são acrescentados nas fases seguintes).

- [x] **Step 1: Escrever o teste `tests/SchemesTest.php`** (`NFePHP\Gnre\Tests\SchemesTest extends PHPUnit\Framework\TestCase`):
  - `testTodosOsXsdCarregam` (data provider com cada `schemes/*/*.xsd`): `assertTrue((new DOMDocument())->load($xsd))`.
  - `testIncludesExistem` (mesmo provider): para cada `xs:include/@schemaLocation` e `xs:import/@schemaLocation`, `assertFileExists(dirname($xsd).'/'.$loc)`.
  - `testGoldenValidos` (provider lido de `tests/fixtures/legacy/validos.txt`; se o arquivo estiver vazio, `markTestSkipped`): extrai o nó raiz de dados (`TLote_GNRE`, `TConsLote_GNRE` ou `TConsultaConfigUf`) e chama `NFePHP\Common\Validator::isValid($xml, $xsd)` → `assertTrue`. O `validos.txt` guarda `arquivo;nome-do-xsd` (R-02): nomes com `v2.00` são procurados em `schemes/PL_GNRE_200/` e os demais em `schemes/PL_GNRE_100/`. Os 4 golden listados precisam passar.
  - `testDocumentsExceptionImplementaInterface`: `assertInstanceOf(ExceptionInterface::class, DocumentsException::wrongDocument(1))`.
- [x] **Step 2: Rodar e ver falhar** — Run: `vendor/bin/phpunit` → Expected: erro `Class "NFePHP\Gnre\Exception\DocumentsException" not found` (ou "No tests executed" sem `phpunit.xml.dist`).
- [x] **Step 3: Criar as exceções e as configurações**:
  - `phpunit.xml.dist`: schema PHPUnit 9.6, `bootstrap="vendor/autoload.php"`, testsuite `tests`, `<coverage><include><directory>src</directory></include></coverage>`.
  - `phpcs.xml`: `<file>src</file><file>tests</file>`, `<arg name="extensions" value="php"/>`, `<rule ref="PSR12"/>`, `<config name="testVersion" value="7.4-"/>`, `<rule ref="PHPCompatibility"/>`, exclusão de `PSR1.Methods.CamelCapsMethodName` em `*/tests/*`.
  - `phpstan.neon`: `level: 5`, `paths: [src, tests]`, `treatPhpDocTypesAsCertain: false`.
- [x] **Step 4: Rodar tudo** — Run: `composer phpcs && composer stan && composer test` → Expected: 0 erros no phpcs, `[OK] No errors` no phpstan, `OK (N tests…)` no phpunit.
- [x] **Step 5: Conferir que o PHPCompatibility funciona**: criar temporariamente `src/Tmp.php` com `function f(): int|string { return 1; }` e rodar `composer phpcs` → Expected: erro de union type. Apagar o `src/Tmp.php`.
- [x] **Step 6: Commit** `feat: esqueleto NFePHP\Gnre (exceptions, phpcs, phpstan, phpunit)`

### Task T1.4: CI, README e anúncio no upstream

**Files:**
- Create: `.github/workflows/ci.yml`
- Modify: `README.md` (reescrito), Create: `CHANGELOG.md`

- [ ] **Step 1: `ci.yml`** com base em `D:/NfePhpDev/sped-nfe/.github/workflows/ci.yml`:
  - gatilhos: push em `master` e `refactor/v1`, PR em `master`
  - matriz `["7.4","8.0","8.1","8.2","8.3","8.4","8.5"]`
  - extensões: `dom, curl, openssl, soap, json, libxml, simplexml, mbstring, gd, zlib`
  - steps: checkout@v4 → setup-php@v2 → `composer validate --strict` → `git ls-files --eol | grep "w/crlf" && exit 1 || true` → `composer install --no-progress` → `composer phpcs` → `composer stan` → `composer test`
  - sem Codecov por enquanto (o fork não tem token)
- [ ] **Step 2: Push e conferir a CI** — Run: `git push` e, com o gh autenticado, `gh run watch` (sem gh: aba Actions do fork) → Expected: as 7 versões verdes. Uma versão vermelha → corrigir antes de seguir e registrar a causa no PROGRESSO.
- [ ] **Step 3: README.md** com: badges da CI do fork; aviso "1.0 em desenvolvimento — para a 0.x use a tag `0.0.x`"; requisitos (PHP ≥ 7.4, extensões); instalação; link para `docs/refatoracao/ROADMAP.md`. Preservar a seção "Antes de usar a API" do README atual (avisos sobre a SEFAZ e o certificado).
- [ ] **Step 4a: Licença** — copiar `D:/NfePhpDev/sped-nfe/LICENSE.md` (LGPLv3) por cima do `LICENSE.md` e acrescentar ao README a seção "Licença" com o texto padrão do sped-nfe (LGPLv3+, GPLv3+ ou MIT, à escolha). Registrar no CHANGELOG: `### Changed — licença alinhada ao ecossistema`.
- [ ] **Step 4: CHANGELOG.md** com `## [1.0.0] - Unreleased` e `### Changed` (namespace, PHP ≥ 7.4, sped-common), `### Removed` (Smarty, Dompdf, laminas, sped-nfe, Guzzle).
- [ ] **Step 5 (usuário): abrir uma issue em nfephp-org/sped-gnre** com o título "Proposta: sped-gnre 1.0 no padrão sped-common (PHP ≥ 7.4)" e o resumo das decisões D-01..D-10 e link para o ROADMAP no fork. Anotar o número da issue no PROGRESSO.
- [ ] **Step 6: Fechar a fase**: marcar F1 ✅ no ROADMAP; no PROGRESSO, definir "Próxima ação: escrever o plano detalhado da F2"; commit `ci: pipeline 7.4-8.5 e docs da 1.0`.
