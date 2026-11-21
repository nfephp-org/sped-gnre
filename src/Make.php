<?php

namespace NFePHP\Gnre;

use DOMElement;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Strings;
use NFePHP\Gnre\Common\Tools as ToolsCommon;
use NFePHP\Gnre\Exception\RuntimeException;
use stdClass;

/**
 * Monta o XML do lote de GNRE no layout 2.00 (TLote_GNRE / TDadosGNRE versao="2.00").
 *
 * Uso: tagGuia() abre uma guia; tagEmitente() e tagItem() pertencem à última guia;
 * tagReferencia(), tagItemValor(), tagDestinatario() e tagCampoExtra() pertencem ao
 * último item. A ordem das chamadas é livre: monta() organiza os nós na sequência do XSD.
 * O XML gerado é validado no XSD por Tools::sefazEnviaLote().
 *
 * @package    NFePHP\Gnre
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class Make
{
    protected const VERSAO = '2.00';
    protected const TIPOS_GNRE = ['0', '1', '2'];
    protected const TIPOS_VALOR = ['11', '12', '21', '22', '31', '32', '41', '42', '51', '52'];
    protected const MAX_VALORES = 10;
    protected const MAX_ITENS = 100;
    protected const MAX_CAMPOS_EXTRAS = 3;

    /**
     * @var array<int, string>
     */
    public $errors = [];
    /**
     * Documento de trabalho onde os nós de cada tag são criados
     * @var Dom
     */
    protected $dom;
    /**
     * @var string
     */
    protected $xml = '';
    /**
     * Guias: ['guia' => stdClass, 'emitente' => ?DOMElement, 'itens' => array]
     * @var array<int, array<string, mixed>>
     */
    protected $guias = [];

    public function __construct()
    {
        $this->dom = new Dom('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = false;
    }

    /**
     * Abre uma nova guia (TDadosGNRE)
     *
     * @param stdClass $std ufFavorecida, tipoGnre, valorGNRE, dataPagamento, identificadorGuia
     */
    public function tagGuia(stdClass $std): DOMElement
    {
        $std = $this->equilizar($std, ['ufFavorecida', 'tipoGnre', 'valorGNRE', 'dataPagamento', 'identificadorGuia']);
        $std->tipoGnre = $std->tipoGnre === null || $std->tipoGnre === '' ? '0' : (string) $std->tipoGnre;
        $id = '[guia ' . (count($this->guias) + 1) . '] ';
        if (!in_array($std->tipoGnre, self::TIPOS_GNRE, true)) {
            $this->errors[] = $id . "tipoGnre inválido [{$std->tipoGnre}]: use 0, 1 ou 2.";
        }
        if ($std->valorGNRE !== null) {
            $std->valorGNRE = $this->decimal($std->valorGNRE, $id . 'valorGNRE');
        }
        $guia = $this->dom->createElement('TDadosGNRE');
        $this->dom->addChild($guia, 'ufFavorecida', $std->ufFavorecida, true, $id . 'UF favorecida');
        $this->dom->addChild($guia, 'tipoGnre', $std->tipoGnre, true, $id . 'Tipo da GNRE');
        $this->guias[] = ['guia' => $std, 'emitente' => null, 'itens' => []];
        $this->xml = '';
        return $guia;
    }

    /**
     * Dados do contribuinte emitente da guia atual
     *
     * @param stdClass $std CNPJ|CPF, IE, razaoSocial, endereco, municipio, uf, cep, telefone
     */
    public function tagEmitente(stdClass $std): DOMElement
    {
        $indice = $this->guiaAtual('tagEmitente');
        $std = $this->equilizar(
            $std,
            ['CNPJ', 'CPF', 'IE', 'razaoSocial', 'endereco', 'municipio', 'uf', 'cep', 'telefone']
        );
        $id = '[guia ' . ($indice + 1) . '] <contribuinteEmitente> ';
        $emitente = $this->dom->createElement('contribuinteEmitente');
        $emitente->appendChild($this->identificacao($std, $id));
        $this->dom->addChild($emitente, 'razaoSocial', $this->limitar($std->razaoSocial, 60), false, $id);
        $this->dom->addChild($emitente, 'endereco', $this->limitar($std->endereco, 60), false, $id);
        $this->dom->addChild($emitente, 'municipio', $std->municipio, false, $id);
        $this->dom->addChild($emitente, 'uf', $std->uf, false, $id);
        $this->dom->addChild($emitente, 'cep', $std->cep, false, $id);
        $this->dom->addChild($emitente, 'telefone', $std->telefone, false, $id);
        $this->guias[$indice]['emitente'] = $emitente;
        $this->xml = '';
        return $emitente;
    }

    /**
     * Abre um novo item (itensGNRE/item) na guia atual
     *
     * @param stdClass $std receita, detalhamentoReceita, documentoOrigem, documentoOrigemTipo, produto,
     *                      dataVencimento, convenio, numeroControle, numeroControleFecp
     */
    public function tagItem(stdClass $std): DOMElement
    {
        $indice = $this->guiaAtual('tagItem');
        $std = $this->equilizar($std, [
            'receita', 'detalhamentoReceita', 'documentoOrigem', 'documentoOrigemTipo', 'produto',
            'dataVencimento', 'convenio', 'numeroControle', 'numeroControleFecp',
        ]);
        $id = '[guia ' . ($indice + 1) . ', item ' . (count($this->guias[$indice]['itens']) + 1) . '] ';
        if (count($this->guias[$indice]['itens']) >= self::MAX_ITENS) {
            $this->errors[] = $id . 'máximo de ' . self::MAX_ITENS . ' itens por guia.';
        }
        if ($std->receita === null || $std->receita === '') {
            $this->errors[] = $id . 'Preenchimento Obrigatório! [receita]';
        }
        if ($std->documentoOrigem !== null && $std->documentoOrigem !== '' && empty($std->documentoOrigemTipo)) {
            $this->errors[] = $id . 'documentoOrigemTipo é obrigatório quando documentoOrigem é informado.';
        }
        $this->guias[$indice]['itens'][] = [
            'base' => $std,
            'referencia' => null,
            'valores' => [],
            'destinatario' => null,
            'camposExtras' => [],
        ];
        $this->xml = '';
        return $this->dom->createElement('item');
    }

    /**
     * Período de referência (item/referencia) do item atual
     *
     * @param stdClass $std periodo, mes, ano, parcela
     */
    public function tagReferencia(stdClass $std): DOMElement
    {
        [$g, $i] = $this->itemAtual('tagReferencia');
        $std = $this->equilizar($std, ['periodo', 'mes', 'ano', 'parcela']);
        $referencia = $this->dom->createElement('referencia');
        foreach (['periodo', 'mes', 'ano', 'parcela'] as $campo) {
            $this->dom->addChild($referencia, $campo, $std->$campo, false);
        }
        $this->guias[$g]['itens'][$i]['referencia'] = $referencia;
        $this->xml = '';
        return $referencia;
    }

    /**
     * Valor do item atual (item/valor[@tipo])
     *
     * @param stdClass $std tipo (11,12,21,22,31,32,41,42,51,52), valor
     */
    public function tagItemValor(stdClass $std): DOMElement
    {
        [$g, $i] = $this->itemAtual('tagItemValor');
        $std = $this->equilizar($std, ['tipo', 'valor']);
        $id = '[guia ' . ($g + 1) . ', item ' . ($i + 1) . '] <valor> ';
        $tipo = (string) $std->tipo;
        if (!in_array($tipo, self::TIPOS_VALOR, true)) {
            $this->errors[] = $id . "tipo inválido [$tipo]: use " . implode(', ', self::TIPOS_VALOR) . '.';
        }
        if (count($this->guias[$g]['itens'][$i]['valores']) >= self::MAX_VALORES) {
            $this->errors[] = $id . 'máximo de ' . self::MAX_VALORES . ' valores por item.';
        }
        $valor = $this->dom->createElement('valor', (string) $this->decimal($std->valor, $id . 'valor', true));
        $valor->setAttribute('tipo', $tipo);
        $this->guias[$g]['itens'][$i]['valores'][] = $valor;
        $this->xml = '';
        return $valor;
    }

    /**
     * Contribuinte destinatário do item atual (item/contribuinteDestinatario)
     *
     * @param stdClass $std CNPJ|CPF, IE, razaoSocial, municipio
     */
    public function tagDestinatario(stdClass $std): DOMElement
    {
        [$g, $i] = $this->itemAtual('tagDestinatario');
        $std = $this->equilizar($std, ['CNPJ', 'CPF', 'IE', 'razaoSocial', 'municipio']);
        $id = '[guia ' . ($g + 1) . ', item ' . ($i + 1) . '] <contribuinteDestinatario> ';
        $destinatario = $this->dom->createElement('contribuinteDestinatario');
        $destinatario->appendChild($this->identificacao($std, $id));
        $this->dom->addChild($destinatario, 'razaoSocial', $this->limitar($std->razaoSocial, 60), false, $id);
        $this->dom->addChild($destinatario, 'municipio', $std->municipio, false, $id);
        $this->guias[$g]['itens'][$i]['destinatario'] = $destinatario;
        $this->xml = '';
        return $destinatario;
    }

    /**
     * Campo extra do item atual (item/camposExtras/campoExtra), no máximo 3 por item
     *
     * @param stdClass $std codigo, valor
     */
    public function tagCampoExtra(stdClass $std): DOMElement
    {
        [$g, $i] = $this->itemAtual('tagCampoExtra');
        $std = $this->equilizar($std, ['codigo', 'valor']);
        $id = '[guia ' . ($g + 1) . ', item ' . ($i + 1) . '] <campoExtra> ';
        if (count($this->guias[$g]['itens'][$i]['camposExtras']) >= self::MAX_CAMPOS_EXTRAS) {
            $this->errors[] = $id . 'máximo de ' . self::MAX_CAMPOS_EXTRAS . ' campos extras por item.';
        }
        $campo = $this->dom->createElement('campoExtra');
        $this->dom->addChild($campo, 'codigo', $std->codigo, true, $id . 'código');
        $this->dom->addChild($campo, 'valor', $this->limitar($std->valor, 100), true, $id . 'valor');
        $this->guias[$g]['itens'][$i]['camposExtras'][] = $campo;
        $this->xml = '';
        return $campo;
    }

    /**
     * Monta o lote na ordem do XSD. Retorna false se houver erros de preenchimento.
     */
    public function monta(): bool
    {
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $lote = $dom->createElementNS(ToolsCommon::PORTAL, 'TLote_GNRE');
        $lote->setAttribute('versao', self::VERSAO);
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
     * Erros de preenchimento encontrados até agora
     *
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
        $std = $dados['guia'];
        $guia = $dom->createElement('TDadosGNRE');
        $guia->setAttribute('versao', self::VERSAO);
        $this->filho($dom, $guia, 'ufFavorecida', $std->ufFavorecida);
        $this->filho($dom, $guia, 'tipoGnre', $std->tipoGnre);
        if ($dados['emitente'] instanceof DOMElement) {
            $guia->appendChild($dom->importNode($dados['emitente'], true));
        }
        if (!empty($dados['itens'])) {
            $itens = $dom->createElement('itensGNRE');
            foreach ($dados['itens'] as $item) {
                $itens->appendChild($this->montaItem($dom, $item));
            }
            $guia->appendChild($itens);
        }
        $this->filho($dom, $guia, 'valorGNRE', $std->valorGNRE);
        $this->filho($dom, $guia, 'dataPagamento', $std->dataPagamento);
        $this->filho($dom, $guia, 'identificadorGuia', $std->identificadorGuia);
        return $guia;
    }

    /**
     * @param array<string, mixed> $item
     */
    protected function montaItem(Dom $dom, array $item): DOMElement
    {
        /** @var stdClass $std */
        $std = $item['base'];
        $no = $dom->createElement('item');
        $this->filho($dom, $no, 'receita', $std->receita);
        $this->filho($dom, $no, 'detalhamentoReceita', $std->detalhamentoReceita);
        if ($std->documentoOrigem !== null && $std->documentoOrigem !== '') {
            $doc = $dom->createElement('documentoOrigem', (string) $std->documentoOrigem);
            $doc->setAttribute('tipo', (string) $std->documentoOrigemTipo);
            $no->appendChild($doc);
        }
        $this->filho($dom, $no, 'produto', $std->produto);
        if ($item['referencia'] instanceof DOMElement) {
            $no->appendChild($dom->importNode($item['referencia'], true));
        }
        $this->filho($dom, $no, 'dataVencimento', $std->dataVencimento);
        foreach ($item['valores'] as $valor) {
            $no->appendChild($dom->importNode($valor, true));
        }
        $this->filho($dom, $no, 'convenio', $this->limitar($std->convenio, 30));
        if ($item['destinatario'] instanceof DOMElement) {
            $no->appendChild($dom->importNode($item['destinatario'], true));
        }
        if (!empty($item['camposExtras'])) {
            $campos = $dom->createElement('camposExtras');
            foreach ($item['camposExtras'] as $campo) {
                $campos->appendChild($dom->importNode($campo, true));
            }
            $no->appendChild($campos);
        }
        $this->filho($dom, $no, 'numeroControle', $std->numeroControle);
        $this->filho($dom, $no, 'numeroControleFecp', $std->numeroControleFecp);
        return $no;
    }

    /**
     * Grupo identificacao: CNPJ ou CPF (CNPJ prevalece) e IE
     */
    protected function identificacao(stdClass $std, string $id): DOMElement
    {
        $identificacao = $this->dom->createElement('identificacao');
        if (!empty($std->CNPJ)) {
            $this->dom->addChild($identificacao, 'CNPJ', $std->CNPJ, true, $id . 'CNPJ');
        } elseif (!empty($std->CPF)) {
            $this->dom->addChild($identificacao, 'CPF', $std->CPF, true, $id . 'CPF');
        } elseif (empty($std->IE)) {
            $this->errors[] = $id . 'informe CNPJ, CPF ou IE.';
        }
        $this->dom->addChild($identificacao, 'IE', $std->IE, false, $id . 'IE');
        return $identificacao;
    }

    /**
     * Acrescenta um filho só se o valor não for nulo nem vazio
     *
     * @param mixed $valor
     */
    protected function filho(Dom $dom, DOMElement $pai, string $nome, $valor): void
    {
        if ($valor === null || $valor === '') {
            return;
        }
        $pai->appendChild($dom->createElement($nome, (string) $valor));
    }

    /**
     * Formata um valor monetário com 2 casas; valor não numérico vira erro
     *
     * @param mixed $valor
     */
    protected function decimal($valor, string $descricao, bool $obrigatorio = false): ?string
    {
        if ($valor === null || $valor === '') {
            if ($obrigatorio) {
                $this->errors[] = "Preenchimento Obrigatório! $descricao";
            }
            return null;
        }
        if (!is_numeric($valor)) {
            $this->errors[] = "$descricao: valor numérico inválido [$valor] (use ponto como separador decimal).";
            return null;
        }
        return number_format((float) $valor, 2, '.', '');
    }

    /**
     * @param mixed $valor
     */
    protected function limitar($valor, int $tamanho): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }
        return mb_substr((string) $valor, 0, $tamanho);
    }

    /**
     * Índice da guia atual
     */
    protected function guiaAtual(string $tag): int
    {
        if (empty($this->guias)) {
            throw new RuntimeException("Chame tagGuia() antes de $tag().");
        }
        return count($this->guias) - 1;
    }

    /**
     * Índices [guia, item] do item atual
     *
     * @return array{0: int, 1: int}
     */
    protected function itemAtual(string $tag): array
    {
        $g = $this->guiaAtual($tag);
        if (empty($this->guias[$g]['itens'])) {
            throw new RuntimeException("Chame tagItem() antes de $tag().");
        }
        return [$g, count($this->guias[$g]['itens']) - 1];
    }

    /**
     * @param array<int, string> $possible
     */
    protected function equilizar(stdClass $std, array $possible): stdClass
    {
        return Strings::equilizeParameters(clone $std, $possible);
    }
}
