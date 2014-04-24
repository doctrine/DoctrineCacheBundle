<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Check if a cache entry exists.
 */
class ContainsCommand extends CacheCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('doctrine-cache:contains')
            ->setDescription('Check if a cache entry exists')
            ->addArgument('cache-name', InputArgument::REQUIRED, 'Which cache provider to use?')
            ->addArgument('cache-id', InputArgument::REQUIRED, 'Which cache ID to check?');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cacheName     = $input->getArgument('cache-name');
        $cacheProvider = $this->getCacheProvider($cacheName);
        $cacheId       = $input->getArgument('cache-id');

        if ($cacheProvider->contains($cacheId)) {
            $output->writeln('<info>TRUE</info>');
        } else {
            $output->writeln('<error>FALSE</error>');
        }
    }
}
