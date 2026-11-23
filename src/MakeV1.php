<?php

namespace NFePHP\Gnre;

use DOMElement;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Strings;
use NFePHP\Gnre\Common\MakeTrait;
use NFePHP\Gnre\Common\Tools as ToolsCommon;
use NFePHP\Gnre\Exception\InvalidArgumentException;
use NFePHP\Gnre\Exception\RuntimeException;
use stdClass;

/**
 * Monta guias GNRE no layout 1.00 (campos c01_..c42_), em dois formatos (D-16):
 *
 * - FORMATO_LOTE_200 (padrão): guia 1.00 dentro do lote 2.00
 *   (TLote_GNRE versao="2.00" / TDadosGNRE versao="1.00"), formato do pacote oficial 2.17.
 *   Envie com o config "versao": "2.00".
 * - FORMATO_LOTE_100: lote 1.00 puro (TLote_GNRE e TDadosGNRE sem versao), como na 0.x.
 *   Envie com o config "versao": "1.00".
 *
 * @package    NFePHP\Gnre
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class MakeV1
{
    use MakeTrait;

    public const FORMATO_LOTE_200 = '2.00';
    public const FORMATO_LOTE_100 = '1.00';

    protected const TIPOS_CAMPO_EXTRA = ['T', 'N', 'D'];
    protected const MAX_CAMPOS_EXTRAS = 3;
    protected const DECIMAIS = ['c06_valorPrincipal', 'c10_valorTotal'];

    /**
     * Campos simples de TDadosGNRE na ordem do XSD; os grupos são marcados com #
     */
    protected const CAMPOS = [
        'c01_UfFavorecida', 'c02_receita', 'c25_detalhamentoReceita', 'c26_produto',
        'c27_tipoIdentificacaoEmitente', '#c03_idContribuinteEmitente', 'c28_tipoDocOrigem', 'c04_docOrigem',
        'c06_valorPrincipal', 'c10_valorTotal', 'c14_dataVencimento', 'c15_convenio',
        'c16_razaoSocialEmitente', 'c17_inscricaoEstadualEmitente', 'c18_enderecoEmitente',
        'c19_municipioEmitente', 'c20_ufEnderecoEmitente', 'c21_cepEmitente', 'c22_telefoneEmitente',
        'c34_tipoIdentificacaoDestinatario', '#c35_idContribuinteDestinatario',
        'c36_inscricaoEstadualDestinatario', 'c37_razaoSocialDestinatario', 'c38_municipioDestinatario',
        'c33_dataPagamento', '#c05_referencia', '#c39_camposExtras', 'c42_identificadorGuia',
    ];

    /**
     * Grupo de identificação => campo que define o tipo (1 CNPJ, 2 CPF)
     */
    protected const IDENTIFICACOES = [
        'c03_idContribuinteEmitente' => 'c27_tipoIdentificacaoEmitente',
        'c35_idContribuinteDestinatario' => 'c34_tipoIdentificacaoDestinatario',
    ];

    /**
     * @var array<int, string>
     */
    public $errors = [];
    /**
     * @var Dom
     */
    protected $dom;
    /**
     * @var string
     */
    protected $formato;
    /**
     * @var string
     */
    protected $xml = '';
    /**
     * Guias: ['dados' => stdClass, 'referencia' => ?DOMElement, 'camposExtras' => DOMElement[]]
     * @var array<int, array<string, mixed>>
     */
    protected $guias = [];

    public function __construct(string $formato = self::FORMATO_LOTE_200)
    {
        if (!in_array($formato, [self::FORMATO_LOTE_200, self::FORMATO_LOTE_100], true)) {
            throw new InvalidArgumentException(
                "Formato inválido [$formato]: use MakeV1::FORMATO_LOTE_200 ou MakeV1::FORMATO_LOTE_100."
            );
        }
        $this->formato = $formato;
        $this->dom = $this->novoDom();
    }

    /**
     * Abre uma nova guia 1.00 com os campos c01_..c42_ (nomes das tags do XSD)
     */
    public function tagGuia(stdClass $std): DOMElement
    {
        $campos = array_map(function (string $campo) {
            return ltrim($campo, '#');
        }, self::CAMPOS);
        $std = $this->equilizar($std, $campos);
        $id = '[guia ' . (count($this->guias) + 1) . '] ';
        if ($std->c01_UfFavorecida === null || $std->c01_UfFavorecida === '') {
            $this->errors[] = $id . 'Preenchimento Obrigatório! [c01_UfFavorecida]';
        }
        foreach (self::DECIMAIS as $campo) {
            $std->$campo = $this->decimal($std->$campo, $id . $campo);
        }
        foreach (self::IDENTIFICACOES as $grupo => $campoTipo) {
            $temDocumento = $std->$grupo !== null && $std->$grupo !== '';
            if ($temDocumento && !in_array((string) $std->$campoTipo, ['1', '2'], true)) {
                $this->errors[] = $id . "$campoTipo deve ser 1 (CNPJ) ou 2 (CPF) quando $grupo é informado.";
            }
        }
        $this->guias[] = ['dados' => $std, 'referencia' => null, 'camposExtras' => []];
        $this->xml = '';
        return $this->dom->createElement('TDadosGNRE');
    }

    /**
     * Período de referência (c05_referencia) da guia atual
     *
     * @param stdClass $std periodo, mes, ano, parcela
     */
    public function tagReferencia(stdClass $std): DOMElement
    {
        $g = $this->guiaAtual('tagReferencia');
        $std = $this->equilizar($std, ['periodo', 'mes', 'ano', 'parcela']);
        $referencia = $this->dom->createElement('c05_referencia');
        foreach (['periodo', 'mes', 'ano', 'parcela'] as $campo) {
            $this->dom->addChild($referencia, $campo, $std->$campo, false);
        }
        $this->guias[$g]['referencia'] = $referencia;
        $this->xml = '';
        return $referencia;
    }

    /**
     * Campo extra (c39_camposExtras/campoExtra) da guia atual, no máximo 3
     *
     * @param stdClass $std codigo, tipo (T texto, N numérico, D data), valor
     */
    public function tagCampoExtra(stdClass $std): DOMElement
    {
        $g = $this->guiaAtual('tagCampoExtra');
        $std = $this->equilizar($std, ['codigo', 'tipo', 'valor']);
        $id = '[guia ' . ($g + 1) . '] <campoExtra> ';
        if (count($this->guias[$g]['camposExtras']) >= self::MAX_CAMPOS_EXTRAS) {
            $this->errors[] = $id . 'máximo de ' . self::MAX_CAMPOS_EXTRAS . ' campos extras por guia.';
        }
        if (!in_array((string) $std->tipo, self::TIPOS_CAMPO_EXTRA, true)) {
            $this->errors[] = $id . "tipo inválido [{$std->tipo}]: use T (texto), N (numérico) ou D (data).";
        }
        $campo = $this->dom->createElement('campoExtra');
        $this->dom->addChild($campo, 'codigo', $std->codigo, true, $id . 'código');
        $this->dom->addChild($campo, 'tipo', $std->tipo, true, $id . 'tipo');
        $this->dom->addChild($campo, 'valor', $this->limitar($std->valor, 100), true, $id . 'valor');
        $this->guias[$g]['camposExtras'][] = $campo;
        $this->xml = '';
        return $campo;
    }

    /**
     * Monta o lote no formato escolhido. Retorna false se houver erros de preenchimento.
     */
    public function monta(): bool
    {
        $dom = $this->novoDom();
        $lote = $dom->createElementNS(ToolsCommon::PORTAL, 'TLote_GNRE');
        if ($this->formato === self::FORMATO_LOTE_200) {
            $lote->setAttribute('versao', '2.00');
        }
        $dom->appendChild($lote);
        $guias = $dom->createElement('guias');
        $lote->appendChild($guias);
        foreach ($this->guias as $dados) {
            $guias->appendChild($this->montaGuia($dom, $dados));
        }
        $this->xml = Strings::clearXmlString((string) $dom->saveXML($lote), true);
        return empty($this->getErrors());
    }

    /**
     * XML do lote (TLote_GNRE), sem declaração XML
     *
     * @throws RuntimeException se não houver guias ou houver erros de preenchimento
     */
    public function getXML(): string
    {
        if (empty($this->guias)) {
            throw new RuntimeException('O lote não tem guias: chame tagGuia() ao menos uma vez.');
        }
        if (!$this->monta()) {
            throw new RuntimeException("O lote tem erros de preenchimento:\n" . implode("\n", $this->getErrors()));
        }
        return $this->xml;
    }

    /**
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return array_values(array_unique(array_merge($this->errors, $this->dom->errors)));
    }

    /**
     * @param array<string, mixed> $dados
     */
    protected function montaGuia(Dom $dom, array $dados): DOMElement
    {
        /** @var stdClass $std */
        $std = $dados['dados'];
        $guia = $dom->createElement('TDadosGNRE');
        if ($this->formato === self::FORMATO_LOTE_200) {
            $guia->setAttribute('versao', '1.00');
        }
        foreach (self::CAMPOS as $campo) {
            if ($campo === '#c05_referencia') {
                if ($dados['referencia'] instanceof DOMElement) {
                    $guia->appendChild($dom->importNode($dados['referencia'], true));
                }
            } elseif ($campo === '#c39_camposExtras') {
                if (!empty($dados['camposExtras'])) {
                    $grupo = $dom->createElement('c39_camposExtras');
                    foreach ($dados['camposExtras'] as $extra) {
                        $grupo->appendChild($dom->importNode($extra, true));
                    }
                    $guia->appendChild($grupo);
                }
            } elseif ($campo[0] === '#') {
                $this->identificacao($dom, $guia, substr($campo, 1), $std);
            } else {
                $this->filho($dom, $guia, $campo, $std->$campo);
            }
        }
        return $guia;
    }

    /**
     * Grupo c03/c35 com o filho CNPJ (tipo 1) ou CPF (tipo 2)
     */
    protected function identificacao(Dom $dom, DOMElement $guia, string $grupo, stdClass $std): void
    {
        $documento = $std->$grupo;
        if ($documento === null || $documento === '') {
            return;
        }
        $tipo = (string) $std->{self::IDENTIFICACOES[$grupo]};
        $no = $dom->createElement($grupo);
        $no->appendChild($dom->createElement($tipo === '2' ? 'CPF' : 'CNPJ', (string) $documento));
        $guia->appendChild($no);
    }

    protected function guiaAtual(string $tag): int
    {
        if (empty($this->guias)) {
            throw new RuntimeException("Chame tagGuia() antes de $tag().");
        }
        return count($this->guias) - 1;
    }
}
