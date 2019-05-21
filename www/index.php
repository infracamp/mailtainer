<?php

namespace App;
use App\Cms\CmsCtrl;
use Phore\MicroApp\App;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Handler\JsonResponseHandler;
use Phore\MicroApp\Helper\CORSHelper;
use Phore\MicroApp\Type\QueryParams;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\RouteParams;


require __DIR__ . "/../vendor/autoload.php";

$app = new App();
$app->activateExceptionErrorHandlers();
$app->setOnExceptionHandler(new JsonExceptionHandler());
$app->setResponseHandler(new JsonResponseHandler());

$app->assets()->addAssetSearchPath(__DIR__ . "/assets/");


/**
 ** Configure Access Control Lists
 **/
$app->acl->addRule(\aclRule()->route("/*")->ALLOW());

require __DIR__ . "/di-config.inc.php";
require __DIR__ . "/admin.inc.php";


$app->addCtrl(CmsCtrl::class);

/**
 ** Run the application
 **/
$app->serve();
