=== Make Filename Lowercase ===
Contributors: ereckers
Donate link: http://www.redbridgenet.com/make-payment/
Tags: strtolower, lowercase, filename, media, upload, sanitize_file_name, strtolower filename, lowercase filename, strtolower media, lowercase media, strtolower upload, lowercase upload
Requires at least: 3.0.1
Tested up to: 4.1.1
Stable tag: 1.0.2

Sets uploaded media filename to lowercase.

== Description ==

Sets uploaded media filename to lowercase as filter on sanitize_file_name. Based on post at Stack Overflow asking if there is a way to "Rename files during upload within Wordpress 3.0 backend".

Created for WordPress 3.0.1+ which saves any uploaded media filename (Image, Video, Audio) as lowercase. This effectively changes the name of the file at upload if a user were to upload a file with any uppercase characters.

For instance:

* MOVIE-FILE.MOV > movie-file.mov
* Movie-File.wmv > movie-file.wmv
* ImAgEfILe.gif > imagefile.gif
* imageFile.JPG > imagefile.jpg
* ETC > etc

<em>Note: WordPress sets file extension to lowercase. This plugin does a strtolower on the filename.</em>

<h4>More Information</h4>

Visit the <a href="http://www.redbridgenet.com/">Red Bridge Internet</a> site for more information about <a href="http://www.redbridgenet.com/make-filename-lowercase/">Make Filename Lowercase</a> plugin.

== Installation ==

1. Download the plugin and unzip it.
2. Upload the folder /make-filename-lowercase to your /wp-content/plugins/ folder.
3. Activate the plugin from your WordPress admin panel.
4. Installation finished.

== Screenshots ==

1. Upload media filename of mixed case (in this case, the file MIXED-cAsE-filename.JPG).
2. Filename changed to lowercase (mixed-case-filename.jpg) preserving the case for Title.
3. An image detail view from the Media Library showing the new File name as lowercase.

== Frequently Asked Questions ==

= Why was this plugin created? =

This plugin evolved from a client requirement that all uploaded media filenames contain no uppercase characters.

= Why did you release this plugin? =

It was useful to me and I wanted to work through the process of releasing a WordPress plugin.

= Why shouldn't this just be added to a theme's functions.php file? =

Upgradeability, portability, backwards compatibility, and other *abilities. If for some reason this function were in need of customization due to a WordPress version upgrade, the plugin will be centrally responsible for releasing the fix as opposed to updating the theme(s).

== Changelog ==

= 1.0.2 =

* moved plugin screenshots to assets directory
* added cool header banner for plugin repository

= 1.0.1 =

* Tested for 3.3.1

= 1.0.0 =

* Initial release

== Upgrade Notice ==

