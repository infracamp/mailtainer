#!/bin/bash

##
# This script is run once the container starts up
#

set -x -e


echo $MAILNAME > /etc/mailname

mkdir -p /data/dovecot
mkdir -p /data/postfix
mkdir -p /data/letsencrypt

chown -R vmail:vmail /data/dovecot
chown -R root:root /data/letsencrypt

service syslog-ng start
service cron start

/opt/scripts/letsencrypt.sh



