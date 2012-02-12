$(document).ready(function(){

  // ------------------------------
  // Form Input Placeholder
  // ------------------------------
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

});
