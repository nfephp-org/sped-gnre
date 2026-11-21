<?php

namespace NFePHP\Gnre\Common;

use NFePHP\Gnre\Exception\RuntimeException;
use stdClass;

/**
 * Resolve URL, namespace, action, método e versaoDados de cada serviço do
 * Portal GNRE a partir de storage/wsgnre.json (ver D-15 no ROADMAP).
 *
 * @package    NFePHP\Gnre\Common
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class Webservices
{
    /**
     * @var stdClass
     */
    private $dados;

    /**
     * @param string $json conteúdo de storage/wsgnre.json
     */
    public function __construct(string $json)
    {
        $dados = json_decode($json);
        if (!$dados instanceof stdClass || !isset($dados->namespace, $dados->url, $dados->servicos)) {
            throw new RuntimeException('Arquivo de webservices GNRE inválido.');
        }
        $this->dados = $dados;
    }

    /**
     * @param string $servico nome do serviço (ex.: GnreLoteRecepcao)
     * @param int $tpAmb 1 produção, 2 homologação
     * @return stdClass {url, namespace, action, metodo, versaoDados}
     */
    public function get(string $servico, int $tpAmb): stdClass
    {
        if (!isset($this->dados->servicos->$servico)) {
            throw new RuntimeException("Serviço GNRE desconhecido: [$servico].");
        }
        $ambiente = (string) $tpAmb;
        if (!isset($this->dados->url->$ambiente)) {
            throw new RuntimeException("Ambiente inválido: [$tpAmb]. Use 1 (produção) ou 2 (homologação).");
        }
        $config = $this->dados->servicos->$servico;
        $namespace = $this->dados->namespace . $servico;

        $ws = new stdClass();
        $ws->url = $this->dados->url->$ambiente . $servico;
        $ws->namespace = $namespace;
        $ws->action = '"' . $namespace . '"';
        $ws->metodo = (string) $config->metodo;
        $ws->versaoDados = isset($config->versaoDados) ? (string) $config->versaoDados : null;
        return $ws;
    }
}
