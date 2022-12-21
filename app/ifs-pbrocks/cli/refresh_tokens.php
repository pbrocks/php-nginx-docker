<?php
/**
 * Created by The API Guys.
 * User: Brandon
 * Date: 11/21/2017
 * Time: 12:34
 */

use Infusionsoft\Infusionsoft;
use Monolog\Logger;

/** @var Infusionsoft $infusionsoft */
/** @var Logger $logger */
ini_set( 'max_execution_time', 0 );
require_once __DIR__ . '/../setup.php';

//
// Connect to Infusionsoft
//
$storage = new TokenStorage( TOKEN_STORAGE, $infusionsoft );

// Try to get a token from storage
$token = $storage->getToken( APP_NAME );

// If a token is available in storage, we tell the SDK to use that token for subsequent requests.
if ( ! empty( $token->getAccessToken() ) ) {
	$infusionsoft->setToken( $token );
}

// If no token or code lets get one
if ( ! $infusionsoft->getToken() ) {
	$logger->critical( 'Missing Infusionsoft Token. Visit ' . IFS_REDIRECT_URL . '?state=' . APP_NAME . ' to authorize' );
	die( 'Missing Token' );
}

// Try to refresh
$tokenData = $infusionsoft->refreshAccessToken();
$token     = $infusionsoft->getToken();
$storage->saveToken( APP_NAME, $token );
