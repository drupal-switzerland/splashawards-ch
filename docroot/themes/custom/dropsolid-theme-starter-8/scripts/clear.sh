######################
## WARNING !!!      ##
######################

# DO NOT USE THIS WHILE THEMING A PROJECT !!!

# This script is for theme R&D only !
# It restores the theme to the state is was in when project was first installd
# so you WILL lose ALL your code!

THEMEPATH=$PWD
BASEFOLDER="00-base"
ATOMSFOLDER="01-atoms"
THEMEFOLDER="00-theme"
PARAGRAPHSFOLDER="01-paragraphs"
FEATURESFOLDER="02-features"
COMPONENTSPATH="$THEMEPATH/components"

# remove folders

if [ -d "gulp" ]; then
  rm -rf gulp
fi

if [ -f "Gulpfile.js" ]; then
  rm Gulpfile.js
fi

if [ -f "CHANGELOG.md" ]; then
  rm CHANGELOG.md
fi

if [ -f "README.md" ]; then
  rm README.md
fi

if [ -d "images" ]; then
  rm -rf images
fi

if [ -d "icons" ]; then
  rm -rf icons
fi

if [ -d "favicons" ]; then
  rm -rf favicons
fi

if [ -d "fonts/fontAwesome" ]; then
  rm -rf fonts/fontAwesome
fi

# clean out components folder

if [ -d "$THEMEPATH/components" ]; then
  find "$THEMEPATH/components" -mindepth 1 -delete -not -path '*/\.*'
fi

# clean out templates folder

if [ -d "$THEMEPATH/templates" ]; then
  find "$THEMEPATH/templates" -mindepth 1 -delete -not -path '*/\.*'
fi

# rename overrides so they can be used again

if [ -d "$THEMEPATH/_components-overrides-original" ]; then
  mv "$THEMEPATH/_components-overrides-original" "$THEMEPATH/_components-overrides"
fi
