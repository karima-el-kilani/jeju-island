<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Jeju_Nature
 */

?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Aucun résultat', 'jeju-nature' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: 1: link to WP admin new post page. */
					__( 'Prêt à publier votre premier contenu ? <a href="%1$s">Commencez ici</a>.', 'jeju-nature' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>

			<p><?php esc_html_e( 'Aucun résultat ne correspond à votre recherche. Essayez avec des mots-clés différents.', 'jeju-nature' ); ?></p>
			<?php
			get_search_form();

		else :
			?>

			<p><?php esc_html_e( 'Nous ne trouvons pas ce que vous cherchez. Essayez une recherche.', 'jeju-nature' ); ?></p>
			<?php
			get_search_form();

		endif;
		?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
