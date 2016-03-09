=== WP Advertising Associate ===
Contributors: mdbitz
Donate Link: http://mdbitz.com/donate/
Tags: amazon, associate, affiliate, carousel, widget, filter, search, short code, ip2nation, localization, omakase, my favorites, mp3 clips, multi-user, multi-author, product cloud
Requires at least: 3.2.1
Tested up to: 3.6.1
Stable tag: 0.9.0

Quickly and easily monetize your website through the integration of Amazon products and widgets targeted by visitors' geo-location.

== Description ==

The WPAA plugin enables you to monetize your website through the use of Amazon's
Affiliate Program. By entering your amazon associate id for Amazon's supported
locales you will earn referral fees for all products users purchase through
your website. The Plugin features tinyMCE editor support for searching and
inserting of amazon products and images into your content, content replacement
of static links with your associate tag, and support for inserting Amazon
Widgets through WordPress ShortCode, tinyMCE editor controls, Widget Admin,
or PHP code.

This plugin fully supports amazon product localization to supported markets:
Canada (CA), China (CN), Germany (DE), Spain (ES), France (FR), Italy (IT), Japan (JP), United Kingdom (GB), and
the Unites States (US).<br>

The [WordPress Advertising Associate](http://mdbitz.com/wpaa/?utm_source=wordpress&utm_medium=plugin-readme&utm_campaign=plugin)
plugin is designed to be your all inclusive source for enriching you website
with Amazon Products and Widgets embedded with your unique Amazon Associate Tag.
Below is a brief overview of the supported features.

**Key Features**

1. **Amazon Widget Support** <br>
  *Carousel*<br>
  *MP3 Clips*<br>
  *My Favorites*<br>
  *Omakase*<br>
  *Product Cloud*<br>
  *Search*<br>

1. **Amazon Product Linking**

1. **Amazon Product Link Associate Tag filtering & update with rel="nofollow"**

1. **Amazon Product Preview**

1. **New Product Template Support**

1. **Amazon Link & Widget Geo-Localization**

1. **Multi-Author Support**

1. **Amazon Product Advertising API Caching**

1. **Complete Administrative control**

1. **MPMU Compatible**

== Installation ==

1. Upload the `wordpress-advertising-associate` folder and all it's contents to the `/wp-content/plugins/` directory
1. Activate the plugin through the *Plugins* menu in WordPress
1. Access the Plugin Settings by clicking the *WPAA - Amazon* menu option
1. Enter your Amazon Associate Ids
1. Enter your Amazon Web Services Access and Secret Keys
1. Optionally Configure Multi-Author, Link Filtering and Product Preview settings
1. Install the ip2nation database if you wish to support Geo Localization, by selecting the *WPAA - Amazon* > *ip2nation* menu option
1. Configure the Plugin Cache by visiting the *WPAA - Amazon* > *Cache* menu option
1. Configure available Widgets by accessing the *WPAA - Amazon* > *Widgets* menu option
1. Insert Products and Widgets into your website through your template, page/post content or the *Widget* admin screen in WordPress

== Frequently Asked Questions ==

= How can I report a bug? request help? request a feature? =

If you find a bug in the WordPress Advertising Associate plugin then please let me
know by emailing it to [matt@mdbitz.com](mailto:matt@mdbitz.com). Another way you can
reach me is through my [website](http://mdbitz.com/wpaa/?utm_source=wordpress&utm_medium=plugin-readme&utm_campaign=plugin)

= Do I need to configure the Amazon Web Service Keys =

No, however by not inputing a valid Amazon Web Service credential you will not be able to
insert amazon product links and images through ShortCode, the content editor's
tinyMCE control or the Quick Links Module.

= Does this plugin support Multi-Author websites? =

Yes! The plugin has a Multi-User module that if enabled allows authors to set
their Amazon Associate Ids on the users profile page. Product links in page/post
content will then be tagged with the author's associate ids, and if not set will
default to the ids configured on the *WPAA - Amazon * > *Settings* page. We also
allow site administrators to define a percentage of links where the
administrator's associate id will be used instead of the author's associate id.

= What is Link Localization? =

Link Localization is the generation of a product link that is localized to the
user's version of Amazon. To give a quick example if you have your default locale
set to United States and a visitor of your website is from France then they would
be presented with a link to the amazon.fr website instead of amazon.com. This
is an optional feature that makes use of the free [ip2nation](http://ip2nation.com)
database. To make things as easy as possible the WPAA plugin includes the ability
to detect if you have ip2nation installed and if not install it for you.

= Can I choose which locales to support? =

Yes, not everyone will want to support link localization for all of Amazon's
Locales. This is why we have built in the ability to disable locales you don't
want to support. If a visitor from a disabled locale visits your website they
will be presented with a product link localized to your default locale.

= Why is Product Preview not working for my website? =

At this time there is a confirmed bug with the Amazon Product Preview code
that in some wordpress themes the previews do not occur. This error is due to
the Amazon script including it's own version of jQuery into the page causing a
conflict. To resolve the issue an optional jQuery.noConflict call to remap
jQuery is included if selected by the user on the admin screen. If product
preview still does not function for your website review your page source to see
if additional jQuery versions are loaded, if so they need to be set to
noConflict.<br>
Also please note that product preview is only supported for 1 locale at a time.

= My website currently uses other plugins for displaying Amazon Widgets, How can I switch to your plugin without rewriting my existing content? =

WPAA has a built in compliance module that enables you maintain usage of the ShortCode
used in various other WordPress plugins for rendering Amazon Widgets.  For the
full list of compliant plugins visit WPAA - Amazon > Compliance in your
WordPress Admin pages. From this page you can also import your Amazon Associate
Tags/Ids from the Amazon Link and Amazon Link Localizer plugins.

= Does Wordpress Amazon Associate support I18n localization? =

Yes, we have built the plugin to support localization, If you have a translation
for the plugin please send the po/mo files to [matt@mdbitz.com](mailto:matt@mdbitz.com)
and I can incorporate it into the project for other users.

== Screenshots ==



== Changelog ==

The full project changelogs can be found at [http://mdbitz.com/wpaa/changelog](http://mdbitz.com/wpaa/changelog/?utm_source=wordpress&utm_medium=plugin-readme&utm_campaign=plugin)

= 0.9.0 = 10/05/2013 =
* Support for sties without curl enabled
* Plugin name modified to comply with Amazon Operating Policy
* Activation Hook enabled to cancel plugin activation if domain is not compliant with Amazon's Operating Policy

== Supported Amazon Widgets/Links ==
<br>
= Carousel =

* `<?php AmazonWidget::Carousel( $options ); ?>`
* `[amazon_carousel]`

= MP3 Clips =

* `<?php AmazonWidget::MP3Clips( $options ); ?>`
* `[amazon_mp3_clips]`

= My Favorites =

* `<?php AmazonWidget::MyFavorites( $options ); ?>`
* `[amazon_my_favorites]`

= Omakase (Leave it to Us) =

* `<?php AmazonWidget::Omakase( $options ); ?>`
* `[amazon_omakase]`

= Product Cloud =

* `<?php AmazonWidget::ProductCloud( $options ); ?>`
* `[amazon_product_cloud]`

= Search =

* `<?php AmazonWidget::Search( $options ); ?>`
* `[amazon_search]`

= Template =

* `[amazon_template template="1" id="0451463471" ]Content that will display if template not found, inactive or error during rendering[/amazon_template]
* `[amazon_template template="Basic Ad" type="ASIN List" id="0451463471,0756407125]Content[/amazon_template]`

= Product Link =

* `<?php AmazonProduct::link( array( "content"=>"link text", "id" => "0345518705" ) ); ?>`
* `[amazon_link id="0345518705"]link text[/amazon_link]`

= Product Image =

* `<?php AmazonProduct::image( array( "content" => "link text", "id" => "0345518705, "size" => "medium", "link" => true ) ); ?>`
* `[amazon_image id="0345518705" link="true"]alt text[/amazon_image]`

= Enhanced Ad =

* `<?php AmazonProduct::enhanced( array( "asin" => "0345518705" ) ); ?>`
* `[amazon_enhanced asin="0345518705" ]`
