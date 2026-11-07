<?php
/**
 * Articles sidebar: categories, archive, consultation call-to-action.
 *
 * @package SaadEldinLibrary
 */

defined( 'ABSPATH' ) || exit;
?>
<aside class="sdl-sidebar" aria-label="الشريط الجانبي">
	<section class="sdl-widget sdl-widget--cta">
		<h2 class="sdl-widget__title">هل تحتاج إلى استشارة قانونية؟</h2>
		<p>تواصل معنا الآن عبر واتساب وسيرد عليك فريقنا في أقرب وقت.</p>
		<a class="sdl-btn" href="<?php echo esc_url( sdl_consultation_url() ); ?>" target="_blank" rel="noopener noreferrer">اطلب استشارتك الآن</a>
	</section>

	<section class="sdl-widget">
		<h2 class="sdl-widget__title">التصنيفات</h2>
		<ul class="sdl-list">
			<?php
			wp_list_categories(
				array(
					'title_li'     => '',
					'show_count'   => true,
					'hide_empty'   => true,
					'exclude'      => array( (int) get_option( 'default_category' ) ),
					'hierarchical' => false,
				)
			);
			?>
		</ul>
	</section>

	<section class="sdl-widget">
		<h2 class="sdl-widget__title">الأرشيف</h2>
		<ul class="sdl-list">
			<?php
			wp_get_archives(
				array(
					'type'            => 'monthly',
					'limit'           => 12,
					'show_post_count' => true,
				)
			);
			?>
		</ul>
	</section>
</aside>
