<?php
      $db_name = 'skeleton8_db_dropsolid';
      $db_user = 'skeleton8_dropsolid';
      $db_pass = 'skeleton8_password_dropsolid';
      $db_host = 'mysql-dropsolid';
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

      $settings['hash_salt'] = '5cacb8072d7c243bb4bcfc977d2d91c9';
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
       $config['dropsolid_env'] = 'dev';
       
      /**
       * Load services definition file.
       */
      $settings['container_yamls'][] = __DIR__ . '/services.yml';

      /**
       *  Show install profile in reports page
       */
      $settings['install_profile'] = 'dropsolid_rocketship_profile';

      $config['dropsolid_purge.config'] = [
        'site_name' => "test",
        'site_environment' => "test",
        'site_group' => "DropsolidSolutions",
        'loadbalancers' => [
          'varnish' => [
            'ip' => '127.0.0.1',
            'protocol' => 'http',
            'port' => '80',
          ],
        ],
      ];

      if (file_exists(DRUPAL_ROOT . '/../etc/drupal/additional_settings.dev.php')) {
        include DRUPAL_ROOT . '/../etc/drupal/additional_settings.dev.php';
      }
