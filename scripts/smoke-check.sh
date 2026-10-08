#!/usr/bin/env bash
# Every URL in the live splashawards.ch sitemap must answer 200 on the new site,
# directly or after redirects. Prints failures, exits non-zero if any.
set -uo pipefail
base=${1:-$(ddev describe -j | python3 -c "import json,sys;print(json.load(sys.stdin)['raw']['primary_url'])")}
fail=0
for path in $(curl -sL https://splashawards.ch/sitemap.xml | grep -oE '<loc>[^<]+' | sed -E 's#<loc>https?://[^/]+##'); do
  code=$(curl -skL -o /dev/null -w '%{http_code}' "$base$path")
  [ "$code" = 200 ] || { echo "$code $path"; fail=1; }
done
[ $fail = 0 ] && echo "all sitemap URLs OK"
exit $fail
