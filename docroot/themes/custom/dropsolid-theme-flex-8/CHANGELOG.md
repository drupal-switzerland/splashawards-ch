#Changelog

### alpha 42

_2019/02/26_

- Fix the number of parameters in flex-grid, in paragraphs Menu Overview
- **77146**: add load more button styling + hide/show images for p 009
  - **files**:
    - `components/01-paragraphs/03-organisms/p009-photo-gallery/_p009-photo-gallery.scss`
  - **dependencies**:
    - dropsolid profile paragraphs version alpha 29

### alpha 41

_2019/02/22_

- **81911: only add browser-sync if arguments passed to Gulp**
  - **files**:
    - `gulp/config.js`
    - `gulp/tasks/00-setup.js`
    - `gulp/tasks/css.js`
    - `gulp/tasks/js.js`
    - `gulp/tasks/watch.js`
- **81506**: add styling for USP items buttons
  - **dependencies**
    - `dropsolid_profile_paragraphs` alpha 26 (or higher)
- **81488**: add styling for USP paragraph 3-col layout
  - **dependencies**
    - `dropsolid_profile_paragraphs` alpha 26 (or higher)
  - **files**:
    - `components/01-paragraphs/03-organisms/p007-usp/_p007-usp.scss`
    - `components/01-paragraphs/02-molecules/p007-usp-item/_p007-usp-item.scss`
- **81550**: make wrappers, change class names to have a better BEM structure, and add styling
  Depends on changes to templates in dropsolid_profile_paragraphs
  - **dependencies**
    - `dropsolid_profile_paragraphs` alpha 26 (or higher)
  - **files**:
    - `components/01-paragraphs/02-molecules/p017-menu-overview/_p017-menu-overview-item.scss`
    - `components/01-paragraphs/03-organisms/p017-menu-overview/_p017-menu-overview.scss`
- **81481**: fix divider in testimonials `name - extra rule`
  - **files**:
    - `components/01-paragraphs/03-organisms/p005-testimonial/_p005-testimonial.scss`
- **81460**: remove duplicate theme suggestions
  - **files**:
    - `dropsolid-theme-base-8/dropsolid_base.theme`

### alpha 40

_2019/02/20_

- **81316**: fix external links breaking the twig template (cause of #79503)
  - **files**:
    - `components/00-theme/molecules/menus/menu_menu-item.twig`

### alpha 39

_2019/02/07_

- **78057**: set the paragraphs button field (link field) to unlimited and added some styling for all non-primary buttons (including for negative buttons)
  - **dependencies**
    - `dropsolid_profile_paragraphs` alpha 25 (or higher)
  - **files**:
    - `components/00-theme/01-atoms/06-buttons/_buttons-helpers.scss`
    - `components/01-paragraphs/03-organisms/_paragraphs.scss`
    - `components/01-paragraphs/style.paragraphs.scss`

### alpha 38

- fixed gulp so the watch works on javascript files (still not working with browsersync though)
  - **files:**
    - `gulp/tasks/watch.js`
- **80594**: paragraph styling: moved the Javascript responsible for setting a class when similar backgrounds to the Dropsolid paragraphs module
  reformatted the javascript and styling to use top padding and adjacent siblings (cleaner, less code)
  in paragraph module: set a class using hook update
  **dependencies**
    - `dropsolid_profile_paragraphs` alpha 24 (or higher)
  - **files**:
    - `components/01-paragraphs/03-organisms/_paragraphs.scss`
    - `components/00-theme/00-base/06-scripts/01-base.js`
- **80575**: added more styling settings for the list styling mixin, renamed it, replaced bullet shape with font-awesome icon
  Also changed tweaked comments in bullet and typography mixins
  - **files**:
      - `components/00-theme/00-base/01-helpers/02-mixins/_mixins-typography.scss`
      - `components/00-theme/01-atoms/03-lists/list/_list--helpers.scss`
      - `components/00-theme/01-atoms/03-lists/list/_list.scss`
      - `components/00-theme/01-atoms/05-forms/form-element/_form-element.scss`
      - `components/00-theme/01-atoms/06-buttons/_buttons-helpers.scss`
- **79408**: add a z-index for the .info-wrapper containing content that goes over the big image (in case of focus header). However, focus header is not an officially supported paragraph and does not follow the standards set by the other headers, so it should be up to the dev to style as needed.
  - **files**:
      - `components/01-paragraphs/03-organisms/_paragraphs.scss`
      
### alpha 37

_2019/01/28_

- **link underlines**: tweaked the animation for the underlined links to be less dramatic (eg. used in .text-long)
  - **files**:
    - `components/01-paragraphs/03-organisms/_paragraphs.scss`
- **79170**: you can now add a CSS-gradient as 'background' and 'background-hover' variables to the button mixin in order to get a gradient background instead of a flat color.  
  With thanks to Roger for thinking of using box-shadow to fake borders without interfering with padding or background.  
  Example to be found with the primary button mixin at `atoms/06-buttons/_buttons-helpers.scss`
  Also cleaned up the links in .text-long so you don't have to worry about buttons inheriting other link styles when used in CKEditor (using `:not()` selector)
  - **files**:
    - `components/00-theme/atoms/01-links/_link.scss`
    - `components/00-theme/atoms/06-buttons/_buttons.scss`
    - `components/00-theme/atoms/06-buttons/_buttons-helpers.scss`
- **80039**: _(with dependencies)_ added styling for the class `no-margin` on p and table tags (and titles and lists) in `.text-long` div.  
  But, we only updated the CKEditor config file to use it on p and table tags, **so only p and table elements will get the `no-margin` style via the Styles dropdown!**  
  This is because the styles list became unreadable with so many elements in it (it's a small dropdown) and changes are small that clients will need it for more than a paragraph.  
  You are of course free to add more styles yourself in `/admin/config/content/formats/manage/full_html?destination=/admin/config/content/formats`  
  - **Dependency**: module dropsolid-profile, tag alpha 26 or higher
  - **files**:
      - `components/00-theme/atoms/02-text/00-headings/_headings.scss`
      - `components/00-theme/atoms/02-text/text/_text.scss`
      - `components/00-theme/atoms/03-list/_list.scss`
      - `components/00-theme/atoms/07-tables/_table.scss`
- **79503**: the general link template now has a flag called 'nolink', that can render empty links as a <span> Right now, only the menu items pass this flag in order to render empty links as a span. 
  - files:
    - `components/00-theme/atoms/01-links/_link.twig`
    - `components/00-theme/molecules/menus/menu_menu-item.twig`

### alpha 36

**2019/01/24**

- added a changelog
- updated the theming readme in project root with info about how to use Gulp and browsersync
- **79799** - properly center 79170list items in paragraph 'Text' that is centered + some info in comments on how to left align list items while still centering the list itself
- **79734** - removed references to mixins that are not global in `_mixins-typography.scss`
These do exist in the atoms folders for lists and links
- **79629** - added hidden label styling for field labels to the normalize scss
- **77988** - second mobile menu styling
  - added a sass-file for styling the old type of mobile menu (icon top right)  
    rename this file to match the default mobile nav file in order to use it (or copy paste the content).  
    Our Sass-files only get picked up by Gulp if they start with an underscore.
    - file: `components/00-theme/03-organisms/site-nav/example--old_mobile_nav--bp-xs-only.scss`
- **Office (Flex)**:
  - **79175** - IE fix where the office info displays under the image instead of next to it. This is because IE does not respect box-sizing, so we changed the values for flex-basis (and in flex shorthand) when combined with left or right padding
    - file: `components/02-features/f006-office/03-organisms/f006-office-full`  
    - src: `https://github.com/philipwalton/flexbugs7-flex-basis-doesnt-account-for-box-sizingborder-box`
- **forms**
  - **'optional' marker** on form labels has some styling to it, so the front-ender can not miss it and is forced to do something with it.
  - **Removed negative margins** from all Features forms (Flex). There were already margins set on the form-wrapper groups inside them.
  - **74492** - floating labels cleanup so we generate less unused form styling CSS
    - cleaned up how we apply the styling by using a general 'form-item' class and using :not to add exceptions, instead of a list of explicit input selectors
    - split mixin 'floating-labels-element' in 2 'floating-labels-element-label' and 'floating-labels-element-field'
    - added !important to some properties for Select2 styling to prevent invisible input becoming visible
    - files:
      - `components/00-theme/00-base/00-config/00-variables/_vars-form.scss`
      - `components/00-theme/01-atoms/05-forms/_forms-helpers.scss`
      - `components/00-theme/01-atoms/05-forms/input/_input.scss`
      - `components/00-theme/01-atoms/05-forms/select/_select.scss`
      - `components/00-theme/01-atoms/_00-normalize.scss`
      - `components/00-theme/02-molecules/search/search-form/_search-form.scss`
  -  **upload fields** cleanup styling (file and managed file)
    - files:
      - `components/00-theme/01-atoms/05-forms/upload/_upload-file.scss`
  - **79174** - styling, extra wrapper and reordering form element to allow for pre- and suffixes (eg. € before or after an input field)
    - files:
      - `components/00-theme/01-atoms/05-forms/_forms-helpers.scss`
      - `components/00-theme/01-atoms/05-forms/form-element/_form-element.scss`
      - `components/00-theme/01-atoms/05-forms/form-element/_form-element.twig`


### alpha 35

- **browsersync** with launchpad: injects css & updates browser (thanks to Mattias!)
  - add parameter to your gulp task:
    - eg. gulp --projectUrl http://PROJECT_NAME:PORT --projectDelay
  - http://localhost:3001/ gives you dev settings
    - eg. sync accross devices, external url to surf to with your phone, …
  - doesn’t work for JS yet, even though there is a listener on it
- **cleaned up** gulp tasks and css output:
  - no more fontawesome stuff in mail css
  - no more redundant fontawesome classes in other css
  - less editor-specific css in main css
