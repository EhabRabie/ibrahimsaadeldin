<?php
/**
 * Shared helpers.
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extract the 11-character YouTube ID from a URL (watch, youtu.be, shorts,
 * live, embed). Returns an empty string for anything else.
 *
 * @param string $url Video URL.
 * @return string
 */
function sdl_youtube_id( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	$pattern = '~^https?://(?:www\.|m\.)?(?:youtube(?:-nocookie)?\.com|youtu\.be)/(?:watch\?(?:[^#]*&)?v=|embed/|shorts/|live/|v/)?([A-Za-z0-9_-]{11})(?:[?&/#].*)?$~i';

	return preg_match( $pattern, $url, $matches ) ? $matches[1] : '';
}

/**
 * Privacy-friendly embed URL.
 *
 * @param string $id       YouTube ID.
 * @param bool   $autoplay Whether to autoplay.
 * @return string
 */
function sdl_embed_url( $id, $autoplay = false ) {
	$args = array(
		'rel'            => 0,
		'modestbranding' => 1,
	);

	if ( $autoplay ) {
		$args['autoplay'] = 1;
	}

	return add_query_arg( $args, 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $id ) );
}

/**
 * YouTube ID saved on a video post.
 *
 * @param int|WP_Post|null $post Post.
 * @return string
 */
function sdl_video_id( $post = null ) {
	$post = get_post( $post );

	return $post ? sdl_youtube_id( get_post_meta( $post->ID, 'youtube_url', true ) ) : '';
}

/**
 * Thumbnail <img> for a video: the uploaded featured image, otherwise the
 * YouTube thumbnail (hotlinked, nothing stored locally).
 *
 * @param int|WP_Post|null $post Post.
 * @param string           $size Image size for featured images.
 * @return string
 */
function sdl_video_thumbnail( $post = null, $size = 'medium_large' ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	if ( has_post_thumbnail( $post ) ) {
		return get_the_post_thumbnail( $post, $size, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
	}

	$id = sdl_video_id( $post );

	if ( '' === $id ) {
		return '';
	}

	return sprintf(
		'<img src="%s" alt="%s" loading="lazy" decoding="async" width="480" height="360">',
		esc_url( 'https://i.ytimg.com/vi/' . rawurlencode( $id ) . '/hqdefault.jpg' ),
		esc_attr( get_the_title( $post ) )
	);
}

/**
 * Where the "Request consultation" button goes. Filterable so the number can
 * be changed without editing the plugin.
 *
 * @return string
 */
function sdl_consultation_url() {
	$text = rawurlencode( 'مرحبًا، أرغب في طلب استشارة قانونية.' );

	return apply_filters( 'sdl_consultation_url', 'https://wa.me/201101202331?text=' . $text );
}

/**
 * Include a template part with arguments.
 *
 * @param string $name Part name inside templates/parts.
 * @param array  $args Variables for the part.
 * @return void
 */
function sdl_part( $name, array $args = array() ) {
	load_template( SDL_DIR . 'templates/parts/' . $name . '.php', false, $args );
}

/**
 * Plain-text category label for a post (first category).
 *
 * @param int|WP_Post|null $post Post.
 * @return string Escaped HTML link, or empty string.
 */
function sdl_primary_term_link( $post = null ) {
	$post     = get_post( $post );
	$taxonomy = 'videos' === get_post_type( $post ) ? 'video_category' : 'category';
	$terms    = get_the_terms( $post, $taxonomy );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}

	$term = reset( $terms );

	if ( 'category' === $taxonomy && 1 === (int) $term->term_id && 'uncategorized' === $term->slug ) {
		return '';
	}

	$link = get_term_link( $term );

	if ( is_wp_error( $link ) ) {
		return '';
	}

	return '<a class="sdl-chip" href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a>';
}
