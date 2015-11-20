<?php

namespace Lemmon;

function config($o = NULL)
{
    static $config;
    if (!isset($config) or isset($o)) {
        $json = json_decode(file_get_contents(@$o['file'] ?: 'config.json'), TRUE);
        if (json_last_error()) {
            throw new \Exception(sprintf('JSON: %s', json_last_error_msg()));
        }
        $config = [];
        if ('cli' === php_sapi_name() and $host = @$o['host'] ?: gethostname()) {
            // cli access
            if (isset($json['$'])) {
                $config = $json['$'];
            } elseif (isset($json['*'])) {
                $config = $json['*'];
            }
            foreach ($json as $_host => $_config) {
                if ('$' === $_host{0}) {
                    if (preg_match('#^' . strtr(substr($_host, 1), ['.'=>'\.','*'=>'.*']) . '$#', $host)) {
                        $config = array_replace_recursive($config, $_config);
                    }
                }
            }
        } elseif ($host = @$o['host'] ?: $_SERVER['SERVER_NAME']) {
            // http access
            foreach ($json as $_host => $_config) {
                if (preg_match('#^' . strtr($_host, ['.' => '\.', '*' => '.*']) . '$#', $host)) {
                    $config = array_replace_recursive($config, $_config);
                }
            }
        }
    }
    return $config;
}