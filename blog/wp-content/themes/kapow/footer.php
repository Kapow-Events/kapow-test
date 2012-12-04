<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after. Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>
			</div><!-- #homepage_body_wrapper -->
		</div><!-- .inner_wrapper -->
	</div><!-- #main_bg -->
	</div><!-- #main -->
	</div><!-- #page -->
</div><!-- #wrapper -->
<footer class="outer_wrapper" id="footer">
    <div class="inner_wrapper" id="contact">
  	<div id="contact_logo"></div>
      <h2>contact us today </h2>
      <div class="content clearfix">
        <div class="left">
          <div class="floatleft" id="email_us_button">
            <div class="list_item_round_beige_large">
              <a href="mailto:info@kapowevents.com" target="_blank" class="icon_email">info@kapowevents.com</a>
            </div>
            <a href="mailto:info@kapowevents.com" target="_blank" class="button_ribbon_small">email us</a>
          </div>

          <div class="floatright clearfix">
            <h2 class="fancy_phone_number">1.855.KAPOWUS</h2>
            <hr>
            <p>OR CONNECT WITH US --- <a target="_blank" href="http://www.linkedin.com/company/2425635" class="icon_linked_in"></a> - <a target="_blank" href="http://twitter.com/kapowchicago" class="icon_twitter"></a> - <a target="_blank" href="http://www.facebook.com/KapowChicago" class="icon_facebook"></a> ---</p>          </div>

          <div id="contact_information">
            <p><strong>211 W. Wacker, 2nd Floor - Chicago, IL 60606</strong><em></em></p>
<p><em>phone</em> <strong><span>1.855.KAPOWUS&nbsp;<span>or 1.855.527.6987&nbsp;</span></span></strong><!-- - <em>fax</em> <strong><span>312.300.7957</span>--></p>
<p><em>email </em> <strong><a href="mailto:info@kapowevents.com" target="_blank">info@kapowevents.com</a></strong></p>          </div>
        </div><!-- footer.content.left -->

        <div class="right">
          <form method="post" id="contactForm" action="http://www.kapowevents.com/contacts/index/post/">
            <input type="hidden" value="/?message_sent#contact" name="return_url">
            <fieldset>
              <legend>got a question? let us know.</legend>
              <table>
                <tbody><tr>
                  <td style="padding-right:10px;"><label class="required" for="name">Name:</label><input type="text" class="input-text required-entry" value="" name="name" id="name"></td>
                  <td style="padding-left:10px;"><label class="required" for="email">Email:</label><input type="text" class="input-text required-entry" value="" name="email" id="email"></td>
                </tr>
                <tr>
                  <td colspan="2"><label class="required" for="message">Message:</label><textarea name="comment" class="required-entry" id="comment"></textarea></td>
                </tr>
                <tr>
                  <td colspan="2">
                      <input type="text" style="display:none !important;" value="" id="hideit" name="hideit">
                      <input type="submit" class="button_ribbon floatright" value="send message">
                  </td>
                </tr>
              </tbody></table>
            </fieldset>
          </form>
        </div><!-- footer.content.right -->
      </div><!-- footer.content -->
    </div><!-- footer.inner_wrapper -->
  </footer><!-- footer.outer_wrapper -->
  
  <div id="footer_sublinks">
	<div id="footerlinks-container">
      <div id="footer_sublinks_left">
        <a href="/terms-of-use">terms of use</a>
        <a href="/privacy-statement">privacy statement</a>
      </div>
      <a href="/" id="footerlogo"></a>
      <div id="footer_sublinks_right">
        <a href="/faqs">faq's</a>
        <a href="/blog">blog</a>
        <a href="/blog/feed" class="last">rss</a>
        <a href="/media/downloadable/Kapow%20Events%20Press%20Kit.pdf" class="presskit_footer">&nbsp;</a>
      </div>
	</div>
  </div>
<div id="copy"> &copy; kapow events, inc,  All rights reserved.</div>
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>
