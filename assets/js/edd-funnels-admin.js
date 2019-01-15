Vue.component('edd-funnels-metabox', {
	props: {
		nonce: String
	},
	data: function(){
		return {
			enabled: false,
			selectedopt : '',
			funnels: [],
			comps: [],
			pages: [],
			downloads: [],
			meta: {},
			bump_added: false,
			upsells_added: false,
		}
	},
	mounted: function() {
		this.init()
	},
	methods: {
		init() {

			//this.initSortabel()

			let thisis = this

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {action:'edd_funnels_ajax', subaction: 'pages_downloads_meta', id: edd_vars.post_id, nonce: thisis.nonce },
				complete: function(res) {
					if ( res.status === 200 ) {
						thisis.pages = res.responseJSON.pages
						thisis.downloads = res.responseJSON.downloads
						thisis.meta = res.responseJSON.meta
						thisis.enabled = res.responseJSON.status
						thisis.process_meta()
					} else {
						if ( res.responseJSON.message !== undefined ) {
							alert(res.responseJSON.message);
						}
					}
				}
			});
		},
		add_new: function() {
			if ( this.selectedopt === '' ) {
				alert('Choose the Funnel and click add new');
				return;
			}
			let selectedopt = this.selectedopt

			if ( selectedopt == 'bump' ) {
				if ( this.bump_added ) {
					alert("You can add bump item once");
					return;
				}
				this.funnels.push(selectedopt)
				this.comps.push({is: 'downloads_list', props: {title: 'Select Download', tag: selectedopt}, object_id: null})
				this.bump_added = true
				
			} else if(selectedopt === 'upsells' ) {
				if ( this.upsells_added ) {
					alert("You can add upsells item once");
					return;
				}
				this.funnels.push(selectedopt)
				this.comps.push({is: 'downloads_multi_list', props: {title: 'Select Downloads', tag: selectedopt}, object_id: []})
				this.upsells_added = true
				setTimeout(function(){
					$('.chosen').chosen();
				}, 500);
			} else {
				this.funnels.push(selectedopt)
				this.comps.push({is: 'modal_html', props: {title: 'Select Page', tag: selectedopt}, object_id: null})
			}
			this.initSortabel()
		},
		set_value: function(value) {
			this.selectedopt = value;
		},
		initSortabel() {
			let start = 0;
			let thisis = this
			$('#dyanmic-funnels').sortable({
				items: '.edd-funnels-section',
				handle: ".ui-icon-arrowthick-2-n-s",
				placeholder: "sortable-placeholder",
				forcePlaceholderSize: true,
				start: function(event, ui) {
					start = ui.item.index()
				},
				update: function( event, ui ) {
					let res = thisis.array_move(thisis.comps, start, ui.item.index());
					console.log(res);
				}
			})
		},
		array_move(arr, old_index, new_index) {
		    if (new_index >= arr.length) {
		        var k = new_index - arr.length + 1;
		        while (k--) {
		            arr.push(undefined);
		        }
		    }
		    arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
		    return arr; // for testing
		},
		remove(index, type) {
			if ( confirm('Are you sure want to remove ?') ) {
				this.comps.splice(index,1);

				if ( type === 'bump' ) {
					this.bump_added = false
				}
				if ( type == 'upsells' ) {
					this.upsells_added = false
				}
			}
		},
		process_meta() {
			let thisis = this
			this.meta.forEach(function(elem){
				if ( elem.type == 'bump' ) {
					if ( thisis.bump_added ) {
						alert("You can add bump item once");
					} else {
						thisis.funnels.push(elem.type)
						thisis.comps.push({is: 'downloads_list', props: {title: 'Select Download', tag: elem.type}, object_id: elem.object_id})
						thisis.bump_added = true
					}
					
				} else if(elem.type === 'upsells' ) {
					if ( thisis.upsells_added ) {
						alert("You can add upsells item once");
						return;
					}
					thisis.funnels.push(elem.type)
					thisis.comps.push({is: 'downloads_multi_list', props: {title: 'Select Downloads', tag: elem.type}, object_id: elem.object_id})
					thisis.upsells_added = true
					setTimeout(function(){
						$('.chosen').chosen();
					}, 2000); 
				}  else {
					thisis.funnels.push(elem.type)
					thisis.comps.push({is: 'modal_html', props: {title: 'Select Page', tag: elem.type}, object_id: elem.object_id})
				}
			})

			setTimeout(function(){
				thisis.initSortabel();
			}, 1000);
			
		}
	}
});

Vue.component('modal_html', {
	props: {
		title: String,
		tag: String
	},
	template: '<div class="edd-funnels-section ui-state-default">\
						\<slot></slot>\
						\<div class="input-group">\
							\<slot name="pages"></slot>\
						\</div>\
					\</div>'
});

Vue.component('downloads_list', {
	props: {
		title: String,
		tag: String
	},
	template: '<div class="edd-funnels-section ui-state-default">\
						\<slot></slot>\
						\<div class="input-group">\
							\<slot name="downloads"></slot>\
						\</div>\
					\</div>'
})

Vue.component('downloads_multi_list', {
	props: {
		title: String,
		tag: String
	},
	template: '<div class="edd-funnels-section ui-state-default">\
						\<slot></slot>\
						\<div class="input-group">\
							\<slot name="multi_downloads"></slot>\
						\</div>\
					\</div>'
})


var vm = new Vue({
	el: '#edd-funnels-settings',
	created: function() {

	}
} );