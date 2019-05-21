<?php

namespace App;
use Phore\MicroApp\App;
use Phore\MicroApp\Response\HtmlResponse;
use Phore\VCS\VcsFactory;
use Phore\VCS\VcsRepository;

/**
 * @var $app App
 */


$app->define("vcsRepository", function() : VcsRepository {
    $vcsFactory = new VcsFactory();
    $vcsFactory->setAuthSshPrivateKey(phore_file(SSH_KEY_PATH)->get_contents());

    $repo = $vcsFactory->repository(REPO_PATH, CONFIG_REPO);
    return $repo;
});
