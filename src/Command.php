<?php

namespace App;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\DomCrawler\Crawler;

class Command extends SingleCommandApplication
{
    protected function configure(): void
    {
        $this
            ->setVersion('1.0.2')
            ->addArgument('word', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $word = $input->getArgument('word');

        $fetcher = new Fetcher;
        $crawler = new Crawler($fetcher->fetch($word));
        $parser = new Parser($crawler);
        $renderer = new Renderer($parser, $output);

        try {
            $renderer->render();
        } catch (NoResultException $e) {
            $output->writeln('<error>很抱歉，字典找不到您要的資料喔！</>');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
