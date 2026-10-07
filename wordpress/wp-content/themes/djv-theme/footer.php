<?php
/**
 * Dharma Jyothi Vedika — Theme Footer
 *
 * @package DJV_Theme
 */
defined( 'ABSPATH' ) || exit;
?>
</main><!-- /#main-content -->

<!-- ════════════════════════════════════════════════════════════
     SITE FOOTER
════════════════════════════════════════════════════════════ -->
<footer class="site-footer" id="site-footer" role="contentinfo">
  <div class="footer-inner">
    <!-- Brand Column -->
    <?php get_template_part( 'template-parts/footer/brand' ); ?>

    <!-- Navigation Columns -->
    <?php get_template_part( 'template-parts/footer/links' ); ?>
  </div>

  <!-- Bottom Legal & Ephemeris Bar -->
  <?php get_template_part( 'template-parts/footer/bottom' ); ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>
