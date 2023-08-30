<?php

use App\Command;
use Symfony\Component\Console\Input\ArgvInput;

define('ABS_BASE', realpath(__DIR__.'/..'));

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

        $files = array_unique(array_merge(
            $this->getIncludedFiles(),
            $this->getFilesFromDirectory(__DIR__.'/../vendor/symfony/console/Resources')
        ));

        foreach ($files as $file) {
            if ($file === __FILE__) {
                continue;
            }

            $phar->addFile($file, $this->pathToLocalName($file));
        }

        $phar->setStub(file_get_contents(__DIR__.'/ydict.php.stub'));

        $phar->stopBuffering();

        chmod($this->filepath, 0755);
    }

    private function getFilesFromDirectory(string $path): array
    {
        $files = [];

        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDir()) {
                continue;
            }

            $files[] = realpath($file->getPathname());
        }

        return $files;
    }

    private function pathToLocalName(string $path): string
    {
        return str_replace(ABS_BASE.'/', '', $path);
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

        $command->setAutoExit(false);
        $command->run(new ArgvInput(['ydict.php']));
        $command->run(new ArgvInput(['ydict.php', 'test']));
        $command->run(new ArgvInput(['ydict.php', 'tests']));
        $command->run(new ArgvInput(['ydict.php', 'testss']));

        return get_included_files();
    }
}

(new Builder())->build();
