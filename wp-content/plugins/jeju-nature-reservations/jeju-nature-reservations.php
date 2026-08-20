<?php
/**
 * Plugin Name:       Jeju Nature Réservations
 * Description:       Gère le type de contenu « Activité », ses taxonomies, ses champs personnalisés et les demandes de réservation.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Karima El Kilani
 * Author URI:        https://github.com/karima-el-kilani
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jeju-nature-reservations
 *
 * @package Jeju_Nature_Reservations
 */

// Sécurité : empêche l'accès direct au fichier par une adresse web.
// ABSPATH est une constante que seul WordPress définit. Si elle n'existe
// pas, c'est que le fichier a été appelé hors de WordPress : on arrête tout.
defined( 'ABSPATH' ) || exit;

/**
 * Déclare le type de contenu « Activité ».
 *
 * Le nom technique est « activite » : sans accent, au singulier.
 * Il est imposé par le §4.1 de l'énoncé et conditionne les noms des
 * fichiers modèles single-activite.php et archive-activite.php.
 */
function jnr_enregistrer_type_activite() {

    // Les libellés : tout ce que l'humain lit dans l'administration.
    $libelles = array(
        'name'               => 'Activités',
        'singular_name'      => 'Activité',
        'menu_name'          => 'Activités',
        'add_new'            => 'Ajouter',
        'add_new_item'       => 'Ajouter une activité',
        'edit_item'          => 'Modifier une activité',
        'new_item'           => 'Nouvelle activité',
        'view_item'          => 'Voir une activité',
        'view_items'         => 'Voir les activités',
        'search_items'       => 'Rechercher une activité',
        'all_items'          => 'Toutes les activités',
        'not_found'          => 'Aucune activité trouvée',
        'not_found_in_trash' => 'Aucune activité dans la corbeille',
    );

    // Les réglages : comment le type se comporte.
    $reglages = array(
        'labels'        => $libelles,
        'public'        => true,
        'has_archive'   => true,
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-palmtree',
        'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
        'rewrite'       => array( 'slug' => 'activites' ),
        'show_in_rest'  => true,
    );

    register_post_type( 'activite', $reglages );
}
add_action( 'init', 'jnr_enregistrer_type_activite' );


/**
 * Déclare les deux taxonomies rattachées au type « activite ».
 *
 * Les deux sont hiérarchiques : dans l'écran d'édition, elles s'affichent
 * sous forme de cases à cocher et non de champ libre. L'association choisit
 * dans une liste, elle ne tape rien — donc aucune faute de frappe possible.
 */
function jnr_enregistrer_taxonomies() {

    // 1. Type d'activité : Randonnée, Atelier, Visite, Patrimoine...
    register_taxonomy(
        'type_activite',
        'activite',
        array(
            'labels'            => array(
                'name'          => "Types d'activité",
                'singular_name' => "Type d'activité",
                'menu_name'     => "Types d'activité",
                'all_items'     => 'Tous les types',
                'edit_item'     => 'Modifier un type',
                'add_new_item'  => 'Ajouter un type',
                'new_item_name' => 'Nom du nouveau type',
                'search_items'  => 'Rechercher un type',
                'not_found'     => 'Aucun type trouvé',
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'type-activite' ),
        )
    );

    // 2. Niveau : Facile, Intermédiaire, Difficile.
    register_taxonomy(
        'niveau',
        'activite',
        array(
            'labels'            => array(
                'name'          => 'Niveaux',
                'singular_name' => 'Niveau',
                'menu_name'     => 'Niveaux',
                'all_items'     => 'Tous les niveaux',
                'edit_item'     => 'Modifier un niveau',
                'add_new_item'  => 'Ajouter un niveau',
                'new_item_name' => 'Nom du nouveau niveau',
                'search_items'  => 'Rechercher un niveau',
                'not_found'     => 'Aucun niveau trouvé',
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'niveau' ),
        )
    );
}
add_action( 'init', 'jnr_enregistrer_taxonomies' );


/**
 * Déclare la métaboîte « Détails de la sortie » sur l'écran d'édition
 * d'une activité.
 */
function jnr_ajouter_metaboite() {
    add_meta_box(
        'jnr_details_activite',        // Identifiant technique de la boîte.
        'Détails de la sortie',        // Titre affiché en haut de la boîte.
        'jnr_afficher_metaboite',      // Fonction qui dessine son contenu.
        'activite',                    // Type de contenu concerné.
        'normal',                      // Emplacement : colonne principale.
        'high'                         // Priorité : le plus haut possible.
    );
}
add_action( 'add_meta_boxes', 'jnr_ajouter_metaboite' );

/**
 * Dessine le contenu de la métaboîte.
 *
 * @param WP_Post $post Le contenu en cours d'édition.
 */
function jnr_afficher_metaboite( $post ) {

    // Le nonce : un jeton anti-CSRF, vérifié au moment de l'enregistrement.
    wp_nonce_field( 'jnr_enregistrer_details', 'jnr_nonce_details' );

    // On récupère les valeurs déjà enregistrées, s'il y en a.
    $date       = get_post_meta( $post->ID, '_jn_date', true );
    $heure      = get_post_meta( $post->ID, '_jn_heure', true );
    $duree      = get_post_meta( $post->ID, '_jn_duree', true );
    $lieu       = get_post_meta( $post->ID, '_jn_lieu', true );
    $places_max = get_post_meta( $post->ID, '_jn_places_max', true );
    $tarif      = get_post_meta( $post->ID, '_jn_tarif', true );
    $statut     = get_post_meta( $post->ID, '_jn_statut', true );

    // Une sortie nouvellement créée est ouverte par défaut.
    if ( '' === $statut ) {
        $statut = 'ouverte';
    }
    ?>

    <p>
        <label for="jn_date"><strong>Date de la sortie</strong></label><br>
        <input type="date" id="jn_date" name="jn_date"
               value="<?php echo esc_attr( $date ); ?>">
    </p>

    <p>
        <label for="jn_heure"><strong>Heure de début</strong></label><br>
        <input type="time" id="jn_heure" name="jn_heure"
               value="<?php echo esc_attr( $heure ); ?>">
    </p>

    <p>
        <label for="jn_duree"><strong>Durée</strong></label><br>
        <input type="text" id="jn_duree" name="jn_duree" class="regular-text"
               placeholder="3 h 30"
               value="<?php echo esc_attr( $duree ); ?>">
    </p>

    <p>
        <label for="jn_lieu"><strong>Lieu de rendez-vous</strong></label><br>
        <input type="text" id="jn_lieu" name="jn_lieu" class="regular-text"
               placeholder="Entrée Seongpanak, parc national du Hallasan"
               value="<?php echo esc_attr( $lieu ); ?>">
    </p>

    <p>
        <label for="jn_places_max"><strong>Nombre maximal de participants</strong></label><br>
        <input type="number" id="jn_places_max" name="jn_places_max" min="1" step="1"
               value="<?php echo esc_attr( $places_max ); ?>">
    </p>

    <p>
        <label for="jn_tarif"><strong>Tarif en wons</strong></label><br>
        <input type="number" id="jn_tarif" name="jn_tarif" min="0" step="1"
               value="<?php echo esc_attr( $tarif ); ?>">
        <span class="description">0 si la sortie est gratuite.</span>
    </p>

    <p>
        <label for="jn_statut"><strong>Statut de la sortie</strong></label><br>
        <select id="jn_statut" name="jn_statut">
            <option value="ouverte"  <?php selected( $statut, 'ouverte' ); ?>>Ouverte</option>
            <option value="complete" <?php selected( $statut, 'complete' ); ?>>Complète</option>
            <option value="annulee"  <?php selected( $statut, 'annulee' ); ?>>Annulée</option>
        </select>
        <span class="description">Statut métier, distinct du statut de publication WordPress.</span>
    </p>

    <?php
}


/**
 * Enregistre les champs personnalisés d'une activité.
 *
 * Quatre vérifications précèdent tout enregistrement : origine de la requête,
 * sauvegarde automatique, droits de l'utilisateur, puis nettoyage des données.
 *
 * @param int $post_id Identifiant de l'activité en cours d'enregistrement.
 */
function jnr_enregistrer_details( $post_id ) {

    // ---- 1. La requête vient-elle bien de notre formulaire ? (anti-CSRF)
    if ( ! isset( $_POST['jnr_nonce_details'] ) ) {
        return;
    }
    $nonce = sanitize_text_field( wp_unslash( $_POST['jnr_nonce_details'] ) );
    if ( ! wp_verify_nonce( $nonce, 'jnr_enregistrer_details' ) ) {
        return;
    }

    // ---- 2. WordPress enregistre des brouillons tout seul : on ne touche à rien.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // ---- 3. L'utilisateur a-t-il le droit de modifier ce contenu ?
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // ---- 4. Nettoyage puis enregistrement, champ par champ.

    // Date : on n'accepte que le format AAAA-MM-JJ, rien d'autre.
    $date = '';
    if ( isset( $_POST['jn_date'] ) ) {
        $saisie = sanitize_text_field( wp_unslash( $_POST['jn_date'] ) );
        if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $saisie ) ) {
            $date = $saisie;
        }
    }
    update_post_meta( $post_id, '_jn_date', $date );

    // Heure : on n'accepte que le format HH:MM.
    $heure = '';
    if ( isset( $_POST['jn_heure'] ) ) {
        $saisie = sanitize_text_field( wp_unslash( $_POST['jn_heure'] ) );
        if ( preg_match( '/^\d{2}:\d{2}$/', $saisie ) ) {
            $heure = $saisie;
        }
    }
    update_post_meta( $post_id, '_jn_heure', $heure );

    // Durée et lieu : du texte libre, nettoyé de tout code.
    $duree = isset( $_POST['jn_duree'] )
        ? sanitize_text_field( wp_unslash( $_POST['jn_duree'] ) )
        : '';
    update_post_meta( $post_id, '_jn_duree', $duree );

    $lieu = isset( $_POST['jn_lieu'] )
        ? sanitize_text_field( wp_unslash( $_POST['jn_lieu'] ) )
        : '';
    update_post_meta( $post_id, '_jn_lieu', $lieu );

    // Places : un entier strictement positif, ou rien du tout.
    // Une sortie à zéro participant n'a pas de sens : on distingue
    // « non renseigné » (chaîne vide) d'une valeur valide.
    $places_max = '';
    if ( isset( $_POST['jn_places_max'] ) ) {
        $saisie = absint( wp_unslash( $_POST['jn_places_max'] ) );
        if ( $saisie > 0 ) {
            $places_max = $saisie;
        }
    }
    update_post_meta( $post_id, '_jn_places_max', $places_max );

    // Tarif : un entier positif. 0 est une valeur légitime (sortie gratuite).
    $tarif = isset( $_POST['jn_tarif'] )
        ? absint( wp_unslash( $_POST['jn_tarif'] ) )
        : 0;
    update_post_meta( $post_id, '_jn_tarif', $tarif );

    // Statut : liste blanche. Toute autre valeur est ignorée.
    $statuts_autorises = array( 'ouverte', 'complete', 'annulee' );
    $statut            = 'ouverte';
    if ( isset( $_POST['jn_statut'] ) ) {
        $saisie = sanitize_text_field( wp_unslash( $_POST['jn_statut'] ) );
        if ( in_array( $saisie, $statuts_autorises, true ) ) {
            $statut = $saisie;
        }
    }
    update_post_meta( $post_id, '_jn_statut', $statut );
}
add_action( 'save_post_activite', 'jnr_enregistrer_details' );