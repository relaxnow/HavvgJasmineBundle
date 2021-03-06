<?php

namespace Havvg\Bundle\JasmineBundle\Routing;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

class CacheClearer implements CacheClearerInterface
{
    protected $directory;

    public function clear($cacheDir)
    {
        // $cacheDir refers to the application, it's not in use by the routing

        if ($directory = realpath($this->getDirectory())) {
            $fs = new Filesystem();
            $fs->remove(Finder::create()->in($directory));
        }
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    public function getDirectory()
    {
        return $this->directory;
    }
}
