<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Mappings;

use GuzzleHttp\Psr7\Response;
use XbNz\Resolver\Domain\Ip\Services\Request;
use XbNz\Resolver\Support\DTOs\Mappable;
use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;

interface Mapper
{
    public function map(RequestResponseWrapper $requestResponse);
}
