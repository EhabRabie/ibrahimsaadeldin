<?php
/**
 * Production config for Docker/Render. All secrets come from environment variables.
 */
define( 'DB_NAME', getenv( 'DB_NAME' ) );
define( 'DB_USER', getenv( 'DB_USER' ) );
define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) );
define( 'DB_HOST', getenv( 'DB_HOST' ) );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) as $k ) {
	define( $k, getenv( $k ) ?: hash( 'sha256', $k . getenv( 'DB_PASSWORD' ) ) );
}

$table_prefix = getenv( 'TABLE_PREFIX' ) ?: 'wp_';

$site = getenv( 'SITE_URL' );
if ( $site ) {
	define( 'WP_HOME', $site );
	define( 'WP_SITEURL', $site );
}
if ( ( $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '' ) === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}
define( 'WP_DEBUG', false );
define( 'FS_METHOD', 'direct' );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
