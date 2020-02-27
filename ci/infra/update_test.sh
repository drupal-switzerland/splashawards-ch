#!/bin/sh
SCRIPT_DIR="$(cd -- "$(dirname "$0")"; pwd -P)"

DRUSH="$SCRIPT_DIR/../../vendor/bin/drush"
DRUPAL="$SCRIPT_DIR/../../vendor/bin/drupal"

if [ -f ${DRUSH} ]; then
  VERSION=$(${DRUSH} --version)
  echo "Using project-specific drush library"
else
  DRUSH=""
  echo "No project specific drush found, checking global version"
  if [ -e $(command -v drush8) ]; then
    DRUSH="drush8"
  elif [ -e $(command -v drush) ]; then
    DRUSH="drush"
  fi
fi

if [ -n ${DRUSH} ]; then
  VERSION=$(${DRUSH} --version --pipe)
  echo "Drush version: ${VERSION}"
else
    echo "Drush not found! exit"
  exit 1
fi

DOCROOT="$SCRIPT_DIR/../../docroot"

cd $DOCROOT

# D8

# Update site
${DRUSH} updb -y

# Clear caches
${DRUSH} cr

# Import configuration overrides if there is any config yet.
CONFIG_DIR=$(${DRUSH} st --format=list --no-field-labels config-sync)
if [ -d $CONFIG_DIR ] && [ 0 -lt $(ls ${CONFIG_DIR}/*.yml 2>/dev/null | wc -w) ]; then
  # Ensure the ignored config is up to date.
  ${DRUPAL} config:import:single --file=../config/sync/config_ignore.settings.yml
#  ${DRUPAL} config:import:single --file=../config/splits/dev/config_ignore_collection.settings.yml
  # Ensure CMI knows about the split.
  ${DRUPAL} config:import:single --file=../config/sync/config_split.config_split.dev.yml
  ${DRUSH} cr
  # Single cim should work without issues
  ${DRUSH} cim -y
else
  echo "Skipping config import, as no config exists yet."
fi

# Clear caches
${DRUSH} cr

# Perform entity schema updates.
${DRUSH} entity-updates -y

# Run cron (Enable when your project needs it on build).
# ${DRUSH} cron


# We don't run this part because the gitlab environment doesn't have Varnish
# and it would error out
# Clear Varnish cache last
# ${DRUSH} cre -y