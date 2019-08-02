=== Woopra Analytics Plugin ===
Contributors: eliekhoury, markjaquith, ShaneF
Web site: http://www.woopra.com
Tags: statistics, analytics, stats, real-time, funnels, track visitors
Requires at least: 2.7.0
Tested up to: 4.8.1
Stable tag: 2.9.2

Track who is on your website, what pages they're browsing, actions they're taking, articles they're reading and more.

== Description ==

[Woopra](http://www.woopra.com/) is an end-to-end Customer Intelligence solution built for teams. Unify your customer data within the platform to analyze, optimize and engage across every customer touchpoint. Among the features, you'll find:

* [AppConnect](https://www.woopra.com/appconnect/): A suite of 40+ one-click integrations to seamlessly integrate data from your CRM, email, chat, social, support tools and more!
* [Customer Profiles](https://www.woopra.com/platform/#customer-profiles): As data syncs to Woopra from every touchpoint, Customer Profiles display a true to life view of user engagement, down to the individual-level. So whether you’re solving a support request or getting a pulse on customer health, you - and your team - will have clarity at your fingertips.
* [Customer Analytics](https://www.woopra.com/platform/#analytics): Whatever questions you have, there’s an analytics report waiting with answers. Full-funnel attribution, onboarding optimization, feature usage, retention reports, cohort analysis and so much more. Regardless of how you measure success, you're covered.
* [Automations](https://www.woopra.com/platform/#automations): Powerful triggers packed into each native integrations. Send a message to users based on any engagement criteria, personalize a live chat when customers experience an issue or instantly update a lead status when a prospect calls. Open the door to personalization at scale! 

Break free from the data siloes of mobile, app, product or web analytics. Combine them - and every other essential touchpoint - to see the world as your customers do with the Woopra platform. 

== Installation ==

These installation instructions assume you have an active account established on Woopra.com.  If not, please visit the site and sign up for service.

Using the manual method:

1. Extract the Woopra.zip file to a location on your local machine
2. Upload the `woopra` folder and all contents into the `/plugins/` directory

Using the automatic method:

1. Click on 'Download' or 'Upgrade'
2. Wait for WordPress to acknowledge that it is on your system and ready to activate.

After step 2 (of either method):

3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Make sure the status is On in the Woopra settings page.

IMPORTANT NOTE: In order for the WordPress Plugin to work, your WordPress theme must have the following code immediately before the </HEAD> element in the header.php file:

    `<?php wp_head(); ?>`

Without this function, the Woopra application will fail to track users who visit your site.

For more detailed installation instructions refer to: http://www.woopra.com/docs/tracking/

For more support refer to: http://support.woopra.com/

== Frequently Asked Questions == 

= What is Woopra? =

Woopra is redefining how companies understand, analyze, engage and retain their customers. By consolidating an organization’s siloed data within a singular hub - Woopra delivers a holistic, real-time, behavioral view of every prospect and visitor. 

 Translating data-driven intelligence into complete profiles for every customer enables brands to create a more intuitive experience and unparalleled customer service. With Woopra, every team within the organization is empowered with real-time insights. Optimize individual touchpoints, monitor product engagement and transform opinion-driven strategies into data-driven actions. 

 Woopra has acted as the catalyst for thousands of companies seeking to harness the power of their customer data - helping them to increase conversions and accelerate revenue while significantly improving customer satisfaction. 

Learn more at [https://www.woopra.com](https://www.woopra.com/)


= How to setup Woopra for WordPress? =

Simply install the Woopra plugin and activate it. You’ll instantly begin seeing visitors to your site and will be able to leverage this data across Woopra.

= My Woopra plugin is not working. =

Make sure that the plugin is active. If it's active and the code is not showing up on your pages (between `<head>` and `</head>`), make sure your theme supports/contains the `<?php wp_head(); ?>` hook.

== Changelog ==

= 2.9.2 (10-03-17) =
* [FIX] Fix compatibility with PHP < 5.2

= 2.9.1 (09-21-17) =
* [FIX] Fix version tag

= 2.9 (09-14-17) =
* [FIX] Fix deprecated use of preg_replace for PHP 7
* [UPDATE] Update setup link in the settings section

= 2.8 (06-13-17) =
* [FIX] Follow PHP strict standards and end support for PHP 4
* [UPDATE] Update plugin description

= 2.7.1 (06-23-15) =
* [FIX] WooCommerce events
* [FIX] Limit Article View to blog posts only (exclude custom post types)

= 2.7 (05-22-15) =
* [NEW] Add download tracking option
* [NEW] Add outgoing link tracking option
* [NEW] Add Woopra logo asset file

= 2.6.2 (08-26-14) =
* [FIX] Fix search event name (wp search)

= 2.6.1 (08-26-14) =
* [UPDATE] Add more comment properties

= 2.6 (08-25-14) =
* [NEW] Add “Article View” event (author, category, post time, permalink & title)
* [UPDATE] Better WooCommerce event tracking support

= 2.5.3 (02-18-14) =
* [FIX] Fix setup URL in WordPress plugin
* [FIX] Stop setting the cookie automatically from the back-end
* [UPDATE] Other Plugin Dashboard Layout & Links Changes
* [NEW] WooCommerce Support

= 2.5.2 (12-23-13) =
* [FIX] Fix tracking admins on non admin pages

= 2.5.1 (12-13-13) =
* [UPDATE] Add identify user on comment
* [UPDATE] Fix admin UI for the new version of WordPress

= 2.5 (12-11-13) =
* [NEW] Add signup tracking

= 2.4 (11-12-13) =
* [FIX] Fix bugs
* [UPDATE] Replace subdomain option with Track As

= 2.3.3 (11-11-13) =
* [UPDATE] Update woopra php SDK

= 2.3 (10-04-13) =
* [FIX] Fixed hide campaign option

= 2.2 (09-27-13) =
* [UPDATE] Update tracking to use the new Woopra PHP SDK
* [CHANGE] Removed old analytics reports unused files

= 2.0 (08-23-13) =
* [CHANGE] Update tracking code to version 4 

= 1.7 (07-04-12) =
* [NEW] Added a new property to allow you to track subdomains as main domains.
* [BUG] Fixed comments tracking
* [BUG] Fixed internal search tracking

= 1.6.1 (12-12-11) =
* [FIXED] Fixed the addVisitorProperty when adding an avatar property.

= 1.6 (12-08-11) =
* [CHANGE] Removed the old Analytics which is no longer compatible with the new API
* [CHANGE] Updated the Woopra Javascript Code
* [CHANGE] Move the Woopra code from the footer to the header for better tracking results
* [NEW] Added a link to the Woopra Live App on the Woopra settings page

= 1.5.0.1 (12-08-10) =
* [BUG] Fixed a problem with search values getting returned by reference (affects search query events)

= 1.5.0.0 (12-08-10) =
* [NEW] Added Asynchronous Javascript Support
* [BUG] Fixed compatibility issues with php 5.3
* [BUG] Fixed problems with search and comment events

= 1.4.7.1 (07-30-10) =
* [BUG] Fixed Author and Category Tracking ompatibility issues with custom themes.
* [BUG] Fixed help link

= 1.4.7 (07-29-10) =
* [NEW] Added Author and Category Tracking

= 1.4.6 (06-27-10) =
* [BUG] Fixed another issue with setIdleTimeout
* [BUG] Fixed a division by 0 issue
* [NEW] Added a few comments on some errors and how to resolve them

= 1.4.5 (06-25-10) =
* [NEW] Added support for Curl in case fopen is disabled on server.
* [BUG] Fixed setIdleTimeout javascript code
* [BUG] Fixed API Url, all requests for analytics should now be working
* [CHANGE] Added event name to javascript tracking code
* [CHANGE] Added extra error checks and handling code

= 1.4.3.2 (12-21-09) =

* [SECRUITY UPDATE] Removed 'ofc_upload_image.php' from the Open Flash Directory. Remove this file if you do upgrading manually.
* [SVN CHANGE] Made a 1.4 branch and moved 'trunk' to the new development version of '1.5.x'

= 1.4.3.1 (10-15-09) =

* [ADDED] New Visitor Percentage
* [REMOVED] Removed check 'Site not part of...'.

= 1.4.3 (10-12-09) =

* [NEW] Adding Error Handling Code
* [ACTIVATED] Uncommented code for events to work now! (Posting Comment, Searching)
* [BUG] Javascript for the Frontend again is updated to work with the new Woopra.js master file.
* [BUG] Fixed 'Site not part of...'. In most cases the XML data returned was not being interpreted correctly.
* [CHANGE] Events code re-written from scratch.
* [CHANGE] No more dropdown for the 'Timeout' feild. This value must be a whole number. Numbers will round down to the lower number if it's set a fraction. Setting it to '0' (Zero) will remove the override timeout.

= 1.4.2 (08-24-09) =

* [NEW] New SuperTab - Tagged Visitors - You can aggregate by name. More options to come in the future.
* [NEW] New Option - Auto Timeout - Up to 600 seconds (Default) for manually setting the timeout.
* [CHANGE] Changed to the new javascript convention. No change it what is outputed.

= 1.4.1.1 (07-23-09) =

* [BUG] "Parse error: parse error in \woopra\inc\chart.php on line 130": Reverted back to 4.3.0 format.
* [BUG] "Parse error: parse error, expecting T_OLD_FUNCTION' orT_FUNCTION' or T_VAR' or'}'' in \plugins\woopra\inc\chart.php on line 194": Moved array values into a global var in the class.
* [BUG] Forgot hook_action name for toplevel support.
* [BUG] Removed index.html files from both the 1.4.1 tag and trunk. Was causing errors during automatic upgrade.
* [BUG] Ignoring admin visits fixed.
* [BUG] Detection in admin section is now being set correctly.
* [BUG] Removed all PHP 5 stuff. :(
* [BUG] Fixed Referrers Subsections: Regular Referrers, Search Engines, Emails, Social Bookmarks, and Social Networks when trying to expand to view the charts now works.
* [BUG] API Key now transfers correctly.

= 1.4.1 (07-20-09) =

* [NEW] Woopra moved into a 'php class' format
* [NEW] Woopra event tracking made more universal. Moved to the 'wp' hook for tracking
* [NEW] Woopra settings moved into a single varabile
* [NEW] XML-API version 2.1 being used.
* [NEW] jQuery used for Datepicker: ui.datepicker.js
* [UPGRADE] Woopra now requires at least WordPress 2.7.x
* [UPGRADE] All functions are now documented
* [UPGRADE] Open-Flash-Charts Version 2
* [BUG] Woopra can now operate out of any directory. Doesn't matter the location.
