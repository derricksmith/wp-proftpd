<input type="hidden" name="wp-proftpd-settings[page]" value='ftps' />   
<div style="margin: 10px;">
	<h5><?php esc_attr_e('FTPS Settings', 'wp-proftpd' ); ?></h5>
	<fieldset>
		<h6><?php esc_attr_e( 'Login Display', 'wp-proftpd' ); ?></h6>
		<legend class="screen-reader-text">
			<span><?php esc_attr_e( 'Login Display', 'wp-proftpd' ); ?></span>
		</legend>

		<textarea class="" cols="90" rows="10" name="wp-proftpd-settings[ftps_display_login]"><?php echo $options['ftps_display_login']; ?></textarea>     
	</fieldset>
</div>