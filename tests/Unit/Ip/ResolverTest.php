<?php

namespace XbNz\Resolver\Tests\Unit\Ip;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Resolver\Resolver;

class ResolverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function calling_the_ip_method_on_resolver_returns_a_builder_instance()
    {
        $shouldBeBuilder = app(Resolver::class)
            ->ip();

        $this->assertInstanceOf(DriverBuilder::class, $shouldBeBuilder);
    }
}