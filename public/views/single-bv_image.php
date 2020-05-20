<?php

/**
 * The template for displaying a preview of the interactive image.
 * Copy this file into your theme to customize it for your specific project
 */

get_header();

?>


<div id="primary" class="site-content">
	<div id="content" role="main">
		<script>
			var carName = new Array();
		</script>
		<?php while (have_posts()) : the_post(); ?>
			<?php echo do_shortcode('[boligvelger ID="' . get_the_id() . '"]'); ?>
		<?php endwhile; // end of the loop. 
		?>
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
				$aprtm = new WP_Query(array(
					'post_type' => 'leilighet',
					'orderby' => 'title',
					'order' => 'ASC'
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

					require_once(__DIR__ . '/shortcode_template.php');

				?>
					<tr data-action="43" class="aprt-row">
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

<script>
	function sortTable(n) {
		let table, rows, switching, i, x, y, shouldSwitch, dir, arrow, switchcount = 0;
		table = document.getElementById("aprt-info");
		arrowInner = document.querySelector("#arrowTh");
		arrowASC = "&#9662;";
		arrowDESC = "&#9652;";
		switching = true;
		// Sorting direction set to ascending:
		dir = "asc";
		// Loop that will continue until no switching has been done:
		while (switching) {
			// Start by saying: no switching is done:
			switching = false;
			rows = table.rows;
			//Loop through all table rows:
			for (i = 1; i < (rows.length - 1); i++) {
				shouldSwitch = false;
				// Compare rows. Current and next:
				x = rows[i].getElementsByTagName("TD")[n];
				y = rows[i + 1].getElementsByTagName("TD")[n];
				/* Check if the two rows should switch place,
				based on the direction, asc or desc: */
				if (dir == "asc") {
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase() || Number(x.innerHTML.toLowerCase()) > Number(y.innerHTML.toLowerCase())) {
						shouldSwitch = true;
						arrowInner.innerHTML = arrowASC;
						break;
					}
				} else if (dir == "desc") {
					if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase() || Number(x.innerHTML.toLowerCase()) < Number(y.innerHTML.toLowerCase())) {
						shouldSwitch = true;
						arrowInner.innerHTML = arrowDESC;

						break;
					}
				}
			}
			if (shouldSwitch) {
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				switching = true;
				switchcount++;
			} else {
				if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				}
			}
		}
	}
</script>

<?php get_footer(); ?>