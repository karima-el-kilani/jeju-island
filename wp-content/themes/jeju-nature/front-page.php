
<?php
/**
 * La page d'accueil du site.
 *
 * Imposé par le §5 de l'énoncé. Grande image panoramique en haut,
 * puis les trois prochaines sorties, puis la présentation de l'association.
 *
 * @package Jeju_Nature
 */

get_header();

$jn_image_haut = get_header_image();
?>

<section class="jn-hero"
    <?php if ( $jn_image_haut ) : ?>
        style="background-image: url('<?php echo esc_url( $jn_image_haut ); ?>');"
    <?php endif; ?>
>
    <div class="jn-hero-contenu">
        <h1 class="jn-hero-titre">Découvrir l'île de Jeju autrement</h1>
        <p class="jn-hero-texte">
            Sorties nature encadrées par des bénévoles de l'association,
            du littoral aux cratères, toute l'année.
        </p>
        <a class="jn-bouton jn-bouton-clair"
           href="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>">
            Voir nos sorties
        </a>
    </div>
</section>

<main id="primary" class="site-main">

    <section class="jn-accueil-bloc">

        <h2 class="jn-accueil-titre">Nos prochaines sorties</h2>

        <?php
        $jn_prochaines = new WP_Query(
            array(
                'post_type'      => 'activite',
                'posts_per_page' => 3,
                'meta_key'       => '_jn_date',
                'orderby'        => 'meta_value',
                'order'          => 'ASC',
            )
        );

        if ( $jn_prochaines->have_posts() ) :
            ?>
            <div class="jn-grille">
                <?php
                while ( $jn_prochaines->have_posts() ) :
                    $jn_prochaines->the_post();
                    get_template_part( 'template-parts/carte', 'activite' );
                endwhile;
                ?>
            </div>
            <?php
            wp_reset_postdata();
        else :
            ?>
            <p class="jn-encadre">Aucune sortie programmée pour le moment.</p>
        <?php
        endif;
        ?>

        <p class="jn-accueil-lien">
            <a class="jn-bouton"
               href="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>">
                Voir toutes nos activités
            </a>
        </p>

    </section>

    <section class="jn-accueil-bloc jn-accueil-presentation">

        <h2 class="jn-accueil-titre">L'association</h2>

        <p>
            Jeju Nature est une association de bénévoles qui fait découvrir
            l'île de Jeju à pied : ses sentiers côtiers, ses cratères, ses
            cascades, ses villages de pierre et ses plongeuses haenyeo.
        </p>
        <p>
            Chaque sortie est encadrée par un accompagnateur de l'association
            et limitée en nombre de participants, pour préserver les milieux
            traversés autant que le plaisir de la marche.
        </p>

    </section>

</main><!-- #primary -->

<?php
get_footer();