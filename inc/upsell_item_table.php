<div class="edd-funnel-bump-item">
	<strong>You may alse like to buy. Please check the below item and click continue to add to cart</strong>
</div>
<table>
	<tbody>
		<?php 
		$cart = edd_get_cart_contents(); 
		foreach ($upsells as $upsell) :
			$data = get_post($upsell);
			$exists = false;
			foreach($cart as $cart_item) {
				if ($cart_item['id'] == $data->ID) {
					$exists = true;
					break;
				}
			}

			$edd_price = edd_price( $data->ID, false );
		?>
		<tr>
			<td>
				<input type="checkbox" class="edd-funnels-add-to-cart" data-id="<?php echo $data->ID; ?>" name="" value="<?php echo $data->ID; ?>" class="cef_upsell" <?php if ($exists) echo 'checked="checked"'; ?>>
			</td>
			<td><?php echo $data->post_title; ?></td>
			<td><?php echo $edd_price ?></td>
			<td><?php echo wp_trim_words($data->post_content, 20); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>