# Dropsolid Base Theme 8 + children

In-depth info on how to approach the theming (as well as other info on development and install), can be found in your project's README.md file  
This is more or less a condensed version of that info

## Dependencies

- [node](https://github.com/creationix/nvm) v8.5.2+
- npm: v5.6.0+
- [Drupal Components Library](https://www.drupal.org/project/components)
- [Unified Twig Extentions](https://github.com/drupal-pattern-lab/unified-twig-extensions)
- Optional: Livereload (Chrome plugin) (https://chrome.google.com/webstore/detail/livereload/jnihajbhpnppcggbcgedagnkighmdlei)
- responsive_image module (part of Drupal core)
- [styleguide module]((https://www.drupal.org/project/styleguide))
- dropsolid_styleguide module
- child theme: eg. starter or flex

## What this theme does

- provide a basis for child themes to extend

## What the themes do

Base:
- Converts Sass (with globbing) into CSS
- Minifies CSS with Sourcemaps
- Concatonates JS with Sourcemaps
- Generates favicons
- Generates custom icon-font with svg's
- adds data attributes to make tables responsive (and removes the core functionality because this is not the way we want).
- add colors for the paragraphs background-color palette

Child themes:
- extends the base theme
- Converts Sass (with globbing) into CSS
- Minifies CSS with Sourcemaps
- Concatonates JS with Sourcemaps
- Generates favicons
- Generates custom icon-font with svg's
- adds data attributes to make tables responsive (and removes the core functionality because this is not the way we want).


## Settings

Your active child theme has settings over at `admin/appearance/settings/themename`
Be sure to check those out before searching the Sass and JS files.
E.g. there is a setting that can switch the language menu from a dropdown to an inline list
E.g. the Flex theme has a setting to make a fixed header

## Libraries & other third party stuff

- Fontawesome: icons made easy, integrated with a theme mixin and variables
- Styleguide: Drupal module to generate a styleguide to theme
Located at `/admin/appearance/styleguide`


## Setup

- Don't work in the base version, use one of the child themes, eg. 'starter'
- Like all other themes, you add both the parent and child theme to your themes-folder (*/docroot/themes/custom*).
- Make sure the main theme folder is named 'dropsolid-base-8'
- **Don't change the name of this theme!** It will break things.
- Check if your npm and node are up-to-date. In your command line, run `npm -v` and `node -v`. The versions should be higher than the above mentioned versions.
- In your command line, navigate to your theme folder (*/docroot/sites/all/themes/custom/dropsolid_base/[child-theme]*) and run `npm install`. You only need to run this command once, when you install the theme! It will copy files in place, add some symlinks and install the node modules for gulp to work
- If you want, you can use Livereload. Download and install the Livereaload Chrome plugin.
- In your Drupal settings, make sure your child theme is activated and set as default.

Your theme is now ready to be used!


## How to use

!!! See the README files about theming in the root of the project !!!

Only work in your active child theme, NOT in the base-8 theme!

Here's a few basics to get you started:

### Gulp

We use Gulp as our task manager. This will compile all the things we need to make the front-end work.

#### + Browser sync

First of all: if you work with launchpad, gulp will use Browsersync to automagically update your browser when you change your Sass or JS  
This requires you to pass 2 arguments to your gulp tasks:
- projectUrl: a string, the url of your project, including http(s) and the port!!!
- projectDelay: a boolean, default is FALSE,  if you have a slow sync, you'll have to tell the system to wait and reload again after the sync has finished.

Example:
`gulp --projectUrl http://rocketshipdemoflex.docker.localhost:81/ --projectDelay`

Your browser will use localhost:3000 to render the site and keep listening to updates.  
It wil **NOT** work if you go to your default project url.

**Extra options:**
If you change the port to 3001, you will get extra development options.
Eg.
- sync across devices: you also get a proxy url so you can surf to your machine with any device. Very handy if you want to debug on Android or iPhone

#### + Tasks

Knowing this, you can now use gulp with any of the following tasks.
Enter `gulp` followed by one of the following task names:

**The basic commands:**

- `dev`: same as the default task, this will compile CSS and JS for development (with linting), try to create favicon and trigger a 'watch' task. 90% of the time, you only need this task.
- `prod`: compile CSS and JS for production (no map files nor linting nor watch)
- `watch`: included in ‘default’, this task will keep running in the background to keep compiling the Sass, JS and images
- `favicon`: This will generate the favicons (if you place an image in /favicons/source folder).  <b>Don't forget to add these images!!!</b>

**What is running in the back of these:**

- `setup`: included in ‘default’, this will check for the favicons but also try to provide a copy of Fontawesome locally. Please use a CDN for fontawesome though. This should already be active in html.tpl.php
- `css:dev` and `css:prod`: this will compile the Sass (inside a component folder) to CSS (and provide sourcemaps if dev is used). The produced files end up in the `css`-folder
- `js:dev` and `js:prod`: this will concatinate any JS-files inside a component folder (and provide sourcemaps if dev is used). The produced files are located in the `js/min`-folder
- `css:mail`: runs after the css-task, produces css from the style.mail.scss file
- `css:editor`: runs after the css-task, produces css from the style.editor.scss file
- `js:lint`: included in ‘js:dev’, this will provide notes for you to write better code
- `css:lint`: included in ‘css:dev’, the same but for Sass
- `critical`: not currently included, this will help generate a file with 'critical' css for a certain site path. Currently not used but you could use the produced file to put inline, in the HEAD of your site.
Needs some setup in order to use (see task)

**The extras:**

- `icons:font`: not included in ‘default’, using gulp iconfont this will generate a font based on svg’s in the 'icons' folder. It will also add mixins to use them.
- `icons:sprite`: not included in ‘default’, using gulp sprite this will generate a sprite based on svg’s in the 'icons' folder. It will also add sass variables and mixins to use the
- `fonts:inline`: Currently not in use. This will attempt to convert all fonts called using @font-face in style.fonts.css into Base64 and include it into the css. Can be useful if you absolutely need the fonts to load with the CSS. But usually you want to avoid using this. Especially since we use a js-solutions to load fonts.

#### Compile Sass/JS

To simply watch and compile your Sass/JS, you must navigate to your theme folder (*/docroot/sites/all/themes/custom/dropsolid_base/[child-theme]*) in your command line and run `gulp`.

#### Favicon Generator

This theme has an automatic favicon generator. Do create your favicons, add a png to the favicons source folder (*/docroot/sites/all/themes/custom/dropsolid_base/[child-theme]/favicons/source*). This png must be at least 250x250 px and a square format. In your command line, run `gulp favicon` to generate the favicons in the generated folder (*/docroot/sites/all/themes/custom/dropsolid_base/[child-theme]/favicons/generated*). You don't have to add these generated favicons manually, there's a function in template.php that takes care of that for you. Don't move the favicons out of the generated folder!

#### Custom icon-font or sprite Generator

This theme also has an automatic icon-font generator. Add the svg's to the icons folder (*/docroot/sites/all/themes/dropsolid_base/icons*) and in your command line run `gulp icons:font`. This will generate a custom icon-font in your fonts folder, that you can use in your Sass files.
This will also produce relevant variables and mixins in the components base folder for you to use to add these icons in your css.
Another option, is to use `gulp icons:sprite`, if you want to use the sprite option instead of fonts.
Just like with the custom font, it will generate variables and mixins in the components base folder.

In order to make use of either option, you will need to change variables in:
`components/00-theme/00-base/00-config/00-variables/_vars-base.scss `

#### Critical

You'll have to add subtasks per path you want to render critical CSS for.
Make sure these pages have content in them, otherwise it's pretty pointless.
Also, the critical CSS files are useless if you don't inline it in the HEAD of your site. Probably best to use something like AdvAgg to do this.

If you need to inline it yourself, you can do so like this:
`<style media="screen">
  {% include directory ~ '/css/ciritical.whatever.css' ignore missing %}
</style>`


#### fonts:inline

You probably won't need this. It's for extreme cases where you really need to have your font loading alongside the CSS. But since we use a JS-solution, it's probably not a good idea.

### Fonts

We use webfont loader to load fonts via JS.
To add font-files, add @font-face declarations to `components/00-theme/00-base/02-fonts/_fonts.scss` and add the files themselves to `/fonts/`
To add these custom families or Google fonts, go into your child theme's `components/00-theme/04-templates/_html.twig` file and add the families to the variables.
You can also add Typekit fonts, by adding the typekit id.

If you are using fonts that take a long time to load, you can set a preloader in the Sass variables: `components/00-theme/00-base/00-config/00-variables/_var-typo.scss`

### Components vs templates

Because this is a component-driven theme, based on the [Pattern Lab structure](http://patternlab.io/docs/pattern-organization.html), You'll find a bunch of Sass, JS and twig-files together inside the `components/00-theme`-folder.
We've also removed the drupal templates folder and just moved those templates insid the relevant component folder. You can recognize them by the fact they have the `.html.twig` extention and are always nested in a `templates`-folder within a `_drupal` folder.

- The theme's `.info` file, let's you list aliasses in order to include or extend twig-templates (see under: component-libraries).
- That way, we can move the content of every template that is in the `templates`-folder to the `components/00-theme`-folders, creating better-named components
- we also have some extra Twig functions available in there, eg. to make BEM-classes easier to construct. You'll find those in `components/_twig-components`
- to add JS-files per component, you'll have to use drupal's `attach_library()`-function to add libraries to you component's main twig file,
The library name is declared in the theme's `libraries`-file.
The status-component is a good example, you can find the relevant files in `templates/misc/status-messages.html.twig` and `compontent/00-theme/02-molecules/status`

#### Naming conventions

Component files end with `.twig` because we don't need to follow Drupal's templating naming conventions: the shorter, the better. So this naming also helps to separate it from the conventional Drupal templates if they would accidentally share the same name.
The file names start with an underscore to indicate that these files are 'partials' and are meant to be included somewhere (eg. in Drupal templates or in other component files).
Looking at a possible future Pattern Lab integration, this will help exclude the component files from being picked up when generating a html styleguide.

#### BEM

As mentioned above, there is a twig-function that can generate BEM-classes for you.
It looks like this: `{{bem(block, element, [modifier], [extra])}}`
This function essentially outputs the same stuff as {{ attributes }} + outputs a bunch of classes made with the BEM principles if you pass those variables to it. Keep in mind the modifier and extra classes are passed as arrays.
That way you can still have the 'normal' drupal classes as well as BEM classes if you need that.
So the output in html may look something like this:
`class="block__element block__element--modifierA block__element--modifierB extra_class01 extra_class02 default_drupal_class" id="someidhere" role="group"`

Check out `00-theme/example` for an example and more info.

#### Tables

By default, we'd like all tables to be responsive. There are 2 methods in place:
- default, is that the `th` and `td` elements will become blocks on mobile and thus form 1 column.
- the other way, is that the table gets a wrapper with a horizontal scrollbar on mobile

What type of responsive behavior is expressed, depends on what class is present in the table attributes.
By default, there is no special class passed to the attributes, and so the twig template will get wrapped in a div with class `is-reformatted`. This causes responsiveness of the first type.
But if we want another behavior, we can pass a class `table--reformatted` and that will cause the table to be wrapped in a div with class `has-scroll`. This causes responsiveness of the second type.

however, sometimes one method or behavior is not desired. A couple of ways to override this:
- follow the theme suggestions to override the twig template and set the responsive class we want for that new table template
- directly override the table classes or responsive setting in a theme function eg. `$variables['table']['#responsive'] = FALSE;` to turn responsiveness off or use `$variables['classes']` to remove and replace the `table--reformatted` class.

## Paragraphs

A custom module loads all the paragraphs of Dropsolid but you'll have to style them in your active child theme.

### background color palette

All paragraphs should have the option of adding background-color tot the paragraph.
You have to add your own my modifying the options inside your `.theme` file's `dropsolid_starter_dropsolid_profile_paragraphs_bg_color_options_list` and styling them inside `style.admin.scss`

## Helper stuff

**Styleguide**

This theme has a file called *styleguide.html*, which can be found in the main folder of the theme. If you open this file in your browser, you can immediately style the main bits and pieces of your site. Use this file to setup the basics of your theme, such as headings, pagers, tables, etc... Make sure items shown in this file look like the design!

**template.php**

- If you want to style drupal messages, you can add some dummy messages by uncommenting the `_dropsolid_base_show_dummy_drupal_messages()` function in *base_preprocess*.
- Add ScrollMagic by uncommenting the `drupal_add_js` under *preprocess_page*. The second one is to add indicators. Handy if your setting up your ScrollMagic functionality! But remove it once you're done setting it up!

**Linting**

What is linting? Linting is the process of checking the source code for programmatic as well as stylistic errors. This is helpful in indentifying some common and not so commong mistakes that are made during coding. They are essentially a spell checker for programming languages.
This theme includes a linter (Stylelint) with its own set of rules. These rules can be found in the *LINTER.md* file.
Maintain code consistency and reduce unnecessary code! And most of all, prevent lazy coding.

## Issues

### Theme suggestions don't work with the DS or Panels

For some reason, the theme suggestions don't work properly with the layouts if the template file name (using dashes) is not the same as the template machine (using underscores) name in `x.layouts.yml`.
So, rename the template file name to match.

## Maintenance

At some point, every dev will be able to add to the development branch (dev) but merges with the master must be approved by the technical lead!
If new types of favicons are introduced in the future, they need to be added to the base theme's html twig file + the favicon gulp task

- Git repos: 
  - base: https://gitlab.com/dropsolid-drupal8/dropsolid-theme-base-8
  - starter: https://gitlab.com/dropsolid-drupal8/dropsolid-theme-starter-8
  - flex: https://gitlab.com/dropsolid-drupal8/dropsolid-theme-flex-8

- Report issues: Redmine
