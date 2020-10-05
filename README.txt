=== Plugin Name ===
Contributors: (derricksmith01)
Donate link: https://derrick-smith.com
Tags: ftp, authentication
Requires at least: 4.4
Tested up to: 5.1.6
Stable tag: 5.1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP ProFTPd helps Authenticate ProFTPd users to FTP, SFTP, FTPS sites using the WordPress database. 

== Description ==

WP ProFTPd helps Authenticate ProFTPd users to FTP, SFTP, FTPS sites using the WordPress database.  This plugin creates stored procedures in the WordPress database that can be used to query users, groups and user account information.  A dashboard shows all ProFTP logging information inside the WordPress Administration Portal.

== Features ==

- Uses the Wordpress database for ProFTPd authentication
- Creates Wordpress user meta to store information about access rights  
- Uses stored procedures to securely access the WordPress database
- Creates a dashboard to show valuable authentication and FTP operations information
- Supports FTP, SFTP, FTPS

== Requirements ==

- PHP >= 5.5.0
- WordPress >= 4.4
- ProFTPd
- WP Password Bcrypt

== Installation ==

###WP Password Bcrypt Wordpress Plugin Prerequisite

1. Download latest Bcyrpt plugin at https://github.com/roots/wp-password-bcrypt/wp-password-bcrypt.php.
2. Save wp-password-bcrypt.php to /wp-content/mu-plugins - Create mu-plugins folder if it does not exist.

###WP ProFTPd Wordpress Plugin

1. Download the plugin and upload to you WordPress site.
2. Install the plugin.
3. Configure a user for FTP access on the user profile page.
   * Enabled = True
   * UID = 2001
   * GID = 2001
   * Home Directory = /srv/ftp/{username}
   * Shell = /sbin/nologin

###ProFTPd

(Ubuntu Installation)
1. cd /opt
2. sudo apt-get -y install build-essential gettext make g++ libwrap0-dev libsasl2-dev python-dev libmysql++-dev libpam0g-dev libssl-dev unixodbc-dev libncurses5-dev libacl1-dev libcap-dev
3. sudo wget ftp://ftp.proftpd.org/distrib/source/proftpd-1.3.7a.tar.gz
4. sudo tar -xvf proftpd-1.3.7a.tar.gz
5. cd proftpd-1.3.7a
6. sudo ./configure --prefix=/usr --with-includes=/usr/include/mysql --mandir=/usr/share/man --sysconfdir=/etc/proftpd --localstatedir=/var/run --libexecdir=/usr/lib/proftpd --enable-sendfile --enable-facl --enable-dso --enable-autoshadow --enable-ctrls --with-modules=mod_readme:mod_sql:mod_sql_passwd:mod_exec --enable-ipv6 --enable-nls --build x86_64-linux-gnu --with-shared=mod_unique_id:mod_site_misc:mod_load:mod_ban:mod_quotatab:mod_sql:mod_sql_mysql:mod_dynmasq:mod_quotatab_sql:mod_ratio:mod_tls:mod_rewrite:mod_radius:mod_wrap:mod_wrap2:mod_wrap2_file:mod_wrap2_sql:mod_quotatab_file:mod_quotatab_radius:mod_facl:mod_ctrls_admin:mod_sftp:mod_sftp_pam:mod_sftp_sql:mod_shaper:mod_sql_passwd:mod_ifsession build_alias=x86_64-linux-gnu CFLAGS=-O2
7. sudo make install
8. sudo groupadd -g 46 proftpd
9. sudo useradd -c proftpd -d /srv/ftp -g proftpd -s /usr/bin/proftpdshell -u 46 proftpd
10. sudo install -v -d -m775 -o proftpd -g proftpd /srv/ftp
11. sudo ln -v -s /bin/false /usr/bin/proftpdshell
12. sudo mkdir -p /etc/proftpd/ssl
13. sudo mkdir -p /var/log/proftpd
14. Create file 'proftpd' using your favorite editor, copy script below

	#!/bin/sh

	# ProFTPD files
	FTPD_BIN=/usr/sbin/proftpd
	FTPD_CONF=/etc/proftpd/proftpd.conf
	PIDFILE=/var/run/proftpd.pid

	# If PIDFILE exists, does it point to a proftpd process?

	if [ -f $PIDFILE ]; then
		pid=`cat $PIDFILE`
	fi

	if [ ! -x $FTPD_BIN ]; then
		echo "$0: $FTPD_BIN: cannot execute"
		exit 1
	fi

	case $1 in

		start)
			if [ -n "$pid" ]; then
				echo "$0: proftpd [PID $pid] already running"
				exit
			fi

			if [ -r $FTPD_CONF ]; then
				echo "Starting proftpd..."

				$FTPD_BIN -c $FTPD_CONF

			else
				echo "$0: cannot start proftpd -- $FTPD_CONF missing"
			fi
		;;

		stop)
			if [ -n "$pid" ]; then
				echo "Stopping proftpd..."
				kill -TERM $pid

			else
				echo "$0: proftpd not running"
				exit 1
			fi
		;;

		restart)
			if [ -n "$pid" ]; then
			echo "Rehashing proftpd configuration"
			kill -TERM $pid

			else
				echo "$0: proftpd not running"
				xit 1
			fi
		;;

		*)
			echo "usage: $0 {start|stop|restart}"
			exit 1
		;;

	esac

	exit 0

15. Edit file '/etc/proftpd/proftpd.conf' using your favorite editor (FTP Type can be "ftp","ftps","sftp" depending on the virtualhost configuration) 
	`
	<Global>
		<IfModule mod_sql.c>
			SQLBackend                      mysql
			SQLAuthTypes                    bcrypt
			SQLPasswordEngine               on
			SQLPasswordEncoding             base64
			SQLPasswordRounds               8
			SQLEngine                       on
			AuthOrder                       mod_sql.c
			SQLConnectInfo                  {wordpress database name}@localhost {wordpress database user} "{wordpress database password}"

			SQLAuthenticate                 users
			SQLGroupInfo                    wp_proftpd_groups groupname gid members

			SQLUserInfo custom:/get-user-by-name

			# set min UID and GID - otherwise these are 999 each
			SQLMinID        500

			# Update count every time user logs in
			SQLLog PASS updatecount
			SQLNamedQuery updatecount FREEFORM "CALL wp_proftpd_update_count('%U')"

			SqlLogFile /var/log/proftpd/sql.log
			SQLLog PASS,DELE,MKD,RETR,RMD,RNFR,RNTO,STOR,APPE extendedlog
			SQLNamedQuery extendedlog FREEFORM "CALL wp_proftpd_insert_log('%a', '%U', '%r')"
		</IfModule>
	</Global>

	<VirtualHost {x.x.x.x}>
		SQLNamedQuery get-user-by-name FREEFORM "CALL wp_proftpd_get_ftp_user_by_username('%U','{ftp_type}')"
	</VirtualHost>
	`
16. sudo mv proftpd /etc/init.d/proftpd
17. sudo chmod 755 /etc/init.d/proftpd
18. sudo ln -s /etc/init.d/proftpd /etc/init.d/proftpd_start
19. sudo mv /etc/init.d/proftpd_start /etc/rc5.d
20. sudo /etc/init.d/proftpd start OR sudo service proftpd start

#### Log File Locations

1. Proftpd Service = /var/log/proftpd/proftpd.log
2. Proftpd SQL = /var/log/proftpd/sql.log
3. Proftpd Transfer = /var/log/proftpd/xfer.log

