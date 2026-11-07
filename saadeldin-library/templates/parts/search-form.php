<?php
/**
 * Search form with live suggestions.
 *
 * @package SaadEldinLibrary
 *
 * @var array $args { type: 'post'|'videos', placeholder: string }
 */

defined( 'ABSPATH' ) || exit;

$type        = isset( $args['type'] ) ? $args['type'] : 'post';
$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : 'ابحث...';
$endpoint    = rest_url( 'videos' === $type ? 'wp/v2/videos' : 'wp/v2/posts' );
$uid         = wp_unique_id( 'sdl-s-' );
?>
<form class="sdl-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-endpoint="<?php echo esc_url( $endpoint ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $uid ); ?>">بحث</label>
	<input type="search" id="<?php echo esc_attr( $uid ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" autocomplete="off" aria-autocomplete="list" aria-controls="<?php echo esc_attr( $uid ); ?>-list">
	<input type="hidden" name="post_type" value="<?php echo esc_attr( $type ); ?>">
	<button type="submit">بحث</button>
	<ul class="sdl-search__list" id="<?php echo esc_attr( $uid ); ?>-list" role="listbox" hidden></ul>
</form>
