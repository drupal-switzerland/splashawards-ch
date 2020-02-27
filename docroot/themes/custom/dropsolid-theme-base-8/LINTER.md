Stylelint rules
==============

Colours
--------------

**color-hex-case** : "upper"  
Use lowercase when using hex colours.  
**color-no-invalid-hex** : true  
Longhand hex colours can either be 6 or 8 (alpha channel) hex characters. Their shorthand variants are 3 and 4 characters respectively.

The following will give a warning:

	a {  
		color: #fff; <- lowercase
		color: #00; <- bad use of shorthand
		color: #fff1az;  
	}

These will not give a warning:

	a {  
		color: #FFFFFF;
		color: #000;
		color: #FFF1A0;  
	}

Functions
--------------

**function-calc-no-unspaced-operator** : true  
Before the operator, there must be a single whitespace or newline plus identation. After the operator there must be a single whitespace or a newline.

The following will give a warning:

	a {  
		top: calc(1px+2px);  
		right: calc(calc(1em*2)/3);
	}

These will not give a warning:

	a {  
		top: calc(1px + 2px);
		right: calc(calc(1em * 2) / 3);
	}

**function-comma-space-after** : "always"  
There must always be a single space after the commas of functions.

The following will give a warning:

	a {  
		 transform: translate(1,1);
	}

These will not give a warning:

	a {  
		 transform: translate(1, 1);
	}

**function-max-empty-lines** : 0  
Limit the number of adjacent empty lines within functions. The limit is set to 0.

The following will give a warning:

	a {  
		transform:
			translate(
		 		1,

		 		1
		 	);
	}

These will not give a warning:

	a {  
		transform:
			translate(
		 		1,
		 		1
		 	);
	}

**function-name-case** : "lower"  
Use lowercase when using functions.

The following will give a warning:

	a {  
		width: CALc(5* - 10px);
	}

These will not give a warning:

	a {  
		width: calc(5* - 10px);
	}

**function-parentheses-space-inside** : "never"   
There must never be a single space inside the parentheses.

The following will give a warning:

	a {  
		transform: translate( 1,1);
		transform: translate( 1, 1 );
	}

These will not give a warning:

	a {  
		transform: translate(1, 1);
	}

**function-url-quotes** : "always"  
URLs must always be quoted.

The following will give a warning:

	@import url(foo.scss);

	a {
		background: url(image.png);
	}

These will not give a warning:

	@import url('foo.scss');

	a {
		background: url('image.png');
	}

Numbers
--------------

**number-no-trailing-zeros** : true  
There are no trailing zeros allowed.

The following will give a warning:

	a {
		top: 1.0px;
	}

These will not give a warning:

	a {
		top: 1px;
	}

Strings
--------------

**string-quotes** : single  
Strings must always be wrapped in single quotes.

The following will give a warning:

	a:before {
		content: "hallo";
	}

These will not give a warning:

	a:before {
		content: 'hallo';
	}

Units
--------------

**unit-case** : "lower"  
Use lowercase when using units.  
**unit-no-unknown** : true  
Only use known units.

The following will give a warning:

	a {
		width: 10pIXELS; <- not lowercase and unknown unit
	}

These will not give a warning:

	a {
		width: 10px;
	}

Value list
--------------

**value-keyword-case** : "lower"  
Use lowercase when assigning a value.

The following will give a warning:

	a {
		display: BLOCK;
	}

These will not give a warning:

	a {
		display: block;
	}

**value-list-comma-space-after** : "always"  
There must always be a single space after the commas.

The following will give a warning:

	a {
		background-size: 0,0;
	}

These will not give a warning:

	a {
		background-size: 0, 0;
	}

Shorthand property
--------------

**shorthand-property-no-redundant-values** : true  
Disallow redundant values in shorthand properties.  
This rule warns you when you use redundant values in the following shorthand properties
- margin  
- padding  
- border-color  
- border-radius  
- border-style  
- border-width  

The following will give a warning:

	a {
		margin: 1px 1px 1px 1px;
		padding: 1px 2px 1px 2px;
	}

These will not give a warning:

	a {
		margin: 1px;
		padding: 1px 2px;
	}

Property
--------------

**property-case** : "lower"  
Use lowercase when adding a property.

The following will give a warning:

	a {
		WIDTH: 10px;
	}

These will not give a warning:

	a {
		width: 10px;
	}

Declaration block
--------------

**declaration-block-no-duplicate-properties** : true  
Disallow duplicate properties within declaration blocks.  
*EXCEPTION*: when the properties are consecutive, they are ignored and will not give a warning. This is useful when adding fallbacks for older browsers.

The following will give a warning:

	a {
		color: $orange;
		font-size: 12px;
		color: $pink;
	}

These will not give a warning:

	a {
		color: $orange;
		font-size: 1rem;
		font-size: 14px;
	}

**declaration-block-no-ignored-properties** : true  
Certain property values rule out other property value pairs, causing them to be ignored by the browser.  
This rule warns you when it finds:  
- `display: inline` used with `width, height, margin, float, overflow` and its variants  
- `display: inline-block` used with `float`   
- `display: list-item` used with `vertical-align`  
- `display: block` used with `vertical-align`  
- `display: flex` used with `vertical-align`  
- `display: table` used with `vertical-align`  
- `display: table-*` used with `margin` and its variants  
- `display: table-*` (except `table-cell`) used with `vertical-align`  
- `display: table-(row|row-group)` used with `width, min-width, max-width`  
- `display: table-(column|column-group)` used with `height, min-height, max-height`  
- `float: left` and `float: right` used with `vertical-align`  
- `position: static` used with `top, right, bottom, left`  
- `position: absolute` and `position: fixed` used with `float, clear, vertical-align`  

**declaration-block-no-shorthand-property-overrides** : true  
Disallow shorthand properties that override longhand properties.

The following will give a warning:

	a {
		padding-left: 15px;
		padding: 10px;
	}

These will not give a warning:

	a {
		padding: 10px;
		padding-left: 15px;
	}

**declaration-block-semicolon-newline-after** : "always"  
There must always be a newline after the semicolon.

The following will give a warning:

	a {
		color: $pink; font-size: 10px;
	}

These will not give a warning:

	a {
		color: $pink;
		font-size: 10px;
	}

**declaration-block-trailing-semicolon** : "always"  
There must always be a trailling semicolon.

The following will give a warning:

	a {
		color: $pink;
		font-size: 10px
	}

These will not give a warning:

	a {
		color: $pink;
		font-size: 10px;
	}

Block
--------------
**block-closing-brace-newline-after** : "always"  
There must always be a newline after the closing brace.

The following will give a warning:

	a {
		color: $pink;
	} b {
		color: $red;
	}

These will not give a warning:

	a {
		color: $pink;
	}
	b {
		color: $red;
	}

**block-no-empty** : true  
Disallow empty blocks.

The following will give a warning:

	a {
	}

These will not give a warning:

	a {
		//add your coding here
	}

**block-opening-brace-newline-after** : "always"  
There must always be a newline after the opening brace.

The following will give a warning:

	a {	color: $pink;
	}

These will not give a warning:

	a {
		color: $pink;
	}

**block-opening-brace-space-before** : "always"  
There must always be a single space before the opening brace.

The following will give a warning:

	a{
		color: $pink;
	}
	a
	{
		color: $pink;
	}

These will not give a warning:

	a {
		color: $pink;
	}

Selector
--------------

**selector-combinator-space-after** : "always"  
**selector-combinator-space-before**: "always"  
There must always be a single space before and after the combinators.  

The following will give a warning:

	a+b{
		color: $pink;
	}

These will not give a warning:

	a + b {
		color: $pink;
	}

**selector-pseudo-class-case** : "lower"
**selector-pseudo-element-case**: "lower"   
Use lowercase when using a pseudo-class or pseudo-element selector.

The following will give a warning:

	a {
		&:HOVER {
			color: $red;
		}
		&:BEFORE {
			content: 'hallo';
		}
	}

These will not give a warning:

	a {
		&:hover {
			color: $red;
		}
		&:before {
			content: 'hallo';
		}
	}

**selector-pseudo-element-colon-notation** : "single"  
Pseudo-elemnts must always have a single colon notation.  

The following will give a warning:

	a {
		&::before {
			content: 'hallo';
		}
	}

These will not give a warning:

	a {
		&:before {
			content: 'hallo';
		}
	}

**selector-type-case** : "lower"  
Use lowercase when using type selectors.

The following will give a warning:

	A {
		color: $pink;
	}

These will not give a warning:

	a {
		color: $pink;
	}

**selector-max-empty-lines** : 0  
Limit the number of adjacent empty lines within selectors. The limit is set to 0.

The following will give a warning:

	a,

	.foo{
		color: $pink;
	}

These will not give a warning:

	a,
	.foo {
		color: $pink;
	}

Selector list
--------------

**selector-list-comma-newline-after** : "always"  
There must always be a newline after the commas.

The following will give a warning:

	a, .foo{
		color: $pink;
	}

These will not give a warning:

	a,
	.foo {
		color: $pink;
	}

Comment
--------------

**comment-whitespace-inside** : "always"  
There must always be whitespace inside the markers.

The following will give a warning:

	/*comment*/

	/*comment */

These will not give a warning:

	/* comment */

	/*
	 * comment
	 */

General
--------------

**indentation** : 2  
Specified indentation. This is set to 2 number of spaces.  
You can set this in your editor (Atom > Preferences > Settings > Tab Length: 2; Sublime > Preferences > Settings - User > "tab_size": 2)

The following will give a warning:

	a {
	span {
	color: $red;
	}
	}

These will not give a warning:

	a {
		span {
			color: $red;
		}
	}

**max-empty-lines** : 1  
Limit the number of adjacent empty lines. The limit is set to 1.

The following will give a warning:

	a {
		color: $red;
	}


	b {
		color: $pink;
	}

These will not give a warning:

	a {
		color: $red;
	}
	b {
		color: $pink;
	}

	.foo {
		color: $red;
	}

	.foobar {
		color: $pink;
	}

**max-nesting-depth** : 5  
Limit the allowed nesting depth. Don't nest beyond 5.

**no-duplicate-selectors** : true  
Disallow duplicate selectors.

The following will give a warning:

	.foo,
	.bar,
	.foo {
		display: none;
	}

	a,
	b,
	b,
	a {
		color: $green;
	}
