<?php

//header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require 'includes/Client.php';
use JsonRPC\Client;

include("settings.php");

$client = new Client($cp_server);
$client->authentication($cp_user, $cp_password);

//$block = 380000;

$block = $_GET["currentblock"]-57600;
$assets = array($BTC);
$filters = array(array('field' => 'get_asset', 'op' => 'IN', 'value' => $assets));

$orders_result = $client->execute('get_orders', array('filters' => $filters, 'filterop' => "AND", 'start_block' => $block));

$give_assets = array();

for($i=0; $i < count($orders_result); $i++){
    $give_assets[$i] = $orders_result[$i]["give_asset"];
}

//array_push($give_assets, $XCP);

$filters = array(array('field' => 'asset', 'op' => 'IN', 'value' => $give_assets));
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

$jsonarray = array('orders' => $orders_result, 'divisibility' => $assets_divisible, 'longnames' => $assets_longname);

echo json_encode($jsonarray);


?>
