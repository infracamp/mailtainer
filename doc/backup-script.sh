#!/usr/bin/env bash

set -e

MAILTAINER_HOST="mailtainer.host.name"
BACKUP_AUTH_PASS_PLAIN="plain passwd"

BACKUP_LOCATION="/bku"

## Download with now progress bar but show errors:
curl -sSfo "$BACKUP_LOCATION/bku-$(date +%a).enc" -u backup:$BACKUP_AUTH_PASS_PLAIN http://$MAILTAINER_HOST/export.php
