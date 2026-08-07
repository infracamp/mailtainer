#!/bin/bash

set -e

if ! command -v a2dismod >/dev/null 2>&1; then
    echo "[APACHE] a2dismod not found; skipping Apache module hardening"
    exit 0
fi

# PHP must remain available for CLI/template generation, but Apache must not execute PHP.
for php_module in /etc/apache2/mods-enabled/php*.load /etc/apache2/mods-available/php*.load; do
    [ -e "$php_module" ] || continue
    module_name="$(basename "$php_module" .load)"
    echo "[APACHE] Disabling Apache PHP module: $module_name"
    a2dismod -f "$module_name" >/dev/null 2>&1 || true
done

# Disable modules not needed for serving static ACME HTTP-01 files from /opt/www/.
for module_name in \
    actions \
    asis \
    autoindex \
    cgi \
    cgid \
    dav \
    dav_fs \
    dav_lock \
    imagemap \
    include \
    info \
    lua \
    negotiation \
    proxy \
    proxy_ajp \
    proxy_balancer \
    proxy_connect \
    proxy_express \
    proxy_fcgi \
    proxy_fdpass \
    proxy_ftp \
    proxy_http \
    proxy_hcheck \
    proxy_html \
    proxy_scgi \
    proxy_uwsgi \
    proxy_wstunnel \
    status \
    suexec \
    userdir; do
    if [ -e "/etc/apache2/mods-enabled/${module_name}.load" ] || [ -e "/etc/apache2/mods-available/${module_name}.load" ]; then
        echo "[APACHE] Disabling unused module: $module_name"
        a2dismod -f "$module_name" >/dev/null 2>&1 || true
    fi
done

apache2ctl configtest
