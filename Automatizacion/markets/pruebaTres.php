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
$fecha = date("Y-m-d");

$ftSku = " AND (sku = '0540-0240-0005' OR sku = '0540-0240-0005')";
$sql=  "SELECT id_market, precio ";
$sql.= "FROM productos_market_sync ";
$sql.="WHERE actualizado = '{$fecha}' AND precio > 0 {$ftSku}";
$result = $conn->query($sql);
$items = array();
$cuentale = 0;
if($result->num_rows > 0){
	while($row = $result->fetch_assoc()) {
		$precio = $row[precio];
		$id_market = $row[id_market];
		$items[$cuentale] = array(
			'product_id' => $id_market, 
			'market_id' => 4, 
			'oferta' => (float)$precio, 
			'precio' => (float)$precio,
		);
		$cuentale++;
	}
}else{

}
$items = json_encode($items);
$PRIVATE_KEY = '7edc3dfb401d55ed53c48680e63f7cc8435ec20d';
$TOKEN = '07f6dfda446c0126d485cc3638f88373';
//$SERVER = 'http://sandbox.marketsync.mx/api/';
$SERVER = 'https://web.marketsync.mx/api/';

# Set initial parameters
$parameters = [];
$parameters['token'] = $TOKEN;
$parameters['timestamp'] = substr(date(DATE_ATOM),0,19); # YYYY-MM-DDTHH:mm:ss
$parameters['version'] = '1.0';
//$parameters['limit'] = '10000';
/*
$parameters['ids'] = '186230';
*/      
//$parameters['markets'] = 4;
# You may add others parameters here
# ...

ksort($parameters);
//URL encode the parameters.
$encoded = array();
foreach ($parameters as $name => $value) {
	$encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
}
//Concatenate the sorted and URL encoded parameters into a string.
$concatenated = implode('&', $encoded);

$sign = rawurlencode(hash_hmac('sha256', $concatenated, $PRIVATE_KEY, false));
# Reemplace controller con el controlador deseado.
$url = $SERVER . 'precios?' . $concatenated . '&signature=' . $sign;
$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 120,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "PUT",
	CURLOPT_POSTFIELDS => $items,
	CURLOPT_HTTPHEADER => array(
		"content-type: application/json"
	),
));
//$fecha = date("Y-m-d");
/*
$responseP = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	$responseP = json_decode($responseP);
	$responseP = $responseP->answer;
	
	foreach ($responseP as $resp) {
		$id = $resp->id;
		$sku = $resp->sku;
		print_r("id ::: {$id}<br>");
		print_r("sku ::: {$sku}<br>");
	}
	
	//$responseP = $responsePanswer;
}
/**/
?>