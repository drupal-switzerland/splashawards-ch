#! /bin/bash

# Current working directory is: /builds/hostingplatform/skeletons-skeleton8/

# Fail on first error, so Gitlab sees this as a failed test.
set -e

git config --global user.email "test@dropsolid.com"
git config --global user.email "test"

# general.yml
composer install

cp ./ci/unit/settings.php ./docroot/sites/default/settings.php
cp ./ci/unit/phpunit.xml ./docroot/core/phpunit.xml

# Start server
pushd docroot
# Some unit tests seem to expect an installed site?
../vendor/bin/drush si -y
php -S localhost:80 .ht.router.php &

# Create a folder for the output to be written.
mkdir -p ./sites/simpletest/browser_output
popd

# Disable errors, else phpunit failures will abort the entire script
# Including the cp to the artifacts folder
set +e

pushd docroot/core
../../vendor/bin/phpunit --exclude-group "Composer,Setup" --testsuite=unit --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" --log-junit="/builds/hostingplatform/skeletons-skeleton8/artifacts/junit-report.xml"
TEST_STATUS=$?
popd

# Copy the browser output to a place GitLab CI gets its artifacts from.
cp -R /builds/hostingplatform/skeletons-skeleton8/docroot/sites/simpletest/browser_output/ artifacts/browser_output/

exit $TEST_STATUS