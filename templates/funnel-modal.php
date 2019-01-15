<?php $uniqid = uniqid(); ?>
<?php $is_last = false;//(EDD_Funnels_Loader::is_last_step() ) ? true : false; ?>
<div class="modal" tabindex="-1" role="dialog" id="<?php echo esc_attr($uniqid) ?>">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php esc_html_e( 'Promotion', 'edd-funnels' ) ?></h5>
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

	$js = 'jQuery(document).ready(function($){
		$("edd-funnels-doing-modal-'.$uniqid.'").modal("show");

		$("edd-funnels-doing-modal-'.$uniqid.'").on("hide.bs.modal", function(e){
			$.ajax({
				url: edd_funnels_data.ajaxurl,
				type: \'POST\',
				data: {action: \'edd_funnels_ajax\', subaction: \'funnel_modal_event\', nonce: edd_funnels_data.nonce, ajax: true, last_step: '.$is_last.'},
				complete: function(res) {
					if(res.status === 200) {
						var json = res.responseJSON;
						
					} else {
						window.location = edd_funnels_data.checkout_url;
					}
				}
			});
		});
	});';

	//wp_add_inline_script( 'edd-funnels-frontend', $js );
?>