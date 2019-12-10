<?php $uniqid = uniqid(); ?>
<?php $is_last = false;//(EDD_Funnels_Loader::is_last_step() ) ? true : false; ?>
<div class="modal" tabindex="-1" role="dialog" id="edd_custom_funnel_modal_<?php echo esc_attr($uniqid) ?>">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Promotion</h5>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
				<div class="edd-funnels-steps-buttons">
					<a href="javascript:void(0);" class="edd-funnels-btn-next btn btn-primary"><?php esc_html_e( 'Next', 'edd-funnels' ); ?></a><span class="edd-loading-ajax edd-loading hide"></span>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	wp_enqueue_script('bootstrap');
?>
<script type="text/javascript">
	jQuery(document).ready(functoin($) {
		var uniqueid = '<?php echo $uniqid;?>';
		$('#edd_custom_funnel_modal_'+uniqueid).modal('show');
		$('#edd_custom_funnel_modal_'+uniqueid).on('hide.bs.modal', function (e){
			$.ajax({
				url: edd_funnels_data.ajaxurl,
				type: 'POST',
				data: {
					action: edd_custom_funnel_modal
				},
				complete: function (res) {
					if (res.status == 200) {
						var json = res.responseJSON;
					} else {
						window.location.href = edd_funnels_data.checkout_url;
					}
				}
			})
		});
	});
</script>