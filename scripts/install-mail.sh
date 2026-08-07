#!/bin/bash

set -x -e

apt update

composer global require nfra/ctool

debconf-set-selections preseed.txt



DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends -q -y \
  php-cli postfix syslog-ng sasl2-bin libsasl2-2 libsasl2-modules net-tools dovecot-core dovecot-imapd dovecot-lmtpd  postfix-policyd-spf-python \
  amavisd-new clamav-daemon spamassassin razor pyzor letsencrypt cron \
  opendkim opendkim-tools dnsutils

apt-get purge -q -y 'libapache2-mod-php*' || true
/opt/scripts/harden-apache.sh

getent group vmail >/dev/null 2>&1 || groupadd --system vmail
id -u vmail >/dev/null 2>&1 || useradd --system --gid vmail --no-create-home --shell /usr/sbin/nologin vmail
getent group opendkim >/dev/null 2>&1 || groupadd --system opendkim
id -u opendkim >/dev/null 2>&1 || useradd --system --gid opendkim --no-create-home --shell /usr/sbin/nologin opendkim
adduser clamav amavis

sudo -i -u amavis razor-admin -create
sudo -i -u amavis razor-admin -register

freshclam
sed -i 's/#SYSLOGNG_OPTS=\"--no-caps\"/SYSLOGNG_OPTS=\"--no-caps\"/' /etc/default/syslog-ng


