<?php

namespace App;
use Phore\MicroApp\App;
use Phore\MicroApp\Response\HtmlResponse;
use Phore\VCS\VcsFactory;
use Phore\VCS\VcsRepository;

/**
 * @var $app App
 */


$app->router->onGet("/.well-known/update", function (VcsRepository $vcsRepository) {
    $vcsRepository->pull();
    return ["success"=>"true"];
});

$app->router->onGet("/.well-known/ssh-public-key", function () {
    header("Content-Type: text/plain");
    echo phore_file(SSH_KEY_PATH . ".pub")->get_contents();
    return true;
});
