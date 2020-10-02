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
class WP_Proftpd_User_Settings {

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
	
	function save_user_profile_fields( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
		
		$enabled = (isset($_POST['wp-proftpd-enabled']) && $_POST['wp-proftpd-enabled'] != '' ? esc_attr($_POST['wp-proftpd-enabled']) : '');
		$uid = (isset($_POST['wp-proftpd-uid']) && $_POST['wp-proftpd-uid'] != '' ? esc_attr($_POST['wp-proftpd-uid']) : '');
		$gid = (isset($_POST['wp-proftpd-gid']) && $_POST['wp-proftpd-gid'] != '' ? esc_attr($_POST['wp-proftpd-gid']) : '');
		$home_directory = (isset($_POST['wp-proftpd-home-directory']) && $_POST['wp-proftpd-home-directory'] != '' ? esc_attr($_POST['wp-proftpd-home-directory']) : '');
		$shell = (isset($_POST['wp-proftpd-shell']) && $_POST['wp-proftpd-shell'] != '' ? esc_attr($_POST['wp-proftpd-shell']) : '');
		$ftp = (isset($_POST['wp-proftpd-ftp']) && $_POST['wp-proftpd-ftp'] != '' ? esc_attr($_POST['wp-proftpd-ftp']) : '');
		$ftps = (isset($_POST['wp-proftpd-ftps']) && $_POST['wp-proftpd-ftps'] != '' ? esc_attr($_POST['wp-proftpd-ftps']) : '');
		$sftp = (isset($_POST['wp-proftpd-sftp']) && $_POST['wp-proftpd-sftp'] != '' ? esc_attr($_POST['wp-proftpd-sftp']) : '');
		$count = (isset($_POST['wp-proftpd-count']) && $_POST['wp-proftpd-count'] != '' ? esc_attr($_POST['wp-proftpd-count']) : '');
		$last_accessed = (isset($_POST['wp-proftpd-last-accessed']) && $_POST['wp-proftpd-last-accessed'] != '' ? esc_attr($_POST['wp-proftpd-last-accessed']) : '');
		$last_modified = (isset($_POST['wp-proftpd-last-modified']) && $_POST['wp-proftpd-last-modified'] != '' ? esc_attr($_POST['wp-proftpd-last-modified']) : '');
		
		update_user_meta($user_id, 'wp-proftpd-enabled', $enabled);
		update_user_meta($user_id, 'wp-proftpd-uid', $uid);
		update_user_meta($user_id, 'wp-proftpd-gid', $gid);
		update_user_meta($user_id, 'wp-proftpd-home-directory', $home_directory);
		update_user_meta($user_id, 'wp-proftpd-shell', $shell);
		update_user_meta($user_id, 'wp-proftpd-ftp', $ftp);
		update_user_meta($user_id, 'wp-proftpd-ftps', $ftps);
		update_user_meta($user_id, 'wp-proftpd-sftp', $sftp);
	}
	
	function display_user_profile_fields( $user ) { 
		$enabled = get_user_meta($user->ID, 'wp-proftpd-enabled', true);
		$uid = get_user_meta($user->ID, 'wp-proftpd-uid', true);
		$gid = get_user_meta($user->ID, 'wp-proftpd-gid', true);
		$home_directory = get_user_meta($user->ID, 'wp-proftpd-home-directory', true);
		$shell = get_user_meta($user->ID, 'wp-proftpd-shell', true);
		$ftp = get_user_meta($user->ID, 'wp-proftpd-ftp', true);
		$ftps = get_user_meta($user->ID, 'wp-proftpd-ftps', true);
		$sftp = get_user_meta($user->ID, 'wp-proftpd-sftp', true);
		$count = get_user_meta($user->ID, 'wp-proftpd-count', true);
		$last_accessed = get_user_meta($user->ID, 'wp-proftpd-last-accessed', true);
		$last_modified = get_user_meta($user->ID, 'wp-proftpd-last-modified', true);
		?>
		<h2>ProFTPd</h2>
		<table class="form-table">
			<tr>
				<th><label for="<?php echo $this->plugin_name; ?>-enabled">Account Enabled</label></th>
				<td><input type="checkbox" id="<?php echo $this->plugin_name; ?>-enabled" name="<?php echo $this->plugin_name; ?>-enabled" value="1" <?php checked( (isset($enabled) && $enabled != '' ? $enabled : ''), 1 ); ?> /></td>
			</tr>
			<tr>
				<th><label for="<?php echo $this->plugin_name; ?>-enabled">FTP Access</label></th>
				<td>
					<p><input type="checkbox" id="<?php echo $this->plugin_name; ?>-ftp" name="<?php echo $this->plugin_name; ?>-ftp" value="1" <?php checked( (isset($ftp) && $ftp != '' ? $ftp : ''), 1 ); ?> /> FTP</p>
					<p><input type="checkbox" id="<?php echo $this->plugin_name; ?>-sftp" name="<?php echo $this->plugin_name; ?>-sftp" value="1" <?php checked( (isset($sftp) && $sftp != '' ? $sftp : ''), 1 ); ?> /> SFTP</p>
					<p><input type="checkbox" id="<?php echo $this->plugin_name; ?>-ftps" name="<?php echo $this->plugin_name; ?>-ftps" value="1" <?php checked( (isset($ftps) && $ftps != '' ? $ftps : ''), 1 ); ?> /> FTPS</p>
				</td>
			</tr>
			<tr>
				<th><label for="<?php echo $this->plugin_name; ?>-uid">UID</label></th>
				<td><input type="text" id="<?php echo $this->plugin_name; ?>-uid" name="<?php echo $this->plugin_name; ?>-uid" value="<?php echo (isset($uid) && $uid != '' ? $uid : '') ?>" /></td>
			</tr>
			<tr>
				<th><label for="<?php echo $this->plugin_name; ?>-gid">GID</label></th>
				<td><input type="text" id="<?php echo $this->plugin_name; ?>-gid" name="<?php echo $this->plugin_name; ?>-gid" value="<?php echo (isset($gid) && $gid != '' ? $gid : '') ?>" /></td>
			</tr>
			<tr>
				<th><label for="<?php echo $this->plugin_name; ?>-home-directory">Home Directory</label></th>
				<td><input type="text" id="<?php echo $this->plugin_name; ?>-home-directory" name="<?php echo $this->plugin_name; ?>-home-directory" value="<?php echo (isset($home_directory) && $home_directory != '' ? $home_directory : '') ?>" /></td>
			</tr>
			<tr>
				<th><label for="<?php echo $this->plugin_name; ?>-shell">Shell</label></th>
				<td><input type="text" id="<?php echo $this->plugin_name; ?>-shell" name="<?php echo $this->plugin_name; ?>-shell" value="<?php echo (isset($shell) && $shell != '' ? $shell : '') ?>" /></td>
			</tr>
			<tr>
				<th><label for="<?php echo $this->plugin_name; ?>-logins">Logins</label></th>
				<td><i><?php echo (isset($count) && $count != '' ? $count : '0'); ?></i></td>
			</tr>
			<tr>
				<th><label for="<?php echo $this->plugin_name; ?>-last-accessed">Last Accessed</label></th>
				<td><i><?php echo (isset($last_accessed) && $last_accessed != '' ? date_i18n(get_option('date_format') . ' ' . get_option('time_format') . ' (P)', strtotime($last_accessed)) : 'Never'); ?></i></td>
			</tr>
			<tr>
				<th><label for="<?php echo $this->plugin_name; ?>-last-modified">Last Modified</label></th>
				<td><i><?php echo (isset($last_accessed) && $last_accessed != '' ? $last_accessed : 'Never'); ?></i></td>
			</tr>
		</table>
	<?php 
	}
	
}