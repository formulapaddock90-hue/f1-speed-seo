<?php
/**
 * Theme functions.
 *
 * @package F1_Speed_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'after_setup_theme', 'f1_speed_seo_setup' );
function f1_speed_seo_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

    register_nav_menus(
        array(
            'primary' => __( 'Menu Principale', 'f1-speed-seo' ),
        )
    );

    add_image_size( 'f1-card', 640, 360, true );
}

function f1_speed_seo_primary_menu_fallback() {
    echo '<ul class="primary-menu">';
    wp_list_pages(
        array(
            'title_li' => '',
            'depth'    => 2,
        )
    );
    echo '</ul>';
}

add_action( 'init', 'f1_speed_seo_cleanup_frontend' );
function f1_speed_seo_cleanup_frontend() {
    if ( is_admin() ) {
        return;
    }

    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}

add_action( 'wp_enqueue_scripts', 'f1_speed_seo_enqueue_assets' );
function f1_speed_seo_enqueue_assets() {
    wp_enqueue_style( 'f1-speed-seo-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
    wp_deregister_script( 'wp-embed' );
}

add_filter( 'wp_resource_hints', 'f1_speed_seo_resource_hints', 10, 2 );
function f1_speed_seo_resource_hints( $urls, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type ) {
        $urls[] = 'https://fonts.gstatic.com';
    }

    return array_unique( $urls );
}

add_action( 'wp_head', 'f1_speed_seo_meta_tags', 1 );
function f1_speed_seo_meta_tags() {
    if ( is_singular() ) {
        $description = has_excerpt() ? get_the_excerpt() : wp_strip_all_tags( (string) get_post_field( 'post_content', get_the_ID() ) );
        $description = wp_trim_words( $description, 28, '…' );
    } else {
        $description = get_bloginfo( 'description' );
    }

    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }

    echo '<meta name="robots" content="index,follow,max-image-preview:large">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";

    if ( is_singular() ) {
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( wp_strip_all_tags( get_the_title() ) ) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( get_permalink() ) . '">' . "\n";
        if ( has_post_thumbnail() ) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
            if ( ! empty( $image[0] ) ) {
                echo '<meta property="og:image" content="' . esc_url( $image[0] ) . '">' . "\n";
            }
        }
    } else {
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( home_url( '/' ) ) . '">' . "\n";
    }
}

add_action( 'wp_head', 'f1_speed_seo_json_ld', 20 );
function f1_speed_seo_json_ld() {
    if ( ! is_single() ) {
        return;
    }

    $schema = array(
        '@context'         => 'https://schema.org',
        '@type'            => 'NewsArticle',
        'headline'         => wp_strip_all_tags( get_the_title() ),
        'datePublished'    => get_the_date( DATE_W3C ),
        'dateModified'     => get_the_modified_date( DATE_W3C ),
        'author'           => array(
            '@type' => 'Person',
            'name'  => wp_strip_all_tags( get_the_author() ),
        ),
        'publisher'        => array(
            '@type' => 'Organization',
            'name'  => wp_strip_all_tags( get_bloginfo( 'name' ) ),
        ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id'   => get_permalink(),
        ),
    );

    if ( has_post_thumbnail() ) {
        $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
        if ( ! empty( $image[0] ) ) {
            $schema['image'] = $image[0];
        }
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}

add_action( 'wp_footer', 'f1_speed_seo_stop_loading_after_5s', 99 );
function f1_speed_seo_stop_loading_after_5s() {
    ?>
    <script>
    (function () {
        function stopLoadingState() {
            document.documentElement.classList.remove('is-loading', 'loading');
            document.body && document.body.classList.remove('is-loading', 'loading');

            var selectors = ['.loader', '.loading', '.site-loader', '#loader', '#loading', '#site-loader'];
            selectors.forEach(function (selector) {
                document.querySelectorAll(selector).forEach(function (el) {
                    el.style.display = 'none';
                    el.setAttribute('aria-hidden', 'true');
                });
            });
        }

        setTimeout(stopLoadingState, 5000);
        window.addEventListener('load', stopLoadingState, { once: true });
    })();
    </script>
    <?php
}

add_filter( 'script_loader_tag', 'f1_speed_seo_delay_adsense_script', 10, 3 );
function f1_speed_seo_delay_adsense_script( $tag, $handle, $src ) {
    if ( ! is_string( $src ) || false === strpos( $src, 'pagead2.googlesyndication.com' ) ) {
        return $tag;
    }

    return '<script type="text/plain" class="delayed-adsense-script" data-adsense-src="' . esc_url( $src ) . '"></script>';
}

add_action( 'wp_footer', 'f1_speed_seo_activate_adsense_on_scroll', 100 );
function f1_speed_seo_activate_adsense_on_scroll() {
    ?>
    <script>
    (function () {
        var activated = false;
        var adsenseSrc = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1892473664508324';

        function appendAdsenseScript(src) {
            if (!src || document.querySelector('script[src*="pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"]')) {
                return;
            }

            var script = document.createElement('script');
            script.async = true;
            script.src = src;
            script.crossOrigin = 'anonymous';
            document.head.appendChild(script);
        }

        function loadAdsenseScripts() {
            if (activated) {
                return;
            }
            activated = true;

            var placeholders = document.querySelectorAll('script.delayed-adsense-script[data-adsense-src]');
            if (!placeholders.length) {
                appendAdsenseScript(adsenseSrc);
                return;
            }

            placeholders.forEach(function (placeholder) {
                var src = placeholder.getAttribute('data-adsense-src') || adsenseSrc;
                appendAdsenseScript(src);
                placeholder.remove();
            });
        }

        ['scroll', 'wheel', 'touchmove', 'keydown'].forEach(function (eventName) {
            window.addEventListener(eventName, loadAdsenseScripts, { passive: true, once: true });
        });
    })();
    </script>
    <?php
}

add_action( 'wp_footer', 'f1_speed_seo_mobile_menu_script', 110 );
function f1_speed_seo_mobile_menu_script() {
    ?>
    <script>
    (function () {
        var menuToggle = document.querySelector('.menu-toggle');
        var nav = document.getElementById('primary-menu-wrapper');
        if (!menuToggle || !nav) {
            return;
        }

        function closeAllSubmenus() {
            nav.querySelectorAll('.menu-item-open').forEach(function (item) {
                item.classList.remove('menu-item-open');
            });
            nav.querySelectorAll('.submenu-toggle').forEach(function (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.textContent = '+';
            });
        }

        function closeMenu() {
            nav.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Apri il menu principale');
            closeAllSubmenus();
        }

        menuToggle.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('is-open');
            menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            menuToggle.setAttribute('aria-label', isOpen ? 'Chiudi il menu principale' : 'Apri il menu principale');
            if (!isOpen) {
                closeAllSubmenus();
            }
        });

        nav.querySelectorAll('.menu-item-has-children').forEach(function (item) {
            var link = item.querySelector(':scope > a');
            var submenu = item.querySelector(':scope > .sub-menu');
            if (!link || !submenu) {
                return;
            }

            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'submenu-toggle';
            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('aria-label', 'Apri sottomenu');
            button.textContent = '+';
            link.insertAdjacentElement('afterend', button);

            button.addEventListener('click', function () {
                var opened = item.classList.toggle('menu-item-open');
                button.setAttribute('aria-expanded', opened ? 'true' : 'false');
                button.setAttribute('aria-label', opened ? 'Chiudi sottomenu' : 'Apri sottomenu');
                button.textContent = opened ? '−' : '+';
            });

            link.addEventListener('click', function (event) {
                if (window.matchMedia('(max-width: 760px)').matches) {
                    event.preventDefault();
                    button.click();
                }
            });
        });

        nav.querySelectorAll('a').forEach(function (anchor) {
            anchor.addEventListener('click', function () {
                if (window.matchMedia('(max-width: 760px)').matches && !anchor.closest('.menu-item-has-children')) {
                    closeMenu();
                }
            });
        });

        document.addEventListener('click', function (event) {
            if (!window.matchMedia('(max-width: 760px)').matches) {
                return;
            }

            var insideMenu = nav.contains(event.target);
            var onToggle = menuToggle.contains(event.target);
            if (!insideMenu && !onToggle) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });

        window.addEventListener('resize', function () {
            if (window.matchMedia('(min-width: 761px)').matches) {
                nav.classList.remove('is-open');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.setAttribute('aria-label', 'Apri il menu principale');
                closeAllSubmenus();
            }
        });
    })();
    </script>
    <?php
}


add_action('rest_after_insert_post', 'fp_save_yoast_meta_from_rest', 10, 3);
add_action('rest_after_insert_page', 'fp_save_yoast_meta_from_rest', 10, 3);

function fp_save_yoast_meta_from_rest($post, $request, $creating) {
    if (!$post instanceof WP_Post) return;

    $meta = $request->get_param('meta');
    if (!is_array($meta)) {
        $meta = $request->get_param('meta_input');
    }
    if (!is_array($meta)) return;

    $allowed_keys = [
        '_yoast_wpseo_title',
        '_yoast_wpseo_metadesc',
        '_yoast_wpseo_focuskw',
    ];

    foreach ($allowed_keys as $key) {
        if (!array_key_exists($key, $meta)) continue;

        $value = sanitize_text_field((string)$meta[$key]);

        if (metadata_exists('post', $post->ID, $key)) {
            update_post_meta($post->ID, $key, $value);
        } else {
            add_post_meta($post->ID, $key, $value, true);
        }
    }
}
add_action( 'pre_get_posts', 'f1_speed_seo_home_post_types' );

function f1_speed_seo_home_post_types( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_home() ) {
        $query->set(
            'post_type',
            array(
                'post',
                'gran_premi',
                'pirelli',
                'evergreen',
            )
        );

        // ordine per data (più recenti prima)
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'DESC' );
    }
}
// Inizializza dataLayer subito; GTM, GA4 e AdSense si caricano al primo scroll
add_action( 'wp_head', function () { ?>
<script>window.dataLayer = window.dataLayer || [];</script>
<?php }, 1 );

// GTM caricato al primo scroll / interazione
add_action( 'wp_footer', 'f1_speed_seo_gtm_on_scroll', 101 );
function f1_speed_seo_gtm_on_scroll() { ?>
<script>
(function () {
    var done = false;
    function load() {
        if (done) return; done = true;
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
        var s = document.createElement('script');
        s.async = true;
        s.src = 'https://www.googletagmanager.com/gtm.js?id=GTM-5L66CTL';
        document.head.appendChild(s);
    }
    ['scroll', 'wheel', 'touchmove', 'keydown', 'mousemove'].forEach(function (e) {
        window.addEventListener(e, load, {passive: true, once: true});
    });
    setTimeout(load, 5000);
})();
</script>
<?php }

// GTM noscript fallback nel body
add_action( 'wp_body_open', function () { ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5L66CTL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php } );

// GA4 caricato al primo scroll / interazione
add_action( 'wp_footer', 'f1_speed_seo_ga4_on_scroll', 102 );
function f1_speed_seo_ga4_on_scroll() { ?>
<script>
(function () {
    var done = false;
    function load() {
        if (done) return; done = true;
        var s = document.createElement('script');
        s.async = true;
        s.src = 'https://www.googletagmanager.com/gtag/js?id=G-KBHJR9HMLS';
        document.head.appendChild(s);
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        window.gtag = window.gtag || gtag;
        gtag('js', new Date());
        gtag('config', 'G-KBHJR9HMLS');
    }
    ['scroll', 'wheel', 'touchmove', 'keydown', 'mousemove'].forEach(function (e) {
        window.addEventListener(e, load, {passive: true, once: true});
    });
    setTimeout(load, 5000);
})();
</script>
<?php }

// Lazy loading nativo su tutte le immagini del contenuto
add_filter( 'wp_get_attachment_image_attributes', 'f1_speed_seo_img_lazy_attr' );
function f1_speed_seo_img_lazy_attr( $attr ) {
    if ( ! is_admin() ) {
        $attr['loading'] = 'lazy';
    }
    return $attr;
}

add_filter( 'the_content', 'f1_speed_seo_content_img_lazy' );
function f1_speed_seo_content_img_lazy( $content ) {
    if ( is_admin() || is_feed() ) {
        return $content;
    }
    return preg_replace( '/<img(?![^>]*\bloading\b)([^>]*)>/i', '<img loading="lazy"$1>', $content );
}

// Subscribe with Google (SWG) caricato al primo scroll / interazione
add_action( 'wp_footer', 'f1_speed_seo_swg_on_scroll', 103 );
function f1_speed_seo_swg_on_scroll() { ?>
<script>
(function () {
    var done = false;
    function load() {
        if (done) return; done = true;
        self.SWG_BASIC = self.SWG_BASIC || [];
        var s = document.createElement('script');
        s.async = true;
        s.type = 'application/javascript';
        s.src = 'https://news.google.com/swg/js/v1/swg-basic.js';
        s.onload = function () {
            self.SWG_BASIC.push(function (basicSubscriptions) {
                basicSubscriptions.init({
                    type: 'NewsArticle',
                    isPartOfType: ['Product'],
                    isPartOfProductId: 'CAowxdrEDA:openaccess',
                    clientOptions: { theme: 'light', lang: 'it' },
                });
            });
        };
        document.head.appendChild(s);
    }
    ['scroll', 'wheel', 'touchmove', 'keydown', 'mousemove'].forEach(function (e) {
        window.addEventListener(e, load, {passive: true, once: true});
    });
    setTimeout(load, 5000);
})();
</script>
<?php }
// Lightbox per tutte le immagini (JS client-side per migliori prestazioni SEO e copertura globale)
function mytheme_glightbox_assets() {
    wp_enqueue_style(
        'glightbox',
        get_template_directory_uri() . '/assets/glightbox/css/glightbox.min.css',
        array(),
        '3.3.0'
    );

    wp_enqueue_script(
        'glightbox',
        get_template_directory_uri() . '/assets/glightbox/js/glightbox.min.js',
        array(),
        '3.3.0',
        true
    );

    wp_add_inline_script('glightbox', "
        document.addEventListener('DOMContentLoaded', function () {
            var images = document.querySelectorAll('img');
            images.forEach(function (img) {
                if (img.closest('#wpadminbar') || img.closest('.avatar') || img.closest('.site-logo') || img.closest('.logo') || img.classList.contains('emoji')) {
                    return;
                }
                var parent = img.parentNode;
                if (parent && parent.nodeName.toLowerCase() === 'a') {
                    var href = parent.getAttribute('href');
                    if (href && href.match(/\.(jpeg|jpg|gif|png|webp|svg)(\?.*)?$/i)) {
                        parent.classList.add('lightbox');
                    }
                } else {
                    var wrapper = document.createElement('a');
                    var src = img.getAttribute('src');
                    var fullSrc = img.getAttribute('data-full-url') || img.getAttribute('data-src') || img.getAttribute('data-lazy-src') || src;
                    if (fullSrc) {
                        wrapper.setAttribute('href', fullSrc);
                        wrapper.setAttribute('class', 'lightbox');
                        if (img.getAttribute('alt')) {
                            wrapper.setAttribute('data-title', img.getAttribute('alt'));
                        }
                        parent.insertBefore(wrapper, img);
                        wrapper.appendChild(img);
                    }
                }
            });
            if (typeof GLightbox !== 'undefined') {
                var lightbox = GLightbox({
                    selector: '.lightbox'
                });
            }
        });
    ");
}
add_action('wp_enqueue_scripts', 'mytheme_glightbox_assets');

/**
 * Ottiene la classifica piloti della F1 dal Jolpi/Ergast API con cache di 12 ore.
 */
function f1_speed_seo_get_driver_standings() {
    $standings = get_transient( 'f1_driver_standings' );
    if ( false === $standings ) {
        $response = wp_remote_get( 'https://api.jolpi.ca/ergast/f1/current/driverStandings.json', array( 'timeout' => 5 ) );
        if ( ! is_wp_error( $response ) ) {
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );
            $list = $data['MRData']['StandingsTable']['StandingsLists'][0]['DriverStandings'] ?? array();
            if ( ! empty( $list ) ) {
                $standings = array();
                foreach ( $list as $item ) {
                    $standings[] = array(
                        'position' => $item['position'],
                        'points'   => $item['points'],
                        'name'     => $item['Driver']['givenName'] . ' ' . $item['Driver']['familyName'],
                        'code'     => $item['Driver']['code'] ?? '',
                        'team'     => $item['Constructors'][0]['name'] ?? 'N/A',
                    );
                }
                set_transient( 'f1_driver_standings', $standings, 12 * HOUR_IN_SECONDS );
            }
        }
    }
    return $standings ?: array();
}

/**
 * Ottiene la classifica costruttori della F1 dal Jolpi/Ergast API con cache di 12 ore.
 */
function f1_speed_seo_get_constructor_standings() {
    $standings = get_transient( 'f1_constructor_standings' );
    if ( false === $standings ) {
        $response = wp_remote_get( 'https://api.jolpi.ca/ergast/f1/current/constructorStandings.json', array( 'timeout' => 5 ) );
        if ( ! is_wp_error( $response ) ) {
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );
            $list = $data['MRData']['StandingsTable']['StandingsLists'][0]['ConstructorStandings'] ?? array();
            if ( ! empty( $list ) ) {
                $standings = array();
                foreach ( $list as $item ) {
                    $standings[] = array(
                        'position' => $item['position'],
                        'points'   => $item['points'],
                        'team'     => $item['Constructor']['name'],
                    );
                }
                set_transient( 'f1_constructor_standings', $standings, 12 * HOUR_IN_SECONDS );
            }
        }
    }
    return $standings ?: array();
}

/**
 * Ottiene le informazioni del prossimo GP di F1 e gli orari di tutte le sessioni dall'API Jolpica F1.
 */
function f1_speed_seo_get_next_grand_prix() {
    delete_transient( 'f1_next_gp_schedule' );
    delete_transient( 'f1_next_gp_schedule_v5' );
    $gp_data = get_transient( 'f1_next_gp_schedule_v6' );

    // Verifichiamo se i dati in cache sono ancora validi (almeno una sessione non passata)
    if ( false !== $gp_data && ! empty( $gp_data['sessions'] ) ) {
        $last_session = end( $gp_data['sessions'] );
        if ( isset( $last_session['timestamp'] ) && time() > ( $last_session['timestamp'] + 7200 ) ) {
            $gp_data = false; // Invalida la cache al termine del GP
        }
    }

    if ( false === $gp_data ) {
        $response = wp_remote_get( 'https://api.jolpi.ca/ergast/f1/current/next.json', array( 'timeout' => 5 ) );
        if ( ! is_wp_error( $response ) ) {
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );
            $race = $data['MRData']['RaceTable']['Races'][0] ?? null;

            if ( $race ) {
                $sessions_raw = array();

                if ( ! empty( $race['FirstPractice'] ) ) {
                    $sessions_raw[] = array( 'id' => 'fp1', 'name' => 'Prove Libere 1', 'data' => $race['FirstPractice'] );
                }
                if ( ! empty( $race['SecondPractice'] ) ) {
                    $sessions_raw[] = array( 'id' => 'fp2', 'name' => 'Prove Libere 2', 'data' => $race['SecondPractice'] );
                }
                if ( ! empty( $race['SprintQualifying'] ) ) {
                    $sessions_raw[] = array( 'id' => 'sprint_qualifying', 'name' => 'Sprint Qualifiche', 'data' => $race['SprintQualifying'] );
                }
                if ( ! empty( $race['Sprint'] ) ) {
                    $sessions_raw[] = array( 'id' => 'sprint', 'name' => 'Sprint Race', 'data' => $race['Sprint'] );
                }
                if ( ! empty( $race['ThirdPractice'] ) ) {
                    $sessions_raw[] = array( 'id' => 'fp3', 'name' => 'Prove Libere 3', 'data' => $race['ThirdPractice'] );
                }
                if ( ! empty( $race['Qualifying'] ) ) {
                    $sessions_raw[] = array( 'id' => 'qualifying', 'name' => 'Qualifiche', 'data' => $race['Qualifying'] );
                }
                if ( ! empty( $race['date'] ) ) {
                    $sessions_raw[] = array(
                        'id'   => 'race',
                        'name' => 'Gara GP',
                        'data' => array(
                            'date' => $race['date'],
                            'time' => $race['time'] ?? '14:00:00Z',
                        ),
                    );
                }

                $now = time();
                $sessions = array();
                $target_tz = new DateTimeZone( 'Europe/Rome' );

                $day_names = array(
                    'Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mer',
                    'Thu' => 'Gio', 'Fri' => 'Ven', 'Sat' => 'Sab', 'Sun' => 'Dom'
                );

                foreach ( $sessions_raw as $s ) {
                    $time_str = isset( $s['data']['time'] ) ? $s['data']['time'] : '12:00:00Z';
                    $datetime_str = $s['data']['date'] . ' ' . $time_str;
                    
                    $timestamp = strtotime( $datetime_str );
                    $dt = new DateTime();
                    $dt->setTimestamp( $timestamp );
                    $dt->setTimezone( $target_tz );

                    $day_en = $dt->format( 'D' );
                    $day_it = $day_names[$day_en] ?? $day_en;
                    $date_formatted = $day_it . ' ' . $dt->format( 'd/m' );
                    $time_formatted = $dt->format( 'H:i' );

                    $is_past = $now > ( $timestamp + 7200 ); // 2h tolleranza

                    $sessions[] = array(
                        'id'             => $s['id'],
                        'name'           => $s['name'],
                        'timestamp'      => $timestamp,
                        'date_formatted' => $date_formatted,
                        'time_formatted' => $time_formatted,
                        'is_past'        => $is_past,
                        'is_next'        => false,
                    );
                }

                // Ordiniamo le sessioni per orario di inizio
                usort($sessions, function($a, $b) {
                    return $a['timestamp'] <=> $b['timestamp'];
                });

                // Troviamo la prossima sessione imminente
                $next_session = null;
                foreach ( $sessions as &$sess ) {
                    if ( ! $sess['is_past'] && null === $next_session ) {
                        $sess['is_next'] = true;
                        $next_session = array(
                            'id'             => $sess['id'],
                            'name'           => $sess['name'],
                            'timestamp'      => $sess['timestamp'],
                            'date_formatted' => $sess['date_formatted'],
                            'time_formatted' => $sess['time_formatted'],
                        );
                    }
                }
                unset($sess);

                $gp_data = array(
                    'race_name'    => $race['raceName'],
                    'circuit'      => $race['Circuit']['circuitName'] ?? '',
                    'country'      => $race['Circuit']['Location']['country'] ?? '',
                    'locality'     => $race['Circuit']['Location']['locality'] ?? '',
                    'round'        => $race['round'],
                    'sessions'     => $sessions,
                    'next_session' => $next_session,
                );

                set_transient( 'f1_next_gp_schedule_v6', $gp_data, 2 * HOUR_IN_SECONDS );
            }
        }
    }

    return $gp_data ?: array();
}

add_action( 'wp_footer', 'f1_speed_seo_countdown_script', 105 );
function f1_speed_seo_countdown_script() {
    ?>
    <script>
    (function () {
        function updateCountdown() {
            var el = document.getElementById('f1-countdown-timer');
            if (!el) return;

            var targetTime = parseInt(el.getAttribute('data-timestamp'), 10);
            if (!targetTime || isNaN(targetTime)) return;

            var now = Math.floor(Date.now() / 1000);
            var diff = targetTime - now;

            var daysEl = document.getElementById('f1-cd-days');
            var hoursEl = document.getElementById('f1-cd-hours');
            var minsEl = document.getElementById('f1-cd-mins');
            var secsEl = document.getElementById('f1-cd-secs');

            if (diff <= 0) {
                if (daysEl) daysEl.textContent = '00';
                if (hoursEl) hoursEl.textContent = '00';
                if (minsEl) minsEl.textContent = '00';
                if (secsEl) secsEl.textContent = '00';

                var titleEl = document.getElementById('f1-cd-status-title');
                if (titleEl) {
                    titleEl.textContent = '⚡ SESSIONE IN CORSO / TERMINATA';
                }
                return;
            }

            var days = Math.floor(diff / (3600 * 24));
            var hours = Math.floor((diff % (3600 * 24)) / 3600);
            var minutes = Math.floor((diff % 3600) / 60);
            var seconds = diff % 60;

            if (daysEl) daysEl.textContent = days < 10 ? '0' + days : days;
            if (hoursEl) hoursEl.textContent = hours < 10 ? '0' + hours : hours;
            if (minsEl) minsEl.textContent = minutes < 10 ? '0' + minutes : minutes;
            if (secsEl) secsEl.textContent = seconds < 10 ? '0' + seconds : seconds;
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    })();
    </script>
    <?php
}