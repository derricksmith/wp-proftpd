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
class Wp_Proftpd_Admin_Dashboard {

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
	
	public function chartjs_login_chart_callback() {
		global $wpdb;
		$request= $_GET;
		$nonce = $request['proftpd_nonce'];
		if (!wp_verify_nonce( $nonce, "proftpd_login_chart" ))
			die( __( 'Security check', 'wp-proftpd' ) ); 
		
		
		$users = get_users(array('meta_key' => 'wp-proftpd-enabled', 'meta_value' => '1'));
		$data = array();
		foreach($users as $user){
			$count = get_user_meta($user->ID, 'wp-proftpd-count', true);
			$data[] = array('user_login' => $user->user_login, 'count' => $count);
		}
		
		wp_send_json_success($data);
	}
	
	public function chartjs_operations_chart_callback() {
		global $wpdb;
		$request= $_GET;
		$nonce = $request['proftpd_nonce'];
		if (!wp_verify_nonce( $nonce, "proftpd_operations_chart" ))
			die( __( 'Security check', 'wp-proftpd' ) ); 
		
		$user = $request['proftpd_user'];
		
		if (isset($user) && $user != ''){
			$data = 'empty';
		} else {
			$users = get_users(array('meta_key' => 'wp-proftpd-enabled', 'meta_value' => '1'));
			$data = array();
			foreach($users as $user){
				$count = get_user_meta($user->ID, 'wp-proftpd-count', true);
				$operations = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "proftpd_logs WHERE username = %s", $user->user_login) );
				$data[] = array('user_login' => $user->user_login, 'operations' => $operations);
			}
		}
		wp_send_json_success($data);
	}
	
	public function chartjs_activity_chart_callback() {
		global $wpdb;
		$request= $_GET;
		$nonce = $request['proftpd_nonce'];
		if (!wp_verify_nonce( $nonce, "proftpd_activity_chart" ))
			die( __( 'Security check', 'wp-proftpd' ) ); 
		
		$timeframe = (isset($request['proftpd_timeframe']) && $request['proftpd_timeframe'] != '' ? $request['proftpd_timeframe'] : '1 year');
		$start = date('Y-m-d H:i:s', strtotime("-$timeframe"));
	
		$activities = $wpdb->get_results( $wpdb->prepare("SELECT DATE(logdatetime) as dt, COUNT(*) AS operations FROM ". $wpdb->prefix . "proftpd_logs WHERE logdatetime IS NOT NULL AND logdatetime > %s GROUP BY dt ORDER BY dt ASC", $start) );
		$data = array();
		foreach($activities as $activity){
			$data[] = array('date' => $activity->dt, 'operations' => $activity->operations);
		}
		
		wp_send_json_success($data);
	}
}