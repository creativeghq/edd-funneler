<?php $uniqid = uniqid() ?>

<div class="edd-funnel-bump-item">
	<strong><?php esc_html_e( 'You may alse like to buy. Please check the below item to add to cart.', 'edd-funnels' ); ?></strong>
	<div id="edd_funnels_add_to_cart_status"></div>
	<div class="edd-funnel-item">
		<div class="edd-funnel-checkbox">
			<input type="checkbox" class="edd-funnels-add-to-cart" data-id="<?php echo esc_attr( $download->ID ) ?>" id="edd-funnel-checkbox-<?php echo esc_attr( $uniqid ) ?>">
		</div>
		<div class="edd-funnel-item-detail">
			<h5>
				<a href="<?php echo esc_url( get_permalink($download->ID)) ?>" target="_blank">
					<?php echo get_the_title($download->ID) ?>
				</a>
			</h5>
			<p><?php echo wp_trim_words( $download->post_content, 20 ) ?></p>
		</div>
	</div>
</div>



