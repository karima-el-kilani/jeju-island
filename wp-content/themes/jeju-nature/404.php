<?php
/**
 * Le fichier modèle affiché quand l'adresse demandée n'existe pas (erreur 404).
 *
 * @package Jeju_Nature
 */

get_header();
?>

    <main id="primary" class="site-main">

        <section class="error-404 not-found">

            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e( 'Cette page est introuvable', 'jeju-nature' ); ?></h1>
            </header><!-- .page-header -->

            <div class="page-content">

                <p><?php esc_html_e( 'La page que vous cherchez a peut-être été déplacée ou supprimée. Vous pouvez lancer une recherche ou revenir à la page principale.', 'jeju-nature' ); ?></p>

                <?php get_search_form(); ?>

                <p>
                    <a class="jn-bouton" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php esc_html_e( 'Revenir à la page principale', 'jeju-nature' ); ?>
                    </a>
                </p>

            </div><!-- .page-content -->

        </section><!-- .error-404 -->

    </main><!-- #primary -->

<?php
get_footer();