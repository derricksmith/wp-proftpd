while getopts ":d:m:i:" opt; do
  case $opt in
    d) download="1"
    ;;
    m) make="1"
    ;;
    i) install="1"
    ;;
    \?) echo "Invalid option -$OPTARG" >&2
    ;;
  esac
done



# install build-tools and dependencies:
apt-get -y install build-essential gettext make g++ libwrap0-dev libsasl2-dev python-dev libmysql++-dev libpam0g-dev libssl-dev unixodbc-dev libncurses5-dev libacl1-dev libcap-dev

if [ -n "$download" ]; then
	# download sources:
	wget ftp://ftp.proftpd.org/distrib/source/proftpd-1.3.7a.tar.gz
	tar -xvf proftpd-1.3.7a.tar.gz
fi

if [ -n "$make" ]; then
	cd proftpd-1.3.7a

	# configure, make and install:
	./configure --prefix=/usr --with-includes=/usr/include/mysql --mandir=/usr/share/man --sysconfdir=/etc/proftpd --localstatedir=/var/run --libexecdir=/usr/lib/proftpd --enable-sendfile --enable-facl --enable-dso --enable-autoshadow --enable-ctrls --with-modules=mod_readme:mod_sql:mod_sql_passwd:mod_exec --enable-ipv6 --enable-nls --build x86_64-linux-gnu --with-shared=mod_unique_id:mod_site_misc:mod_load:mod_ban:mod_quotatab:mod_sql:mod_sql_mysql:mod_dynmasq:mod_quotatab_sql:mod_ratio:mod_tls:mod_rewrite:mod_radius:mod_wrap:mod_wrap2:mod_wrap2_file:mod_wrap2_sql:mod_quotatab_file:mod_quotatab_radius:mod_facl:mod_ctrls_admin:mod_sftp:mod_sftp_pam:mod_sftp_sql:mod_shaper:mod_sql_passwd:mod_ifsession build_alias=x86_64-linux-gnu CFLAGS=-O2
	make install
	cd ..
fi

if [ -n "$install" ]; then
	cd proftpd-1.3.7a
	groupadd -g 46 proftpd                             &&
	useradd -c proftpd -d /srv/ftp -g proftpd \
        	-s /usr/bin/proftpdshell -u 46 proftpd     &&

	install -v -d -m775 -o proftpd -g proftpd /srv/ftp &&
	ln -v -s /bin/false /usr/bin/proftpdshell          &&
	echo /usr/bin/proftpdshell >> /etc/shells	
	
	make install                                   &&
	install -d -m755 /usr/share/doc/proftpd-1.3.7a &&
	cp -Rv doc/*     /usr/share/doc/proftpd-1.3.7a
	cd ..
	
	mkdir -p /etc/proftpd/ssl
	mkdir -p /var/log/proftpd
	
	cat >> proftpd <<'EOF'
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
      exit 1
    fi
    ;;

  *)
    echo "usage: $0 {start|stop|restart}"
    exit 1
    ;;

esac

exit 0
EOF

	mv proftpd /etc/init.d/proftpd

	cd /etc/init.d

	chmod 755 proftpd 

	ln -s proftpd proftpd_start

	mv proftpd_start /etc/rc5.d 

fi


# /etc/init.d/proftpd start