<?php
/**
 * Main template file.
 *
 * @package F1_Speed_SEO
 */

get_header();
?>

<main id="main" class="site-main" role="main">
    <div class="wrap">

        <?php
        $paged = max( 1, get_query_var( 'paged' ) );

        $featured_query = new WP_Query(
            array(
                'post_type' => array(
                    'post',
                    'page',
                    'gran_premi',
                    'pirelli',
                    'evergeen',
                ),
                'posts_per_page'      => 1,
                'ignore_sticky_posts' => true,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'meta_query'          => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'futuro',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'     => 'futuro',
                        'value'   => '"ok"',
                        'compare' => 'NOT LIKE',
                    ),
                ),
            )
        );

        $featured_id = 0;

        if ( $featured_query->have_posts() ) :
            while ( $featured_query->have_posts() ) :
                $featured_query->the_post();

                $featured_id = get_the_ID();
                $post_url    = get_permalink();
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'hero-post' ); ?>>
                    <div class="hero-grid">

                        <div class="hero-copy">
                            <p class="hero-kicker">
                                <?php esc_html_e( 'In Pole Position', 'f1-speed-seo' ); ?>
                            </p>

                            <h1 class="hero-title">
                                <a href="<?php echo esc_url( $post_url ); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h1>

                            <p class="entry-meta">
                                <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
                                    <?php echo esc_html( get_the_date() ); ?>
                                </time>

                                <?php
                                $categories = get_the_category_list( ', ' );

                                if ( $categories ) {
                                    echo ' . ' . wp_kses_post( $categories );
                                }
                                ?>
                            </p>

                            <div class="excerpt">
                                <?php
                                echo wp_kses_post(
                                    wp_trim_words( get_the_excerpt(), 28 )
                                );
                                ?>
                            </div>

                            <a class="read-more" href="<?php echo esc_url( $post_url ); ?>">
                                <?php esc_html_e( 'Leggi l\'analisi completa', 'f1-speed-seo' ); ?>
                            </a>
                        </div>

                        <div class="hero-media">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php echo esc_url( $post_url ); ?>">
                                    <?php
                                    the_post_thumbnail(
                                        'large',
                                        array(
                                            'loading'       => false,
                                            'fetchpriority' => 'high',
                                            'decoding'      => 'async',
                                            'sizes'         => '(max-width: 1080px) 100vw, 42vw',
                                        )
                                    );
                                    ?>
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                </article>

                <?php
            endwhile;
        endif;

        wp_reset_postdata();

        $main_query = new WP_Query(
            array(
                'post_type' => array(
                    'post',
                    'page',
                    'gran_premi',
                    'pirelli',
                    'evergeen',
                ),
                'paged'               => $paged,
                'posts_per_page'      => get_option( 'posts_per_page' ),
                'post__not_in'        => array( $featured_id ),
                'ignore_sticky_posts' => true,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'meta_query'          => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'futuro',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'     => 'futuro',
                        'value'   => '"ok"',
                        'compare' => 'NOT LIKE',
                    ),
                ),
            )
        );
        ?>

        <?php if ( $main_query->have_posts() ) : ?>

            <div class="home-cols">
                <div class="home-cards">
                    <div class="grid">

                        <?php while ( $main_query->have_posts() ) : $main_query->the_post(); ?>

                            <?php $post_url = get_permalink(); ?>

                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>

                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php echo esc_url( $post_url ); ?>">
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
                                        <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
                                            <?php echo esc_html( get_the_date() ); ?>
                                        </time>

                                        <?php
                                        $categories = get_the_category_list( ', ' );

                                        if ( $categories ) {
                                            echo ' . ' . wp_kses_post( $categories );
                                        }
                                        ?>
                                    </p>

                                    <h2 class="entry-title">
                                        <a href="<?php echo esc_url( $post_url ); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>

                                    <div class="excerpt">
                                        <?php
                                        echo wp_kses_post(
                                            wp_trim_words( get_the_excerpt(), 24 )
                                        );
                                        ?>
                                    </div>

                                    <a class="read-more" href="<?php echo esc_url( $post_url ); ?>">
                                        <?php esc_html_e( 'Leggi di piu', 'f1-speed-seo' ); ?>
                                    </a>
                                </div>

                            </article>

                        <?php endwhile; ?>

                    </div>
                </div>

                <?php get_sidebar(); ?>
            </div>

            <div class="pagination" aria-label="<?php esc_attr_e( 'Navigazione articoli', 'f1-speed-seo' ); ?>">
                <?php
                echo paginate_links(
                    array(
                        'total'   => $main_query->max_num_pages,
                        'current' => $paged,
                        'mid_size' => 1,
                        'prev_text' => __( 'Precedente', 'f1-speed-seo' ),
                        'next_text' => __( 'Successivo', 'f1-speed-seo' ),
                    )
                );
                ?>
            </div>

        <?php else : ?>

            <article class="card">
                <div class="post-content">
                    <h1 class="entry-title">
                        <?php esc_html_e( 'Nessun contenuto trovato', 'f1-speed-seo' ); ?>
                    </h1>

                    <p>
                        <?php esc_html_e( 'Non ci sono articoli da mostrare in questo momento.', 'f1-speed-seo' ); ?>
                    </p>
                </div>
            </article>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </div>
</main>

<?php get_footer(); ?>
