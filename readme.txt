=== Hyper PWA ===
Contributors: rickey29
Donate link: https://flexplat.com
Tags: progressive web apps, pwa, web app manifests, service worker, workbox
Requires at least: 5.1
Tested up to: 5.7.2
Requires PHP: 7.2
Stable tag: 1.14.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Convert WordPress into Progressive Web Apps style.

== Description ==
Hyper PWA plugin converts WordPress into Progressive Web Apps style.

Basic Features:
* Based on WorkBox;
* Pass Lighthouse PWA audit;
* Work compatible with AMP;
* Display an offline page when network is not available;
* Make PWA bypass WordPress Administration Dashboard.

Premium Features:
* Support Workbox Precaching;
* Support Workbox Navigation Preload;
* Support Workbox Strategies;
* Support Workbox Common Recipes;
* Support Workbox Advanced Recipes;
* Support Workbox DefaultHandler Routing;
* Support Workbox Google Analytics.

== Highlight ==
This plugin is relying on a 3rd party Software as a Service -- FlexPlat: https://flexplat.com to generate PWA Service Workers related files.  The Terms and Conditions is located at: https://flexplat.com/terms-and-conditions/

In detail, to make PWA working, end users will ask your website to provide Service Workers related files:
* hyper-pwa-manifest.json
* hyper-pwa-offline.html
* hyper-pwa-service-worker.html
* hyper-pwa-service-worker.js
* hyper-pwa-service-worker-unregister.html
Inside of producing these files within my plugin, my plugin will send necessary parameters to FlexPlat, FlexPlat will build the Service Workers related files based on the received parameters, and return these files to your website.  Then my plugin forwards these files to end users.

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

I use Google Chrome Lighthouse PWA audit.  You can Google to find more tools.

= How to add my website to mobile device home screen? = 

https://natomasunified.org/kb/add-website-to-mobile-device-home-screen/

= Does this plugin support Push Notifications? =

No.  You can use other plugins, such as OneSignal: https://wordpress.org/plugins/onesignal-free-web-push-notifications/

= During Google Chrome Lighthouse PWA audit, I get following error message: "No matching service worker detected. You may need to reload the page, or check that the scope of the service worker for the current page encloses the scope and start URL from the manifest."  And in Chrome Console, I get following error message: "The script has an unsupported MIME type (\'text/html\')."  What should I do now? =

If your website uses any cache plugin, purge the cache.  If your website uses any CDN/cache server, purge the cache.  Go to your web browser Developer Tools, unregister Service Worker and clear cache.  Then redo the audit.
If it is still not working, you must use some cache plugin.  Let your cache plugin not caching "https://yoursite/hyper-pwa-service-worker.js" -- set above link as an exception to the caching.  Go to your web browser Developer Tools, unregister Service Worker and clear cache.  Then redo the audit.

= Get the following error message in web browser console: "The service worker navigation preload request was cancelled before 'preloadResponse' settled. If you intend to use 'preloadResponse', use waitUntil() or respondWith() to wait for the promise to settle." What should I do now? =

https://stackoverflow.com/questions/66818391/service-worker-the-service-worker-navigation-preload-request-failed-with-networ

== Premium ==
Each web page is different, so the best cache strategy for each web page is different.  If you want to have a personalization/customization Service Worker solution for each page of your site, instead of one solution for the whole site, I can do it for you.  It is a paid service.  Send email to me: rickey29@gmail.com .

Price:
*  10 USD per month, or   100 USD per year, when your website page number is between      1 to      9;
*  20 USD per month, or   200 USD per year, when your website page number is between     10 to     99;
*  40 USD per month, or   400 USD per year, when your website page number is between    100 to    999;
*  80 USD per month, or   800 USD per year, when your website page number is between  1,000 to  9,999;
* 160 USD per month, or 1,600 USD per year, when your website page number is between 10,000 to 99,999;
... ... ...
All above items include a 30 days free trial.

== Changelog ==

= 1.14.0 =
(Thur., Jul. 08, 2021)
* New Feature: Support Workbox Background Sync.

= 1.13.0 =
(Mon., Jun. 28, 2021)
* Improvement for PluginTests.

= 1.12.0 =
(Mon., Jun. 21, 2021)
* Improvement for Lighthouse Audit.

= 1.11.0 =
(Mon., Jun. 14, 2021)
* New feature: Use corn job to refresh cache.

= 1.10.0 =
(Tue., May 25, 2021)
* Bug fix: nonce not working for multiple users.

= 1.9.0 =
(Mon., May 24, 2021)
* Update according to WordPress Plugin Security guideline.

= 1.8.0 =
(Fri., May 07, 2021)
* New feature: multiple recipes.

= 1.7.0 =
(Fri., Apr. 23, 2021)
* Improve Service Worker recipe.

= 1.6.0 =
(Mon., Apr. 19, 2021)
* Improve Service Worker recipe.

= 1.5.0 =
(Fri., Apr. 09, 2021)
* Improve Service Worker recipe.

= 1.4.0 =
(Sun., Apr. 04, 2021)
* Deactivate Service Worker within Administration Dashboard.

= 1.3.0 =
(Tue., Mar. 30, 2021)
* Provide plugin Settings Page.

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
