#!/bin/bash

set -x -e

apt update
debconf-set-selections preseed.txt

DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends -q -y postfix syslog-ng sasl2-bin libsasl2-2 libsasl2-modules net-tools dovecot-core dovecot-imapd


sed -i 's/#SYSLOGNG_OPTS=\"--no-caps\"/SYSLOGNG_OPTS=\"--no-caps\"/' /etc/default/syslog-ng


