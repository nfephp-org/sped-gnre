# Controle de progresso — sped-gnre 1.0

## Como retomar (qualquer dia)

1. `cd D:\betoeu\sped-gnre && git checkout refactor/v1 && git pull`
2. Leia **Estado atual** e **Próxima ação** abaixo.
3. Abra o plano da fase indicada e continue a partir do primeiro `- [ ]` não marcado.
4. Ao terminar cada tarefa: marque os `- [x]` no plano, atualize esta página, faça o commit (o plano e o PROGRESSO entram no mesmo commit da tarefa).
5. Ao parar no meio de uma tarefa: escreva em **Próxima ação** exatamente o passo em que parou e o que está pendente, e faça um commit `wip:` se houver código.

Para o Claude: "continue a refatoração do sped-gnre" → ler este arquivo, `ROADMAP.md` e o plano da fase atual.

## Estado atual

- **Fase:** F0, em andamento
- **Tarefa:** T0.2 (T0.1 e T0.5 concluídas)
- **Branch:** `refactor/v1` (criada a partir de `189fee1`, enviada ao origin)
- **Próxima ação:** T0.2, normalizar os finais de linha. Existem só 2 arquivos CRLF no oficial: `xsd/v1/tiposBasicoGNRE_v1.00.xsd` e `xsd/v2/lote_gnre_v2.00.xsd`.

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

## Diário de sessões

| Data | Fase/Tarefa | O que foi feito | Commit |
|------|-------------|-----------------|--------|
| 2026-11-21 | — | Análise do gnre + padrões (common, nfe, cte, mdfe, da); decisões D-01..D-10; ROADMAP e plano F0–F1 criados | (ver linha abaixo) |
| 2026-11-21 | T0.1, T0.5 | Usuário: base = remoto oficial, versão 1.0 aceita, licença igual aos demais (D-10..D-12). Backup local dos commits de 2024; master resetado para `189fee1`; fork do sped-da clonado; branch `refactor/v1` criada | docs: roadmap e plano |
| 2026-11-21 | T0.2 | `.gitattributes` (eol=lf + export-ignore); 4 XSDs renormalizados (eram CRLF ou mistos); 0 CRLF restante | chore: normaliza finais de linha |

## Divergências legado × 1.0 (preencher a partir da F3)

| Item | Legado | 1.0 | Fonte (manual/XSD) |
|------|--------|-----|--------------------|
