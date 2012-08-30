<?php

include '../src/bootstrap.php';

$config = require( 'config.php' );

$client = new \MegaplanAPIClient\Client( $config['host'], $config['accessId'], $config['secretKey'] );
$request = new \MegaplanAPIClient\Request( '/BumsTaskApiV01/Task/list.api', array( 'Folder' => 'incoming' ) );
$response = $client->send( $request );
var_dump( $response );




//include '../src/MegaplanAPIClient/_Request.php';
//include '../src/MegaplanAPIClient/_RequestInfo.php';
//
//$request = new SdfApi_Request( '6644812f6ab70b1e725f', '2a630F6ed3E229012d193c5960c3d8ae988fbe1d', 'androidtest.megaplan.ru' );
//$response = $request->get( '/BumsTaskApiV01/Task/list.api' );
//var_dump( $response );