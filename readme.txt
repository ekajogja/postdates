=== PostDates ===
Contributors: ekajogja
Donate link: https://paypal.me/ekajogja
Tags: date, last-updated, published-date
Requires at least: 4.7
Tested up to: 6.7
Stable tag: 2.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display the publication and last update dates on your posts, pages, and custom post types. You can choose which dates and its location.

== Description ==

PostDates allows you to easily display the publication and last update dates for your content. It includes a settings page where you can configure which dates to show, their position, and the date format.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/postdates` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings -> PostDates screen to configure the plugin.

== Frequently Asked Questions ==

= How do I change the position of the dates? =

Go to the plugin settings page and select the desired position (above or below the content).

= How do I change the format of the dates? =

Go to the plugin settings page and enter a valid PHP date format in the "Date Format" field.

== Screenshots ==

1. Plugin setting
   
   ![plugin settings](assets/screenshots/settings.jpg)
    
2. Display on page
   
   ![display on post](assets/screenshots/display.jpg)

== Changelog ==

= 2.0 =
* Added a date format option to the settings page.
* Implemented internationalization (I18N) for translation readiness.
* Refactored the code for better performance and maintainability.
* Improved security with more specific input sanitization.
* Fixed a bug in date display logic for different post types.

= 1.0 =
* Initial release of the PostDates plugin.
* Features include displaying publication and last update dates on posts, pages, and custom post types.
* Added settings page to configure date display options.
* Added option to choose the position of dates (above or below the content).

== Upgrade Notice ==

= 2.0 =
This version introduces a new date format option and includes significant code improvements. Your settings should be preserved, but it's always a good idea to check them after updating.

= 1.0 =
None.
