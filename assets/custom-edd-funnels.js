Vue.component('custom-edd-funneler-metabox', {
	data: function (){
		return {
			enable_funneler: false,
			funnel_option: '',
			selected_funnels: [],
			downloads: [],
			pages: [],
			meta: [],
			available_funnel_option: ['Page', 'Bump Item', 'Upsells', 'Modal']
		}
	},
	methods: {
		funneler_enable_handler: function (){

		},
		check_options: function () {
			var available_funnels = ['Page', 'Bump Item', 'Upsells', 'Modal'];
			var funnels = this.selected_funnels;
			if (funnels.length >0 ) {
				for(var i=0; i<funnels.length; i++) {
					for (var j in funnels[i]) {
						if (j == 'bump') {
							var index = available_funnels.indexOf('Bump Item');
							if (index !== -1) {
								available_funnels.splice(index,1);	
							}
						}
						if (j == 'page') {
							var index = available_funnels.indexOf('Page');
							if (index !== -1) {
								available_funnels.splice(index,1);	
							}
						}
						if (j == 'modal') {
							var index = available_funnels.indexOf('Modal');
							if (index !== -1) {
								available_funnels.splice(index,1);	
							}
						}
						if (j == 'upsells') {
							var index = available_funnels.indexOf('Upsells');
							if (index !== -1) {
								available_funnels.splice(index,1);	
							}
						}
					}
				}
				this.available_funnel_option = available_funnels;
			}
			
		},
		add_new_funnel_option: function () {	
			var type = this.funnel_option;
			var funnel_items = {};
			if (type == 'Page') {
				funnel_items.page = '';
			} else if(type == 'Bump Item') {
				funnel_items.bump = '';
			} else if(type == 'Upsells') {
				funnel_items.upsells = [];
			} else if (type == 'Modal') {
				funnel_items.modal = '';
			}
			this.selected_funnels.push(funnel_items);
			this.check_options();
		},
		removeFunnel: function (index) {
			this.selected_funnels.splice(index, 1);
			this.check_options();
		},
		update_meta_ui: function (meta) {
			if (meta.status == 'enable') {
				this.enable_funneler = true;
				
				for (var i in meta) {
					var funnel_items = {};	
					if (meta[i]!= undefined && meta[i]['page']) {
						funnel_items.page = meta[i]['page'];
					}
					if (meta[i]!= undefined && meta[i]['bump']) {
						funnel_items.bump = meta[i]['bump'];
					}
					if (meta[i]!= undefined && meta[i]['upsells']) {
						funnel_items.upsells = meta[i]['upsells'];
					}
					if (meta[i]!= undefined && meta[i]['modal']) {
						funnel_items.modal = meta[i]['modal'];
					}
					if (Object.keys(funnel_items).length > 0) {
						this.selected_funnels.push(funnel_items);	
					}
					this.check_options();
				}
			}
		},
		get_pages: function () {
			var that = this;
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: "json",
				data: {action:'custom_edd_funnel_get_pages', postId: edd_vars.post_id},
				complete: function(res) {
					if ( res.status === 200 ) {
						that.downloads = res.responseJSON.downloads;
						that.pages = res.responseJSON.pages;
						that.meta = res.responseJSON.meta;
						that.update_meta_ui(res.responseJSON.meta);
					} else {
						
					}
					jQuery('select').select2();

				}
			});
		}
	},
	created: function() {
		this.get_pages();

		jQuery('select').select2();
	}

});
var vm = new Vue({
	el: '#custom-edd-funnels-settings',
	created: function() {

	}
} );