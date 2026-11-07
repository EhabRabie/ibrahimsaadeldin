<?php
/**
 * Videos archive: library, category and search results.
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	/* translators: %s: search phrase. */
	$title = sprintf( 'نتائج البحث عن: %s', get_search_query() );
} elseif ( is_tax() ) {
	$title = single_term_title( '', false );
} else {
	$title = 'مكتبة الفيديو القانونية';
}

$terms   = get_terms(
	array(
		'taxonomy'   => SDL_Videos::TAXONOMY,
		'hide_empty' => true,
	)
);
$current = is_tax() ? get_queried_object_id() : 0;
?>
<main id="primary" class="sdl-page">
	<?php
	sdl_part(
		'hero',
		array(
			'eyebrow'  => 'فيديوهات قانونية',
			'title'    => $title,
			'subtitle' => 'شروحات قصيرة وإرشادات عملية من محامين مختصين في أهم القضايا والمسائل القانونية.',
			'search'   => array(
				'type'        => 'videos',
				'placeholder' => 'ابحث في الفيديوهات...',
			),
		)
	);
	?>

	<div class="sdl-container">
		<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
			<nav class="sdl-filters" aria-label="تصنيفات الفيديوهات">
				<a class="<?php echo $current ? '' : 'is-active'; ?>" href="<?php echo esc_url( get_post_type_archive_link( SDL_Videos::POST_TYPE ) ); ?>">الكل</a>
				<?php foreach ( $terms as $term ) : ?>
					<a class="<?php echo (int) $term->term_id === $current ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="sdl-grid sdl-grid--3">
				<?php
				while ( have_posts() ) {
					the_post();
					sdl_part( 'video-card' );
				}
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => 'السابق',
					'next_text' => 'التالي',
					'class'     => 'sdl-pagination',
				)
			);
			?>
		<?php else : ?>
			<p class="sdl-empty">لا توجد فيديوهات مطابقة حاليًا.</p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
