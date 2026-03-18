<?php

$settings['config_sync_directory'] = '../config/default';

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all environments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Skipping permissions hardening will make scaffolding
 * work better, but will also raise a warning when you
 * install Drupal.
 *
 * https://www.drupal.org/project/drupal/issues/3091285
 */
// $settings['skip_permissions_hardening'] = TRUE;

/**
 * Override System Performance Settings Per Pantheon Environment
 *
 * @see https://docs.pantheon.io/guides/environment-configuration/environment-specific-config-drupal#override-system-performance-settings-per-environment
 * */
if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
    switch($_ENV['PANTHEON_ENVIRONMENT']) {
        case 'live':
        case 'test':
            $config['system.performance']['cache']['page']['use_internal'] = TRUE;
            $config['system.performance']['css']['preprocess'] = TRUE;
            $config['system.performance']['css']['gzip'] = TRUE;
            $config['system.performance']['js']['preprocess'] = TRUE;
            $config['system.performance']['js']['gzip'] = TRUE;
            $config['system.performance']['response']['gzip'] = TRUE;
            $config['views.settings']['ui']['show']['sql_query']['enabled'] = FALSE;
            $config['views.settings']['ui']['show']['performance_statistics'] = FALSE;
            $config['system.logging']['error_level'] = 'none';
            break;
        case 'dev':
        default :
            $config['system.performance']['cache']['page']['use_internal'] = FALSE;
            $config['system.performance']['css']['preprocess'] = FALSE;
            $config['system.performance']['css']['gzip'] = FALSE;
            $config['system.performance']['js']['preprocess'] = FALSE;
            $config['system.performance']['js']['gzip'] = FALSE;
            $config['system.performance']['response']['gzip'] = FALSE;
            $config['views.settings']['ui']['show']['sql_query']['enabled'] = TRUE;
            $config['views.settings']['ui']['show']['performance_statistics'] = TRUE;
            $config['system.logging']['error_level'] = 'all';
            # $settings['cache']['bins']['render'] = 'cache.backend.null';
            # $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
            break;
    }
}

/**
 * If there is a lando settings file, then include it
 */
$lando_settings = __DIR__ . "/settings.lando.php";
if (getenv('LANDO_INFO') && file_exists($lando_settings)) {
    include $lando_settings;
}

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}
