<?php

/*
 * This file is part of the micro edition package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class MicroKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @inheritdoc
     */
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new DebugBundle();
            $bundles[] = new WebProfilerBundle();
        }

        return $bundles;
    }

    /**
     * The index route of the app.
     */
    public function index()
    {
        return new Response(sprintf('From micro kernel'));
    }

    /**
     * @inheritdoc
     */
    public function getCacheDir()
    {
        return $this->getLocalRootDir() . 'cache/' . $this->getEnvironment();
    }

    /**
     * Shortcut for creating cache and log directory
     *
     * @return string
     */
    protected function getLocalRootDir()
    {
        return $this->getRootDir() . '/../var/';
    }

    /**
     * @inheritdoc
     */
    public function getLogDir()
    {
        return $this->getLocalRootDir() . 'logs';
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.xml', '/_wdt');
            $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.xml', '/_profiler');
        }

        $routes->add('/', 'kernel:index');

    }

    /**
     * @inheritdoc
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => '12345',
            'profiler' => false,
            'templating' => ['engines' => ['twig']],
            'session' => [
                'storage_id' => 'session.storage.filesystem',
                'handler_id' => 'session.handler.native_file',
            ]
        ]);

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $c->loadFromExtension('web_profiler', [
                'toolbar' => true
            ]);

            $c->loadFromExtension('framework', [
                'profiler' => true,
            ]);
        }
    }


}
