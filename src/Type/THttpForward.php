<?php


namespace Infracamp\Mailtainer\Type;


class THttpForward
{

    /**
     * Full URL to the http target to call
     *
     * @var string
     */
    public $targetUrl;

    /**
     * Separated by colon ":" - <user>:<passwd> for
     * HTTP basic authentication
     *
     * @var string|null
     */
    public $basicAuth;

    /**
     * The e-Mail Aliases
     *
     * @var string[]
     */
    public $aliases = [];

    public function getAliasesName()
    {
        return "httpredir-" . md5($this->targetUrl);
    }

}
