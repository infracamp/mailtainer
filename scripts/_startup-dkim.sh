#!/bin/bash

setup_dkim() {
    if [ "${ENABLE_DKIM:-0}" != "1" ]; then
        return 0
    fi

    DKIM_SELECTOR="${DKIM_SELECTOR:-mail}"
    DKIM_PRIVATE_KEY_FILE="${DKIM_PRIVATE_KEY_FILE:-/run/secrets/dkim_private_key}"
    DKIM_KEY_FILE="/etc/opendkim/keys/${DKIM_SELECTOR}.private"

    if [ ! -e "$DKIM_PRIVATE_KEY_FILE" ]; then
        echo "[DKIM] Private key file does not exist: $DKIM_PRIVATE_KEY_FILE" >&2
        echo "[DKIM] Please verify that the Docker secret is configured and mounted at this path." >&2
        exit 1
    fi

    if [ ! -r "$DKIM_PRIVATE_KEY_FILE" ]; then
        echo "[DKIM] Private key file is not readable: $DKIM_PRIVATE_KEY_FILE" >&2
        exit 1
    fi

    mkdir -p /etc/opendkim/keys /run/opendkim
    install -o opendkim -g opendkim -m 0600 "$DKIM_PRIVATE_KEY_FILE" "$DKIM_KEY_FILE"

    openssl rsa -in "$DKIM_KEY_FILE" -check -noout >/dev/null 2>&1 || {
        echo "[DKIM] Invalid RSA private key: $DKIM_PRIVATE_KEY_FILE" >&2
        exit 1
    }

    chown -R opendkim:opendkim /etc/opendkim/keys /run/opendkim

    DKIM_PUBLIC_KEY="$(openssl rsa -in "$DKIM_KEY_FILE" -pubout -outform DER 2>/dev/null | openssl base64 -A)"

    echo "[DKIM] Enabled automatic signing for all accepted sender domains"
    echo "[DKIM] Selector: $DKIM_SELECTOR"
    echo "[DKIM] Add this TXT record for every outgoing sender domain:"
    echo "[DKIM] Name:  ${DKIM_SELECTOR}._domainkey.<domain>"
    echo "[DKIM] Type:  TXT"
    echo "[DKIM] Value: v=DKIM1; k=rsa; p=${DKIM_PUBLIC_KEY}"
}
