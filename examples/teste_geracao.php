<?php 
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeAmtec\Tools;
use NFePHP\NFSeAmtec\Rps;
use NFePHP\NFSeAmtec\Common\Soap\SoapCurl;
use NFePHP\NFSeAmtec\Common\FakePretty;

try {

    $config = [
        'cnpj'  => '99999999000191',
        'im'    => '99999999',
        'cmun'  => '0025300', // Goiânia não segue a tabela nacional IBGE Usar ( ./Municipios_SETEC_22.04.2013.txt )
        'razao' => 'Empresa Test Ltda',
        'tpamb' => 2
    ];

    $configJson = json_encode($config);

    $content = file_get_contents('expired_certificate.pfx');
    $password = 'associacao';
    $cert = Certificate::readPfx($content, $password);

    $soap = new SoapCurl($cert);

    $tools = new Tools($configJson, $cert);
    $tools->loadSoapClass($soap);

    $std = new \stdClass();
    $std->naturezaOperacao = 1;
    $std->regimeEspecialTributacao = 1;
    $std->optanteSimplesNacional = 2;
    $std->incentivadorCultural = 2;

    $std->version = '2.00'; //indica qual JsonSchema USAR na validação
    $std->IdentificacaoRps = new \stdClass();
    $std->IdentificacaoRps->Numero = 3033; //limite 15 digitos
    $std->IdentificacaoRps->Serie = 'TESTE'; //BH deve ser string numerico
    $std->IdentificacaoRps->Tipo = 1; //1 - RPS 2-Nota Fiscal Conjugada (Mista) 3-Cupom
    $std->DataEmissao = '2020-08-12T23:33:22';
    $std->Status = 1;  // 1 – Normal  2 – Cancelado

    $std->Tomador = new \stdClass();
    $std->Tomador->Cnpj = "99999999000191";
    //$std->Tomador->Cpf = "12345678901";
    $std->Tomador->RazaoSocial = "Fulano de Tal";

    $std->Tomador->Endereco = new \stdClass();
    $std->Tomador->Endereco->Endereco = 'Rua das Rosas';
    $std->Tomador->Endereco->Numero = '111';
    $std->Tomador->Endereco->Complemento = 'Sobre Loja';
    $std->Tomador->Endereco->Bairro = 'Centro';
    $std->Tomador->Endereco->CodigoMunicipio = '0025300';
    $std->Tomador->Endereco->Uf = 'GO';
    $std->Tomador->Endereco->Cep = 74672680;

    $std->Servico = new \stdClass();
    $std->Servico->ItemListaServico = '11.01';
    $std->Servico->CodigoTributacaoMunicipio = '749010400';
    $std->Servico->Discriminacao = 'Teste de RPS';
    $std->Servico->CodigoMunicipio = '0025300';

    $std->Servico->Valores = new \stdClass();
    $std->Servico->Valores->ValorServicos = 100.00;
    // $std->Servico->Valores->ValorDeducoes = 10.00;
    // $std->Servico->Valores->ValorPis = 10.00;
    // $std->Servico->Valores->ValorCofins = 10.00;
    // $std->Servico->Valores->ValorInss = 10.00;
    // $std->Servico->Valores->ValorIr = 10.00;
    // $std->Servico->Valores->ValorCsll = 10.00;
    $std->Servico->Valores->IssRetido = 2;
    // $std->Servico->Valores->ValorIss = 10.00;
    // $std->Servico->Valores->OutrasRetencoes = 10.00;
    $std->Servico->Valores->Aliquota = 5;
    // $std->Servico->Valores->DescontoIncondicionado = 10.00;
    // $std->Servico->Valores->DescontoCondicionado = 10.00;

    // $std->IntermediarioServico = new \stdClass();
    // $std->IntermediarioServico->RazaoSocial = 'INSCRICAO DE TESTE SIATU - D AGUA -PAULINO S'; 
    // $std->IntermediarioServico->Cnpj = '99999999000191';
    //$std->IntermediarioServico->Cpf = '12345678901';
    // $std->IntermediarioServico->InscricaoMunicipal = '8041700010';
    
    // $std->ConstrucaoCivil = new \stdClass();
    // $std->ConstrucaoCivil->CodigoObra = '1234';
    // $std->ConstrucaoCivil->Art = '1234';

    $rps = new Rps($std);

    $lote = 1;

    $response = $tools->gerarNfse($rps, $lote);
    echo FakePretty::prettyPrint($response, '');
} catch (\Exception $e) {
    echo $e->getMessage();
}