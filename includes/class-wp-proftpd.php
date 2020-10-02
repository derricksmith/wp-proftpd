<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://derrick-smith.com
 * @since      1.0.0
 *
 * @package    Wp_Proftpd
 * @subpackage Wp_Proftpd/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Proftpd
 * @subpackage Wp_Proftpd/includes
 * @author     Derrick Smith <derricksmith01@msn.com>
 */
class Wp_Proftpd {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Proftpd_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_PROFTPD_VERSION' ) ) {
			$this->version = WP_PROFTPD_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-proftpd';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Proftpd_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Proftpd_i18n. Defines internationalization functionality.
	 * - Wp_Proftpd_Admin. Defines all hooks for the admin area.
	 * - Wp_Proftpd_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-proftpd-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-proftpd-i18n.php';
		
		/**
		 * The class responsible for defining all actions that occur in the plugin settings area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-proftpd-admin-settings.php';
		
		/**
		 * The class responsible for defining all actions that occur in the plugin user area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-proftpd-user-settings.php';
		
		/**
		 * The class responsible for defining all actions that occur in the plugin logs area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-proftpd-admin-logs.php';
		
		/**
		 * The class responsible for defining all actions that occur in the plugin dashboard area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-proftpd-admin-dashboard.php';
		
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-proftpd-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-proftpd-public.php';

		$this->loader = new Wp_Proftpd_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Proftpd_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Proftpd_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin_settings = new Wp_Proftpd_Admin_Settings( $this->get_plugin_name(), $this->get_version() );
		$plugin_user_settings = new Wp_Proftpd_User_Settings( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_dashboard = new Wp_Proftpd_Admin_Dashboard( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_logs = new Wp_Proftpd_Admin_Logs( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin = new Wp_Proftpd_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		//Plugin Dependencies
		$this->loader->add_action( 'admin_init', $plugin_admin, 'check_plugin_requirements' );
		
		//Menus and Options Page
		$this->loader->add_action('admin_init', $plugin_admin_settings, 'options_update');
		$this->loader->add_action('admin_menu', $plugin_admin_settings, 'add_plugin_admin_menu' );
		$this->loader->add_filter('plugin_action_links_' . plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' ), $plugin_admin_settings, 'add_action_links' );
		$this->loader->add_action('parent_file', $plugin_admin_settings, 'keep_menu_open');
		$this->loader->add_action('submenu_file', $plugin_admin_settings, 'highlight_menu_item');
		$this->loader->add_action('admin_notices', $plugin_admin_settings, 'general_admin_notice');
		
		
		//User Settings
		$this->loader->add_action( 'show_user_profile', $plugin_user_settings, 'display_user_profile_fields' );
		$this->loader->add_action( 'edit_user_profile', $plugin_user_settings, 'display_user_profile_fields' );
		$this->loader->add_action( 'personal_options_update', $plugin_user_settings, 'save_user_profile_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_user_settings, 'save_user_profile_fields' );
		
		//Dashboard
		
		  //Login Chart
		$this->loader->add_action('wp_ajax_proftpd_login_chart', $plugin_admin_dashboard,'chartjs_login_chart_callback');
		$this->loader->add_action('wp_ajax_nopriv_proftpd_login_chart', $plugin_admin_dashboard, 'chartjs_login_chart_callback');
		  //Operations Chart
		$this->loader->add_action('wp_ajax_proftpd_operations_chart', $plugin_admin_dashboard,'chartjs_operations_chart_callback');
		$this->loader->add_action('wp_ajax_nopriv_proftpd_operations_chart', $plugin_admin_dashboard, 'chartjs_operations_chart_callback');
		  //Activity Chart
		$this->loader->add_action('wp_ajax_proftpd_activity_chart', $plugin_admin_dashboard,'chartjs_activity_chart_callback');
		$this->loader->add_action('wp_ajax_nopriv_proftpd_activity_chart', $plugin_admin_dashboard, 'chartjs_activity_chart_callback');
		
		//Logs
		$this->loader->add_action('wp_ajax_proftpd_logs_load', $plugin_admin_logs,'datatables_load_callback');
		$this->loader->add_action('wp_ajax_nopriv_proftpd_logs_load', $plugin_admin_logs, 'datatables_load_callback');
		$this->loader->add_action('wp_ajax_proftpd_logs_clear', $plugin_admin_logs,'datatables_clear_callback');
		$this->loader->add_action('wp_ajax_nopriv_proftpd_logs_clear', $plugin_admin_logs, 'datatables_clear_callback');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Proftpd_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Proftpd_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
