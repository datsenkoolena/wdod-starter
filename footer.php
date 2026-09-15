<?php
/**
 * The footer: renders the site footer (or an Elementor Theme Builder footer)
 * and closes the document.
 *
 * @package WDOD_Starter
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'template-parts/footer/site-footer' );
}
?>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
