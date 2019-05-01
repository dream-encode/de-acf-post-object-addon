=== ACF Post Object Field Type Add-on ===
Contributors: davidbaumwald
Tags: acf, acf post object, acf post title, post object field type
Requires at least: 3.5
Tested up to: 5.1.1
Stable tag: 1.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An ACF extension that appends selected field data to post titles for the "Post Object" field type.

== Description ==

This plugin extends the basic functionality of the "Post Object" field type within Advanced Custom
Fields(ACF).  As such, ACF is required.  Once enabled and configured, each post listed in the
post dropdown is suffixed with extra post data, including other ACF field data, to aid in post
differentiation.

Features:

*   Choose from post ID or ACF field to suffix to post title
*   Setting for format of suffixed text

== Installation ==

This section describes how to install the plugin and get it working.

1. Ensure Advanced Custom Fields is installed and activated
1. Upload the plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Configure settings under Custom Fields -> ACF Post Object Add-On

== Screenshots ==

1. Settings - Enable add-on, field to suffix to post title, and formatting options.
2. Example of what the post object field type looks like once the plugin is activated and configured.  In
this case, an ACF for "SKU" is appended to each post title in the dropdown.

== Changelog ==

= 1.1.4 =
* Add ACF version check for "acf_get_fields_by_id" compatibility

= 1.1.3 =
* Bumping version to resolve merge conflicts in 1.1.2 tagged release.

= 1.1.2 =
* Removing uninstall hook in favor of uninstall.php.

= 1.1.1 =
* Adding install and uninstall hooks.

= 1.1 =
* Adding i18n

= 1.0 =
* Initial release.
