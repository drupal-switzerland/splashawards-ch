#! /bin/bash

# Fail on first error, so Gitlab sees this as a failed test.
set -e

# general.yml
composer install

mkdir -p /tmp/repo /tmp/environment
pushd /tmp/repo
git init --bare
popd

git clone /tmp/repo /tmp/checkout
rsync -az --rsh="/usr/bin/ssh -S none -o StrictHostKeyChecking=no" --ignore-times --omit-dir-times --no-perms --exclude=ci --exclude=.gitlab-ci.yml --exclude=.git --exclude=auth.json --exclude=README.txt ./ /tmp/checkout/
cp ./etc/.gitignore /tmp/checkout/.gitignore

pushd /tmp/checkout
git add .
# made the commit quiet, so the output fits in gitlab-ci log.
git commit -q -am 'initial commit'
git push origin master
popd

cp ./ci/settings.php /tmp/checkout/etc/drupal/settings_dev.php

pushd /tmp/checkout
git add .
git commit -q -am 'Created settings.php files'
git push origin master
popd

# drupal.yml
# removed the verbose flag to limit the output, so it fits in gitlab-ci log.
rsync -az --rsh="/usr/bin/ssh -S none -o StrictHostKeyChecking=no" --ignore-times --omit-dir-times --no-perms --exclude=.git --exclude=./etc --exclude=./config --exclude=./bash /tmp/checkout/ /tmp/environment/

cp /tmp/checkout/etc/drupal/settings_dev.php /tmp/environment/docroot/sites/default/settings.php

# drupal_install.yml
pushd /tmp/environment/docroot
PHP_OPTIONS="-d sendmail_path=`which true`" ../vendor/bin/drush site-install standard --locale=en -y
popd

# Jenkins job
pushd /tmp/environment/bash/updates
bash ./update_dev.sh
popd