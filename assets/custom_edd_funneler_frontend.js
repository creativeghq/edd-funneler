function upsell_add_to_cart(original_checkout_page)
{
	var selected = [];
	jQuery('.edd-funnels-add-to-cart').each(function() {
		if (jQuery(this).is(":checked")) {
			selected.push(jQuery(this).val());
		}
	})	

	jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "json",
            data: {
                action: 'custom_edd_funneler_add_upsell',
                items: selected,
                type: 'upsell'
            },
            complete: function (res) {
            	if (res.status === 200) {
            	 	window.location.href = original_checkout_page;
            	}
            }
		});
}

function redirect_to_page_analytics(page)
{
    jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: "json",
            data: {
                action: 'redirect_to_page_analytics',
                items: page,
                type: 'page'
            },
            complete: function (res) {
            
            }
        });
}

jQuery(document).ready(function() {
    jQuery('.edd-add-to-cart').each(function() {
        jQuery(this).click(function() {
            var that = this;
            var original_checkout_page = jQuery(that).siblings('.edd_go_to_checkout').attr('href');

            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: "json",
                data: {
                    action: 'custom_edd_get_funnel_detail',
                    postId: jQuery(that).attr('data-download-id')
                },
                complete: function(res) {
                    if (res.status === 200) {
                        var redirect_page = res.responseJSON.redirect_page
                        var meta = res.responseJSON.meta;
                        var modal = res.responseJSON.modal;
                        var upsell = res.responseJSON.upsell;
                        if (redirect_page != '') {
                            // send for analytics 
                            redirect_to_page_analytics(redirect_page);

                            jQuery(that).siblings('.edd_go_to_checkout').attr('href', redirect_page);
                        } else if (modal != '') {
                        	jQuery(that).siblings('.edd_go_to_checkout').attr('href', 'javascript:void(0);');
                        	jQuery(that).siblings('.edd_go_to_checkout').click(function() {
                        		jQuery('#custom_edd_funneler_modalbox').show();
                        		jQuery('#cefm_modal_body').html(modal);
	                        	jQuery('.cefm_modal_continue_link').attr('href', original_checkout_page);	
                        	})
                        	
                        } else if (upsell != '') {
                        	jQuery(that).siblings('.edd_go_to_checkout').attr('href', 'javascript:void(0);');
                        	jQuery(that).siblings('.edd_go_to_checkout').click(function() {
                        		jQuery('#cefm_modal_body').html(upsell);
	                        	jQuery('#custom_edd_funneler_modalbox').show();
	                        	jQuery('.cefm_modal_continue_link').attr('href', "javascript:void(0);");
	                        	jQuery('.cefm_modal_continue_link').click(function() {
	                        		upsell_add_to_cart(original_checkout_page);
	                        	})	

                        	})
                        	
                        }
                    } else {

                    }
                }
            });
        })

        jQuery('.edd_cancel_close').bind('click', function() {
            jQuery('#custom_edd_funneler_modalbox').hide();
        })
    })

    jQuery('.edd-funnels-add-to-cart.bump').bind('click',function () {
    	if (jQuery(this).is(':checked')) {
    		var selected = [];
    		selected.push(jQuery(this).val());
    		jQuery.ajax({
	            url: ajaxurl,
	            type: 'POST',
	            dataType: "json",
	            data: {
	                action: 'custom_edd_funneler_add_upsell',
	                items: selected,
                    type: 'bump'
	            },
	            complete: function (res) {
	            	if (res.status === 200) {
	            	 	window.location.reload();
	            	}
	            }
			});
    	}
    })

})