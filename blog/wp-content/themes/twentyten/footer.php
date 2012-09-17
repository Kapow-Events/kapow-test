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
	</div><!-- #main -->
	</div><!-- #page -->
</div><!-- #wrapper -->
<footer id="footer" class="outer_wrapper">
  	<div id="contact_logo"></div>
    <div id="contact" class="inner_wrapper">
      <h2>contact us today </h2>
      <div class="content clearfix">
        <div class="left">
          <div id="email_us_button" class="floatleft">
            <div class="list_item_round_beige_large">
              <a class="icon_email" href="mailto:info@kapowevents.com">info@kapowevents.com</a>
            </div>
            <a class="button_ribbon_small" href="mailto:info@kapowevents.com">email us</a>
          </div>

          <div class="floatright clearfix">
            <h2 class="fancy_phone_number">312.283.8610</h2>
            <hr>
            <p>OR CONNECT WITH US --- <a class="icon_linked_in" href="http://www.linkedin.com/company/2425635" target="_blank"></a> - <a class="icon_twitter" href="http://twitter.com/kapowchicago" target="_blank"></a> - <a class="icon_facebook" href="http://www.facebook.com/KapowChicago" target="_blank"></a> ---</p>          </div>

          <div id="contact_information">
            <p><strong>WILLIS TOWER </strong>- <em>233 S Wacker, 84th Floor - Chicago, IL 60606</em></p>
<p><em>phone</em> <strong>312 283.8610</strong> - <em>fax</em> <strong>312 283.8611</strong></p>
<p><em>email</em> <strong><a href="mailto:info@kapowevents.com">info@kapowevents.com</a></strong></p>          </div>
        </div><!-- footer.content.left -->

        <div class="right">
          <form action="http://ec2-23-20-73-152.compute-1.amazonaws.com/contacts/index/post/" id="contactForm" method="post">
            <input name="return_url" value="http:/ec2-23-20-73-152.compute-1.amazonaws.com/?message_sent#contact" type="hidden">
            <fieldset>
              <legend>got a question? let us know.</legend>
              <table>
                <tbody><tr>
                  <td style="padding-right:10px;"><label for="name" class="required">Name:</label><input id="name" name="name" value="" class="input-text required-entry" type="text"></td>
                  <td style="padding-left:10px;"><label for="email" class="required">Email:</label><input id="email" name="email" value="" class="input-text required-entry" type="text"></td>
                </tr>
                <tr>
                  <td colspan="2"><label for="message" class="required">Message:</label><textarea id="comment" class="required-entry" name="comment"></textarea></td>
                </tr>
                <tr>
                  <td colspan="2">
                      <input name="hideit" id="hideit" value="" style="display:none !important;" type="text">
                      <input value="send message" class="button_ribbon floatright" type="submit">
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
      <div id="footer_sublinks_left">
        <a href="/terms-of-use">terms of use</a>
        <a href="/privacy-statement">privacy statement</a>
      </div>
      <div id="footerlogo"></div>
      <div id="footer_sublinks_right">
        <a href="/faqs">faq's</a>
        <a href="/site-map" class="last">site map</a>
      </div>
  </div>
	<div id="copy">
		© kapow events, llc,  All rights reserved.
	</div>

<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>
