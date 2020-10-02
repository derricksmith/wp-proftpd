<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://derrick-smith.com
 * @since      1.0.0
 *
 * @package    WP ProFTPd
 * @subpackage WP ProFTPd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP ProFTPd
 * @subpackage WP ProFTPd/admin
 * @author     Derrick Smith <derricksmith01@msn.com>
 */
class WP_Proftpd_Admin_Settings {

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
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_menu_page(__('WP ProFTPd', $this->plugin_name),  __('WP ProFTPd', $this->plugin_name), 'manage_options', 'wp-proftpd', array($this, 'display_plugin_setup_page') );
		add_submenu_page('wp-proftpd', __('Dashboard', $this->plugin_name), __('Dashboard', $this->plugin_name), 'manage_options', 'wp-proftpd', array($this, 'display_plugin_setup_page') );
		add_submenu_page('wp-proftpd', __('Settings', $this->plugin_name), __('Settings', $this->plugin_name), 'manage_options', $this->plugin_name .'-settings', array($this, 'display_plugin_setup_page') );
		add_submenu_page('wp-proftpd', __('Logs', $this->plugin_name), __('Logs', $this->plugin_name), 'manage_options', 'wp-proftpd-logs', array($this, 'display_plugin_setup_page') );
	}
	
	function keep_menu_open($parent_file) {
		global $current_screen;
		$post = $current_screen->post_type;
		$taxonomy = $current_screen->taxonomy;
		return $parent_file;
    }
	
	function highlight_menu_item($submenu_file) {
		global $current_screen;
		$post = $current_screen->post_type;
		$taxonomy = $current_screen->taxonomy;
		return $submenu_file;
    }

	 /**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		/*
		*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		*/
	   $settings_link = array(
	    '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '">' . __( 'Dashboard', $this->plugin_name ) . '</a>',
		'<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '-settings">' . __( 'Settings', $this->plugin_name ) . '</a>',
		'<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '-logs">' . __( 'Logs', $this->plugin_name ) . '</a>',
	   );
	   return array_merge(  $settings_link, $links );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		$tab = $_GET['page'];
		switch($tab){
			case 'wp-proftpd':
				include_once( 'partials/' . $this->plugin_name . '-admin-dashboard.php' );
				break;
			case 'wp-proftpd-settings':
				include_once( 'partials/' . $this->plugin_name . '-admin-settings.php' );
				break;
			case 'wp-proftpd-logs':
				include_once( 'partials/' . $this->plugin_name . '-admin-logs.php' );
				break;
			default:
				include_once( 'partials/' . $this->plugin_name . '-admin-dashboard.php' );
				break;
		}
	}

	/**
	 * Validate fields from admin area plugin settings form ('exopite-lazy-load-xt-admin-display.php')
	 * @param  mixed $input as field form settings form
	 * @return mixed as validated fields
	 */
	public function validate($input) {
		$options = get_option( $this->plugin_name . '-settings' );
		if(!is_array($options)) $options = array();
		if ($page = (isset($input['page']) && $input['page'] != '' ? $input['page'] : null)){
			switch($page){
				case 'general':
					$options['use_ipv6'] = (isset( $input['use_ipv6'] ) && ($input['use_ipv6'] = 'on') ? 1 : 0);
					$options['max_instances'] = (isset( $input['max_instances'] ) && ($input['max_instances'] != '') ? (int)$input['max_instances'] : 0);
					$options['ftp_enabled'] = (isset( $input['ftp_enabled'] ) && ($input['ftp_enabled'] = 'on') ? 1 : 0);
					$options['ftps_enabled'] = (isset( $input['ftps_enabled'] ) && ($input['ftps_enabled'] = 'on') ? 1 : 0);
					$options['sftp_enabled'] = (isset( $input['sftp_enabled'] ) && ($input['sftp_enabled'] = 'on') ? 1 : 0);
					break;
				case 'ftp':
					$options['ftp_display_login'] = (isset( $input['ftp_display_login'] ) && ($input['ftp_display_login'] != '') ? sanitize_textarea_field($input['ftp_display_login']) : '');
					break;
				case 'ftps':
					$options['ftps_display_login'] = (isset( $input['ftps_display_login'] ) && ($input['ftps_display_login'] != '') ? sanitize_textarea_field($input['ftps_display_login']) : '');
					break;
				case 'sftp':
					$options['sftp_display_login'] = (isset( $input['sftp_display_login'] ) && ($input['sftp_display_login'] != '') ? sanitize_textarea_field($input['sftp_display_login']) : '');
					break;
				
			}
		}
	
		
		//$options['example_checkbox'] = ( isset( $input['example_checkbox'] ) && ! empty( $input['example_checkbox'] ) ) ? 1 : 0;
		//$options['example_text'] = ( isset( $input['example_text'] ) && ! empty( $input['example_text'] ) ) ? esc_attr( $input['example_text'] ) : 'default';
		//$options['example_select'] = ( isset($input['example_select'] ) && ! empty( $input['example_select'] ) ) ? esc_attr($input['example_select']) : 1;

		return $options;

	}

	public function options_update() {
		register_setting( $this->plugin_name .'-settings', $this->plugin_name .'-settings', array('sanitize_callback' => array( $this, 'validate' )) );

	}
	
	public function check_proftpd_installed(){
		if (file_exists('/etc/proftpd/proftpd.conf')){
			return true;
		}
		return false;
	}
	
	public function check_proftpd_config(){
		
	}
	
	public function general_admin_notice(){
		global $pagenow;
		if ( $pagenow == 'admin.php' && $_GET['page'] == 'wp-proftpd' && !$this->check_proftpd_installed()){
			 echo '<div class="notice notice-error">
				 <p>ProFTPd is not installed.  Visit <a href="'. admin_url( "admin.php?page=" . $this->plugin_name . "-installation" ).'">here</a> to configure!</p>
			 </div>';
		}
		if ( $pagenow == 'admin.php' && $_GET['page'] == 'wp-proftpd-installation' && !$this->check_proftpd_installed()){
			 echo '<div class="notice notice-error">
				 <p>ProFTPd is not installed.  Configure ProFTPd server settings and follow the installation instructions below!</p>
			 </div>';
		}
	}
	
	
	
}