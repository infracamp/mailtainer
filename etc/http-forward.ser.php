<?php
$tpl = \Phore\CloudTool\CloudToolTemplate::Get();

$tpl->ignoreGlobalChangeAction();

$config = $tpl->getEnvironment()["config"];
if ( ! $config instanceof \Infracamp\Mailtainer\Type\TConfig)
    throw new InvalidArgumentException("Invalid config");


$map = [];

foreach ($config->httpForward as $forward) {
    foreach ($forward->aliases as $alias)
        $map[$alias] = [
            "targetUrl" => $forward->targetUrl,
            "basicAuth" => $forward->basicAuth
        ];
}

echo serialize($map);
