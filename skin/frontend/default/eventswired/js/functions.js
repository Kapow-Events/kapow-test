// remap jQuery to $
(function($){})(window.jQuery);

var cj =
{
  $window:           $(window),
  $document:         $(document),
  $body:             $('body'),
  $header:           $('#header'),
  $homepage_header:  $([]),
  $content:          $([]),
  $navigation:       $([]),
  $rtt_link:         $([]),
  isFrontpage:       false,

  initialize: function() {
    this.performAutoScroll();
    this.setupLinks();
    this.addPlaceholderSupport();
    this.startCarousel();
  },

  performAutoScroll: function ()
  {
    var id = window.location.hash;

    if (id != '') { this.scrollTo(id); }
  },

  // This does not run when viewing inner pages
  setupHomepageHeader: function ()
  {
    var self               = this,
        homepage_header_ID = '#homepage_header',
        navigation_ID      = '#header',
        content_ID         = '#main_content',
        rtt_link_ID        = '#return_to_top';

    // Find elements
    self.$homepage_header  = $(homepage_header_ID),
    self.$navigation       = $(navigation_ID);
    self.$content          = $(content_ID);
    self.$rtt_link         = $(rtt_link_ID);

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
          var opacity = parseFloat($(homepage_header_ID).css('opacity'), 10) - (multiplier / header_height);
              opacity = opacity <= 0 ? 0 : opacity;  // Hard limit of 0.0 opacity

          var l_opacity = parseFloat($(rtt_link_ID).css('opacity'), 10) + (multiplier / header_height);
              l_opacity = l_opacity >= 1 ? 1 : l_opacity;  // Hard limit of 1.0 opacity
        }
        // SCROLL UP
        else
        {
          var opacity = parseFloat($(homepage_header_ID).css('opacity'), 10) + (multiplier / header_height);
              opacity = opacity >= 1 ? 1 : opacity;  // Hard limit of 1.0 opacity

          var l_opacity = parseFloat($(rtt_link_ID).css('opacity'), 10) - (multiplier / header_height);
              l_opacity = l_opacity <= 0 ? 0 : l_opacity;  // Hard limit of 0.0 opacity
        }
        self.$homepage_header.css('opacity', opacity);

        // --------------------------------------------
        // Make the navigation bar stick to the top
        // --------------------------------------------
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
            self.$content.css('margin-top', (self.$window.scrollTop() - navigation_orig_yb) * -1);
            self.$rtt_link.css('opacity', 1);
          }
        }
        // Landing area is in view
        else if (self.$window.scrollTop() < navigation_orig_yt)
        {
          self.$content.css('margin-top', 0);
          self.$navigation.removeClass('fixed');
          self.$rtt_link.css('opacity', 0);
        }

        // At top dead center
        if (self.$window.scrollTop() == 0)
        {
          self.$homepage_header.css('opacity', 1);
          self.$rtt_link.css('opacity', 0);
        }

        last_scroll_top = self.$window.scrollTop();
      });

    }
  },

  setupLinks: function ()
  {
    var self  = this;

    self.$body.delegate('a', 'click', function(e){
      var id = $(e.currentTarget).attr('data-rel');

      // Normal behavior for standard links
      if(!$(id).is('*') || (id == '#header' && !self.isFrontpage)){ return true; }

      self.scrollTo(id);

      return false;
    });
  },

  scrollTo: function (id)
  {
    var scroll_y = $(id).offset().top,
        offset   = this.$navigation.outerHeight(true),
        speed    = 2500;

    // --------------------------------------------------------------------
    // Not sure what the 146px double offset is caused by but this corrects
    // issues with the navigation transitioning between relative and fixed
    // --------------------------------------------------------------------
    offset = this.$navigation.hasClass('fixed') ? offset : offset + 146;

    this.$header.removeAttr('class').addClass(id.replace('#',''));
    $('html,body').stop().animate({'scrollTop' : scroll_y - offset}, speed);

    return this;
  },

  addPlaceholderSupport: function ()
  {
    if (!Modernizr.inputtypes.email)
    {
      $('input[placeholder], textarea[placeholder]').each(function(i, input){
        var $input = $(input);

        // Initially load the placeholder value
        if ($input.val() == '') { $input.val($input.attr('placeholder')); }

        $input
          .bind('focusin', function(){
            var $this = $(this);
            if ($this.val() == $input.attr('placeholder')) { $this.val(''); }
          })
          .bind('focusout', function(){
            var $this = $(this);
            if ($this.val() == '') { $this.val($input.attr('placeholder')); }
          });
      });
    }
  },

  startCarousel: function ()
  {
    $('#carousel').carousel({
      btnsPosition: 'inside',
      //autoSlide: true,
      //autoSlideInterval: 4000,
      animSpeed: 1000
    });
  }
};




$(document).ready(function(){
  cj.initialize();
  cj.setupHomepageHeader();
});

$(window).resize(function(){
  cj.setupHomepageHeader();
});
