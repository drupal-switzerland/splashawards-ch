#!/usr/bin/env bash
# Rebuilds the site from config and re-imports the legacy splashawards.ch content.
# Needs: the legacy DB in the "legacy" database (ddev import-db --database=legacy --file=...)
# and the legacy public files in ./legacy/files (see README "Legacy migration").
set -euo pipefail
cd "$(dirname "$0")/.."

ddev drush si --existing-config --account-name=admin -y
ddev drush en -y splash_ch_migrate
ddev drush mim --tag=splash_ch --execute-dependencies --continue-on-failure

# Award 2025 is current; no Swiss edition is planned yet, so submissions stay closed.
# Term ids are preserved from the legacy site: 43 = award year 2025.
ddev drush cset -y splash_awards_base.settings award 43
ddev drush cset -y splash_awards_base.settings case_submission_active 0
ddev drush cset -y system.site page.front /node/140
ddev drush cr
ddev drush uli
