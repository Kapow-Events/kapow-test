<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	?></title>
 <link href="http://fonts.googleapis.com/css?family=Quicksand:400,300,700%7CCabin:400,600,500%7CHomenaje" rel="stylesheet" type="text/css">

  <link rel="icon" href="http://ec2-23-20-73-152.compute-1.amazonaws.com/skin/frontend/default/kapowdesign/images/favicon.gif" type="image/x-icon">
  <link rel="shortcut icon" href="http://ec2-23-20-73-152.compute-1.amazonaws.com/skin/frontend/default/kapowdesign/images/favicon.gif" type="image/x-icon">
  
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" href="http://ec2-23-20-73-152.compute-1.amazonaws.com/skin/frontend/default/kapowdesign/css/custom.css" media="all">
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
</head>

<body <?php body_class(); ?>>
<!-- BEGIN GOOGLE ANALYTICS CODE -->
<script type="text/javascript">
//<![CDATA[
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
    })();

    var _gaq = _gaq || [];

_gaq.push(['_setAccount', 'UA-31806591-1']);
_gaq.push(['_trackPageview']);


//]]>
</script>
<!-- END GOOGLE ANALYTICS CODE -->
<div id="wrapper" class="hfeed wrapper">
    <noscript>
        <div class="noscript">
            <div class="noscript-inner">
                <p><strong>JavaScript seem to be disabled in your browser.</strong></p>
                <p>You must have JavaScript enabled in your browser to utilize the functionality of this website.</p>
            </div>
        </div>
    </noscript>
    <div class="page">
              
      <header id="header" class="outer_wrapper fixed">
        <div class="inner_wrapper">
          <h1 id="logo"><a href="/"></a></h1>
    
          <div id="primary_navigation">
            <ul id="primary_navigation_left">
    			<li><a href="http://ec2-23-20-73-152.compute-1.amazonaws.com/all-events">all events</a></li>
    			<li><a href="http://ec2-23-20-73-152.compute-1.amazonaws.com/">home</a></li>
    		</ul>        
    		<ul id="primary_navigation_right">
			    <li><a data-rel="#about_events_wired" href="http://ec2-23-20-73-152.compute-1.amazonaws.com/about-magento-demo-store/">about</a></li>
			    <li><a data-rel="#recent_success" href="http://ec2-23-20-73-152.compute-1.amazonaws.com/#recent_success">success</a></li>
    		</ul>      
    	  </div><!-- primary_navigation -->
    	  <div id="secondary_navigation">
    	  		<div id="leftarrow" class="arrow"></div>
    	        <div id="rightarrow" class="arrow"></div>
    	          <ul id="secondary_navigation_left">
    	         	<li class="first"><a href="http://ec2-23-20-73-152.compute-1.amazonaws.com/sales/order/history/" title="My Account">My Account</a></li>
    	  			<!--  <li class="spacer_dot"></li> -->
    	            <li class=" last"><a href="http://ec2-23-20-73-152.compute-1.amazonaws.com/customer/account/login/referer/aHR0cDovL2VjMi0yMy0yMC03My0xNTIuY29tcHV0ZS0xLmFtYXpvbmF3cy5jb20v/" title="Sign In">Sign In</a></li>          
    	          </ul>
    	          <ul id="secondary_navigation_right">
    	  			<li><a data-rel="#recent_success" href="http://ec2-23-20-73-152.compute-1.amazonaws.com/#events">my events</a></li>
    	  			<li><a data-rel="#recent_success" href="http://ec2-23-20-73-152.compute-1.amazonaws.com/#contact">contact</a></li>
    	  		  </ul>      
    	  		</div><!-- secondary_navigation -->
	      </div><!-- header.inner_wrapper -->
	    </header><!-- header.outer_wrapper -->
		<!--<div id="access" role="navigation"> Blog Nav
		  <?php /* Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
			<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>"><?php _e( 'Skip to content', 'twentyten' ); ?></a></div>
			<?php /* Our navigation menu. If one isn't filled out, wp_nav_menu falls back to wp_page_menu. The menu assiged to the primary position is the one used. If none is assigned, the menu with the lowest ID is used. */ ?>
			<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
		</div>--><!-- #access -->
	<div id="main_content" class="outer_wrapper clearfix">
		<div class="inner_wrapper">
			<div id="homepage_body_wrapper" class="outer_wrapper">
				<h1 class="news-title"><a href="/blog">KAPOW NEWS</a><div class="social-header">Follow Us On<span class="arrow"></span><a href="http://www.facebook.com/KapowChicago" target="_blank" class="facebook">Kapow on Facebook</a> <a href="http://www.twitter.com/kapowchicago" target="_blank" class="twitter">KapowChicago on Twitter</a></div></h1>