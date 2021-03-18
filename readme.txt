=== Hyper PWA ===
Contributors: rickey29
Donate link: https://flexplat.com
Tags: progressive web apps, pwa, wordpress, wp, plugin, manifest, web manifest, web app, offline support, add to homescreen, accelerated mobile pages, amp, pwamp, performance, speed up, service worker, service workers, workbox
Requires at least: 5.1
Tested up to: 5.7
Requires PHP: 7.2
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Converts WordPress into Progressive Web Apps style.

== Description ==
Hyper PWA plugin converts WordPress into Progressive Web Apps style.  It helps your website

* Pass Lighthouse PWA audit;
* Work compatible with AMP;
* Display an Offline Page when network is not available;
* Bypass WordPress Administration Dashboard for PWA.

== Highlight ==
This plugin is relying on a 3rd party Software as a Service -- FlexPlat: https://flexplat.com to generate PWA Service Workers related files.  The Terms and Conditions is located at: https://flexplat.com/terms-and-conditions/

In detail, to make PWA working, end users will ask your website to provide Service Workers related files: manifest.json, hyper-pwa-sw.js, hyper-pwa-sw.html and offline.html.  Inside of producing these files within my plugin, my plugin will send necessary parameters to FlexPlat, FlexPlat will build the Service Workers related files based on the received parameters, and return these files to your website.  Then my plugin forwards these files to end users.

== Open Issue ==
None.

== Demo ==
1. https://flexplat.com

== Screenshots ==
1. https://download.flexplat.com/flexplat.png

== Download ==
1. GitHub: https://github.com/rickey29/hyper-pwa-wordpress
2. WordPress Plugins Libraries: https://wordpress.org/plugins/hyper-pwa/

== Installation ==
1. Upload the plugin files to the '/wp-content/plugins/hyper-pwa' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

== Configuration ==
None.

== Upgrade Notice ==
None.

== Frequently Asked Questions ==

= How to validate my website PWA status? =

I use Google Chrome Lighthouse PWA audit.  You can Google to find more solutions.

== Changelog ==

= 1.2.0 =
(Thur., Mar. 18, 2021)
* Pass Lighthouse PWA audit.
* Work compatible with AMP.
* Display an Offline Page when network is not available.
* Bypass WordPress Administration Dashboard for PWA.

= 1.1.0 =
(Thur., Mar. 04, 2021)
* Update according to WordPress Plugin Handbook.

= 1.0.0 =
(Tue., Mar. 02, 2021)
* Submission accepted by WordPress Plugin Review Team.

= 0.3.0 =
(Tue., Mar. 02, 2021)
* Update continued according to the comments of WordPress Plugin Review Team.

= 0.2.0 =
(Sat., Feb. 27, 2021)
* Update according to the comments of WordPress Plugin Review Team.

= 0.1.0 =
(Wed., Feb. 21, 2021)
* primary development

== Support ==
Author: Rickey Gu
Web: https://flexplat.com
Email: rickey29@gmail.com
Twitter: @rickey29
