jQuery(document).ready(function($){

	$('.edd-funnels-steps-buttons').on('click', '.edd-funnels-btn-next', function(e){

		e.preventDefault();

		$.ajax({
			url: edd_funnels_data.ajaxurl,
			type: 'POST',
			data: {action: 'edd_funnels_ajax', subaction: 'running_funnel', nonce: edd_funnels_data.nonce, ajax: true},
			complete: function(res) {
				if(res.status === 200) {
					console.log(res);
					var json = res.responseJSON;
					if(json.type === 'redirect') {
						if ( json.next_url !== undefined ) {
							window.location = json.next_url;
						}
					} else if ( json.type === 'show_modal' ) {
						var mod_id = $('.modal').attr('id');
						$('#'+mod_id).find('.modal-body').html(json.content);
						$('#'+mod_id).modal({
							backdrop: 'static',
						});
					}
				} else {
					//window.location = edd_funnels_data.checkout_url;
				}
			}
		});
	});

	$(document.body).on('edd_checkout_error', function(error, res){

		var url = new URL(window.location.href);
		var is_doing = url.searchParams.get('doing_funnel');
		/*console.log(is_doing);
		if ( is_doing === null ) {
			var newurl = url.searchParams.set('doing_funnel', 1);
			window.history.replaceState("", "", url.href);
		}
		console.log(res);*/
		if ( res.type !== undefined ) {
			if ( res.type === 'show_modal' ) {
				var mod_id = $('.modal').attr('id');
				$('#'+mod_id).find('.modal-body').html(res.content);
				$('#'+mod_id).modal({
					backdrop: 'static',
				});
			}
		}
	});

	$(document).on('change', '.edd-funnels-add-to-cart', function(e){

		e.preventDefault();

		var id = $(this).data('id'),
		notif_div = $(this).parents('.edd-funnel-bump-item').find('#edd_funnels_add_to_cart_status'),
		thisis = this;

		notif_div.html('').removeClass('alert alert-success');

		if ( $(this).is(':checked') ) {
			$.ajax({
				url: edd_funnels_data.ajaxurl,
				type: 'POST',
				data: {action: 'edd_funnels_ajax', subaction: 'add_bump_to_cart', nonce: edd_funnels_data.nonce, id: id},
				complete: function(res) {
					if(res.status === 200) {
						var json = res.responseJSON;
						if ( json.message !== undefined ) {
							notif_div
								.html(json.message)
								.addClass('alert alert-success');
						}
					} else {

					}
				}
			});
		} else {
			if ( confirm('Do you really want to remove item from cart ?') ) {
				$.ajax({
					url: edd_funnels_data.ajaxurl,
					type: 'POST',
					data: {action: 'edd_funnels_ajax', subaction: 'add_bump_remove_from_cart', nonce: edd_funnels_data.nonce, id:id},
					complete: function(res) {
						if(res.status === 200) {
							var json = res.responseJSON;
							if ( json.message !== undefined ) {
								notif_div
									.html(json.message)
									.addClass('alert alert-success');
							}
						} else {

						}
					}
				});
			}
		}
	});
});