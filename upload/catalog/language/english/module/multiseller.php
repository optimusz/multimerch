<?php

// Global

$_['ms_product_status_enabled']      = 'Enabled';
$_['ms_product_status_disabled']      = 'Disabled';

$_['ms_product_review_status_draft']       = 'Draft';
$_['ms_product_review_status_pending']       = 'Pending';
$_['ms_product_review_status_approved']       = 'Approved';
$_['ms_product_review_status_declined']       = 'Declined';

$_['ms_button_submit']      = 'Submit';
$_['ms_button_submit_request']      = 'Submit request';
$_['ms_button_save']      = 'Save';
$_['ms_button_save_draft']      = 'Save draft';
$_['ms_button_save_draft_unpublish']      = 'This product will be unpublished';
$_['ms_button_cancel']      = 'Cancel';
$_['ms_delete']      = 'Delete';

$_['ms_transaction_sale']      = 'Sale: %s (-%s commission)';
$_['ms_transaction_pending_withdrawal']      = 'Pending withdrawal request: %s';
$_['ms_request_submitted']      = 'Your request is submitted';

$_['ms_totals_line']      = 'Currently %s sellers and %s products for sale!';

#38B0E3

// Mails

   // Seller
$_['ms_mail_greeting']      = "Hello %s,\n\n";
$_['ms_mail_ending']      = "\n\nRegards,\n%s";
$_['ms_mail_message']      = "\n\nMessage:\n%s";

$_['ms_mail_subject_seller_account_created']      = 'Seller account created';
$_['ms_mail_seller_account_created'] = <<<EOT
Your seller account at %s has been created!

You can now start adding your products.
EOT;

$_['ms_mail_subject_seller_account_awaiting_moderation']      = 'Seller account awaiting moderation';
$_['ms_mail_seller_account_awaiting_moderation'] = <<<EOT
Your seller account at %s has been created and is now awaiting moderation.

You will receive an email as soon as it is approved.
EOT;


$_['ms_mail_subject_product_awaiting_moderation']      = 'Product awaiting moderation';
$_['ms_mail_product_awaiting_moderation'] = <<<EOT
Your product %s at %s is awaiting moderation.

You will receive an email as soon as it is processed.
EOT;

$_['ms_mail_subject_product_purchased']      = 'Product purchased';
$_['ms_mail_product_purchased'] = <<<EOT
Your product %s has been purchased from %s.
EOT;

$_['ms_mail_subject_withdraw_request_submitted']      = 'Withdrawal request submitted';
$_['ms_mail_withdraw_request_submitted'] = <<<EOT
We have received your withdrawal request. You will be notified when it is processed.
EOT;

$_['ms_mail_subject_withdraw_request_completed']      = 'Withdrawal completed';
$_['ms_mail_withdraw_request_completed'] = <<<EOT
Your withdrawal request has been processed. You should now receive your earnings.
EOT;

$_['ms_mail_subject_withdraw_request_declined']      = 'Withdrawal request declined';
$_['ms_mail_withdraw_request_declined'] = <<<EOT
Your withdrawal request has been declined. Your funds have been returned to your balance at %s.
EOT;

$_['ms_mail_subject_transaction_performed']      = 'New transaction';
$_['ms_mail_transaction_performed'] = <<<EOT
New transaction has been added to your account at %s.
EOT;

	//
	// Admin
	//
$_['ms_mail_admin_subject_seller_account_created']      = 'New seller account created';
$_['ms_mail_admin_seller_account_created'] = <<<EOT
New seller account at %s has been created!
EOT;

$_['ms_mail_admin_subject_seller_account_awaiting_moderation']      = 'New seller account awaiting moderation';
$_['ms_mail_admin_seller_account_awaiting_moderation'] = <<<EOT
New seller account at %s has been created and is now awaiting moderation.

You can process it in the Multiseller - Sellers section in back office.
EOT;

$_['ms_mail_admin_subject_product_created']      = 'New product added';
$_['ms_mail_admin_product_created'] = <<<EOT
New product %s has been added to %s.

You can view or edit it in back office.
EOT;

$_['ms_mail_admin_subject_new_product_awaiting_moderation']      = 'New product awaiting moderation';
$_['ms_mail_admin_new_product_awaiting_moderation'] = <<<EOT
New product %s has been added to %s and is awaiting moderation.

You can process it in the Multiseller - Products section in back office.
EOT;

$_['ms_mail_admin_subject_edit_product_awaiting_moderation']      = 'Product edited and awaiting moderation';
$_['ms_mail_admin_edit_product_awaiting_moderation'] = <<<EOT
Product %s at %s has been edited and is awaiting moderation.

You can process it in the Multiseller - Products section in back office.
EOT;

$_['ms_mail_admin_subject_withdraw_request_submitted']      = 'Withdrawal request awaiting moderation';
$_['ms_mail_admin_withdraw_request_submitted'] = <<<EOT
New withdrawal request has been submitted.

You can process it in the Multiseller - Finances section in back office.
EOT;


// Errors
$_['ms_error_sellerinfo_nickname_empty'] = 'Username cannot be empty';
$_['ms_error_sellerinfo_nickname_alphanumeric'] = 'Username can only contain alphanumeric characters';
$_['ms_error_sellerinfo_nickname_length'] = 'Username should be between 4 and 50 characters';
$_['ms_error_sellerinfo_nickname_taken'] = 'This username is already taken';
$_['ms_error_sellerinfo_company_length'] = 'Company name cannot be longer than 50 characters';
$_['ms_error_sellerinfo_description_length'] = 'Description cannot be longer than 1000 characters';
$_['ms_error_sellerinfo_paypal'] = 'Invalid PayPal address';

$_['ms_error_file_extension'] = 'Invalid extension';
$_['ms_error_file_type'] = 'Invalid file type';
$_['ms_error_file_size'] = 'File too big';
$_['ms_error_file_upload_error'] = 'File upload error';

$_['ms_error_product_name_empty'] = 'Product name cannot be empty';
$_['ms_error_product_name_length'] = 'Product name should be between 4 and 50 characters';
$_['ms_error_product_description_empty'] = 'Product description cannot be empty';
$_['ms_error_product_description_length'] = 'Product description should be between 25 and 1000 characters';
$_['ms_error_product_tags_length'] = 'Line too long';
$_['ms_error_product_price_empty'] = 'Please specify a price for your product';
$_['ms_error_product_price_invalid'] = 'Invalid price';
$_['ms_error_product_price_low'] = 'Price too low';
$_['ms_error_product_category_empty'] = 'Please select a category';
$_['ms_error_product_thumbnail_empty'] = 'Please upload a thumbnail for your product';
$_['ms_error_product_download_empty'] = 'Please add a download for your product';
$_['ms_error_product_image_count'] = 'Please upload at least %s image(s) for your product';
$_['ms_error_product_message_length'] = 'Message cannot be longer than 1000 characters';

$_['ms_error_withdraw_amount'] = 'Invalid amount';
$_['ms_error_withdraw_balance'] = 'Not enough funds on your balance';
$_['ms_error_withdraw_minimum'] = 'Cannot withdraw less than minimum limit';


// Account - General

$_['ms_account_create']      = 'Create seller account';
$_['ms_account_seller_account']      = 'Seller Account';
$_['ms_account_sellerinfo']       = 'Seller information';
$_['ms_account_newproduct']      = 'New product';
$_['ms_account_products']       = 'Products';
$_['ms_account_sellerstatus']      = 'Account status';
$_['ms_account_transactions']       = 'Transactions';
$_['ms_account_withdraw']       = 'Request earnings';


// Account - New product

$_['ms_account_newproduct_heading']      = 'New Product';
$_['ms_account_newproduct_breadcrumbs']       = 'New Product';

$_['ms_account_product_name_description']    = 'Name & Description';
$_['ms_account_product_name']    = 'Name';
$_['ms_account_product_name_note']    = 'Your note text goes here';
$_['ms_account_product_description']     = 'Description';
$_['ms_account_product_description_note']    = 'Your note text goes here';
$_['ms_account_product_short_description']     = 'Short description';
$_['ms_account_product_tags']    = 'Tags';
$_['ms_account_product_tags_note']    = 'Your note text goes here';


$_['ms_account_product_price_attributes']    = 'Price & Attributes';
$_['ms_account_product_price']     = 'Price';
$_['ms_account_product_price_note']    = 'Your note text goes here';
$_['ms_account_product_category']     = 'Category';
$_['ms_account_product_category_note']    = 'Your note text goes here';

$_['ms_account_product_files']    = 'Files';
$_['ms_account_product_thumbnail']     = 'Thumbnail';
$_['ms_account_product_thumbnail_note']    = 'Your note text goes here';
$_['ms_account_product_download']     = 'Downloads';
$_['ms_account_product_download_note']     = 'Your note text goes here';
$_['ms_account_product_image']     = 'Images';
$_['ms_account_product_image_note']     = 'Your note text goes here';

$_['ms_account_product_message']     = 'Message';
$_['ms_account_product_message_note']    = 'Your note text goes here';

// Account - Edit product

$_['ms_account_editproduct_heading']      = 'Edit Product';
$_['ms_account_editproduct_breadcrumbs']       = 'Edit Product';



// Account - Seller info

$_['ms_account_sellerinfo_heading']      = 'Seller Information';
$_['ms_account_sellerinfo_breadcrumbs']       = 'Seller Information';

$_['ms_account_sellerinfo_activation_notice'] = 'Please complete the following form to activate your seller account';

$_['ms_account_sellerinfo_nickname']    = 'Nickname';
$_['ms_account_sellerinfo_nickname_note']    = 'Your note text goes here.';
$_['ms_account_sellerinfo_description']    = 'Description';
$_['ms_account_sellerinfo_description_note']    = 'Your note text goes here.';
$_['ms_account_sellerinfo_company']     = 'Company';
$_['ms_account_sellerinfo_company_note']    = 'Your note text goes here';
$_['ms_account_sellerinfo_country']    = 'Country';
$_['ms_account_sellerinfo_country_dont_display']    = 'Do not display my country';
$_['ms_account_sellerinfo_country_note']    = 'Your note text goes here';
$_['ms_account_sellerinfo_website']    = 'Website';
$_['ms_account_sellerinfo_website_note']    = 'Your note text goes here';
$_['ms_account_sellerinfo_avatar']    = 'Avatar';
$_['ms_account_sellerinfo_avatar_note']    = 'Your note text goes here';
$_['ms_account_sellerinfo_paypal']    = 'Paypal';
$_['ms_account_sellerinfo_paypal_note']    = 'Your note text goes here';
$_['ms_account_sellerinfo_reviewer_message']    = 'Message to the reviewer';
$_['ms_account_sellerinfo_reviewer_message_note']    = 'Your note text goes here';


$_['ms_account_sellerinfo_mail_account_thankyou']	= 'Thank you for signing up as a seller at %s!';
$_['ms_account_sellerinfo_mail_account_created_subject']    = '[%s] Seller account created';
$_['ms_account_sellerinfo_mail_account_created_message']    = "You now have full access to your seller account and can start publishing your products!";

$_['ms_account_sellerinfo_mail_account_pleaseactivate_subject']    = '[%s] Please activate your seller account';
$_['ms_account_sellerinfo_mail_account_pleaseactivate_message']    = "Please click the link below to activate your account: ";

$_['ms_account_sellerinfo_mail_account_needsapproval_subject']    = '[%s] Seller account approval';
$_['ms_account_sellerinfo_mail_account_needsapproval_message']    = "As soon as your account is approved, you will receive a confirmation email.";

$_['ms_account_sellerinfo_saved'] = 'Seller account data saved.';



// Account - Seller account status
$_['ms_account_status'] = 'Your seller account status is: ';

$_['ms_account_status_activation'] = <<<EOT
Waiting for activation

Please visit the link contained in the email we sent you to complete the activation process.
EOT;

$_['ms_account_status_approval'] = <<<EOT
<b>Waiting for approval</b>
<br />
As soon as your account is approved, you will receive a confirmation email.
EOT;

$_['ms_account_status_active'] = <<<EOT
<b>Active</b>
<br />
You have full access to your seller account.
EOT;

$_['ms_account_status_disabled'] = <<<EOT
<b>Disabled</b>
<br />
Your seller account has been disabled by the administrator.
EOT;

$_['ms_account_status_please_fill_in'] = 'Please complete the following form to create a seller account.';


// Account - Products

$_['ms_account_products_heading']      = 'Your Products';
$_['ms_account_products_breadcrumbs']       = 'Your Products';

$_['ms_account_products_product']      = 'Product';
$_['ms_account_products_publish']       = 'Published';
$_['ms_account_products_sales']      = 'Sales';
$_['ms_account_products_status']       = 'Review status';
$_['ms_account_products_date']       = 'Date added';
$_['ms_account_products_action']       = 'Action';
$_['ms_account_products_action_edit']       = 'Edit';
$_['ms_account_products_action_delete']       = 'Delete';
$_['ms_account_products_noproducts']       = 'You don\'t have any products yet!';


// Account - Transactions

$_['ms_account_transactions_heading']      = 'Your Transactions';
$_['ms_account_transactions_breadcrumbs']       = 'Your Transactions';

$_['ms_account_transactions_balance']      = 'Your current balance:';
$_['ms_account_transactions_date_created']      = 'Date created';
$_['ms_account_transactions_date_modified']      = 'Date modified';
$_['ms_account_transactions_description']       = 'Description';
$_['ms_account_transactions_amount']      = 'Amount';
$_['ms_account_transactions_status']      = 'Status';
$_['ms_account_transactions_notransactions']       = 'You don\'t have any transactions yet!';

// Account - Request withdrawal

$_['ms_account_withdraw_heading']      = 'Request Earnings';
$_['ms_account_withdraw_breadcrumbs']       = 'Request Earnings';

$_['ms_account_withdraw_balance']      = 'Your current balance:';
$_['ms_account_withdraw_minimum']      = 'Minimum withdrawal amount:';

$_['ms_account_withdraw_amount']      = 'Amount:';
$_['ms_account_withdraw_amount_note']      = 'Amount note';

$_['ms_account_withdraw_method']      = 'Payment method:';
$_['ms_account_withdraw_method_note']      = 'Method note';
$_['ms_account_withdraw_method_paypal']      = 'PayPal';

$_['ms_account_withdraw_all']      = 'All earnings currently available';
$_['ms_account_withdraw_minimum_not_reached']      = 'Your total balance is less than the minimum withdrawal amount!';
$_['ms_account_withdraw_no_funds']      = 'No funds to withdraw.';
$_['ms_account_withdraw_disabled']      = 'We don\'t accept withdrawal requests. You will be paid automatically.';
$_['ms_account_withdraw_no_paypal']      = 'Please <a href="index.php?route=account/ms-seller/sellerinfo">specify your PayPal address</a> first!';


// Product page - Seller information
$_['ms_catalog_product_sellerinfo']      = 'Seller information';
$_['ms_catalog_product_date_added']      = 'Date added:';
$_['ms_catalog_product_sales']      = 'Sales:';


// Product page - Comments
$_['ms_comments_post_comment']  = 'Post Comment';
$_['ms_comments_name']          = 'Name';
$_['ms_comments_note']          = '<span style="color: #FF0000;">Note:</span> HTML is not translated!';
$_['ms_comments_email']         = 'E-mail';
$_['ms_comments_comment']      = 'Comment';
$_['ms_comments_button_continue']    = 'Submit';
$_['ms_comments_wait']         = 'Please Wait!';
$_['ms_comments_error_name']        = 'Warning: Name must be between 3 and 25 characters!';
$_['ms_comments_error_text_long']   = 'Warning: Comment text too long!';
$_['ms_comments_error_email_long']  = 'Warning: Email is too long!';
$_['ms_comments_error_text_empty']  = 'Warning: Comment can not be empty!';
$_['ms_comments_error_captcha']     = 'Warning: Verification code does not match the image!';
$_['ms_comments_success']      = 'Thank you for your comment.';
$_['ms_comments_captcha']     = 'Enter the code in the box below:';
$_['ms_comments_no_comments_yet'] = 'No comments added yet';
$_['ms_comments_tab_comments']       = 'Comments (%s)';
$_['entry_captcha']       = 'Enter the code in the box below:';

$_['ms_footer']       = '<br>Digital Multiseller Marketplace by <a href="http://ffct.cc/">ffct.cc</a>';


// Catalog - Sellers list
$_['ms_catalog_sellers_heading']      = 'Sellers';
$_['ms_catalog_sellers_breadcrumbs']      = 'Sellers';

$_['ms_catalog_sellers_country']      = 'Country:';
$_['ms_catalog_sellers_website']      = 'Website:';
$_['ms_catalog_sellers_company']      = 'Company:';
$_['ms_catalog_sellers_totalsales']      = 'Sales:';
$_['ms_catalog_sellers_totalproducts']      = 'Products:';

$_['ms_sort_country_desc']      = 'Country (Z - A)';
$_['ms_sort_country_asc']      = 'Country (A - Z)';
$_['ms_sort_nickname_desc']      = 'Name (Z - A)';
$_['ms_sort_nickname_asc']      = 'Name (A - Z)';


// Catalog - Seller profile page
$_['ms_catalog_sellers']      = 'Sellers';
$_['ms_catalog_sellers_empty']      = 'There are no sellers yet.';
$_['ms_catalog_seller_profile_heading']      = '%s\'s profile';
$_['ms_catalog_seller_profile_breadcrumbs']      = '%s\'s profile';
$_['ms_catalog_seller_profile_products']      = 'Some of seller\'s products';

$_['ms_catalog_seller_profile_country']      = 'Country:';
$_['ms_catalog_seller_profile_website']      = 'Website:';
$_['ms_catalog_seller_profile_company']      = 'Company:';
$_['ms_catalog_seller_profile_totalsales']      = 'Total sales:';
$_['ms_catalog_seller_profile_totalproducts']      = 'Total products:';
$_['ms_catalog_seller_profile_view']      = 'View all %s\'s products';

// Catalog - Seller's products list
$_['ms_catalog_seller_products_heading']      = '%s\'s products';
$_['ms_catalog_seller_products_breadcrumbs']      = '%s\'s products';

$_['ms_catalog_seller_products_empty']      = 'This seller doesn\'t have any products yet!';
$_['ms_catalog_seller_products_website']      = 'Website:';
$_['ms_catalog_seller_products_company']      = 'Company:';
$_['ms_catalog_seller_products_totalsales']      = 'Total sales:';
$_['ms_catalog_seller_products_totalproducts']      = 'Total products:';

// Catalog - Carousel
$_['ms_carousel_sellers']      = 'Our sellers';
$_['ms_carousel_view']      = 'View all sellers';

// Catalog - Top sellers
$_['ms_topsellers_sellers']      = 'Top sellers';
$_['ms_topsellers_view']      = 'View all sellers';

// Catalog - New sellers
$_['ms_newsellers_sellers']      = 'New sellers';
$_['ms_newsellers_view']      = 'View all sellers';

// Catalog - Seller dropdown
$_['ms_sellerdropdown_sellers']      = 'Our sellers';
$_['ms_sellerdropdown_select']      = '-- Select a seller --';
?>