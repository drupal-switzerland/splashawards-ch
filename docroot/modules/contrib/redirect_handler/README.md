Redirect Handler
-------------

A drupal 8 module which enables you to redirects from a csv.
The csv has to be in the format:
1) Source url
2) Redirect url
3) Language (optional)

INSTALLATION
------------

* Download from gitlab and copy to /modules/contrib/
* Enable with drush: drush en redirect_handler -y

CONFIGURATION
-------------

Copy your csv to /sites/default/redirect/ and rename it to sources.csv
Or use the upload form at: /admin/config/search/redirect/import
