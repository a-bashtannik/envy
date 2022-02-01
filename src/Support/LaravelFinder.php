<?php

declare(strict_types=1);

namespace Worksome\Envsync\Support;

use Illuminate\Support\LazyCollection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Worksome\Envsync\Contracts\Finder;

final class LaravelFinder implements Finder
{
    public function __construct(private array $config)
    {
    }

    public function configFilePaths(): array
    {
        return collect($this->config['config_files'])
            ->map(fn(string $path) => is_file($path) ? [$path] : $this->allFilesRecursively($path))
            ->flatten()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function allFilesRecursively(string $directory): array
    {
        return LazyCollection::make(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)))
            ->reject(fn($file) => $file->isDir())
            ->map(fn($file) => $file->getPathname())
            ->values()
            ->all();
    }

    public function environmentFilePaths(): array
    {
        return $this->config['environment_files'];
    }
}