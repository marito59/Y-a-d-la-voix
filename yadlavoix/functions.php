<?php

	/*
	 * Fonction de base pour un child theme qui permet d'enregistrer le lien vers le thème parent
	 */
	function my_theme_enqueue_styles() {

		$parent_style = 'avada-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

		wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'child-style',
			get_stylesheet_directory_uri() . '/style.css',
			array( $parent_style ),
			wp_get_theme()->get('Version')
		);
	}
	add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

	function my_child_theme_setup() {
		// load custom translation file for the parent theme
		load_theme_textdomain( 'Avada', get_stylesheet_directory() . '/languages/yadlavoix' );
		// load translation file for the child theme
		load_child_theme_textdomain( 'Avada', get_stylesheet_directory() . '/languages' );
	}
	add_action( 'after_setup_theme', 'my_child_theme_setup' );
	

	// utilisation d'une template part dans Display Post Shortcode pour afficher les évènements
	function cma_ydlv_dps_template_part( $output, $original_atts ) {

		// Return early if our "layout" attribute is not specified
		if( empty( $original_atts['layout'] ) )
			return $output;
		ob_start();
		get_template_part( 'template-parts/posts', $original_atts['layout'] );
		$new_output = ob_get_clean();
		if( !empty( $new_output ) )
			$output = $new_output;
		return $output;
	}

	add_action( 'display_posts_shortcode_output', 'cma_ydlv_dps_template_part', 10, 2 );
	
	/**
	 * Filter the "read more" excerpt string link to the post.
	 *
	 * @param string $more "Read more" excerpt string.
	 * @return string (Maybe) modified "read more" excerpt string.
	 */
	function my_custom_excerpt_more( $more ) {
		return sprintf( '<br /><p><a class="read-more" href="%1$s">%2$s</a></p>',
			get_permalink( get_the_ID() ),
			__( 'Read More', 'Avada' )
		);
	}
	add_filter( 'excerpt_more', 'my_custom_excerpt_more' ); // pour the_excerpt (extrait créé automatiquement)
	add_filter( 'the_content_more_link', 'my_custom_excerpt_more' ); // pour the_content (extrait créé avec le more tag)

	// ajoute les styles dans l'éditeur TinyMCE

	function cma_theme_add_editor_styles() {
		add_editor_style();
	}
	add_action( 'admin_init', 'cma_theme_add_editor_styles' );

	// autorise les appels récursifs de Display Posts Shortcode
	add_filter( 'display_posts_shortcode_inception_override', '__return_false' );

	// ajoute des catégories aux medias
	// register new taxonomy which applies to attachments
	// from https://code.tutsplus.com/articles/applying-categories-tags-and-custom-taxonomies-to-media-attachments--wp-32319
/* 	function cma_add_location_taxonomy() {
		$labels = array(
			'name'              => 'Catégories d\'images',
			'singular_name'     => 'Catégorie d\'image',
			'search_items'      => 'Rechercher',
			'all_items'         => 'Toutes',
			'parent_item'       => 'Catégorie parente',
			'parent_item_colon' => 'Catégorie parente:',
			'edit_item'         => 'Modifier',
			'update_item'       => 'Mettre à jour',
			'add_new_item'      => 'Ajouter une nouvelle catégorie',
			'new_item_name'     => 'Nouveau nom de catégorie',
			'menu_name'         => 'Catégorie',
		);
	
		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'query_var' => 'true',
			'rewrite' => 'true',
			'show_admin_column' => 'true',
		);
	
		register_taxonomy( 'catégorie', 'attachment', $args );
	}
	add_action( 'init', 'cma_add_location_taxonomy' );

	// ajouter une bulk action sur les media (attachments)
	// from : https://make.wordpress.org/core/2016/10/04/custom-bulk-actions/
	add_filter( 'bulk_actions-edit-attachment', 'cma_register_my_bulk_actions' );
 
	function cma_register_my_bulk_actions($bulk_actions) {
		// note CMA : il faudrait faire une boucle sur les catégories
		$bulk_actions['email_to_eric'] = __( 'Email to Eric', 'email_to_eric');
		return $bulk_actions;
	} */
?>