<?php

// No hotspots are defined
$has_hotspots = false;
if (!empty($settings['hotspots']['0'])) {
	foreach ($settings['hotspots'] as $key => $hotspot) {
		if (!empty($hotspot['coordinates'])) {
			$has_hotspots = true;
			break;
		}
	}
}

if (empty($has_hotspots)) : ?>
	<p><?php _e(
			'You need to define some clickable areas for your image.',
			'bolig-velger'
		); ?></p>
	<p><?php echo edit_post_link(
			__('Edit Image', 'bolig-velger'),
			false,
			false,
			$settings['image_id']
		); ?></p>

	<?php
	// In page builder edit mode - just display the image
	?>

	<script>
		window.daStyles<?php echo $settings['image_id']; ?> = <?php echo json_encode($formatted_styles); ?>
	</script>

<?php
// There are hotspots! Show the interactive image // Grab the metadata from the database
// There are hotspots! Show the interactive image
/* Error message for admins when there's a JS error */
/* Loop through the hotspots and output the more info content for each */
// Grab the metadata from the database

elseif (
	!empty($_GET['fl_builder']) || !empty($_GET['elementor-preview']) || (!empty($_GET['action']) && $_GET['action'] == 'elementor')
) : ?>
	<div class="hotspots-image-container">
		<img width="<?php echo $settings['img_width']; ?>" height="<?php echo $settings['img_height']; ?>" src="<?php echo $settings['img_url']; ?>" alt="<?php echo esc_attr(
																																								$settings['img_alt']
																																							); ?>" class="hotspots-image skip-lazy" data-id="<?php echo $settings['image_id']; ?>" data-no-lazy="1" data-lazy-src="" data-lazy="false">
	</div>
	<?php
	// There are hotspots! Show the interactive image
	?>
<?php else : ?>

	<style>
		#<?php echo $settings['spot_id']; ?>.hotspots-image-container,
		#<?php echo $settings['spot_id']; ?>.leaflet-container {
			background: <?php echo $settings['img_bg']; ?>
		}

		#<?php echo $settings['spot_id']; ?>.hotspots-placeholder {
			background: <?php echo $settings['more_info_bg']; ?>;
			border: 0 <?php echo $settings['more_info_bg']; ?> solid;
			color: <?php echo $settings['more_info_text']; ?>;
		}

		#<?php echo $settings['spot_id']; ?>.hotspot-title {
			color: <?php echo $settings['more_info_title']; ?>;
		}

		<?php foreach ($formatted_styles as $style) : ?>#<?php echo $settings['spot_id']; ?>.hotspot-<?php echo $style['name']; ?> {
			stroke-width: <?php echo $style['borderWidth']; ?>;
			fill: <?php echo $style['display']['fillColor']; ?>;
			fill-opacity: <?php echo $style['display']['fillOpacity']; ?>;
			stroke: <?php echo $style['display']['borderColor']; ?>;
			stroke-opacity: <?php echo $style['display']['borderOpacity']; ?>;
		}

		.hotspot-active {
			fill-opacity: 0.7;
		}

		<?php endforeach; ?>.ledig {
			fill: green;
			fill-opacity: 0.3;
			stroke: white;
			stroke-width: .2rem;
			stroke-opacity: .8;
		}

		.ledig:hover {
			fill: green;
			fill-opacity: 0.5;
		}

		.ledig.hotspot-active {
			fill: green;
			fill-opacity: 0.5;
		}

		.opptatt {
			fill: red;
			fill-opacity: 0.3;
			stroke: white;
			stroke-width: .2rem;
			stroke-opacity: .8;
		}

		.opptatt:hover {
			fill: red;
			fill-opacity: 0.5;
		}

		.opptatt.hotspot-active {
			fill: red;
			fill-opacity: 0.5;
		}

		#<?php echo $settings['spot_id']; ?>.leaflet-tooltip,
		#<?php echo $settings['spot_id']; ?>.leaflet-rrose-content-wrapper {
			background: <?php echo $settings['more_info_bg']; ?>;
			border-color: <?php echo $settings['more_info_bg']; ?>;
			color: <?php echo $settings['more_info_text']; ?>;
		}

		#<?php echo $settings['spot_id']; ?>a.leaflet-rrose-close-button {
			color: <?php echo $settings['more_info_title']; ?>;
		}

		#<?php echo $settings['spot_id']; ?>.leaflet-rrose-tip {
			background: <?php echo $settings['more_info_bg']; ?>;
		}

		#<?php echo $settings['spot_id']; ?>.leaflet-popup-scrolled {
			border-bottom-color: <?php echo $settings['more_info_text']; ?>;
			border-top-color: <?php echo $settings['more_info_text']; ?>;
		}

		#<?php echo $settings['spot_id']; ?>.leaflet-tooltip-top:before {
			border-top-color: <?php echo $settings['more_info_bg']; ?>;
		}

		#<?php echo $settings['spot_id']; ?>.leaflet-tooltip-bottom:before {
			border-bottom-color: <?php echo $settings['more_info_bg']; ?>;
		}

		#<?php echo $settings['spot_id']; ?>.leaflet-tooltip-left:before {
			border-left-color: <?php echo $settings['more_info_bg']; ?>;
		}

		#<?php echo $settings['spot_id']; ?>.leaflet-tooltip-right:before {
			border-right-color: <?php echo $settings['more_info_bg']; ?>;
		}
	</style>

	<?php
	/*
<script>
	window.daStyles<?php echo $settings['image_id']; ?> = <?php echo json_encode($formatted_styles); ?>
</script>
*/ ?>

	<div class="hotspots-container <?php echo $settings['urls_class']; ?> layout-<?php echo $settings['layout']; ?> event-<?php echo $settings['event_trigger']; ?>" id="<?php echo $settings['spot_id']; ?>" data-layout="<?php echo $settings['layout']; ?>" data-trigger="<?php echo $settings['event_trigger']; ?>">
		<div class="bolig-velger-area">
			<?php if ($settings['urls_only']) {
				require($this->get_plugin_dir() . '/public/views/image_template.php');
			} else {
				require($this->get_plugin_dir() . '/public/views/image_template.php');
				require($this->get_plugin_dir() . '/public/views/more_info_template.php');
			} ?>
		</div>
		<map name="hotspots-image-<?php echo $settings['image_id']; ?>" class="hotspots-map">
			<?php
			$arrHref = array();
			foreach ($settings['hotspots'] as $key => $hotspot) : ?>
				<?php
				$coords = $hotspot['coordinates'];
				$target = !empty($hotspot['action']) ? $hotspot['action'] : '';
				$targetURL = get_post_type($hotspot['action']) == 'bv_image' ? 'url' : $hotspot['action'];
				$new_window = !empty($hotspot['action-url-open-in-window']) ? $hotspot['action-url-open-in-window'] : '';
				$target_window = $new_window == 'on' ? '_new' : '';
				$target_url = !empty($hotspot['action-url-url']) ? $hotspot['action-url-url'] : '';
				$$area_class = get_post_type($hotspot['action']) == 'bv_image' ? 'url-area' : 'more-info-area';
				$href = get_post_type($hotspot['action']) == 'bv_image' ? get_permalink($hotspot['action']) : '#hotspot-' . $settings['spot_id'] . '-' . $key;
				$href = !empty($href) ? $href : '#';
				$arrTarget = array(
					'id' => $target,
					'href' => $href
				);
				array_push($arrHref, $arrTarget);
				$title = !empty($hotspot['action']) ? 'Enhet ' . get_the_title($hotspot['action']) : '';
				if (empty($hotspot['description'])) {
					$hotspot['description'] = '';
				}
				if (empty($settings['img_settings']['_bv_has_multiple_styles']['0']) || $settings['img_settings']['_bv_has_multiple_styles']['0'] != 'on' || empty($hotspot['style'])) {
					$color_scheme = '';
				} else {
					$color_scheme = $hotspot['style'];
				}

				?>
				<area shape="poly" coords="<?php echo $coords; ?>" href="<?php echo $href; ?>" title="<?php echo $title; ?>" alt="<?php echo esc_attr($title); ?>" data-action="<?php echo $targetURL; ?>" data-color-scheme="<?php echo $color_scheme; ?>" target="<?php echo $target_window; ?>" class="<?php echo $area_class; ?>">
			<?php endforeach; ?>
		</map>

		<?php /* Error message for admins when there's a JS error */
		if (!empty($_GET['bv_debug'])) : ?>
			<div id="error-<?php echo $settings['spot_id']; ?>" class="da-error">
				<p>It looks like there is a JavaScript error in a plugin or theme that is causing a conflict with Draw Attention. For more information on troubleshooting this issue, please see our <a href="https://wpdrawattention.com/document/troubleshooting-conflicts-themes-plugins/" target="_new">help page</a>.
			</div>
		<?php endif; ?>

		<?php /* Loop through the hotspots and output the more info content for each */
		foreach ($settings['hotspots'] as $key => $hotspot) : ?>
			<?php if (empty($settings['img_settings']['_bv_has_multiple_styles']['0']) || $settings['img_settings']['_bv_has_multiple_styles']['0'] != 'on' || empty($hotspot['style'])) {
				$color_scheme_class = '';
			} else {
				$color_scheme_class = 'bv-style-' . $hotspot['style'];
			}

			if (empty($hotspot['title'])) {
				$hotspot['title'] = '';
			}

			?>
			<div class="hotspot-info <?php echo $color_scheme_class; ?>" id="hotspot-<?php echo $settings['spot_id']; ?>-<?php echo $key; ?>">
				<?php
				if (!empty($hotspot['action'])) {
					if ('bigcommerce' === $hotspot['action']) {
						echo DrawAttention_BigCommerce_Action::render_hotspot_content($hotspot, $settings);
						echo '</div>';
						continue;
					}
				}
				?>

				<?php echo apply_filters('boligvelger_hotspot_title', '<h2 class="bolig-velger-title"> Leilighet ' . get_the_title($hotspot['action']) . '</h2>', $hotspot); ?>
				<?php if (!empty($hotspot['detail_image_id'])) : ?>
					<div class="hotspot-thumb">
						<?php echo wp_get_attachment_image($hotspot['detail_image_id'], apply_filters('bv_detail_image_size', 'large', $hotspot, $settings['img_post'], $settings['img_settings'])); ?>
					</div>
				<?php elseif (empty($hotspot['detail_image_id']) && !empty($hotspot['detail_image'])) : ?>
					<div class="hotspot-thumb">
						<img src="<?php echo $hotspot['detail_image']; ?>">
					</div>
				<?php endif; ?>
				<div class="hotspot-content">

					<div class="aprt-content">
						<?php
						$image = get_the_post_thumbnail($hotspot['action']); ?>

						<div class="aprt-image">
							<?php echo $image; ?>
						</div>
						<div class="aprt-content-text">
							<?php
							if (!empty($hotspot['description'])) echo apply_filters('bv_description', do_shortcode($wp_embed->autoembed($wp_embed->run_shortcode($hotspot['description'])))); ?>
							<?php echo get_post_field('post_content', $hotspot['action']) ?>
							<!-- kode her for å fylle inn info om leiligheten! --->
							<?php
							// Grab the metadata from the database
							$status = get_post_meta($hotspot['action'], '_cmb2_leilighet_status', true);
							$nr = get_post_meta($hotspot['action'], '_cmb2_leilighet_nr', true);
							$floor = get_post_meta($hotspot['action'], '_cmb2_leilighet_etasje', true);
							$area = get_post_meta($hotspot['action'], '_cmb2_leilighet_bruttoareal', true);
							$rooms = get_post_meta($hotspot['action'], '_cmb2_leilighet_antall', true);
							$price = get_post_meta($hotspot['action'], '_cmb2_leilighet_pris', true);
							?>

						</div>
					</div>
					<div class="aprt-content-info">
						<span class="nr">Leilighet: <?php echo $nr ?></span>
						<span class="nr">Brutto areal: <?php echo $area ?></span>
						<span class="floor">Etasje: <?php echo $floor ?></span>
						<span class="rooms">Antall rom: <?php echo $rooms ?></span>
						<span class="price">Pris: <?php echo number_format($price, 0, ".", " "); ?></span>
						<span class="status">Status: <?php echo $status ?></span>

						<script>
							var nyEl = {};
							nyEl['id'] = '<?php echo $hotspot['action'] ?>';
							nyEl['status'] = '<?php echo $status ?>';
							carName.push(nyEl);
						</script>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		<div class="aprt-list">
			<table id="aprt-info">
				<tr>
					<th id="aprt-nr" onclick="sortTable(0)">Leilighet <span id="arrowTh">&#9662;</span></th>
					<th id="aprt-area" onclick="sortTable(1)">Brutto areal <span id="arrowTh"></span></th>
					<th id="aprt-floor" onclick="sortTable(2)">Etasje <span id="arrowTh"></span></th>
					<th id="aprt-rooms" onclick="sortTable(3)">Ant. rom <span id="arrowTh"></span></th>
					<th id="aprt-price" onclick="sortTable(4)">Pris <span id="arrowTh"></span></th>
					<th>Status</th>
				</tr>
				<?php
				if (null !== get_post_meta(get_the_ID(), 'blokk_velger', true)) {
					$aprtm = new WP_Query(array(
						'post_type' => 'leilighet',
						'orderby' => 'title',
						'order' => 'ASC',
						'cat' => get_post_meta(get_the_ID(), 'blokk_velger', true)
					));
				} else {
					$aprtm = new WP_Query(array(
						'post_type' => 'leilighet',
						'orderby' => 'title',
						'order' => 'ASC'
					));
				}

				while ($aprtm->have_posts()) {
					$aprtm->the_post();
					$status = get_post_meta(get_the_ID(), '_cmb2_leilighet_status', true);
					$nr = get_post_meta(get_the_ID(), '_cmb2_leilighet_nr', true);
					$floor = get_post_meta(get_the_ID(), '_cmb2_leilighet_etasje', true);
					$area = get_post_meta(get_the_ID(), '_cmb2_leilighet_bruttoareal', true);
					$rooms = get_post_meta(get_the_ID(), '_cmb2_leilighet_antall', true);
					$price = get_post_meta(get_the_ID(), '_cmb2_leilighet_pris', true);

					number_format($price, 2, ".", " ");



					foreach ($arrHref as $href => $key) {
						if ($key['id'] == get_the_ID()) {
							$href = $key['href'];
							$target2 = $key['id']; ?>

							<tr class="aprt-row" data-href="<?php echo $href; ?>" data-target="<?php echo $target2; ?>">
								<td><?php echo the_title(); ?></td>
								<td><?php echo $area; ?></td>
								<td><?php echo $floor; ?></td>
								<td><?php echo $rooms; ?></td>
								<td><?php echo number_format($price, 0, ".", " "); ?></td>
								<td><?php echo $status; ?></td>
							</tr>
				<?php
						}
					}
				}
				?>
			</table>

		</div>
	</div>

<?php endif; ?>