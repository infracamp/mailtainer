<?php


namespace Infracamp\Mailtainer\Type;


class TConfig
{

    /**
     * @var string
     */
    public $postmaster;

    /**
     * @var TAccount[]
     */
    public $accounts = [];


    public function getAllMailDomains() : array
    {
        $allDomains = [];
        foreach ($this->accounts as $account) {
            foreach ($account->aliases as $alias) {
                [$name, $domain] = explode("@", $alias);
                $allDomains[strtolower(trim($domain))] = true;
            }
        }
        $allDomains= array_keys($allDomains);
        asort($allDomains);
        return $allDomains;
    }

}
