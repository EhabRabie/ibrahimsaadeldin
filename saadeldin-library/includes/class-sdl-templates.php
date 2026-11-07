<?php
/**
 * Template routing and assets for articles (native posts) and videos.
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class SDL_Templates
 */
final class SDL_Templates {

	/**
	 * Template picked for the current request ('' when not ours).
	 *
	 * @var string
	 */
	private static $current = '';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'template_include' ), 99 );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'query' ) );
		add_filter( 'excerpt_more', '__return_empty_string' );
	}

	/**
	 * Which of our templates should render this request.
	 *
	 * @return string archive-articles | archive-videos | single-article | single-videos | ''
	 */
	public static function detect() {
		$search_type = is_search() ? get_query_var( 'post_type' ) : '';

		if ( is_post_type_archive( SDL_Videos::POST_TYPE ) || is_tax( SDL_Videos::TAXONOMY ) || SDL_Videos::POST_TYPE === $search_type ) {
			return 'archive-videos';
		}

		if ( is_singular( SDL_Videos::POST_TYPE ) ) {
			return 'single-videos';
		}

		if ( is_singular( 'post' ) ) {
			return 'single-article';
		}

		if ( is_home() || is_category() || is_tag() || ( is_date() && ! is_post_type_archive() ) || ( is_search() && 'post' === $search_type ) ) {
			return 'archive-articles';
		}

		return '';
	}

	/**
	 * Swap in our template.
	 *
	 * @param string $template Path chosen by WordPress.
	 * @return string
	 */
	public static function template_include( $template ) {
		self::$current = self::detect();

		if ( '' === self::$current ) {
			return $template;
		}

		$file = SDL_DIR . 'templates/' . self::$current . '.php';

		return is_readable( $file ) ? $file : $template;
	}

	/**
	 * Body class used to scope our CSS.
	 *
	 * @param array $classes Classes.
	 * @return array
	 */
	public static function body_class( $classes ) {
		if ( '' !== self::$current ) {
			$classes[] = 'sdl-template';
			$classes[] = 'sdl-' . self::$current;
		}

		return $classes;
	}

	/**
	 * Page size for the archives (1 featured + 8 cards, 12 videos).
	 *
	 * @param WP_Query $query Query.
	 * @return void
	 */
	public static function query( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_post_type_archive( SDL_Videos::POST_TYPE ) || $query->is_tax( SDL_Videos::TAXONOMY ) || ( $query->is_search() && SDL_Videos::POST_TYPE === $query->get( 'post_type' ) ) ) {
			$query->set( 'posts_per_page', 12 );
		} elseif ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_date() || ( $query->is_search() && 'post' === $query->get( 'post_type' ) ) ) {
			$query->set( 'posts_per_page', 9 );
		}
	}

	/**
	 * Enqueue assets: header fix everywhere, library assets only on our pages.
	 *
	 * @return void
	 */
	public static function enqueue() {
		wp_enqueue_style( 'sdl-header', SDL_URL . 'assets/css/header.css', array(), SDL_VERSION );

		if ( '' === self::$current ) {
			self::$current = self::detect();
		}

		if ( '' === self::$current ) {
			return;
		}

		wp_enqueue_style( 'sdl-library', SDL_URL . 'assets/css/library.css', array(), SDL_VERSION );
		wp_enqueue_script( 'sdl-library', SDL_URL . 'assets/js/library.js', array(), SDL_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
	}
}
