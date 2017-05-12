/* SilverWare Validator Parsley Init
===================================================================================================================== */

import $ from 'jquery';
import Parsley from 'parsleyjs';

$(function() {
  
  // Match Parsley Backend Forms:
  
  $('form.parsleybackend').each(function() {
    
    // Obtain Form Object:
    
    var $form = $(this);
    
    // Setup Optionset Fields:
    
    $form.find('.optionset').each(function() {
      
      // Obtain Optionset Element:
      
      var $optionset = $(this);
      
      // Obtain Optionset Input Elements:
      
      var $input = $optionset.find('input');
      
      // Find Parsley Data Attribute Names:
      
      var attrs = $.map(this.attributes , function(attr) {
        if (attr.name.indexOf('data-parsley') !== -1) {
          return attr.name;
        }
      });
      
      // Move Parsley Data Attributes to Input Elements:
      
      $.each(attrs, function() {
        $input.attr(this, $optionset.attr(this));
        $optionset.removeAttr(this);
      });
      
    });
    
    // Is Client-Side Validation Enabled?
    
    if ($form.data('client-side')) {
      
      // Initialise Parsley:
      
      new Parsley.Factory($form[0], {
        classHandler: function(el) {
          return el.$element;
        },
        errorsContainer: function(el) {
          return el.$element.closest('.field');
        },
        errorClass: $form.data('field-error-class'),
        successClass: $form.data('field-success-class'),
        errorsWrapper: '<div class="' + $form.data('error-wrapper-class') + '"></div>',
        errorTemplate: '<span></span>'
      });
      
      // Setup Bootstrap Classes:
      
      Parsley.on('field:validated', function(e) {
        
        // Obtain Group Element:
        
        var $group = e.$element.closest('.' + $form.data('group-class').replace(/^\./, ''));
        
        // Obtain Validation Classes:
        
        var errorClass   = $form.data('group-error-class');
        var successClass = $form.data('group-success-class');
        
        // Handle Non-Required and Empty Fields:
        
        if (!e.element.required && !e.$element.val()) {
          return $group.removeClass(errorClass).removeClass(successClass);
        }
        
        // Handle All Other Fields:
        
        if (e.validationResult.constructor !== Array) {
          return $group.removeClass(errorClass).addClass(successClass);
        } else {
          return $group.removeClass(successClass).addClass(errorClass);
        }
        
      });
      
    }
    
  });
  
});
