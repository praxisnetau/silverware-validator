/* Parsley Domain Validator
===================================================================================================================== */

import Parsley from 'parsleyjs';

// Define Domain Validator:

Parsley.addValidator('domain', {
  validateString: function(value) {
    return /^((localhost)|((?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}))$/.test(value);
  }
});
