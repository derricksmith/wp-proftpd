<input type="hidden" name="wp-proftpd-settings[page]" value='general' />   
<div style="margin: 10px;">
	<h5><?php esc_attr_e('Global Settings', 'wp-proftpd' ); ?></h5>
	<fieldset>
		<h6><?php esc_attr_e( 'Use IPv6', 'wp-proftpd' ); ?></h6>
		<legend class="screen-reader-text">
			<span><?php esc_attr_e( 'Use IPv6', 'wp-proftpd' ); ?></span>
		</legend>
		<input type="checkbox" name="wp-proftpd-settings[ipv6]" data-toggle="toggle" data-onstyle="primary" <?php checked($options['use_ipv6']); ?> />
	</fieldset>
							
	<fieldset>
		<h6><?php esc_attr_e( 'Max Instances', 'wp-proftpd' ); ?></h6>
		<legend class="screen-reader-text">
			<span><?php esc_attr_e( 'Max Instances', 'wp-proftpd' ); ?></span>
		</legend>
		<button class='down_count btn btn-primary button-counter' title='Down'><i class='fas fa-minus'></i></button>
		<input class='counter max-instances' type="text" value="<?php echo $options['max_instances']; ?>" name="wp-proftpd-settings[max_instances]" />    
		<button class='up_count btn btn-primary button-counter' title='Up'><i class='fas fa-plus'></i></button>
	</fieldset>
							
	<h5><?php esc_attr_e('Virtualhost Settings', 'wp-proftpd' ); ?></h5>
	<fieldset>
		<h6><?php esc_attr_e( 'Enabled FTP Servers', 'wp-proftpd' ); ?></h6>
		<legend class="screen-reader-text">
			<span><?php esc_attr_e( 'Enabled FTP Servers', 'wp-proftpd' ); ?></span>
		</legend>				
		<input type="checkbox" name="wp-proftpd-settings[ftp_enabled]" data-toggle="toggle" data-onstyle="primary" data-on="FTP<br>On" data-off="FTP<br>Off" <?php checked($options['ftp_enabled']); ?> />
		<input type="checkbox" name="wp-proftpd-settings[sftp_enabled]" data-toggle="toggle" data-onstyle="primary" data-on="SFTP<br>On" data-off="SFTP<br>Off" <?php checked($options['sftp_enabled']); ?> />
		<input type="checkbox" name="wp-proftpd-settings[ftps_enabled]" data-toggle="toggle" data-onstyle="primary" data-on="FTPS<br>On" data-off="FTPS<br>Off" <?php checked($options['ftps_enabled']); ?> />
								
		<p><span class="small">
			<?php esc_attr_e( 'FTP - Standard FTP (unsecured)', 'wp-proftpd' ); ?> 
			<br />
			<?php esc_attr_e( 'SFTP - FTP over SSH (secured)', 'wp-proftpd' ); ?> 
			<br />
			<?php esc_attr_e( 'FTPS - FTP over implicit TLS (secured)', 'wp-proftpd' ); ?>
			</span>
		</p>
	</fieldset>
</div>