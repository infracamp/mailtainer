<?php if (defined("ENABLE_DKIM") && ENABLE_DKIM): ?>
default %:<?= DKIM_SELECTOR ?>:/etc/opendkim/keys/<?= DKIM_SELECTOR ?>.private
<?php endif; ?>
