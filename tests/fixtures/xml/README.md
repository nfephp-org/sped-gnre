# Fixtures XML

| Arquivo | Origem | Alterações |
|---|---|---|
| `lote-consulta-guia.xml` | montado a partir de `lote_consulta_guia_v2.00.xsd` (2.17) | — |
| `retorno-recibo.xml` | montado a partir de `lote_gnre_recibo_v1.00.xsd`, no formato dos retornos reais (envelope SOAP + prefixo `ns1:`) | — |
| `retorno-config-uf.xml` | montado a partir de `config_uf_v1.00.xsd` (mínimo válido) | — |
| `retorno-resultado-v2.xml` | ACBr `trunk2/Testes/Recursos/GNRe/GuiaGNRe_Editada_Adicionando_Valores_FCP.xml` (rev. 49272), envolvido em envelope SOAP | `numeroRecibo` completado de 10 para 14 dígitos (o XSD 2.17 exige 14) |
| `retorno-resultado-v1.xml` | `exemplos/xml/resultado-consulta-gnre.xml` da 0.x (TXT posicional do lote 1.00) | só finais de linha (LF) |
| `nfe-difal.xml` | sped-nfe `tests/fixtures/xml/nfe_4.0.xml` (NF-e autorizada, com DIFAL `vICMSUFDest` 2.45) | CPF do destinatário `014345733812` (12 dígitos, inválido) trocado por `90483926086`; finais de linha (LF) |
