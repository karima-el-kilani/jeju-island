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