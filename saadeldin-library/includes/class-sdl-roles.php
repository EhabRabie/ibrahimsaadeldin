<?php
/**
 * "Content Publisher" role: writes and publishes articles and videos, uploads
 * thumbnails, and nothing else (no plugins, themes, users or settings).
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class SDL_Roles
 */
final class SDL_Roles {

	const ROLE    = 'sd_publisher';
	const VERSION = '1';
	const OPTION  = 'sdl_roles_version';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_add_role' ), 20 );
	}

	/**
	 * Create or refresh the role once per version.
	 *
	 * @return void
	 */
	public static function maybe_add_role() {
		if ( get_option( self::OPTION ) !== self::VERSION || ! get_role( self::ROLE ) ) {
			self::add_role();
		}
	}

	/**
	 * Add the role.
	 *
	 * @return void
	 */
	public static function add_role() {
		remove_role( self::ROLE );
		add_role(
			self::ROLE,
			'ناشر المحتوى',
			array(
				'read'                   => true,
				'edit_posts'             => true,
				'edit_published_posts'   => true,
				'publish_posts'          => true,
				'delete_posts'           => true,
				'delete_published_posts' => true,
				'upload_files'           => true,
			)
		);
		update_option( self::OPTION, self::VERSION, false );
	}
}
