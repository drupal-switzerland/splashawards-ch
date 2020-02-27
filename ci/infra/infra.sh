#! /bin/bash

# Current working directory is: /builds/hostingplatform/skeletons-skeleton8/

cat /etc/hosts

# Fail on first error, so Gitlab sees this as a failed test.
set -e

apk add --no-cache mysql mysql-client rsync patch
mysql_install_db --user=root --basedir=/usr --datadir=/var/lib/mysql
mkdir -p /run/mysqld
nohup /usr/bin/mysqld --user=root --max_allowed_packet=1073741824 > /dev/null 2>&1 &

apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev > /dev/null 2>&1
docker-php-ext-install mysqli pdo_mysql gd > /dev/null 2>&1
 
git config --global user.email "test@dropsolid.com"
git config --global user.email "test"

# Copy test file
cp ./ci/infra/update_test.sh /builds/hostingplatform/skeletons-skeleton8/bash/updates/update_test.sh

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

cp ./ci/infra/settings.php /tmp/checkout/etc/drupal/settings_dev.php

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
PHP_OPTIONS="-d sendmail_path=`which true`" ../vendor/bin/drush site-install dropsolid_rocketship_profile --locale=en dropsolid_rocketship_profile_multilingual_configuration.enable_multilingual=1 dropsolid_rocketship_profile_multilingual_configuration.multilingual_languages.nl=nl dropsolid_rocketship_profile_multilingual_configuration.multilingual_languages.fr=fr dropsolid_rocketship_profile_extra_components.theme=dropsolid_starter dropsolid_rocketship_profile_extra_components.dcp_demo=1 dropsolid_rocketship_profile_extra_components.dropsolid_demo_content=1 dropsolid_rocketship_profile_extra_components.dropsolid_blog_advanced=1 dropsolid_rocketship_profile_extra_components.dropsolid_blocks=1 -y
../vendor/bin/drush d-set
popd

# Jenkins job
pushd /tmp/environment/bash/updates
bash ./update_test.sh
popd