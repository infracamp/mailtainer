#!/bin/bash

set -x -e

apt update
debconf-set-selections preseed.txt

DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends -q -y \
  postfix syslog-ng sasl2-bin libsasl2-2 libsasl2-modules net-tools dovecot-core dovecot-imapd dovecot-lmtpd  postfix-policyd-spf-python \
  amavisd-new clamav-daemon spamassassin razor pyzor letsencrypt

useradd vmail
adduser clamav amavis

sudo -i -u amavis razor-admin -create
sudo -i -u amavis razor-admin -register

freshclam
sed -i 's/#SYSLOGNG_OPTS=\"--no-caps\"/SYSLOGNG_OPTS=\"--no-caps\"/' /etc/default/syslog-ng


