=== Flash Gallery ===
Contributors: ulfben
Donate link: http://www.amazon.com/gp/registry/wishlist/2QB6SQ5XX2U0N/105-3209188-5640446?reveal=unpurchased&filter=all&sort=priority&layout=standard&x=11&y=10
Tags: gallery, flashgallery, flash, slideshow, wall, album, fullscreen, picture, photo, image
Requires at least: 2.6
Tested up to: 2.8
Stable tag: 1.0

Flash Gallery turns your normal galleries into interactive, full screen slideshows.

== Description ==

The Flash Gallery plugin lets you turn your ordinary galleries into awesome Flash image walls, with full screen slideshow support and more!

It is *especially* great for when you have *lots* of images in a post, or want to have several categories in one gallery. [Here's an example](http://game.hgo.se/blog/gotland-game-awards-2009/) showing some 600 pictures in 7 albums.

The plugin uses a modified version of [Jerald Zhan's](http://www.zcube.sg) excellent [zGallery](http://flashden.net/item/zgallery-v1-fit-2-screen-xml-gallery/43071)

**Flash Gallery supports:**

* full screen viewing
* slideshow
* multiple albums/categories per post
* deep linking
* RSS-readers
* mouse- & keyboard interaction
* configurable gallery background and logo

See [Installation](http://wordpress.org/extend/plugins/flash-gallery/installation/) for more instructions.

== Changelog ==

**1.0 (2009-06-27)**

1. Release

== Installation ==

1. Unzip to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use [flashgallery] instead of [gallery] in your posts.

= Usage = 

Upload your images [like usual](http://wordpress.org/extend/plugins/flash-gallery/faq/), but use the [flashgallery]-shortcode instead of [gallery]. 

= Configuration = 

All settings are optional, and made through the shortcode syntax. 

Here's an example that will display a gallery with five rows, 650px high, and 3 Albums with 10 images in each:

	[flashgallery orderby="ID" rows="5" height="650px" cats="Prep_10 % Exhibit_10 % Presentations_10"]

All the regular [gallery] parameters still apply, and you've got these extras to play with:
	
* 	cats = "Album1\_12 % Album2\_33 % Album3\_66": *three albums and their image count*
*	height = "400px", "100%"
* 	rows = "3": *number of rows in the gallery thumbnail view.*
*	background = "background.jpg": *URL to high-rez background*
*	logo = "logo.png": *URL to logo*
*   transparent = "false" or "true": *set flash's wmode*
*	scaling = "fill", "fit" or "noscale": *how images are scaled when displayed(default: fit)*
*	thumbsize = "110": *size (in pixels) of thumbnails*


== Frequently Asked Questions ==

= How do I create WordPress galleries?  =
Use the built-in media uploader to create and insert galleries in your posts. See [the screencast](http://wordpress.org/development/2008/03/wordpress-25-rc2/) (at 01:35), or [read the instructions](http://codex.wordpress.org/Using_Images#Inserting_Images_into_Posts).

= How do I ask for help? =
1. Search [the forums](http://wordpress.org/tags/Flash-Gallery) and post in a relevant thread if one exists.
1. Always tag your post with `Flash Gallery`
1. State your problem succintly, *provide a link*!
1. Always tag your post `resolved` and publish your solution.

== Screenshots ==

1. Full screen gallery view, with the Album-selector opened.
2. Gallery view as seen in a post (before entering full screen mode)
3. Single-image view.

== Other Notes ==

The included FLA-source is a heavily modified version of [Jerald Zhan's](http://www.zcube.sg) stock [zGallery](http://flashden.net/item/zgallery-v1-fit-2-screen-xml-gallery/43071).

Jerald graciously allowed me to distribute the modified source for everyone to benifit from the improvements:
	
* WordPress integration

* deep linking supports

* scaling options (fit, fill, no scaling)
	
* mouse- & keyboard interaction	
	
* exposing all practical settings to flashvars
	
* various tweaks and fixes	

If using this file commercially, please show your appreciation by purchasing [the original stock file](http://flashden.net/item/zgallery-v1-fit-2-screen-xml-gallery/43071) from him.
	


Copyright (C) 2009 Ulf Benjaminsson (ulf at ulfben dot com).

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA