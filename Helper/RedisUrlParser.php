<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Helper;

/**
 * Redis Url Parser
 *
 * @author David Gerardin <davidgerah@gmail.com>
 */
class RedisUrlParser
{
    /**
     * Extracts parts from the URL in config (if present), updates the config and returns it
     *
     * @param array $config
     *
     * @return array
     */
    public static function parse(array $config): array
    {
        if ( ! isset($config['url'])) {
            return $config;
        }

        $url = parse_url($config['url']);
        $url = array_map('rawurldecode', $url);

        if (isset($url['host'])) {
            $config['host'] = $url['host'];
        }

        if (isset($url['port'])) {
            $config['port'] = $url['port'];
        }

        if (isset($url['user'])) {
            $config['password'] = $url['user'];
        }

        if (isset($url['path']) && strlen($url['path']) >= 2) {
            $config['database'] = substr($url['path'], 1);
        }

        return $config;
    }
}
