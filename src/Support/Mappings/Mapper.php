<?php

declare(strict_types=1);

namespace XbNz\Resolver\Support\Mappings;

use XbNz\Resolver\Support\DTOs\RequestResponseWrapper;

interface Mapper
{
    public function map(RequestResponseWrapper $requestResponse);
}
