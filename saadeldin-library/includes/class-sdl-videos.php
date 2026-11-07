<?php
/**
 * Videos custom post type, category taxonomy, YouTube URL field and schema.
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class SDL_Videos
 */
final class SDL_Videos {

	const POST_TYPE = 'videos';
	const TAXONOMY  = 'video_category';
	const META_KEY  = 'youtube_url';
	const NONCE     = 'sdl_save_video';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'add_meta_boxes_' . self::POST_TYPE, array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
		add_filter( 'redirect_post_location', array( __CLASS__, 'flag_invalid_url' ), 10, 2 );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_action( 'wp_head', array( __CLASS__, 'schema' ) );
	}

	/**
	 * Register the post type, taxonomy and meta.
	 *
	 * @return void
	 */
	public static function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'          => array(
					'name'               => 'الفيديوهات',
					'singular_name'      => 'فيديو',
					'add_new'            => 'إضافة فيديو',
					'add_new_item'       => 'إضافة فيديو جديد',
					'edit_item'          => 'تعديل الفيديو',
					'new_item'           => 'فيديو جديد',
					'view_item'          => 'عرض الفيديو',
					'search_items'       => 'بحث في الفيديوهات',
					'not_found'          => 'لا توجد فيديوهات.',
					'not_found_in_trash' => 'لا توجد فيديوهات في سلة المهملات.',
					'all_items'          => 'كل الفيديوهات',
					'menu_name'          => 'الفيديوهات',
				),
				'public'          => true,
				'show_in_rest'    => true,
				'rest_base'       => 'videos',
				'menu_position'   => 21,
				'menu_icon'       => 'dashicons-video-alt3',
				'capability_type' => 'post',
				'map_meta_cap'    => true,
				'has_archive'     => 'videos',
				'rewrite'         => array(
					'slug'       => 'videos',
					'with_front' => false,
				),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			)
		);

		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			array(
				'labels'            => array(
					'name'          => 'تصنيفات الفيديوهات',
					'singular_name' => 'تصنيف',
					'menu_name'     => 'التصنيفات',
					'add_new_item'  => 'إضافة تصنيف جديد',
					'all_items'     => 'كل التصنيفات',
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'videos/category',
					'with_front' => false,
				),
			)
		);

		register_post_meta(
			self::POST_TYPE,
			self::META_KEY,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => false,
				'sanitize_callback' => array( __CLASS__, 'sanitize_url' ),
				'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);
	}

	/**
	 * Keep only valid YouTube URLs.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public static function sanitize_url( $value ) {
		$url = esc_url_raw( trim( (string) $value ), array( 'https', 'http' ) );

		return '' !== sdl_youtube_id( $url ) ? $url : '';
	}

	/**
	 * Meta box with the single YouTube field.
	 *
	 * @return void
	 */
	public static function add_meta_box() {
		add_meta_box(
			'sdl-youtube',
			'رابط فيديو YouTube',
			array( __CLASS__, 'render_meta_box' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render the meta box.
	 *
	 * @param WP_Post $post Post.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		$url = (string) get_post_meta( $post->ID, self::META_KEY, true );
		$id  = sdl_youtube_id( $url );

		wp_nonce_field( self::NONCE, self::NONCE );
		?>
		<p>
			<label for="sdl-youtube-url"><strong>الصق رابط الفيديو من YouTube</strong></label>
			<input type="url" id="sdl-youtube-url" name="<?php echo esc_attr( self::META_KEY ); ?>" value="<?php echo esc_attr( $url ); ?>" class="widefat" dir="ltr" placeholder="https://www.youtube.com/watch?v=XXXXXXXXXXX" />
		</p>
		<p class="description">يعمل مع الروابط العادية والمختصرة (youtu.be) وShorts. لا يتم رفع أي ملف فيديو على الموقع. أضف صورة مصغرة من "الصورة البارزة"، وإلا ستُستخدم صورة YouTube تلقائيًا.</p>
		<?php if ( $id ) : ?>
			<p><img src="<?php echo esc_url( 'https://i.ytimg.com/vi/' . rawurlencode( $id ) . '/mqdefault.jpg' ); ?>" alt="" style="max-width:240px;height:auto;border-radius:6px"></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Save the YouTube URL.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post.
	 * @return void
	 */
	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE ] ) ), self::NONCE ) ) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$raw = isset( $_POST[ self::META_KEY ] ) ? trim( wp_unslash( $_POST[ self::META_KEY ] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized below.
		$url = self::sanitize_url( $raw );

		if ( '' === $url ) {
			delete_post_meta( $post_id, self::META_KEY );
		} else {
			update_post_meta( $post_id, self::META_KEY, $url );
		}

		// Remember whether the editor typed something we rejected.
		set_transient( 'sdl_invalid_' . $post_id, ( '' !== $raw && '' === $url ) ? 1 : 0, MINUTE_IN_SECONDS );
	}

	/**
	 * Add a flag to the redirect URL when the link was rejected.
	 *
	 * @param string $location Redirect URL.
	 * @param int    $post_id  Post ID.
	 * @return string
	 */
	public static function flag_invalid_url( $location, $post_id ) {
		if ( self::POST_TYPE === get_post_type( $post_id ) && get_transient( 'sdl_invalid_' . $post_id ) ) {
			delete_transient( 'sdl_invalid_' . $post_id );
			$location = add_query_arg( 'sdl_invalid_url', 1, $location );
		}

		return $location;
	}

	/**
	 * Notice shown after saving with an invalid link, or without any link.
	 *
	 * @return void
	 */
	public static function admin_notice() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || self::POST_TYPE !== $screen->post_type || 'post' !== $screen->base ) {
			return;
		}

		if ( isset( $_GET['sdl_invalid_url'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<div class="notice notice-error"><p>رابط YouTube غير صالح، لم يتم حفظه. مثال صحيح: https://www.youtube.com/watch?v=XXXXXXXXXXX</p></div>';
			return;
		}

		global $post;

		if ( $post && 'publish' === $post->post_status && '' === sdl_video_id( $post ) ) {
			echo '<div class="notice notice-warning"><p>هذا الفيديو منشور بدون رابط YouTube صالح، لذلك لن يظهر المشغّل.</p></div>';
		}
	}

	/**
	 * List table columns.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public static function columns( $columns ) {
		$new = array();

		foreach ( $columns as $key => $label ) {
			if ( 'title' === $key ) {
				$new['sdl_thumb'] = 'الصورة';
			}
			$new[ $key ] = $label;
		}

		$new['sdl_youtube'] = 'YouTube';

		return $new;
	}

	/**
	 * Column output.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public static function column_content( $column, $post_id ) {
		if ( 'sdl_thumb' === $column ) {
			echo wp_kses_post( sdl_video_thumbnail( $post_id, array( 80, 60 ) ) );
		}

		if ( 'sdl_youtube' === $column ) {
			$id = sdl_video_id( $post_id );
			echo $id ? '<span style="color:#1a7f37">&#10003; ' . esc_html( $id ) . '</span>' : '<span style="color:#b32d2e">لا يوجد رابط</span>';
		}
	}

	/**
	 * VideoObject structured data on single video pages.
	 *
	 * @return void
	 */
	public static function schema() {
		if ( ! is_singular( self::POST_TYPE ) ) {
			return;
		}

		$id = sdl_video_id();

		if ( '' === $id ) {
			return;
		}

		$description = wp_strip_all_tags( get_the_excerpt() );
		$data        = array(
			'@context'     => 'https://schema.org',
			'@type'        => 'VideoObject',
			'name'         => get_the_title(),
			'description'  => '' !== $description ? $description : get_the_title(),
			'thumbnailUrl' => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : 'https://i.ytimg.com/vi/' . rawurlencode( $id ) . '/hqdefault.jpg',
			'uploadDate'   => get_the_date( 'c' ),
			'embedUrl'     => sdl_embed_url( $id ),
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>' . "\n";
	}
}
