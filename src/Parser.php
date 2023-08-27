<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler;

class Parser
{
    public function __construct(private Crawler $crawler)
    {
        //
    }

    public function parseWord(): ?string
    {
        $node = $this->crawler->filter('.dictionaryWordCard .compTitle .title span');

        if (! $node->count()) {
            return null;
        }

        return $node->text();
    }

    public function parsePronunciation(): ?array
    {
        $nodes = $this->crawler->filter('.dictionaryWordCard .compList');

        if ($nodes->count() === 1) {
            return null;
        }

        return $nodes->first()->filter('span')->each(function (Crawler $node) {
            [$type, $value] = preg_split('/(?=[^a-zA-Z])/', $node->text(), 2);

            return compact('type', 'value');
        });
    }

    public function parseSummary(): array
    {
        return $this->crawler->filter('.dictionaryWordCard .compList')->last()->filter('li')->each(function (Crawler $node) {
            $exploded = explode(' ', $node->text(), 2);

            if (count($exploded) === 1) {
                return ['description' => $exploded[0]];
            }

            return [
                'partOfSpeech' => $exploded[0],
                'description' => $exploded[1],
            ];
        });
    }

    public function parseVariant(): array
    {
        return array_reduce($this->crawler->filter('.dictionaryWordCard .compArticleList h4')->each(function (Crawler $node) {
            return array_map(function ($string) {
                [$type, $value] = explode('：', $string);

                return compact('type', 'value');
            }, explode(' ', $node->text()));
        }), function ($carry, $item) {
            return array_merge($carry, $item);
        }, []);
    }

    public function parseExplanation(): array
    {
        $partOfSpeech = $this->crawler->filter('.tab-content-explanation .compTitle')->each(function (Crawler $node) {
            return [
                'english' => $node->filter('label span')->text(),
                'chinese' => $node->filter('h3 span')->text(),
            ];
        });

        $description = $this->crawler->filter('.tab-content-explanation .compTextList')->each(function (Crawler $node) {
            return $node->filter('li')->each(function (Crawler $node) {
                $value = $node->filter('span')->eq(1)->text();
                $exampleNodes = $node->filter('span')->slice(2);

                if (! $exampleNodes->count()) {
                    return compact('value');
                }

                $example = $exampleNodes->each(function (Crawler $node) {
                    [$english, $chinese] = array_map('trim', preg_split('/(?=[^\x00-\x7F])/', $node->text(), 2));

                    return compact('english', 'chinese');
                });

                return compact('value', 'example');
            });
        });

        return array_map(function ($partOfSpeech, $description) {
            return compact('partOfSpeech', 'description');
        }, $partOfSpeech, $description);
    }
}
