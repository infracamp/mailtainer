<?php

namespace App;
use Phore\MicroApp\App;
use Phore\VCS\VcsFactory;

/**
 * @var $app App
 */


$app->router->onGet("/.well-known/update", function () {
    $vcsFactory = new VcsFactory();
    $vcsFactory->setAuthSshPrivateKey(phore_file(SSH_KEY_PATH)->get_contents());

    $repo = $vcsFactory->repository(REPO_PATH, CONFIG_REPO);

    $repo->pull();

});
