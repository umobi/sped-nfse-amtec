<?php 
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeAmtec\Tools;
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

    $numero = 3032;
    $serie = 'UNICA';
    $tipo = 1;

    $response = $tools->consultarNfsePorRps($numero, $serie, $tipo);
    echo FakePretty::prettyPrint($response, '');
} catch (\Exception $e) {
    echo $e->getMessage();
}