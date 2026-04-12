<?php

namespace Tests;

use Anibalealvarezs\TripleWhaleApi\TripleWhaleApi;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\ApiRequestException;

class TripleWhaleApiTest extends TestCase
{
    private TripleWhaleApi $tripleWhaleApi;
    private Generator $faker;

    /**
     * @param MockHandler $mock
     * @return GuzzleClient
     */
    protected function createMockedGuzzleClient(MockHandler $mock): GuzzleClient
    {
        $handlerStack = HandlerStack::create($mock);
        return new GuzzleClient(['handler' => $handlerStack]);
    }

    protected function setUp(): void
    {
        $this->tripleWhaleApi = new TripleWhaleApi(
            token: 'token',
            shopId: 'shop-id',
            user: 'user',
            shopDomain: 'test-shop.com',
            gitSha: 'sha',
            datadogParentId: 'parent',
            datadogTraceId: 'trace',
        );
        $this->faker = Factory::create();
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(TripleWhaleApi::class, $this->tripleWhaleApi);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetActivitiesAll(): void
    {
        $response1 = [
            'activities' => [['id' => 1]],
            'totalPages' => 2
        ];
        $response2 = [
            'activities' => [['id' => 2]],
            'totalPages' => 2
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($response1)),
            new Response(200, [], json_encode($response2)),
        ]);
        $guzzle = $this->createMockedGuzzleClient($mock);

        $client = new TripleWhaleApi(
            token: 'token',
            shopId: 'shop-id',
            user: 'user',
            shopDomain: 'test-shop.com',
            gitSha: 'sha',
            datadogParentId: 'parent',
            datadogTraceId: 'trace',
            guzzleClient: $guzzle
        );

        $result = $client->getActivitiesAll();

        $this->assertCount(2, $result['activities']);
        $this->assertEquals(1, $result['activities'][0]['id']);
        $this->assertEquals(2, $result['activities'][1]['id']);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetActivitiesAllAndProcess(): void
    {
        $response1 = [
            'activities' => [['id' => 1]],
            'totalPages' => 2
        ];
        $response2 = [
            'activities' => [['id' => 2]],
            'totalPages' => 2
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($response1)),
            new Response(200, [], json_encode($response2)),
        ]);
        $guzzle = $this->createMockedGuzzleClient($mock);

        $client = new TripleWhaleApi(
            token: 'token',
            shopId: 'shop-id',
            user: 'user',
            shopDomain: 'test-shop.com',
            gitSha: 'sha',
            datadogParentId: 'parent',
            datadogTraceId: 'trace',
            guzzleClient: $guzzle
        );

        $processedCount = 0;
        $client->getActivitiesAllAndProcess(function ($data) use (&$processedCount) {
            $processedCount += count($data);
        });

        $this->assertEquals(2, $processedCount);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetActivitiesAllEmpty(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['activities' => [], 'totalPages' => 0])),
        ]);
        $guzzle = $this->createMockedGuzzleClient($mock);

        $client = new TripleWhaleApi(
            token: 'token',
            shopId: 'shop-id',
            user: 'user',
            shopDomain: 'test-shop.com',
            gitSha: 'sha',
            datadogParentId: 'parent',
            datadogTraceId: 'trace',
            guzzleClient: $guzzle
        );

        $result = $client->getActivitiesAll();
        
        $this->assertCount(0, $result['activities']);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetActivitiesAllErrorMidLoop(): void
    {
        $response1 = [
            'activities' => [['id' => 1]],
            'totalPages' => 2
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($response1)),
            new Response(500, [], 'Internal Server Error'),
        ]);
        $guzzle = $this->createMockedGuzzleClient($mock);

        $client = new TripleWhaleApi(
            token: 'token',
            shopId: 'shop-id',
            user: 'user',
            shopDomain: 'test-shop.com',
            gitSha: 'sha',
            datadogParentId: 'parent',
            datadogTraceId: 'trace',
            guzzleClient: $guzzle
        );

        $this->expectException(ApiRequestException::class);

        $client->getActivitiesAllAndProcess(function ($data) {});
    }
}
