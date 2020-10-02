<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://derrick-smith.com
 * @since      1.0.0
 *
 * @package    Kloudnets
 * @subpackage Kloudnets/admin/partials
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

if(!$this->check_proftpd_installed()) return;
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php esc_attr_e('WP ProFTPd Logs', 'wp-proftpd' ); ?></h2>
	
	<div class="row">
		<div class="col-md-8">
			<div style="background:white; padding:20px; border:1px solid #b4b4b4; border-radius:5px; margin-bottom:20px;">
				<table id="proftpd_logs" class="table table-striped table-hover"> 
					<thead> 
						<tr> 
							<th>Datetime</th>
							<th>IP</th>
							<th>Userid</th> 
							<th>Operation</th> 
						</tr> 
					</thead> 
				</table> 
			</div>
		</div>

		<?php include 'wp-proftpd-admin-sidebar.php'; ?>

	</div>
</div>