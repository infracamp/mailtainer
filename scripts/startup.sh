#!/bin/bash

##
# This script is run once the container starts up
#

set -x -e


echo $MAILNAME > /etc/mailname

mkdir -p /data/dovecot
mkdir -p /data/postfix
mkdir -p /data/letsencrypt
mkdir -p /data/log
mkdir -p /var/spool/postfix/private

chown -R vmail:vmail /data/dovecot
chown -R postfix:postdrop /data/postfix
chown -R root:root /data/letsencrypt
chown -R root:root /data/log
chown postfix:root /var/spool/postfix/private
chmod 0700 /var/spool/postfix/private

. /opt/scripts/_startup-dkim.sh
setup_dkim
/opt/src/bin/validata-domain-config.php

service syslog-ng start
service cron start

service apache2 start
/opt/scripts/letsencrypt.sh
service apache2 stop


