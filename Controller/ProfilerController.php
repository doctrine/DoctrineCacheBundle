<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

/**
 * ProfilerController.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class ProfilerController extends ContainerAware
{
    /**
     * Renders the profiler panel for the given token.
     *
     * @param string  $token       The profiler token.
     * @param string  $cacheName   Cache provider name.
     * @param string  $requestType Type of cache request.
     * @param integer $logIndex    Log index.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dumpAction($token, $cacheName, $requestType, $logIndex)
    {
        $profiler = $this->container->get('profiler');

        $profiler->disable();

        $profile = $profiler->loadProfile($token);
        $logs    = $profile->getCollector('doctrine_cache')->getLogs();

        if ( ! isset($logs[$cacheName][$requestType][$logIndex])) {
            return new Response('No cache log found.');
        }

        $log = $logs[$cacheName][$requestType][$logIndex];

        if ( ! isset($log['data'])) {
            return new Response('No cache data logged.');
        }

        $templating = $this->container->get('templating');

        return $templating->renderResponse(
            'DoctrineCacheBundle:Profiler:dump.html.twig',
            array(
                'data' => $log['data'],
            )
        );
    }
}
