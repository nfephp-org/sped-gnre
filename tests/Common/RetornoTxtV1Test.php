<?php

namespace NFePHP\Gnre\Tests\Common;

use NFePHP\Gnre\Common\RetornoTxtV1;
use NFePHP\Gnre\Common\Standardize;
use PHPUnit\Framework\TestCase;

class RetornoTxtV1Test extends TestCase
{
    private function txtDaFixture(): string
    {
        $std = (new Standardize())->toStd(
            (string) file_get_contents(__DIR__ . '/../fixtures/xml/retorno-resultado-v1.xml')
        );
        return (string) $std->resultado;
    }

    public function testCabecalho(): void
    {
        $retorno = RetornoTxtV1::ler($this->txtDaFixture());
        $this->assertNotNull($retorno->cabecalho);
        $this->assertSame('0000070008', $retorno->cabecalho->numeroProtocoloLote);
        $this->assertSame('0', $retorno->cabecalho->ambiente);
    }

    public function testGuia(): void
    {
        $retorno = RetornoTxtV1::ler($this->txtDaFixture());
        $this->assertCount(1, $retorno->guias);
        $guia = $retorno->guias[0];
        $this->assertSame('0001', $guia->sequencialGuia);
        $this->assertSame('1', $guia->situacaoGuia);
        $this->assertSame('AC', $guia->ufFavorecida);
        $this->assertSame('123456', $guia->receita);
        $this->assertSame('TESTE RAZAO SOCIAL', $guia->razaoSocialEmitente);
        $this->assertSame('SP', $guia->ufEmitente);
        $this->assertSame('10102014', $guia->dataVencimento);
        $this->assertSame('3', $guia->periodoReferencia);
        $this->assertSame('112014', $guia->mesAnoReferencia);
        $this->assertSame('005', $guia->parcela);
        $this->assertSame('0.00', $guia->valorPrincipal);
        $this->assertSame('3', $guia->quantidadeVias);
    }

    public function testRejeicao(): void
    {
        $retorno = RetornoTxtV1::ler($this->txtDaFixture());
        $this->assertCount(1, $retorno->rejeicoes);
        $rejeicao = $retorno->rejeicoes[0];
        $this->assertSame('0001', $rejeicao->sequencialGuia);
        $this->assertSame('c02_receita', $rejeicao->nomeCampo);
        $this->assertSame('205', $rejeicao->codigoMotivo);
        $this->assertSame('A UF favorecida nao gera GNRE para a Receita informada.', $rejeicao->descricaoMotivo);
        $this->assertCount(1, $retorno->guias[0]->rejeicoes);
        $this->assertSame('205', $retorno->guias[0]->rejeicoes[0]->codigoMotivo);
    }

    public function testRodape(): void
    {
        $retorno = RetornoTxtV1::ler($this->txtDaFixture());
        $this->assertNotNull($retorno->rodape);
        $this->assertStringStartsWith('914066705180001', $retorno->rodape->conteudo);
    }

    public function testValorEmCentavos(): void
    {
        $linha = '1' . str_pad('', 917, ' ') . '000000000012345';
        $retorno = RetornoTxtV1::ler($linha);
        $this->assertSame('123.45', $retorno->guias[0]->valorPrincipal);
    }

    public function testQuebrasDeLinha(): void
    {
        $txt = $this->txtDaFixture();
        $lf = RetornoTxtV1::ler($txt);
        $crlf = RetornoTxtV1::ler(str_replace("\n", "\r\n", $txt));
        $cr = RetornoTxtV1::ler(str_replace("\n", "\r", $txt));
        $comBrancos = RetornoTxtV1::ler(str_replace("\n", "\n\n   \n", $txt));
        $this->assertEquals($lf, $crlf);
        $this->assertEquals($lf, $cr);
        $this->assertEquals($lf, $comBrancos);
    }

    public function testTxtVazio(): void
    {
        $retorno = RetornoTxtV1::ler('');
        $this->assertNull($retorno->cabecalho);
        $this->assertSame([], $retorno->guias);
        $this->assertSame([], $retorno->rejeicoes);
        $this->assertNull($retorno->rodape);
    }

    /**
     * Linha TXT 1.00 sintética: campos posicionados por CARACTERE (base 1)
     *
     * @param array<int, string> $campos início => conteúdo
     */
    private function linhaTxt(array $campos, int $tamanho = 1098): string
    {
        $chars = array_fill(0, $tamanho, ' ');
        foreach ($campos as $inicio => $conteudo) {
            foreach (preg_split('//u', $conteudo, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $i => $char) {
                $chars[$inicio - 1 + $i] = $char;
            }
        }
        return implode('', $chars);
    }

    public function testTxtComAcentoNaoDeslocaCampos(): void
    {
        $linha = $this->linhaTxt([
            1 => '1',
            2 => '0001',
            6 => '0',
            32 => 'JOSÉ DA CONCEIÇÃO',
            92 => 'AVENIDA SÃO JOÃO',
            919 => '000000000012345',
            979 => '858500000029430099992006280100962200000000000000',
            1027 => '85850000002430099992002801000000000000000000',
        ]);

        $guia = RetornoTxtV1::ler($linha)->guias[0];

        $this->assertSame('JOSÉ DA CONCEIÇÃO', $guia->razaoSocialEmitente);
        $this->assertSame('AVENIDA SÃO JOÃO', $guia->enderecoEmitente);
        $this->assertSame('123.45', $guia->valorPrincipal);
        $this->assertSame('858500000029430099992006280100962200000000000000', $guia->representacaoNumerica);
        $this->assertSame('85850000002430099992002801000000000000000000', $guia->codigoBarras);
    }
}
