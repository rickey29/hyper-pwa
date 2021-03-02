=== Hyper PWA ===
Contributors: rickey29
Donate link: https://flexplat.com
Tags: progressive web apps, pwa, wordpress, wp, plugin, accelerated mobile pages, amp, performance, speed up, pwamp
Requires at least: 4.7
Tested up to: 5.6.2
Requires PHP: 5.2.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Converts Accelerated Mobile Pages WordPress into Progressive Web Apps style.

== Description ==
Hyper PWA plugin converts Accelerated Mobile Pages WordPress into Progressive Web Apps style.  It helps you pass PWA validation testing/audit.

== Highlight ==
This plugin is relying on a 3rd party Software as a Service -- FlexPlat: https://flexplat.com to generate PWA Service Workers JavaScript file.  The Terms and Conditions is located: https://flexplat.com/terms-and-conditions/

In detail, to make PWA working, end users will ask your website to provide a Service Workers JavaScript file (in my case, the name of the file is https://yoursite/hyper-pwa-sw.js).  Inside of producing the file within my plugin, my plugin will send necessary parameters (so far, the necessary paramter is "plugin directory", where my plugin is located) to FlexPlat, FlexPlat will build the Service Workers JavaScript file based on these parameters, and return this file to your website.  Then my plugin forwards this file to the end users.

== Open Issue ==
None.

== Demo ==
1. https://apparel.flexplat.com/
2. https://boat-rental.flexplat.com/
3. https://book-shop.flexplat.com/
4. https://digital-store.flexplat.com/
5. https://electronics-shop.flexplat.com/
6. https://jewellery-shop-2.flexplat.com/
7. https://perfume-shop.flexplat.com/
8. https://pet-shop.flexplat.com/
9. https://restaurant.flexplat.com/
10. https://restaurant-2.flexplat.com/
11. https://shop.flexplat.com/

== Screenshots ==
1. https://download.flexplat.com/apparel.png
2. https://download.flexplat.com/boat-rental.png
3. https://download.flexplat.com/book-shop.png
4. https://download.flexplat.com/digital-store.png
5. https://download.flexplat.com/electronics-shop.png
6. https://download.flexplat.com/jewellery-shop-2.png
7. https://download.flexplat.com/perfume-shop.png
8. https://download.flexplat.com/pet-shop.png
9. https://download.flexplat.com/restaurant.png
10. https://download.flexplat.com/restaurant-2.png
11. https://download.flexplat.com/shop.png

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

= How to audit my website PWA validation status? =

I use Google Chrome Lighthouse.  You can Google to find more solution.

== Changelog ==

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
