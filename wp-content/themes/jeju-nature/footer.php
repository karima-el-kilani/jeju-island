<?php
/**
 * Le pied de page.
 *
 * Ferme la structure HTML ouverte dans header.php.
 *
 * @package Jeju_Nature
 */

?>

<footer id="colophon" class="site-footer">
    <div class="site-info">

        <p class="jn-footer-titre"><?php bloginfo( 'name' ); ?></p>

        <p>
            Association Jeju Nature — Seogwipo, île de Jeju<br>
            Sorties nature encadrées par des bénévoles.
        </p>

        <p class="jn-footer-liens">
            <?php
            // Le lien n'est affiché que si une page de confidentialité est publiée.
            if ( get_privacy_policy_url() ) :
                ?>
                <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">
                    Politique de confidentialité
                </a>
            <?php
            endif;
            ?>
        </p>

        <p class="jn-footer-copyright">
            <?php
            // date( 'Y' ) : l'année en cours, recalculée à chaque affichage.
            echo esc_html( '© ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) );
            ?>
        </p>

    </div><!-- .site-info -->
</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>