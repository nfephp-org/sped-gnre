<?php

namespace NFePHP\Gnre\Common;

use stdClass;

/**
 * Lê o resultado TXT (posicional) do processamento de um lote 1.00 puro, devolvido no
 * elemento <resultado> do TResultLote_GNRE quando a consulta é feita com versaoDados 1.00.
 *
 * Uso: RetornoTxtV1::ler((new Standardize($retorno))->toStd()->resultado)
 *
 * Registros: 0 = cabeçalho, 1 = guia, 2 = rejeição de campo, 9 = rodapé (mantido bruto).
 * As posições seguem o ACBr (PCNGNRE/pgnreRetConsResLoteGNRE.pas). Os valores vêm em
 * centavos e são devolvidos com 2 casas decimais.
 *
 * @package    NFePHP\Gnre\Common
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class RetornoTxtV1
{
    /**
     * campo => [início (base 1), tamanho]
     */
    protected const CABECALHO = [
        'tipoIdentificadorSolicitante' => [2, 1],
        'identificadorSolicitante' => [3, 14],
        'numeroProtocoloLote' => [17, 10],
        'ambiente' => [27, 1],
    ];

    protected const GUIA = [
        'sequencialGuia' => [2, 4],
        'situacaoGuia' => [6, 1],
        'ufFavorecida' => [7, 2],
        'receita' => [9, 6],
        'tipoDocEmitente' => [15, 1],
        'docEmitente' => [16, 16],
        'razaoSocialEmitente' => [32, 60],
        'enderecoEmitente' => [92, 60],
        'municipioEmitente' => [152, 50],
        'ufEmitente' => [202, 2],
        'cepEmitente' => [204, 8],
        'telefoneEmitente' => [212, 11],
        'tipoDocDestinatario' => [223, 1],
        'docDestinatario' => [224, 16],
        'municipioDestinatario' => [240, 50],
        'produto' => [290, 255],
        'docOrigem' => [545, 18],
        'convenio' => [563, 30],
        'informacoesComplementares' => [593, 300],
        'dataVencimento' => [893, 8],
        'dataLimitePagamento' => [901, 8],
        'periodoReferencia' => [909, 1],
        'mesAnoReferencia' => [910, 6],
        'parcela' => [916, 3],
        'valorPrincipal' => [919, 15],
        'atualizacaoMonetaria' => [934, 15],
        'juros' => [949, 15],
        'multa' => [964, 15],
        'representacaoNumerica' => [979, 48],
        'codigoBarras' => [1027, 44],
        'quantidadeVias' => [1071, 1],
        'numeroControle' => [1072, 16],
        'identificadorGuia' => [1088, 10],
        'guiaGeradaContingencia' => [1098, 1],
    ];

    protected const VALORES = ['valorPrincipal', 'atualizacaoMonetaria', 'juros', 'multa'];

    protected const REJEICAO = [
        'sequencialGuia' => [2, 4],
        'nomeCampo' => [6, 30],
        'codigoMotivo' => [36, 3],
        'descricaoMotivo' => [39, 355],
    ];

    /**
     * @return stdClass {cabecalho: ?stdClass, guias: stdClass[], rejeicoes: stdClass[], rodape: ?stdClass}
     */
    public static function ler(string $txt): stdClass
    {
        $retorno = new stdClass();
        $retorno->cabecalho = null;
        $retorno->guias = [];
        $retorno->rejeicoes = [];
        $retorno->rodape = null;
        $guiasPorSequencial = [];
        foreach (preg_split('/\r\n|\n|\r/', $txt) ?: [] as $linha) {
            $linha = rtrim(ltrim($linha), "\r\n");
            if (trim($linha) === '') {
                continue;
            }
            switch ($linha[0]) {
                case '0':
                    $retorno->cabecalho = self::campos($linha, self::CABECALHO);
                    break;
                case '1':
                    $guia = self::campos($linha, self::GUIA);
                    foreach (self::VALORES as $campo) {
                        $guia->$campo = self::centavos($guia->$campo);
                    }
                    $guia->rejeicoes = [];
                    $retorno->guias[] = $guia;
                    $guiasPorSequencial[$guia->sequencialGuia] = $guia;
                    break;
                case '2':
                    $rejeicao = self::campos($linha, self::REJEICAO);
                    $retorno->rejeicoes[] = $rejeicao;
                    if (isset($guiasPorSequencial[$rejeicao->sequencialGuia])) {
                        $guiasPorSequencial[$rejeicao->sequencialGuia]->rejeicoes[] = $rejeicao;
                    }
                    break;
                case '9':
                    $retorno->rodape = (object) ['conteudo' => rtrim($linha)];
                    break;
            }
        }
        return $retorno;
    }

    /**
     * @param array<string, array{0: int, 1: int}> $layout
     */
    protected static function campos(string $linha, array $layout): stdClass
    {
        $campos = new stdClass();
        foreach ($layout as $campo => [$inicio, $tamanho]) {
            $campos->$campo = trim((string) substr($linha, $inicio - 1, $tamanho));
        }
        return $campos;
    }

    protected static function centavos(string $valor): string
    {
        $numero = preg_replace('/\D/', '', $valor);
        return number_format(((int) ($numero === '' ? '0' : $numero)) / 100, 2, '.', '');
    }
}
