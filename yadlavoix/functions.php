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
	
	/*
	 * Enregistrement des formats personnalisés de thumbnail
	 */
	add_action('init', 'my_init_function');
	
    function my_init_function() {
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'nos-cours-image', 360, 240, true ); // Hard Crop Mode
   }	

   	add_filter( 'image_size_names_choose', 'my_custom_sizes' );
   
  	function my_custom_sizes( $sizes ) {
	  	return array_merge( $sizes, array(
			'nos-cours-image' => __( 'Image Nos Cours - 360x240' ),
	  	) );
	}	


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

	// affiche les pièces-jointes attachées à la page
	//Le code ci-dessous n'est pas utilisé - il a été remplacé par l'utilisation de ACF
/* 	function cma_get_attachments($parent_id) {
		$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $parent_id,
				   'post_mime_type'=>array('application/pdf', 'application/msword', 'image/jpeg'), 'order'=>'ASC', 'orderby'=>'ID' ); 
		$attachments = get_posts($args);
		if ($attachments) {
			echo('<ul id="list_docu">');
				foreach ( $attachments as $attachment ) {
					echo '<li class="' . join( ' ', get_post_class() ) . '" id="post-' . get_the_ID() . '">';
				    echo '<img class="picto" src="' . get_bloginfo('stylesheet_directory') . '/images/docu.png" alt="Document" />';
    				echo '<h2>' . $attachment->post_title . '</h2>';
    				echo '<p>' . $attachment->post_content .'</p>';
    				echo '<a href="' . get_permalink( $attachment->ID ) . '" target="_blank"><img class="lire" src="' . get_bloginfo('stylesheet_directory') . '/images/lire.png" alt="lire" /></a>';
					echo '</li>';
				}
			echo('</ul><br />');
		}
	}
 */
	// display file attached to page by ACF
	function cma_acf_attachment ( $file ) {
		echo '<li class="' . join( ' ', get_post_class() ) . '" id="attachment-' . $file["ID"] . '">';
		echo '<img class="picto" src="' . get_bloginfo('stylesheet_directory') . '/images/docu.png" alt="Document" />';
		echo '<h2>' . $file["title"] . '</h2>';
		echo '<p>' . $file["caption"] .'</p>';
		echo '<a href="' . $file["url"] . '" target="_blank"><img class="lire" src="' . get_bloginfo('stylesheet_directory') . '/images/lire.png" alt="lire" /></a>';
		echo '</li>';

	}

	// afichage de la page complète Documents à télécharger depuis le menu
	// La page Documents dans le menu est la page 8686 et les différents documents sont des sous-pages de la page 39
	// Les documents sont attachés aux sous-pages de la page 39 via ACF dans un champ document_pdf
	add_action( 'avada_before_additional_page_content', 'cma_add_attachments');
	function cma_add_attachments () {
		if ( get_the_ID() == 8686 ) {
			echo '<ul id="list_docu">';
			$args = array(
				'sort_order' => 'asc',
				'sort_column' => 'menu_order',
				'hierarchical' => 0,
				'exclude' => '',
				'include' => '',
				'meta_key' => '',
				'meta_value' => '',
				'authors' => '',
				'child_of' => 0,
				'parent' => 39,
				'exclude_tree' => '',
				'number' => '',
				'offset' => 0,
				'post_type' => 'page',
				'post_status' => 'publish'
			); 
			$pages = get_pages($args); 
			foreach ( $pages as $page )	{
				$file = get_field( "document_pdf", $page->ID );
				if ($file) {
					cma_acf_attachment( $file );
				}
			}
			echo '</ul>';
		}
	}

	// Modifie  la présentation de l'article (single) de la catégorie equipe pour inclure le titre et le champ type_enseignement au début du texte 
	// Vient en remplacement du titre normal qui n'est pas affiché par Avada
	function cma_modify_team_post_content( $content ) {
		$cat = get_the_category( );
		if ( $cat[0]->slug != "equipe")
			return $content;

		$result .= "<div class='team_member_header'><span class='team_member_name'>" . get_the_title() . " </span>&bullet; <span class='team_member_desc'>" . get_field( "type_enseignement" ) ."</span></div>";
	
		return $result . $content;
	
	}
	
	add_filter( 'the_content', 'cma_modify_team_post_content' );

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

	// Vérifie si l'adhésion produit woocommerce se trouve dans le panier
	// le Product ID est 9472
	function ydlv_is_adhesion_in_cart() {
		foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
			// est-il déjà dans le panier, si oui, on  ne l'ajoute pas
			$_product = $values['data'];
		
			if( 9472 == $_product->id ) {
				return true;
			}
		}
		return false;
	}

	// vérifie si le contenu du panier nécessite une adhésion
	function ydlv_adhesion_needed() {
		foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
			
			if( "Oui" == $_product->get_attribute("adhesion") ) {
				return true;
			}
		}
		return false;	
	}
	
	// ajoute le produit adhésion au panier (product ID 9472)
	// cette action est déclenchée quand on ajoute un produit au panier
	function ydlv_add_adhesion ( $cart_item_key, $product_id ) {
		if ( $product_id == 9472 ) {
			// inutile de l'ajouter si c'est lui qu'on ajoute
			return;
		}
		$bInCart = ydlv_is_adhesion_in_cart();
		$bAdhesionRequired = ydlv_adhesion_needed();
		if (!$bInCart && $bAdhesionRequired) {
			WC()->cart->add_to_cart( 9472, 1 );
		}
	}
	
	add_action( "woocommerce_add_to_cart", "ydlv_add_adhesion", 10, 2 );
	
	// à l'affichage du panier, on affiche un message indiquant la nécessité de l'adhésion
	// TODO : faire une liste des produits qui nécessitent l'adhésion pour être plus fin
	// dans la gestion de l'adhésion
	function ydlv_msg_adhesion () {
		$bInCart = ydlv_is_adhesion_in_cart();
		$msg = "<span class='adhesion'>L'adhésion à l'association Y a d'la voix ! est obligatoire pour certaines activités. ";
		if ($bInCart) {
			$msg .= "L'adhésion figure dans votre panier. ";
		} else {
			$msg .= "L'adhésion ne figure pas dans votre panier. ";
		}
		$msg .= sprintf("Pensez à %s ce produit à votre panier si vous %s adhérent. ", $bInCart ? "retirer" : "ajouter", $bInCart ? "êtes déjà" : "n'êtes pas encore");
		$msg .= $bInCart ? "" : "Pour ajouter l'adhésion au panier, cliquez sur le bouton ci-dessous" . do_shortcode("[add_to_cart id=\"9472\" show_price=\"FALSE\" style=\"border:0px solid #e2001a; padding: 6px; width:260px;\"]");
		$msg .= "</span>";
	
		echo $msg;
	}
	
	add_action( "woocommerce_before_cart_contents", "ydlv_msg_adhesion" );
?>