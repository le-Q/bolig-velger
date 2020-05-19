<?php

/**
 * The template for displaying a preview of the interactive image.
 * Copy this file into your theme to customize it for your specific project
 */

get_header();

?>

<div id="primary" class="site-content">
	<div id="content" role="main">

		<?php while (have_posts()) : the_post(); ?>
			<?php echo do_shortcode('[boligvelger ID="' . get_the_id() . '"]'); ?>
			<?php if (current_user_can('edit_others_posts')) : ?>
				<?php edit_post_link(__('Edit Interactive Image', 'bolig-velger')); ?>
			<?php endif ?>
		<?php endwhile; // end of the loop. 
		?>
		<div class="aprt-list">
			<table>
				<tr>
					<th>Leilighet</th>
					<th>Brutto areal</th>
					<th>Etasje</th>
					<th>Ant. rom</th>
					<th>Pris</th>
					<th>Status</th>
				</tr>
				<?php
				$aprtm = new WP_Query(array(
					'post_type' => 'leilighet'
				));

				while ($aprtm->have_posts()) {
					$aprtm->the_post();
					$status = get_post_meta(get_the_ID(), '_cmb2_leilighet_status', true);
					$nr = get_post_meta(get_the_ID(), '_cmb2_leilighet_nr', true);
					$floor = get_post_meta(get_the_ID(), '_cmb2_leilighet_etasje', true);
					$area = get_post_meta(get_the_ID(), '_cmb2_leilighet_bruttoareal', true);
					$rooms = get_post_meta(get_the_ID(), '_cmb2_leilighet_antall', true);
					$price = get_post_meta(get_the_ID(), '_cmb2_leilighet_pris', true);

					number_format($price, 2, ".", " ");
				?>

					<tr class="aprt-row">
						<td><?php echo the_title(); ?></td>
						<td><?php echo $area; ?></td>
						<td><?php echo $floor; ?></td>
						<td><?php echo $rooms; ?></td>
						<td><?php echo number_format($price, 0, ".", " "); ?></td>
						<td><?php echo $status; ?></td>
					</tr>
				<?php
				}
				?>
			</table>

		</div>
	</div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>