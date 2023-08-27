<?php

namespace Tests;

use App\Parser;
use Symfony\Component\DomCrawler\Crawler;

class ParserTest extends TestCase
{
    public function testParseWord()
    {
        $crawler = new Crawler(file_get_contents(__DIR__.'/fixtures/normal.html'));

        $this->assertEquals('test', (new Parser($crawler))->parseWord());
    }

    public function testParseWordWithNoResult()
    {
        $crawler = new Crawler(file_get_contents(__DIR__.'/fixtures/no-result.html'));

        $this->assertNull((new Parser($crawler))->parseWord());
    }

    public function testParsePronunciation()
    {
        $crawler = new Crawler(file_get_contents(__DIR__.'/fixtures/normal.html'));

        $this->assertEquals([
            ['type' => 'KK', 'value' => '[tɛst]'],
            ['type' => 'DJ', 'value' => '[test]'],
        ], (new Parser($crawler))->parsePronunciation());
    }

    public function testParsePronunciationWithSimpleExplanationOnly()
    {
        $crawler = new Crawler(file_get_contents(__DIR__.'/fixtures/simple-explanation-only.html'));

        $this->assertNull((new Parser($crawler))->parsePronunciation());
    }

    public function testParseSummary()
    {
        $crawler = new Crawler(file_get_contents(__DIR__.'/fixtures/normal.html'));

        $this->assertEquals([
            [
                'partOfSpeech' => 'n.[C]',
                'description' => '試驗；測試；化驗；化驗法；化驗劑',
            ], [
                'partOfSpeech' => 'vt.',
                'description' => '試驗；檢驗；測驗[（+for/in/on）]；化驗，分析[（+for）]',
            ], [
                'partOfSpeech' => 'vi.',
                'description' => '受試驗；受測驗；測得結果',
            ],
        ], (new Parser($crawler))->parseSummary());
    }

    public function testParseSummaryWithSimpleExplanationOnly()
    {
        $crawler = new Crawler(file_get_contents(__DIR__.'/fixtures/simple-explanation-only.html'));

        $this->assertEquals([
            ['description' => 'test的名詞複數'],
        ], (new Parser($crawler))->parseSummary());
    }

    public function testParseVariant()
    {
        $crawler = new Crawler(file_get_contents(__DIR__.'/fixtures/normal.html'));

        $this->assertEquals([
            ['type' => '名詞複數', 'value' => 'tests'],
            ['type' => '過去式', 'value' => 'tested'],
            ['type' => '過去分詞', 'value' => 'tested'],
            ['type' => '現在分詞', 'value' => 'testing'],
        ], (new Parser($crawler))->parseVariant());
    }

    public function testParseExplanation()
    {
        $crawler = new Crawler(file_get_contents(__DIR__.'/fixtures/normal.html'));

        $this->assertEquals([
            [
                'partOfSpeech' => ['english' => 'n.[C]', 'chinese' => '可數名詞'],
                'description' => [
                    [
                        'value' => '試驗；測試',
                        'example' => [
                            [
                                'english' => 'A simple test will show if this is real gold.',
                                'chinese' => '簡單的試驗就能證明這是否是真金。',
                            ],
                        ],

                    ], [
                        'value' => '化驗；化驗法；化驗劑',
                        'example' => [
                            [
                                'english' => 'He had a blood test.',
                                'chinese' => '他驗過血了。',
                            ],
                        ],
                    ], [
                        'value' => '檢驗；檢驗標準',
                    ], [
                        'value' => '測驗；考察；小考',
                        'example' => [
                            [
                                'english' => 'We are to have a history test next week.',
                                'chinese' => '下週我們有歷史測驗。',
                            ],
                        ],
                    ], [
                        'value' => '考驗',
                    ],
                ],
            ], [
                'partOfSpeech' => ['english' => 'vt.', 'chinese' => '及物動詞'],
                'description' => [
                    [
                        'value' => '試驗；檢驗；測驗[（+for/in/on）]',
                        'example' => [
                            [
                                'english' => 'The doctor tested his ears.',
                                'chinese' => '醫生檢查他的耳朵。',
                            ],
                            [
                                'english' => 'The teacher will test us in maths.',
                                'chinese' => '老師將測驗我們數學。',
                            ],
                        ],
                    ], [
                        'value' => '化驗，分析[（+for）]',
                    ], [
                        'value' => '考驗；考察',
                    ],
                ],
            ],
            [
                'partOfSpeech' => ['english' => 'vi.', 'chinese' => '不及物動詞'],
                'description' => [
                    [
                        'value' => '受試驗；受測驗',
                    ], [
                        'value' => '測得結果',
                    ], [
                        'value' => '（為鑑定而）進行測驗[（+for）]',
                    ],
                ],
            ],
        ], (new Parser($crawler))->parseExplanation());
    }
}
