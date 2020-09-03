<?php

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../config.php";


Phore\CloudTool\PhoreCloudTool::Config()->environmentLoader = function() {
    return [
        "config" => phore_hydrate(
            phore_file(CONFIG_FILE)->get_yaml(),
            \Infracamp\Mailtainer\Type\TConfig::class
        )
    ];
};
