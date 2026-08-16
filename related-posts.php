<?php
/**
 * Template part per la visualizzazione degli articoli correlati per categoria.
 * Ottimizzato per il tema F1 Paddock con supporto immagine in evidenza.
 *
 * @package f1-paddock
 */

$categories = get_the_category();

if ( $categories ) :
    $category_ids = array();
    foreach ( $categories as $individual_category ) {
        $category_ids[] = $individual_category->term_id;
    }

    $args = array(
        'category__in'     => $category_ids,
        'post__not_in'     => array( get_the_ID() ),
        'posts_per_page'   => 3,
        'ignore_sticky_posts' => 1
    );

    $related_query = new WP_Query( $args );

    if ( $related_query->have_posts() ) : ?>
        <section class="related-posts">
            <div class="container">
                <h3 class="related-title mb-4 text-uppercase fw-bold" style="letter-spacing: 2px; border-left: 4px solid var(--f1-red); padding-left: 15px;">
                    <?php esc_html_e( 'Articoli Simili', 'f1-paddock' ); ?>
                </h3>
                
                <div class="related-grid">
                    <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
                        <article class="related-card">
                            <a href="<?php the_permalink(); ?>" class="d-block text-decoration-none">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'medium_large', array( 'class' => 'related-thumb' ) ); ?>
                                <?php else : ?>
                                    <!-- Fallback se non c'è immagine: un box grigio stile carbon -->
                                    <div class="related-thumb carbon-bg d-flex align-items-center justify-content-center">
                                        <span class="text-white opacity-25 fw-black italic">F1 PADDOCK</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="related-info">
                                    <h4 class="text-dark fw-bold">
                                        <?php the_title(); ?>
                                    </h4>
                                    <time class="text-muted small">
                                        <?php echo get_the_date(); ?>
                                    </time>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php
    endif;
    wp_reset_postdata();
endif;