<?php
/**
 * Archive template.
 *
 * @package F1_Speed_SEO
 */

get_header();
?>
<main id="main" class="site-main" role="main">
    <div class="wrap">
        <header class="archive-header">
            <h1 class="entry-title"><?php the_archive_title(); ?></h1>
            <?php the_archive_description( '<div class="entry-meta">', '</div>' ); ?>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
                                <?php
                                the_post_thumbnail(
                                    'f1-card',
                                    array(
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                        'sizes'    => '(max-width: 700px) 100vw, (max-width: 1100px) 50vw, 360px',
                                    )
                                );
                                ?>
                            </a>
                        <?php endif; ?>

                        <div class="post-content">
                            <p class="entry-meta">
                                <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                                <?php
                                $categories = get_the_category_list( ', ' );
                                if ( $categories ) {
                                    echo ' · ' . wp_kses_post( $categories );
                                }
                                ?>
                            </p>
                            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="excerpt"><?php the_excerpt(); ?></div>
                            <a class="read-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Leggi di più', 'f1-speed-seo' ); ?></a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination" aria-label="<?php esc_attr_e( 'Navigazione archivio', 'f1-speed-seo' ); ?>">
                <?php the_posts_pagination(); ?>
            </div>
        <?php else : ?>
            <article class="card"><div class="post-content"><p><?php esc_html_e( 'Nessun articolo trovato in questo archivio.', 'f1-speed-seo' ); ?></p></div></article>
        <?php endif; ?>
    </div>
</main>
<?php get_footer();