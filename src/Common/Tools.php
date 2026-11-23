<?php

namespace NFePHP\Gnre\Common;

use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Common\Soap\SoapInterface;
use NFePHP\Common\Validator;
use NFePHP\Gnre\Exception\DocumentsException;
use NFePHP\Gnre\Exception\InvalidArgumentException;
use NFePHP\Gnre\Exception\RuntimeException;
use SoapHeader;
use stdClass;

/**
 * Classe base para a comunicação com o Portal GNRE (SEFAZ-PE).
 *
 * Lê a configuração (JSON), guarda o certificado digital, resolve o serviço em
 * storage/wsgnre.json, valida as mensagens nos XSD de schemes/ e monta o
 * envelope SOAP 1.2 (gnreCabecMsg + gnreDadosMsg) enviado pelo SoapCurl.
 *
 * @package    NFePHP\Gnre\Common
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @category   NFePHP
 * @link       http://github.com/nfephp-org/sped-gnre for the canonical source repository
 */
class Tools
{
    /**
     * Namespace dos dados GNRE
     */
    public const PORTAL = 'http://www.gnre.pe.gov.br';

    /**
     * Unidades da federação aceitas pelo Portal GNRE
     */
    public const UFS = [
        'AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MG', 'MS', 'MT', 'PA',
        'PB', 'PE', 'PI', 'PR', 'RJ', 'RN', 'RO', 'RR', 'RS', 'SC', 'SE', 'SP', 'TO',
    ];

    /**
     * Versões de layout suportadas => pasta de schemas
     */
    protected const VERSOES = [
        '1.00' => 'PL_GNRE_100',
        '2.00' => 'PL_GNRE_200',
    ];

    /**
     * Tamanho máximo da mensagem aceito pelo Portal GNRE (Anexo I, código 103)
     */
    protected const TAMANHO_MAXIMO = 600 * 1024;

    protected const SOAP_NAMESPACES = [
        'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
        'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
        'xmlns:soap' => 'http://www.w3.org/2003/05/soap-envelope',
    ];

    /**
     * @var stdClass
     */
    public $config;
    /**
     * @var string
     */
    public $lastRequest = '';
    /**
     * @var string
     */
    public $lastResponse = '';
    /**
     * 1 produção, 2 homologação
     * @var int
     */
    public $tpAmb = 2;
    /**
     * Versão do layout (1.00 ou 2.00)
     * @var string
     */
    public $versao = '2.00';
    /**
     * @var SoapInterface|null
     */
    public $soap;
    /**
     * @var Certificate
     */
    protected $certificate;
    /**
     * @var string
     */
    protected $pathwsfiles;
    /**
     * @var string
     */
    protected $pathschemes;
    /**
     * @var string
     */
    protected $urlService = '';
    /**
     * @var string
     */
    protected $urlNamespace = '';
    /**
     * @var string
     */
    protected $urlAction = '';
    /**
     * @var string
     */
    protected $urlMethod = '';
    /**
     * @var string
     */
    protected $urlVersion = '';

    /**
     * @param string $configJson {"tpAmb","razaosocial","cnpj"|"cpf","siglaUF","versao"}
     * @param Certificate $certificate certificado digital A1
     */
    public function __construct(string $configJson, Certificate $certificate)
    {
        $this->config = $this->lerConfig($configJson);
        $this->certificate = $certificate;
        $this->pathwsfiles = (string) realpath(__DIR__ . '/../../storage') . DIRECTORY_SEPARATOR;
        $this->pathschemes = (string) realpath(__DIR__ . '/../../schemes') . DIRECTORY_SEPARATOR;
        $this->setEnvironment((int) $this->config->tpAmb);
        $this->version((string) $this->config->versao);
    }

    /**
     * Define o ambiente: 1 produção, 2 homologação
     */
    public function setEnvironment(int $tpAmb = 2): void
    {
        if ($tpAmb !== 1 && $tpAmb !== 2) {
            throw new InvalidArgumentException("Ambiente inválido [$tpAmb]: use 1 (produção) ou 2 (homologação).");
        }
        $this->tpAmb = $tpAmb;
    }

    /**
     * Define (se informada) e retorna a versão do layout
     */
    public function version(string $versao = ''): string
    {
        if ($versao !== '') {
            if (!isset(self::VERSOES[$versao])) {
                throw new InvalidArgumentException("Versão de layout não suportada [$versao]: use 1.00 ou 2.00.");
            }
            $this->versao = $versao;
        }
        return $this->versao;
    }

    /**
     * Define a classe de comunicação (SoapCurl ou outra que implemente SoapInterface)
     */
    public function loadSoapClass(SoapInterface $soap): void
    {
        $this->soap = $soap;
        $this->soap->loadCertificate($this->certificate);
    }

    /**
     * Cria um SoapCurl caso nenhuma classe de comunicação tenha sido carregada
     */
    protected function checkSoap(): void
    {
        if ($this->soap === null) {
            $this->soap = new SoapCurl($this->certificate);
        }
    }

    /**
     * Prepara URL, namespace, action, método e versaoDados do serviço
     *
     * @param string $servico nome do serviço em storage/wsgnre.json
     * @param int|null $tpAmb ambiente; null usa o do config
     */
    protected function servico(string $servico, ?int $tpAmb = null): void
    {
        $webservices = new Webservices((string) file_get_contents($this->pathwsfiles . 'wsgnre.json'));
        $ws = $webservices->get($servico, $tpAmb ?? $this->tpAmb);
        $this->urlService = $ws->url;
        $this->urlNamespace = $ws->namespace;
        $this->urlAction = $ws->action;
        $this->urlMethod = $ws->metodo;
        $this->urlVersion = $ws->versaoDados ?? $this->versao;
    }

    /**
     * Envelopa a mensagem (gnreCabecMsg + gnreDadosMsg) e envia ao serviço preparado em servico()
     *
     * @param string $request mensagem XML da área de dados, sem declaração XML
     * @return string retorno SOAP bruto
     */
    protected function sendRequest(string $request): string
    {
        if (strlen($request) > self::TAMANHO_MAXIMO) {
            throw new InvalidArgumentException(sprintf(
                'A mensagem tem %d KB e o Portal GNRE aceita no máximo 600 KB (código 103): divida o lote.',
                (int) ceil(strlen($request) / 1024)
            ));
        }
        $this->checkSoap();
        $this->lastRequest = $request;
        $body = '<gnreDadosMsg xmlns="' . $this->urlNamespace . '">' . $request . '</gnreDadosMsg>';
        $header = new SoapHeader($this->urlNamespace, 'gnreCabecMsg', ['versaoDados' => $this->urlVersion]);
        /** @var SoapInterface $soap */
        $soap = $this->soap;
        $this->lastResponse = (string) $soap->send(
            $this->urlService,
            $this->urlMethod,
            $this->urlAction,
            SOAP_1_2,
            [],
            self::SOAP_NAMESPACES,
            $body,
            $header
        );
        return $this->lastResponse;
    }

    /**
     * Valida o XML no XSD da pasta de schemas da versão
     *
     * @param string $xml mensagem a validar
     * @param string $xsd nome do arquivo XSD (ex.: lote_gnre_v2.00.xsd)
     * @param string|null $versao pasta de schemas; null usa a versão do config
     * @throws \NFePHP\Common\Exception\ValidatorException se o XML for inválido
     */
    protected function isValid(string $xml, string $xsd, ?string $versao = null): bool
    {
        $versao = $versao ?? $this->versao;
        $arquivo = $this->pathschemes . self::VERSOES[$versao] . DIRECTORY_SEPARATOR . $xsd;
        if (!is_file($arquivo)) {
            throw new RuntimeException("Schema não encontrado: $arquivo");
        }
        return Validator::isValid($xml, $arquivo);
    }

    /**
     * Lê e valida o config JSON
     */
    private function lerConfig(string $configJson): stdClass
    {
        $config = json_decode($configJson);
        if (!$config instanceof stdClass) {
            throw DocumentsException::wrongDocument(8, 'JSON inválido');
        }
        if (!isset($config->tpAmb) || !in_array((int) $config->tpAmb, [1, 2], true)) {
            throw DocumentsException::wrongDocument(8, 'tpAmb deve ser 1 ou 2');
        }
        if (!isset($config->versao) || !isset(self::VERSOES[(string) $config->versao])) {
            throw DocumentsException::wrongDocument(8, 'versao deve ser 1.00 ou 2.00');
        }
        if (!isset($config->siglaUF) || !in_array((string) $config->siglaUF, self::UFS, true)) {
            throw DocumentsException::wrongDocument(8, 'siglaUF inválida');
        }
        if (empty($config->cnpj) && empty($config->cpf)) {
            throw DocumentsException::wrongDocument(8, 'informe cnpj ou cpf');
        }
        return $config;
    }
}
