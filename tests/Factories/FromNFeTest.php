<?php

namespace NFePHP\Gnre\Tests\Factories;

use DOMDocument;
use DOMElement;
use NFePHP\Gnre\Exception\DocumentsException;
use NFePHP\Gnre\Exception\InvalidArgumentException;
use NFePHP\Gnre\Factories\FromNFe;
use NFePHP\Gnre\Tests\MakeTestCase;

class FromNFeTest extends MakeTestCase
{
    private const CHAVE = '32191107670813000825550010059062251133037938';

    /**
     * XML da fixture, opcionalmente alterado
     *
     * @param array<string, string> $valores tag => novo valor (primeira ocorrência)
     */
    private function nfe(array $valores = [], bool $semProtocolo = false): string
    {
        $dom = new DOMDocument();
        $dom->load(self::FIXTURES . '/xml/nfe-difal.xml');
        foreach ($valores as $tag => $valor) {
            [$pai, $nome] = strpos($tag, '/') !== false ? explode('/', $tag) : [null, $tag];
            $no = $pai === null
                ? $dom->getElementsByTagName($nome)->item(0)
                : $this->filho($dom, $pai, $nome);
            if (!$no instanceof DOMElement) {
                $alvo = $dom->getElementsByTagName('ICMSTot')->item(0);
                $this->assertInstanceOf(DOMElement::class, $alvo);
                $no = $dom->createElementNS('http://www.portalfiscal.inf.br/nfe', $nome);
                $alvo->appendChild($no);
            }
            $no->nodeValue = $valor;
        }
        if ($semProtocolo) {
            $prot = $dom->getElementsByTagName('protNFe')->item(0);
            if ($prot instanceof DOMElement && $prot->parentNode !== null) {
                $prot->parentNode->removeChild($prot);
            }
        }
        return (string) $dom->saveXML();
    }

    private function filho(DOMDocument $dom, string $pai, string $nome): ?DOMElement
    {
        $grupo = $dom->getElementsByTagName($pai)->item(0);
        if (!$grupo instanceof DOMElement) {
            return null;
        }
        $no = $grupo->getElementsByTagName($nome)->item(0);
        return $no instanceof DOMElement ? $no : null;
    }

    public function testEmitente(): void
    {
        $emitente = (new FromNFe($this->nfe()))->emitente();
        $this->assertSame('07670813000823', $emitente->CNPJ);
        // IE da GNRE = IEST (inscrição na UF favorecida), não a emit/IE 013581225 (regra 203 do manual 2.17)
        $this->assertSame('0914567891113', $emitente->IE);
        $this->assertSame('SEILA COMERCIO ELETRONICO S.A', $emitente->razaoSocial);
        $this->assertSame('ROD BR-132, 1000 - VILA AMARO', $emitente->endereco);
        $this->assertSame('05101', $emitente->municipio);
        $this->assertSame('ES', $emitente->uf);
        $this->assertSame('29143080', $emitente->cep);
        $this->assertSame('195552323', $emitente->telefone);
    }

    public function testEmitenteSemIestNaoInformaIe(): void
    {
        $dom = new DOMDocument();
        $dom->loadXML($this->nfe());
        $iest = $dom->getElementsByTagName('IEST')->item(0);
        $this->assertInstanceOf(DOMElement::class, $iest);
        $this->assertNotNull($iest->parentNode);
        $iest->parentNode->removeChild($iest);

        $emitente = (new FromNFe((string) $dom->saveXML()))->emitente();

        $this->assertFalse(property_exists($emitente, 'IE'));
        $this->assertSame('07670813000823', $emitente->CNPJ);
    }

    public function testDestinatarioCpf(): void
    {
        $nfe = new FromNFe($this->nfe());
        $destinatario = $nfe->destinatario();
        $this->assertSame('90483926086', $destinatario->CPF);
        $this->assertFalse(property_exists($destinatario, 'CNPJ'));
        $this->assertSame('CICLANO DA SILVA', $destinatario->razaoSocial);
        $this->assertSame('50308', $destinatario->municipio);
        $this->assertSame('SP', $nfe->ufDestino());
    }

    public function testChaveENumero(): void
    {
        $nfe = new FromNFe($this->nfe());
        $this->assertSame(self::CHAVE, $nfe->chave());
        $this->assertSame('5906225', $nfe->numero());
        $this->assertSame(self::CHAVE, (new FromNFe($this->nfe([], true)))->chave());
    }

    public function testValores(): void
    {
        $valores = (new FromNFe($this->nfe()))->valores();
        $this->assertSame('40.79', $valores->vNF);
        $this->assertSame('2.45', $valores->vICMSUFDest);
        $this->assertSame('0.00', $valores->vFCPUFDest);
        $this->assertSame('0.00', $valores->vST);
        $this->assertSame('0.00', $valores->vFCPST);
    }

    public function testDocumentoOrigemSp(): void
    {
        $doc = (new FromNFe($this->nfe()))->documentoOrigem();
        $this->assertSame('5906225', $doc->documentoOrigem);
        $this->assertSame('10', $doc->documentoOrigemTipo);
    }

    public function testUfComChave(): void
    {
        $doc = (new FromNFe($this->nfe(['enderDest/UF' => 'RJ'])))->documentoOrigem();
        $this->assertSame(self::CHAVE, $doc->documentoOrigem);
        $this->assertSame('24', $doc->documentoOrigemTipo);
    }

    public function testGuiaDifal(): void
    {
        $make = (new FromNFe($this->nfe()))->guiaDifal((object) [
            'receita' => '100102',
            'dataVencimento' => '2026-12-15',
        ]);

        $xml = $make->getXML();

        $this->valida($xml);
        $this->assertStringContainsString('<ufFavorecida>SP</ufFavorecida>', $xml);
        $this->assertStringContainsString('<receita>100102</receita>', $xml);
        $this->assertStringContainsString('<documentoOrigem tipo="10">5906225</documentoOrigem>', $xml);
        $this->assertStringContainsString('<valor tipo="11">2.45</valor>', $xml);
        $this->assertStringNotContainsString('tipo="12"', $xml);
        $this->assertStringContainsString(
            '<valorGNRE>2.45</valorGNRE><dataPagamento>2026-12-15</dataPagamento>',
            $xml
        );
    }

    public function testGuiaDifalComFcp(): void
    {
        $xml = (new FromNFe($this->nfe(['ICMSTot/vFCPUFDest' => '0.50'])))
            ->guiaDifal((object) ['receita' => '100102', 'dataVencimento' => '2026-12-15'])
            ->getXML();

        $this->valida($xml);
        $this->assertStringContainsString('<valor tipo="11">2.45</valor><valor tipo="12">0.50</valor>', $xml);
        $this->assertStringContainsString('<valorGNRE>2.95</valorGNRE>', $xml);
    }

    public function testMakeDevolvidoAceitaMaisTags(): void
    {
        $make = (new FromNFe($this->nfe()))->guiaDifal((object) [
            'receita' => '100102',
            'dataVencimento' => '2026-12-15',
        ]);
        $make->tagCampoExtra((object) ['codigo' => '62', 'valor' => 'REFERENTE NF 5906225']);

        $xml = $make->getXML();

        $this->assertStringContainsString('<codigo>62</codigo>', $xml);
        $this->valida($xml);
    }

    public function testGuiaStSemSt(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new FromNFe($this->nfe()))->guiaSt((object) ['receita' => '100099', 'dataVencimento' => '2026-12-15']);
    }

    public function testGuiaSt(): void
    {
        $xml = (new FromNFe($this->nfe(['ICMSTot/vST' => '10.00'])))
            ->guiaSt((object) ['receita' => '100099', 'dataVencimento' => '2026-12-15'])
            ->getXML();

        $this->valida($xml);
        $this->assertStringContainsString('<valor tipo="11">10.00</valor>', $xml);
        $this->assertStringContainsString('<valorGNRE>10.00</valorGNRE>', $xml);
    }

    public function testNaoENfe(): void
    {
        $this->expectException(DocumentsException::class);
        new FromNFe('<cteProc/>');
    }

    public function testOpcoesObrigatorias(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new FromNFe($this->nfe()))->guiaDifal((object) []);
    }
}
