<?php 
require_once('/var/www/vhosts/lideart.net/httpdocs/logs_locales.php');

$dbname = "prestashop_3";
$username = "admin_lideart";
$password = "Avanceytec_2022";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$contador=0;
$productos = array();

$sql =  "SELECT id_product, 0 as id_product_attribute, reference, unitId FROM prstshp_product WHERE reference != '' AND reference LIKE '%-%'  AND (unitId IS NOT NULL AND unitId != '')";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$sql_att =  "SELECT id_product, id_product_attribute, reference, unitId  FROM prstshp_product_attribute WHERE id_product = {$row[id_product]} AND (unitId IS NOT NULL AND unitId != '')";
		$result_att = $conn->query($sql_att);
		if ($result_att->num_rows > 0) {
			while($row_att = $result_att->fetch_assoc()) {
				$reference = str_replace(":",'', $row_att[reference]);
				$reference = str_replace("_P",'', $reference);
				$productos[$contador] = array( "id_product" => (int)$row_att[id_product], 
					"id_product_attribute" => (int)$row_att[id_product_attribute], 
					"reference" =>"{$reference}",
					"unitId" =>"{$row_att[unitId]}"
				);
				$contador++;
			}
		}else{
			$reference = str_replace(":",'', $row[reference]);
			$reference = str_replace("_P",'', $reference);
			$productos[$contador] = array( "id_product" => (int)$row[id_product], 
				"id_product_attribute" => (int)$row[id_product_attribute], 
				"reference" =>"{$reference}",
				"unitId" =>"{$row[unitId]}"
			);
			$contador++;
			capuraLogs::nuevo_log("traerArticulos sql : {$sql}");
		}
	}
}else{
	capuraLogs::nuevo_log("traerArticulos sql : {$sql}");
}
echo json_encode($productos);

?>