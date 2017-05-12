/* Parsley Date Validator
===================================================================================================================== */

import Moment from 'moment';
import Parsley from 'parsleyjs';

// Define Date Validator:

Parsley.addValidator('date', {
  validateString: function(value, requirement) {
    return Moment(value, requirement, true).isValid();
  }
});
