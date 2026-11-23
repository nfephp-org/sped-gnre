# Roteiro de homologação — sped-gnre 1.0

Este roteiro confirma, no ambiente de **homologação** do Portal GNRE (`www.testegnre.pe.gov.br`),
os pontos que os testes automatizados não alcançam, porque eles não acessam a rede (pendência **P-05**
em `docs/refatoracao/PROGRESSO.md`):

| # | O que confirmar | Por quê |
|---|---|---|
| a | O envelope SOAP é aceito nos **5 serviços** | Decisão D-15: `gnreCabecMsg`, `gnreDadosMsg` e a action usam `http://www.gnre.pe.gov.br/webservice/<Servico>` nos dois ambientes, como o ACBr. A 0.x usava outros namespaces. |
| b | `versaoDados` do **ConfigUF** | A 1.0 envia a versão do config (`2.00`), como o ACBr; a 0.x enviava `1.00`. |
| c | `versaoDados` **2.00** nas consultas de guia | `GnreLoteRecepcaoConsulta`/`GnreResultadoLoteConsulta` não existiam na 0.x e o ACBr não os implementa. |
| d | Layout 1.00 nos dois formatos (D-16) | Guia 1.00 dentro do lote 2.00 e lote 1.00 puro. |

## Pré-requisitos

1. Certificado digital **A1** (`.pfx`) da empresa (o mesmo da NF-e serve). Certificados emitidos pela
   Certisign podem exigir a cadeia manual (ver README).
2. Serviço GNRE **liberado em homologação** para o CNPJ do certificado, junto à SEFAZ da UF.
3. PHP 7.4+ com `curl`, `openssl`, `soap`, `dom` e o projeto instalado (`composer install`).

## Configuração

```sh
cp examples/config.json.dist examples/config.json
```

Edite `examples/config.json`:

- `tpAmb`: `2` (o roteiro força homologação de qualquer forma);
- `cnpj`, `razaosocial` e `siglaUF`: os dados da empresa do certificado;
- `certificado`: nome do arquivo `.pfx` colocado em `examples/`;
- `senha`: senha do certificado.

Edite também `examples/dados-lote.php`, porque o lote precisa ser **aceito pela UF**: emitente = CNPJ do
certificado, uma UF favorecida e uma receita habilitadas, e datas futuras. Para descobrir as receitas e
exigências da UF, use o passo 1 abaixo.

`config.json`, `*.pfx` e as pastas `homologacao-*` estão no `.gitignore`: **nunca** versione o
certificado ou a senha.

## Execução

```sh
php examples/homologacao.php            # (a)(b)(c) lote 2.00
php examples/homologacao.php v1-200     # (d) guia 1.00 dentro do lote 2.00
php examples/homologacao.php v1-100     # (d) lote 1.00 puro (usa versaoDados 1.00)
```

O script executa, em ordem:

1. `sefazConsultaConfigUf` (UF do config) → **ponto (b)**;
2. `sefazEnviaLote` → recibo → **ponto (a)**;
3. `sefazConsultaLote`, repetido a cada 15 s até sair da situação 400/401;
4. `sefazEnviaLoteConsulta` com o código de barras da guia gerada → **ponto (c)**;
5. `sefazConsultaLoteConsulta`.

Cada passo grava `NN-servico-request.xml` (a mensagem enviada), `NN-servico-response.xml` (a resposta
SOAP bruta) e, se houver exceção, `NN-servico-erro.txt`, numa pasta `examples/homologacao-AAAAMMDD-HHMMSS`.
O resumo (código e descrição) aparece no terminal.

## Como interpretar

| Código | Significado | O que fazer |
|---|---|---|
| 100 | Lote recebido com sucesso | ok |
| 101 | Certificado inválido | conferir o `.pfx`, a validade e a cadeia |
| 102 | CNPJ não habilitado para uso do serviço | pedir a liberação à SEFAZ |
| 103 | Mensagem excedeu o tamanho máximo de 600 KB | dividir o lote |
| 153 / 155 / 199 | Quantidade de guias / itens / consultas acima do limite da UF | ver os limites no ConfigUF (`qtdMaximas`) |
| 154 | CNPJ bloqueado para uso do serviço | contato com a SEFAZ |
| 156 | XML de Lote inválido | anexar o `request.xml` ao relatório |
| 157 | Versão do cabeçalho difere da versão do XML de dados | **ponto (b)/(c)**: anotar o serviço e o `versaoDados` enviado |
| 197 / 198 | Erro ao recepcionar / exige HTTPS com certificado | repetir; conferir o certificado e o proxy |
| 301 / 302 | `gnreCabecMsg`/`versaoDados` inexistente no SOAP Header | **ponto (a)**: o servidor não reconheceu o cabeçalho (namespace) |
| 303 | Versão dos Dados não suportada | **ponto (b)/(c)** |
| 400 / 401 | Lote recebido, aguardando / em processamento | o script espera e consulta de novo |
| 402 | Lote processado com sucesso | ok; conferir as guias no resultado |
| 403 | Processado com pendência | ver `motivosRejeicao` de cada guia (regras da UF) |
| 404 | Erro no processamento do lote | reenviar |
| 450 | Consulta da configuração da UF realizada com sucesso | ok (ponto b) |
| 451–455 | Receita/UF não cadastrada ou não habilitada | escolher outra receita/UF |

### Se o ponto (a), (b) ou (c) falhar

Os namespaces, as actions e o `versaoDados` de cada serviço ficam em `storage/wsgnre.json`, e dá para
ajustar sem mexer no código:

- **ConfigUF rejeitado por versão (157/303):** troque `"GnreConfigUF": {"metodo": "consultar", "versaoDados": null}`
  por `"versaoDados": "1.00"` e execute de novo.
- **Cabeçalho não reconhecido (301/302) ou SOAP Fault:** anote o serviço e a resposta. A alternativa
  conhecida é o formato da 0.x (cabeçalho em `http://www.gnre.pe.gov.br/wsdl/processar` ou
  `.../wsdl/consultar`), que exige uma mudança no `Common\Tools`. Abra uma issue antes.

## Relatório

Abra uma issue no fork (`betoeu/sped-gnre`) com:

- o resultado de cada ponto (a)–(d): **ok** ou o código e a descrição recebidos;
- os arquivos `*-request.xml` e `*-response.xml` dos passos com problema, **removendo dados sensíveis**
  (CNPJ/CPF, razão social e endereço podem ser trocados por valores fictícios; nunca anexe o `.pfx`).

Com o resultado, a P-05 é fechada no `PROGRESSO.md`, e qualquer ajuste no `storage/wsgnre.json` vira
uma decisão registrada no ROADMAP.
