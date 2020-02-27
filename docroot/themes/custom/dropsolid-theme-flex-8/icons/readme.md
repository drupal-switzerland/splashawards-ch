Automatic icon-font generation

The icons located in here will be compiled into 1 sprite or 1 font,  
located in either 'images/generated/sprite.svg' (sprite)
or 'fonts/iconfont'

Requirements:
- Icons must be svg
- Icons must be perfectly aligned: no decimals in px values for left and top !!!
- Artboard must be perfectly sized: no decimals in width and height !!!

Instructions:
- Add your svg's to this folder
- run one of these gulp tasks, depending on if you want a font or sprite:
- - gulp icons:font
- - gulp icons:sprite
- In your components-folder, look for `00-theme/04-pages/html/_html.twig` and add the iconfont name to `webfont_config_custom_families variable`
- Refer to `00-base/01-helpers/02-mixins/_mixins-iconfont.scss` to use the iconfont
- Or refer to `00-base/00-config/00-variables/_vars-sprite.scss` and  `00-base/01-helpers/02-mixins/_mixins-sprite.scss` to use the sprite
