<?php
/**
 * Header template.
 *
 * @package F1_Speed_SEO
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="race-ticker" aria-hidden="true">
    <div class="wrap race-ticker-inner">
        <span class="ticker-pill">LIVE RACE HUB F1</span>
        <span class="ticker-text">Analisi gara, telemetria e strategie pit stop in tempo reale</span>
    </div>
</div>
<header class="site-header" role="banner">
    <div class="wrap header-inner">
        <div class="brand-block">
            <span class="brand-kicker">F1 Editorial Desk</span>
            <?php if ( is_front_page() && is_home() ) : ?>
                <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
            <?php else : ?>
                <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></p>
            <?php endif; ?>

            <?php
            $description = get_bloginfo( 'description', 'display' );
            if ( $description || is_customize_preview() ) :
                ?>
                <p class="site-description"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>
        </div>

        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu-wrapper" aria-label="<?php esc_attr_e( 'Apri il menu principale', 'f1-speed-seo' ); ?>">
            <span class="menu-toggle-icon" aria-hidden="true"></span>
            <span class="menu-toggle-label"><?php esc_html_e( 'Menu', 'f1-speed-seo' ); ?></span>
        </button>

        <nav id="primary-menu-wrapper" class="main-nav" aria-label="<?php esc_attr_e( 'Menu principale', 'f1-speed-seo' ); ?>">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'fallback_cb'    => 'f1_speed_seo_primary_menu_fallback',
                    'depth'          => 2,
                    'menu_class'     => 'primary-menu',
                )
            );
            ?>
        </nav>
    </div>
</header>
