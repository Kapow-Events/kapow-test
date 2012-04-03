var cj =
{
  $window:           jQuery(window),
  $document:         jQuery(document),
  $body:             jQuery('body'),
  $header:           jQuery('#header'),
  $homepage_header:  jQuery([]),
  $content:          jQuery([]),
  $navigation:       jQuery([]),
  $rtt_link:         jQuery([]),
  isFrontpage:       false,

  initialize: function() {
    this.findElements();
    this.lightboxModal();
    this.lightbox();
    this.slidebox();
    this.performAutoScroll();
    this.setupLinks();
    this.setupFeaturedProducts();
    this.setupHomepageHeader();
    this.addPlaceholderSupport();
    this.startCarousel();
  },

  performAutoScroll: function ()
  {
    var id = window.location.hash;

    this.scrollTo(id);
  },

  findElements: function ()
  {
    this.homepage_header_ID = '#homepage_header';
    this.navigation_ID      = '#header';
    this.content_ID         = '#main_content';
    this.rtt_link_ID        = '#return_to_top';

    // Find elements
    this.$homepage_header  = jQuery(this.homepage_header_ID);
    this.$navigation       = jQuery(this.navigation_ID);
    this.$content          = jQuery(this.content_ID);
    this.$rtt_link         = jQuery(this.rtt_link_ID);
  },

  lightboxModal: function ()
  {
    jQuery(".lightbox-modal").fancybox({
      'titleShow': false,
      'hideOnOverlayClick': false,
      'hideOnContentClick': false,
      'overlayShow': true,
      'overlayColor': '#000',
      'overlayOpacity': 0.7,
      'showCloseButton': false,
      'enableEscapeButton': false,
      'centerOnScroll': true,
      'padding': 0
    })
    .trigger('click');
  },

  lightbox: function ()
  {
    jQuery(".lightbox").fancybox({
      'overlayShow': true,
      'overlayColor': '#000',
      'overlayOpacity': 0.7,
      'showCloseButton': true,
      'centerOnScroll': false,
      'padding': 10
    });
  },

  slidebox: function ()
  {
    var self          = this,
        delay         = 750,
        speed         = 1500,
        offset        = 100,
        $slidebox     = jQuery('#slidebox'),
        $content      = $slidebox.find('.content'),
        $close_button = $slidebox.find('#slidebox_close'),
        height        = self.$window.height() >= 600 ? 480 : self.$window.height() - (self.$window.height() * 0.15)

    // Setup an alias
    self.$slidebox = $slidebox;

    if ($slidebox.is('*') && !jQuery.cookie('has_been_closed'))
    {
      // Dynamically size the box
      $slidebox.css({
        'left': ((self.$window.width() / 2) - ($slidebox.outerWidth(true) / 2)),
        'top': -(height + offset),
        'height': height,
        'display': 'block'
      });

      // -----------------------------------------------------------------
      // When the slidebox is too small to contain the content dynamically
      // set the content height so that overflow scrollbars work
      // -----------------------------------------------------------------
      if ($content.height() > $slidebox.innerHeight())
      {
        var content_offset = (
          parseInt($content.css('padding-top'))
          + parseInt($content.css('padding-bottom'))
          + $close_button.outerHeight(true)
          + (parseInt($close_button.css('bottom')) * 2)
        );
        $content.css('height', $slidebox.height() - content_offset);
      }

      // Animate the slide after the prescribed delay
      setTimeout(function(){
        $slidebox.animate({ 'top':0 }, speed);
      }, delay);

      // Wire up the close button
      $close_button.click(function(e){
        $slidebox.animate(
          { 'top': -($slidebox.outerHeight(true) + offset) }, speed * 0.6, function(e){
          jQuery.cookie('has_been_closed', 'true', { expires: 2 });
        });
      });
    }
  },

  // This does not run when viewing inner pages
  setupHomepageHeader: function ()
  {
    var self = this;

    if (self.$homepage_header.is('*') && self.$navigation.is('*') && self.$content.is('*') && self.$rtt_link.is('*'))
    {
      var last_scroll_top    = (self.$window.scrollTop() > 0) ? self.$window.scrollTop() : 0,
          navigation_orig_yt = self.$navigation.position().top,
          navigation_orig_yb = navigation_orig_yt + self.$navigation.outerHeight(false),
          header_height      = self.$homepage_header.outerHeight(false),
          multiplier         = Math.round(self.$window.height() / 30);

      // Initial load - helps with iOS issues
      if (self.$window.scrollTop() > header_height)
      {
        self.$homepage_header.css('opacity', 0);
        self.$rtt_link.css('opacity', 1);
        self.$navigation.addClass('fixed');
      }

      self.$window.scroll(function()
      {
        // ----------------------------
        // Fade In/Out the landing area
        // ----------------------------
        // SCROLL DOWN
        if (self.$window.scrollTop() > last_scroll_top)
        {
          var opacity = parseFloat(jQuery(self.homepage_header_ID).css('opacity'), 10) - (multiplier / header_height);
              opacity = opacity <= 0 ? 0 : opacity;  // Hard limit of 0.0 opacity

          var l_opacity = parseFloat(jQuery(self.rtt_link_ID).css('opacity'), 10) + (multiplier / header_height);
              l_opacity = l_opacity >= 1 ? 1 : l_opacity;  // Hard limit of 1.0 opacity
        }
        // SCROLL UP
        else
        {
          var opacity = parseFloat(jQuery(self.homepage_header_ID).css('opacity'), 10) + (multiplier / header_height);
              opacity = opacity >= 1 ? 1 : opacity;  // Hard limit of 1.0 opacity

          var l_opacity = parseFloat(jQuery(self.rtt_link_ID).css('opacity'), 10) - (multiplier / header_height);
              l_opacity = l_opacity <= 0 ? 0 : l_opacity;  // Hard limit of 0.0 opacity
        }
        self.$homepage_header.css('opacity', opacity);

        // --------------------------------------------
        // Make the navigation bar stick to the top
        // --------------------------------------------
        if (self.$window.scrollTop() == 1){
          self.$slidebox.fadeOut('fast');
        }
        // Landing area has completely scrolled away
        if (self.$window.scrollTop() >= navigation_orig_yt)
        {
          self.$navigation.addClass('fixed');
          self.$homepage_header.css('opacity', 0);

          // ---------------------------------------------------
          // "bump" down the content for a smooth transition
          // from position:relative to position:fixed navigation
          // ---------------------------------------------------
          if (self.$window.scrollTop() <= navigation_orig_yb) {
            self.$homepage_header.parent().css('margin-bottom', self.$navigation.outerHeight(true));
            self.$rtt_link.css('opacity', 1);
          }
        }
        // Landing area is in view
        else if (self.$window.scrollTop() < navigation_orig_yt)
        {
          self.$homepage_header.parent().css('margin-bottom', 0);
          self.$navigation.removeClass('fixed');
          self.$rtt_link.css('opacity', 0);
        }

        // At top dead center
        if (self.$window.scrollTop() == 0)
        {
          self.$homepage_header.css('opacity', 1);
          self.$rtt_link.css('opacity', 0);

        if (self.$slidebox.is('*') && !jQuery.cookie('has_been_closed')) {
            self.$slidebox.fadeIn('fast');
          }
        }

        last_scroll_top = self.$window.scrollTop();
      });

    }
  },

  setupFeaturedProducts: function ()
  {
    var self  = this,
        speed = 1000,
        fp    = {
          speed: speed,
          $links: self.$homepage_header.find('#steps a'),
          $text: self.$homepage_header.find('.featured_product_text'),
          backstretch_options: { 'target':self.homepage_header_ID, 'speed':speed, 'positionType':'absolute', 'zIndex':0 }
        };

    if (self.$homepage_header.is('*') && fp.$text.is('*') && fp.$links.is('*'))
    {
      // Show first image/text on initial load
      jQuery.backstretch( fp.$links.first().attr('rel'), fp.backstretch_options );
      fp.$text.first().addClass('active');

      // Start the timer
      self.timer_is_on = true;

      var interval = 7000,
          count    = 2;

      self.timer = setInterval(function(){
        if (self.timer_is_on)
        {
          // reset the count if at the last step
          count = count > fp.$links.length ? 1 : count;
          self.changeFeaturedProduct(jQuery(fp.$links[count-1]), fp);
          count++;
        }
      }, interval);

      // Setup navigation click event
      self.$homepage_header.delegate('#steps a', 'click', function(e){
        e.preventDefault();
        var $target = jQuery(e.currentTarget);

        // If the timer is running, turn it off
        if (self.timer_is_on)
        {
          clearInterval(self.timer);
          self.timer_is_on = false;
        }

        self.changeFeaturedProduct($target, fp);
      });
    }
  },

  changeFeaturedProduct: function($target, fp)
  {
    if (!$target.hasClass('active'))
    {
      var percentage = 0.7;

      fp.$links.removeClass('active');
      fp.$text.fadeOut(fp.speed * percentage).removeClass('active');

      $target.addClass('active');

      jQuery.backstretch( $target.attr('rel'), fp.backstretch_options );
      jQuery( $target.attr('href')+'_text' ).fadeIn(fp.speed * percentage).addClass('active');
    }
  },

  setupLinks: function ()
  {
    var self  = this;

    self.$body.delegate('a', 'click', function(e){
      var id = jQuery(e.currentTarget).attr('data-rel');

      // Normal behavior for standard links
      if (id == '' || !jQuery(id).is('*') || (id == '#header' && !self.isFrontpage)) { return true; }

      // Smooth scroll for anchor links
      self.scrollTo(id); return false;
    });
  },

  scrollTo: function (id)
  {
    if (id != '' && jQuery(id).is('*'))
    {
      var $target           = jQuery(id),
          scroll_y          = $target.offset().top,
          navigation_height = this.$navigation.outerHeight(true),
          speed             = 2500;

      jQuery('body').stop().animate({'scrollTop' : scroll_y - navigation_height}, speed, function(){ });
    }

    return this;
  },

  addPlaceholderSupport: function ()
  {
    if (!Modernizr.inputtypes.email)
    {
      jQuery('input[placeholder], textarea[placeholder]').each(function(i, input){
        var $input = jQuery(input);

        // Initially load the placeholder value
        if ($input.val() == '') { $input.val($input.attr('placeholder')); }

        $input
          .bind('focusin', function(){
            var $this = jQuery(this);
            if ($this.val() == $input.attr('placeholder')) { $this.val(''); }
          })
          .bind('focusout', function(){
            var $this = jQuery(this);
            if ($this.val() == '') { $this.val($input.attr('placeholder')); }
          });
      });
    }
  },

  startCarousel: function ()
  {
    jQuery('#carousel').carousel({
      btnsPosition: 'inside',
      //autoSlide: true,
      //autoSlideInterval: 4000,
      animSpeed: 1000
    });
  }
};



jQuery.noConflict();

jQuery(document).ready(function(){
  cj.initialize();
});

jQuery(window).resize(function(){
  cj.setupHomepageHeader();
});
