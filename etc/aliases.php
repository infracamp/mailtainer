<?php
$tpl = \Phore\CloudTool\CloudToolTemplate::Get();

$tpl->ignoreGlobalChangeAction()->setOnChangeAction(function () use ($tpl) {
    phore_exec("postmap hash:" . $tpl->getTargetFile());
});

$config = $tpl->getEnvironment()["config"];
if ( ! $config instanceof \Infracamp\Mailtainer\Type\TConfig)
    throw new InvalidArgumentException("Invalid config");
?>

postmaster root
amavis root
clamav root





