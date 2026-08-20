
<?php
/**
 * Le fichier modèle d'une activité.
 *
 * Imposé par le §5 de l'énoncé. Affiche l'image, les informations
 * pratiques issues des champs personnalisés, le contenu rédigé,
 * les classements, et le formulaire de réservation.
 *
 * @package Jeju_Nature
 */

get_header();

while ( have_posts() ) :
    the_post();

    $jn_id     = get_the_ID();
    $jn_date   = get_post_meta( $jn_id, '_jn_date', true );
    $jn_heure  = get_post_meta( $jn_id, '_jn_heure', true );
    $jn_duree  = get_post_meta( $jn_id, '_jn_duree', true );
    $jn_lieu   = get_post_meta( $jn_id, '_jn_lieu', true );
    $jn_places = get_post_meta( $jn_id, '_jn_places_max', true );
    $jn_tarif  = (int) get_post_meta( $jn_id, '_jn_tarif', true );
    $jn_statut = get_post_meta( $jn_id, '_jn_statut', true );
    ?>

    <main id="primary" class="site-main">

        <article <?php post_class( 'jn-activite' ); ?>>

            <header class="jn-activite-entete">

                <p class="jn-fil">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'activite' ) ); ?>">
                        Nos activités
                    </a>
                </p>

                <h1 class="entry-title"><?php the_title(); ?></h1>

                <p class="jn-activite-classements">
                    <?php
                    echo get_the_term_list( $jn_id, 'type_activite', '', ' ' );
                    echo get_the_term_list( $jn_id, 'niveau', ' ', ' ' );
                    ?>
                </p>

            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="jn-activite-image">
                    <?php the_post_thumbnail( 'large' ); ?>
                </figure>
            <?php endif; ?>

            <div class="jn-activite-colonnes">

                <div class="jn-activite-texte">
                    <?php the_content(); ?>
                </div>

                <aside class="jn-activite-infos">

                    <h2>Informations pratiques</h2>

                    <dl class="jn-infos-liste">

                        <?php if ( $jn_date ) : ?>
                            <dt>Date</dt>
                            <dd><?php echo esc_html( date_i18n( 'l j F Y', strtotime( $jn_date ) ) ); ?></dd>
                        <?php endif; ?>

                        <?php if ( $jn_heure ) : ?>
                            <dt>Rendez-vous</dt>
                            <dd><?php echo esc_html( $jn_heure ); ?></dd>
                        <?php endif; ?>

                        <?php if ( $jn_duree ) : ?>
                            <dt>Durée</dt>
                            <dd><?php echo esc_html( $jn_duree ); ?></dd>
                        <?php endif; ?>

                        <?php if ( $jn_lieu ) : ?>
                            <dt>Lieu</dt>
                            <dd><?php echo esc_html( $jn_lieu ); ?></dd>
                        <?php endif; ?>

                        <?php if ( $jn_places ) : ?>
                            <dt>Participants</dt>
                            <dd><?php echo esc_html( $jn_places ); ?> au maximum</dd>
                        <?php endif; ?>

                        <dt>Tarif</dt>
                        <dd>
                            <?php
                            echo $jn_tarif > 0
                                ? esc_html( number_format_i18n( $jn_tarif ) . ' won' )
                                : 'Gratuit';
                            ?>
                        </dd>

                        <?php if ( 'annulee' === $jn_statut ) : ?>
                            <dt>Statut</dt>
                            <dd><strong>Sortie annulée</strong></dd>
                        <?php elseif ( 'complete' === $jn_statut ) : ?>
                            <dt>Statut</dt>
                            <dd><strong>Sortie complète</strong></dd>
                        <?php endif; ?>

                    </dl>

                </aside>

            </div>

            <section class="jn-activite-reservation">
                <?php
                if ( function_exists( 'jnr_fabriquer_formulaire' ) ) {
                    echo jnr_fabriquer_formulaire( $jn_id ); // Déjà échappé dans l'extension.
                }
                ?>
            </section>

        </article>

        <nav class="jn-activite-suite">
            <?php
            the_post_navigation(
                array(
                    'prev_text' => 'Activité précédente : %title',
                    'next_text' => 'Activité suivante : %title',
                )
            );
            ?>
        </nav>

    </main><!-- #primary -->

<?php
endwhile;

get_footer();