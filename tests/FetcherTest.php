<?php

namespace Tests;

use App\Fetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery as m;

class FetcherTest extends TestCase
{
    public function testFetch()
    {
        $client = m::mock(Client::class);
        $response = m::mock(Response::class);

        $client->shouldReceive('get')
            ->once()
            ->with('https://tw.dictionary.search.yahoo.com/search?p=foo')
            ->andReturn($response);

        $response->shouldReceive('getBody->getContents')
            ->once()
            ->andReturn('bar');

        $this->assertEquals('bar', (new Fetcher($client))->fetch('foo'));
    }
}
