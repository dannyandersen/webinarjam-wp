<?php

/*
Plugin Name: WebinarJam for WP
Plugin URI: https://github.com/dannyandersen/webinarjam-wp
Description: This Plugin allows your website users to connect directly to your WebinarJam webinar without having to fill out their details again.
Version: 1.0
Author: Danny Andersen
Author URI: http://bang-andersen.com
License: GPL2
*/

// Shortcode for WebnarJam User

function webinarjam_scode_user( $atts, $content = null ) {
    $atts = shortcode_atts(
        array(
            'webicode' => '',
            'memberid' => '',
            'schedule' => '1',
            'onlyusers' => 'yes',
            'buttontext' => 'Click to start the webinar',
        ), $atts, 'webinarjam_user' );

    ob_start();
    require('inc/webinarjam-wp-user.php');
    $content = ob_get_clean();

    return $content;

}

add_shortcode( 'webinarjam_user', 'webinarjam_scode_user' );

function webinarjam_wp_styles() {

    wp_enqueue_style( 'webinarjam_wp_css', plugins_url( 'webinarjam-wp/css/webinarjam-wp.css' ) );

}
add_action( 'wp_enqueue_scripts', 'webinarjam_wp_styles' );
