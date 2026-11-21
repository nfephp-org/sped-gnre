# Rascunho — issue de anúncio em nfephp-org/sped-gnre

> Revisar antes de publicar. Para abrir pela CLI (depois de revisado):
> `gh issue create --repo nfephp-org/sped-gnre --title "<título>" --body-file docs/refatoracao/rascunho-issue-upstream.md`
> (apague este bloco de citação antes).

**Título:** Proposta: sped-gnre 1.0 no padrão sped-common (PHP ≥ 7.4)

---

Olá, mantenedores e comunidade!

Estou trabalhando numa refatoração do sped-gnre para alinhá-lo aos demais pacotes do NFePHP
(sped-nfe, sped-cte, sped-mdfe, sped-da), de forma que um mesmo sistema consiga usar NF-e + GNRE
com o mesmo certificado e a mesma configuração, sem conflitos de dependências.

O trabalho está no fork, na branch `refactor/v1`:
https://github.com/betoeu/sped-gnre/tree/refactor/v1

O planejamento completo, com decisões, fases e andamento, está em
[`docs/refatoracao/ROADMAP.md`](https://github.com/betoeu/sped-gnre/blob/refactor/v1/docs/refatoracao/ROADMAP.md).

## Resumo da proposta

- **Versão 1.0.0** (major, quebra a compatibilidade com a 0.x): namespace `NFePHP\Gnre`, estrutura `src/ tests/ schemes/ storage/`.
- **PHP ≥ 7.4**, com CI de 7.4 a 8.5, igual ao sped-common e ao sped-nfe.
- Comunicação via `NFePHP\Common\Certificate` + `SoapCurl`, com config em JSON (`Tools`/`Make`/`Standardize`, como nos demais pacotes). O pacote deixa de gravar a chave privada em disco.
- **Schemas atualizados** para o pacote oficial do Manual de Integração **v2.17**. O `xsd/v2` atual do repositório está incompleto: inclui `dados_gnre_v1.00.xsd`, que não está na pasta.
- **Dependências removidas:** Smarty 3.1 (5 vulnerabilidades conhecidas), Dompdf, laminas, sabberworm, sped-nfe e o uso não declarado de Guzzle.
- **Impressão da guia** reescrita no padrão do sped-da (`DaCommon`), com a intenção de migrá-la depois para o próprio sped-da.
- **Licença alinhada aos demais pacotes:** LGPL-3.0-or-later, GPL-3.0-or-later ou MIT. Hoje o pacote é GPL-3.0-or-later/MIT. Gostaria da concordância dos autores e mantenedores para essa mudança.

## Situação atual

As fases de preparação e infraestrutura estão concluídas: esqueleto, phpcs (PSR-12 + PHPCompatibility),
phpstan nível 5 e phpunit, todos verdes no PHP 7.4 a 8.5. Os XMLs de referência gerados a partir do
comportamento da 0.x também estão registrados. As próximas fases são comunicação, montagem do lote 2.00,
padronização dos retornos e impressão da guia.

Feedback é muito bem-vindo, principalmente sobre:
1. a mudança de licença;
2. se ainda faz sentido manter o layout de lote 1.00, que não consta mais no pacote oficial v2.17;
3. a estratégia de lançamento (1.0.0 como novo major, com a 0.x apenas para correções).

Obrigado!
