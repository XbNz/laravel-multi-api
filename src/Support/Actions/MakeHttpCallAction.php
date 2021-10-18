<?php

namespace XbNz\Resolver\Support\Actions;

use Config;

class MakeHttpCallAction
{

    public function execute(string $url, array $params = [])
    {
        if (Config::has('resolver.use_proxy') && Config::get('resolver.use_proxy') === true){
            //TODO: Make universal config file (not IP specific)
        }
    }
}