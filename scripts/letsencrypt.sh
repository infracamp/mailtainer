#!/bin/bash

set -ex

if [[ "$ENABLE_LETSENCRYPT" == "1" ]]; then
    echo "Letsencrypt enabled - trying to load cert for $MAILNAME..."
    certbot certonly -n --config-dir /data/letsencrypt --agree-tos -m admin@$MAILNAME --webroot -w /opt/www/ -d $MAILNAME
fi;
