# sped-gnre 1.0 — Fase F7: revisão, roteiro de homologação, documentação, limpeza e RC

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Deixar o sped-gnre 1.0 (e o `Dagnre` do sped-da) prontos para os PRs no nfephp-org:
- revisão independente com os achados tratados;
- roteiro de homologação para o usuário executar quando tiver certificado (decisão do usuário: **só o roteiro**; não há certificado de homologação agora);
- documentação completa e guia de migração;
- `legacy/` removido;
- tag `v1.0.0-rc1` no fork.

Os PRs e a issue no upstream (D-13, D-18) são o último passo e **só acontecem com a confirmação explícita do usuário**.

**Spec:** `docs/refatoracao/ROADMAP.md` (D-08, D-13, D-18, P-05) e `docs/refatoracao/PROGRESSO.md`.

## Global Constraints

- Tudo das fases anteriores continua valendo (cadeia condicionada `composer phpcs && composer stan && composer test` antes de cada commit; CI verde nas 7 versões antes de fechar).
- A homologação **não** é executada nesta fase. O roteiro precisa permitir que o usuário a execute sozinho e registre o resultado de cada ponto da P-05.
- Nada é enviado ao nfephp-org sem o usuário confirmar naquele momento.

## Review Focus

- Achados Critical/Important da revisão independente: cada correção precisa de um teste que falhe antes (RED → GREEN).
- Depois da remoção do `legacy/`, nenhum teste, doc ou exemplo pode apontar para um caminho inexistente (T7.3).
- O roteiro de homologação não pode pedir ao usuário que versione certificado ou senha (T7.2).

---

### Task T7.1: Revisão independente e passe de correção

- [x] **Step 1:** dois revisores independentes, um para cada branch: sped-gnre `refactor/v1` (base `189fee1`) e sped-da `feature/gnre` (base `6e4b3e7`). Os dois são só leitura e recebem o ROADMAP, o PROGRESSO e os planos.
- [x] **Step 2:** reavaliar cada achado pelo efeito para o integrador.
  - Critical e Important entram num **único** passe de correção, cada um com teste RED → GREEN e suíte completa verde.
  - Minor vai para o PROGRESSO como "Final: minor (adiado)".
  - Cada item da lista "Declined to judge" vira uma decisão registrada.
- [x] **Step 3:** registrar no PROGRESSO o veredito de cada revisor, os itens corrigidos (teste + commit) e os adiados.
- [x] **Step 4:** commits nos dois repositórios + push (sped-gnre: CI das 7 versões verde).

### Task T7.2: Roteiro de homologação (P-05)

**Files:** Create `docs/HOMOLOGACAO.md`, `examples/homologacao.php`

- [x] **Step 1: `examples/homologacao.php`** executa, em sequência, contra o ambiente de **homologação** (`tpAmb` 2):
  1. `sefazConsultaConfigUf` (UF do config, sem receita);
  2. `sefazEnviaLote` com o lote do `dados-lote.php`;
  3. `sefazConsultaLote` com o recibo retornado (aguardando o `tempoEstimadoProc`);
  4. `sefazEnviaLoteConsulta` com uma consulta pelo código de barras da guia gerada;
  5. `sefazConsultaLoteConsulta`.

  Para cada serviço, grava `lastRequest` e `lastResponse` em `examples/homologacao-AAAAMMDD-HHMMSS/NN-servico-{request,response}.xml` e imprime um resumo (código e descrição da situação via `Standardize`). O certificado e a senha vêm do `config.json` (fora do git). A pasta de saída também fica no `.gitignore`.
- [x] **Step 2: `docs/HOMOLOGACAO.md`** com:
  - pré-requisitos: certificado A1, liberação do serviço GNRE na UF e acesso ao `testegnre.pe.gov.br`;
  - configuração e passo a passo;
  - **o que observar em cada ponto da P-05**: (a) o envelope D-15 aceito nos 5 serviços; (b) o `versaoDados` do ConfigUF, com o que testar se houver rejeição (trocar `versaoDados` para `1.00` no `storage/wsgnre.json`); (c) o `versaoDados` `2.00` nas consultas de guia;
  - um teste do layout 1.00 nos dois formatos (D-16);
  - a tabela de códigos de rejeição comuns do manual 2.17 (seção de mensagens de erro da área de dados);
  - como reportar o resultado (abrir uma issue no fork com os XMLs anexados, **removendo dados sensíveis**).
- [x] **Step 3:** `php -l` no exemplo; execução **sem** `config.json` → mensagem amigável (nenhuma chamada de rede).
- [x] **Step 4:** commit `docs: roteiro de homologacao (P-05)`.

### Task T7.3: Remover o `legacy/` (D-08)

- [x] **Step 1:** `git rm -r legacy/`.
- [x] **Step 2:** atualizar as referências:
  - `tests/fixtures/xml/README.md` (origem do `retorno-resultado-v1.xml`);
  - `tests/fixtures/legacy/` ganha um `README.md` dizendo que os golden foram gerados pelo `legacy/gerar-golden.php` no commit `a71cdc0` (reproduzível com `git show a71cdc0:tests/fixtures/legacy/gerar.php`);
  - `schemes/README.md` (`PL_GNRE_100`).
- [x] **Step 3:** `rg -n "legacy/" --glob '!docs/refatoracao/**'` → só as referências intencionais acima; a suíte continua verde.
- [x] **Step 4:** commit `chore!: remove o codigo 0.x (legacy/)`.

### Task T7.4: Documentação final

**Files:** Modify `README.md`, `CHANGELOG.md`; Create `docs/MIGRACAO.md`

- [x] **Step 1: README** com: instalação; config JSON; certificado; um exemplo de uso de cada classe (`Tools`, `Make`, `MakeV1`, `MakeConsulta`, `Standardize`, `RetornoTxtV1`, `FromNFe`, `RegrasUf`); a impressão no sped-da; e notas práticas:
  - datas `AAAA-MM-DD`;
  - município IBGE sem a UF;
  - recibos de 14 dígitos;
  - algumas UFs podem exigir o valor total (tipo 21) além do principal (tipo 11);
  - PIX;
  - layout 1.00 nos dois formatos;
  - consultar as regras da UF no `sefazConsultaConfigUf`.
- [x] **Step 2: `docs/MIGRACAO.md`**, com a tabela 0.x → 1.0:
  - `Setup` → config JSON + `Certificate`;
  - `Lote`/`LoteV2`/`Guia` → `Make`/`MakeV1`;
  - `Send` + `Connection` → `Tools`;
  - `Consulta`/`ConfigUf` → `sefazConsultaLote`/`sefazConsultaConfigUf`;
  - `Parser\SefazRetorno` → `Standardize` + `RetornoTxtV1`;
  - `Render\Html`/`Pdf` → sped-da `Dagnre`;
  - `GnreHelper` → `FromNFe`.

  Também os campos `c01..c42` → tags 2.00, e as mudanças de comportamento da tabela de divergências do PROGRESSO (correções de bugs).
- [x] **Step 3:** CHANGELOG revisado (data do RC).
- [x] **Step 4:** commit `docs: README completo e guia de migracao 0.x -> 1.0`.

### Task T7.5: Preparar para o upstream e criar o RC

- [ ] **Step 1:** `composer.json` → `extra.branch-alias` `{"dev-master": "1.0.x-dev"}`; o badge do README aponta para `nfephp-org/sped-gnre`; o workflow continua aceitando push em `master`.
- [ ] **Step 2:** atualizar a issue #1 do fork com o texto final (resumo da 1.0, da revisão e do estado da homologação).
- [ ] **Step 3:** cadeia de verificação + push + CI das 7 versões verde.
- [ ] **Step 4:** tag anotada `v1.0.0-rc1` no fork (`git tag -a v1.0.0-rc1 -m ...` + `git push origin v1.0.0-rc1`).
- [ ] **Step 5:** fechar a F7 no ROADMAP e no PROGRESSO.

### Task T7.6: PRs e issue no nfephp-org (só com confirmação explícita do usuário)

- [ ] **Step 1 (usuário confirma):** issue no `nfephp-org/sped-gnre` com o texto da issue #1.
- [ ] **Step 2 (usuário confirma):** PR `betoeu/sped-gnre:refactor/v1` → `nfephp-org/sped-gnre:master`, com o resumo, o link para o ROADMAP/MIGRACAO e o estado da homologação (P-05 pendente).
- [ ] **Step 3 (usuário confirma):** PR `betoeu/sped-da:feature/gnre` → `nfephp-org/sped-da:master`, citando o PR do sped-gnre e o achado do `debugMode` + tc-lib-barcode 1.x.
- [ ] **Step 4:** registrar os links no PROGRESSO.
