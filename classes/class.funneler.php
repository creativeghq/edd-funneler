<?php
class Custom_EDD_Funnels_Funneler
{
	public function __construct()
	{

		add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend'),1);

		add_action('wp_ajax_custom_edd_get_funnel_detail', array($this, 'ajax_get_funnel_detail'));
        add_action('wp_ajax_nopriv_custom_edd_get_funnel_detail', array($this, 'ajax_get_funnel_detail'));

        add_action('wp_ajax_custom_edd_funneler_add_upsell', array($this, 'ajax_add_upsell'));
        add_action('wp_ajax_nopriv_custom_edd_funneler_add_upsell', array($this, 'ajax_add_upsell'));

        add_action('wp_ajax_redirect_to_page_analytics', array($this, 'ajax_page_analytics'));
        add_action('wp_ajax_nopriv_redirect_to_page_analytics', array($this, 'ajax_page_analytics'));

        add_action('edd_purchase_form_before_cc_form', array($this, 'checkout_bump') ,10, 1);

        add_action('wp_footer', array($this, 'add_modal_box'));

        // add_action('edd_pre_process_purchase', array($this, 'init'));
        // add_action('edd_empty_cart', array($this, 'on_empty_cart' ) );

	}

	public function ajax_page_analytics()
	{
		global $wpdb;
		$type = $_POST['type'];
		$array = array();
		$array['user_id'] = get_current_user_id();
		$array['funneler_used'] = $type;
		$array['funneler_used_products'] = $_POST['items'];
		$array['created'] = date('Y-m-d H:i:s');
		$array['modified'] = date('Y-m-d H:i:s');
		$wpdb->insert($wpdb->prefix."edd_custom_funneler", $array);

	}

	public function checkout_bump()
	{
		$cart = edd_get_cart_contents();
		$bump = '';
		if ($cart[0]['id']) {
			//getting the bump item on the cart
			$meta = (array) get_post_meta( $cart[0]['id'], '_custom_edd_funneler_data', true );

			if ($meta) {
				if($meta['status'] == 'enable') {

					foreach($meta as $key=>$m) {
						if ($key != 'status' && array_key_exists('bump', $m)) {
							$bump = $m['bump'];
							break;
						}
						
					}
				}
			}
		}

		echo $this->render('bump_item_table', array('bump'=>$bump));die;
	}

	public function ajax_add_upsell()
	{
		$items_to_purchase = $_POST['items'];
		$type = $_POST['type'];
		if ($items_to_purchase) {
			global $wpdb;
			foreach($items_to_purchase as $items) {
				$download_id = $items;
				$options = array();
				$options['quantity'] = 1;
				edd_add_to_cart($download_id, $options);		

				//inserting in funneler table
				$array = array();
				$array['user_id'] = get_current_user_id();
				$array['funneler_used'] = $type;
				$array['funneler_used_products'] = $download_id;
				$array['created'] = date('Y-m-d H:i:s');
				$array['modified'] = date('Y-m-d H:i:s');
				$wpdb->insert($wpdb->prefix."edd_custom_funneler", $array);
			
			}
		}
		wp_send_json(array('status'=>true));	

	} 

	public function add_modal_box()
	{
		include CEDDF_PATH . 'inc/custom_edd_funneler_modalbox.php';	
	}

	private function generate_upsell_html($data) 
	{
		$upsell = $data;
		return $this->render('upsell_item_table', array('upsells'=>$upsell));
		
	}

	public static function render($view, $data = null)
    {
        // Handle data
        ($data) ? extract($data) : null;
        ob_start();
        include plugin_dir_path(__FILE__) . '../inc/' . $view . '.php';
        $view = ob_get_contents();
        ob_end_clean();

        return $view;
    }

	public function ajax_get_funnel_detail()
	{
		$download_id = $_POST['postId'];
		$meta = (array) get_post_meta( $download_id, '_custom_edd_funneler_data', true );
		$page = '';
		$upsell = '';
		$modal = '';
		if ($meta) {
			if($meta['status'] == 'enable') {
				foreach($meta as $m) {
					if (array_key_exists('page', $m)) {
						$page = get_post($m['page']);
						$page = $page->guid;
					}
					if (array_key_exists('upsells', $m)) {
						$upsell = $this->generate_upsell_html($m['upsells']);
					}
					if (array_key_exists('modal', $m)) {
						$modal =  $m['modal'];
					}
				}
			}
		}
		wp_send_json(array('status'=>true, 'redirect_page'=>$page,'modal'=>$modal, 'upsell'=>$upsell, 'meta'=>$meta));

	}

	public function after_add_to_cart($download_id) 
	{
		$meta = (array) get_post_meta( $download_id, '_custom_edd_funneler_data', true );
		if ($meta) {
			if($meta['status'] == 'enable') {
				foreach($meta as $m) {
					if (array_key_exists('page', $m)) {
						$page = get_post($m['page']);
						exit(wp_redirect($page->guid));
					}
				}
			}
		}
	}

	public function enqueue_frontend($page)
	{
		wp_enqueue_script(
		    'jquery');
		wp_enqueue_style('custom_edd_funneler_frontend_css', plugins_url('../assets/custom-edd-frontend-funnels.css', __FILE__));
		wp_enqueue_script('custom_edd_funneler_frontend', plugins_url('../assets/custom_edd_funneler_frontend.js', __FILE__));
		
	}


	public function modal_content()
	{
		include CEDDF_PATH . 'inc/funnel_modalbox.php';	
	}
}
new Custom_EDD_Funnels_Funneler();
