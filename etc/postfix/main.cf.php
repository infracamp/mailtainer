

# See /usr/share/postfix/main.cf.dist for a commented, more complete version


# Debian specific:  Specifying a file name will cause the first
# line of that file to be used as the name.  The Debian default
# is /etc/mailname.
myorigin = <?= MAILNAME ?>
compatibility_level=2

smtpd_banner = $myhostname ESMTP $mail_name (Debian/GNU)
biff = no


# appending .domain is the MUA's job.
append_dot_mydomain = no

# Uncomment the next line to generate "delayed mail" warnings
delay_warning_time = 2h

readme_directory = no
mail_spool_directory = /data/postfix
maillog_file=/data/log/postfix.log

content_filter=smtp-amavis:[127.0.0.1]:10024


smtpd_sasl_auth_enable = yes
smtpd_sasl_type = dovecot
# The path is relative to $queue_directory:
#   $postconf |grep queue_directory
#   queue_directory = /var/spool/postfix
smtpd_sasl_path = private/auth

policy-spf_time_limit = 3600s


# TLS parameters
smtp_tls_loglevel = 1
smtpd_tls_loglevel = 1

smtp_tls_CApath = /etc/ssl/certs
smtpd_tls_CApath = /etc/ssl/certs

<?php if(ENABLE_LETSENCRYPT): ?>
smtp_tls_protocols = !SSLv2, !SSLv3
smtp_tls_ciphers = high
smtp_tls_security_level = may
smtp_use_tls = yes
smtp_tls_cert_file=/data/letsencrypt/live/<?= MAILNAME ?>/fullchain.pem
smtp_tls_key_file=/data/letsencrypt/live/<?= MAILNAME ?>/privkey.pem

smtpd_tls_protocols = !SSLv2, !SSLv3
smtpd_tls_ciphers = high
smtpd_tls_cert_file=/data/letsencrypt/live/<?= MAILNAME ?>/fullchain.pem
smtpd_tls_key_file=/data/letsencrypt/live/<?= MAILNAME ?>/privkey.pem
smtpd_use_tls=yes
smtpd_tls_session_cache_database = btree:${data_directory}/smtpd_scache
smtp_tls_session_cache_database = btree:${data_directory}/smtp_scache

## PRODUCTION:
smtpd_tls_auth_only = yes
smtp_sasl_security_options = noplaintext noanonymous
<?php else: ?>
## TESTING ONLY
smtpd_tls_auth_only = no
smtp_sasl_security_options = noanonymous
<?php endif; ?>

# See /usr/share/doc/postfix/TLS_README.gz in the postfix-doc package for
# information on enabling SSL in the smtp client.

smtpd_relay_restrictions =
	permit_mynetworks
	permit_sasl_authenticated
	defer_unauth_destination

smtpd_recipient_restrictions =
    permit_mynetworks
	permit_sasl_authenticated
	reject_unauth_destination
	check_policy_service unix:private/policy-spf
	reject_non_fqdn_sender
    reject_non_fqdn_recipient
    reject_unauth_pipelining
    reject_unauth_destination
    reject_invalid_hostname
<?php foreach(explode(";", RBL_CLIENT) as $cur): ?>
<?php if ($cur === "") continue; ?>
    reject_rbl_client <?= $cur ?>
<?php endforeach; ?>

## Allow only MAIL FROM: Adresses listed in Aliases of this account
smtpd_sender_login_maps = hash:/etc/postfix/virtual_aliases
smtpd_sender_restrictions =
    reject_sender_login_mismatch



smtpd_helo_restrictions =
	permit_sasl_authenticated
	permit_mynetworks
	reject_invalid_hostname
	reject_unauth_pipelining
	reject_non_fqdn_hostname


alias_maps = hash:/etc/aliases
alias_database = hash:/etc/aliases


mydestination = localhost, $myorigin
relayhost =
mynetworks = 127.0.0.0/8 [::ffff:127.0.0.0]/104 [::1]/128
mailbox_command = procmail -a "$EXTENSION"
mailbox_size_limit = 0
recipient_delimiter = +
inet_interfaces = all


# Handing off local delivery to Dovecot's LMTP
# http://wiki2.dovecot.org/HowTo/PostfixDovecotLMTP
#
# The path relative to $queue_directory, that is:
#    /var/spool/postfix/private/dovecot-lmtp
virtual_transport = lmtp:unix:private/dovecot-lmtp
transport_maps = hash:/etc/postfix/transport

# Check domains only, query users and aliases in Dovecot
#
# IMPORTANT: Don't overlap with $mydestination
#virtual_mailbox_domains = example1.com, example2.com
virtual_mailbox_domains = hash:/etc/postfix/virtual_domains


#virtual_alias_domains = $virtual_alias_maps

virtual_alias_maps = hash:/etc/postfix/virtual_aliases

#virtual_alias_domains = hash:/etc/postfix/virtual_aliases


