<?php
// Heading
$_['heading_title']	= '[ffct.cc] PayPal Adaptive Payments for MultiMerch';

$_['text_payment'] = 'Payment';

$_['ppa_api_username'] = 'API Username';
$_['ppa_api_username_note'] = 'API Username';
$_['ppa_api_password'] = 'API Password';
$_['ppa_api_password_note'] = 'API Password';
$_['ppa_api_signature']	= 'API Signature';
$_['ppa_api_signature_note'] = 'API Signature';
$_['ppa_api_appid'] = 'Application ID';
$_['ppa_api_appid_note'] = 'Application ID';
$_['ppa_secret'] = 'Shared secret';
$_['ppa_secret_key'] = 'Key';
$_['ppa_secret_value'] = 'Value';
$_['ppa_secret_note'] = 'Strings that will be used for IPN validation. This can be anything';


$_['ppa_payment_type']					 = 'Payment type';
$_['ppa_payment_type_note']					 = 'See <a href="https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_APIntro">introduction to PayPal Adaptive Payments</a> for details';
$_['ppa_payment_type_simple']					 = 'Simple';
$_['ppa_payment_type_parallel']					 = 'Parallel';
$_['ppa_payment_type_chained']					 = 'Chained';

$_['ppa_feespayer']					 = 'Fees payer';
$_['ppa_feespayer_note']					 = 'See <a href="https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_APIntro">introduction to PayPal Adaptive Payments</a> for details';
$_['ppa_feespayer_sender']					 = 'Sender';
$_['ppa_feespayer_primaryreceiver']					 = 'Primary receiver';
$_['ppa_feespayer_eachreceiver']					 = 'Each receiver';
$_['ppa_feespayer_secondaryonly']					 = 'Secondary only';

$_['ppa_receiver']					 = 'Receiver';
$_['ppa_receivers']					 = 'Receivers';
$_['ppa_receivers_note']					 = 'See the readme file for details on payment amount distribution between receivers';
$_['ppa_receiver_note']					 = '';
$_['ppa_receiver_email']					 = 'Email';
$_['ppa_receiver_amount']					 = 'Percentage';
$_['ppa_receiver_percentage']					 = 'Percentage';

$_['ppa_invalid_email']					 = 'Invalid PayPal account action';
$_['ppa_invalid_email_note']					 = 'What to do if one of the sellers has an invalid PayPal address specified';
$_['ppa_too_many_receivers']					 = 'Too many sellers in cart';
$_['ppa_too_many_receivers_note']					 = 'What to do if the cart contains products from too many sellers';
$_['ppa_disable_module']					 = 'Disable Adaptive Payments';
$_['ppa_balance_transaction']					 = 'Create a balance record instead';


$_['ppa_sandbox']					 = 'Sandbox Mode';
$_['ppa_sandbox_note']					 = 'Testing the extension in Sandbox Mode requires Sandbox API credentials';
$_['ppa_debug']					 = 'Debug Mode';
$_['ppa_debug_note']					 = 'Logs detailed information to the PayPal log';

$_['ppa_total']					 = 'Total';
$_['ppa_total_note']				 = 'The checkout total the order must reach before this payment method becomes active.';

$_['ppa_status'] = 'Status:';
$_['ppa_completed_status'] = 'Completed Status:';
$_['ppa_error_status'] = 'Error/Failed Status';
$_['ppa_pending_status'] = 'Pending Status';

$_['ppa_geo_zone'] = 'Geo Zone:';
$_['ppa_sort_order'] = 'Sort Order:';


$_['ppa_error_secondaryonly'] = 'The fee payer types SECONDARYONLY and PRIMARYRECEIVER can only be used for CHAINED payments';

$_['ppa_success'] = 'Success: You have modified PayPal account details!';
$_['ppa_error_permission'] = 'Warning: You do not have permission to modify payment PayPal';
$_['ppa_error_receiver'] = 'You need to specify the primary receiver (#1)';
$_['ppa_error_credentials'] = 'You need to specify all API credentials';
$_['ppa_error_secret'] = 'Both secret key and value required';

$_['text_pp_adaptive'] = '<a onclick="window.open(\'https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_APIntro\');"><img src="view/image/payment/paypal.png" alt="PayPal Adaptive" title="PayPal Adaptive" style="border: 1px solid #EEEEEE;" /></a>';

?>