<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
// Ppal (Paypal IPN Class)
// ------------------------------------------------------------------------

//Location of IPN log file
$config['paypal_lib_ipn_log_file'] = BASEPATH . 'logs/paypal_ipn.log';


//Location of NVP (API) log file
$config['paypal_lib_nvp_log_file'] = BASEPATH . 'logs/paypal_nvp.log';

//Turn on logging. TRUE == ON
$config['paypal_lib_log'] = TRUE;

// What is the default currency?
$config['paypal_lib_currency_code'] = 'USD';

//PayPal account email
$config['paypal_account'] = 'rld2_1271131856_biz_api1.gmail.com';

$config['paypal_api_user'] = 'rld2_1271131856_biz_api1.gmail.com';

$config['paypal_api_pwd'] = '1271131861';

$config['paypal_api_signature'] = 'A.aTeASoEAGJH4lmURmjqmCaNW.jAEqgmfyov6l3iHMOSX5krGbuwXNC';

$config['paypal_api_version'] = '62.0';

?>
