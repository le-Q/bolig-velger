<?php
class DrawAttention_CustomFields {
	public $parent;
	public $prefix = '_bv_';
	public $actions = array();

	function __construct( $parent ) {
		$this->parent = $parent;
		if ( !class_exists( 'CMB2' ) ) {
			if ( file_exists(  __DIR__ .'/lib/cmb2/init.php' ) ) {
				require_once  __DIR__ .'/lib/cmb2/init.php';
			} elseif ( file_exists(  __DIR__ .'/lib/CMB2/init.php' ) ) {
				require_once  __DIR__ .'/lib/CMB2/init.php';
			}
		}
		if ( !class_exists( 'cmb2_bootstrap_208', false ) ) return;

		include_once __DIR__ . '/actions/action.php';
		include_once __DIR__ . '/actions/action-bigcommerce.php';
		$this->actions['bigcommerce'] = new DrawAttention_BigCommerce_Action();
		include_once __DIR__ . '/actions/action-url.php';
		$this->actions['url'] = new DrawAttention_URL_Action();

		add_action( 'wp_ajax_hotspot_update_custom_fields', array( $this, 'update_hotspot_area_details' ) );

		add_filter( 'cmb2_meta_boxes', array( $this, 'hotspot_area_group_details_metabox' ), 11 );
		add_filter( 'cmb2_meta_boxes', array( $this, 'choosen_element' ));

		//add_action( 'cmb2_admin_init', 'choosen_element' );
		add_filter( 'cmb2_enqueue_js', array( $this, 'cmb2_scripts' ) );
		add_action( 'wp_ajax_get_options', array( $this, 'return_options' ) );
		
	}

	function choosen_element(array $metaboxes) {
		if ( empty( $_REQUEST['post'] ) && empty( $_POST ) ) { return $metaboxes; }

		if ( !empty( $_REQUEST['post'] ) ) {
			$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( esc_attr( $_REQUEST['post'] ) ), 'full' );
		}

		$categories = get_categories( array(
			'orderby' => 'name',
			'parent'  => 0
		) );
		$cats = array();

		foreach ( $categories as $category ) {
			$cats[$category->term_id ] = $category->name;
		}

		$metaboxes['test'] = apply_filters( 'test', array(
			'id'           => 'test',
			'title'        => __( 'Valg', 'bolig-velger' ),
			'object_types' => array( $this->parent->cpt->post_type, ),
			'fields'       => array(
						'option' => array(
							'name' => __('Leilighet', 'bolig-velger' ),
							'description' => '',
							'id'   => 'blokk_velger',
							'attributes' => array(
								'class' => 'cmb2_select action'
							),
							'type' => 'select',
							'options' => $cats
						)
					)
			)
		);
  
		return $metaboxes;
	}

	function hotspot_area_group_details_metabox( array $metaboxes ) {
		if ( empty( $_REQUEST['post'] ) && empty( $_POST ) ) { return $metaboxes; }

		if ( !empty( $_REQUEST['post'] ) ) {
			$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( esc_attr( $_REQUEST['post'] ) ), 'full' );
		}

		$metaboxes['field_group'] = apply_filters( 'bv_hotspot_area_group_details', array(
			'id'           => 'field_group',
			'title'        => __( 'Leiligheter', 'bolig-velger' ),
			'object_types' => array( $this->parent->cpt->post_type, ),
			'fields'       => array(
				array(
					'id'          => $this->prefix . 'hotspots',
					'type'        => 'group',
					'options'     => array('group_title'   => __( 'Område #{#}', 'bolig-velger' ) // {#} gets replaced by row number
					),
					// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
					'fields'      => array(
						'coordinates' => array(
							'name' => __( 'Coordinates', 'bolig-velger' ),
							'id'   => 'coordinates',
							'type' => 'text',
							'attributes' => array(
								'data-image-url' => ( !empty( $thumbnail_src[0] ) ) ? $thumbnail_src[0] : '')
						),
						'option' => array(
							'name' => __('Leilighet', 'bolig-velger' ),
							'description' => '',
							'id'   => 'action',
							'attributes' => array(
								'class' => 'cmb2_select action'
							),
							'type' => 'select',
							'options' => $this->set_option(),
						)
					)
				)
			)
		) );
	
		/*
		function enhets_options() {

			$select_value = get_post_meta( 37, 'blokk_velger', true );

			if (!empty($select_value)) {
				$leilighet = array(
					'post_type' => 'leilighet'
				);
			} else {
				$leilighet = array(
					'post_type' => 'leilighet',
					'category_name' => $select_value
				);
			}
			
			$loop = new WP_Query($leilighet);
			$leiligheter = array();
	
			while($loop->have_posts()) : $loop->the_post();
				$leiligheter[get_the_ID()] = get_the_title(); 
			endwhile;
	
			return $leiligheter;
		}
		*/



	
		return $metaboxes;
	}






	function hotspot_area_override_title_and_content( $value, $object_id, $args, $field ) {
		if ( $value != 'cmb2_field_no_override_val' ) return $value; // don't modify already overridden values

		if ( $args['id'] == '_title' ) {
			$post = get_post( $object_id );
			if ( !empty( $post->post_title ) ) return $post->post_title;
		}
		if ( $args['id'] == '_content' ) {
			$post = get_post( $object_id );
			if ( !empty( $post->post_content ) ) return $post->post_content;
		}

		return $value;
	}

	function update_hotspot_area_details() {
		if ( !isset( $_POST['_pid'] ) ) return;
		check_ajax_referer( 'update-hotspot_'.$_POST['_pid'], 'ajaxnonce' );

		if ( isset( $_POST['_title'] ) ) {
			$_POST['_title'] = wp_filter_nohtml_kses( $_POST['_title'] ); // also expects & returns slashes
			$title = $_POST['_title'];
			wp_update_post( array(
				'ID' => $_POST['_pid'],
				'post_title' => $_POST['_title'],
			) );
		}

		if ( isset( $_POST['_content'] ) ) {
			$_POST['_content'] = wp_filter_kses( $_POST['_content'] );
			$title = $_POST['_content'];
			wp_update_post( array(
				'ID' => $_POST['_pid'],
				'post_content' => $_POST['_content'],
			) );
		}

		$coordinates = $_POST[$this->prefix.'coordinates'];
		update_post_meta( $_POST['_pid'], $this->prefix.'coordinates', $coordinates );
	}

	//Oppretter ny metode for custom fields til blokken

	function cmb2_blokk_metabox() {

		$cmb = new_cmb2_box_2( array(
			'id'           => 'cmb2_blokk_metabox',
			'title'        => 'Informasjon',
			'object_types' => array( 'post' ),
		) );
	
		// Blokknummer
		$cmb->add_field( array(
			'name' => 'Blokknummer',
			'id'   => '_cmb2_blokk_nr',
			'type' => 'text',
			'desc' => 'Dette er blokken sitt nummer (Eksempel: A)',
		) );

		// Addresse
		$cmb->add_field( array(
			'name' => 'Addresse',
			'id'   => '_cmb2_blokk_addresse',
			'type' => 'text',
			'desc' => 'Dette er en addresse',
		) );

		// Antall leiligheter
		$cmb->add_field( array(
			'name' => 'Antall leiligheter',
			'id'   => '_cmb2_blokk_antall',
			'type' => 'number',
			'desc' => 'Dette er antall leiligheter i en blokk',
		) );

		add_action( 'cmb2_admin_init', 'cmb2_blokk_metabox' );
	}



	

		// Henter leiligheter
		public function set_option() {

		global $post;

		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : false;
		if( false == $post_id && isset( $post->ID )){
			$post_id = $post->ID;
		}

		$current_option = '';
		if( $post_id ) {
			$current_option = get_post_meta( $post_id, 'blokk_velger', true );
		}


		return $this->get_leilighet( $current_option );
		}
	
		public function get_leilighet( $enhet = '' ) {

		$leilighet = array(
			'post_type' => 'leilighet',
			'cat' => $enhet,
		);

		$loop = new WP_Query($leilighet);
		$leiligheter = array();

		while($loop->have_posts()) : $loop->the_post();
			$leiligheter[get_the_ID()] = get_the_title(); 
		endwhile;

		return $leiligheter;
	}
	
	// Kobler til AJAX - blokk_velger.js
		public function cmb2_scripts( $return ) {
	
			wp_enqueue_script( 'ajaxified_dropdown', plugins_url( 'blokk_velger.js', __FILE__ ), array( 'jquery' ), 0, true );
			return $return;
		}
	
		public function return_options() {
			$value = $_POST[ 'value' ];
			$safe_value = esc_attr( $value );
	
			$options = $this->get_leilighet( $safe_value );
			if( ! $options ){
				wp_send_json_error( array( 'msg' => 'Value inaccessible') );
			}
	
			$output = '';
			foreach( $options as $enhets_value => $enhets_navn ){
				$output .= sprintf( "<option value='%s'>%s</option>", $enhets_value, $enhets_navn );
			}
	
			if( ! empty( $output ) ){
				wp_send_json_success( $output );
			}
	
			wp_send_json_error();
		}

	

}
