<?php

function config($input = NULL)
{
    static $config;
    if (!isset($config) or isset($input)) {
        $config = [];
        foreach (json_decode(file_get_contents('config.json'), TRUE) as $_host => $_config) {
            if (preg_match('#^' . strtr($_host, ['.' => '\.', '*' => '.*']) . '$#', $_SERVER['SERVER_NAME'])) {
                $config = array_replace_recursive($config, $_config);
            }
        }
        if (json_last_error()) {
            throw new \Exception(sprintf('JSON: %s', json_last_error_msg()));
        }
    }
    return $config;
}