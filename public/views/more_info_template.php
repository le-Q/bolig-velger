<?php if ($settings['layout'] != 'lightbox' && $settings['layout'] != 'tooltip') : ?>
	<div class="hotspots-placeholder" id="content-hotspot-<?php echo $settings['image_id']; ?>">
		<div class="hotspot-initial">
			<h2 class="bolig-velger-title">
				<?php echo get_the_title($settings['image_id']); ?>
			</h2>
			<div class="hotspot-content">
				<?php if (!empty($settings['img_settings'][$this->custom_fields->prefix . 'map_more_info'][0])) : ?>
					<?php echo apply_filters('bv_description', do_shortcode($wp_embed->run_shortcode($settings['img_settings'][$this->custom_fields->prefix . 'map_more_info'][0]))); ?>
				<?php endif;
				// Content
				echo get_the_content($settings['image_id']);
				?>
			</div>
		</div>
	</div>
<?php endif; ?>