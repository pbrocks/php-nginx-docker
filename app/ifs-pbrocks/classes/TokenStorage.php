<?php
/**
 * Created by The API Guys.
 * User: Brandon
 * Date: 9/23/2016
 * Time: 15:48
 */

use Infusionsoft\Infusionsoft;

class TokenStorage {

	public $file = '';
	protected $ifs;
	public $errors = array();
	/**
	 * @var Monolog\Logger
	 */
	public $logger;

	public function __construct( $file, Infusionsoft $infusionsoft = null ) {
		$this->file = $file;
		if ( ! file_exists( $this->file ) ) {
			$fp = fopen( $this->file, 'wb' );
			fwrite( $fp, "<?php\n//" );
			fclose( $fp );
		}
		if ( $infusionsoft ) {
			$this->ifs = $infusionsoft;
		}
	}

	public function log( $level, $message, $context = array() ) {
		if ( ! empty( $this->logger ) ) {
			$this->logger->log( $level, $message, $context );
		}
	}

	public function setLogger( Monolog\Logger $logger ) {
		$this->logger = $logger;
	}

	public function saveToken( $appDomainName, $token ) {
		$data                   = $this->readFile();
		$data[ $appDomainName ] = serialize( $token );
		file_put_contents( $this->file, "<?php\n//" . json_encode( $data ) );
	}

	public function deleteToken( $appDomainName ) {
		$data = $this->readFile();
		unset( $data[ $appDomainName ] );
		file_put_contents( $this->file, "<?php\n//" . json_encode( $data ) );
	}

	public function getToken( $appDomainName ) {
		$data = $this->readFile();
		if ( isset( $data[ $appDomainName ] ) ) {
			$token = unserialize( $data[ $appDomainName ] );
		} else {
			$token = new Token();
		}
		return $token;
	}

	public function readFile() {
		if ( file_exists( $this->file ) ) {
			$fileContents = file_get_contents( $this->file );
			$fileContents = substr( $fileContents, 8 );
			$data         = json_decode( $fileContents, true );
			return $data;
		} else {
			$data = array();
			return $data;
		}
	}

	public function getFirstAppName() {
		$data = $this->readFile();
		return array_keys( $data )[0];
	}


	public function refreshToken( $appDomainName, Token $token ) {
		// If a token is available in storage, we tell the SDK to use that token for subsequent requests.
		if ( empty( $token->getAccessToken() ) ) {
			$this->errors[] = "$appDomainName does not have access token to refresh";
			$this->log( \Monolog\Logger::WARNING, "$appDomainName does not have access token to refresh" );
			$this->deleteToken( $appDomainName );
			throw new Exception( "$appDomainName does not have access token to refresh" );
		}
		$this->ifs->setToken( $token );
		// Try to refresh if necessary
		try {
			$tokenData = $this->ifs->refreshAccessToken();
			$token     = $this->ifs->getToken();
			$this->saveToken( $appDomainName, $token );
		} catch ( Exception $e ) {
			$this->errors[] = $e->getCode() . ': ' . $e->getMessage();
			$this->log( \Monolog\Logger::WARNING, $e->getCode() . ': ' . $e->getMessage(), (array) $e );
			try {
				$tokenData = $this->ifs->refreshAccessToken();
				$token     = $this->ifs->getToken();
				$this->saveToken( $appDomainName, $token );
			} catch ( Exception $e ) {
				$this->errors[] = 'Retry:' . $e->getCode() . ': ' . $e->getMessage();
				$this->log( \Monolog\Logger::WARNING, 'Retry: ' . $e->getCode() . ': ' . $e->getMessage(), (array) $e );
				throw new Exception( $e->getMessage(), $e->getCode(), $e );
			}
		}
		return $this;

	}

	public function refreshTokens() {
		if ( ! $this->ifs ) {
			$this->ifs = new InfusionsoftRateLimiter(
				array(
					'clientId'     => IFS_CLIENT_KEY,
					'clientSecret' => IFS_CLIENT_SECRET,
					'redirectUri'  => IFS_REDIRECT_URL,
				)
			);
		}

		$data = $this->readFile();

		foreach ( $data as $key => $datum ) {
			if ( ! empty( $key ) ) {
				try {
					$this->log( \Monolog\Logger::INFO, "Trying to refresh token for $key" );
					$token   = unserialize( $datum );
					$refresh = $this->refreshToken( $key, $token );
				} catch ( \Exception $e ) {
					$this->log( \Monolog\Logger::WARNING, $e->getCode() . ': ' . $e->getMessage(), (array) $e );
					$this->errors[] = $e->getCode() . ': ' . $e->getMessage();
				}
			}
		}
	}
}
