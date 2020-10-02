<?php

/**
 * Fired during plugin activation
 *
 * @link       https://derrick-smith.com
 * @since      1.0.0
 *
 * @package    Wp_Proftpd
 * @subpackage Wp_Proftpd/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Proftpd
 * @subpackage Wp_Proftpd/includes
 * @author     Derrick Smith <derricksmith01@msn.com>
 */
class Wp_Proftpd_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$wpdb->show_errors(); 
		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();
		
		//Create SQL Log Table for ProFTPd
		$sql  = "CREATE TABLE IF NOT EXISTS  " . $wpdb->prefix . "proftpd_logs (
			logdatetime datetime DEFAULT CURRENT_TIMESTAMP,
			ip varchar(255) default NULL,
			username varchar(255) default NULL,
			operation text default NULL,
			PRIMARY KEY  (logdatetime)
		)";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		//Create SQL Log Table for ProFTPd - Not used currently but must exist for ProFTPd
		$sql  = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "proftpd_groups (
			groupname varchar(255) COLLATE utf8_general_ci NOT NULL,
			gid smallint(6) NOT NULL DEFAULT '5500',
			members varchar(16) COLLATE utf8_general_ci NOT NULL,
			PRIMARY KEY  (groupname)
		)";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		// Drop SPROC for ProFTPd get ftp user by username if exists
		$sql  = "
			DROP PROCEDURE IF EXISTS {$wpdb->prefix}proftpd_get_ftp_user_by_username;
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		// Create SPROC for ProFTPd get ftp user by username
		$sql  = "
			CREATE PROCEDURE {$wpdb->prefix}proftpd_get_ftp_user_by_username
			(
				IN username VARCHAR(60),
				IN type VARCHAR(5)
			)
			BEGIN
				CREATE TEMPORARY TABLE tmp(
					ID int(11) NOT NULL,
					user_login varchar(60) default NULL,
					user_pass varchar(60) default NULL,
					uid int(11) default NULL,
					gid int(11) default NULL,
					homedir varchar(255) default NULL,
					shell varchar(255) default NULL,
					ftp int(1) default NULL,
					ftps int(1) default NULL,
					sftp int(1) default NULL,
					enabled int(1) default NULL
				) engine=memory
					SELECT
						u.ID,
						u.user_login as userid,
						u.user_pass as passwd,
						Min( CASE WHEN m.meta_key = 'wp-proftpd-uid' THEN m.meta_value ELSE NULL End ) AS uid,
						Min( CASE WHEN m.meta_key = 'wp-proftpd-gid' THEN m.meta_value ELSE NULL End ) AS gid,
						Min( CASE WHEN m.meta_key = 'wp-proftpd-home-directory' THEN m.meta_value ELSE NULL End ) AS homedir,
						Min( CASE WHEN m.meta_key = 'wp-proftpd-shell' THEN m.meta_value ELSE NULL End ) AS shell,
						Min( CASE WHEN m.meta_key = 'wp-proftpd-ftp' THEN m.meta_value ELSE NULL End ) AS ftp,
						Min( CASE WHEN m.meta_key = 'wp-proftpd-ftps' THEN m.meta_value ELSE NULL End ) AS ftps,
						Min( CASE WHEN m.meta_key = 'wp-proftpd-sftp' THEN m.meta_value ELSE NULL End ) AS sftp,
						Min( CASE WHEN m.meta_key = 'wp-proftpd-enabled' THEN m.meta_value ELSE NULL End ) AS enabled
					FROM wp_users u 
					JOIN wp_usermeta AS m ON m.user_id = u.ID;
					
				SELECT userid, passwd, uid, gid, homedir, shell
				FROM tmp
				WHERE userid = username COLLATE utf8mb4_unicode_520_ci 
					AND enabled = 1 
					AND CASE 
						WHEN type = 'ftp' THEN ftp = 1
						WHEN type = 'ftps' THEN ftps = 1
						WHEN type = 'sftp' THEN sftp = 1
						END;

				DROP TEMPORARY TABLE IF EXISTS tmp;
				
			END
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		//Drop SPROC for ProFTPd update count if exists
		$sql = "
			DROP PROCEDURE IF EXISTS {$wpdb->prefix}proftpd_update_count;
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		//Create SPROC for ProFTPd update count
		$sql = "
			CREATE PROCEDURE {$wpdb->prefix}proftpd_update_count
			(
				IN username VARCHAR(60)
			)
			BEGIN
				DECLARE userid INT(11);
				DECLARE usercount INT(11);
				DECLARE useraccessed VARCHAR(255);
				
				SELECT id INTO userid
				FROM {$wpdb->prefix}users 
				WHERE user_login = username COLLATE utf8mb4_unicode_520_ci;
				
				SELECT CAST(meta_value as UNSIGNED) INTO usercount
				FROM {$wpdb->prefix}usermeta
				WHERE meta_key = 'wp-proftpd-count'
				AND user_id = userid;
				
				IF usercount IS NULL OR usercount='' THEN
					INSERT INTO {$wpdb->prefix}usermeta (umeta_id, user_id, meta_key, meta_value)
					VALUES ('', userid, 'wp-proftpd-count', '1');
				ELSE
					UPDATE {$wpdb->prefix}usermeta
					SET meta_value = usercount + 1 
					WHERE meta_key = 'wp-proftpd-count' COLLATE utf8mb4_unicode_520_ci 
					AND user_id = userid;
				END IF;
				
				SELECT meta_value INTO useraccessed
				FROM {$wpdb->prefix}usermeta
				WHERE meta_key = 'wp-proftpd-last-accessed'
				AND user_id = userid;
				
				IF useraccessed IS NULL OR useraccessed='' THEN
					INSERT INTO {$wpdb->prefix}usermeta (umeta_id, user_id, meta_key, meta_value)
					VALUES ('', userid, 'wp-proftpd-last-accessed', NOW());
				ELSE
					UPDATE {$wpdb->prefix}usermeta
					SET meta_value = NOW() 
					WHERE meta_key = 'wp-proftpd-last-accessed' COLLATE utf8mb4_unicode_520_ci 
					AND user_id = userid;
				END IF;
			END
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		//Drop SPROC for ProFTPd insert log if exists
		$sql = "
			DROP procedure IF EXISTS {$wpdb->prefix}proftpd_insert_log;
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		//Create SPROC for ProFTPd insert log
		$sql = "
			CREATE PROCEDURE {$wpdb->prefix}proftpd_insert_log
			(
				IN varip VARCHAR(60),
				IN varusername VARCHAR(60),
				IN varoperation TEXT
			)
			BEGIN
				INSERT INTO {$wpdb->prefix}proftpd_logs (logdatetime, ip, username, operation) VALUES (NOW(), varip, varusername, varoperation);
			END
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		//Drop SPROC for ProFTPd get display login if exists
		$sql = "
			DROP procedure IF EXISTS {$wpdb->prefix}proftpd_get_variable;
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		//Create SPROC for ProFTPd insert log
		$sql = "
			CREATE PROCEDURE {$wpdb->prefix}proftpd_get_variable
			(
				IN variable VARCHAR(60)
			)
			BEGIN
				DECLARE options text DEFAULT '';
				
				SELECT option_value
				INTO options
				FROM {$wpdb->prefix}options 
				WHERE option_name = 'wp-proftpd-settings';
				
				CALL {$wpdb->prefix}proftpd_get_serialized_value_by_key(options, variable);
				
			END
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		//Drop Function for ProFTPd unserialize
		$sql = "
			DROP PROCEDURE IF EXISTS {$wpdb->prefix}proftpd_get_serialized_value_by_key;
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
		//Create Function for ProFTPd Unserialize
		$sql = "
			CREATE PROCEDURE {$wpdb->prefix}proftpd_get_serialized_value_by_key
			(
				IN _input_string TEXT, 
				IN _key TEXT
			
			) 
			BEGIN
				/*
					Function returns last value from serialized array by specific string key.
					
					@author Adam Wnęk (http://kredyty-chwilowki.pl/)
					@licence MIT
					@version 1.2
				*/
				-- required variables
				DECLARE __output_part,__output,__extra_byte_counter,__extra_byte_number,__value_type,__array_part_temp TEXT;
				DECLARE __value_length,__char_ord,__start,__char_counter,__non_multibyte_length,__array_close_bracket_counter,__array_open_bracket_counter INT SIGNED;
				SET __output := NULL;
				
				-- check if key exists in input
				IF LOCATE(CONCAT('s:',LENGTH(_key),':\"',_key,'\";'), _input_string) != 0 THEN
				
					-- cut from right to key		
					SET __output_part := SUBSTRING_INDEX(_input_string,CONCAT('s:',LENGTH(_key),':\"',_key,'\";'),-1);
					
					-- get type of value [s,a,b,O,i,d]
					SET __value_type := SUBSTRING(SUBSTRING(__output_part, 1, CHAR_LENGTH(SUBSTRING_INDEX(__output_part,';',1))), 1, 1);
					
					-- custom cut depends of value type
					CASE 	
					WHEN __value_type = 'a' THEN
						-- we get proper array by counting open and close brackets
						SET __array_open_bracket_counter := 1;
						SET __array_close_bracket_counter := 0;
						-- without first open { so counter is 1
						SET __array_part_temp := SUBSTRING(__output_part FROM LOCATE('{',__output_part)+1);
						
						-- we start from first { and counting open and closet brackets until we find last closing one
						WHILE (__array_open_bracket_counter > 0 OR LENGTH(__array_part_temp) = 0) DO
							-- next { exists and its before closest }
							IF LOCATE('{',__array_part_temp) > 0 AND (LOCATE('{',__array_part_temp) < LOCATE('}',__array_part_temp)) THEN
								-- cut from found { + 1, to the end
								SET __array_open_bracket_counter := __array_open_bracket_counter + 1;
								SET __array_part_temp := SUBSTRING(__array_part_temp FROM LOCATE('{',__array_part_temp) + 1);					
							ELSE
								-- cut from found } + 1, to the end
								SET __array_open_bracket_counter := __array_open_bracket_counter - 1;
								SET __array_close_bracket_counter := __array_close_bracket_counter + 1;
								SET __array_part_temp := SUBSTRING(__array_part_temp FROM LOCATE('}',__array_part_temp) + 1);					
							END IF;
						END WHILE;
						-- final array is from beginning to [__array_close_bracket_counter] count of closing }
						SET __output := CONCAT(SUBSTRING_INDEX(__output_part,'}',__array_close_bracket_counter),'}');
						
					WHEN __value_type = 'd' OR __value_type = 'i' OR __value_type = 'b' THEN
						
						-- from left to first appearance of }, from right to first :
						SET __output := SUBSTRING_INDEX(SUBSTRING_INDEX(__output_part,';',1),':',-1);
						
					WHEN __value_type = 'O' THEN			
						
						-- from left to first appearance of ;} but without it so we add it back
						SET __output := CONCAT(SUBSTRING_INDEX(__output_part,';}',1),';}');
						
					WHEN __value_type = 'N' THEN 
						-- when we have null return empty string
						SET __output := NULL;		
					ELSE
						
						-- get serialized length
						SET __value_length := SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(__output_part, ':', 2),':',-1),';',1);
									
						SET __output_part := SUBSTRING(__output_part, 5+LENGTH(__value_length));
						
						SET __char_counter := 1;
						
						-- real length to cut
						SET __non_multibyte_length := 0;
						
						SET __start := 0;
						-- check every char until [__value_length]
						WHILE __start < __value_length DO
						
							SET __char_ord := ORD(SUBSTR(__output_part,__char_counter,1));
							
							SET __extra_byte_number := 0;
							SET __extra_byte_counter := FLOOR(__char_ord / 256);
							
							-- we detect multibytechars and count them as one to substring correctly
							-- when we now how many chars make multibytechar we can use it to count what is non multibyte length of our value
							WHILE __extra_byte_counter > 0 DO
								SET __extra_byte_counter := FLOOR(__extra_byte_counter / 256);
								SET __extra_byte_number := __extra_byte_number+1;
							END WHILE;
							
							-- to every char i add extra multibyte number (for non multibyte char its 0)
							SET __start := __start + 1 + __extra_byte_number;			
							SET __char_counter := __char_counter + 1;
							SET __non_multibyte_length := __non_multibyte_length +1;
											
						END WHILE;
						
						SET __output :=  SUBSTRING(__output_part,1,__non_multibyte_length);
								
					END CASE;		
				END IF;
				SELECT __output;
				END
		";
		$results = $wpdb->query($sql);
		Wp_Proftpd_Activator::get_last_error();
		
	}
	
	public static function get_last_error(){
		global $wpdb;
		if($wpdb->last_error !== '') :
			$wpdb->print_error();
			die();
		endif;
	}
	
}
