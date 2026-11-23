# Golden XMLs da 0.x

Mensagens geradas pelo código 0.x (`Sped\Gnre`), usadas para comparar o comportamento da 1.0
(`Make`, `MakeV1` e `Tools`), e não reprodutíveis sem ele.

- Gerador: `tests/fixtures/legacy/gerar.php` no commit `a71cdc0`
  (`git show a71cdc0:tests/fixtures/legacy/gerar.php`), executado com o código 0.x de `lib/`.
- `validos.txt`: golden cujos dados passam no XSD (`arquivo;nome-do-xsd`).
- Os `*-completo*` usam os dados do exemplo da 0.x, que violam o XSD (data dd/mm/aaaa, município com
  7 dígitos etc.). Eles servem para comparar estrutura, não dados.

O código 0.x foi removido na 1.0 (ver `docs/MIGRACAO.md`).
