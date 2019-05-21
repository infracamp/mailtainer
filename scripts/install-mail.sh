#!/bin/bash

set -x -e

apt update
apt-get install -y dovecot-core dovecot-imapd postfix

debconf-set-selections preseed.txt
DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends -q -y postfix syslog-ng sasl2-bin libsasl2-2 libsasl2-modules net-tools


sed -i 's/#SYSLOGNG_OPTS=\"--no-caps\"/SYSLOGNG_OPTS=\"--no-caps\"/' /etc/default/syslog-ng


