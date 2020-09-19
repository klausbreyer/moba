=== mobile batch ===
Contributors: klausbreyer
Author: Klaus Breyer
Author URI: https://v01.io
Plugin URI: https://v01.io/2017/11/04/mobile-batch-upload-wordpress-plugin/
Tags: upload, mobile, images, pictures, batch,
Requires at least: 3.3
Tested up to: 5.5
Stable tag: 0.2
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A handy little plugin for batch uploading pictures from your mobile phone into a wordpress posting.
This is useful, because the mobile app often have some hickups when uploading a lot or large (or both) pictures. Through this plugin you can upload all of them from your mobile browser. 

Given that you have LTE or a stable Wifi, there should be no hickups. 

== Installation ==
1. Copy the folder `moba` into the directory `wp-content/plugins/` and activate the plugin.
2. Go to the "Mobile Batch" Dashboard page to start uploading
3. Leave a positive rating.. :)

Note: On some  installations it may be necessary to increase the PHP memory limit. In internal tests, 100M have not always been enough. The recommendation is to set the PHP memory limit to 200M.

== Screenshots ==

1. The form in its empty state
2. The filled out form
3. Successfull upload

== Changelog ==

= 0.2.1 =
Now the input only accepting images. 

= 0.2 =
The first major upate. Now it is really tested out in the wild.
* Read exif data and rotate picture of necessary.
* Added comment/discussion setting for the post.
* Async upload to handle an unlimited number of pictures and sanitize/validate transferred data.
* Empty lines between pictures.
* Set last picture as thumbnail of the posting.


= 0.1 =
The very first public release.
* Upload photos as batch selection
* Choose if should be a draft or published
* Copy the link to your mobile phone


== Debugging ==
* If only one post is being created, but none or not all are uploaded: Depending on the server load, it may be helpful to increase the memory limit. It should be at least 100m, better 200m.