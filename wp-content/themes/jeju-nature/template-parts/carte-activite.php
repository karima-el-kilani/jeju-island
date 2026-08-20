<?php
/**
 * Une vignette d'activité, utilisée dans toutes les listes.
 *
 * @package Jeju_Nature
 */

$jn_id      = get_the_ID();
$jn_date    = get_post_meta( $jn_id, '_jn_date', true );
$jn_duree   = get_post_meta( $jn_id, '_jn_duree', true );
$jn_tarif   = (int) get_post_meta( $jn_id, '_jn_tarif', true );
$jn_niveaux = get_the_terms( $jn_id, 'niveau' );
$jn_types   = get_the_terms( $jn_id, 'type_activite' );
?>

<article <?php post_class( 'jn-carte' ); ?>>

    <a class="jn-carte-image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'medium_large' ); ?>
        <?php else : ?>
            <span class="jn-carte-vide"></span>
        <?php endif; ?>
    </a>

    <div class="jn-carte-corps">

        <?php if ( $jn_types && ! is_wp_error( $jn_types ) ) : ?>
            <p class="jn-carte-type"><?php echo esc_html( $jn_types[0]->name ); ?></p>
        <?php endif; ?>

        <h2 class="jn-carte-titre">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <p class="jn-meta">
            <?php
            $jn_morceaux = array();
            if ( $jn_date ) {
                $jn_morceaux[] = date_i18n( 'j F Y', strtotime( $jn_date ) );
            }
            if ( $jn_duree ) {
                $jn_morceaux[] = $jn_duree;
            }
            echo esc_html( implode( ' · ', $jn_morceaux ) );
            ?>
        </p>

        <p class="jn-carte-extrait"><?php echo esc_html( get_the_excerpt() ); ?></p>

        <p class="jn-carte-pied">
            <?php if ( $jn_niveaux && ! is_wp_error( $jn_niveaux ) ) : ?>
                <span class="jn-niveau"><?php echo esc_html( $jn_niveaux[0]->name ); ?></span>
            <?php endif; ?>
            <span class="jn-tarif">
				<?php echo $jn_tarif > 0 ? esc_html( number_format_i18n( $jn_tarif ) . ' won' ) : 'Gratuit'; ?>
			</span>
        </p>

    </div>
</article>