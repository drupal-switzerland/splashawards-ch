# Before installing

- [Before installing](#before-installing)
  - [Composer](#composer)
  - [Drush](#drush)
  - [Adding modules](#adding-modules)
  - [Apply patches to drupal core, modules, profiles or themes.](#apply-patches-to-drupal-core-modules-profiles-or-themes)
  - [Update drupal core](#update-drupal-core)
  - [Locked modules](#locked-modules)

## Composer

This skeleton works with composer.json
If you have gotten a project from Dropsolid (using the local installer, 
launchpad or some other way), you won't have to do anything with composer.

If you are making a site from scratch, you will need to run 
`composer install --no-dev` to get all the modules, libraries, … needed to begin.

If you just want to update the dependencies of the project, you can run 
`composer update --with-dependencies --no-dev`

It is **VERY IMPORTANT** that you do not commit any dev dependencies to the 
git repo. These pose a security risk if they wind up on live environments. If 
you are unsure if there are dev dependencies in your repo, simply run 
`composer install --no-dev`. If there are any, they will be removed.

## Drush

Drush comes bundled with the skeleton, always use that version. You can use 
the bundled drush by using its path: `../vendor/bin/drush [drush commands]` 
will use the project specific version when you are just inside docroot.

Because this can get tiresome quickly, we recommend that you install [drush 
launcher](https://github.com/drush-ops/drush-launcher). This will allow you to 
call drush as you normally would, and the launcher will use the bundled drush 
version if present, and if not you can set a fallback drush version to use.
Set that fallback version to your global drush 8 installation (drush 8 is 
fully compatible with drupal 7 _and_ 8).

## Adding modules

To add a new module to the skeleton you need to use the following command:

```
composer require drupal/module-name --update-no-dev
```

e.g:

```
composer require drupal/ctools --update-no-dev
```

## Apply patches to drupal core, modules, profiles or themes.

Add a new "patches" line in the "extra" section of the composer.json file,
e.g: Example patching admin_toolbar module

```
"patches": {
  "drupal/admin_toolbar": {
    "Go to overview instead of taxonomy admin page": "https://www.drupal.org/files/issues/default_link_taxonomy-2518202-1.patch"
  }
}
```

## Update drupal core

Use `composer update --with-dependencies --no-dev` to update to the latest 
drupal core.
We use the drupal-scaffold plugin to update the drupal root files:

- https://github.com/drupal-composer/drupal-scaffold

The skeleton is configured to update within the same minor release. A manual
action is required to update Drupal core from, eg, 8.5.x to 8.6.x

## Locked modules

Certain modules are currently locked and should not be updated unless the 
following criteria have been met.

- Page Manager: v4.0.0-beta3
  - see patches in composer.json
- All beta, RC and alpha releases are locked. If you need to update them, do 
so at your own risk and make sure to test thoroughly!
