<?php

namespace mCzolko\MemcachedBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class mCzolkoMemcachedExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('debug.yml');
        }

        $loader->load('services.yml');

        $definition = $container->getDefinition('memcached.client');

        if (isset($config['auth'])) {
            $username = $config['auth']['username'];
            $password = $config['auth']['password'];

            if ($username && $password) {
                $definition->addMethodCall('setAuthData', [$username, $password]);
            }
        }

        $servers = explode(',', $config['servers_string']);
        foreach ($servers as $s) {
            $definition->addMethodCall('addServer', explode(':', $s));
        }

        if ($container->hasDefinition('memcache.data_collector')) {
            $definition = $container->getDefinition('memcache.data_collector');
            $definition->addMethodCall('addClient', [new Reference('memcached.client')]);
        }
    }
}
