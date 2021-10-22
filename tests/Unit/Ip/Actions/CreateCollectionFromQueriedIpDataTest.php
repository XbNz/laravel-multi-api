<?php

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use XbNz\Resolver\Domain\Ip\Actions\CreateCollectionFromQueriedIpDataAction;
use XbNz\Resolver\Factories\QueriedIpDataFactory;

class CreateCollectionFromQueriedIpDataTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function it_takes_a_collection_of_queried_dtos_and_returns_a_normalized_collection()
    {
        $queriedResults = collect([
            QueriedIpDataFactory::generateTestData(['country' => 'Canada']),
            QueriedIpDataFactory::generateTestData(['city' => 'Berlin']),
            QueriedIpDataFactory::generateTestData(['latitude' => '11.11'])
        ]);

        $collection = app(CreateCollectionFromQueriedIpDataAction::class)
            ->execute($queriedResults);

        $this->assertEquals('Canada', $collection['country'][0]['data']);
        $this->assertEquals('Berlin', $collection['city'][1]['data']);
        $this->assertEquals('11.11', $collection['latitude'][2]['data']);
    }
}