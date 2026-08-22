<?php
/**
 * Page template.
 *
 * @package F1_Speed_SEO
 */

get_header();
?>
<main id="main" class="site-main" role="main">
    <div class="wrap single-layout">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : ?>
                <?php the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'post page-entry' ); ?>>
                    <div class="post-content">
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                            <?php
                            wp_link_pages(
                                array(
                                    'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Pagine del contenuto', 'f1-speed-seo' ) . '">',
                                    'after'  => '</nav>',
                                )
                            );
                            ?>
                        </div>

                        <?php if ( get_edit_post_link() ) : ?>
                            <footer class="entry-footer">
                                <?php edit_post_link( esc_html__( 'Modifica pagina', 'f1-speed-seo' ) ); ?>
                            </footer>
                        <?php endif; ?>
                    </div>
                </article>

                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php else : ?>
            <article class="card">
                <div class="post-content">
                    <h1 class="entry-title"><?php esc_html_e( 'Pagina non trovata', 'f1-speed-seo' ); ?></h1>
                </div>
            </article>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
