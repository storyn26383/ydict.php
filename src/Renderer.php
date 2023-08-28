<?php

namespace App;

use Exception;
use Symfony\Component\Console\Output\OutputInterface;

class Renderer
{
    private array $cache = [];

    public function __construct(private Parser $parser, private OutputInterface $output)
    {
        //
    }

    public function render(): void
    {
        if (! $this->hasResult()) {
            throw new NoResultException;
        }

        if ($this->hasPronunciation()) {
            $this->renderPronunciation();
            $this->print();
        }

        if (! $this->output->isVerbose()) {
            $this->renderSummary();

            return;
        }

        $this->renderExplanation();
        $this->print();

        $this->renderVariant();
    }

    private function renderPronunciation(): void
    {
        $pronunciation = $this->get('pronunciation');

        if ($pronunciation) {
            $this->print(implode(' ', array_map(fn ($summary) => "{$summary['type']} <options=bold>{$summary['value']}</>", $pronunciation)));
        }
    }

    private function renderSummary(): void
    {
        foreach ($this->get('summary') as $summary) {
            $message = $summary['description'];

            if (isset($summary['partOfSpeech'])) {
                $message = "<bg=blue>{$summary['partOfSpeech']}</> {$message}";
            }

            $this->print($message);
        }
    }

    private function renderExplanation(): void
    {
        foreach ($this->get('explanation') as $index => $explanation) {
            if ($index > 0) {
                $this->print();
            }

            $this->print("<bg=blue>{$explanation['partOfSpeech']['english']}</> {$explanation['partOfSpeech']['chinese']}");

            foreach ($explanation['description'] as $index => $description) {
                $no = $index + 1;
                $this->print("  <options=bold>{$no}.</> {$description['value']}");

                if (isset($description['example'])) {
                    foreach ($description['example'] as $example) {
                        $this->print("     <fg=gray>{$example['english']} {$example['chinese']}</>");
                    }
                }
            }
        }
    }

    private function renderVariant(): void
    {
        foreach ($this->get('variant') as $variant) {
            $this->print("{$variant['type']}：<fg=red>{$variant['value']}</>");
        }
    }

    private function hasResult(): bool
    {
        return $this->get('word') !== null;
    }

    private function hasPronunciation(): bool
    {
        return $this->get('pronunciation') !== null;
    }

    private function get(string $key): mixed
    {
        if (! isset($this->cache[$key])) {
            $this->cache[$key] = $this->getFromParser($key);
        }

        return $this->cache[$key];
    }

    private function getFromParser(string $key): mixed
    {
        if ($key === 'word') {
            return $this->parser->parseWord();
        }

        if ($key === 'pronunciation') {
            return $this->parser->parsePronunciation();
        }

        if ($key === 'summary') {
            return $this->parser->parseSummary();
        }

        if ($key === 'variant') {
            return $this->parser->parseVariant();
        }

        if ($key === 'explanation') {
            return $this->parser->parseExplanation();
        }

        throw new Exception("Unsupport key: {$key}");
    }

    private function print(string $message = ''): void
    {
        $this->output->writeln($message);
    }
}
