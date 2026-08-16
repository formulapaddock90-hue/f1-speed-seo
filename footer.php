<?php
/**
 * Footer template.
 *
 * @package F1_Speed_SEO
 */
?>
<footer class="site-footer" role="contentinfo">
    <div class="wrap footer-inner">
        <p>
            &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> ·
            <?php esc_html_e( 'News e analisi Formula 1 con focus su performance e strategia.', 'f1-speed-seo' ); ?>
        </p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>