<?php

namespace App;

class Fetcher
{
    private const URL = 'https://tw.dictionary.search.yahoo.com/search?p=%s';

    public function fetch(string $word): string
    {
        return file_get_contents(sprintf(self::URL, $word));
    }
}
