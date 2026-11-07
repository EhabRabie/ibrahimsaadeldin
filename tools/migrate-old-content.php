<?php
/**
 * One-off migration: old saadeldin-content CPTs -> native posts + `videos` CPT.
 * Run with the old plugin deactivated and saadeldin-library active.
 */
define( 'WP_USE_THEMES', false );
require 'C:/Users/ZBOOK/Documents/Default Project/IbrahimSaad/public_html/wp-load.php';
global $wpdb;

// Old taxonomies are no longer registered; register them so term APIs work.
register_taxonomy( 'article_category', 'post', array( 'hierarchical' => true ) );
register_taxonomy( 'article_tag', 'post' );

$cat_map = array(); // old term_id => new category term_id.

// ---- Articles -> posts ----
$articles = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type='article'" );
foreach ( $articles as $id ) {
	$cats = wp_get_object_terms( $id, 'article_category' );
	$tags = wp_get_object_terms( $id, 'article_tag', array( 'fields' => 'names' ) );

	$new_cats = array();
	foreach ( $cats as $c ) {
		$exists = term_exists( $c->name, 'category' );
		$tid    = $exists ? (int) $exists['term_id'] : (int) wp_insert_term( $c->name, 'category', array( 'description' => $c->description ) )['term_id'];
		$new_cats[] = $tid;
	}

	$wpdb->update( $wpdb->posts, array( 'post_type' => 'post' ), array( 'ID' => $id ) );
	clean_post_cache( $id );

	wp_set_object_terms( $id, $new_cats ? $new_cats : array( (int) get_option( 'default_category' ) ), 'category' );
	wp_set_object_terms( $id, is_wp_error( $tags ) ? array() : $tags, 'post_tag' );
	wp_delete_object_term_relationships( $id, array( 'article_category', 'article_tag' ) );

	foreach ( array( '_sd_reading_time', '_sd_reviewed' ) as $k ) {
		delete_post_meta( $id, $k );
	}
	echo "article #$id -> post\n";
}

// ---- Videos -> videos CPT ----
$videos = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type='video'" );
foreach ( $videos as $id ) {
	$old_url  = (string) get_post_meta( $id, '_sd_video_url', true );
	$yt       = sdl_youtube_id( $old_url );
	$update   = array( 'post_type' => 'videos' );

	$wpdb->update( $wpdb->posts, $update, array( 'ID' => $id ) );
	clean_post_cache( $id );

	if ( $yt ) {
		update_post_meta( $id, 'youtube_url', esc_url_raw( $old_url ) );
		echo "video #$id -> videos (youtube $yt)\n";
	} else {
		// Not a YouTube link: cannot be represented, keep as draft for the editor.
		$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array( 'ID' => $id ) );
		clean_post_cache( $id );
		echo "video #$id -> videos (DRAFT, no YouTube link: $old_url)\n";
	}

	foreach ( array( '_sd_video_url', '_sd_video_provider', '_sd_video_id', '_sd_video_valid', '_sd_video_thumb', '_sd_video_duration_seconds', '_sd_video_duration', '_sd_reviewed', '_sd_reading_time' ) as $k ) {
		delete_post_meta( $id, $k );
	}
}

// ---- Remove old taxonomy terms ----
foreach ( array( 'article_category', 'article_tag' ) as $tx ) {
	$terms = get_terms( array( 'taxonomy' => $tx, 'hide_empty' => false ) );
	foreach ( $terms as $t ) {
		wp_delete_term( $t->term_id, $tx );
	}
	echo "removed terms of $tx\n";
}

// ---- Articles page (posts page) so /articles/ keeps working ----
$page = get_page_by_path( 'articles' );
if ( ! $page ) {
	$page_id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_title'  => 'المقالات',
			'post_name'   => 'articles',
		)
	);
} else {
	$page_id = $page->ID;
}
update_option( 'page_for_posts', $page_id );
echo "page_for_posts = $page_id\n";

// ---- Old options ----
delete_option( 'sd_content_settings' );
delete_option( 'sd_roles_version' );

flush_rewrite_rules();
echo "done\n";
