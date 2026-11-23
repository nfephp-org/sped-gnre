Olá, mantenedores e comunidade!

Refatorei o sped-gnre para o padrão dos demais pacotes do NFePHP (sped-nfe, sped-cte, sped-mdfe,
sped-da). Com isso, um mesmo sistema usa NF-e e GNRE com o mesmo certificado, a mesma configuração e
sem conflito de dependências. O trabalho está pronto como **release candidate** (`v1.0.0-rc1`) no fork,
na branch `refactor/v1`:
https://github.com/betoeu/sped-gnre/tree/refactor/v1

- Guia de migração 0.x → 1.0: [`docs/MIGRACAO.md`](https://github.com/betoeu/sped-gnre/blob/refactor/v1/docs/MIGRACAO.md)
- Decisões, fases e histórico: [`docs/refatoracao/ROADMAP.md`](https://github.com/betoeu/sped-gnre/blob/refactor/v1/docs/refatoracao/ROADMAP.md)

## O que muda na 1.0

- **Major** (não é compatível com a 0.x): namespace `NFePHP\Gnre`, estrutura `src/ tests/ schemes/ storage/`;
  a 0.x segue em `^0.3` para quem precisar.
- **PHP 7.4 a 8.5**, com CI nas 7 versões, igual ao sped-common e ao sped-nfe.
- `Tools` com os **5 serviços** do Portal GNRE sobre o `Certificate` + `SoapCurl` do sped-common, com
  config em JSON. Isso inclui a **consulta de guias já emitidas**, que não existia na 0.x. O pacote não
  grava mais a chave privada em disco.
- `Make` (lote 2.00, várias guias e itens), `MakeV1` (layout 1.00 dentro do lote 2.00, ou lote 1.00 puro),
  `MakeConsulta`, `Standardize` (preserva os atributos) e `RetornoTxtV1` (resultado TXT do lote 1.00).
- `Factories\FromNFe`: monta a guia de DIFAL ou ST direto do XML da NF-e.
- **Schemas** do pacote oficial do Manual de Integração **v2.17**. O `xsd/v2` antigo estava incompleto.
- **Impressão** movida para o sped-da (`NFePHP\DA\GNRE\Dagnre`), com código de barras I25 vetorial e
  QR Code PIX. Ela vai em PR separado no sped-da.
- **Dependências removidas:** Smarty 3.1 (5 vulnerabilidades conhecidas), Dompdf, laminas, sabberworm,
  sped-nfe e o uso não declarado de Guzzle.
- **Licença** alinhada aos demais pacotes: LGPL-3.0-or-later, GPL-3.0-or-later ou MIT (hoje é GPL/MIT).

## Qualidade

- 214 testes (golden XMLs gerados a partir da 0.x, comparados com C14N, e validação nos XSDs).
- phpcs PSR-12 + PHPCompatibility (7.4+) e phpstan nível 5.
- Revisão independente de código nos dois pacotes, com todos os achados críticos e importantes corrigidos.

## Pendente: homologação real

Não tenho certificado para o ambiente de homologação do Portal GNRE. O envelope SOAP segue o WSDL
oficial e o ACBr, mas **ainda não foi validado contra o servidor**. Por isso o lançamento proposto é
`1.0.0-rc1`, e a 1.0.0 sai depois de executado o
[roteiro de homologação](https://github.com/betoeu/sped-gnre/blob/refactor/v1/docs/HOMOLOGACAO.md).
Se alguém puder executá-lo, ajuda muito.

Feedback é muito bem-vindo, principalmente sobre:
1. a mudança de licença (preciso da concordância dos autores e mantenedores);
2. a estratégia de lançamento (1.0.0-rc1 agora, 0.x apenas para correções).

Obrigado!
