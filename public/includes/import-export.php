<?php
class Enhet_ImportExport {
	public $parent;

	function __construct( $parent ) {
		$this->parent = $parent;

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 15 );
	}

	public function is_action( $action ) {
		if ( !empty( $_POST['action'] ) && $_POST['action'] === $action ) {
			return true;
		}

		return false;
	}

	public function process_import() {
		if ( !$this->is_action( 'import' ) ) {
			return;
		}
		
		if ( empty( $_POST['import_code'] ) ) {
			return;
		}
		$import_code = stripslashes($_POST['import_code']);
		$import_array = json_decode( $import_code, true );
		if ( empty( $import_array['0']['post'] ) ) {
			return false;
		}

		$imported = array();
		$errors = array();

		foreach ($import_array as $key => $to_import) {
			unset($to_import['post']['ID']);
			$insert_id = wp_insert_post( $to_import['post'], $error );
			$catID = wp_create_category('cats33');
			if ( !empty( $insert_id ) ) {
				$imported[] = array(
					'ID' => $insert_id,
					'post_title' => $to_import['post']['post_title']." yes ",
					'post_category' => array(2,3),
				);
			} else {
				$errors[] = $to_import;
			}
		}

		return array(
			'imported' => $imported,
			'errors' => $errors,
		);
	}

	public function get_export_array( $ids=array() ) {
		$response = array();
		foreach ($ids as $key => $id) {
			$post = get_post( $id );
			if ( empty( $post->post_type ) || $post->post_type !== 'leilighet' ) {
				continue;
			}

			$response[$key] = array(
				'id' => $id,
				'post' => (array)$post,
			);

			$response[$key]['post']['post_category'] = array(get_the_category($id)[0]->cat_ID);
			$metadata = get_post_meta( $id, '', true );
			foreach ($metadata as $meta_key => $meta_value) {
				if ( strpos( $meta_key, '_cmb2_' ) !== 0 ) {
					continue;
				}
				$response[$key]['post']['meta_input'][$meta_key] = maybe_unserialize( $meta_value[0] );
			}
		}

		return $response;
	}

	public function get_export_json( $ids=array() ) {
		$export_array = $this->get_export_array( $ids );
		return json_encode( $export_array );
	}

	public function admin_menu() {
		global $submenu;

		add_submenu_page( 'edit.php?post_type=leilighet', __( 'Import / Export', 'leilighet' ), __( 'Import / Export', 'leilighet' ), 'manage_options', 'import_export', array( $this, 'output_import_export_page' ) );
	}

	public function output_import_export_page() {
		?>
		<div class="import">
			<h3>Import</h3>
			<p>Hvis du har har en kode, lim inn eksport koden under:</p>
			<form method="POST" name="import" action="edit.php?post_type=leilighet&page=import_export">
				<input type="hidden" name="action" value="import" />
				<textarea name="import_code" cols="100" rows="5" placeholder=""></textarea><br />
				<input type="submit" value="Import" />
			</form>
			<?php $response = $this->process_import(); ?>
			<?php if ( !empty( $response ) ): ?>
				<?php foreach ($response['imported'] as $key => $value): ?>
					<h4>
						Vellykket import
						<a href="<?php echo admin_url( 'post.php?post='.$value['ID'].'&action=edit' ); ?>">
							<?php echo $value['post_title']; ?>
						</a>
					</h4>
				<?php endforeach ?>
				<h3>Advarsel: Bilder blir ikke lastes opp, dette må gjøres manuelt og hvert enkelt.
			<?php endif ?>
		</div>
		<br />
		<div class="export">
			<h3>Export</h3>
			<p>Velg leilighet for eksport.</p>
			<form method="POST" name="export" action="edit.php?post_type=leilighet&page=import_export">
				<input type="hidden" name="action" value="export" />
				<?php
				$da_images = new WP_Query( array(
					'post_type' => 'leilighet',
					'post_status' => 'any',
					'order' => 'DESC',
					'orderby' => 'ID',
				) );
				$export_ids = ( empty( $_POST['export_ids'] ) ) ? array() : (array)$_POST['export_ids'];
				foreach ($da_images->posts as $key => $da_image): ?>
					<input type="checkbox" name="export_ids[]" value="<?php echo $da_image->ID; ?>" id="export_id_<?php echo $da_image->ID; ?>" <?php if( in_array( $da_image->ID, $export_ids ) ) echo 'checked="checked"'; ?> /> <label for="export_id_<?php echo $da_image->ID; ?>"><?php echo $da_image->post_title; ?></label><br />
				<?php endforeach; ?>
				<input type="submit" value="Generate Export Code" />
			</form>

			<?php if ( $this->is_action( 'export' ) ): ?>
				<?php if ( empty( $_POST['export_ids'] ) ): ?>
					Vennligst velg minst en leilighet for eksport.
				<?php else: ?>
					<?php
					$export_ids = $_POST['export_ids'];
					if ( !is_array( $export_ids ) ) {
						$export_ids = array();
					}
					$export_ids = array_map( 'esc_attr', $export_ids );
					$export_json = $this->get_export_json( $export_ids );
					?>
					<textarea cols="100" rows="20"><?php echo $export_json; ?></textarea>
				<?php endif ?>
			<?php endif ?>
		</div>
		<?php
	}

}