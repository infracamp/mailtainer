<?php if (defined("ENABLE_DKIM") && ENABLE_DKIM): ?>
# Defaults consumed by /etc/init.d/opendkim. Keep these set: if RUNDIR
# is empty, the init script calls `install -d ""` and startup fails.
USER="opendkim"
GROUP="opendkim"
RUNDIR="/run/opendkim"
SOCKET="inet:8891@127.0.0.1"
<?php endif; ?>
