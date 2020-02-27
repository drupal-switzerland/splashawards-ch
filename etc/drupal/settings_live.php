<?php
      $db_name = 'splashawardsswiss_live';
      $db_user = 'splashawardsswiss';
      $db_pass = 'y3Nm7BaJaSCdwaPUfVjv';
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


      $settings['hash_salt'] = '936413f7c0dd6d64317d8706b1a8134d';
      $config_directories['sync'] = DRUPAL_ROOT . '/../config/sync';

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
       $config['dropsolid_env'] = 'live';
       
      /**
       * Load services definition file.
       */
      $settings['container_yamls'][] = __DIR__ . '/services.yml';

      /**
       *  Show install profile in reports page
       */
      $settings['install_profile'] = 'standard';




      // BEGIN reverse proxy setting added by Dropsolid infrastructure, DO NOT EDIT
      // Tell Drupal that we are behind a reverse proxy server
      $settings['reverse_proxy'] = TRUE;

      // List of trusted IPs (IP numbers of our reverse proxies)
      $settings['reverse_proxy_addresses'] = ['127.0.0.1', '104.199.104.19', '35.195.110.3'];
      // END reverse proxy setting added by Dropsolid infrastructure, DO NOT EDIT

      if (file_exists(DRUPAL_ROOT . '/../etc/drupal/additional_settings.live.php')) {
        include DRUPAL_ROOT . '/../etc/drupal/additional_settings.live.php';
      }
