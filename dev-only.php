<?php
/*
Plugin Name: Dev Only
Plugin URI: https://github.com/tanftw/mu-plugins
Description: Utility plugin for development only. Do not use in production. If you seen this in production, delete it immediately. I don't know how it got there.
Author: Tan Nguyen
Version: 0.0.0-alpha
Author URI: https://tan.ng/
*/

// Functions for debugging
function ddd( $object ) {
	echo '<pre>';
	print_r( $object );
	exit;
}

// Functions for debugging AJAX and Webhooks
function consolelog( $object ) {
	error_log( print_r( $object, true ) );
}

function clv( $object ) {
	error_log( var_export( $object, true ) );
}

// Always login REST API as admin
add_filter( 'determine_current_user', fn() => 1 );
