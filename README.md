# Splash Awards Switzerland

splashawards.ch, rebuilt on the shared Splash Awards codebase from
[splashawards_germany](https://git.drupal.org/project/splashawards_germany)
(branch `1.0.x` @ `f370b7e`). The profile `splash_awards_starter`, the module
`splash_awards_base` and the theme `splash_awards` are copied **verbatim**: do
not change them here, so they can later be replaced by a shared
`splash_awards` composer package used by every country.

Differences from Germany: English-only config (`config/default`), the
`splash_ch_migrate` module, Platform.sh hosting (`.platform*`) and
`redirect`, `migrate_plus`, `migrate_tools`, `phpass`, `redis` modules.

## Project Setup
The project contains a DDEV configuration
(in particular /.ddev/config.yaml).
To launch a local development environment, simply run the
following command

```shell
ddev start
```

To install all required PHP dependencies with Composer
```shell
ddev composer install
```

Running:

```bash
ddev drush deploy
```

- Import configuration changes (drush config:import)
- run all database updates (drush updatedb)
- clear caches (drush cr)
- run post-deploy hooks (if defined)

It’s the standard way to apply all changes after pulling code or deploying updates.

If you are not familiar with ddev, please
refer to https://ddev.readthedocs.io/en/stable/users/install/

### Installation

The project is build upon Drupal 11. A new instance can be
installed in any language.
When installing, the installation profile splash_awards_starter should be selected
automatically. To rebuild the frontend end assets during development,
run the following command in the custom splash_awards themes directory:

```shell
npm intall
npm run build:drupal
```
The site is English only. Note that the profile's own install config is still
German; this site installs from `config/default` instead
(`drush si --existing-config`).

## Project Structure

### Nodes
The project contains three node types:
- Normal content pages such as the frontpage or a presentation of the jury can be displayed with the Node Type `Page`
- Time-critical content such as announcements should be created with the Node Type `Article`. These have information on the author, reading time and date. Articles can be placed in teaser form on pages with the help of a paragraph and linked this way.
- The type `Case` represents a project submitted for the Splash Awards. Typically, these are not created by authors but by customers.

### Taxonomy
The case submissions must be structured using the taxonomy system.
- A term in the vocabulary `Splash Awards` represents an award (or season) that is or has been awarded. An editor (admin) should start by creating a term here in the form “Splash Awards 2025” so that cases can be assigned to and submitted for this award. A term should also be created for past award ceremonies. For each term created here, a link is displayed in the CTA in the header on the page, which takes the visitor to an overview page (view) with teasers of all **nominated** projects. In the “Header” area, such a term has the option of overwriting the title of the respective landing page of the award and placing a background image or video, as well as CTA links.
- The vocabulary `Badges`  is used to assign the cases a status in the form “nominated”, “runner up” or “winner”. The respective terms should therefore be created by an admin. Here you have the option of uploading an image matching the status, which is displayed on the case page in the header area.
  To decide which image is displayed for a case (e.g. if a case is both nominated and “winner”), there is the option of giving the “winner” term a higher weighting in the weight field. Another important option is the “Nominated” field, Use this checkbox to determine if this Badge is the required for a case to be considered a nominated case. Only "nominated" cases are displayed in the case views. This Badge is also not availible as a filter option for the view.
- The vocabulary `Categories` is used to the structure the cases in categories like "Education", "E-Commerce" or "Enterprise". This is available  as a filter in the award landing pages.

### Case Submission
After a user has registered, the role “candidate” is automatically assigned.
This enables him to see a dashboard on his user page (/user/id) of all the cases
he has submitted and to edit them. The case submission form is divided into 6
individual steps and allows to save after each step.
Information relevant to a case, such as “Company website” or “Size of your company”,
which a user has already entered in their profile, is automatically offered in
the form and the corresponding fields are then disabled.
Conversely, the first-time entry of such fields is transferred to the profile
if the profile data is empty. Cases are initially created unpublished.

An admin has the option under /admin/config/case-submission-settings (linked in the toolbar)
to set the current award (new cases are automatically assigned to this), to set a deadline for
the submission and to activate / deactivate the submission completely.

## Legacy migration (splashawards.ch, Drupal 8 / Rocketship)

One-off, rerunnable import of the old site. Uninstall and delete
`splash_ch_migrate` after cutover.

```shell
# 1. Legacy DB and files (never committed, contain PII)
platform db:dump -p zhuwntz4pn7zo -e prod --gzip -f legacy.sql.gz
ddev import-db --database=legacy --file=legacy.sql.gz
platform mount:download -p zhuwntz4pn7zo -e prod -m docroot/sites/default/files --target legacy/files --exclude 'styles/*' --exclude 'css/*' --exclude 'js/*' --exclude 'php/*'
# 2. web/sites/default/settings.local.php: $databases['migrate'] pointing at
#    the "legacy" DB and $settings['splash_ch_migrate_files'] = '/var/www/html/legacy/files'
# 3. Install + migrate + settings, then check every old URL still resolves
scripts/legacy-migrate.sh
scripts/smoke-check.sh
```

What is migrated (legacy ids are preserved for nodes, terms, users and files):

| Legacy | New |
|---|---|
| `product` (74) | `case`; nominee/runner-up/winner(+of the year) flags → `badges` |
| `news` (36) | `article`, byline from the old author's name |
| `page` (18 of 20) | `page`; `/nominees-2023`, `/nominees-2025` → 301 to `/nominees/{year}` |
| `award_jahr`, `product_category` | `splash_awards` (slug = year), `categories` |
| Rocketship paragraphs | `text`, `images`, `cta`, `news`, `jury_grid`, `sponsor_grid`; rare types flattened, forms dropped (`drush mmsg splash_ch_node_page`) |
| 6 active admins | users with their password hashes (core `phpass`) |
| aliases + redirect table | `redirect` (301), new aliases from pathauto |
| main + footer menu | menu links |

Not migrated, kept in the archive only: jurors and blocked accounts, webform
submissions, custom blocks, unreferenced files, case images beyond 5 per case.
