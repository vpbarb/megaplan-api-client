<?php

include '../src/bootstrap.php';

$config = require( 'config.php' );

$client = new \MegaplanAPIClient\Client( $config['host'], $config['accessId'], $config['secretKey'] );
$request = new \MegaplanAPIClient\Request( '/BumsTaskApiV01/Task/list.api', array( 'Folder' => 'incoming' ) );
$response = $client->send( $request );
var_dump( $response );