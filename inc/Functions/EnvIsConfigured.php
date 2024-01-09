<?php

 /**
  * Checks if the environment is already configured and installed.
  * This means, for example, translations loaded to the database.
  */
function EnvIsConfigured(): bool
{
    global $config;

    $result = false;

    if (!array_key_exists('environment', $config)) {
        return $result;
    }

    if (!array_key_exists('configured', $config['environment'])) {
        return $result;
    }

    if (empty($config['environment']['configured'])) {
        return $result;
    }

    return true;
}
