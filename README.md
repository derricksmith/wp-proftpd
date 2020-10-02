# WP ProFTPd

## Description
WP ProFTPd is a Wordpress plugin that creates a frontend GUI for the ProFTPd FTP daemon.  This plugin allows ProFTPd to use the Wordpress database for FTP user authentication and log storage.

## Features
- Uses the Wordpress database for ProFTPd authentication
- Creates Wordpress user meta to store information about access rights  
- Uses stored procedures to securely access the WordPress database
- Creates a dashboard to show valuable authentication and FTP operations information
- Supports FTP, SFTP, FTPS

## Requirements
- PHP >= 5.5.0
- WordPress >= 4.4
- ProFTPd
- WP Password Bcrypt

## Installation

### Prerequisites

#### ProFTPd
WP ProFTPd requires ProFTPd to be installed and configured prior to installing the WordPress plugin.  ProFTPd can be installed from source, APT or Yum depending on your Linux preference.  The ProFTPd installation must contain all necessary SQL/MySQL modules.  An install script exists in the proftpd folder to make installation easier.  The install script installs ProFTP version 1.3.7a from source and uses APT to install all ProFTPd prerequisites.
1. Install ProFTPd
   sh install.sh -d -m -i
2. Copy the proftpd.conf configuration file from the proftpd directory to your proftpd configuration folder
   e.g. "/etc/proftpd/proftpd.conf"
3. Edit proftpd.conf SQL connection information
4. Restart ProFTPd

#### WP Password Bcrypt
WP ProFTPd requires the wp-password-bcrypt Wordpress plugin.  WordPress, by default, uses the MD5 hashing algorithym to store user passwords but is difficult to integrate with ProFTPd.  The Bcrypt hashing algorithym is proven to be stronger than MD5 and can integrate easily with the ProFTPd authentication system.

WP ProFTPd installation will fail without this plugin.  You can find this plugin [here](https://github.com/roots/wp-password-bcrypt).

### WP ProFTPd Plugin Installation
1. Download the plugin and upload to you WordPress site.
2. Install the plugin.
3. Configure a user for FTP access.