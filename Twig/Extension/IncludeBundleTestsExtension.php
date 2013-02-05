<?php

namespace Havvg\Bundle\JasmineBundle\Twig\Extension;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Twig_Extension;
use Twig_Function_Method as TwigMethod;

/**
 * Twig extensions for the publication bundle. Exposes a new twig method:
 *  - <script>{{ includeBundleTests('Resources/js/spec', 'OptionalBundleName') }}</script>
 *      Recursively concatenates all *.js files in the given subdir of all bundles.
 *      Optional second parameter allows you to filter out only a single Bundle
 *
 * @author Boy Baukema <boy@ibuildings.nl>
 */
class IncludeBundleTestsExtension extends Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    private $kernel;

    public function __construct(\AppKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @inheritDoc
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return array(
            'includeBundleTests' => new TwigMethod(
                $this,
                'includeBundleTests',
                array('is_safe' => array('js'))
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'jasminebundle_includetests';
    }

    /**
     * Renders the list of versions including the publish button
     *
     * @param array  $list
     * @param string $publicationRouteName
     * @param string $viewRouteName
     * @param string $archiveRouteName
     *
     * @return string
     */
    public function includeBundleTests($directory, $bundleFilter = '')
    {
        $testsContent = "";
        $enabledBundles = $this->kernel->getBundles();
        /** @var $bundle Bundle */
        foreach ($enabledBundles as $bundleName => $bundle) {
            if ($bundleFilter && $bundleFilter !== $bundleName) {
                continue;
            }

            $bundlePath = $bundle->getPath();
            $directoryPath = $bundlePath . DIRECTORY_SEPARATOR . $directory;
            if (!file_exists($directoryPath)) {
                continue;
            }

            $files = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($directoryPath)),
                '#\.js$#i'
            );
            foreach ($files as $file) {
                $testsContent .= PHP_EOL . file_get_contents($file);
            }
        }
        echo $testsContent;
    }
}