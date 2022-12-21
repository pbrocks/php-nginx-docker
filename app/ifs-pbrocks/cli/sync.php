<?php
/**
 * run composer install to install the needed libs
 * SF creds are found on there site when you login under admin i believe
 *
 * Set where the token.txt file lives right now its in the with the script, make sure proper permission are set to update the file
 *
 * Tags, make sure tag ids are set in the variables
 *
 * set $ibkRedirect to point to the token creation.
 * $ibk vars need to be set to the dev account of ibk
 *
 * go here to set the inital token https://{yoururl}/sync.php?ibklogin=1 via a browser
 * you must run https://{yoururl}/testme/sync.php?refreshtoken=1 before 24 hours or the token will have to be manually updated again
 * you can run this via CLI as well php sync.php refreshtoken
 * the token also refreshes when the script is run.
 *
 * to run sync either from the web https://{yourul}/sync.php?runsync=1
 * or from CLI php sync.php runsync
 */

use Infusionsoft\Infusionsoft;
use Monolog\Logger;

set_time_limit( 0 );
if ( empty( session_id() ) ) {
	session_start();
}

/** @var Infusionsoft $infusionsoft */
/** @var Logger $logger */

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
	header( 'Location: ' . IFS_REDIRECT_URL . '?state=' . APP_NAME );
	die( 'Missing Token' );
}

// Try to refresh if necessary
if ( $infusionsoft->getToken()->endOfLife - time() < 7200 ) {
	$tokenData = $infusionsoft->refreshAccessToken();
	$token     = $infusionsoft->getToken();
	$storage->saveToken( APP_NAME, $token );
}


$logger->info( 'Start Sync' );
