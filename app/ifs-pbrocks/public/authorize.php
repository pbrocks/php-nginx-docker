<?php

/** @var Infusionsoft $infusionsoft */

use Infusionsoft\Infusionsoft;

require_once __DIR__ . '/../setup.php';

//
// Connect to Infusionsoft
//
$storage = new TokenStorage( TOKEN_STORAGE );

// Try to get a token from storage
$token = $storage->getToken( APP_NAME );

// If a token is available in storage, we tell the SDK to use that token for subsequent requests.
if ( ! empty( $token->getAccessToken() ) ) {
	$infusionsoft->setToken( $token );
}

// If we are returning from Infusionsoft we need to exchange the code for an access token.
// We redirect back to the page to prevent sending the same code to Infusionsoft twice.
if ( isset( $_GET['code'] ) ) {
	$infusionsoft->requestAccessToken( $_GET['code'] );
	$token = $infusionsoft->getToken();
	$storage->saveToken( APP_NAME, $token );
	header( 'Location: ' . IFS_REDIRECT_URL . '?state=' . APP_NAME );
	die();
}

// Try to refresh if necessary
if ( $infusionsoft->getToken() && $infusionsoft->getToken()->endOfLife - time() < 7200 ) {
	$tokenData = $infusionsoft->refreshAccessToken();
	$token     = $infusionsoft->getToken();
	$storage->saveToken( APP_NAME, $token );
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Authorize Infusionsoft</title>
	<meta name="author" content="Brandon Rumiser - The API Guys"/>

	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3 text-center">
			<?php
			// If no token or code lets get one
			if ( ! $infusionsoft->getToken() ) :
				?>
				<h2 class="form-signin-heading">Please Authorize Infusionsoft</h2>
				<a class="btn btn-lg btn-primary btn-block"
				   href="<?php echo $infusionsoft->getAuthorizationUrl( APP_NAME ); ?>">Authorize</a>
			<?php else : ?>
				<h2 class="form-signin-heading">Infusionsoft Authorized</h2>
			<?php endif; ?>
		</div>
	</div>
</div>

</body>
</html>
