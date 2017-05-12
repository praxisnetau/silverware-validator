/* SilverWare Validator Parsley Bundle
===================================================================================================================== */

// Load Extra Validators:

require('parsleyjs/src/extra/validator/comparison');
require('parsleyjs/src/extra/validator/words');

// Load Custom Validators:

require('../parsley/validator/date');
require('../parsley/validator/domain');
require('../parsley/validator/notequalto');

// Initialise:

require('../parsley/init.js');
