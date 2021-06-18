<?php 
require_once '/var/www/vhosts/' . $_SERVER['HTTP_HOST'] . '/httpdocs/Automatizacion/database/dbSelectors.php';
include(dirname(__FILE__).'/config/config.inc.php');
include_once(dirname(__FILE__).'/config/settings.inc.php');
include_once('/classes/Cookie.php');
include('/init.php');

$selectBDD = selectBDD();
$dbname    = $selectBDD[dbname];
$username  = $selectBDD[username];
$password  = $selectBDD[password];
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}


$PRIVATE_KEY = '7edc3dfb401d55ed53c48680e63f7cc8435ec20d';
$TOKEN = '07f6dfda446c0126d485cc3638f88373';
$SERVER = 'https://web.marketsync.mx/api/';
//$SERVER = 'http://sandbox.marketsync.mx/api/';

# Set initial parameters
$parameters = [];
$parameters['token'] = $TOKEN;
$parameters['timestamp'] = substr(date(DATE_ATOM),0,19); # YYYY-MM-DDTHH:mm:ss
$parameters['version'] = '1.0';
$parameters['limit'] = '10';
$parameters['ids'] = '190187';
$parameters['markets'] = 8;
# You may add others parameters here
# ...

ksort($parameters);
// URL encode the parameters.
$encoded = array();
foreach ($parameters as $name => $value) {
	$encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
}
// Concatenate the sorted and URL encoded parameters into a string.
$concatenated = implode('&', $encoded);

$sign = rawurlencode(hash_hmac('sha256', $concatenated, $PRIVATE_KEY, false));
# Reemplace controller con el controlador deseado.
$url = $SERVER . 'precios?' . $concatenated . '&signature=' . $sign;
//print $url.PHP_EOL;

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 120,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_POSTFIELDS => "",
	CURLOPT_HTTPHEADER => array(
		"content-type: application/json"
	),
));
//$fecha = date("Y-m-d");
/*
$responseP = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$cuentele = 1;
if ($err) {
	capuraLogs::nuevo_log("precios POSTFIELDS : {$POSTFIELDS}");
	echo "cURL Error #:" . $err;
} else {
	print_r($responseP);
	$responseP = json_decode($responseP);
	$responseP = $responseP->answer;
	
}
/**/
?>