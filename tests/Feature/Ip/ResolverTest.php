<?php

namespace XbNz\Resolver\Tests\Feature\Ip;

use XbNz\Resolver\Domain\Ip\Builders\IpBuilder;
use XbNz\Resolver\Resolver\Resolver;
use function app;

class ResolverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function ip_builder_is_successfully_instantiated()
    {
        $builder = app(Resolver::class)
            ->ip();

        $this->assertInstanceOf(IpBuilder::class, $builder);
    }
}