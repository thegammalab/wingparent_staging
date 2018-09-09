=== WP Staging - DB & File Duplicator & Migration  === 

Author URL: https://wordpress.org/plugins/wp-staging
Plugin URL: https://wordpress.org/plugins/wp-staging
Contributors: ReneHermi, WP-Staging
Donate link: https://wordpress.org/plugins/wp-staging
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: staging, duplication, cloning, clone, migration, sandbox, test site, testing, backup, post, admin, administration, duplicate posts
Requires at least: 3.6+
Tested up to: 4.9
Stable tag: 2.3.4
Requires PHP: 5.3

A duplicator plugin! Clone, duplicate and migrate live sites to independent staging and development sites that are available only to administrators.

== Description == 

<strong>This cloning and staging plugin is well tested and work in progress. <br><br>
If you find any issue, please open a [support ticket](https://wp-staging.com/support/ "support ticket").
</strong>
<br /><br />
<strong>Note: </strong> For pushing & migrating plugins and theme files to live site, check out the pro version [https://wp-staging.com/](https://wp-staging.com/ "WP Staging Pro")
<br /><br />
<blockquote>
<h4> WP Staging for WordPress Migration </h4>
This duplicator plugin allows you to create an staging or development environment in seconds* <br /> <br />
It creates a clone of your website into a subfolder of your main WordPress installation including an entire copy of your database. 
This sounds pretty simple and yes it is! All the hard time-consumptive database and file copying stuff including url replacements is done in the background.
 <br /> <br />
I created this plugin because all other solutions are way too complex, overloaded with dozens of options or having server requirements which are not available on most shared hosting solutions.
All these reasons prevent user from testing new plugins and updates first before installing them on their live website, so its time to release a plugin which has the potential to be merged into everyone´s wordpress workflow.
 <br /><br />
<p><small><em>* Time of creation depends on size of your database and file size</em></small></p>
</blockquote>

WP Staging helps you to prevent your website from being broken or unavailable because of installing untested plugin updates! 
<p>Note: WordPress 5.0 will be shipped with a new visual editor called Gutenberg. Use WP Staging to check if Gutenberg editor is working as intended on your website and that all used plugins are compatible with that new editor.</p>


[youtube https://www.youtube.com/watch?v=Ye3fC6cdB3A]

= Main Features =

* <strong>Easy: </strong> Staging migration applicable for everyone. No configuration needed!
* <strong>Fast: </strong> Migration process lasts only a few seconds or minutes, depending on the site's size and server I/O power
* <strong>Safe: </strong> Access to staging site is granted for administrators only.
<br /><br />
<strong>More safe:</strong> 
<br>
* Admin bar reflects that you are working on a staging site
* Extensive logging if duplication or  migration process should fail.
* New: Compatible to All In One WP Security & Firewall

= What does not work or is not tested when running wordpress migration? =

* Wordpress migration of wordpress multisites (not tested)
* WordPress duplicating process on windows server (not tested but will probably work) 
Edit: Duplication on windows server seems to be working well: [Read more](https://wordpress.org/support/topic/wont-copy-files?replies=5 "Read more") 


<strong>Change your workflow of updating themes and plugins data:</strong>

1. Use WP Staging for migration of a production website to a clone site for staging purposes
2. Customize theme, configuration and plugins or install new plugins
3. Test everything on your staging site first
4. Everything running as expected? You are on the save side for migration of all these modifications to your production site!


<h3> Why should i use a staging website? </h3>

Plugin updates and theme customizations should be tested on a staging platform first. Its recommended to have the staging platform on the same server where the production website is located.
When you run a plugin update or plan to install a new one, it is a necessary task to check first the modifications on a clone of your production website.
This makes sure that any modifications is  working on your website without throwing unexpected errors or preventing your site from loading. (Better known as the wordpress blank page error)

Testing a plugin update before installing it in live environment isn´t done very often by most user because existing staging solutions are too complex and need a lot of time to create a 
up-to-date copy of your website.

Some people are also afraid of installing plugins updates because they follow the rule "never touch a running system" with having in mind that untested updates are increasing the risk of breaking their site.
I totally understand this and i am guilty as well here, but unfortunately this leads to one of the main reasons why WordPress installations are often outdated, not updated at all and unsecure due to this non-update behavior.

<strong> I think its time to change this, so i created "WP Staging" for WordPress migration of staging sites</strong>

<h3> Can´t i just use my local wordpress development copy for testing like xampp / lampp? </h3>

Nope! If your local hardware and software environment is not a 100% exact clone of your production server there is NO guarantee that every aspect 
of your local copy is working on your live website exactely as you would expect it. 
There are some obvious things like differences in the config of php and the server you are running but even such non obvious settings like the amount of ram or the 
the cpu performance can lead to unexpected results on your production website. 
There are dozens of other possible cause of failure which can not be handled well when you are testing your changes on a local staging platform.

This is were WP Staging steps in... Site cloning and staging site creation simplified!

<h3>I just want to migrate the database from one installation to another</h3>
If you want to migrate your local database to a already existing production site you can use a tool like WP Migrate DB.
WP Staging is only for creating a staging site with latest data from your production site. So it goes the opposite way of WP Migrate DB.
Both tools are excellent cooperating eachother.

<h3>What are the benefits compared to a plugin like Duplicator?</h3>
At first, i love the [Duplicator plugin](https://wordpress.org/plugins/duplicator/ "Duplicator plugin"). Duplicator is a great tool for migrating from development site to production one or from production site to development one. 
The downside is that Duplicator needs adjustments, manually interventions and prerequirements for this. Duplicator also needs some skills to be able to create a development / staging site, where WP Staging does not need more than a click from you.
However, Duplicator is best placed to be a tool for first-time creation of your production site. This is something where it is very handy and powerful.

So, if you have created a local or webhosted development site and you need to migrate this site the first time to your production domain than you are doing nothing wrong with using
the Duplicator plugin! If you need all you latest production data like posts, updated plugins, theme data and styles in a testing environment than i recommend to use WP Staging instead!

= I need you feedback =
This plugin has been done in hundreds of hours to work on even the smallest shared webhosting package but i am limited in testing this only on a handful of different server so i need your help:
Please open a [support request](https://wordpress.org/support/plugin/wp-staging/ "support request") and describe your problem exactely. In wp-content/wp-staging/logs you find extended logfiles. Have a look at them and let me know the error-thrown lines.


= Important =

Permalinks are disabled on the staging site because the staging site is cloned into a subfolder and permalinks are not working on all systems
without doing changes to the .htaccess (Apache server) or nginx.conf (Nginx Server).
[Read here](https://wp-staging.com/docs/activate-permalinks-staging-site/ "activate permalinks on staging site") how to activate permalinks on the staging site.

 
= How to install and setup? =
Install it via the admin dashboard and to 'Plugins', click 'Add New' and search the plugins for 'Staging'. Install the plugin with 'Install Now'.
After installation goto the settings page 'Staging' and do your adjustments there.


== Frequently Asked Questions ==

* I can not login to the staging site
If you are using a security plugin like All In One WP Security & Firewall you need to install latest version of WP Staging. 
Go to WP Staging > Settings and add the slug to the custom login page which you set up in All In One WP Security & Firewall plugin.



== Official Site ==
https://wp-staging.com

== Installation ==
1. Download the file "wp-staging.zip":
2. Upload and install it via the WordPress plugin backend wp-admin > plugins > add new > uploads
3. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==

1. Step 1. Create new WordPress staging site
2. Step 2. Scanning your website for files and database tables
3. Step 3. Wordpress Staging site creation in progress
4. Finish!

== Changelog ==

= 2.3.4 =
* New: Compatible up to WordPress 4.9.8
* New: Support for Windows Azure cloud servers
* Fix: Missing http(s) scheme after cloning multisites results in not working clones

= 2.3.3 =
* New: Compatible up to WordPress 4.9.8
* New: Better error reporting
* New: Detect if wp-config.php has been moved one folder level up
* New: Login options not needed any more - removed
* Fix: Remove term 'Error' from login page

= 2.3.2 =
* Fix: Do not search & replace mail addresses

= 2.3.1 =
* Fix: Creating a new clone resets the custom wp staging user settings. E.g. ignores authentication setting
* Fix: Search & replace path not correct if scheme http/https is not identical in wp-config and db wp_options
* New: Setting to specify the maximum allowed file size to copy

= 2.3.0 =
* Security: Do not allow to create a new staging site into a subfolder which already exists
* New: Option to allow adjustment of the allowed maximum size of files that are going to be copied while cloning.
* New: Add multisite informations in system info log
* New: Option to allow adjustment of the allowed maximum size of files that are going to be copied while cloning.
* New: Use the new progress bar for clone updating process
* Fix: Progress bar for step 'database' is not filling up to 100%
* Fix: If cloning update process is interupted it may happen that staging site is not available any longer. (Updating the clone does not copy index.php to staging site again)
* Fix: Progress bar not shown as intented for clone updating process
* Fix: Can not open upload folder in file selection menu
* Fix: Undefined object $this->tables
* Fix: wp-config.php not copied when previous clone updating process has been failed
* Fix: Parameter must be an array or an object that implements Callable
* Fix: Skip search & replace for objects where key is null
* Fix: Search & Replace not working if serialized object contains __PHP_Incomplete_Class_Name
* Tweaks: remove term "error" from several log entries
* Tweak: Remove certain debugging notices from the default log window

= 2.2.9 =
* Fix: Missing trailingslash results to wrong absolute paths in database after Search & Replace operation 

= 2.2.8 =
* New: Add filter 'wpstg_filter_options_replace' to exclude certain tables from updating while cloning
* New: Exclude tables for plugin wp_mail_smtp
* New: Support for custom upload folder. For instance, if upload folder has been renamed and removed outsite wp-content folder
* New: Add datetime timestamp internally to clone. (Used in WP Staging pro)
* New: Add filter 'wpstg_fiter_search_replace_rows' to exclude certain tables from search & replace
* New: Supports search & replace for revslider image slider and several visual editors which are using non default serialized data
* New: Add new setting which allow to specify the search & replace processing query limit
* New: Compatible to WordPress 4.9.6


= 2.2.7 =
* Fix: Serialize replace is not working properly for serialized strings
* Fix: WP_SITEURL & WP_HOME not replaced if constant contains php generated string


Complete changelog: [https://wp-staging.com/changelog.txt](https://wp-staging.com/changelog.txt)

== Upgrade Notice ==

= 2.3.4 =
* New: Compatible to WordPress 4.9.7

