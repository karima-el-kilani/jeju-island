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