<?php

final class SOCurl {

	private $curl;
	private $config = null;

	public function __construct() {
		$this->config = array(
			'method' => 'GET',
			'timeout' => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent' => $this->_get_user_agent(),
			'blocking' => true,
			'headers' => array(),
			'cookies' => array(),
			'body' => null,
			'compress' => false,
			'decompress' => true,
			'sslverify' => false,
			'stream' => false,
			'filename' => null,
		);
	}

	private function _get_user_agent() {
		$agents = array(
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Version/3.1 Safari/525.13',
			'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.53 Safari/525.19',
			'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_7; en-US) AppleWebKit/531.0 (KHTML, like Gecko) Chrome/3.0.183 Safari/531.0',
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1) Gecko/20090624 Firefox/3.5 (.NET CLR 3.5.30729)',
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.0.13) Gecko/2009073022 Firefox/3.0.13',
			'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10.5; en-US; rv:1.9.0.3) Gecko/2008092414 Firefox/3.0.3',
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)',
			'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)',
			'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; FunWebProducts; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506; Windows-Media-Player/10.00.00.3990)',
			'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Meridio for Excel 5.0.251; Meridio for PowerPoint 5.0.251; Meridio for Word 5.0.251; Meridio Protocol; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
		);

		shuffle( $agents );
		return $agents[0];
	}

	public function fetch_page( $url ) {
		$resp = wp_remote_get( $url, $this->config );

		if ( is_wp_error( $resp ) || $resp['response']['code'] != 200 ) {
			/* @var $resp WP_Error */
			if ( is_wp_error( $resp ) ) {
				$error = $resp->get_error_message();
				$errno = $resp->get_error_code();
			} else {
				$error = -1;
				$errno = null;
			}
			return array(
				'error' => $error,
				'errno' => $errno,
				'info' => array( 'http_code' => 404 ), // default to Not found
				'body' => null,
			);
		} else {

			return array(
				'error' => 0,
				'errno' => 0,
				'info' => array( 'http_code' => $resp['response']['code'] ),
				'body' => $resp['body'],
			);
		}
	}

	public function fetch_image_dimensions( $url ) {
		$resp = wp_remote_get( $url, $this->config );

		if ( is_wp_error( $resp ) || $resp['response']['code'] != 200 ) {
			/* @var $resp WP_Error */
			if ( is_wp_error( $resp ) ) {
				$error = $resp->get_error_message();
				$errno = $resp->get_error_code();
			} else {
				$error = -1;
				$errno = null;
			}
			return array(
				'error' => $error,
				'errno' => $errno,
				'info' => array( 'http_code' => 404 ), // default to Not found
				'body' => null,
			);
		} else {
			$img = imagecreatefromstring( $resp['body'] );
			if ( $img == null ) {
				return null;
			} else {
				return array(
					'width' => imagesx( $img ),
					'height' => imagesy( $img ),
				);
			}
		}
	}

}
