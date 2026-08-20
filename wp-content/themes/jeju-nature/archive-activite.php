
<?php
/**
 * Le fichier modèle de la liste des activités.
 *
 * Affiché à l'adresse /activites/. Imposé par le §5 de l'énoncé.
 *
 * @package Jeju_Nature
 */

get_header();

$jn_type_actuel   = isset( $_GET['jn_type'] ) ? sanitize_title( wp_unslash( $_GET['jn_type'] ) ) : '';
$jn_niveau_actuel = isset( $_GET['jn_niveau'] ) ? sanitize_title( wp_unslash( $_GET['jn_niveau'] ) ) : '';
$jn_tarif_actuel  = isset( $_GET['jn_tarif'] ) ? sanitize_key( wp_unslash( $_GET['jn_tarif'] ) ) : '';
$jn_apres_actuel  = isset( $_GET['jn_apres'] ) ? sanitize_text_field( wp_unslash( $_GET['jn_apres'] ) ) : '';
?>

<main id="primary" class="site-main">

    <header class="page-header">
        <h1 class="page-title">Nos activités</h1>
        <p class="jn-chapo">
            Sorties encadrées sur l'île de Jeju, toute l'année.
            Choisissez selon votre envie, votre niveau et votre budget.
        </p>
    </header>

    <form class="jn-filtres" method="get"
          action="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>">

        <div class="jn-filtre">
            <label for="jn_type">Type d'activité</label>
            <?php
            wp_dropdown_categories(
                array(
                    'taxonomy'        => 'type_activite',
                    'name'            => 'jn_type',
                    'id'              => 'jn_type',
                    'value_field'     => 'slug',
                    'show_option_all' => 'Tous les types',
                    'selected'        => $jn_type_actuel,
                    'hide_empty'      => false,
                    'orderby'         => 'name',
                )
            );
            ?>
        </div>

        <div class="jn-filtre">
            <label for="jn_niveau">Niveau</label>
            <?php
            wp_dropdown_categories(
                array(
                    'taxonomy'        => 'niveau',
                    'name'            => 'jn_niveau',
                    'id'              => 'jn_niveau',
                    'value_field'     => 'slug',
                    'show_option_all' => 'Tous les niveaux',
                    'selected'        => $jn_niveau_actuel,
                    'hide_empty'      => false,
                    'orderby'         => 'name',
                )
            );
            ?>
        </div>

        <div class="jn-filtre">
            <label for="jn_tarif">Tarif</label>
            <select name="jn_tarif" id="jn_tarif">
                <option value="">Tous les tarifs</option>
                <option value="gratuit" <?php selected( $jn_tarif_actuel, 'gratuit' ); ?>>Gratuit</option>
                <option value="payant" <?php selected( $jn_tarif_actuel, 'payant' ); ?>>Payant</option>
            </select>
        </div>

        <div class="jn-filtre">
            <label for="jn_apres">À partir du</label>
            <input type="date" name="jn_apres" id="jn_apres"
                   value="<?php echo esc_attr( $jn_apres_actuel ); ?>">
        </div>

        <div class="jn-filtre jn-filtre-boutons">
            <button type="submit" class="jn-bouton">Filtrer</button>
            <a class="jn-lien-discret"
               href="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>">Tout afficher</a>
        </div>

    </form>

    <?php if ( have_posts() ) : ?>

        <div class="jn-grille">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/carte', 'activite' );
            endwhile;
            ?>
        </div>

        <?php
        the_posts_pagination(
            array(
                'prev_text' => 'Précédent',
                'next_text' => 'Suivant',
            )
        );
        ?>

    <?php else : ?>

        <p class="jn-encadre">
            Aucune activité ne correspond à votre recherche.
            Essayez d'élargir vos critères.
        </p>

    <?php endif; ?>

</main><!-- #primary -->

<?php
get_footer();