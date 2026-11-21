# Controle de progresso — sped-gnre 1.0

## Como retomar (qualquer dia)

1. `cd D:\betoeu\sped-gnre && git checkout refactor/v1 && git pull`
2. Leia **Estado atual** e **Próxima ação** abaixo.
3. Abra o plano da fase indicada e continue a partir do primeiro `- [ ]` não marcado.
4. Ao terminar cada tarefa: marque os `- [x]` no plano, atualize esta página, faça o commit (o plano e o PROGRESSO entram no mesmo commit da tarefa).
5. Ao parar no meio de uma tarefa: escreva em **Próxima ação** exatamente o passo em que parou e o que está pendente, e faça um commit `wip:` se houver código.

Para o Claude: "continue a refatoração do sped-gnre" → ler este arquivo, `ROADMAP.md` e o plano da fase atual.

## Estado atual

- **Fase:** F0 ✅ concluída. Próxima: F1
- **Tarefa:** T1.1, começando pelo Step 0 (pendência P-01)
- **Branch:** `refactor/v1` (criada a partir de `189fee1`, enviada ao origin)
- **Próxima ação:** resolver a **P-01** (qual pacote de XSD vira `schemes/`) e depois executar a T1.1.

## Pendências abertas

| ID | Pendência | Bloqueia | Proposta padrão |
|----|-----------|----------|-----------------|
| P-01 | O `xsd/v2` do repositório está **quebrado**: o `lote_gnre_v2.00.xsd` e o `lote_gnre_result_v2.00.xsd` incluem `dados_gnre_v1.00.xsd`, e o `tiposBasicoGNRE_v2.00.xsd` inclui `tiposBasicoGNRE_v1.00.xsd`; nenhum dos dois está na pasta. Além disso, 6 XSDs diferem do zip oficial do manual nos dois sentidos (o repositório tem o padrão `[0-9]{2}BR[0-9]{11}`; o zip tem os enums `c39..c42` e `maxOccurs=200`). | T1.1 Step 0 | Tentar baixar o pacote vigente do Portal GNRE. Se não for possível, usar o zip do manual (13 XSDs, completo) e aplicar por cima o padrão `BR` do repositório. |

## Decisões de execução (R-xx)

Decisões tomadas durante a execução, quando o plano não previa o caso. Formato: decisão, motivo, custo se estiver errada.

- **R-01** (T0.3): rodei a suíte legada com `--no-configuration`, porque o `stopOnFailure="true"` do phpunit.xml legado parava no 1º erro. Custo se errado: nenhum, só muda a forma de executar.
- **R-02** (T0.4): o `validos.txt` guarda só o **nome** do XSD, sem a pasta, para não depender da P-01. Custo se errado: 1 linha no `SchemesTest` (T1.3, já ajustado no plano).
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

## Divergências legado × 1.0 (preencher a partir da F3)

| Item | Legado | 1.0 | Fonte (manual/XSD) |
|------|--------|-----|--------------------|
| `identificadorGuia` (v2) | fixo `1` em todas as guias do lote | a conferir na F3 | Manual 2.01 |
| `item/valor` (v2) | `c06_valorPrincipal` enviado 2×, `tipo=11` e `tipo=21` | a conferir na F3 | Manual 2.01, tabela de tipos de valor |
| namespace em homologação (v2) | só o `gnreDadosMsg` muda para `testegnre`; o `TLote_GNRE` fica em `gnre.pe.gov.br` | a conferir na F2 | WSDL de homologação |
| `referencia/periodo` (v2) | fixo `0` | a conferir na F3 | Manual 2.01 |
