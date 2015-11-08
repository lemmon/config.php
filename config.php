<?php

function config($input = NULL)
{
    static $config;
    if (!isset($config) or isset($input)) {
        $json = json_decode(file_get_contents('config.json'), TRUE);
        $config = [];
        if (isset($_SERVER['SERVER_NAME'])) {
            // web access
            foreach ($json as $_host => $_config) {
                if (preg_match('#^' . strtr($_host, ['.' => '\.', '*' => '.*']) . '$#', $_SERVER['SERVER_NAME'])) {
                    $config = array_replace_recursive($config, $_config);
                }
            }
        } elseif ($hostname = gethostname()) {
            // cli access
            if (isset($json['*'])) {
                $config = $json['*'];
            }
            foreach ($json as $_host => $_config) {
                if ('$' === $_host{0}) {
                    if (preg_match('#^' . strtr(substr($_host, 1), ['.'=>'\.','*'=>'.*']) . '$#', $hostname)) {
                        $config = array_replace_recursive($config, $_config);
                    }
                }
            }
        }
        if (json_last_error()) {
            throw new \Exception(sprintf('JSON: %s', json_last_error_msg()));
        }
    }
    return $config;
}