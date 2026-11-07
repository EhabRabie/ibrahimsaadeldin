<?php
/**
 * Articles archive: posts page, category, tag, date and search results.
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	/* translators: %s: search phrase. */
	$title = sprintf( 'نتائج البحث عن: %s', get_search_query() );
} elseif ( is_category() || is_tag() ) {
	$title = single_term_title( '', false );
} elseif ( is_date() ) {
	$title = get_the_archive_title();
} else {
	$title = 'الرؤى القانونية والمقالات التحليلية';
}

$is_front = is_home() && ! is_paged();
?>
<main id="primary" class="sdl-page">
	<?php
	sdl_part(
		'hero',
		array(
			'eyebrow'  => 'المكتبة القانونية',
			'title'    => $title,
			'subtitle' => is_home() ? 'مقالات ودراسات مبسّطة يكتبها فريق مؤسسة سعد الدين للمحاماة والتحكيم.' : '',
			'search'   => array(
				'type'        => 'post',
				'placeholder' => 'ابحث في المقالات...',
			),
		)
	);
	?>

	<div class="sdl-container sdl-layout">
		<div class="sdl-main">
			<?php if ( have_posts() ) : ?>
				<?php
				if ( $is_front || ( ! is_paged() && ! is_search() ) ) {
					the_post();
					sdl_part( 'article-card', array( 'featured' => true ) );
				}
				?>

				<?php if ( have_posts() ) : ?>
					<div class="sdl-grid sdl-grid--2">
						<?php
						while ( have_posts() ) {
							the_post();
							sdl_part( 'article-card' );
						}
						?>
					</div>
				<?php endif; ?>

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
				<p class="sdl-empty">لا توجد مقالات مطابقة حاليًا.</p>
			<?php endif; ?>
		</div>

		<?php sdl_part( 'sidebar-articles' ); ?>
	</div>
</main>
<?php
get_footer();
