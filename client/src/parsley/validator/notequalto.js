/* Parsley NotEqualTo Validator
===================================================================================================================== */

import $ from 'jquery';
import Parsley from 'parsleyjs';

// Define NotEqualTo Validator:

Parsley.addValidator('notequalto', {
  validateString: function(value, requirement) {
    return value !== ($(requirement).length ? $(requirement).val() : requirement);
  },
  priority: 256
});
