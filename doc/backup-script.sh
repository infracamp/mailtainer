#!/usr/bin/env bash

set -e

MAILTAINER_HOST="mailtainer.host.name"
BACKUP_AUTH_PASS_PLAIN="plain passwd"

BACKUP_LOCATION="/bku"


curl -fo "$BACKUP_LOCATION/bku-$(date +%a).enc" -u backup:$BACKUP_AUTH_PASS_PLAIN http://$MAILTAINER_HOST/export.php
