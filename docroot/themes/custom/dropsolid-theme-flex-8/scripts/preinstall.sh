# If permission denied, eg. for copying folders
# set permissions using: chmod 775 prepublish.sh

# to do: check for existing folders instead of just removing and copying over it
# rm -rf gulp
# rm -rf Gulpfile.js

# make node modules directory with a no-index file

if [ ! -d "node_modules" ]; then
  mkdir node_modules
  chmod -R 775 node_modules
fi

if [ ! -f "node_modules/.metadata_never_index" ]; then
  touch node_modules/.metadata_never_index
  chmod 775 node_modules/.metadata_never_index
fi

# copy directories from base theme over to this theme

if [ ! -d "gulp" ]; then
  cp -R ../dropsolid-theme-base-8/gulp gulp
  chmod -R 775 gulp
fi

if [ ! -f "Gulpfile.js" ]; then
  cp -R ../dropsolid-theme-base-8/Gulpfile.js Gulpfile.js
  chmod 775 Gulpfile.js
fi

if [ ! -f "CHANGELOG.md" ]; then
  cp -R ../dropsolid-theme-base-8/CHANGELOG.md CHANGELOG.md
  chmod 775 CHANGELOG.md
fi

#if [ ! -f "README.md" ]; then
#  cp -R ../dropsolid-theme-base-8/README.md README.md
#  chmod 775 README.md
#fi

if [ ! -d "images" ]; then
  cp -R ../dropsolid-theme-base-8/images images
  chmod -R 775 images
fi

if [ ! -d "icons" ]; then
  cp -R ../dropsolid-theme-base-8/icons icons
  chmod -R 775 icons
fi

if [ ! -d "favicons" ]; then
  cp -R ../dropsolid-theme-base-8/favicons favicons
  chmod -R 775 favicons
fi

if [ ! -d "fonts/fontAwesome" ]; then
  cp -R ../dropsolid-theme-base-8/fonts/fontAwesome fonts/fontAwesome
  chmod -R 775 fonts/fontAwesome
fi

# loop all nested folders of parent theme
# if folder does not exist, make it
# loop again, check files
# if file does not exist, copy it over

# save current theme path
THEMEPATH=$PWD

# go to parent theme components & save path
cd "../dropsolid-theme-base-8"

PARENTPATH=$PWD

# echo $THEMEPATH;
# echo $PARENTPATH;

# make components folder in child theme if doesn't exist
if [ ! -d "$THEMEPATH/components" ]; then
  mkdir  "$THEMEPATH/components"  "$PARENTPATH/components"
  chmod -R 775 "$PARENTPATH/components"
fi

# go to parent components
cd "$PARENTPATH/components"

# echo 'before copying components folders';

# loop all nested folders of parent theme
for d in $(find . -type d -name "*"); do

  # if folder does not exists, copy it over

  if [ ! -d "$THEMEPATH/components/$d" ]; then
    # mkdir "$THEMEPATH/components/$d"
    cp -R  "$PARENTPATH/components/${d}" "$THEMEPATH/components/$d"
    chmod -R 775 "$THEMEPATH/components/$d"
  fi
done

# echo 'before copying components files';

# loop all nested files of parent theme
for f in $(find . -type f -name "*"); do

  # if file does not exists, copy it over

  if [ ! -f "$THEMEPATH/components/$f" ]; then
    cp -R  "$PARENTPATH/components/${f}" "$THEMEPATH/components/$f"
    chmod 775 "$THEMEPATH/components/$f"
  fi
done

# echo 'before copying overrides';

# in an override folder exists,
# copy those components and templates over the already present ones

if [ -d "$THEMEPATH/_components-overrides" ]; then

  # echo 'go to overrides folder';
  cd "$THEMEPATH/_components-overrides"

  # echo 'loop the nested folders';
  # loop all nested folders in override components
  for d in $(find . -type d -name "*"); do

    # if folder does not exists in normal components, make it

    if [ ! -d "$THEMEPATH/components/$d" ]; then
      # echo 'make folders';
      mkdir -p "$THEMEPATH/_components-overrides/${d}" "$THEMEPATH/components/$d"
      # echo 'set permissions';
      chmod -R 775 "$THEMEPATH/components/$d"
    fi
  done

  # echo 'loop nested files';
  # loop all nested files of _components-overrides
  for f in $(find . -type f -name "*"); do

    # echo 'copy file';
    # echo "$THEMEPATH/_components-overrides/${f}" "$THEMEPATH/components/$f";
    # Copy and override files (force)
    cp -rf "$THEMEPATH/_components-overrides/${f}" "$THEMEPATH/components/$f"
    # echo 'set files permissions';
    chmod 775 "$THEMEPATH/components/$f"
  done

  # echo 'rename overrides folder';
  # rename overrides so they can't be used again
  mv "$THEMEPATH/_components-overrides" "$THEMEPATH/_components-overrides-original"
fi

# at the end, go back to theme folder
# echo 'back to theme path';

cd "$THEMEPATH"
