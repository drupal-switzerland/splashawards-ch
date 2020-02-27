#Changelog

### 8.6.0-beta20
- Update themes to 3.0.0 alpha 6
- Added Service version 3.0.0 alpha 1
- Put Drupal core version on ~8.6.0 (3 digits) to avoid updates to 8.7 for now.
  It breaks a patch and we need to review if this is something we need to remove or fix ourselves
  `https://www.drupal.org/project/drupal/issues/2900248`

### 8.6.0-beta19
- Update themes to 3.0.0 alpha 5
- Update paragraphs to beta 7
- Added Service version 3.0.0 alpha 1

### 8.6.0-beta18
- Update themes to 3.0.0 alpha 4

### 8.6.0-beta11
- Update themes to 3.0.0 alpha 3

### 8.6.0-beta10
- Update themes to 3.0.0 alpha 2

### 11.0.0-alpha.68
- Added info about Typekit in the theming readme files + minor cleanup and move images

### 11.0.0-alpha.67
- Updated dropsolid_layouts to v alpha 15
- Updated DS to 3.3

### 11.0.0-alpha.66
- Update various modules to fix the search api + purge + varnish bug

### 11.0.0-alpha.65
- Update paragraphs to v31
- Test: Fixed mysql version bump changes in alpine 3.7 

### 11.0.0-alpha.64
- Update paragraphs to v30

### 11.0.0-alpha.63
- Update themes to v42
- Update paragraphs to v29

### 11.0.0-alpha.62
- Applied patch to Panels to make it use the updated SharedTempStore.

### 11.0.0-alpha.61
- Updated dropsolid paragraphs to alpha 28: see its changelog for more details

### 11.0.0-alpha.60
- Updated the themes to 2.0.0-alpha41: see changelog in base-8 for more details
- Updated dropsolid paragraphs to alpha 26: see its changelog for more details

### 11.0.0-alpha.59
- Re-add delete .gits
  - files: `composer.json`

### 11.0.0-alpha.58
- Updates related to CVE-2019-6340: updated and patched metatags, …
- Various updates, including Core to 8.6.10, CTools to 3.2.0, Paragraphs to 1.6.0, …

### 11.0.0-alpha.57
- Updated the themes to 2.0.0-alpha40: see changelog in base-8 for more details

### 11.0.0-alpha.56
- Updated the themes to 2.0.0-alpha39: see changelog in base-8 for more details
- Updated dropsolid paragraphs to alpha 25: see its changelog for more details

### 11.0.0-alpha.55
- Updated dropsolid paragraphs to alpha 24: see its changelog for more details
- Updated the themes to 2.0.0-alpha38: see changelog in base-8 for more details

### 11.0.0-alpha.54
- Updated dropsolid blog to alpha 17: see its changelog for more details
- Updated dropsolid paragraphs to alpha 23: see its changelog for more details

### 11.0.0-alpha.53
- Updated the themes to 2.0.0-alpha37: see changelog in base-8 for more details
- Updated module dropsolid_profile to 2.0.0-alpha26:
  - **80039**: added extra Styles for resetting margins on p and table tag using html-class `no-margin`. Works in conjuncture with the above styling changes in the themes
    - files: `/admin/config/content/formats/manage/full_html?destination=/admin/config/content/formats`  
