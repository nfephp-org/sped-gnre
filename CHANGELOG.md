# Changelog

Todas as mudanças relevantes deste pacote são registradas aqui.

Projeto criado por Matheus Marabesi; refatoração para o padrão NFePHP (1.0) por Alberto Leal.
O formato segue o [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/) e o versionamento segue o [SemVer](https://semver.org/lang/pt-BR/).

## [1.0.0] - Unreleased

### Changed
- Namespace `Sped\Gnre` substituído por `NFePHP\Gnre` (pasta `src/`).
- Requer PHP >= 7.4 (testado de 7.4 a 8.5) e `nfephp-org/sped-common` ^5.1.
- Schemas atualizados para o pacote oficial do Manual de Integração GNRE v2.17 (`schemes/PL_GNRE_200`).
- Licença alinhada ao ecossistema NFePHP: LGPL-3.0-or-later, GPL-3.0-or-later ou MIT.
- CI migrada do Travis para o GitHub Actions.

### Removed
- Dependências `smarty/smarty` (5 vulnerabilidades conhecidas na 3.1.46), `dompdf/dompdf`, `laminas/*`, `sabberworm/php-css-parser`, `nfephp-org/sped-nfe` e o uso não declarado de `guzzlehttp/guzzle`.
- Código 0.x (`Sped\Gnre`), mantido só como referência em `legacy/` até a versão final.
