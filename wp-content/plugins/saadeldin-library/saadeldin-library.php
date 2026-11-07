<?php
/**
 * Plugin Name:       Saad Eldin Library
 * Plugin URI:        https://ibrahimsaadeldin.com/
 * Description:       Legal articles (native posts) and a YouTube video library for Saad Eldin Law Firm. Videos are YouTube links only: no video files are stored on the host.
 * Version:           2.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Saad Eldin Law Firm
 * License:           GPL-2.0-or-later
 * Text Domain:       saadeldin-library
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;

define( 'SDL_VERSION', '2.0.0' );
define( 'SDL_FILE', __FILE__ );
define( 'SDL_DIR', plugin_dir_path( __FILE__ ) );
define( 'SDL_URL', plugin_dir_url( __FILE__ ) );

require_once SDL_DIR . 'includes/helpers.php';
require_once SDL_DIR . 'includes/class-sdl-videos.php';
require_once SDL_DIR . 'includes/class-sdl-roles.php';
require_once SDL_DIR . 'includes/class-sdl-templates.php';

SDL_Videos::init();
SDL_Roles::init();
SDL_Templates::init();

register_activation_hook(
	__FILE__,
	static function () {
		SDL_Videos::register();
		SDL_Roles::add_role();
		flush_rewrite_rules();
	}
);

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
