<?php

namespace Tests;

use Carbon\Carbon;
use Anibalealvarezs\TripleWhaleApi\TripleWhaleApi;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class TripleWhaleApiTest extends TestCase
{
    private TripleWhaleApi $netSuiteApi;
    private Generator $faker;

    /**
     * @throws GuzzleException
     */
    protected function setUp(): void
    {
        $config = Yaml::parseFile(__DIR__ . "/../config/config.yaml");
        $this->netSuiteApi = new NetSuiteApi(
            consumerId: $config['netsuite_consumer_id'],
            consumerSecret: $config['netsuite_consumer_secret'],
            token: $config['netsuite_token_id'],
            tokenSecret: $config['netsuite_token_secret'],
            accountId: $config['netsuite_account_id'],
        );
        $this->faker = Factory::create();
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(NetSuiteApi::class, $this->netSuiteApi);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetSalesOrders(): void
    {
        $salesOrders = $this->netSuiteApi->getSalesOrders(
            limit: $this->faker->numberBetween(1, 1000)
        );

        $this->assertIsArray($salesOrders);
        $this->assertArrayHasKey('links', $salesOrders);
        $this->assertArrayHasKey('count', $salesOrders);
        $this->assertArrayHasKey('hasMore', $salesOrders);
        $this->assertArrayHasKey('items', $salesOrders);
        $this->assertIsArray($salesOrders['items']);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetSuiteQLQuery(): void
    {
        $result = $this->netSuiteApi->getSuiteQLQuery(
            query: "SELECT transaction.id FROM transaction WHERE ( transaction.Type = 'SalesOrd' )",
            limit: $this->faker->numberBetween(1, 1000)
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('links', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertArrayHasKey('hasMore', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
    }
}