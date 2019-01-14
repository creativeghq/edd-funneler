<?php 
	$query = new WP_Query(array(
		'post_type'		=> 'download',
		'post__in'		=> eddfunnels_set( $step, 'object_id' )
	));

?>


<div class="edd-funnel-bump-item">
	<strong><?php esc_html_e( 'You may alse like to buy. Please check the below item to add to cart.', 'edd-funnels' ); ?></strong>
</div>
<?php while( $query->have_posts() ) : $query->the_post(); ?>
	<div class="edd-funnel-bump-item">
		<?php $uniqid = uniqid() ?>
		<div id="edd_funnels_add_to_cart_status"></div>
		<div class="edd-funnel-item">
			<div class="edd-funnel-checkbox">
				<input type="checkbox" class="edd-funnels-add-to-cart" data-id="<?php the_ID() ?>" id="edd-funnel-checkbox-<?php echo esc_attr( $uniqid ) ?>">
			</div>
			<div class="edd-funnel-item-detail">
				<h5>
					<a href="<?php echo esc_url( get_permalink()) ?>" target="_blank">
						<?php the_title() ?>
					</a>
				</h5>
				<p><?php echo wp_trim_words( get_the_content(), 20 ) ?></p>
			</div>
		</div>
	</div>
<?php endwhile; ?>

<?php wp_reset_postdata() ?>
