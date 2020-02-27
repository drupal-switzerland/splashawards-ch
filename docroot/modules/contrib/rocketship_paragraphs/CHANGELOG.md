#Changelog

### 8.x-2.0-alpha5
### 8.x-1.0-beta25
- [#89147] - no has-image class for testimonial when image field is empty

### 8.x-2.0-alpha4
### 8.x-1.0-beta24

- changes to default CSS in case of using Media + fix masonry when multiple galleries

### 8.x-1.0-beta23

- changes to logic that fixes 'double padding' when 2 paragraphs with same bg color follow each other: takes in to account when previous paragraph had bottom margin 

### 8.x-1.0-beta22

- documentation about the patches

### 8.x-1.0-beta21

- patches added to module

### 8.x-1.0-beta20

- remove focalpoint from Focus paragraph: not cropped so no use. Cropping has no point anyway because it is responsive and size changes fluidly.

### 8.x-1.0-beta19

- custom permission to manage access to settings form

### 8.x-1.0-beta18

- fix some semantic issues in JS + styling for layouts in admin
- Update form displays: eg. add 'details' groups around child paragraphs

### 8.x-1.0-beta17

- Some fixes to get the custom colors and layouts working with Claro
- get text-alignment changes working with CKE + p003
- changes in descriptions of paragraph types
- icons for the paragraph types
- Changes to paragraphs form display (more fieldsets)

### 8.x-1.0-beta16

- Split up color css from structural css option

### 8.x-1.0-beta15

_2019/05/29_

- Make config form path and title shorter for more legibility

### 8.x-1.0-beta14

_2019/05/27_

- [#85993] - Load admin override styles from active Rocketship theme if it has any

### 8.x-1.0-beta13

_2019/05/17_

- [#84317] - added p014 Related Items paragraph to add items of all content types
  - **Dependencies:**
    - Needs 'Related' View Mode to be used by the CT in order to work
- added changes to autocomplete output in admin form: show content type next to title when searching

### 8.x-1.0-beta12

_2019/05/08_

- fixed wrong class name for centered text alignment in front-end css

### 8.x-1.0-beta11

_2019/05/08_

- added minified and example CSS with front-end tweaks for prev version

### 8.x-1.0-beta10

_2019/05/08_

- [#85238] - Text Main: changing the layout to 'center', in the admin UI, now also changes the CKE preview by wrapping the text in a text-align-left class + hide some of the cke text alignment buttons to avoid conflict with the intended behavior of our custom layout setting
  **Dependencies:**
  - If using Dropsolid themes: use version 3.0.0-alpha8 or higher

### 8.x-1.0-beta9

_2019/05/03_

- [#85137] - Cleaner way to run callback method for cached images

### 8.x-1.0-beta8

_2019/05/03_

- [#85140] - Fix the layout icon labels showing multiple times
- [#85137] - Fix masonry on P009 for multiple instances + wait for img load

### 8.x-1.0-beta7

_2019/04/29_

- USP paragraph: Added image-style switch when changing view modes
- Added 3 responsive view modes for it
- Changes in html class names & options machine names so it doesn't started with a number
- For all layout options with icons in admin UI: add visible label text
- Photo Gallery: view mode switch added to switch between a grid and a masonry view

### 8.x-1.0-beta6

_2019/04/25_

- Remove redundant padding settings on last paragraph in content
- Refactor the paragraph numbering, add extra descriptions to fields and paragraphs
- Wrap usp title in a link if there is one
- Add stretched layout option for the Video paragraph
- Added icons and change in descriptions for paragraphs with layout options
  - **For:** 001 Story, 002 Image, 003 Text, 006 Video and 007 USP
  - **Dependencies:**
    - If using Dropsolid themes: use version 3.0.0-alpha4 or higher

### 8.x-1.0-beta5

_2019/04/23_

- recommit, now with better commit message

### 8.x-1.0-beta4

_2019/04/8_

- Clean up the background color classes

### 8.x-1.0-beta3

_2019/03/29_

- Use correct dependency format

### 8.x-1.0-beta2

_2019/03/28_

- Update required version of Rocketship core

### 8.x-1.0-beta1

_2019/03/28_

- fix stretched images in Story paragraph
