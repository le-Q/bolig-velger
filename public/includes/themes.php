<?php
class DrawAttention_Themes {
	public $parent;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_action( 'bv_register_admin_script', array( $this, 'pass_themes_to_admin_js' ) );
	}
	
	function display_theme_pack_metabox() {
		echo '<p>'.__( 'Quickly apply a theme (you can adjust each color afterwards).', 'bolig-velger' ).'</p>'; ?>
		<select id="da-theme-pack-select">
			<option value=""><?php  _e( 'Select a theme...', 'bolig-velger' ) ?></option>
			<?php foreach ( $this->get_themes() as $key => $theme ) : ?>
			<option value="<?php echo $theme['slug']; ?>"><?php echo $theme['name']; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function pass_themes_to_admin_js() {
		wp_localize_script( $this->parent->plugin_slug . '-admin-script', 'daThemes', array(
			'cfPrefix' => $this->parent->custom_fields->prefix,
			'themes' => $this->get_themes(),
		) );
	}

	public static function apply_theme( $post_id, $theme_slug ) {
		$themes = self::get_themes();
		if ( empty( $themes[$theme_slug]['values'] ) ) { return false; }

		foreach ($themes[$theme_slug]['values'] as $key => $meta_value) {
			update_post_meta( $post_id, '_bv_'.$key, $meta_value );
			// TODO: Make prefix dynamic
		}
	}

	public static function get_themes() {
		$themes = array(
			'boligvelger' => array(
				'slug' => 'boligvelger',
				'name' => 'Draw Attention',
				'values' => array(
					'map_highlight_color' => '#3CA2A2',
					'map_highlight_opacity' => 0.7,

					'map_border_color' => '#235B6E',
					'map_border_opacity' => 1,
					'map_border_width' => 2,

					'map_title_color' => '#93C7A4',
					'map_text_color' => '#DFEBE5',
					'map_background_color' => '#2E2D29',
				),
			),
		);

		return apply_filters( 'bv_themes', $themes );
	}

}
