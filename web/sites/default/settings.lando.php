<?php

if (getenv('LANDO_INFO')) {
    $lando_info = json_decode(getenv('LANDO_INFO'), TRUE);
    $databases['default']['default'] = [
        'driver' => 'mysql',
        'database' => $lando_info['database']['creds']['database'],
        'username' => $lando_info['database']['creds']['user'],
        'password' => $lando_info['database']['creds']['password'],
        'host' => $lando_info['database']['internal_connection']['host'],
        'port' => $lando_info['database']['internal_connection']['port'],
    ];
}

$settings['container_yamls'][] = 'sites/lando.services.yml';

// Local Configuration
$config['system.performance']['cache']['page']['use_internal'] = FALSE;
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['css']['gzip'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$config['system.performance']['js']['gzip'] = FALSE;
$config['system.performance']['response']['gzip'] = FALSE;
$config['views.settings']['ui']['show']['sql_query']['enabled'] = TRUE;
$config['views.settings']['ui']['show']['performance_statistics'] = TRUE;
$config['system.logging']['error_level'] = 'all';

// Config Split
$config['config_split.config_split.local_dev']['status'] = TRUE;
$config['config_split.config_split.dev']['status'] = FALSE;
$config['config_split.config_split.test']['status'] = FALSE;
$config['config_split.config_split.prod']['status'] = FALSE;
