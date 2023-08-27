<?php

namespace App;

use GuzzleHttp\Client;

class Fetcher
{
    private const URL = 'https://tw.dictionary.search.yahoo.com/search?p=%s';

    public function __construct(private Client $client)
    {
        //
    }

    public function fetch(string $word): string
    {
        $response = $this->client->get(sprintf(self::URL, $word));

        return $response->getBody()->getContents();
    }
}
