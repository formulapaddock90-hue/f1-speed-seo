<?php
/**
 * Single post template.
 *
 * @package F1_Speed_SEO
 */

get_header();
?>
<main id="main" class="site-main" role="main">
    <div class="wrap single-layout">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>

                <div class="post-content">
                    <header class="entry-header">
                        <p class="entry-meta">
                            <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                            · <?php echo esc_html( get_the_author() ); ?>
                        </p>
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </article>

            <?php
            the_post_navigation(
                array(
                    'prev_text' => '← %title',
                    'next_text' => '%title →',
                )
            );
            ?>
        <?php endwhile; endif; ?>
    </div>
</main>
<?php get_footer();