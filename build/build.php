<?php

use App\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

require __DIR__.'/../vendor/autoload.php';

class Builder
{
    private string $filepath;

    public function __construct(private string $filename = 'ydict.php.phar')
    {
        $this->filepath = __DIR__."/{$filename}";
    }

    public function build(): void
    {
        $this->buildPharFile();

        echo "$this->filepath was successfully built.".PHP_EOL;
    }

    private function buildPharFile(): void
    {
        $this->clean();

        $phar = new Phar($this->filepath);

        $phar->startBuffering();

        foreach ($this->getIncludedFiles() as $file) {
            if ($file === __FILE__) {
                continue;
            }

            $phar->addFile($file);
        }

        $phar->setStub(file_get_contents(__DIR__.'/ydict.php.stub'));

        $phar->stopBuffering();

        chmod($this->filepath, 0755);
    }

    private function clean(): void
    {
        if (file_exists($this->filepath)) {
            unlink($this->filepath);
        }
    }

    private function getIncludedFiles(): array
    {
        $command = new Command('ydict.php');
        $output = new NullOutput;

        $command->setAutoExit(false);
        $command->run(new ArgvInput(['ydict.php']), $output);
        $command->run(new ArgvInput(['ydict.php', 'test']), $output);
        $command->run(new ArgvInput(['ydict.php', 'tests']), $output);
        $command->run(new ArgvInput(['ydict.php', 'testss']), $output);

        return get_included_files();
    }
}

(new Builder())->build();
