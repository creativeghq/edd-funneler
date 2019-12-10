<?php if ($bump!=''):?>
<?php 
	$cart = edd_get_cart_contents(); 
	$data = get_post($bump);
	$exists = false;
	foreach($cart as $cart_item) {
		if ($cart_item['id'] == $data->ID) {
			$exists = true;
			break;
		}
	}
	$edd_price = edd_price( $data->ID, false );
	if ($exists == false):	
	?>
	<div class="edd-funnel-bump-item">
		<strong>You may alse like to buy. Please check the below item to add to cart</strong>
	</div>
	<table>
		<tbody>
			
			<tr>
				<td>
					<input type="checkbox" class="edd-funnels-add-to-cart bump" data-id="<?php echo $data->ID; ?>" name="" value="<?php echo $data->ID; ?>" class="cef_upsell">
				</td>
				<td><?php echo $data->post_title; ?></td>
				<td><?php echo $edd_price; ?></td>
				<td><?php echo wp_trim_words($data->post_content, 20); ?></td>
			</tr>
		</tbody>
	</table>
	<?php endif; ?>
<?php endif;?>