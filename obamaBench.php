<?php
require 'lib/FastCGIClient.php';

define( 'FCGI_HOST', '127.0.0.1' );
define( 'FCGI_PORT', '9009' );
define( 'MW_INSTALL_PATH', realpath( getcwd() ) . '/mediawiki' );

function get_page( $title ) {
	$title = urlencode( strtr( $title, array( ' ' => '_' ) ) );
	$client = new Adoy\FastCGI\Client( FCGI_HOST, FCGI_PORT );
	$client->setConnectTimeout( 10000 );
	$client->setReadWriteTimeout( 10000 );
	return $client->request( array(
		'SERVER_SOFTWARE'   => 'mwbench',
		'SERVER_NAME'       => '127.0.0.1',
		'SERVER_ADDR'       => '10.0.2.15',
		'SERVER_PORT'       => '80',
		'REMOTE_ADDR'       => '10.20.30.40',
		'DOCUMENT_ROOT'     => MW_INSTALL_PATH,
		'SCRIPT_FILENAME'   => '/' . MW_INSTALL_PATH . '/index.php',
		'REMOTE_PORT'       => '63827',
		'GATEWAY_INTERFACE' => 'CGI/1.1',
		'SERVER_PROTOCOL'   => 'HTTP/1.1',
		'REQUEST_METHOD'    => 'GET',
		'QUERY_STRING'      => 'title=' . $title,
		'REQUEST_URI'       => '/w/index.php',
	), false );
}

function bench( $title, $loops = 20, $warmup_loops = 20 ) {
	while ( $warmup_loops-- ) get_page( $title );
	$best = INF;
	while ( $loops-- ) {
		$start = microtime( true );
		get_page( $title );
		$best = min( microtime( true ) - $start, $best );
	}
	return round( $best * 1000 );
}

$best = bench( 'Barack_Obama' );
echo "Best of 20 loops: {$best}ms.\n";
