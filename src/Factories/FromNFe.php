<?php

namespace NFePHP\Gnre\Factories;

use DOMDocument;
use DOMElement;
use NFePHP\Gnre\Common\RegrasUf;
use NFePHP\Gnre\Exception\DocumentsException;
use NFePHP\Gnre\Exception\InvalidArgumentException;
use NFePHP\Gnre\Make;
use stdClass;

/**
 * Extrai de uma NF-e (nfeProc ou NFe) os dados para a guia GNRE: emitente, destinatário,
 * documento de origem e valores de DIFAL ou ST. Os atalhos guiaDifal() e guiaSt() devolvem
 * um Make já preenchido, que ainda aceita outras tags (ex.: tagCampoExtra, tagReferencia).
 *
 * A receita, o vencimento e as exigências de cada UF são de responsabilidade do usuário:
 * consulte Tools::sefazConsultaConfigUf() para as regras vigentes. O tipo e o número do
 * documento de origem usam as sugestões de RegrasUf.
 *
 * @package    NFePHP\Gnre\Factories
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class FromNFe
{
    /**
     * @var DOMDocument
     */
    protected $dom;
    /**
     * @var DOMElement
     */
    protected $infNFe;

    public function __construct(string $xmlNFe)
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        if (!@$this->dom->loadXML($xmlNFe)) {
            throw DocumentsException::wrongDocument(6);
        }
        $infNFe = $this->dom->getElementsByTagName('infNFe')->item(0);
        if (!$infNFe instanceof DOMElement) {
            throw DocumentsException::wrongDocument(1, 'esperada uma NF-e (nfeProc ou NFe)');
        }
        $this->infNFe = $infNFe;
    }

    /**
     * Dados do emitente da NF-e no formato de Make::tagEmitente(). A IE informada é a IEST (inscrição do
     * emitente na UF favorecida); a IE de origem (emit/IE) não é usada.
     */
    public function emitente(): stdClass
    {
        $emit = $this->grupo('emit');
        $ender = $this->grupo('enderEmit');
        $emitente = $this->identificacao($emit);
        // a IE da GNRE é a inscrição na UF favorecida (IEST da NF-e); sem ela, só CNPJ/CPF (regra 203 do manual 2.17)
        $emitente->IE = $this->valor($emit, 'IEST');
        $emitente->razaoSocial = $this->valor($emit, 'xNome');
        $endereco = trim($this->valor($ender, 'xLgr') . ', ' . $this->valor($ender, 'nro'), ', ');
        $bairro = $this->valor($ender, 'xBairro');
        $emitente->endereco = mb_substr($endereco . ($bairro !== '' ? ' - ' . $bairro : ''), 0, 60);
        $emitente->municipio = $this->municipio($this->valor($ender, 'cMun'));
        $emitente->uf = $this->valor($ender, 'UF');
        $emitente->cep = $this->valor($ender, 'CEP');
        $emitente->telefone = $this->valor($ender, 'fone');
        return $this->semVazios($emitente);
    }

    /**
     * Dados do destinatário da NF-e no formato de Make::tagDestinatario()
     */
    public function destinatario(): stdClass
    {
        $dest = $this->grupo('dest');
        $destinatario = $this->identificacao($dest);
        $destinatario->razaoSocial = mb_substr($this->valor($dest, 'xNome'), 0, 60);
        $destinatario->municipio = $this->municipio($this->valor($this->grupo('enderDest'), 'cMun'));
        return $this->semVazios($destinatario);
    }

    /**
     * UF de destino (UF favorecida da guia de DIFAL/ST)
     */
    public function ufDestino(): string
    {
        return $this->valor($this->grupo('enderDest'), 'UF');
    }

    /**
     * Chave de acesso (do protocolo ou, sem ele, do Id do infNFe)
     */
    public function chave(): string
    {
        $chave = $this->dom->getElementsByTagName('chNFe')->item(0);
        if ($chave instanceof DOMElement && trim($chave->nodeValue ?? '') !== '') {
            return trim((string) $chave->nodeValue);
        }
        return preg_replace('/^NFe/', '', $this->infNFe->getAttribute('Id')) ?? '';
    }

    /**
     * Número da NF-e (nNF)
     */
    public function numero(): string
    {
        return $this->valor($this->grupo('ide'), 'nNF');
    }

    /**
     * Totais relevantes do ICMSTot, com 2 casas: vNF, vICMSUFDest, vFCPUFDest, vST, vFCPST
     */
    public function valores(): stdClass
    {
        $total = $this->grupo('ICMSTot');
        $valores = new stdClass();
        foreach (['vNF', 'vICMSUFDest', 'vFCPUFDest', 'vST', 'vFCPST'] as $campo) {
            $valores->$campo = number_format((float) $this->valor($total, $campo), 2, '.', '');
        }
        return $valores;
    }

    /**
     * Documento de origem sugerido para a UF de destino: {documentoOrigem, documentoOrigemTipo}
     */
    public function documentoOrigem(bool $difal = false): stdClass
    {
        $uf = $this->ufDestino();
        return (object) [
            'documentoOrigem' => RegrasUf::tipoChaveDoc($uf) === 'chave' ? $this->chave() : $this->numero(),
            'documentoOrigemTipo' => RegrasUf::tipoDocOrigem($uf, $difal),
        ];
    }

    /**
     * Guia de diferencial de alíquota (vICMSUFDest, mais vFCPUFDest se houver)
     *
     * @param stdClass $opcoes receita, dataVencimento (obrigatórios), dataPagamento (padrão: o próprio
     *                         vencimento; o Portal calcula juros/multa por ela), identificadorGuia,
     *                         detalhamentoReceita, produto
     */
    public function guiaDifal(stdClass $opcoes): Make
    {
        $valores = $this->valores();
        return $this->guia($opcoes, $valores->vICMSUFDest, $valores->vFCPUFDest, true, 'DIFAL (vICMSUFDest)');
    }

    /**
     * Guia de substituição tributária (vST, mais vFCPST se houver)
     *
     * @param stdClass $opcoes receita, dataVencimento (obrigatórios), dataPagamento,
     *                         identificadorGuia, detalhamentoReceita, produto
     */
    public function guiaSt(stdClass $opcoes): Make
    {
        $valores = $this->valores();
        return $this->guia($opcoes, $valores->vST, $valores->vFCPST, false, 'ICMS ST (vST)');
    }

    protected function guia(stdClass $opcoes, string $principal, string $fcp, bool $difal, string $descricao): Make
    {
        foreach (['receita', 'dataVencimento'] as $obrigatorio) {
            if (empty($opcoes->$obrigatorio)) {
                throw new InvalidArgumentException("Informe \"$obrigatorio\" nas opções da guia.");
            }
        }
        if ((float) $principal <= 0) {
            throw new InvalidArgumentException("A NF-e não tem valor de $descricao a recolher.");
        }
        $total = (float) $principal + (float) $fcp;
        $make = new Make();
        $make->tagGuia((object) [
            'ufFavorecida' => $this->ufDestino(),
            'tipoGnre' => '0',
            'valorGNRE' => $total,
            'dataPagamento' => $opcoes->dataPagamento ?? $opcoes->dataVencimento,
            'identificadorGuia' => $opcoes->identificadorGuia ?? null,
        ]);
        $make->tagEmitente($this->emitente());
        $documento = $this->documentoOrigem($difal);
        $make->tagItem((object) [
            'receita' => $opcoes->receita,
            'detalhamentoReceita' => $opcoes->detalhamentoReceita ?? null,
            'documentoOrigem' => $documento->documentoOrigem,
            'documentoOrigemTipo' => $documento->documentoOrigemTipo,
            'produto' => $opcoes->produto ?? null,
            'dataVencimento' => $opcoes->dataVencimento,
        ]);
        $make->tagItemValor((object) ['tipo' => '11', 'valor' => $principal]);
        if ((float) $fcp > 0) {
            $make->tagItemValor((object) ['tipo' => '12', 'valor' => $fcp]);
        }
        $make->tagDestinatario($this->destinatario());
        return $make;
    }

    protected function grupo(string $nome): ?DOMElement
    {
        $no = $this->infNFe->getElementsByTagName($nome)->item(0);
        return $no instanceof DOMElement ? $no : null;
    }

    protected function valor(?DOMElement $grupo, string $nome): string
    {
        if ($grupo === null) {
            return '';
        }
        $no = $grupo->getElementsByTagName($nome)->item(0);
        return $no instanceof DOMElement ? trim((string) $no->nodeValue) : '';
    }

    /**
     * CNPJ ou CPF e IE do grupo emit/dest
     */
    protected function identificacao(?DOMElement $grupo): stdClass
    {
        $std = new stdClass();
        $cnpj = $this->valor($grupo, 'CNPJ');
        if ($cnpj !== '') {
            $std->CNPJ = $cnpj;
        } else {
            $std->CPF = $this->valor($grupo, 'CPF');
        }
        $std->IE = $this->valor($grupo, 'IE');
        return $std;
    }

    /**
     * Código IBGE do município sem os 2 dígitos da UF
     */
    protected function municipio(string $cMun): string
    {
        return strlen($cMun) === 7 ? substr($cMun, 2) : $cMun;
    }

    protected function semVazios(stdClass $std): stdClass
    {
        foreach (get_object_vars($std) as $campo => $valor) {
            if ($valor === '' || $valor === null) {
                unset($std->$campo);
            }
        }
        return $std;
    }
}
