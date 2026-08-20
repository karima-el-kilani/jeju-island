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

/**
 * Déclare le type de contenu « Réservation ».
 *
 * Contrairement aux activités, ce type n'est PAS public : une réservation
 * contient le nom et le courriel d'un visiteur. Elle ne doit ni avoir
 * d'adresse web, ni apparaître dans les résultats de recherche, ni être
 * exposée par l'API REST. Elle reste en revanche consultable dans
 * l'administration, sinon l'association ne pourrait pas la traiter.
 */
function jnr_enregistrer_type_reservation() {

    $libelles = array(
            'name'               => 'Réservations',
            'singular_name'      => 'Réservation',
            'menu_name'          => 'Réservations',
            'add_new'            => 'Ajouter',
            'add_new_item'       => 'Ajouter une réservation',
            'edit_item'          => 'Consulter la réservation',
            'new_item'           => 'Nouvelle réservation',
            'view_item'          => 'Voir la réservation',
            'search_items'       => 'Rechercher une réservation',
            'all_items'          => 'Toutes les réservations',
            'not_found'          => 'Aucune réservation',
            'not_found_in_trash' => 'Aucune réservation dans la corbeille',
    );

    $reglages = array(
            'labels'              => $libelles,

        // Le verrou principal : aucune façade publique.
            'public'              => false,

        // On rouvre uniquement la porte de l'administration.
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 6,
            'menu_icon'           => 'dashicons-tickets-alt',

        // Les trois lignes suivantes découlent déjà de « public => false ».
        // On les écrit quand même : l'intention doit être lisible par
        // quelqu'un qui relit le code, et elle résiste à une modification
        // distraite de la ligne du dessus.
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'has_archive'         => false,

        // L'API REST exposerait ces données à qui sait la lire.
            'show_in_rest'        => false,

        // Seul le titre nous sert : tout le reste vit en champs
        // personnalisés. Pas d'éditeur, pas d'extrait, pas d'image.
            'supports'            => array( 'title' ),
    );

    register_post_type( 'reservation', $reglages );
}
add_action( 'init', 'jnr_enregistrer_type_reservation' );

/**
 * Fabrique le formulaire de réservation d'une activité.
 *
 * Cette fonction RENVOIE le code HTML, elle n'affiche rien elle-même.
 * C'est ce qui permet de s'en servir aussi bien depuis un code court
 * que depuis un fichier modèle du thème.
 *
 * @param int $activite_id Identifiant de l'activité concernée.
 * @return string Le code HTML du formulaire, ou une chaîne vide.
 */
function jnr_fabriquer_formulaire( $activite_id ) {

    $activite_id = absint( $activite_id );

    // Garde-fou : sans activité valide, on ne fabrique rien du tout.
    if ( ! $activite_id || 'activite' !== get_post_type( $activite_id ) ) {
        return '';
    }

    // On capture l'affichage au lieu de le laisser partir vers le navigateur.
    ob_start();
    ?>

    <form class="jn-formulaire" method="post"
          action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

        <h2>Réserver cette sortie</h2>

        <?php wp_nonce_field( 'jnr_nouvelle_reservation', 'jnr_nonce_reservation' ); ?>

        <input type="hidden" name="action" value="jnr_nouvelle_reservation">
        <input type="hidden" name="jn_activite_id"
               value="<?php echo esc_attr( $activite_id ); ?>">

        <p>
            <label for="jn_nom">Nom et prénom <span class="jn-obligatoire">*</span></label><br>
            <input type="text" id="jn_nom" name="jn_nom" required maxlength="100">
        </p>

        <p>
            <label for="jn_email">Adresse électronique <span class="jn-obligatoire">*</span></label><br>
            <input type="email" id="jn_email" name="jn_email" required maxlength="100">
        </p>

        <p>
            <label for="jn_telephone">Téléphone</label><br>
            <input type="tel" id="jn_telephone" name="jn_telephone" maxlength="30">
        </p>

        <p>
            <label for="jn_participants">Nombre de participants <span class="jn-obligatoire">*</span></label><br>
            <input type="number" id="jn_participants" name="jn_participants"
                   min="1" max="20" step="1" value="1" required>
        </p>

        <p>
            <label for="jn_message">Message (facultatif)</label><br>
            <textarea id="jn_message" name="jn_message" rows="4" maxlength="1000"></textarea>
        </p>

        <p>
            <input type="checkbox" id="jn_consentement" name="jn_consentement" value="1" required>
            <label for="jn_consentement">
                J'accepte que mes coordonnées soient conservées par l'association
                pour le traitement de cette demande.
                <span class="jn-obligatoire">*</span>
            </label>
            <?php
            $page_confidentialite = get_privacy_policy_url();
            if ( $page_confidentialite ) :
                ?>
                <br>
                <a href="<?php echo esc_url( $page_confidentialite ); ?>">
                    Politique de confidentialité
                </a>
            <?php endif; ?>
        </p>

        <p>
            <button type="submit" class="jn-bouton">Envoyer ma demande</button>
        </p>

        <p class="jn-meta">Les champs marqués d'une étoile sont obligatoires.</p>

    </form>

    <?php
    // On récupère ce qui a été capturé, et on vide le seau.
    return ob_get_clean();
}

/**
 * Déclare le code court [formulaire_reservation].
 *
 * Un code court se comporte comme un filtre : il doit RENVOYER
 * son contenu. S'il l'affichait avec echo, le formulaire
 * apparaîtrait tout en haut de la page au lieu d'être à sa place.
 *
 * @param array $attributs Les attributs écrits dans le code court.
 * @return string Le code HTML du formulaire.
 */
function jnr_code_court_formulaire( $attributs ) {

    $attributs = shortcode_atts(
            array( 'activite' => 0 ),
            $attributs,
            'formulaire_reservation'
    );

    $activite_id = absint( $attributs['activite'] );

    // Sans attribut, on prend l'activité de la page en cours.
    if ( ! $activite_id ) {
        $activite_id = get_the_ID();
    }

    return jnr_fabriquer_formulaire( $activite_id );
}
add_shortcode( 'formulaire_reservation', 'jnr_code_court_formulaire' );

/**
 * Reçoit et traite le formulaire public de réservation.
 *
 * Contrairement à l'enregistrement des activités, il n'y a ici AUCUN
 * contrôle de droits : n'importe quel visiteur a le droit d'envoyer une
 * demande. La protection ne porte donc pas sur QUI envoie, mais sur CE
 * QUI est envoyé — d'où la validation de chaque champ, un par un.
 */
function jnr_traiter_reservation() {

    // ---- 1. La demande vient-elle bien de notre formulaire ? (anti-CSRF)
    $nonce = isset( $_POST['jnr_nonce_reservation'] )
            ? sanitize_text_field( wp_unslash( $_POST['jnr_nonce_reservation'] ) )
            : '';

    if ( ! wp_verify_nonce( $nonce, 'jnr_nouvelle_reservation' ) ) {
        jnr_rediriger_apres_envoi( 0, 'erreur' );
    }

    // ---- 2. L'activité visée existe-t-elle vraiment ?
    $activite_id = isset( $_POST['jn_activite_id'] )
            ? absint( wp_unslash( $_POST['jn_activite_id'] ) )
            : 0;

    if ( ! $activite_id || 'activite' !== get_post_type( $activite_id ) ) {
        jnr_rediriger_apres_envoi( 0, 'erreur' );
    }

    // ---- 3. Le consentement RGPD est-il donné ?
    $consentement = isset( $_POST['jn_consentement'] )
            ? sanitize_text_field( wp_unslash( $_POST['jn_consentement'] ) )
            : '';

    if ( '1' !== $consentement ) {
        jnr_rediriger_apres_envoi( $activite_id, 'consentement' );
    }

    // ---- 4. Nettoyage et validation de chaque champ.

    $nom = isset( $_POST['jn_nom'] )
            ? sanitize_text_field( wp_unslash( $_POST['jn_nom'] ) )
            : '';
    $nom = mb_substr( $nom, 0, 100 );

    $email = isset( $_POST['jn_email'] )
            ? sanitize_email( wp_unslash( $_POST['jn_email'] ) )
            : '';

    $telephone = isset( $_POST['jn_telephone'] )
            ? sanitize_text_field( wp_unslash( $_POST['jn_telephone'] ) )
            : '';
    $telephone = mb_substr( $telephone, 0, 30 );

    $participants = isset( $_POST['jn_participants'] )
            ? absint( wp_unslash( $_POST['jn_participants'] ) )
            : 0;

    $message = isset( $_POST['jn_message'] )
            ? sanitize_textarea_field( wp_unslash( $_POST['jn_message'] ) )
            : '';
    $message = mb_substr( $message, 0, 1000 );

    // Les trois champs obligatoires doivent être exploitables.
    if ( '' === $nom || ! is_email( $email ) || $participants < 1 || $participants > 20 ) {
        jnr_rediriger_apres_envoi( $activite_id, 'invalide' );
    }

    // ---- 5. Création de la réservation.
    $titre = sprintf(
            '%1$s — %2$s (%3$d)',
            get_the_title( $activite_id ),
            $nom,
            $participants
    );

    $reservation_id = wp_insert_post(
            array(
                    'post_type'   => 'reservation',
                    'post_title'  => $titre,
                    'post_status' => 'publish',
            ),
            true
    );

    if ( is_wp_error( $reservation_id ) ) {
        jnr_rediriger_apres_envoi( $activite_id, 'erreur' );
    }

    // ---- 6. Enregistrement des champs.
    update_post_meta( $reservation_id, '_jn_activite_id', $activite_id );
    update_post_meta( $reservation_id, '_jn_nom', $nom );
    update_post_meta( $reservation_id, '_jn_email', $email );
    update_post_meta( $reservation_id, '_jn_telephone', $telephone );
    update_post_meta( $reservation_id, '_jn_participants', $participants );
    update_post_meta( $reservation_id, '_jn_message', $message );
    update_post_meta( $reservation_id, '_jn_consentement', 1 );
    update_post_meta( $reservation_id, '_jn_date_consentement', current_time( 'mysql' ) );
    update_post_meta( $reservation_id, '_jn_statut_reservation', 'en_attente' );

    jnr_rediriger_apres_envoi( $activite_id, 'ok' );
}
add_action( 'admin_post_nopriv_jnr_nouvelle_reservation', 'jnr_traiter_reservation' );
add_action( 'admin_post_jnr_nouvelle_reservation', 'jnr_traiter_reservation' );

/**
 * Renvoie le visiteur sur la page de l'activité avec un code de résultat.
 *
 * Cette redirection est indispensable : sans elle, le visiteur resterait
 * sur admin-post.php, et un simple rafraîchissement renverrait le
 * formulaire une seconde fois.
 *
 * @param int    $activite_id Activité vers laquelle revenir, 0 pour l'accueil.
 * @param string $resultat    Code de résultat à afficher.
 */
function jnr_rediriger_apres_envoi( $activite_id, $resultat ) {

    $adresse = $activite_id ? get_permalink( $activite_id ) : home_url( '/' );

    wp_safe_redirect( add_query_arg( 'jnr', rawurlencode( $resultat ), $adresse ) );
    exit;
}