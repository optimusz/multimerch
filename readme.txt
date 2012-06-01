Digital Multiseller Marketplace extension for OpenCart
http://ffct.cc

Usage in multiple projects:
----------------------------------
	If you intend to use the extension on multiple OpenCart installations, please
	do purchase	additional copies of the extension for your other installations.
	This additional funding will allow us to spend more time upgrading the extension
	and making it better and it will let you receive your free updates more promptly.

Installation:
----------------------------------
	1. If you don't have vQmod installed, install it first. vQmod is 
	   _required_ for this module to work. You can get it at http://vqmod.com/
	   It needs to be installed and functional. 
 
	2. Copy the files and folders from the upload/ directory to your OpenCart installation.

	3. Create a folder named tmp/ within your image/ directory and grant write permissions to your web server.
	   Prevent file and directory indexing for this new folder either by configuring your web server accordingly
	   or by creating an empty index.html file within this directory.

	4. Install the module via Backend -> Extensions -> Modules -> [ffct.cc] Digital Multiseller Marketplace
	
	   NOTE: This step is mandatory since it creates the tables needed for the extension to work.
	   If you don't do this you'll get a MySQL error in frontend. 


If you are using a custom theme:
----------------------------------
	1. Modify the line in file vqmod/xml/multiseller.xml that says
		<!ENTITY themeFolder "default">
	   and replace "default" with your theme's folder name, e.g.
		<!ENTITY themeFolder "yourtheme">

	2. Copy the templates folder
		catalog/view/theme/default/template/module/multiseller/
	   to the corresponding folder in your theme, e.g.
		catalog/view/theme/yourtheme/template/module/multiseller/
	   for modifications.

	3. This extension may not work with heavily modified custom themes out of the box 
	   since it relies on specific lines of code found in the default theme. Contact us if that's the case.


If you are using a renamed admin folder:
----------------------------------
	Modify the line in file vqmod/xml/multiseller.xml that says
		<!ENTITY adminFolder "admin">
	and replace "admin" with your admin area folder name, e.g.
		<!ENTITY adminFolder "admin123">


If you are using multiple languages in your store
---------------------------------
	Copy the following files to the corresponding language folder and translate them accordingly:
		catalog/language/english/module/multiseller.php
		admin/language/english/module/multiseller.php
	For example, depending on your language pack, your new translations for German language might be located in
		catalog/language/de_DE/module/multiseller.php
		admin/language/de_DE/module/multiseller.php

Usage:
----------------------------------
	This extension turns your OpenCart installation into a multiseller store for digital products.


	Seller accounts
	---------------
	When a customer has signed up at your store, he may additionally create a seller account to upload and sell
	digital downloadable goods. Seller commission is deducted from every sale and is a configurable percentage.
	It is also possible to set new seller accounts and new products to be approved manually. If that's the case,
	store administrator will have to review new sellers and products and either approve or decline them before
	they are active.


	Products
	---------------
	When a seller submits or updates a product, it either gets active right away or gets a "Pending" status if
	product validation settting is set to "Manual". In that case, store administrator has to approve the product
	before it gets available for purchase.

	Minimum product price, allowed file types and image preview sizes are all configurable values.

	If your store has multiple languages configured, sellers will be able to fill in product name, description and
	tags in different languages.


	Order statuses and transactions
	---------------
	It is possible to configure order statuses that will correspond to positive or negative seller transactions.
	When the order status gets set to one of credit (or positive) order statuses, sellers will receive a positive
	transaction to their earnings balance. If the store status gets set to one of debig (negative) statuses, 
	corresponding amount will be deducted from sellers' balance. This also happens when order status gets changed
	in the back office either directly or via order history.

	Make sure your payment modules are configured to set order statuses to the ones you specify as "credit".

	Note, that "Product Sold" emails will only be sent for orders having one of the "credit order statuses", e.g.
	only if the seller receives a positive transaction to his balance.

	Withdrawals
	---------------
	Sellers can request a payout to their PayPal account. It is possible to fully disable withdrawal requests
	(in that case you'll need to perform payouts manually), configure the minimum required balance to perform
	a request and enable or disable partial withdrawals (if disabled, sellers will only be able to request a
	full payout).

	Currently, only PayPal MassPay payouts are supported. You will need to enable this feature for your PayPal
	account and apply for PayPal API.


Changelog:
---------------------------------
	1.2 (2012-06-01)
		- Added product validation setting per seller
		- Added commission per seller
		- Added flat fee commissions
		- Added possibility to upload free products
		- Added possibility to mark withdrawal requests as paid		
		- Added "Message to the reviewer" field when creating seller account
		- Fixed installer bug	
		- Fixed minor bugs when no sellers have been added yet
		- Fixed Chrome back office syntax error in JavaScript
		
	1.1 (2012-05-20)
		- Added a hookable sellers carousel module	
		- Added a page to list seller's products
		- Added a page to list sellers
		- Added seller profile page
		- Changed nowdoc strings to heredoc for PHP < 5.3.0 compatibility	
		- Modified seller information on product page		


	1.0.1 (2012-05-09)
		- Fixed seller account approval bug
		- Fixed duplicate administrative seller account actions
		- Added a possibility to choose whether to notify seller about account modification or not
		- Split general mails and administrator messages
		
	1.0.0 (2012-05-04)
		- Initial release






ALTER TABLE oc152_ms_seller ADD COLUMN `commission_flat` decimal(15,4) NOT NULL DEFAULT '0';
ALTER TABLE oc152_ms_seller ADD COLUMN `product_validation` tinyint(4) NOT NULL DEFAULT '1';
ALTER TABLE oc152_ms_commission ADD COLUMN `commission_flat` decimal(15,4) NOT NULL DEFAULT '0';