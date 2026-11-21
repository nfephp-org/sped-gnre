# Controle de progresso — sped-gnre 1.0

## Como retomar (qualquer dia)

1. `cd D:\betoeu\sped-gnre && git checkout refactor/v1 && git pull`
2. Leia **Estado atual** e **Próxima ação** abaixo.
3. Abra o plano da fase indicada e continue a partir do primeiro `- [ ]` não marcado.
4. Ao terminar cada tarefa: marque os `- [x]` no plano, atualize esta página, faça o commit (o plano e o PROGRESSO entram no mesmo commit da tarefa).
5. Ao parar no meio de uma tarefa: escreva em **Próxima ação** exatamente o passo em que parou e o que está pendente, e faça um commit `wip:` se houver código.

Para o Claude: "continue a refatoração do sped-gnre" → ler este arquivo, `ROADMAP.md` e o plano da fase atual.

## Estado atual

- **Fase:** F4 (`MakeV1`, layout 1.00 em 2 formatos), em andamento
- **Tarefa:** T4.3 do plano `planos/2026-11-22-fase-4-makev1.md` (T4.1 e T4.2 concluídas)
- **Branch:** `refactor/v1` (criada a partir de `189fee1`, enviada ao origin)
- **Próxima ação:** T4.3, exemplo `make-lote-v1.php`, CHANGELOG, CI e fechamento da F4.

## Pendências abertas

| ID | Pendência | Bloqueia | Proposta padrão |
|----|-----------|----------|-----------------|
| P-01 | ✅ **Resolvida** (2026-11-21): pacote oficial do **Manual v2.17** baixado do Portal GNRE (`https://www.gnre.pe.gov.br:444/gnre/v/automacao/baixarManual`; a porta 443 devolve 403 para acesso automatizado). Tem 16 XSDs completos, sem include faltando, e valida os golden com dados corretos. Virou `schemes/PL_GNRE_200/`. | — | — |
| P-02 | ✅ Resolvida (2026-11-22): o usuário **manteve o layout 1.00** (D-04 reconfirmada) "caso alguma SEFAZ use a versão antiga". O XSD vem de `schemes/PL_GNRE_100`. | — | — |
| P-04 | ✅ Resolvida (D-13): a issue de anúncio está **só no fork**, em https://github.com/betoeu/sped-gnre/issues/1. Vai para o nfephp-org na F7. | — | — |
| P-03 | ✅ Resolvida (2026-11-22): consulta de guia incluída (D-14) com os 2 serviços do manual 2.17 (`GnreLoteRecepcaoConsulta` e `GnreResultadoLoteConsulta`). | — | — |
| P-06 | ✅ Resolvida (2026-11-22): o usuário escolheu **oferecer os dois formatos** (D-16). | — | — |
| P-05 | Pontos a confirmar em homologação **real** na F7, com o certificado do usuário: (a) o envelope D-15 nos 5 serviços; (b) `versaoDados` do ConfigUF (o ACBr usa a versão do config, o legado usava `1.00`); (c) `versaoDados` `2.00` nas consultas de guia (o ACBr não implementa esses serviços). | F7 | — |

## Decisões de execução (R-xx)

Decisões tomadas durante a execução, quando o plano não previa o caso. Formato: decisão, motivo, custo se estiver errada.

- **R-01** (T0.3): rodei a suíte legada com `--no-configuration`, porque o `stopOnFailure="true"` do phpunit.xml legado parava no 1º erro. Custo se errado: nenhum, só muda a forma de executar.
- **R-02** (T0.4): o `validos.txt` guarda só o **nome** do XSD, sem a pasta, para não depender da P-01. Custo se errado: 1 linha no `SchemesTest` (T1.3, já ajustado no plano).
- **R-04** (T1.1): `schemes/PL_GNRE_200/` = pacote oficial 2.17 sem alteração (só LF). O `xsd/v2` antigo foi removido e `xsd/v1` virou `schemes/PL_GNRE_100/`. Origem registrada em `schemes/README.md`. Custo se errado: trocar a pasta.
- **R-05** (T1.1): `docs/manuais/` guarda só o manual vigente (v2.17 PDF). Os manuais antigos (`exemplos/docs`) foram para `legacy/manuais-antigos/`. Custo se errado: nenhum.
- **R-06** (T1.3): o `DocumentsException` ficou só com os códigos 0–8, com textos adaptados à GNRE. Os códigos 9–16 do mdfe (CSC, QRCode, TXT) não existem na GNRE. Custo se errado: acrescentar códigos depois.
- **R-07** (T1.3): `phpcompatibility/php-compatibility` passou de `^9.3` para **`^10.0@alpha`**, igual ao sped-common. Verificado: a 9.3.5 (de 2019) **não detecta** union types nem `str_contains`, então não garantia a D-01; a 10.0.0-alpha2 detecta os dois. Custo se errado: depender de uma versão alpha só em dev (o sped-common faz o mesmo).
- **R-08** (T1.3): ~~usar `phpstan.neon.dist`~~ **revogada**. A sessão chegou a ver EPERM ao gravar `phpstan.neon*` (a escrita aparecia só com atraso). O arquivo final é o **`phpstan.neon`**, igual ao sped-nfe e ao sped-common, e entrou num commit à parte. Se o EPERM voltar, criar o arquivo pelo terminal do usuário.
- **R-09** (T1.3): `wrongDocument()` usa `new self()` em vez de `new static()` (o phpstan acusou "Unsafe usage of new static"). Custo se errado: uma subclasse recebe a classe-mãe.
- **R-10** (T2.2): propriedades da `Common\Tools` declaradas com `@var` (padrão sped-mdfe/sped-nfe), sem tipo nativo; o comportamento é o mesmo do contrato do ROADMAP. Custo se errado: tipar depois, sem quebra.
- **R-11** (T2.4): os helpers `golden()`/`c14n()` do `ToolsTestCase` passaram a usar **C14N exclusivo**. O C14N comum levava junto os `xmlns` do envelope SOAP legado, e com isso o teste do lote da T2.3 enviava o golden já "sujo" e comparava o golden com ele mesmo; a consulta e o ConfigUF falharam por isso. Agora a comparação considera só os namespaces usados. Custo se errado: nenhum (só afeta testes).
- **R-12** (T2.4): valores do usuário inseridos no XML (recibo, receita) passam por `htmlspecialchars(ENT_XML1)`, prevenindo injeção de XML; a validação de formato continua no XSD. Custo se errado: nenhum.
- **R-13** (T2.6): a regra `certs/` do `.gitignore` legado ignorava **qualquer** pasta `certs`, inclusive `tests/fixtures/certs`, e a CI falhou nas 7 versões por falta do certificado de teste. A regra passou a ser `/certs/` (só na raiz), e foi conferido que nenhum outro arquivo do projeto estava sendo ignorado. Custo se errado: nenhum.
- **Revisão final da F2:** autorrevisão (sem agente revisor, que só é criado a pedido do usuário). Uma revisão independente pode ser pedida antes da F3.
- **R-14** (T3.1): o `Make.php` da T3.1 já inclui as tags de item da T3.2, porque o desenho de slots por guia e item é um só e dividi-lo geraria retrabalho. A T3.2 continua TDD: os testes dela cobrem esse código e, se algum passar de primeira, isso é registrado como achado. Custo se errado: nenhum.
- **R-03** (T0.4): acrescentei 2 golden com dados válidos (`lote-v1-valido.xml`, `lote-v2-valido-prod.xml`), porque os dados de `exemplos/gerar-xml.php` violam o XSD. A F3 e a F4 precisam de uma referência que passe no XSD. Custo se errado: 2 arquivos a mais.

## Achados da T0.4 (golden)

8 golden em `tests/fixtures/legacy/`; a geração é determinística (2ª execução sem diff). Validados contra o pacote do zip do manual:

| Golden | Resultado | Motivo |
|---|---|---|
| lote-v1-completo / lote-v1-cpf-sem-extras | ✘ (só os dados) | receita com 7 dígitos (o XSD pede 6), detalhamento com 8, produto em texto, data dd/mm/aaaa, município com 7 dígitos (o XSD pede 5, IBGE sem UF), período 2014 (enum 0..5), tipo de campo extra `TXT`/`INT` (enum T/N/D) |
| lote-v2-completo-prod / -homolog | ✘ (só os dados) | município, receita e data, como acima. A **estrutura** é válida |
| lote-v1-valido, lote-v2-valido-prod | ✔ | também válidos contra `xsd/v1` do repositório (v1) |
| consulta-lote-prod, config-uf-prod | ✔ | |

Conclusão: a estrutura gerada pelo legado (v1 e v2) é válida; os exemplos e a documentação antigos ensinam formatos de dado errados (data dd/mm/aaaa, município com 7 dígitos). Isso vira um item do guia de migração (F7).

## Checagem de forks (atualizada em 2026-11-21)

| Repositório | Fork betoeu | Clone local | Necessário em |
|---|---|---|---|
| sped-gnre | ✅ github.com/betoeu/sped-gnre | `D:\betoeu\sped-gnre` (origin=fork, upstream=nfephp-org); `master` = oficial `189fee1` | F0 |
| sped-da | ✅ github.com/betoeu/sped-da | `D:\betoeu\sped-da` (origin=fork, upstream=nfephp-org); `master` = oficial `6e4b3e7` | F6 (dependência) e F8 (PR) |
| sped-common | ❌ (não precisa) | oficial em `D:\NfePhpDev\sped-common` | só se precisar de mudança no common (não previsto) |
| sped-nfe / sped-cte | ❌ (não precisa) | oficiais | só leitura (referência) |
| sped-mdfe | ✅ (já existe) | `D:\betoeu\sped-mdfe` | não necessário |

`gh` autenticado como betoeu. As pastas `D:\betoeu\*` estão no workspace.

Achados do fork sped-gnre:
- Sincronizado a partir do remoto oficial (D-12). Os 2 commits locais antigos (`5f5435d`, `1f667c3`, com LoteV2Builder, LoteV2ConsultaBuilder, LoteV2Request e mudanças no gnre.tpl) estão guardados **só localmente** na branch `backup/master-local-2024-08`. Para consultar: `git show backup/master-local-2024-08:lib/Sped/Gnre/Sefaz/LoteV2Request.php`.
- O `LoteV2` oficial não tem construtor nem Guzzle (usa `setAmbienteDeTesteV2(bool)`).

Ambiente local: PHP 8.3.6, Composer 2.8.6 (Windows). O PHP 7.4 é coberto pelo CI.

## Linha de base F0 (T0.3, 2026-11-21, PHP 8.3.6 / OpenSSL 3.0.13 / PHPUnit 9.6.39)

- `composer update` sem erro (lock reconciliado; Smarty v3.1.46 instalado).
- `composer audit`: **5 advisories no smarty/smarty 3.1.46** (2 high: CVE-2024-35226, CVE-2023-28447; 3 medium: CVE-2026-62993, CVE-2026-62992, CVE-2018-25047). É mais um motivo para remover o Smarty na 1.0 (D-09).
- O `phpunit.xml.dist` legado tem `stopOnFailure="true"` e para no 1º erro. Linha de base completa com: `vendor/bin/phpunit --no-configuration --bootstrap vendor/autoload.php testes`
- **Resultado: 70 testes, 67 ✔, 2 erros, 1 warning.**
  - ✘ `TestCertificatePfx::testPassarAoCriarChavePrivadaApartirDoCertificado`: "Trying to access array offset on null" (`CertificatePfx.php:71`). O mock de `open()` devolve null e o código acessa `['pkey']`; desde o PHP 7.4 isso vira notice e o phpunit converte em exceção. Defeito do teste.
  - ✘ `TestCertificatePfx::testPassarAoCriarCertificadoPemApartirDoCertificado`: mesma causa (`CertificatePfx.php:86`).
  - ⚠ Nome da classe `TestCertificatePfx` diferente do nome do arquivo `CertificatePfxTest.php` (deprecated no PHPUnit 9).
  - Nenhum teste cobre `LoteV2` (o layout 2.00 em uso hoje).
- Não corrigido (D-08: o legado fica congelado).

## Diário de sessões

| Data | Fase/Tarefa | O que foi feito | Commit |
|------|-------------|-----------------|--------|
| 2026-11-21 | — | Análise do gnre + padrões (common, nfe, cte, mdfe, da); decisões D-01..D-10; ROADMAP e plano F0–F1 criados | (ver linha abaixo) |
| 2026-11-21 | T0.1, T0.5 | Usuário: base = remoto oficial, versão 1.0 aceita, licença igual aos demais (D-10..D-12). Backup local dos commits de 2024; master resetado para `189fee1`; fork do sped-da clonado; branch `refactor/v1` criada | docs: roadmap e plano |
| 2026-11-21 | T0.2 | `.gitattributes` (eol=lf + export-ignore); 4 XSDs renormalizados (eram CRLF ou mistos); 0 CRLF restante | chore: normaliza finais de linha |
| 2026-11-21 | T0.3 | Lock legado atualizado; suíte antiga 70 testes: 67 ✔ e 2 erros (teste do CertificatePfx no PHP ≥ 7.4); 5 CVEs no Smarty | chore: atualiza lock legado |
| 2026-11-21 | T0.4 | 8 golden determinísticos; estrutura legada válida, dados de exemplo inválidos; XSD v2 do repositório quebrado (P-01); R-01..R-03. **F0 concluída** | test: golden XMLs |
| 2026-11-21 | T1.1 | P-01 resolvida com o manual oficial v2.17; legado em `legacy/`; `schemes/PL_GNRE_100` e `PL_GNRE_200`; arquivos de Travis, Coveralls e Docker removidos; P-02 e P-03 abertas | refactor!: congela codigo 0.x |
| 2026-11-21 | — | Sessão pausada pelo usuário no início da T1.2 | docs: ponto de retomada |
| 2026-11-22 | T1.2 | Novo `composer.json` (php>=7.4, sped-common ^5.1, licença LGPL/GPL/MIT); install limpo; `composer audit`: 0 vulnerabilidades (antes 5 no Smarty) | build!: composer 1.0 |
| 2026-11-22 | T1.3 | Exceções `NFePHP\Gnre\Exception\*`, phpcs (PSR-12 + PHPCompatibility 10), phpstan nível 5, phpunit; `SchemesTest` com 53 testes ✔ (XSDs, includes, 4 golden válidos, exceções). R-06..R-09 | feat: esqueleto NFePHP\Gnre + chore: phpstan.neon |
| 2026-11-22 | T1.4 | `ci.yml` (7.4–8.5, validate, CRLF, phpcs, stan, test); **CI verde nas 7 versões** (run 44904014382); README, LICENSE.md (LGPLv3), CHANGELOG; rascunho da issue. **F1 concluída** | ci: pipeline 7.4-8.5 |
| 2026-11-22 | T1.4 | Crédito a Alberto Leal (README, composer authors, CHANGELOG). D-13: issue só no fork (#1, issues ativadas no fork) | docs: credita + docs: D-13 |
| 2026-11-22 | plano F2 | Usuário: manter o layout 1.00 (P-02) e incluir a consulta de guia (P-03). Pesquisa: manual 2.17 (5 serviços, métodos, PDF da guia via `incluirPDFGuias`), WSDLs legados, **ACBr trunk2 rev. 49272** (namespaces). Decisões D-14 e D-15; contrato da `Tools` atualizado; plano da F2 escrito | docs: plano da F2 |
| 2026-11-22 | T2.1 | `storage/wsgnre.json` (5 serviços) + `Common\Webservices`; 6 testes ✔ | feat(common): Webservices |
| 2026-11-22 | T2.2 | `Common\Tools` (config validado, ambiente, versão, SoapCurl, servico, sendRequest, isValid) + `SoapFake` + `ToolsTestCase` + certificado de teste; suíte 70 ✔ | feat(common): Tools base |
| 2026-11-22 | T2.3 | `Tools::sefazEnviaLote` (1.00 e 2.00): a mensagem é igual ao golden (C14N), o envelope segue D-15 e um XML inválido não é enviado; suíte 75 ✔ | feat: sefazEnviaLote |
| 2026-11-22 | T2.4 | `sefazConsultaLote` (opções incluirPDFGuias/ArquivoPagamento/Noticias só no 2.00, na ordem do XSD) e `sefazConsultaConfigUf` (courier); mensagens iguais ao golden; R-11 e R-12; suíte 88 ✔ | feat: sefazConsultaLote e sefazConsultaConfigUf |
| 2026-11-22 | T2.5 | Consulta de guia: `sefazEnviaLoteConsulta` (XSD lote_consulta_guia 2.00) e `sefazConsultaLoteConsulta`, sempre com versaoDados 2.00 e pasta PL_GNRE_200, mesmo com o config em 1.00; suíte 94 ✔ | feat: consulta de guia |
| 2026-11-22 | T2.6 | Exemplos dos 5 serviços (`examples/`), CHANGELOG `Added`; CI falhou (certificado ignorado pelo `.gitignore`, R-13), corrigido; **CI verde nas 7 versões** (run com sha 899430e). **F2 concluída** | docs: exemplos + test: certificado |
| 2026-11-22 | plano F3 | Pesquisa: XSD 2.17 (ordem dos elementos), ACBr `pgnreGNREW.GerarXml2`, golden. Achados: o legado descartava detalhamentoReceita/produto/IE do emitente/convenio e duplicava o valor principal; o layout 1.00 vive dentro do lote 2.00 (P-06, para a F4). Plano da F3 escrito | docs: plano da F3 |
| 2026-11-22 | T3.1 | `Make`: guia, emitente, montagem idempotente na ordem do XSD, erros em `getErrors()`, `RuntimeException` sem guia ou com erro; 10 testes; suíte 104 ✔ | feat: Make (guia, emitente) |
| 2026-11-22 | T3.2 | 12 testes de item, incluindo um idêntico ao golden do legado (C14N). Eles passaram de primeira (R-14), e por isso foi feita uma **verificação por mutação**: trocar a ordem referência/valor e aceitar valor não numérico derrubaram 2 e 1 testes, respectivamente. Base `MakeTestCase`; suíte 116 ✔ | feat: Make (itens) |
| 2026-11-22 | T3.3 | `MakeConsulta` (7 tipos de consulta, emitenteId por CNPJ/CPF/IE, máx. 50, docOrigem com tipo); o XML gerado é idêntico à fixture da F2; 9 testes; suíte 125 ✔ | feat: MakeConsulta |
| 2026-11-22 | T3.4 | `RegrasUf` + `storage/regras_uf.json` (sugestões da 0.x; o oficial é o ConfigUF); as 27 UFs conferidas contra o legado; suíte 155 ✔ | feat(common): RegrasUf |
| 2026-11-22 | T3.5 | Exemplos com `Make`/`MakeConsulta` (`make-lote.php` gera um XML válido no XSD sem certificado), CHANGELOG Added/Fixed; **CI verde nas 7 versões** (sha 4aa8132). **F3 concluída**. P-06 aberta | docs: exemplos F3 |
| 2026-11-22 | plano F4 | Usuário: os dois formatos do 1.00 (D-16). Os dois XSDs têm a mesma sequência de campos `c01..c42`; muda só o invólucro. Achado: o legado descartava `periodo` 0. Plano da F4 escrito | docs: plano da F4 |
| 2026-11-22 | T4.1 | `MakeV1` (2 formatos): o lote puro é idêntico ao golden v1 (C14N) e o embutido é válido no XSD 2.17; `Common\MakeTrait` extraído do `Make` (testes do Make seguem verdes); 13 testes; suíte 168 ✔ | feat: MakeV1 |
| 2026-11-22 | T4.2 | Integração MakeV1 + Tools nos 2 formatos (versaoDados 2.00/1.00; formato trocado → ValidatorException sem envio). Passou de primeira (sem código novo, como previsto); mutação (pasta de schemas fixa em PL_GNRE_100) derrubou 2 testes; suíte 172 ✔ | test: envio layout 1.00 |

## Divergências legado × 1.0 (preencher a partir da F3)

| Item | Legado | 1.0 | Fonte (manual/XSD) |
|------|--------|-----|--------------------|
| `identificadorGuia` (v2) | fixo `1` em todas as guias do lote | informado pelo usuário em `tagGuia` (opcional) | XSD 2.17 + ACBr `GerarXml2` |
| `item/valor` (v2) | `c06_valorPrincipal` enviado 2×, como `tipo=11` **e** `tipo=21` | só os valores informados em `tagItemValor` (tipos 11–52), como o ACBr | XSD 2.17 (enum de tipos) + ACBr |
| Envelope SOAP (todos os serviços) | `gnreCabecMsg` em `.../wsdl/processar` ou `.../wsdl/consultar`; `gnreDadosMsg` em `.../webservice/<Servico>`, trocado para `testegnre` em homologação; ConfigUF com prefixo `gnr:` | **D-15**: cabeçalho, corpo e action em `http://www.gnre.pe.gov.br/webservice/<Servico>` nos **dois** ambientes; só a URL muda | WSDL (`targetNamespace`) + ACBr trunk2 rev. 49272; confirmar em homologação real (P-05) |
| `referencia/periodo` (v2) | fixo `0` | informado pelo usuário em `tagReferencia` | XSD 2.17 |
| Campos descartados (v2) | o `LoteV2` ignorava `detalhamentoReceita`, `produto`, a IE do emitente e o `convenio`, mesmo quando preenchidos | o `Make` envia todos, na ordem do XSD | XSD 2.17 + golden `lote-v2-valido-prod` |
| `periodo` = 0 (v1) | `Estados/Padrao` testava `if ($guia->periodo)` e descartava o período mensal (`0`) | `MakeV1` envia o período sempre que informado | XSD (enum 0..5) |
