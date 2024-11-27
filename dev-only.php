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
add_filter( 'determine_current_user', function ( $user_id ) {
	// Check the request headers
	$headers = getallheaders();
	
	if ( ! isset( $headers['Authorization'] ) ) {
		return 1;
	}
	
	$params = explode( ' ', $headers['Authorization'] );
	
	if ( $params[0] === 'as' ) {
		if ( is_numeric( $params[1] ) ) {
			return $params[1];
		}

		$user = get_user_by( 'login', $params[1] );
		
		if ( $user ) {
			return $user->ID;
		}
	}

	return $user_id;
}, 10, 1 );

// Modify REST API response to include all data
add_filter( 'rest_prepare_post', function ( $response, $post, $request ) {
	if ($request->get_header('user-agent') !== 'vscode-restclient') {
		return $response;
	}

	// remove all links
	foreach($response->get_links() as $_linkKey => $_linkVal) {
        $response->remove_link($_linkKey);
    }

	// Include all meta data
	$meta = get_post_meta( $post->ID );

	$skip = ['_edit_lock', '_edit_last', '_wp_old_slug', '_wp_page_template', '_wp_trash_meta_status'];
	// Loop through all meta data and unserialize the value
	$output = [];
	foreach ( $meta as $key => $value ) {
		if ( in_array( $key, $skip ) ) {
			continue;
		}

		foreach ( $value as $index => $val ) {
			$value[$index] = maybe_unserialize( $val );
		}

		$output[$key] = $value;
	}

	$response->data = $output;

	return $response;
}, 999, 3 );