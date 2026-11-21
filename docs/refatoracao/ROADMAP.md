# sped-gnre 1.0 — Roadmap da refatoração

> Documento-mestre (faz o papel de especificação). Os planos detalhados de cada fase
> ficam em `planos/` e o estado atual em `PROGRESSO.md`. **Ao retomar o trabalho, leia
> primeiro `PROGRESSO.md`.**

## Objetivo

Colocar o `nfephp-org/sped-gnre` no mesmo padrão de sped-nfe, sped-cte, sped-mdfe e sped-da
(estrutura, sped-common, config JSON, `Tools`/`Make`/`Standardize`, CI), compatível com as
mesmas versões de PHP do ecossistema, para que um sistema use NF-e + GNRE (+ CT-e/MDF-e) com
um único certificado/config e sem conflito de dependências.

## Decisões registradas

| ID | Decisão | Origem |
|----|---------|--------|
| D-01 | PHP `>= 7.4`, CI de 7.4 a 8.5 (igual a sped-common/sped-nfe). Proibido no `src/`: union types, `mixed`, `match`, enums, promoção de construtor, `str_starts_with`/`str_contains`, `readonly`, nullsafe `?->`. | Usuário (2026-11-21) |
| D-02 | Nova versão **major 1.0.0**, namespace `NFePHP\Gnre\`, sem camada de compatibilidade com `Sped\Gnre\`. A 0.x continua no histórico/tag para quem não migrar. | Usuário |
| D-03 | A impressão da guia (DA) começa **dentro** do sped-gnre, já escrita no padrão do sped-da (`extends NFePHP\DA\Common\DaCommon`), para depois virar `NFePHP\DA\GNRE\Dagnre` no sped-da. | Usuário |
| D-04 | **Manter o layout 1.00** além do 2.00: `MakeV1` (1.00) e `Make` (2.00), escolhidos pelo `versao` do config. | Usuário |
| D-05 | Comunicação só via `NFePHP\Common\Soap\SoapCurl` (sem Guzzle, sem chave privada gravada pelo pacote). | Análise |
| D-06 | O lote GNRE não é assinado (só TLS mútuo): `Tools` usa `Certificate` + `SoapCurl`, não usa `Signer`. Conferir no Manual 2.01 na Fase 2. | Análise (a confirmar) |
| D-07 | ~~Licença mantida como está.~~ Substituída por D-11. | Análise |
| D-08 | O código antigo vai para `legacy/` na Fase 1 (sem autoload e fora do CI), serve só de referência e é apagado na Fase 7. O comportamento antigo fica registrado em XMLs de referência ("golden") gerados na Fase 0. | Análise |
| D-09 | Dependências finais do `require`: `nfephp-org/sped-common`, `nfephp-org/sped-da` (só por causa da DA, removida na Fase 8) e extensões. Saem Smarty, Dompdf, laminas, sabberworm e sped-nfe. | Análise |
| D-10 | A versão alvo é **1.0.0** (a última publicada é a 0.3.0; o próximo major pelo semver é a 1.0). Os layouts da SEFAZ continuam sendo 1.00 e 2.00, sem relação com a versão do pacote. A linha antiga é chamada de "0.x". | Usuário (2026-11-21) |
| D-11 | Licença igual à dos demais projetos: `LGPL-3.0-or-later`, `GPL-3.0-or-later`, `MIT` (LICENSE.md do sped-nfe). Mencionar a mudança na issue de anúncio (T1.4) para ciência dos autores e mantenedores. | Usuário (2026-11-21) |
| D-12 | A base é o remoto oficial (`origin/master` = `upstream/master` = `189fee1`). O trabalho local de 2024 (LoteV2Builder, LoteV2ConsultaBuilder, LoteV2Request, mudanças no gnre.tpl) fica só na branch local `backup/master-local-2024-08`, para consulta nas fases F2, F3 e F6. | Usuário (2026-11-21) |

Novas decisões: acrescentar uma linha com a data. Nunca apague linhas; para mudar uma decisão, marque a antiga como "substituída por D-xx".

## Arquitetura alvo

```
src/
  Common/Tools.php        base: config, certificado, SoapCurl, servico(), sendRequest(), isValid()
  Common/Webservices.php  lê storage/wsgnre.json
  Common/RegrasUf.php     lê storage/regras_uf.json (substitui switch getCodigoDoc/getNumDoc e Estados/*)
  Common/Standardize.php  retorno SEFAZ → stdClass/array/json
  Tools.php               sefazEnviaLote, sefazConsultaLote, sefazConsultaConfigUf
  Make.php                layout 2.00 (tags → TLote_GNRE)
  MakeV1.php              layout 1.00
  Factories/FromNFe.php   XML de NF-e autorizada → stdClass para o Make (substitui GnreHelper)
  DA/Dagnre.php           impressão (Fase 6) → migra para o sped-da (Fase 8)
  Exception/              ExceptionInterface, InvalidArgumentException, RuntimeException, DocumentsException
schemes/PL_GNRE_100/, schemes/PL_GNRE_200/   (vindos de xsd/v1 e xsd/v2)
storage/wsgnre.json, storage/regras_uf.json
tests/ (+ tests/fixtures/legacy = golden da Fase 0), examples/, docs/
```

### Contratos públicos (assinaturas fixadas; os planos das fases não podem divergir)

```php
// config JSON
{"tpAmb":2,"razaosocial":"...","cnpj":"99999999000191","siglaUF":"SP","versao":"2.00"}
// "cpf" pode substituir "cnpj"; "versao" ∈ {"1.00","2.00"}

namespace NFePHP\Gnre\Common;
class Tools {
    public string $lastRequest = '';  public string $lastResponse = '';
    public int $tpAmb = 2;            public string $versao = '2.00';
    public function __construct(string $configJson, \NFePHP\Common\Certificate $certificate);
    public function loadSoapClass(\NFePHP\Common\Soap\SoapInterface $soap): void;
    public function setEnvironment(int $tpAmb = 2): void;
    public function version(string $versao = ''): string;
    protected function servico(string $service): void;               // GnreLoteRecepcao|GnreResultadoLote|GnreConfigUF
    protected function sendRequest(string $body, array $parameters = []): string;
    protected function isValid(string $versao, string $xml, string $schema): bool;
}

namespace NFePHP\Gnre;
class Tools extends Common\Tools {
    public function sefazEnviaLote(string $xmlLote): string;              // retorno SOAP bruto
    public function sefazConsultaLote(string $recibo, ?int $tpAmb = null): string;
    public function sefazConsultaConfigUf(string $uf, string $receita): string;
}

class Make {                         // 2.00; cada tagGuia abre uma nova guia, as demais tags vão para a última
    public function tagGuia(\stdClass $std): \DOMElement;            // TDadosGNRE
    public function tagEmitente(\stdClass $std): \DOMElement;        // contribuinteEmitente
    public function tagItem(\stdClass $std): \DOMElement;            // itensGNRE/item
    public function tagReferencia(\stdClass $std): \DOMElement;      // item/referencia
    public function tagDestinatario(\stdClass $std): \DOMElement;    // item/contribuinteDestinatario
    public function tagItemValor(\stdClass $std): \DOMElement;       // item/valor[@tipo]
    public function tagCampoExtra(\stdClass $std): \DOMElement;      // item/camposExtras/campoExtra
    public function getXML(): string;                                // TLote_GNRE validado no XSD 2.00
    public function getErrors(): array;
}
class MakeV1 { public function tagGuia(\stdClass $std): \DOMElement; public function getXML(): string; public function getErrors(): array; }

namespace NFePHP\Gnre\Common;
class Standardize { public function whichIs(string $xml): string; public function toStd(?string $xml = null): \stdClass;
                    public function toArray(?string $xml = null): array; public function toJson(?string $xml = null): string; }
class RegrasUf { public static function tipoDocOrigem(string $uf, bool $difal = false): string;  // '10','22','24'
                 public static function tipoChaveDoc(string $uf): string; }                       // 'numero'|'chave'
```

## Fases

Status: ⬜ não iniciada · 🟨 em andamento · ✅ concluída · ⏸️ bloqueada

| Fase | Nome | Status | Plano detalhado | Critério de saída (tudo verificável) |
|------|------|--------|-----------------|---------------------------------------|
| F0 | Preparação e linha de base | ✅ | `planos/2026-11-21-fase-0-1-preparacao-e-infra.md` | branch `refactor/v1` no origin; finais de linha normalizados; resultado da suíte antiga registrado; golden XMLs em `tests/fixtures/legacy/`; fork do sped-da clonado |
| F1 | Infraestrutura e esqueleto | ✅ | idem | `composer validate --strict`, `composer phpcs`, `composer stan`, `composer test` verdes no PHP 8.3 local e CI verde de 7.4 a 8.5 |
| F2 | Comunicação (`Common\Tools`, `Tools`, `Webservices`) | ⬜ | escrever ao iniciar | os 3 serviços testados com `SoapFake`; envelope equivalente ao golden (XML canonicalizado); nenhuma chave gravada fora do temp do SoapCurl |
| F3 | `Make` 2.00 + `RegrasUf` | ⬜ | escrever ao iniciar | os cenários de `legacy/exemplos/xml` geram XML válido no XSD 2.00; diferenças em relação ao golden do `LoteV2` explicadas no PROGRESSO (bugs corrigidos) |
| F4 | `MakeV1` (layout 1.00) | ⬜ | escrever ao iniciar | igual ao golden do `Lote` v1 e válido no XSD 1.00 |
| F5 | `Standardize` + `Factories\FromNFe` | ⬜ | escrever ao iniciar | retornos de exemplo → std/array/json; NF-e de exemplo → guia válida |
| F6 | DA da guia (`DA\Dagnre`, padrão sped-da) | ⬜ | escrever ao iniciar | PDF gerado a partir do XML de resultado; código de barras conferido; teste com smalot/pdfparser |
| F7 | Docs, exemplos, limpeza e RC | ⬜ | escrever ao iniciar | `legacy/` removido; README + guia de migração 0.x→1.0; CHANGELOG; teste real em homologação (feito por você, com certificado); tag `v1.0.0-rc1` no fork; PR aberto no nfephp-org |
| F8 | Migrar a DA para o sped-da | ⬜ | escrever ao iniciar | PR no sped-da com `NFePHP\DA\GNRE\Dagnre`; o sped-gnre remove `DA/` e a dependência sped-da (1.1.0) |

Dependências: F0 → F1 → F2 → (F3 ∥ F4) → F5 → F6 → F7 → F8. F3 e F4 podem ser feitas em qualquer ordem.

## Riscos conhecidos

- O ambiente de homologação da SEFAZ-PE exige certificado e liberação da UF. Os testes automatizados usam só `SoapFake`; o teste real fica na F7, feito por você.
- Os golden XMLs podem carregar bugs do código antigo (ex.: `identificadorGuia` fixo em `1`, `valor` tipo 11 e 21 iguais, namespace `testegnre`). Na F3, cada divergência é conferida contra o Manual 2.01 (`docs/manuais/`) antes de decidir.
- Aceitação upstream: abrir uma issue no nfephp-org/sped-gnre anunciando a 1.0 antes do PR grande (tarefa da F1).
