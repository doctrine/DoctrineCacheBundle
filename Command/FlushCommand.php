<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Cache\Cache;

/**
 * Flush a cache provider.
 */
class FlushCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('doctrine-cache:flush')
            ->setAliases(array('doctrine-cache:clear'))
            ->setDescription('Flush a given cache')
            ->addArgument('cache-name', InputArgument::REQUIRED, 'Which cache to flush?');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $cacheName = $input->getArgument('cache-name');

        // Try to use user input as cache service alias.
        $cacheProvider = $container->get($cacheName, ContainerInterface::NULL_ON_INVALID_REFERENCE);

        // If cache provider was not found try the service provider name.
        if (!$cacheProvider instanceof Cache) {
            $cacheProvider = $container->get('doctrine_cache.providers.' . $cacheName, ContainerInterface::NULL_ON_INVALID_REFERENCE);
        }
        // Cache provider was not found.
        if (!$cacheProvider instanceof Cache) {
            throw new \InvalidArgumentException('Cache provider not found.');
        }

        if (method_exists($cacheProvider, 'flushAll')) {
            $cacheProviderName = get_class($cacheProvider);
            $output->writeln(sprintf('Clearing the cache for the <info>%s</info> provider of type <info>%s</info>',$cacheName, $cacheProviderName, true));
            $cacheProvider->flushAll();
        } else {
            throw new \RuntimeException('Cache provider does not implement a flushAll method.');
        }
    }
}
