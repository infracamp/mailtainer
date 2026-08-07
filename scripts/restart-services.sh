#!/bin/bash

set -e

postmap hash:/etc/postfix/virtual_domains
postmap hash:/etc/postfix/virtual_aliases

service syslog-ng restart
if [ "${ENABLE_DKIM:-0}" = "1" ]; then
    service opendkim restart
fi
service amavis restart
service clamav-daemon start
mkdir -p /var/spool/postfix/private
chown postfix:root /var/spool/postfix/private
chmod 0700 /var/spool/postfix/private
service dovecot restart
service postfix restart
