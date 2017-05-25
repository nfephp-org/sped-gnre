<?php

namespace Sped\Gnre\Test\Sefaz;

use Sped\Gnre\Sefaz\Lote;
use Sped\Gnre\Sefaz\Guia;

/**
 * @covers Sped\Gnre\Sefaz\Lote
 */
class LoteTest extends \PHPUnit_Framework_TestCase
{

    public function testDeveRetornarOsCabecalhosParaArequisicaoSoap()
    {
        $lote = new Lote();
        $headersArray = $lote->getHeaderSoap();

        $header = 'Content-Type: application/soap+xml;charset=utf-8;action="http://www.gnre.pe.gov.br/webservice/GnreRecepcaoLote"';
        $this->assertEquals($header, $headersArray[0]);
        $this->assertEquals('SOAPAction: processar', $headersArray[1]);
    }

    public function testDeveUtilizarOAmbienteDeProducaoAoEnviarUmLoteParaOwebService()
    {
        $lote = new Lote();

        $this->assertEquals('https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao', $lote->soapAction());
    }

    public function testDeveRetornarAacaoAserExecutadaNoSoap()
    {
        $lote = new Lote();

        $this->assertEquals('https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao', $lote->soapAction());
    }

    public function testDeveRetornarOxmlDoLoteSemCamposExtrasEparaEmitenteEdestinatarioJuridicos()
    {
        $estruturaLote = file_get_contents(__DIR__ . '/../../exemplos/lote-emit-cnpj-dest-cnpj-sem-campos-extras.xml');

        $guia = new Guia();

        $guia->c01_UfFavorecida = 26;
        $guia->c02_receita = 1000099;
        $guia->c25_detalhamentoReceita = 10101010;
        $guia->c26_produto = 'TESTE DE PROD';
        $guia->c27_tipoIdentificacaoEmitente = 1;
        $guia->c03_idContribuinteEmitente = 41819055000105;
        $guia->c28_tipoDocOrigem = 10;
        $guia->c04_docOrigem = 5656;
        $guia->c06_valorPrincipal = 10.99;
        $guia->c10_valorTotal = 12.52;
        $guia->c14_dataVencimento = '2015-05-01';
        $guia->c15_convenio = 546456;
        $guia->c16_razaoSocialEmitente = 'GNRE PHP EMITENTE';
        $guia->c17_inscricaoEstadualEmitente = 56756;
        $guia->c18_enderecoEmitente = 'Queens St';
        $guia->c19_municipioEmitente = 5300108;
        $guia->c20_ufEnderecoEmitente = 'DF';
        $guia->c21_cepEmitente = '08215917';
        $guia->c22_telefoneEmitente = 1199999999;
        $guia->c34_tipoIdentificacaoDestinatario = 1;
        $guia->c35_idContribuinteDestinatario = 86268158000162;
        $guia->c36_inscricaoEstadualDestinatario = 10809181;
        $guia->c37_razaoSocialDestinatario = 'RAZAO SOCIAL GNRE PHP DESTINATARIO';
        $guia->c38_municipioDestinatario = 2702306;
        $guia->c33_dataPagamento = '2015-11-30';
        $guia->mes = '05';
        $guia->ano = 2015;
        $guia->parcela = 2;
        $guia->periodo = 2014;

        $lote = new Lote();
        $lote->addGuia($guia);

        $this->assertXmlStringEqualsXmlString($estruturaLote, $lote->toXml());
    }

    public function testDeveRetornarOxmlDoLoteSemCamposExtrasEparaEmitenteEdestinatarioFisicos()
    {
        $estruturaLote = file_get_contents(__DIR__ . '/../../exemplos/lote-emit-cpf-dest-cpf-sem-campos-extras.xml');

        $guia = new Guia();

        $guia->c01_UfFavorecida = 26;
        $guia->c02_receita = 1000099;
        $guia->c25_detalhamentoReceita = 10101010;
        $guia->c26_produto = 'TESTE DE PROD';
        $guia->c27_tipoIdentificacaoEmitente = 2;
        $guia->c03_idContribuinteEmitente = 52162197650;
        $guia->c28_tipoDocOrigem = 10;
        $guia->c04_docOrigem = 5656;
        $guia->c06_valorPrincipal = 10.99;
        $guia->c10_valorTotal = 12.52;
        $guia->c14_dataVencimento = '2015-05-01';
        $guia->c15_convenio = 546456;
        $guia->c16_razaoSocialEmitente = 'GNRE PHP EMITENTE';
        $guia->c17_inscricaoEstadualEmitente = 56756;
        $guia->c18_enderecoEmitente = 'Queens St';
        $guia->c19_municipioEmitente = 5300108;
        $guia->c20_ufEnderecoEmitente = 'DF';
        $guia->c21_cepEmitente = '08215917';
        $guia->c22_telefoneEmitente = 1199999999;
        $guia->c34_tipoIdentificacaoDestinatario = 2;
        $guia->c35_idContribuinteDestinatario = 99942896759;
        $guia->c36_inscricaoEstadualDestinatario = 10809181;
        $guia->c37_razaoSocialDestinatario = 'RAZAO SOCIAL GNRE PHP DESTINATARIO';
        $guia->c38_municipioDestinatario = 2702306;
        $guia->c33_dataPagamento = '2015-11-30';
        $guia->mes = '05';
        $guia->ano = 2015;
        $guia->parcela = 2;
        $guia->periodo = 2014;

        $lote = new Lote();
        $lote->addGuia($guia);

        $this->assertXmlStringEqualsXmlString($estruturaLote, $lote->toXml());
    }

    public function testDeveRetornarOxmlDoLoteComOsCamposExtras()
    {
        $estruturaLote = file_get_contents(__DIR__ . '/../../exemplos/estrutura-lote-completo-gnre.xml');

        $guia = new Guia();

        $guia->c01_UfFavorecida = 26;
        $guia->c02_receita = 1000099;
        $guia->c25_detalhamentoReceita = 10101010;
        $guia->c26_produto = 'TESTE DE PROD';
        $guia->c27_tipoIdentificacaoEmitente = 1;
        $guia->c03_idContribuinteEmitente = 41819055000105;
        $guia->c28_tipoDocOrigem = 10;
        $guia->c04_docOrigem = 5656;
        $guia->c06_valorPrincipal = 10.99;
        $guia->c10_valorTotal = 12.52;
        $guia->c14_dataVencimento = '2015-05-01';
        $guia->c15_convenio = 546456;
        $guia->c16_razaoSocialEmitente = 'GNRE PHP EMITENTE';
        $guia->c17_inscricaoEstadualEmitente = 56756;
        $guia->c18_enderecoEmitente = 'Queens St';
        $guia->c19_municipioEmitente = 5300108;
        $guia->c20_ufEnderecoEmitente = 'DF';
        $guia->c21_cepEmitente = '08215917';
        $guia->c22_telefoneEmitente = 1199999999;
        $guia->c34_tipoIdentificacaoDestinatario = 1;
        $guia->c35_idContribuinteDestinatario = 86268158000162;
        $guia->c36_inscricaoEstadualDestinatario = 10809181;
        $guia->c37_razaoSocialDestinatario = 'RAZAO SOCIAL GNRE PHP DESTINATARIO';
        $guia->c38_municipioDestinatario = 2702306;
        $guia->c33_dataPagamento = '2015-11-30';
        $guia->mes = '05';
        $guia->ano = 2015;
        $guia->parcela = 2;
        $guia->periodo = 2014;

        $guia->c39_camposExtras = array(
            array(
                'campoExtra' => array(
                    'codigo' => 16,
                    'tipo' => 'T',
                    'valor' => '1200012',
                )
            ),
            array(
                'campoExtra' => array(
                    'codigo' => 15,
                    'tipo' => 'D',
                    'valor' => '2015-03-02',
                )
            ),
            array(
                'campoExtra' => array(
                    'codigo' => 10,
                    'tipo' => 'T',
                    'valor' => 17.21,
                )
            ),
        );

        $lote = new Lote();
        $lote->addGuia($guia);

        $this->assertXmlStringEqualsXmlString($estruturaLote, $lote->toXml());
    }

    public function testDeveUtilizarOAmbienteDeTestesAoEnviarUmLoteParaOwebService()
    {
        $lote = new Lote();
        $lote->utilizarAmbienteDeTeste(true);
        $this->assertEquals('https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao', $lote->soapAction());
    }

    public function testDeveRetornarOsCabecalhosParaArequisicaoSoapAoWebServiceDeteste()
    {
        $lote = new Lote();
        $lote->utilizarAmbienteDeTeste(true);

        $headersArray = $lote->getHeaderSoap();

        $header = 'Content-Type: application/soap+xml;charset=utf-8;action="http://www.testegnre.pe.gov.br/webservice/GnreRecepcaoLote"';
        $this->assertEquals($header, $headersArray[0]);
        $this->assertEquals('SOAPAction: processar', $headersArray[1]);
    }
}
