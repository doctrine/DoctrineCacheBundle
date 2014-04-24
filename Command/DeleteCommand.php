<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete cache entries.
 */
class DeleteCommand extends CacheCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('doctrine-cache:delete')
            ->setDescription('Delete a cache entry')
            ->addArgument('cache-name', InputArgument::REQUIRED, 'Which cache provider to use?')
            ->addArgument('cache-id', InputArgument::OPTIONAL, 'Which cache ID to delete?')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Delete all cache entries in provider');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cacheName     = $input->getArgument('cache-name');
        $cacheProvider = $this->getCacheProvider($cacheName);
        $cacheId       = $input->getArgument('cache-id');

        if ($input->getOption('all')) {
            if (method_exists($cacheProvider, 'deleteAll')) {
                $success = $cacheProvider->deleteAll();
                $color   = $success ? 'info' : 'error';
                $success = $success ? 'succeeded' : 'failed';
                $message = "Deletion of <$color>all</$color> entries in <$color>%s</$color> has <$color>%s</$color>";
                $output->writeln(sprintf($message, $cacheName, $success, true));
            } else {
                throw new \RuntimeException('Cache provider does not implement a deleteAll method.');
            }
        } else {
            if ($cacheId) {
                $success = $cacheProvider->delete($cacheId);
                $color   = $success ? 'info' : 'error';
                $success = $success ? 'succeeded' : 'failed';
                $message = "Deletion of <$color>%s</$color> in <$color>%s</$color> has <$color>%s</$color>";
                $output->writeln(sprintf($message, $cacheId, $cacheName, $success, true));
            } else {
                throw new \InvalidArgumentException('Missing cache ID');
            }
        }
    }
}
