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
| D-04 | **Manter o layout 1.00** além do 2.00: `MakeV1` (1.00) e `Make` (2.00), escolhidos pelo `versao` do config. **Reconfirmada em 2026-11-22** (P-02), mesmo com o XSD do lote 1.00 fora do pacote 2.17, "caso alguma SEFAZ use a versão antiga". O XSD 1.00 vem de `schemes/PL_GNRE_100`. | Usuário |
| D-05 | Comunicação só via `NFePHP\Common\Soap\SoapCurl` (sem Guzzle, sem chave privada gravada pelo pacote). | Análise |
| D-06 | O lote GNRE não é assinado (só TLS mútuo): `Tools` usa `Certificate` + `SoapCurl`, não usa `Signer`. **Confirmada:** o Manual 2.17 (seção 4, item d) exige só "SSL com autenticação mútua", e o ACBr também não assina o lote. | Análise + Manual 2.17 + ACBr |
| D-07 | ~~Licença mantida como está.~~ Substituída por D-11. | Análise |
| D-08 | O código antigo vai para `legacy/` na Fase 1 (sem autoload e fora do CI), serve só de referência e é apagado na Fase 7. O comportamento antigo fica registrado em XMLs de referência ("golden") gerados na Fase 0. | Análise |
| D-09 | Dependências finais do `require`: `nfephp-org/sped-common`, `nfephp-org/sped-da` (só por causa da DA, removida na Fase 8) e extensões. Saem Smarty, Dompdf, laminas, sabberworm e sped-nfe. | Análise |
| D-10 | A versão alvo é **1.0.0** (a última publicada é a 0.3.0; o próximo major pelo semver é a 1.0). Os layouts da SEFAZ continuam sendo 1.00 e 2.00, sem relação com a versão do pacote. A linha antiga é chamada de "0.x". | Usuário (2026-11-21) |
| D-11 | Licença igual à dos demais projetos: `LGPL-3.0-or-later`, `GPL-3.0-or-later`, `MIT` (LICENSE.md do sped-nfe). Mencionar a mudança na issue de anúncio (T1.4) para ciência dos autores e mantenedores. | Usuário (2026-11-21) |
| D-12 | A base é o remoto oficial (`origin/master` = `upstream/master` = `189fee1`). O trabalho local de 2024 (LoteV2Builder, LoteV2ConsultaBuilder, LoteV2Request, mudanças no gnre.tpl) fica só na branch local `backup/master-local-2024-08`, para consulta nas fases F2, F3 e F6. | Usuário (2026-11-21) |
| D-13 | O anúncio e a issue ficam **só no fork** (betoeu/sped-gnre#1) até a refatoração terminar. Só na F7 vão para o nfephp-org/sped-gnre (issue + PR). Substitui o passo de anúncio da T1.4 e o item de risco "abrir issue antes do PR grande". | Usuário (2026-11-22) |
| D-14 | **Incluir a consulta de guia** (serviços `GnreLoteRecepcaoConsulta` + `GnreResultadoLoteConsulta`, XSD `lote_consulta_guia_v2.00.xsd`), que o legado não implementava. Na F2: `sefazEnviaLoteConsulta()` e `sefazConsultaLoteConsulta()`; na F3: `MakeConsulta`. | Usuário (2026-11-22) |
| D-15 | **Envelope SOAP igual ao do ACBr** (`D:\AcbrDev\trunk2\Fontes\ACBrDFe`, rev. 49272, 2026-11-19), que funciona em produção: o `gnreCabecMsg` e o `gnreDadosMsg` usam o mesmo namespace `http://www.gnre.pe.gov.br/webservice/<Servico>` **nos dois ambientes**; a action é esse namespace; o `versaoDados` é o `versao` do config (as duas consultas de guia são fixas em `2.00`). Bate com o `targetNamespace` dos WSDLs. Diverge de propósito do legado (`wsdl/processar`, `wsdl/consultar`, `testegnre` em homologação) e do exemplo do manual (`wsdl/<Servico>`). Tudo fica em `storage/wsgnre.json`. Teste real em homologação na F7. | Análise: WSDL + ACBr + manual |
| D-16 | **Layout 1.00 nos dois formatos** (resolve a P-06): o `MakeV1` gera (a) a **guia 1.00 embutida no lote 2.00**, isto é, `TLote_GNRE versao="2.00"` com `TDadosGNRE versao="1.00"`, o formato do pacote oficial 2.17, enviado com config `versao` 2.00 (padrão); ou (b) o **lote 1.00 puro**, `TLote_GNRE` e `TDadosGNRE` sem `versao`, como na 0.x, enviado com config `versao` 1.00. Os campos `c01_...` são idênticos nos dois. | Usuário (2026-11-22) |
| D-17 | **`Standardize` sem perda de atributos**: o `json_encode` do SimpleXML (usado no sped-nfe e no sped-mdfe) descarta o atributo de elemento com texto, então `<valor tipo="12">1.82</valor>` viraria só `"1.82"`. Na GNRE o atributo define o tipo do valor e do documento de origem. O `Standardize` da GNRE usa um conversor DOM próprio: `attributes` + `value`, irmãos repetidos viram array, sem prefixos `ns1:`. A API pública continua a do ecossistema. | Análise (2026-11-22) |

Novas decisões: acrescentar uma linha com a data. Nunca apague linhas; para mudar uma decisão, marque a antiga como "substituída por D-xx".

## Arquitetura alvo

```
src/
  Common/Tools.php        base: config, certificado, SoapCurl, servico(), sendRequest(), isValid()
  Common/Webservices.php  lê storage/wsgnre.json
  Common/RegrasUf.php     lê storage/regras_uf.json (substitui switch getCodigoDoc/getNumDoc e Estados/*)
  Common/Standardize.php  retorno SEFAZ → stdClass/array/json
  Tools.php               sefazEnviaLote, sefazConsultaLote, sefazConsultaConfigUf, sefazEnviaLoteConsulta, sefazConsultaLoteConsulta
  MakeConsulta.php        lote de consultas de guia (TLote_ConsultaGNRE 2.00) — D-14
  Make.php                layout 2.00 (tags → TLote_GNRE)
  MakeV1.php              layout 1.00
  Factories/FromNFe.php   XML de NF-e autorizada → stdClass para o Make (substitui GnreHelper)
  DA/Dagnre.php           impressão (Fase 6) → migra para o sped-da (Fase 8)
  Exception/              ExceptionInterface, InvalidArgumentException, RuntimeException, DocumentsException
schemes/PL_GNRE_100/ (layout 1.00 histórico), schemes/PL_GNRE_200/ (pacote oficial v2.17)
storage/wsgnre.json, storage/regras_uf.json
tests/ (+ tests/fixtures/legacy = golden da Fase 0), examples/, docs/
```

### Contratos públicos (assinaturas fixadas; os planos das fases não podem divergir)

```php
// config JSON
{"tpAmb":2,"razaosocial":"...","cnpj":"99999999000191","siglaUF":"SP","versao":"2.00"}
// "cpf" pode substituir "cnpj"; "versao" ∈ {"1.00","2.00"}

namespace NFePHP\Gnre\Common;
class Webservices {                        // lê storage/wsgnre.json
    public function __construct(string $json);
    public function get(string $servico, int $tpAmb): \stdClass;   // {url, namespace, action, metodo, versaoDados: ?string}
}
class Tools {
    public \stdClass $config;
    public string $lastRequest = '';  public string $lastResponse = '';
    public int $tpAmb = 2;            public string $versao = '2.00';
    public function __construct(string $configJson, \NFePHP\Common\Certificate $certificate);
    public function loadSoapClass(\NFePHP\Common\Soap\SoapInterface $soap): void;
    public function setEnvironment(int $tpAmb = 2): void;
    public function version(string $versao = ''): string;
    protected function servico(string $servico, ?int $tpAmb = null): void;  // nome do serviço em wsgnre.json
    protected function sendRequest(string $request): string;               // envelopa, envia, grava lastRequest/lastResponse
    protected function isValid(string $xml, string $xsd, ?string $versao = null): bool; // lança ValidatorException
}

namespace NFePHP\Gnre;
class Tools extends Common\Tools {            // todos devolvem o retorno SOAP bruto
    public function sefazEnviaLote(string $xmlLote): string;                          // GnreLoteRecepcao
    public function sefazConsultaLote(string $recibo, array $opcoes = []): string;    // GnreResultadoLote; opcoes: incluirPDFGuias|incluirArquivoPagamento|incluirNoticias => S|N (só 2.00)
    public function sefazConsultaConfigUf(string $uf, string $receita = '', bool $courier = false): string; // GnreConfigUF
    public function sefazEnviaLoteConsulta(string $xmlLoteConsulta): string;          // GnreLoteRecepcaoConsulta (D-14)
    public function sefazConsultaLoteConsulta(string $recibo): string;                // GnreResultadoLoteConsulta (D-14)
}

class MakeConsulta { public function tagConsulta(\stdClass $std): \DOMElement; public function getXML(): string; public function getErrors(): array; } // D-14, F3

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
class MakeV1 {                       // layout 1.00 (D-16)
    public const FORMATO_LOTE_200 = '2.00';  // guia 1.00 dentro do lote 2.00 (padrão)
    public const FORMATO_LOTE_100 = '1.00';  // lote 1.00 puro (0.x)
    public function __construct(string $formato = self::FORMATO_LOTE_200);
    public function tagGuia(\stdClass $std): \DOMElement;        // campos c01_..c42_ (nomes das tags)
    public function tagReferencia(\stdClass $std): \DOMElement;  // c05_referencia: periodo, mes, ano, parcela
    public function tagCampoExtra(\stdClass $std): \DOMElement;  // c39_camposExtras/campoExtra: codigo, tipo (T|N|D), valor
    public function getXML(): string; public function getErrors(): array;
}

namespace NFePHP\Gnre\Common;
class Standardize { public function whichIs(string $xml): string; public function toStd(?string $xml = null): \stdClass;
                    public function toArray(?string $xml = null): array; public function toJson(?string $xml = null): string; } // D-17
class RetornoTxtV1 { public static function ler(string $txt): \stdClass; }   // resultado TXT do lote 1.00 puro (posições do ACBr)

namespace NFePHP\Gnre\Factories;
class FromNFe { public function __construct(string $xmlNFe); public function emitente(): \stdClass; public function destinatario(): \stdClass;
                public function documentoOrigem(bool $difal = false): \stdClass; public function valores(): \stdClass;
                public function guiaDifal(\stdClass $opcoes): \NFePHP\Gnre\Make; public function guiaSt(\stdClass $opcoes): \NFePHP\Gnre\Make; }
class RegrasUf { public static function tipoDocOrigem(string $uf, bool $difal = false): string;  // '10','22','24'
                 public static function tipoChaveDoc(string $uf): string; }                       // 'numero'|'chave'
```

## Fases

Status: ⬜ não iniciada · 🟨 em andamento · ✅ concluída · ⏸️ bloqueada

| Fase | Nome | Status | Plano detalhado | Critério de saída (tudo verificável) |
|------|------|--------|-----------------|---------------------------------------|
| F0 | Preparação e linha de base | ✅ | `planos/2026-11-21-fase-0-1-preparacao-e-infra.md` | branch `refactor/v1` no origin; finais de linha normalizados; resultado da suíte antiga registrado; golden XMLs em `tests/fixtures/legacy/`; fork do sped-da clonado |
| F1 | Infraestrutura e esqueleto | ✅ | idem | `composer validate --strict`, `composer phpcs`, `composer stan`, `composer test` verdes no PHP 8.3 local e CI verde de 7.4 a 8.5 |
| F2 | Comunicação (`Common\Tools`, `Tools`, `Webservices`) | ✅ | `planos/2026-11-22-fase-2-comunicacao.md` | os **5** serviços testados com `SoapFake`; envelope equivalente ao golden (XML canonicalizado); nenhuma chave gravada fora do temp do SoapCurl |
| F3 | `Make` 2.00 + `MakeConsulta` + `RegrasUf` | ✅ | `planos/2026-11-22-fase-3-make.md` | os cenários de `legacy/exemplos/xml` geram XML válido no XSD 2.00; diferenças em relação ao golden do `LoteV2` explicadas no PROGRESSO (bugs corrigidos) |
| F4 | `MakeV1` (layout 1.00, 2 formatos — D-16) | ✅ | `planos/2026-11-22-fase-4-makev1.md` | formato 1.00: igual ao golden do `Lote` v1 e válido em `PL_GNRE_100`; formato 2.00: válido em `PL_GNRE_200`; os dois enviados pela `Tools` (SoapFake) |
| F5 | `Standardize` + `RetornoTxtV1` + `Factories\FromNFe` | ✅ | `planos/2026-11-22-fase-5-retornos-nfe.md` | retornos de exemplo → std/array/json; NF-e de exemplo → guia válida |
| F6 | DA da guia (`DA\Dagnre` + `DA\DadosGuia`, padrão sped-da) | ✅ | `planos/2026-11-22-fase-6-dagnre.md` | PDF gerado a partir do XML de resultado; código de barras conferido; teste com smalot/pdfparser |
| F7 | Docs, exemplos, limpeza e RC | ⬜ | escrever ao iniciar | `legacy/` removido; README + guia de migração 0.x→1.0; CHANGELOG; teste real em homologação (feito por você, com certificado); tag `v1.0.0-rc1` no fork; issue (texto da betoeu/sped-gnre#1) e PR abertos no nfephp-org (D-13) |
| F8 | Migrar a DA para o sped-da | ⬜ | escrever ao iniciar | PR no sped-da com `NFePHP\DA\GNRE\Dagnre`; o sped-gnre remove `DA/` e a dependência sped-da (1.1.0) |

Dependências: F0 → F1 → F2 → (F3 ∥ F4) → F5 → F6 → F7 → F8. F3 e F4 podem ser feitas em qualquer ordem.

## Riscos conhecidos

- O ambiente de homologação da SEFAZ-PE exige certificado e liberação da UF. Os testes automatizados usam só `SoapFake`; o teste real fica na F7, feito por você.
- Os golden XMLs podem carregar bugs do código antigo (ex.: `identificadorGuia` fixo em `1`, `valor` tipo 11 e 21 iguais, namespace `testegnre`). Na F3, cada divergência é conferida contra o Manual 2.01 (`docs/manuais/`) antes de decidir.
- Aceitação upstream: por decisão D-13 o anúncio ao nfephp-org só acontece na F7, junto com o PR. Risco: mantenedores pedirem mudanças estruturais tarde. Mitigação: a issue #1 no fork já documenta a proposta.
