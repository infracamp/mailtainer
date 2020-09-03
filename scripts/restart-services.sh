#!/bin/bash

set -e

postmap hash:/etc/postfix/virtual_domains
postmap hash:/etc/postfix/virtual_aliases

service amavis restart
service clamav-daemon start
service dovecot restart
service postfix restart
