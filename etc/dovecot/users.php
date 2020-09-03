<?php
$tpl = \Phore\CloudTool\CloudToolTemplate::Get();
$tpl->ignoreGlobalChangeAction();

$config = $tpl->getEnvironment()["config"];
if ( ! $config instanceof \Infracamp\Mailtainer\Type\TConfig)
    throw new InvalidArgumentException("Invalid config");


foreach ($config->accounts as $account) {
    echo "{$account->account}:{$account->passwordHash}\n";
}



