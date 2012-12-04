=== VideoJS - HTML5 Video Player for Wordpress ===
Contributors: Steve Heffernan, Dustin Lammiman
Donate link: http://videojs.com/
Tags: html5, video, player, javascript
Requires at least: 2.7
Tested up to: 3.4.2
Stable tag: 3.2.2

Video.js is the most widely used HTML5 Video Player available. It allows you to embed video in your post or page using HTML5.

== Description ==

A video plugin for WordPress built on the Video.js HTML5 video player library. Allows you to embed video in your post or page using HTML5 with Flash fallback support for non-HTML5 browsers.

View [videojs.com](http://videojs.com) for additional information.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `videojs-html5-video-player-for-wordpress` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the [video] shortcode in your post or page using the following options.

Video Shortcode Options
-----------------------

### mp4
The location of the h.264/MP4 source for the video.
    
    [video mp4="http://video-js.zencoder.com/oceans-clip.mp4"]

### ogg
The location of the Theora/Ogg source for the video.

    [video ogg="http://video-js.zencoder.com/oceans-clip.ogg"]

### webm
The location of the VP8/WebM source for the video.

    [video webm="http://video-js.zencoder.com/oceans-clip.webm"]

### poster
The location of the poster frame for the video.

    [video poster="http://video-js.zencoder.com/oceans-clip.png"]

### width
The width of the video.

    [video width="640"]

### height
The height of the video.

    [video height="264"]

### preload
Start loading the video as soon as possible, before the user clicks play.
Use 'auto', 'metadata', or 'none'. Auto will preload when the browser or device allows it. Metadata will load only the meta data of the video.

    [video preload="auto"]

### autoplay
Start playing the video as soon as it's ready. Use 'true' or 'false'.

    [video autoplay="true"]

### loop
Causes the video to start over as soon as it ends. Use 'true' or 'false'.

    [video loop="true"]

### controls
Use 'false' to hide the player controls.

    [video controls="false"]
    
### id
Add a custom ID to your video player.

    [video id="movie-id"]
    
### class
Add a custom class to your player. Use full for floating the video player using 'alignleft' or 'alignright'.

    [video class="alignright"]

### Tracks
Text Tracks are a function of HTML5 video for providing time triggered text to the viewer. To use tracks use the [track] shortcode inside of the [video] shortcode. You can set values for the kind, src, srclang, label, and default attributes. More information is available in the [Video.js Documentation](http://videojs.com/docs/tracks/).

    [video][track kind="captions" src="http://video-js.zencoder.com/oceans-captions.vtt" srclang="en" label="English" default="true"][/video]

### All Attributes Example

    [video mp4="http://video-js.zencoder.com/oceans-clip.mp4" ogg="http://video-js.zencoder.com/oceans-clip.ogg" webm="http://video-js.zencoder.com/oceans-clip.webm" poster="http://video-js.zencoder.com/oceans-clip.png" preload="auto" autoplay="true" width="640" height="264" id="movie-id" class="alignleft" controls="false"][track kind="captions" src="http://example.com/path/to/captions.vtt" srclang="en" label="English" default="true"][/video]
    

Video.js Settings Screen
------------------------
The values set here will be the default values for all videos, unless you specify differently in the shortcode. Uncheck "Use CDN hosted version?" if you want to use a self-hosted copy of Video.js instead of the CDN hosted version. *Using the CDN hosted version is preferable in most situations.*


== Changelog ==

= 3.2.2 =

* Fixed preload="none".

= 3.2.1 =

* Added support for tracks.
* Added loop support.
* Added the ability to disable controls.
* Fixed a bug with the autoplay attribute.
* Fixed mixed-content warnings on SSL pages.
* Added a license to the plugin folder (LGPLv3).

= 3.2.0 =

* Fixed a bug with the self-hosted option.
* Updated self-hosted files to Video.js 3.2.0.

= 3.0.1 =

* Added an options page to set default values for you video players.
* Added an option to use a self-hosted version of the script instead of the CDN hosted version.
* Added a new class="" option to the shortcode.
* Minor bug fixes.

= 3.0 =

* Completely re-worked for Video.js 3.0, which uses a CDN to host the script and the same HTML/CSS skin and JavaScript API for HTML5 and Flash Video.
* See videojs.com for specific changes to the script.
* Removed files used by old versions of Video.js.

= 2.0.2 = 

* Upgraded to version 2.0.2 of VideoJS. See videojs.com for specific version updates.

= 1.1.3 = 

* Added poster to Flash fallback

= 1.1.2 = 

* Updated to VideoJS 1.1.2
* Fixed the default plugin folder name.
* Fixed an issue with autoplay not working correctly.

= 1.0.1 =

* Updated to latest version of VideoJS

= 1.0 =

* First release.
