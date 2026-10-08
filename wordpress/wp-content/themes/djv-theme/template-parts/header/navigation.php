<?php
/**
 * Template Part: Header Navigation
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;

if ( has_nav_menu( 'primary' ) ) :
  wp_nav_menu( [
    'theme_location' => 'primary',
    'container'      => 'nav',
    'container_class'=> 'header-nav',
    'container_id'   => 'header-nav-primary',
    'menu_class'     => 'nav-list',
    'fallback_cb'    => false,
    'depth'          => 2,
  ] );
else :
?>
<nav class="header-nav" aria-label="<?php esc_attr_e( 'Main Navigation', 'djv-theme' ); ?>">
  <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/' ) ) ); ?>" class="nav-link <?php echo is_front_page() ? 'active' : ''; ?>" id="nav-home"><?php esc_html_e( 'Home', 'djv-theme' ); ?></a>
  <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/panchangam/' ) ) ); ?>" class="nav-link <?php echo is_page( 'panchangam' ) ? 'active' : ''; ?>" id="nav-panchangam"><?php esc_html_e( 'Panchangam', 'djv-theme' ); ?></a>
  <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/festivals/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_festival' ) || is_page( 'festivals' ) ? 'active' : ''; ?>" id="nav-festivals"><?php esc_html_e( 'Festivals', 'djv-theme' ); ?></a>
  <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/muhurtham/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_muhurtham' ) || is_page( 'muhurtham' ) ? 'active' : ''; ?>" id="nav-muhurtham"><?php esc_html_e( 'Muhurtham', 'djv-theme' ); ?></a>
  <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/pooja/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_pooja' ) || is_page( 'pooja' ) ? 'active' : ''; ?>" id="nav-pooja"><?php esc_html_e( 'Pooja', 'djv-theme' ); ?></a>
  <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/mantras/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_mantra' ) || is_page( 'mantras' ) ? 'active' : ''; ?>" id="nav-mantras"><?php esc_html_e( 'Mantras', 'djv-theme' ); ?></a>
  <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/temples/' ) ) ); ?>" class="nav-link <?php echo is_post_type_archive( 'djv_temple' ) || is_page( 'temples' ) ? 'active' : ''; ?>" id="nav-temples"><?php esc_html_e( 'Temples', 'djv-theme' ); ?></a>
  <a href="<?php echo esc_url( djv_append_lang_to_url( home_url( '/articles/' ) ) ); ?>" class="nav-link <?php echo is_home() || is_archive() || is_singular( 'post' ) ? 'active' : ''; ?>" id="nav-articles"><?php esc_html_e( 'Articles', 'djv-theme' ); ?></a>
</nav>
<?php endif; ?>
