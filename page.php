<?php
/**
 * Il template per la visualizzazione di tutti i singoli articoli (post)
 * Versione ottimizzata per F1 Paddock: focus su SEO, leggibilità e spaziatura.
 *
 * @package F1_Paddock
 */

get_header(); ?>

<div id="primary" class="content-area container py-5" >
    <div class="row justify-content-center">
        <!-- Layout a 8 colonne per mantenere una lunghezza di riga ottimale per la lettura -->
        <main id="main" class="site-main col-lg-8" role="main">

            <?php
            while ( have_posts() ) :
                the_post();
                
                // Calcolo tempo di lettura
                $content = get_the_content();
                $word_count = str_word_count( strip_tags( $content ) );
                $reading_time = ceil( $word_count / 200 );
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'mb-5 pb-5' ); ?> itemscope itemtype="https://schema.org/BlogPosting">
                    
                    <header class="entry-header mb-5 pb-4">
                        <div class="entry-meta mb-4 d-flex align-items-center gap-3">
                            <?php
                            $categories_list = get_the_category_list( ' ' );
                            if ( $categories_list ) {
                                // Badge stile F1 con padding migliorato
                                echo str_replace( '<a', '<a class="badge bg-danger text-white text-decoration-none text-uppercase px-3 py-2"', $categories_list );
                            }
                            ?>
                            <span class="text-muted small ms-auto d-none d-sm-inline">
                                <i class="bi bi-stopwatch"></i> <?php echo esc_html( $reading_time ); ?> min lettura
                            </span>
                        </div>

                        <?php the_title( '<h1 class="entry-title display-4 fw-bold text-dark mb-4" itemprop="headline">', '</h1>' ); ?>

                        <div class="entry-meta-bottom d-flex align-items-center text-muted small border-top border-bottom py-4">
                            <div class="author-meta me-5">
                                <span class="text-uppercase fw-light me-2"><?php echo esc_html__( 'Scritto da:', 'f1-paddock' ); ?></span>
                                <span class="fw-bold text-dark" itemprop="author"><?php the_author(); ?></span>
                            </div>
                            <div class="date-meta">
                                <span class="text-uppercase fw-light me-2"><?php echo esc_html__( 'Data:', 'f1-paddock' ); ?></span>
                                <time class="entry-date fw-bold text-dark" datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                                    <?php echo get_the_date(); ?>
                                </time>
                            </div>
                        </div>
                    </header>

                    <!-- Sezione Contenuto con margini ottimizzati -->
                    <div class="entry-content mb-5 pb-4" itemprop="articleBody">
                        <?php
                        the_content();

                        wp_link_pages(
                            array(
                                'before'      => '<div class="page-links mt-5 pt-4 border-top"><span class="page-links-title me-3">' . esc_html__( 'Pagine:', 'f1-paddock' ) . '</span>',
                                'after'       => '</div>',
                                'link_before' => '<span class="btn btn-sm btn-outline-secondary ms-2">',
                                'link_after'  => '</span>',
                            )
                        );
                        ?>
                    </div>

                    <footer class="entry-footer pt-5 mt-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-4">
                        <div class="tags-links">
                            <?php 
                            $tags = get_the_tags();
                            if ( $tags ) :
                                foreach ( $tags as $tag ) : ?>
                                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="btn btn-light btn-sm border text-muted mb-2 px-3 py-2">
                                        #<?php echo esc_html( $tag->name ); ?>
                                    </a>
                                <?php endforeach;
                            endif;
                            ?>
                        </div>
                        
                        <?php if ( get_edit_post_link() ) : ?>
                            <div class="edit-action">
                                <?php edit_post_link( esc_html__( 'Modifica Post', 'f1-paddock' ), '<span class="text-muted small"><i class="bi bi-pencil-square me-2"></i>', '</span>' ); ?>
                            </div>
                        <?php endif; ?>
                    </footer>

                </article>

                <!-- ============================================
                     SEZIONE POST CORRELATI CON CAROSELLO
                     ============================================ -->
                <?php
                // Recupera le categorie del post corrente
                $current_categories = wp_get_post_categories( get_the_ID() );
                
                if ( ! empty( $current_categories ) ) {
                    // Query per post di tipo 'page' nella stessa categoria
                    $related_args = array(
                        'post_type'      => 'page',
                        'posts_per_page' => 6,
                        'post__not_in'   => array( get_the_ID() ),
                        'category__in'   => $current_categories,
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    );
                    
                    $related_query = new WP_Query( $related_args );
                    
                    if ( $related_query->have_posts() ) :
                        ?>
                        <section class="related-posts-section mt-5 pt-5 border-top">
                            <h2 class="section-title h3 fw-bold mb-4">
                                <?php esc_html_e( 'Pagine Correlate', 'f1-paddock' ); ?>
                            </h2>
                            
                            <!-- Carosello Bootstrap -->
                            <div id="relatedPostsCarousel" class="carousel slide" data-bs-ride="carousel">
                                <!-- Contenuto Carosello -->
                                <div class="carousel-inner">
                                    <?php
                                    $carousel_index = 0;
                                    $items_per_slide = 3;
                                    $is_first_slide = true;
                                    
                                    while ( $related_query->have_posts() ) :
                                        $related_query->the_post();
                                        
                                        // Inizio nuovo slide ogni 3 items
                                        if ( $carousel_index % $items_per_slide === 0 ) {
                                            if ( ! $is_first_slide ) {
                                                echo '</div></div>';
                                            }
                                            echo '<div class="carousel-item' . ( $is_first_slide ? ' active' : '' ) . '"><div class="row g-3">';
                                            $is_first_slide = false;
                                        }
                                        ?>
                                        
                                        <div class="col-md-4 col-sm-6">
                                            <a href="<?php the_permalink(); ?>" class="card h-100 text-decoration-none related-post-card border shadow-sm">
                                                <?php
                                                // Immagine in evidenza
                                                if ( has_post_thumbnail() ) :
                                                    ?>
                                                    <div class="card-img-top overflow-hidden" style="height: 200px;">
                                                        <?php
                                                        the_post_thumbnail( 'medium', array(
                                                            'class' => 'w-100 h-100 object-fit-cover',
                                                            'alt'   => get_the_title(),
                                                        ) );
                                                        ?>
                                                    </div>
                                                <?php else : ?>
                                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                        <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="card-body">
                                                    <h5 class="card-title fw-bold text-dark mb-2">
                                                        <?php the_title(); ?>
                                                    </h5>
                                                    
                                                    <?php
                                                    // Excerpt
                                                    $excerpt = get_the_excerpt();
                                                    if ( $excerpt ) :
                                                        ?>
                                                        <p class="card-text text-muted small mb-3">
                                                            <?php echo wp_trim_words( $excerpt, 12, '...' ); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <small class="text-uppercase text-muted d-block">
                                                        <i class="bi bi-calendar-event"></i> 
                                                        <?php echo get_the_date( 'd/m/Y' ); ?>
                                                    </small>
                                                </div>
                                            </a>
                                        </div>
                                        
                                        <?php
                                        $carousel_index++;
                                    endwhile;
                                    
                                    // Chiudi ultimo slide
                                    echo '</div></div>';
                                    
                                    // Reset post data
                                    wp_reset_postdata();
                                    ?>
                                </div>
                                
                                <!-- Controlli Navigazione -->
                                <button class="carousel-control-prev" type="button" data-bs-target="#relatedPostsCarousel" data-bs-slide="prev" aria-label="<?php esc_attr_e( 'Previous', 'f1-paddock' ); ?>">
                                    <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                                    <span class="visually-hidden"><?php esc_html_e( 'Previous', 'f1-paddock' ); ?></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#relatedPostsCarousel" data-bs-slide="next" aria-label="<?php esc_attr_e( 'Next', 'f1-paddock' ); ?>">
                                    <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                                    <span class="visually-hidden"><?php esc_html_e( 'Next', 'f1-paddock' ); ?></span>
                                </button>
                            </div>
                        </section>
                        <?php
                    endif;
                }
                ?>

                <!-- Commenti con padding interno per separare dal footer -->
                <?php
                if ( comments_open() || get_comments_number() ) :
                    echo '<div class="comments-area mt-5 pt-5 p-4 p-md-5 bg-white border rounded shadow-sm">';
                    comments_template();
                    echo '</div>';
                endif;

            endwhile; 
            ?>

        </main>
    </div>
</div>

<style>
.carousel-item .row {
    display: flex;
    flex-wrap: nowrap;
    gap: 1.5rem;
}

.carousel-item .col-md-4 {
    flex: 0 0 calc(33.333% - 1rem);
    max-width: calc(33.333% - 1rem);
}

@media (max-width: 992px) {
    .carousel-item .col-md-4 {
        flex: 0 0 calc(50% - 0.75rem);
        max-width: calc(50% - 0.75rem);
    }
}

@media (max-width: 768px) {
    .carousel-item .row {
        flex-wrap: wrap;
    }

    .carousel-item .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}</style>

<?php
get_footer();
