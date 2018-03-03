<?php

//header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require 'includes/Client.php';
use JsonRPC\Client;

include("settings.php");

$client = new Client($cp_server);
$client->authentication($cp_user, $cp_password);

$address = array($_GET["address"]);

$filters = array(array('field' => 'address', 'op' => 'IN', 'value' => $address));
$balances_result = $client->execute('get_balances', array('filters' => $filters));

$balances_parsed = array();

//check if divisible
$assets = array();
for($i=0; $i < count($balances_result); $i++){
    $assets[$i] = $balances_result[$i]["asset"];
}
$filters = array(array('field' => 'asset', 'op' => 'IN', 'value' => $assets));
$issuances_result = $client->execute('get_issuances', array('filters' => $filters, 'filterop' => "AND"));

$assets_longname = array();
$assets_longname[$XCP] = $XCP;
$assets_divisible = array();
$assets_divisible[$XCP] = 1;

for($i=0; $i < count($issuances_result); $i++){
    $asset = $issuances_result[$i]["asset"];
    $assets_divisible[$asset] = $issuances_result[$i]["divisible"];
    $assets_longname[$asset] = $issuances_result[$i]["asset_longname"];
}

for($i=0; $i < count($balances_result); $i++){
    $asset = $balances_result[$i]["asset"];
    $balances_parsed[$i]["asset"] = $asset;
    $balances_parsed[$i]["asset_longname"] = $assets_longname[$asset];
    if($assets_divisible[$asset] == 1) {
        $balances_result[$i]["quantity"] /= 100000000;
        $balances_parsed[$i]["amount"] = number_format($balances_result[$i]["quantity"], 8, ".", "");
    } else {
        $balances_parsed[$i]["amount"] = number_format($balances_result[$i]["quantity"], 0, ".", "");
    }  
}

echo json_encode(array('success' => 1, 'total' => count($balances_parsed), 'data' => $balances_parsed));


?>
