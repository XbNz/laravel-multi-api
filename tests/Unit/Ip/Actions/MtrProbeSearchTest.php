<?php

declare(strict_types=1);

namespace XbNz\Resolver\Tests\Unit\Ip\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use XbNz\Resolver\Domain\Ip\Actions\MtrProbeSearchAction;
use XbNz\Resolver\Domain\Ip\DTOs\MtrDotSh\MtrDotShProbeData;
use XbNz\Resolver\Tests\TestCase;

class MtrProbeSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(
            [
                'https://mtr.sh/probes.json' => Http::response(
                    [
                        'kswof' => [
                            'asnumber' => 42473,
                            'caps' => [
                                'trace' => true,
                                'mtr' => true,
                                'dnsr' => true,
                                'dnst' => true,
                                'ping' => true,
                            ],
                            'city' => 'Copenhagen',
                            'country' => 'Denmark',
                            'group' => 'Europe',
                            'provider' => 'Anexia',
                            'providerurl' => 'https://anexia.com/',
                            'residential' => false,
                            'status' => true,
                            'status4' => true,
                            'status6' => false,
                            'unlocode' => 'dkcph',
                        ],

                        '1-s283' => [
                            'asnumber' => 38753,
                            'caps' => [
                                'trace' => true,
                                'mtr' => true,
                                'dnsr' => true,
                                'dnst' => true,
                                'ping' => true,
                            ],
                            'city' => 'Randomsville',
                            'country' => 'Fakemenistan',
                            'group' => 'Europe',
                            'provider' => 'Whatever',
                            'providerurl' => 'https://blah.com',
                            'residential' => false,
                            'status' => true,
                            'status4' => true,
                            'status6' => true,
                            'unlocode' => 'cczzz',
                        ],
                    ]
                ),
            ]
        );
    }

    /** @test **/
    public function it_caches_the_mtr_probes_for_the_appropriate_amount_of_Time(): void
    {
        // Arrange
        $action = app(MtrProbeSearchAction::class);

        // Act
        $collection = $action->execute(searchTerm: '*');

        // Assert
        $this->assertContainsOnlyInstancesOf(MtrDotShProbeData::class, $collection);
        $this->assertTrue(Cache::has('mtr_probes'));
    }

    /** @test **/
    public function if_finds_any_field_matching_the_search_term_and_returns_the_probe_associated_with_it(): void
    {
        // Arrange
        $action = app(MtrProbeSearchAction::class);

        // Act
        $collection = $action->execute(searchTerm: 'copenhagen');
        $collectionB = $action->execute(searchTerm: '::gibberish::');

        // Assert
        $this->assertContainsOnlyInstancesOf(MtrDotShProbeData::class, $collection);
        $this->assertContainsOnlyInstancesOf(MtrDotShProbeData::class, $collectionB);
        $this->assertCount(1, $collection);
        $this->assertCount(0, $collectionB);
    }

    /** @test **/
    public function ipv4_boolean_test(): void
    {
        // Arrange
        $action = app(MtrProbeSearchAction::class);

        // Act
        $collection = $action->execute(false);
        $collectionB = $action->execute(true);
        $collectionC = $action->execute(null);

        // Assert
        $this->assertCount(0, $collection);
        $this->assertCount(2, $collectionB);
        $this->assertCount(2, $collectionC);
    }

    /** @test **/
    public function ipv6_boolean_test(): void
    {
        // Arrange
        $action = app(MtrProbeSearchAction::class);

        // Act
        $collection = $action->execute(ipv6: false);
        $collectionB = $action->execute(ipv6: true);
        $collectionC = $action->execute(ipv6: null);

        // Assert
        $this->assertCount(1, $collection);
        $this->assertCount(1, $collectionB);
        $this->assertCount(2, $collectionC);
    }

    /** @test **/
    public function is_online_boolean_test(): void
    {
        // Arrange
        $action = app(MtrProbeSearchAction::class);

        // Act
        $collection = $action->execute(isOnline: false);
        $collectionB = $action->execute(isOnline: true);
        $collectionC = $action->execute(isOnline: null);

        // Assert
        $this->assertCount(0, $collection);
        $this->assertCount(2, $collectionB);
        $this->assertCount(2, $collectionC);
    }


    /** @test **/
    public function it_ignores_boolean_values(): void
    {
        // Arrange
        $action = app(MtrProbeSearchAction::class);

        // Act
        $collection = $action->execute(searchTerm: '1');

        // Assert
        $this->assertCount(0, $collection);
    }
}
