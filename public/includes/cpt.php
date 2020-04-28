<?php
class DrawAttention_CPT {
	public $post_type = 'bv_image';
	public $singular_name = 'Bilde';

	function __construct( $parent ) {

		add_action( 'init' , array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'load_drag_drop_featured_image' ) );
	}

	function load_drag_drop_featured_image() {
		global $drag_drop_featured_image_map;
		if ( empty( $drag_drop_featured_image_map ) || !class_exists( 'WP_Drag_Drop_Featured_Image_Map' ) ) {
			include_once( 'lib/drag-drop-featured-image/index.php' );
			if ( !class_exists( 'WP_Drag_Drop_Featured_Image_Map' ) ) return;
			$drag_drop_featured_image_map = new WP_Drag_Drop_Featured_Image_Map;
		}
	}




	function register_cpt() {
		$result = register_post_type( $this->post_type, /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		 	// let's now add all the options for this post type
			array('labels' => array(
				'name' => __('Bolig Velger', $this->post_type.' general name', 'bolig-velger' ), /* This is the Title of the Group */
				'singular_name' => __('Bilde', $this->post_type.' singular name', 'bolig-velger' ), /* This is the individual type */
				'all_items' => __('Alle Bilder', 'bolig-velger' ), /* the all items menu item */
				'add_new' => __('Legg til ny', 'custom '.$this->post_type.' item', 'bolig-velger' ), /* The add new menu item */
				'add_new_item' => __('Legg til bilde', 'bolig-velger' ), /* Add New Display Title */
				'edit' => __( 'Edit' ), /* Edit Dialog */
				'edit_item' => __('Endre bilde', 'bolig-velger' ), /* Edit Display Title */
				'new_item' => __('Nytt bilde', 'bolig-velger' ), /* New Display Title */
				'view_item' => __('Vis bilde', 'bolig-velger' ), /* View Display Title */
				'search_items' => __('Søkte bilder', 'bolig-velger' ), /* Search CPT_SINGULAR_NAME Title */
				'not_found' =>  __('Nothing found in the Database.', 'bolig-velger' ), /* This displays if there are no entries yet */
				'not_found_in_trash' => __('Nothing found in Trash', 'bolig-velger' ), /* This displays if there is nothing in the trash */
				'parent_item_colon' => ''
				), /* end of arrays */
				'description' => __( 'Stores '.$this->post_type.'s in the database', 'bolig-velger' ), /* CPT_SINGULAR_NAME Description */
				'public' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => true,
				'show_ui' => true,
				'query_var' => true,
				'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */
				'menu_icon' => 'dashicons-images-alt2', /* the icon for the custom post type menu */

				'capabilities' => array(
					'edit_post' => 'edit_others_posts',
					'edit_posts' => 'edit_others_posts',
					'edit_others_posts' => 'edit_others_posts',
					'publish_posts' => 'edit_others_posts',
					'read_post' => 'edit_others_posts',
					'read_private_posts' => 'edit_others_posts',
					'delete_post' => 'edit_others_posts'
				),

				'hierarchical' => false,
				/* the next one is important, it tells what's enabled in the post editor */
				'supports' => array( 'title', 'thumbnail' ),
		 	) /* end of options */
		); /* end of register post type */


	  // Leilighet Post Type
		register_post_type('leilighet', array(
			'supports' => array('title', 'editor', 'thumbnail'),
			'public' => true,
			'labels' => array(					
				'name' => 'Leilighet',
				'add_new_item' => 'Legg til leilighet',
				'edit_item' => 'Endre leilighet',
				'all_items' => 'Alle leiligheter',
				'singular_name' => 'Leilighet'
			),
			'menu_icon' => 'dashicons-welcome-learn-more',
			'taxonomies' => array('category', 'post_tag')
		));
	}

}
