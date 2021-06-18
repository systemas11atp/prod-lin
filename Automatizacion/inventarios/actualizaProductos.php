<?php 

require_once '/var/www/vhosts/' . $_SERVER['HTTP_HOST'] . '/httpdocs/Automatizacion/database/dbSelectors.php';
include(dirname(__FILE__).'/config/config.inc.php');
include_once(dirname(__FILE__).'/config/settings.inc.php');
include_once('/classes/Cookie.php');
include('/init.php');

if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
	throw new Exception('Request method must be POST!');
}
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
$content = trim(file_get_contents("php://input"));
$decodedT = json_decode($content, true);
if(!is_array($decodedT)){
	throw new Exception('Received content contained invalid JSON!');
}
$selectBDD = selectBDD();
$dbname    = $selectBDD[dbname];
$username  = $selectBDD[username];
$password  = $selectBDD[password];
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$stock = $decodedT[stock];
$reserved = $decodedT[reserved];
$chihuahua = $decodedT[chihuahua];
$chihuahua = (int)($chihuahua * 0.3);
$lideart = $stock - $chihuahua;
$fisica = $stock-$reserved;
$id_stock_available = $decodedT[id_stock_available];
$sql_update ="UPDATE prstshp_stock_available SET quantity = {$stock}, physical_quantity = {$fisica}, chihuahua = {$chihuahua}, lideart = {$lideart} WHERE id_stock_available = {$id_stock_available}";
if($conn->query($sql_update)){
	print_r("1 ::: sql_update : {$sql_update}<br>");
}else{
	print_r("2 ::: sql_update : {$sql_update}<br>");
}
/* 
ALTER TABLE `prstshp_stock_available` ADD `chihuahua` INT NULL AFTER `location`, ADD `lideart` INT NULL AFTER `chihuahua`;
*/

?>
