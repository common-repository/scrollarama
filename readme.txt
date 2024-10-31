=== Scrollarama ===
Contributors: maltpress
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CMGXVNG4MKQYU
Tags: widget, posts, featured posts, jQuery, Cycle, slider, scroller, fader, effects
Requires at least: 3.0.1
Tested up to: 3.1.1
Stable tag: trunk

Creates a loop of recent posts (up to 10) with images, within selected category, and scrolls through with jQuery effects

== Description ==

This plugin creates a widget which loops up to ten recent posts and cycles through them with your selected jQuery Cycle effect.

== Installation ==

1. Upload the folder named 'scrollarama' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Drag and drop the widget to the right sidebar and change the settings as appropriate.

== Frequently Asked Questions ==

= What categories are used? =

You can specify any number of categories to use. Select multiple categories in the widget control panel using shift and/or ctrl.

= What images are used? =

The widget looks for a custom field called "slider" - which you should assign an image URL (you can grab this from the image details in the media library, or use an external image if you have permission).

If this custom field isn't set, the first image attachment assigned to a post is used.

If neither is in place, the background of the slider will be blank.

= How big should the image be? =

The height should match the height you specify in the widget, and width needs to match the width you set or the width of your sidebar. If it's bigger than this, the background image will be centered. If smaller, you'll have some blank space around it, so try to make it big enough.

= How can I restyle the widget? =

You can edit scrollarama/styles/scrollarama_style.css - just make sure you read the notes or you might break it! A white .png (to replace the default black background behind the post header) is provided, so you can easily swap that out.

= The widget doesn't do x, y or z... =

There are several bits of functionality I'd like to add when I get time - mainly to make it a bit more flexible and easy to style and adapt. If you want it to do anything in particular, though, get in touch via [my site](http://www.maltpress.co.uk "Maltpress.co.uk")

Version 1.2 should include better styling options. I'm trying to keep the widget controls tidy and simple, though.

= It doesn't show in IE8! =

There's possibly a conflict with other plugins using jQuery Cycle, possibly only if their Cycle plugin version isn't the same (2.99 at plugin version 1.1). I've seen it once but can't replicate the issue at the moment. If this happens to you, please contact me via [my site](http://www.maltpress.co.uk "Maltpress.co.uk") or in the forum for this plugin.

= What's with the name? =

I couldn't think of one, so I asked on Twitter and @michlan came up with it. I'm going to make this a thing. If you want to name a future plugin, follow @maltpress

= Marry me? =

Maybe.

== Screenshots ==

1. The widget menu
2. The widget in action

== Changelog ==

= 1.1.1 =
* Fixed bug on new WP and Scrollarama installs where category array chucks an "implode: Invalid arguments" error

= 1.1 =
* Added timeout (time each slide shows for) editing
* Added speed (time it takes for transition to complete) editing
* Added custom jQuery Cycle option editing
* Can now select multiple categories to slide through
* Now select up to ten posts

= 1.0 =
* Plugin launched

== Settings ==

You can change the following settings:

* **Title:** Widget title
* **Category:** Category to draw posts from - the list is automatically populated. Make sure the category you want to use exists before you start! You can select any number of categories - use ctrl and/or shift to select more than one
* **Number of posts:** Number of posts used - if there are fewer posts, that's fine. It'll just use the ones in your chosen category
* **Slider effect:** jQuery Cycle effect to be used. To see a preview of all effects, check out [jQuery Cycle Effects Browser](http://jquery.malsup.com/cycle/browser.html "jQuery Cycle Effects Browser")
* **Timeout:** the amount of time each slide will show for, in milliseconds. For example 3000 = 3 seconds
* **Transition speed:** how long it takes for each transition, again in milliseconds. For example, if you set this to 1000 it will take one second for a post to fade out and the next to fade in
* **Width:** Add a width here *if needed*. By default, the widget should fit the sidebar you drop it in to. If not, you can override this by setting a width in pixels (don't include the units, just the number)
* **Height:** Set the height of the widget in pixels. Don't include the units.

Add images to posts as follows:

* Edit the post to add the image to
* Add a custom field called "slider"
* Use the path to an image for the custom field value. This can be one in your media library or use an absolute path from another site if you have permission to do so.
* That's very important - image leeching is naughty.
* Update your post.