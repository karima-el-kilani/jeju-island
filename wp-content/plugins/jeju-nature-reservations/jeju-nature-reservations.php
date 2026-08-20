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