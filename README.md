# WP ProFTPd

## Description
WP ProFTPd is a Wordpress plugin that creates a frontend GUI for the ProFTPd FTP daemon.  This plugin allows ProFTPd to use the Wordpress database for FTP user authentication and log storage.

## Features
- Uses the Wordpress database for ProFTPd authentication
- Creates Wordpress user meta to store information about access rights  
- Uses stored procedures to securely access the WordPress database
- Creates a dashboard to show valuable authentication and FTP operations information
- Supports FTP, SFTP, FTPS

## Installation

### Prerequisites

#### ProFTPd
WP ProFTPd requires ProFTPd to be installed and configured prior to installing the WordPress plugin.  ProFTPd can be installed from source, APT or Yum depending on your Linux preference.  The ProFTPd installation must contain all necessary SQL/MySQL modules.  An install script exists in the proftpd folder to make installation easier.  The install script installs ProFTP version 1.3.7a from source using APT.

sh install.sh -d -m -i

#### 



