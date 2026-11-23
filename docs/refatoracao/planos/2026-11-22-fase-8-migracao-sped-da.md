# sped-gnre 1.0 — Fase F8: mover a impressão da guia para o sped-da (antes da F7, D-18)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** A impressão da GNRE passa a viver no sped-da como `NFePHP\DA\GNRE\Dagnre`, ao lado de DANFE, DACTE e DAMDFE. O sped-gnre deixa de depender do sped-da (só o sugere) e remove o seu `DA/`.

**Architecture:**
- O sped-da **não pode** depender do sped-gnre: o sped-gnre já dependia do sped-da, e a dependência ficaria circular.
- Por isso, no sped-da, o `Dagnre` lê o XML do resultado sozinho, como fazem `Danfe` e `Damdfe`. A leitura sem perda de atributos (D-17) e a do TXT do 1.00 viram uma classe interna do sped-da, `GNRE\ResultadoLote`, portada do `Standardize`/`RetornoTxtV1` reduzida ao que a impressão precisa.
- O `DadosGuia` e o `Dagnre` são portados com a troca de namespace.
- Os testes da F6 vão junto e são adaptados às convenções do sped-da (`TEST_FIXTURES`, `NFePHP\DA\Tests\Utils`).

**Tech Stack:** sped-da (PHP ≥ 7.4, PHPUnit 9, phpcs `--standard=psr2`, phpstan nível 1, CI com phpunit em 7.4/8.0), fork em `D:\betoeu\sped-da`.

**Spec:** `docs/refatoracao/ROADMAP.md`, D-03, D-09, D-13 e D-18. Código de origem: `src/DA/Dagnre.php`, `src/DA/DadosGuia.php`, `src/Common/Standardize.php` e `src/Common/RetornoTxtV1.php` do sped-gnre (commit `9886c5f`), com os testes `tests/DA/*` e `tests/Common/{Standardize,RetornoTxtV1}Test.php`.

## Global Constraints

- **No sped-da** (branch `feature/gnre` no fork `betoeu/sped-da`):
  - namespace `NFePHP\DA\GNRE`, arquivos em `src/GNRE/`;
  - testes em `tests/GNRE/` (namespace `NFePHP\DA\Tests\GNRE`), fixtures em `tests/fixtures/xml/gnre/`;
  - exemplo em `examples/gnre/dagnre.php`, documentação em `docs/Dagnre.md`;
  - código PHP 7.4 (sem sintaxe PHP 8), `composer phpcs` (PSR-2) e `composer stan` (nível 1) do próprio sped-da limpos para `src/GNRE`;
  - **nenhuma** referência a `NFePHP\Gnre`.
- A API pública do `Dagnre` não muda: `__construct(string $xmlResultado)`, `render($logo = '')`, `getGuias()`, `getGuiasNaoImpressas()`, mais o herdado do `DaCommon` (`creditsIntegratorFooter`, `logoParameters`, `printParameters`...).
- **No sped-gnre:**
  - saem `src/DA/`, `tests/DA/`, as dependências `nfephp-org/sped-da`, `ext-gd` e `smalot/pdfparser`;
  - entra `"suggest": {"nfephp-org/sped-da": "Impressão da guia (NFePHP\\DA\\GNRE\\Dagnre)"}`;
  - o `Standardize` e o `RetornoTxtV1` **continuam** no sped-gnre, porque são úteis para o integrador ler os retornos.
- **PR no nfephp-org/sped-da:** por analogia com a D-13, ele só é aberto **na F7**, junto com o anúncio do sped-gnre. Na F8, a branch fica só no fork.

## Review Focus

- Prefixo `ns1:` + envelope SOAP + atributo com texto: o `ResultadoLote` do sped-da produz os mesmos dados que o `Standardize` do sped-gnre. Os testes do `DadosGuia` portados (valores por tipo, `documentoOrigem->value`) cobrem isso (T8.2).
- Os testes já existentes do sped-da continuam passando: a linha de base é registrada antes e comparada depois (T8.1, T8.3).
- Depois da remoção, nada no `src/` do sped-gnre referencia `NFePHP\DA` ou `Com\Tecnick`, e o `composer install --no-dev` do sped-gnre não instala o sped-da (T8.4).
- O exemplo do sped-gnre que imprime precisa ficar claro que exige `composer require nfephp-org/sped-da` (ainda sem versão publicada com GNRE) e falhar com uma mensagem amigável se a classe não existir (T8.4).

---

### Task T8.1: Preparar o fork do sped-da

- [x] **Step 1:** `git -C D:/betoeu/sped-da fetch upstream && git -C D:/betoeu/sped-da checkout -b feature/gnre upstream/master`.
- [x] **Step 2:** `composer install --no-interaction` no sped-da → sem erro.
- [x] **Step 3: Linha de base:** `vendor/bin/phpunit` no sped-da e registrar no PROGRESSO do sped-gnre o total de testes, falhas e erros. `composer phpcs` e `composer stan`: registrar se o upstream já tem violações (só `src/GNRE` precisa sair limpo).
- [x] **Step 4:** registrar no PROGRESSO a branch e o commit de partida.

### Task T8.2: Portar `ResultadoLote`, `DadosGuia` e `Dagnre` para o sped-da (TDD)

**Files (sped-da):**
- Create: `src/GNRE/ResultadoLote.php`, `src/GNRE/DadosGuia.php`, `src/GNRE/Dagnre.php`
- Create: `tests/fixtures/xml/gnre/retorno-resultado-v2.xml`, `retorno-resultado-v1.xml`, `retorno-recibo.xml` e `README.md` (origem), copiados das fixtures do sped-gnre
- Test: `tests/GNRE/ResultadoLoteTest.php`, `tests/GNRE/DadosGuiaTest.php`, `tests/GNRE/DagnreTest.php`, `tests/GNRE/DagnreEspiao.php`

**Interfaces:**
- Produces: `NFePHP\DA\GNRE\ResultadoLote::ler(string $xml): stdClass` → `{situacaoProcess: {codigo, descricao}, guias: stdClass[]}`.
  - As guias já vêm normalizadas pelo `DadosGuia` (todas, inclusive as rejeitadas).
  - Documento sem `TResultLote_GNRE` → `\InvalidArgumentException`.
  - Internamente: localiza o nó pelo nome local (aceita prefixo e envelope), converte com as regras D-17 e, se o `resultado` for texto, lê o TXT do 1.00 com as posições do ACBr.
- Produces: `NFePHP\DA\GNRE\DadosGuia` (mesma API e comportamento do sped-gnre) e `NFePHP\DA\GNRE\Dagnre` (mesma API pública). O `Dagnre` lança `\InvalidArgumentException` onde o sped-gnre lançava `DocumentsException`, porque o sped-da usa as exceções nativas do PHP; as mensagens continuam as mesmas.

- [x] **Step 1: Copiar as fixtures e portar os testes.** Troque os namespaces; use `TEST_FIXTURES . 'xml/gnre/'`; use `NFePHP\DA\Tests\Utils::textoPdf()` no lugar do helper `texto()`; e troque `DocumentsException` por `\InvalidArgumentException`. O `ResultadoLoteTest` recebe do `StandardizeTest`/`RetornoTxtV1Test` os casos que importam para a impressão: o nó dentro do envelope com `ns1:`, `valor` com `attributes`/`value`, uma guia vira objeto e duas viram array, o TXT do 1.00 e um documento que não é resultado.
- [x] **Step 2: Rodar e ver falhar** — `vendor/bin/phpunit tests/GNRE` → classes inexistentes.
- [x] **Step 3: Portar o código.**
  - `ResultadoLote` reúne o conversor D-17 e o leitor de TXT.
  - O `DadosGuia` passa a receber o `stdClass` produzido pelo `ResultadoLote`.
  - O `Dagnre` troca `Standardize` + `RetornoTxtV1` por `ResultadoLote::ler()`.
- [x] **Step 4:** `vendor/bin/phpunit` (a suíte inteira do sped-da) → os testes GNRE verdes e os demais iguais à linha de base. `vendor/bin/phpcs --standard=psr2 src/GNRE` e `vendor/bin/phpstan analyse src/GNRE --level 1` → limpos. Conferir também o PHPCompatibility, rodando o phpcs do sped-gnre (`testVersion 7.4-`) sobre `D:/betoeu/sped-da/src/GNRE`.
- [x] **Step 5: Conferência visual** (pypdfium2 no venv do scratchpad): o PDF gerado no sped-da é igual ao da F6.
- [x] **Step 6: Commit no sped-da** `feat: GNRE - documento auxiliar da guia (Dagnre)`, com corpo explicando a origem (refatoração do sped-gnre 1.0).

### Task T8.3: Exemplo, documentação e push da branch no fork do sped-da

**Files (sped-da):**
- Create: `examples/gnre/dagnre.php` (no estilo de `examples/mdfe/*`: lê o XML de exemplo e devolve o PDF), `docs/Dagnre.md` (uso, entrada aceita, 3 vias, I25, PIX, guias não impressas)
- Modify: `README.md` do sped-da (incluir o GNRE na lista de documentos) e `CHANGELOG.md` do sped-da

- [x] **Step 1:** exemplo + `php -l` + execução (`%PDF`).
- [x] **Step 2:** docs, README e CHANGELOG.
- [x] **Step 3:** suíte completa do sped-da de novo → igual à linha de base + GNRE verde.
- [x] **Step 4:** commit `docs: GNRE (exemplo e documentação do Dagnre)` e `git push -u origin feature/gnre` (só no fork; o PR para o upstream fica para a F7).

### Task T8.4: Remover a impressão do sped-gnre

**Files (sped-gnre):**
- Delete: `src/DA/`, `tests/DA/`
- Modify: `composer.json` (tira `nfephp-org/sped-da`, `ext-gd` e `smalot/pdfparser`; acrescenta o `suggest`), `examples/dagnre.php` (passa a usar `NFePHP\DA\GNRE\Dagnre`, com checagem `class_exists` e mensagem explicando como instalar), `README.md` (a seção "Impressão da guia" aponta para o sped-da; requisitos sem `gd`), `CHANGELOG.md`, `docs/refatoracao/ROADMAP.md`, `docs/refatoracao/PROGRESSO.md`

- [x] **Step 1:** remover os diretórios e as dependências; `composer update` → sem erro; `composer install --no-dev --dry-run` não lista `nfephp-org/sped-da`.
- [x] **Step 2:** `rg -n "NFePHP\\\\DA|Com\\\\Tecnick" src tests` → nenhuma ocorrência.
- [x] **Step 3:** a cadeia `composer phpcs && composer stan && composer test` → verde (a suíte cai em 225 − os testes do `DA` = 205).
- [x] **Step 4:** avaliar se o phpstan ainda precisa do `--memory-limit=512M`. Se não precisar, reverter a R-16 e registrar.
- [x] **Step 5:** push e CI das 7 versões → verde.
- [x] **Step 6: Fechar a F8:** F8 ✅ no ROADMAP; D-09 atualizada (o sped-da sai do `require` já na 1.0); PROGRESSO com a próxima ação = escrever o plano da F7, incluindo o PR do sped-da. Commit `refactor!: impressao da guia movida para o sped-da (NFePHP\DA\GNRE\Dagnre)`.
