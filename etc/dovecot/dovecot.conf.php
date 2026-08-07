dovecot_config_version = 2.4.0
dovecot_storage_version = 2.4.0

log_path = /data/log/dovecot.log
info_log_path = /data/log/dovecot-info.log
debug_log_path = /data/log/dovecot-debug.log

<?php if(DEBUG): ?>
log_debug = category=auth
log_debug = category=mail
auth_verbose_passwords = yes
<?php endif ?>

protocols {
  imap = yes
  lmtp = yes
}

<?php if(ENABLE_LETSENCRYPT): ?>
ssl = required
ssl_server_cert_file = /data/letsencrypt/live/<?= MAILNAME ?>/fullchain.pem
ssl_server_key_file = /data/letsencrypt/live/<?= MAILNAME ?>/privkey.pem
<?php else: ?>
ssl = no
<?php endif; ?>

mail_home = /data/dovecot/%{user | domain}/%{user | username}
mail_driver = maildir
mail_path = %{home}
mail_uid = vmail
mail_gid = vmail

<?php if( ! ENABLE_LETSENCRYPT): ?>
auth_allow_cleartext = yes
<?php endif; ?>
auth_mechanisms = plain login

namespace inbox {
  inbox = yes
  prefix =
  mailbox Drafts {
    special_use = \Drafts
  }
  mailbox Junk {
    special_use = \Junk
  }
  mailbox Sent {
    special_use = \Sent
  }
  mailbox "Sent Messages" {
    special_use = \Sent
  }
  mailbox Trash {
    special_use = \Trash
  }
}

passdb passwd-file {
  passwd_file_path = /etc/dovecot/users
}

userdb static {
  fields {
    uid = vmail
    gid = vmail
    home = /data/dovecot/%{user | domain}/%{user | username}
  }
}

service auth-worker {
  user = $SET:default_internal_user
}

service auth {
  unix_listener /var/spool/postfix/private/auth {
    type = auth
    group = postfix
    user = postfix
    mode = 0666
  }
}

service lmtp {
 unix_listener /var/spool/postfix/private/dovecot-lmtp {
   mode = 0666
   user = postfix
   group = postfix
  }
}
