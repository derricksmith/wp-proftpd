<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://derrick-smith.com
 * @since      1.0.0
 *
 * @package    Wp_Proftpd
 * @subpackage Wp_Proftpd/admin/partials
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

if(!$this->check_proftpd_installed()) return;

$active_tab = (isset($_GET[ 'tab' ]) && $_GET[ 'tab' ] != '' ? $_GET[ 'tab' ] : '');

$options = get_option( $this->plugin_name .'-settings' );
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2><?php esc_attr_e('WP ProFTPd Settings', 'wp-proftpd' ); ?></h2>
    <form method="post" action="options.php">
		<div class="row">
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-12">
						<div style="background:white; padding:20px; border:1px solid #b4b4b4; border-radius:5px; margin-bottom:20px;">
							<?php settings_errors(); ?>
							
							<h2 class="nav-tab-wrapper">
								<a href="?page=wp-proftpd-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' || $active_tab == '' ? 'nav-tab-active' : ''; ?>">General</a>
								<a href="?page=wp-proftpd-settings&tab=ftp" class="nav-tab <?php echo $active_tab == 'ftp' ? 'nav-tab-active' : ''; ?>">FTP</a>
								<a href="?page=wp-proftpd-settings&tab=sftp" class="nav-tab <?php echo $active_tab == 'sftp' ? 'nav-tab-active' : ''; ?>">SFTP</a>
								<a href="?page=wp-proftpd-settings&tab=ftps" class="nav-tab <?php echo $active_tab == 'ftps' ? 'nav-tab-active' : ''; ?>">FTPS</a>
							</h2>
							<form method="post" class="form-horizontal" name="<?php echo $this->plugin_name; ?>" action="options.php">
							
							<?php
								settings_fields( $this->plugin_name .'-settings' );

								switch($active_tab){
									case 'general':
										do_settings_sections( $this->plugin_name .'-general-settings' );
										include "wp-proftpd-admin-settings-general.php";
										break;
									case 'ftp':
										do_settings_sections( $this->plugin_name .'-ftp-settings' );
										include "wp-proftpd-admin-settings-ftp.php";
										break;
									case 'sftp':
										do_settings_sections( $this->plugin_name .'-sftp-settings' );
										include "wp-proftpd-admin-settings-sftp.php";
										break;
									case 'ftps':
										do_settings_sections( $this->plugin_name .'-ftps-settings' );
										include "wp-proftpd-admin-settings-ftps.php";
										break;
									default:
										do_settings_sections( $this->plugin_name .'-general-settings' );
										include "wp-proftpd-admin-settings-general.php";
										break;
								}
								submit_button( __( 'Save all changes', 'wp-proftpd' ), 'primary','submit', TRUE );
							?>
							</form>
						</div>
					</div>
				</div>
			</div>
			
			<?php include 'wp-proftpd-admin-sidebar.php'; ?>

		</div>
    </form>
</div>