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

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php esc_attr_e('WP ProFTPd Dashboard', 'wp-proftpd' ); ?></h2>
	
	<div class="row">
		<div class="col-md-8">
			<div class="row">
				<div class="col-md-6">
					<div class="" style="background:white; padding:20px; border:1px solid #b4b4b4; border-radius:5px; margin-bottom:20px;">
						<h4>User Logins</h4>
						<hr />
						<div class="chart-container">
							<canvas id="proftpd_login_chart"></canvas>
						</div>
					</div>
					
				</div>
				<div class="col-md-6">
					<div class="" style="background:white; padding:20px; border:1px solid #b4b4b4; border-radius:5px; margin-bottom:20px;">	
						<h4>Operations</h4>
						<hr />
						<div class="chart-container">
							<canvas id="proftpd_operations_chart"></canvas>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="" style="background:white; padding:20px; border:1px solid #b4b4b4; border-radius:5px; margin-bottom:20px;">	
						<h4>All Activity</h4>
						<hr />
						<div class="chart-container">
							<canvas id="proftpd_activity_chart"></canvas>
						</div>
					</div>
				</div>
				
			</div>
		</div>
		
		<?php include 'wp-proftpd-admin-sidebar.php'; ?>

	</div>
    
</div>