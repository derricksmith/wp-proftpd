<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://derrick-smith.com
 * @since      1.0.0
 *
 * @package    Wp_Proftpd
 * @subpackage Wp_Proftpd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Proftpd
 * @subpackage Wp_Proftpd/admin
 * @author     Derrick Smith <derricksmith01@msn.com>
 */
class Wp_Proftpd_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Proftpd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Proftpd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$current_page = get_current_screen();
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-proftpd-admin.css', array(), $this->version, 'all' );
		
		if ($current_page->base == 'toplevel_page_wp-proftpd' || $current_page->base == 'wp-proftpd_page_wp-proftpd-logs'|| $current_page->base == 'wp-proftpd_page_wp-proftpd-settings'){
			wp_register_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
			wp_enqueue_style('bootstrap');
		}
		if ($current_page->base == 'wp-proftpd_page_wp-proftpd-settings'){
			wp_register_style('bootstrap_toggle', 'https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css');
			wp_enqueue_style('bootstrap_toggle');
			
			wp_enqueue_style( 'fontAwesome', 'https://use.fontawesome.com/releases/v5.0.13/css/all.css', array(), null);
		}
		if ($current_page->base == 'wp-proftpd_page_wp-proftpd-logs'){
			wp_register_style('datatables', 'https://cdn.datatables.net/v/bs4-4.1.1/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-colvis-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/datatables.min.css');
			wp_enqueue_style('datatables');
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Proftpd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Proftpd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$current_page = get_current_screen();
		$dependancies = array( 'jquery' );
		
		$wp_proftpd = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'plugin_url' => plugin_dir_url( __FILE__ ),
			'textdomain' => array ()
		);
		
		if ($current_page->base == 'toplevel_page_wp-proftpd' || $current_page->base == 'wp-proftpd_page_wp-proftpd-logs'|| $current_page->base == 'wp-proftpd_page_wp-proftpd-settings'){
			wp_register_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), true);
			wp_enqueue_script('bootstrap');
		}
		
		if ($current_page->base == 'wp-proftpd_page_wp-proftpd-settings'){
			wp_register_script('popper', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js', array('jquery', 'bootstrap'), true);
			wp_enqueue_script('popper');
			
			wp_register_script('bootstrap_toggle', 'https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js', array('jquery', 'bootstrap'), true);
			wp_enqueue_script('bootstrap_toggle');
		}
		
		if ($current_page->base == 'wp-proftpd_page_wp-proftpd-logs'){
			$wp_proftpd['ajax_url_logs_load'] = admin_url( 'admin-ajax.php?action=proftpd_logs_load&proftpd_nonce='.wp_create_nonce( 'proftpd_logs') );
			$wp_proftpd['ajax_url_logs_clear'] = admin_url( 'admin-ajax.php?action=proftpd_logs_clear&proftpd_nonce='.wp_create_nonce( 'proftpd_logs_clear') );
			
			wp_register_script('datatables_pdfmake', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js', array('jquery', 'datatables'), true);
			wp_enqueue_script('datatables_pdfmake');
			
			wp_register_script('datatables_vfs_fonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js', array('jquery', 'datatables'), true);
			wp_enqueue_script('datatables_vfs_fonts');
			
			wp_register_script('datatables', 'https://cdn.datatables.net/v/bs4-4.1.1/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-colvis-1.6.3/b-flash-1.6.3/b-html5-1.6.3/b-print-1.6.3/datatables.min.js', array('jquery'), true);
			wp_enqueue_script('datatables');
		}
		
		if ($current_page->base == 'toplevel_page_wp-proftpd'){
			$wp_proftpd['ajax_url_login_chart'] = admin_url( 'admin-ajax.php?action=proftpd_login_chart&proftpd_nonce='.wp_create_nonce( 'proftpd_login_chart') );
			$wp_proftpd['ajax_url_operations_chart'] = admin_url( 'admin-ajax.php?action=proftpd_operations_chart&proftpd_nonce='.wp_create_nonce( 'proftpd_operations_chart') );
			$wp_proftpd['ajax_url_activity_chart'] = admin_url( 'admin-ajax.php?action=proftpd_activity_chart&proftpd_nonce='.wp_create_nonce( 'proftpd_activity_chart') );
			
			wp_enqueue_script( 'chart-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js', array(), true );
			$dependancies[] = 'chart-js';
		}
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-proftpd-admin.js', $dependancies, $this->version, false );
		wp_localize_script( $this->plugin_name, 'wp_proftp', $wp_proftpd);
	}
	
	/**
	 * Checks for dependancies.
	 */
	public function check_plugin_requirements() {
		if ( !function_exists( 'is_plugin_active_for_network' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		$cannot_activate = FALSE;
		if ( current_user_can( 'activate_plugins' ) && !$this->is_mu_active( 'wp-password-bcrypt.php' ) ) {
			$cannot_activate = TRUE;
			add_action( 'admin_notices', array($this, 'wp_password_bcrypt_plugin_notice') );	
		}
		if ($cannot_activate === TRUE) {
			add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' ), array($this, 'remove_action_links' ));
			add_action( 'admin_menu', array($this, 'remove_menu_pages'));
			deactivate_plugins( 'wp-proftpd/wp-proftpd.php' ); 
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
	
	/**
	 * Checks for mu-plugin.
	 */
	public function is_mu_active( $plugin_main_file ) {
		$_mu_plugins = get_mu_plugins();

		if ( isset( $_mu_plugins[ $plugin_main_file ] ) ) {
			return true;
		}

		return false;
	}
	
	/**
	 * Returns error message for WP Password bcrypt dependancy.
	 */
	public function wp_password_bcrypt_plugin_notice(){
		echo '<div class="error"><p>Sorry, but WP Proftpd requires WP Password bcrypt to be installed and active.</p></div>';
	}
	
	/**
	 * Remove settings action link from the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function remove_action_links( $links ) {
		
		/*
		*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		*/
	   $settings_link = array(
	    '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '-admin-dashboard">' . __( 'Dashboard', $this->plugin_name ) . '</a>',
		'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '-admin-settings">' . __( 'Settings', $this->plugin_name ) . '</a>',
		'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '-admin-logs">' . __( 'Logs', $this->plugin_name ) . '</a>',
	   );
	   
	   foreach ($settings_link as $link){
		 if (($key = array_search($link, $links)) !== false) {
			unset($links[$key]);
		}  
	   }
	   return $links;

	}
	
	public function remove_menu_pages(){
		remove_menu_page( 'wp-proftpd' );
		remove_submenu_page( 'wp-proftpd', 'wp-proftpd-settings' );
		remove_submenu_page( 'wp-proftpd', 'wp-proftpd-logs' );
	}
}
