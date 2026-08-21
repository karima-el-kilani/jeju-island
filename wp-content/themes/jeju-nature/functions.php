<?php
/**
 * Jeju Nature functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Jeju_Nature
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function jeju_nature_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Jeju Nature, use a find and replace
		* to change 'jeju-nature' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'jeju-nature', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'jeju-nature' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'jeju_nature_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'jeju_nature_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function jeju_nature_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'jeju_nature_content_width', 640 );
}
add_action( 'after_setup_theme', 'jeju_nature_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function jeju_nature_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'jeju-nature' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'jeju-nature' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'jeju_nature_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function jeju_nature_scripts() {
	wp_enqueue_style( 'jeju-nature-style', get_stylesheet_uri(), array(), _S_VERSION );

	wp_enqueue_script( 'jeju-nature-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'jeju_nature_scripts' );


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';


/**
 * Applique les filtres de l'archive des activités.
 *
 * On modifie la requête principale plutôt que d'en créer une seconde :
 * la pagination et le comptage restent cohérents.
 *
 * Quatre critères : type d'activité, niveau, tarif, date de départ.
 *
 * @param WP_Query $query La requête en cours de préparation.
 */
function jeju_nature_filtrer_activites( $query ) {

    // Uniquement la requête principale du site public,
    // et uniquement sur la page de liste des activités.
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( ! $query->is_post_type_archive( 'activite' ) ) {
        return;
    }

    $query->set( 'posts_per_page', 12 );

    // Tri chronologique sur la date de sortie, pas sur la date de publication.
    $query->set( 'meta_key', '_jn_date' );
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'order', 'ASC' );

    $filtres_taxonomies = array();
    $filtres_champs     = array();

    // --- Critère 1 : le type d'activité.
    $type = isset( $_GET['jn_type'] ) ? sanitize_title( wp_unslash( $_GET['jn_type'] ) ) : '';
    if ( $type ) {
        $filtres_taxonomies[] = array(
            'taxonomy' => 'type_activite',
            'field'    => 'slug',
            'terms'    => $type,
        );
    }

    // --- Critère 2 : le niveau de difficulté.
    $niveau = isset( $_GET['jn_niveau'] ) ? sanitize_title( wp_unslash( $_GET['jn_niveau'] ) ) : '';
    if ( $niveau ) {
        $filtres_taxonomies[] = array(
            'taxonomy' => 'niveau',
            'field'    => 'slug',
            'terms'    => $niveau,
        );
    }

    // --- Critère 3 : gratuit ou payant.
    $tarif = isset( $_GET['jn_tarif'] ) ? sanitize_key( wp_unslash( $_GET['jn_tarif'] ) ) : '';
    if ( 'gratuit' === $tarif ) {
        $filtres_champs[] = array(
            'key'     => '_jn_tarif',
            'value'   => 0,
            'compare' => '=',
            'type'    => 'NUMERIC',
        );
    } elseif ( 'payant' === $tarif ) {
        $filtres_champs[] = array(
            'key'     => '_jn_tarif',
            'value'   => 0,
            'compare' => '>',
            'type'    => 'NUMERIC',
        );
    }

    // --- Critère 4 : à partir d'une date.
    $apres = isset( $_GET['jn_apres'] ) ? sanitize_text_field( wp_unslash( $_GET['jn_apres'] ) ) : '';
    if ( $apres && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $apres ) ) {
        $filtres_champs[] = array(
            'key'     => '_jn_date',
            'value'   => $apres,
            'compare' => '>=',
            'type'    => 'DATE',
        );
    }

    if ( $filtres_taxonomies ) {
        $filtres_taxonomies['relation'] = 'AND';
        $query->set( 'tax_query', $filtres_taxonomies );
    }
    if ( $filtres_champs ) {
        $filtres_champs['relation'] = 'AND';
        $query->set( 'meta_query', $filtres_champs );
    }
}
add_action( 'pre_get_posts', 'jeju_nature_filtrer_activites' );

/**
 * Active l'image panoramique de la page d'accueil.
 *
 * On passe par la fonctionnalité native « image d'en-tête » plutôt que
 * par une image codée en dur : l'association peut ainsi la changer
 * depuis Apparence → Personnaliser, sans toucher au code (§19).
 */
function jeju_nature_image_haut() {
    add_theme_support(
        'custom-header',
        array(
            'default-image' => '',
            'width'         => 1920,
            'height'        => 900,
            'flex-width'    => true,
            'flex-height'   => true,
            'header-text'   => false,
        )
    );
}
add_action( 'after_setup_theme', 'jeju_nature_image_haut' );
