<?php

use Infusionsoft\Infusionsoft;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

if ( defined( 'DEBUG' ) && DEBUG ) {
	ini_set( 'display_errors', 1 );
	ini_set( 'display_startup_errors', 1 );
	error_reporting( E_ALL );
}

$required_settings = array(
	'BASE_URL',
	'IFS_CLIENT_KEY',
	'IFS_CLIENT_SECRET',
	'IFS_REDIRECT_URL',
);
foreach ( $required_settings as $required_setting ) {
	if ( ! defined( $required_setting ) || empty( constant( $required_setting ) ) ) {
		throw new Exception( "Missing required settings {$required_setting}" );
	}
}

/**
 * Infusionsoft Settings
 */
if ( ! defined( 'IFS_REDIRECT_URL' ) ) {
	define( 'IFS_REDIRECT_URL', BASE_URL . '/authorize.php' );
}

/**
 * Logging Settings
 */
define( 'LOG_KEEP_DAYS', 30 );
if ( ! defined( 'LOGGER_NAME' ) ) {
	define( 'LOGGER_NAME', 'log' );
}
if ( ! defined( 'LOGGER_FILE_NAME' ) ) {
	define( 'LOGGER_FILE_NAME', 'log.txt' );
}
if ( ! defined( 'LOG_DIRECTORY' ) ) {
	define( 'LOG_DIRECTORY', __DIR__ . '/logs/' );
}

$handler = new RotatingFileHandler( LOG_DIRECTORY . LOGGER_FILE_NAME, LOG_KEEP_DAYS, Logger::DEBUG );
$logger  = new Logger( LOGGER_NAME );
$logger->pushHandler( $handler );

$infusionsoft = new Infusionsoft(
	array(
		'clientId'     => IFS_CLIENT_KEY,
		'clientSecret' => IFS_CLIENT_SECRET,
		'redirectUri'  => IFS_REDIRECT_URL,
	)
);
