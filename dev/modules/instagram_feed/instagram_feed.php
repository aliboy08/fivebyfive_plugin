<?php
/**
 * Module Name: Instagram Feed
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) die(); 

define( 'FF_INSTA', [
	'app_id' => get_option('ff_instagram_app_id'),
	'app_secret' => get_option('ff_instagram_app_secret'),
	'webhook_verify_token' => get_option('ff_instagram_webhook_verify_token'),
	'redirect_uri' => get_option('ff_instagram_redirect_uri'),
	'access_token' => get_option('ff_instagram_initial_access_token'),
]);

include 'class-ff-instagram.php';