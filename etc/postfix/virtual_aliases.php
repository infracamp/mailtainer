<?php
$tpl = \Phore\CloudTool\CloudToolTemplate::Get();

$tpl->ignoreGlobalChangeAction()->setOnChangeAction(function () use ($tpl) {
    phore_exec("postmap hash:" . $tpl->getTargetFile());
});

$config = $tpl->getEnvironment()["config"];
if ( ! $config instanceof \Infracamp\Mailtainer\Type\TConfig)
    throw new InvalidArgumentException("Invalid config");
?>

# The input(left column) without domain, will match user@$myorigin
#   and user@$mydestination (e.g. root@example.com, root@localhost)
#
# The result(right column) without domain, Postfix will append
#   $myorigin as $append_at_myorigin=yes
# So the user user1@appbead.com must exists in /etc/dovecot/users
# See: The section TABLE FORMAT in manual virtual(5)

postmaster          root
webmaster           root

# Person who should get root's mail
root               <?= $config->postmaster ?>

<?php

foreach ($config->accounts as $account) {
    foreach ($account->aliases as $alias)
        echo "{$alias}\t{$account->account}\n";
}





