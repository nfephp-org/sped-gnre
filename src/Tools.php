<?php

namespace NFePHP\Gnre;

use NFePHP\Common\Strings;
use NFePHP\Gnre\Common\Tools as ToolsCommon;

/**
 * Serviços do Portal GNRE (SEFAZ-PE).
 *
 * Todos os métodos sefaz* devolvem o retorno SOAP bruto; a mensagem enviada
 * fica em $lastRequest e a resposta em $lastResponse.
 *
 * @package    NFePHP\Gnre
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class Tools extends ToolsCommon
{
    /**
     * Envia um lote de GNRE (serviço GnreLoteRecepcao, assíncrono: retorna o recibo)
     *
     * @param string $xmlLote TLote_GNRE no layout da versão configurada
     * @return string retorno SOAP bruto
     */
    public function sefazEnviaLote(string $xmlLote): string
    {
        $request = $this->limparXml($xmlLote);
        $this->isValid($request, "lote_gnre_v{$this->versao}.xsd");
        $this->servico('GnreLoteRecepcao');
        return $this->sendRequest($request);
    }

    /**
     * Remove a declaração XML, quebras de linha e espaços entre tags
     */
    protected function limparXml(string $xml): string
    {
        return trim((string) Strings::clearXmlString($xml, true));
    }
}
