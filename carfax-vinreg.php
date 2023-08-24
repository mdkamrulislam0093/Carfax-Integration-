<?php
/**
 * Plugin Name: Carfax LP
 * Description: This plugin will integrate carfax.
 * Plugin URI: http://#
 * Author: Kamrul Islam
 * Author URI: http://#
 * Version: 1.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;


add_action('init', function(){

	if( isset( $_GET['lp'] ) ){

		$get_access_token = wp_remote_post(
      'https://was.carfax.eu/v1/oauth/token',
      [
        'headers' => [
          'Content-Type' => 'application/json'
        ],
        'body' => json_encode( [
          'client_id' => 'ZyJuJ1GWuF1jc74J08j4RAqQBiPGx8e7',
          'client_secret' => 'FPdk4gcWc_8Gq5NRX6AueErFzG_BOYy47dM7UG6D28FMwHjGxNDVD5vhUXt1CJ34',
          'grant_type' => 'client_credentials',
          'audience' => 'https://was.carfax.eu'
        ] )
      ]
    );

    if( is_wp_error( $get_access_token ) ){
    	error_log('Failed: '. print_r($get_access_token, true));
    } else {
    	$token = json_decode( wp_remote_retrieve_body( $get_access_token ), true );

    	error_log('Success: '. print_r($token, true));

    	if( isset( $token['access_token'] ) ){

    		// Get Number Details

    		$id = 5100016499;
    		$svc_id = 901;
    		$vinreg = esc_attr( $_GET['lp'] );
    		$tsvc_id = 201;


    		$url = sprintf('http://was.carfax.eu/v0/api/ccdid/%s/svc/%s/vinreg/%s/targetsvc/%s', $id, $svc_id, $vinreg, $tsvc_id );

    		$response = wp_remote_get(
    			$url,
    			[
    				'headers' => 'Authorization: Bearer '. $token['access_token']
    			]
    		);

    		if( is_wp_error( $response ) ){
    			error_log('Failed: '. print_r($get_access_token, true));
    		} else {
    			$details = json_decode( wp_remote_retrieve_body( $response ), true );

    			error_log('Success: '. print_r($details, true));

    			$url = $details['url'] ?? '';
    			error_log('Success: '. print_r($url, true));

    			wp_redirect( $url );
    			die();
    		}

    	}
    }

	}
});