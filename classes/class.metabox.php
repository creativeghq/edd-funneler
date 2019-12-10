<?php
class Custom_EDD_Funnels_Metabox
{

	public function __construct()
	{
		
		add_action('add_meta_boxes', array($this, 'register') );
		add_action('wp_ajax_custom_edd_funnel_get_pages', array($this, 'get_pages'));
        add_action('wp_ajax_nopriv_custom_edd_funnel_get_pages', array($this, 'get_pages'));
        add_action('publish_download', array($this, 'save_metabox'), 20, 2 );
	}

	public function eddfunnels_set( $var, $key, $def = '' ) 
	{
		if (is_array($var) && isset( $var[$key] ) ) {
			return $var[$key];
		} else if ( is_object($var) && isset($var->{$key} ) ) {
			return $var->{$key};
		}
		return $def;
	}

	public function save_metabox($post_id, $post = null)
	{

		$funnels = $this->eddfunnels_set( $_POST, 'custom_edd_funneler' );
 		if ( $funnels ) {
			update_post_meta( $post_id, '_custom_edd_funneler_data', $funnels );
		}
	}

	public function get_pages()
	{
		$pages = get_pages();	
		$args = array(
		    'post_type' => 'download',
		);
		$downloads = get_posts($args);

		$post_id = $_POST['postId'];

		$meta = (array) get_post_meta( $post_id, '_custom_edd_funneler_data', true );

		wp_send_json(array('status'=>true, 'pages'=>$pages,'downloads'=>$downloads, 'meta'=>$meta));
		
	}


	public function register()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin'));
		add_meta_box( 'custom-edd-funnels-settings', __( 'EDD Funnels Settings', 'edd-funnels' ), array($this, 'output'), 'download' );		
	}

	public function output()
	{
		$this->enqueue_admin();

		include CEDDF_PATH . 'inc/downloads_metabox.php';
	}

	public function render($view)
	{
		 // Handle data
        ($data) ? extract($data) : null;

        ob_start();
        include plugin_dir_path(__FILE__) . '../inc/' . $view . '.php';
        $view = ob_get_contents();
        ob_end_clean();

        return $view;
	}

	public function enqueue_admin()
	{
		global $post_type;

		if ( $post_type !== 'download' ) {
			return;
		}
		wp_enqueue_script( 'vue', 'https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.16/vue.js', array(), '2.5.16', true );
		
		wp_enqueue_script( 'custom-edd-funnels-admin', CEDDF_URL . 'assets/custom-edd-funnels.js', array('vue'), '1.1', true );

		wp_enqueue_style('custom_edd_jquery_select2_css','https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css');

		wp_enqueue_script('custom_edd_jquery_select2_js','https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js');


		wp_enqueue_style('custom_edd_funneler_admin_css',CEDDF_URL . 'assets/custom-edd-funnels.css');
		wp_enqueue_style( 'custom-edd-funnels-prettycheckbox', 'https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css');
	}
}
new Custom_EDD_Funnels_Metabox();

