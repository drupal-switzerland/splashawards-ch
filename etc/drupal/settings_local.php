<?php
      $db_name = 'splashawardsswiss_local';
      $db_user = 'admin';
      $db_pass = 'YotxXkyrchFZTiG9hrpSBVpQnhQtBh';
      $db_host = 'localhost';
      $db_port = '';
      $db_driver = 'mysql';
      $db_prefix = '';

      $databases = array (
        'default' =>
        array (
          'default' =>
          array (
            'database' => $db_name,
            'username' => $db_user,
            'password' => $db_pass,
            'host' => $db_host,
            'port' => $db_port,
            'driver' => $db_driver,
            'prefix' => $db_prefix,
          ),
        ),
      );

      $update_free_access = FALSE;


      $settings['hash_salt'] = 'bc27fa1c8b5184d16fee28f6870d287d';
      $config_directories['sync'] = '../config/sync';

      ini_set('session.gc_probability', 1);
      ini_set('session.gc_divisor', 100);
      ini_set('session.gc_maxlifetime', 200000);
      ini_set('session.cookie_lifetime', 2000000);

      /**
       * Fast 404 pages:
       */
       $config['system.performance']['fast_404']['exclude_paths'] = '/\/(?:styles)|(?:system\/files)\//';
       $config['system.performance']['fast_404']['paths'] = '/\.(?:txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp)$/i';
       $config['system.performance']['fast_404']['html'] = '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server.</p></body></html>';
       $config['dropsolid_env'] = 'local';

      /**
       * Load services definition file.
       */
      $settings['container_yamls'][] = __DIR__ . '/services.yml';

      /**
       *  Show install profile in reports page
       */
      $settings['install_profile'] = 'standard';

      /**
       * Environment indicator settings
       */
      $config['environment_indicator.indicator']['name'] = 'Local';
      $config['environment_indicator.indicator']['bg_color'] = '#0F0';
      $config['environment_indicator.indicator']['fg_color'] = '#FFF';

      if (file_exists(DRUPAL_ROOT . '/../etc/drupal/additional_settings.local.php')) {
        include DRUPAL_ROOT . '/../etc/drupal/additional_settings.local.php';
      }