<?php

namespace AdrienBrault\UML\Finder;

use Symfony\Component\Finder\Finder as SymfonyFinder;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Finder extends SymfonyFinder
{
    public function __construct(array $dirs)
    {
        parent::__construct();

        $this
            ->files()
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->exclude('vendor')
            ->in($dirs)
        ;
    }
}
