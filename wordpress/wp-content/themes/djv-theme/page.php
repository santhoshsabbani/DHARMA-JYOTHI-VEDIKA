<?php
/**
 * Generic Page Template
 *
 * @package DJV_Theme
 */

get_header();
?>

<div class="page-wrapper" style="padding: 3rem 0 5rem 0;">
  <div class="container" style="max-width: 960px;">

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header" style="margin-bottom: 2rem;">
          <h1 class="entry-title" style="font-family: var(--font-heading); font-size: 2.25rem; color: var(--clr-primary); margin: 0 0 0.5rem 0;">
            <?php the_title(); ?>
          </h1>
        </header>

        <div class="entry-content" style="font-size: 1rem; line-height: 1.8; color: var(--clr-text);">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; endif; ?>

  </div>
</div>

<?php
get_footer();
