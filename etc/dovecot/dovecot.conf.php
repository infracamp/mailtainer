# 2.2.13: /etc/dovecot/dovecot.conf
# OS: Linux 3.16.0-042stab117.10 x86_64 Debian 8.5

log_path = /data/log/dovecot.log
# If you want everything in one file, just don't specify info_log_path and debug_log_path
info_log_path = /data/log/dovecot-info.log
# Leave empty in order to send debug-level messages to info_log_path
debug_log_path = /data/log/dovecot-debug.log

<?php if(DEBUG): ?>
auth_debug=yes
mail_debug=yes
auth_debug_passwords=yes
<?php endif ?>

<?php if(ENABLE_LETSENCRYPT): ?>
ssl = required
ssl_cert = </data/letsencrypt/live/<?= MAILNAME ?>/fullchain.pem
ssl_key = </data/letsencrypt/live/<?= MAILNAME ?>/privkey.pem
<?php else: ?>
ssl = no
<?php endif; ?>

mail_location = maildir:/data/dovecot/%d/%n

namespace inbox {
  inbox = yes
  location =
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
  prefix =
}
passdb {

  driver = passwd-file
  # The entire email address will be used as the username for email client.
  # Don't bother about the scheme here, will be overwritten by a strong scheme from file.
  #    (http://wiki2.dovecot.org/AuthDatabase/PasswdFile)
  args = scheme=CRYPT username_format=%u /etc/dovecot/users

}
protocols = " imap lmtp"

userdb {
  # For static type, LDA verify the user's existence by lookup passdb
  #   ( http://wiki2.dovecot.org/UserDatabase/Static )
  driver = static
  args = uid=vmail gid=vmail home=/data/dovecot/%d/%n
}

auth_mechanisms = plain login cram-md5 scram-sha-1
disable_plaintext_auth = yes

service auth-worker {
  # Forbid to access /etc/shadow
  user = $default_internal_user
}

service auth {
  # IMPORTANT: Match the path to smtpd_sasl_path of Postfix
  unix_listener /var/spool/postfix/private/auth {
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

