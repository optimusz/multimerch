<?php echo $header; ?>

<div id="content" class="ms-settings">
	<div class="breadcrumb">
	  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	  <?php } ?>
	</div>
	<div class="error" id="error"></div>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/multiseller/ms-gear.png"/><?php echo $heading_title; ?></h1>
			<div class="buttons">
				<?php if (isset($updates)) { ?>
				<?php foreach ($updates as $v => $link) { ?>
				<a class="button" href="<?php echo $link; ?>">Update DB to version <?php echo $v; ?></a>
				<?php } ?>
				<?php } ?>
				<a class="button" id="saveSettings"><?php echo $button_save; ?></a>
				<a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a>
			</div>
	  	</div>
	  	<div class="content">
			<form id="settings" method="post" enctype="multipart/form-data">
			 	<div id="tabs" class="htabs">
			 		<a href="#tab-general"><?php echo $tab_general; ?></a>
			 		<a href="#tab-productform"><?php echo $ms_config_productform; ?></a>
			 		<a href="#tab-finances"><?php echo $ms_config_finances; ?></a>
			 		<a href="#tab-comments"><?php echo $ms_config_comments; ?></a>
			 		<a href="#tab-modules"><?php echo $ms_config_modules; ?></a>
					<a href="#tab-miscellaneous"><?php echo $ms_config_miscellaneous; ?></a>
			 	</div>
				
			 	<!-- BEGIN GENERAL TAB -->
			 	<div id="tab-general">
				<table class="form">
					<tr>
						<td>
							<span><?php echo $ms_config_notification_email; ?></span>
							<span class="help"><?php echo $ms_config_notification_email_note; ?></span>
						</td>
						<td>
							<input size="20" type="text" name="msconf_notification_email" value="<?php echo $msconf_notification_email; ?>" />
						</td>
					</tr>				
				
					<tr>
						<td>
							<span><?php echo $ms_config_seller_validation; ?></span>
							<span class="help"><?php echo $ms_config_seller_validation_note; ?></span>
						</td>
						<td>
						  	<select name="msconf_seller_validation">
						  	  <option value="1" <?php if($msconf_seller_validation == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_none; ?></option>
							  <!--<option value="2" <?php if($msconf_seller_validation == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_activation; ?></option>-->
							  <option value="3" <?php if($msconf_seller_validation == 3) { ?> selected="selected" <?php } ?>><?php echo $ms_config_seller_validation_approval; ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_product_validation; ?></span>
							<span class="help"><?php echo $ms_config_product_validation_note; ?></span>
						</td>
						<td>
						  	<select name="msconf_product_validation">
							<option value="1" <?php if($msconf_product_validation == 1) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_none; ?></option>
							<option value="2" <?php if($msconf_product_validation == 2) { ?> selected="selected" <?php } ?>><?php echo $ms_config_product_validation_approval; ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_disable_product_after_quantity_depleted; ?></span>
							<span class="help"><?php echo $ms_config_disable_product_after_quantity_depleted_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_disable_product_after_quantity_depleted" value="1" <?php if($msconf_disable_product_after_quantity_depleted == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_disable_product_after_quantity_depleted" value="0" <?php if($msconf_disable_product_after_quantity_depleted == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_allow_relisting; ?></span>
							<span class="help"><?php echo $ms_config_allow_relisting_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_allow_relisting" value="1" <?php if($msconf_allow_relisting == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_allow_relisting" value="0" <?php if($msconf_allow_relisting == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_enable_one_page_seller_registration; ?></span>
							<span class="help"><?php echo $ms_config_enable_one_page_seller_registration_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_one_page_seller_registration" value="1" <?php if($msconf_enable_one_page_seller_registration == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_enable_one_page_seller_registration" value="0" <?php if($msconf_enable_one_page_seller_registration == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_seller_terms_page; ?></span>
							<span class="help"><?php echo $ms_config_seller_terms_page_note; ?></span>
						</td>
						<td>
							<select name="msconf_seller_terms_page">
								<option value="0"><?php echo $text_none; ?></option>
								<?php foreach ($informations as $information) { ?>
								<?php if ($information['information_id'] == $msconf_seller_terms_page) { ?>
								<option value="<?php echo $information['information_id']; ?>" selected="selected"><?php echo $information['title']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $information['information_id']; ?>"><?php echo $information['title']; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_graphical_sellermenu; ?></span>
							<span class="help"><?php echo $ms_config_graphical_sellermenu_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_graphical_sellermenu" value="1" <?php if($msconf_graphical_sellermenu == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_graphical_sellermenu" value="0" <?php if($msconf_graphical_sellermenu == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_enable_rte; ?></span>
							<span class="help"><?php echo $ms_config_enable_rte_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_rte" value="1" <?php if($msconf_enable_rte == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_enable_rte" value="0" <?php if($msconf_enable_rte == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_rte_whitelist; ?></span>
							<span class="help"><?php echo $ms_config_rte_whitelist_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_rte_whitelist" value="<?php echo $msconf_rte_whitelist; ?>" />
						</td>
					</tr>
				</table>
				</div>
				<!-- END GENERAL TAB -->
				
			 	<!-- BEGIN PRODUCT FORM TAB -->
			 	<div id="tab-productform">
				<table class="form">
					<tr>
						<td>
							<span><?php echo $ms_config_minmax_product_price; ?></span>
						</td>
						<td>
							<span><?php echo $ms_config_minimum_product_price; ?></span><input type="text" name="msconf_minimum_product_price" value="<?php echo $msconf_minimum_product_price; ?>" size="4"/>
							<span><?php echo $ms_config_maximum_product_price; ?></span><input type="text" name="msconf_maximum_product_price" value="<?php echo $msconf_maximum_product_price; ?>" size="4"/>
							<span class="help"><?php echo $ms_config_minmax_product_price_note; ?></span>
						</td>
					</tr>
					
		   			<tr>
						<td>
							<span><?php echo $ms_config_allow_free_products; ?></span>
							<span class="help"><?php echo $ms_config_allow_free_products_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_allow_free_products" value="1" <?php if($msconf_allow_free_products == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_allow_free_products" value="0" <?php if($msconf_allow_free_products == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>

		   			<tr>
						<td>
							<span><?php echo $ms_config_allow_specials; ?></span>
							<span class="help"><?php echo $ms_config_allow_specials_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_allow_specials" value="1" <?php if($msconf_allow_specials == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_allow_specials" value="0" <?php if($msconf_allow_specials == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>

		   			<tr>
						<td>
							<span><?php echo $ms_config_allow_discounts; ?></span>
							<span class="help"><?php echo $ms_config_allow_discounts_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_allow_discounts" value="1" <?php if($msconf_allow_discounts == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_allow_discounts" value="0" <?php if($msconf_allow_discounts == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_allow_multiple_categories; ?></span>
							<span class="help"><?php echo $ms_config_allow_multiple_categories_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_allow_multiple_categories" value="1" <?php if($msconf_allow_multiple_categories == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_allow_multiple_categories" value="0" <?php if($msconf_allow_multiple_categories == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_additional_category_restrictions; ?></span>
							<span class="help"><?php echo $ms_config_additional_category_restrictions_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_additional_category_restrictions" value="0" <?php if($msconf_additional_category_restrictions == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_none; ?>
							<input type="radio" name="msconf_additional_category_restrictions" value="1" <?php if($msconf_additional_category_restrictions == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_topmost_categories; ?>
							<input type="radio" name="msconf_additional_category_restrictions" value="2" <?php if($msconf_additional_category_restrictions == 2) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_parent_categories; ?>							
					  	</td>
					</tr>
					
		   			<tr>
						<td>
							<span><?php echo $ms_config_restrict_categories; ?></span>
							<span class="help"><?php echo $ms_config_restrict_categories_note; ?></span>
						</td>
						<td>
							<div class="scrollbox">
							<?php $class = 'odd'; ?>
							<?php foreach ($categories as $category) { ?>
								<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
								<div class="<?php echo $class; ?>">
									<input type="checkbox" name="msconf_restrict_categories[]" value="<?php echo $category['category_id']; ?>" <?php if (isset($msconf_restrict_categories) && in_array($category['category_id'], $msconf_restrict_categories)) { ?>checked="checked"<?php } ?> />
									<?php echo $category['name']; ?>
								</div>
							<?php } ?>
							</div>
					  	</td>
					</tr>

                    <tr>
                        <td>
                            <span><?php echo $ms_config_product_included_fields; ?></span>
                            <span class="help"><?php echo $ms_config_product_included_fields_note; ?></span>
                        </td>
                        <td>
                            <div class="scrollbox">
                                <?php $class = 'odd'; ?>
                                <?php foreach ($product_included_fieds as $field_code=>$field_name) { ?>
                                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                <div class="<?php echo $class; ?>">
                                    <input type="checkbox" name="msconf_product_included_fields[]" value="<?php echo $field_code; ?>" <?php if (isset($msconf_product_included_fields) && in_array($field_code, $msconf_product_included_fields)) { ?>checked="checked"<?php } ?> />
                                    <?php echo $field_name; ?>
                                </div>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
					
					<tr>
						  <td>
						  		<span><?php echo $ms_config_allowed_image_types; ?></span>
								<span class="help"><?php echo $ms_config_allowed_image_types_note; ?></span>
						  </td>
						  <td>
						  	<input type="text" name="msconf_allowed_image_types" value="<?php echo $msconf_allowed_image_types; ?>" />
						  </td>
					</tr>
					
					<tr>
						  <td>
						  		<span><?php echo $ms_config_allowed_download_types; ?></span>
								<span class="help"><?php echo $ms_config_allowed_download_types_note; ?></span>
						  </td>
						  <td>
						  	<input type="text" name="msconf_allowed_download_types" value="<?php echo $msconf_allowed_download_types; ?>" />
						  </td>
					</tr>
					
					<tr>
						  <td>
						  		<span><?php echo $ms_config_images_limits; ?></span>
								<span class="help"><?php echo $ms_config_images_limits_note; ?></span>
						  </td>
						  <td>
						  	Min. <input type="text" name="msconf_images_limits[]" value="<?php echo $msconf_images_limits[0]; ?>" size="3" /> 
						  	Max. <input type="text" name="msconf_images_limits[]" value="<?php echo $msconf_images_limits[1]; ?>" size="3" />
						  </td>
					</tr>

					<tr>
						<td>
								<span><?php echo $ms_config_downloads_limits; ?></span>
								<span class="help"><?php echo $ms_config_downloads_limits_note; ?></span>
						</td>
						<td>
							Min. <input type="text" name="msconf_downloads_limits[]" value="<?php echo $msconf_downloads_limits[0]; ?>" size="3" /> 
							Max. <input type="text" name="msconf_downloads_limits[]" value="<?php echo $msconf_downloads_limits[1]; ?>" size="3" />
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_enable_shipping; ?></span>
							<span class="help"><?php echo $ms_config_enable_shipping_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_shipping" value="1" <?php if($msconf_enable_shipping == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_enable_shipping" value="0" <?php if($msconf_enable_shipping == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
							<input type="radio" name="msconf_enable_shipping" value="2" <?php if($msconf_enable_shipping == 2) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_seller_select; ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_enable_quantities; ?></span>
							<span class="help"><?php echo $ms_config_enable_quantities_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_quantities" value="1" <?php if($msconf_enable_quantities == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_enable_quantities" value="0" <?php if($msconf_enable_quantities == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
							<input type="radio" name="msconf_enable_quantities" value="2" <?php if($msconf_enable_quantities == 2) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_shipping_dependent; ?>
					  	</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_provide_buyerinfo; ?></span>
							<span class="help"><?php echo $ms_config_provide_buyerinfo_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_provide_buyerinfo" value="1" <?php if($msconf_provide_buyerinfo == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_provide_buyerinfo" value="0" <?php if($msconf_provide_buyerinfo == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
							<input type="radio" name="msconf_provide_buyerinfo" value="2" <?php if($msconf_provide_buyerinfo == 2) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_shipping_dependent; ?>
					  	</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_enable_pdf_generator; ?></span>
							<span class="help"><?php echo $ms_config_enable_pdf_generator_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_pdf_generator" value="1" <?php if($msconf_enable_pdf_generator == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_enable_pdf_generator" value="0" <?php if($msconf_enable_pdf_generator == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>
				</table>
				</div>
				<!-- END PRODUCT FORM TAB -->
				
			 	<!-- BEGIN FINANCES TAB -->
			 	<div id="tab-finances">
				<table class="form">
					<tr>
						  <td>
						  		<span><?php echo $ms_config_credit_order_statuses; ?></span>
								<span class="help"><?php echo $ms_config_credit_order_statuses_note; ?></span>
						  </td>
						  <td>
						  	<div class="scrollbox">
							  <?php $class = 'odd'; ?>
							  <?php foreach ($order_statuses as $status) { ?>
							  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
							  <div class="<?php echo $class; ?>">
								<?php if (in_array($status['order_status_id'], $msconf_credit_order_statuses)) { ?>
								<input type="checkbox" name="msconf_credit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" checked="checked" />
								<?php echo $status['name']; ?>
								<?php } else { ?>
								<input type="checkbox" name="msconf_credit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" />
								<?php echo $status['name']; ?>
								<?php } ?>
							  </div>
							  <?php } ?>
							</div>
						  </td>
					</tr>
					
					<tr>
						  <td>
						  		<span><?php echo $ms_config_debit_order_statuses; ?></span>
								<span class="help"><?php echo $ms_config_debit_order_statuses_note; ?></span>
						  </td>
						  <td>
						  	<div class="scrollbox">
							  <?php $class = 'odd'; ?>
							  <?php foreach ($order_statuses as $status) { ?>
							  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
							  <div class="<?php echo $class; ?>">
								<?php if (in_array($status['order_status_id'], $msconf_debit_order_statuses)) { ?>
								<input type="checkbox" name="msconf_debit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" checked="checked" />
								<?php echo $status['name']; ?>
								<?php } else { ?>
								<input type="checkbox" name="msconf_debit_order_statuses[]" value="<?php echo $status['order_status_id']; ?>" />
								<?php echo $status['name']; ?>
								<?php } ?>
							  </div>
							  <?php } ?>
							</div>
						  </td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_allow_withdrawal_requests; ?></span>
							<span class="help"><?php echo $ms_config_allow_withdrawal_requests_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_allow_withdrawal_requests" value="1" <?php if($msconf_allow_withdrawal_requests == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_allow_withdrawal_requests" value="0" <?php if($msconf_allow_withdrawal_requests == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>

					<tr>
						  <td>
								<span><?php echo $ms_config_withdrawal_waiting_period; ?></span>
								<span class="help"><?php echo $ms_config_withdrawal_waiting_period_note; ?></span>
						</td>
						<td>
							<input type="text" size="3" name="msconf_withdrawal_waiting_period" value="<?php echo $msconf_withdrawal_waiting_period; ?>" /><?php echo $ms_days; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_minimum_withdrawal; ?></span>
							<span class="help"><?php echo $ms_config_minimum_withdrawal_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_minimum_withdrawal_amount" value="<?php echo $msconf_minimum_withdrawal_amount; ?>" size="3"/>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_allow_partial_withdrawal; ?></span>
							<span class="help"><?php echo $ms_config_allow_partial_withdrawal_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_allow_partial_withdrawal" value="1" <?php if($msconf_allow_partial_withdrawal == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_allow_partial_withdrawal" value="0" <?php if($msconf_allow_partial_withdrawal == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_paypal_address; ?></span>
							<span class="help"><?php echo $ms_config_paypal_address_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_paypal_address" value="<?php echo $msconf_paypal_address; ?>" size="30"/>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_paypal_api_username; ?></span>
							<span class="help"><?php echo $ms_config_paypal_api_username_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_paypal_api_username" value="<?php echo $msconf_paypal_api_username; ?>" size="30"/>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_paypal_api_password; ?></span>
							<span class="help"><?php echo $ms_config_paypal_api_password_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_paypal_api_password" value="<?php echo $msconf_paypal_api_password; ?>" size="30"/>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_paypal_api_signature; ?></span>
							<span class="help"><?php echo $ms_config_paypal_api_signature_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_paypal_api_signature" value="<?php echo $msconf_paypal_api_signature; ?>" size="30"/>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_paypal_sandbox; ?></span>
							<span class="help"><?php echo $ms_config_paypal_sandbox_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_paypal_sandbox" value="1" <?php if($msconf_paypal_sandbox == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_paypal_sandbox" value="0" <?php if($msconf_paypal_sandbox == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>
				</table>
				</div>
				
			 	<!-- BEGIN COMMENTS TAB -->
			 	<div id="tab-comments">
				<table class="form">
                    <!-- PRODUCT COMMENTS -->
                    <tr><td colspan="2"><h2><?php echo $ms_config_product_comments; ?></h2></td></tr>                    
					<tr>
						<td>
							<span><?php echo $ms_config_comments_enable; ?></span>
							<span class="help"><?php echo $ms_config_comments_enable_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_comments_enable" value="1" <?php if($msconf_comments_enable == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_comments_enable" value="0" <?php if($msconf_comments_enable == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_allow_guests; ?></span>
							<span class="help"><?php echo $ms_config_comments_allow_guests_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_comments_allow_guests" value="1" <?php if($msconf_comments_allow_guests == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_comments_allow_guests" value="0" <?php if($msconf_comments_allow_guests == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_enforce_customer_data; ?></span>
							<span class="help"><?php echo $ms_config_comments_enforce_customer_data_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_comments_enforce_customer_data" value="1" <?php if($msconf_comments_enforce_customer_data == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_comments_enforce_customer_data" value="0" <?php if($msconf_comments_enforce_customer_data == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_enable_customer_captcha; ?></span>
							<span class="help"><?php echo $ms_config_comments_enable_customer_captcha_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_comments_enable_customer_captcha" value="1" <?php if($msconf_comments_enable_customer_captcha == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_comments_enable_customer_captcha" value="0" <?php if($msconf_comments_enable_customer_captcha == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_perpage; ?></span>
							<span class="help"><?php echo $ms_config_comments_perpage_note; ?></span>
						</td>
						<td>
							<input size="2" type="text" name="msconf_comments_perpage" value="<?php echo $msconf_comments_perpage; ?>" />
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_maxlen; ?></span>
							<span class="help"><?php echo $ms_config_comments_maxlen_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_comments_maxlen" value="<?php echo $msconf_comments_maxlen; ?>" size="3"/>
						</td>
					</tr>
                    
                    <!-- PRODUCT COMMENTS END -->

                    <!-- SELLER COMMENTS -->
                    <tr><td colspan="2"><h2><?php echo $ms_config_seller_comments; ?></h2></td></tr>
					<tr>
						<td>
							<span><?php echo $ms_config_seller_comments_enable; ?></span>
							<span class="help"><?php echo $ms_config_seller_comments_enable_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_seller_comments_enable" value="1" <?php if($msconf_seller_comments_enable == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_seller_comments_enable" value="0" <?php if($msconf_seller_comments_enable == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_allow_guests; ?></span>
							<span class="help"><?php echo $ms_config_comments_allow_guests_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_seller_comments_allow_guests" value="1" <?php if($msconf_seller_comments_allow_guests == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_seller_comments_allow_guests" value="0" <?php if($msconf_seller_comments_allow_guests == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_enforce_customer_data; ?></span>
							<span class="help"><?php echo $ms_config_comments_enforce_customer_data_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_seller_comments_enforce_customer_data" value="1" <?php if($msconf_seller_comments_enforce_customer_data == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_seller_comments_enforce_customer_data" value="0" <?php if($msconf_seller_comments_enforce_customer_data == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_enable_customer_captcha; ?></span>
							<span class="help"><?php echo $ms_config_comments_enable_customer_captcha_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_seller_comments_enable_customer_captcha" value="1" <?php if($msconf_seller_comments_enable_customer_captcha == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_seller_comments_enable_customer_captcha" value="0" <?php if($msconf_seller_comments_enable_customer_captcha == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_perpage; ?></span>
							<span class="help"><?php echo $ms_config_comments_perpage_note; ?></span>
						</td>
						<td>
							<input size="2" type="text" name="msconf_seller_comments_perpage" value="<?php echo $msconf_seller_comments_perpage; ?>" />
						</td>
					</tr>

					<tr>
						<td>
							<span><?php echo $ms_config_comments_maxlen; ?></span>
							<span class="help"><?php echo $ms_config_comments_maxlen_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_seller_comments_maxlen" value="<?php echo $msconf_seller_comments_maxlen; ?>" size="3"/>
						</td>
					</tr>
                    <!-- SELLER COMMENTS END -->                    
                    
				</table>
				</div>
								
				<!-- BEGIN MODULES TAB -->
				<div id="tab-modules">	
					<div id="tabs-modules" class="htabs">
				 		<a href="#tab-carousel"><?php echo $ms_config_carousel; ?></a>
				 		<a href="#tab-sellerdropdown"><?php echo $ms_config_sellerdropdown; ?></a>				 		
				 		<a href="#tab-topsellers"><?php echo $ms_config_topsellers; ?></a>
				 		<a href="#tab-newsellers"><?php echo $ms_config_newsellers; ?></a>
				 	</div>
				 	
					<!-- BEGIN CAROUSEL -->
					<div id="tab-carousel">
					  <table id="module" class="list">
						<thead>
						  <tr>
							<td class="left"><?php echo $ms_config_layout; ?></td>
							<td class="left"><?php echo $ms_config_position; ?></td>				  
							<td class="left"><?php echo $ms_config_limit; ?></td>
							<td class="left"><?php echo $ms_config_scroll; ?></td>
							<td class="left"><?php echo $ms_config_image; ?></td>
							<td class="left"><?php echo $ms_config_status; ?></td>
							<td class="right"><?php echo $ms_config_sort_order; ?></td>
							<td></td>
						  </tr>
						</thead>
						<tbody>
						
						<!-- sample row -->
						<tr class="ffSample">
							<td class="left">
								<select name="ms_carousel_module[0][layout_id]">
									<?php foreach ($layouts as $layout) { ?>
									<option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="left">
								<select name="ms_carousel_module[0][position]">
									<option value="content_top"><?php echo $ms_config_top; ?></option>
									<option value="content_bottom"><?php echo $ms_config_bottom; ?></option>
									<option value="column_left"><?php echo $ms_config_column_left; ?></option>
									<option value="column_right"><?php echo $ms_config_column_right; ?></option>
								</select>
							</td>
							<td class="left">
								<input type="text" name="ms_carousel_module[0][limit]" value="" size="1" />
							</td>
							<td class="left">
								<input type="text" name="ms_carousel_module[0][scroll]" value="" size="3" />
							</td>
							<td class="left">
								<input type="text" name="ms_carousel_module[0][width]" value="" size="3" />
								<input type="text" name="ms_carousel_module[0][height]" value="" size="3" />
							  	<span class="error"></span>
							</td>
							<td class="left">
								<select name="ms_carousel_module[0][status]">
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
								</select>
							</td>
							<td class="right">
								<input type="text" name="ms_carousel_module[0][sort_order]" value="" size="3" />
							</td>
							<td class="left">
								<a class="button ffRemove"><?php echo $button_remove; ?></a>
							</td>
						</tr>				
						<!-- /sample row -->
						
						<?php $row = 1; ?>
						<?php if (isset($ms_carousel_module) && is_array($ms_carousel_module)) { ?>
						<?php foreach ($ms_carousel_module as $module) { ?>
						<tr id="module-row<?php echo $row; ?>">
							<td class="left">
								<select name="ms_carousel_module[<?php echo $row; ?>][layout_id]">
									<?php foreach ($layouts as $layout) { ?>
									<?php if ($layout['layout_id'] == $module['layout_id']) { ?>
									<option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</td>
							<td class="left">
						   		<select name="ms_carousel_module[<?php echo $row; ?>][position]">
									<?php if ($module['position'] == 'content_top') { ?>
									<option value="content_top" selected="selected"><?php echo $ms_config_top; ?></option>
									<?php } else { ?>
									<option value="content_top"><?php echo $ms_config_top; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'content_bottom') { ?>
									<option value="content_bottom" selected="selected"><?php echo $ms_config_bottom; ?></option>
									<?php } else { ?>
									<option value="content_bottom"><?php echo $ms_config_bottom; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'column_left') { ?>
									<option value="column_left" selected="selected"><?php echo $ms_config_column_left; ?></option>
									<?php } else { ?>
									<option value="column_left"><?php echo $ms_config_column_left; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'column_right') { ?>
									<option value="column_right" selected="selected"><?php echo $ms_config_column_right; ?></option>
									<?php } else { ?>
									<option value="column_right"><?php echo $ms_config_column_right; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="left">
								<input type="text" name="ms_carousel_module[<?php echo $row; ?>][limit]" value="<?php echo $module['limit']; ?>" size="1" />
							</td>
							<td class="left">
								<input type="text" name="ms_carousel_module[<?php echo $row; ?>][scroll]" value="<?php echo $module['scroll']; ?>" size="3" />
							</td>
							<td class="left">
								<input type="text" name="ms_carousel_module[<?php echo $row; ?>][width]" value="<?php echo $module['width']; ?>" size="3" />
								<input type="text" name="ms_carousel_module[<?php echo $row; ?>][height]" value="<?php echo $module['height']; ?>" size="3" />
								<?php if (isset($error_image[$row])) { ?>
								<span class="error"><?php echo $error_image[$row]; ?></span>
								<?php } ?>
							</td>
							<td class="left">
								<select name="ms_carousel_module[<?php echo $row; ?>][status]">
									<?php if ($module['status']) { ?>
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
									<option value="1"><?php echo $text_enabled; ?></option>
									<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="right">
								<input type="text" name="ms_carousel_module[<?php echo $row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" />
							</td>
							<td class="left">
								<a class="button ffRemove"><?php echo $button_remove; ?></a>
						   	</td>
						</tr>
						<?php $row++; ?>
						<?php } ?>
						<?php } ?>
						</tbody>
						<tfoot>
						  <tr>
							<td colspan="7"></td>
							<td class="left"><a class="button ffClone"><?php echo $button_add_module; ?></a></td>
						  </tr>
						</tfoot>
					  </table>
					</div>
					<!-- END CAROUSEL -->
					
					<!-- BEGIN TOPSELLERS -->
					<div id="tab-topsellers">
					  <table class="list">
						<thead>
						  <tr>
							<td class="left"><?php echo $ms_config_layout; ?></td>
							<td class="left"><?php echo $ms_config_position; ?></td>				  
							<td class="left"><?php echo $ms_config_limit; ?></td>
							<td class="left"><?php echo $ms_config_image; ?></td>							
							<td class="left"><?php echo $ms_config_status; ?></td>
							<td class="right"><?php echo $ms_config_sort_order; ?></td>
							<td></td>
						  </tr>
						</thead>
		
						<tbody>
						<!-- sample row -->
						<tr class="ffSample">
							<td class="left">
								<select name="ms_topsellers_module[0][layout_id]">
									<?php foreach ($layouts as $layout) { ?>
									<option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="left">
								<select name="ms_topsellers_module[0][position]">
									<option value="content_top"><?php echo $ms_config_top; ?></option>
									<option value="content_bottom"><?php echo $ms_config_bottom; ?></option>
									<option value="column_left"><?php echo $ms_config_column_left; ?></option>
									<option value="column_right"><?php echo $ms_config_column_right; ?></option>
								</select>
							</td>				  
							<td class="left">
								<input type="text" name="ms_topsellers_module[0][limit]" value="" size="1" />
							</td>
							<td class="left">
								<input type="text" name="ms_topsellers_module[0][width]" value="" size="3" />
								<input type="text" name="ms_topsellers_module[0][height]" value="" size="3" />
							  	<span class="error"></span>
							</td>							
							<td class="left">
								<select name="ms_topsellers_module[0][status]">
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
							  	</select>
							</td>
							<td class="right">
								<input type="text" name="ms_topsellers_module[0][sort_order]" value="" size="3" />
							</td>
							<td class="left">
								<a class="button ffRemove"><?php echo $button_remove; ?></a>
							</td>
						  </tr>
						<!-- /sample row -->
		
						<?php $row = 1; ?>
						<?php if (isset($ms_topsellers_module) && is_array($ms_topsellers_module)) { ?>
						<?php foreach ($ms_topsellers_module as $module) { ?>
						<tr>
							<td class="left">
								<select name="ms_topsellers_module[<?php echo $row; ?>][layout_id]">
									<?php foreach ($layouts as $layout) { ?>
									<?php if ($layout['layout_id'] == $module['layout_id']) { ?>
									<option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</td>
							<td class="left">
						   		<select name="ms_topsellers_module[<?php echo $row; ?>][position]">
									<?php if ($module['position'] == 'content_top') { ?>
									<option value="content_top" selected="selected"><?php echo $ms_config_top; ?></option>
									<?php } else { ?>
									<option value="content_top"><?php echo $ms_config_top; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'content_bottom') { ?>
									<option value="content_bottom" selected="selected"><?php echo $ms_config_bottom; ?></option>
									<?php } else { ?>
									<option value="content_bottom"><?php echo $ms_config_bottom; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'column_left') { ?>
									<option value="column_left" selected="selected"><?php echo $ms_config_column_left; ?></option>
									<?php } else { ?>
									<option value="column_left"><?php echo $ms_config_column_left; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'column_right') { ?>
									<option value="column_right" selected="selected"><?php echo $ms_config_column_right; ?></option>
									<?php } else { ?>
									<option value="column_right"><?php echo $ms_config_column_right; ?></option>
									<?php } ?>
								</select>
							</td>				  
							<td class="left">
								<input type="text" name="ms_topsellers_module[<?php echo $row; ?>][limit]" value="<?php echo $module['limit']; ?>" size="1" />
							</td>
							<td class="left">
								<input type="text" name="ms_topsellers_module[<?php echo $row; ?>][width]" value="<?php echo $module['width']; ?>" size="3" />
								<input type="text" name="ms_topsellers_module[<?php echo $row; ?>][height]" value="<?php echo $module['height']; ?>" size="3" />
								<?php if (isset($error_image[$row])) { ?>
								<span class="error"><?php echo $error_image[$row]; ?></span>
								<?php } ?>
							</td>							
							<td class="left">
								<select name="ms_topsellers_module[<?php echo $row; ?>][status]">
									<?php if ($module['status']) { ?>
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
									<option value="1"><?php echo $text_enabled; ?></option>
									<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="right">
								<input type="text" name="ms_topsellers_module[<?php echo $row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" />
							</td>
							<td class="left">
								<a class="button ffRemove"><?php echo $button_remove; ?></a>
							</td>
						</tr>
						<?php $row++; ?>
						<?php } ?>
						<?php } ?>
						</tbody>
						
						<tfoot>
						  <tr>
							<td colspan="6"></td>
							<td class="left">
								<a class="button ffClone"><?php echo $button_add_module; ?></a>
							</td>
						  </tr>
						</tfoot>
					  </table>
					</div>
					<!-- END TOPSELLERS -->
					
					<!-- BEGIN SELLERS DROPDOWN -->
					<div id="tab-sellerdropdown">
					  <table class="list">
						<thead>
						  <tr>
							<td class="left"><?php echo $ms_config_layout; ?></td>
							<td class="left"><?php echo $ms_config_position; ?></td>				  
							<td class="left"><?php echo $ms_config_status; ?></td>
							<td class="right"><?php echo $ms_config_sort_order; ?></td>
							<td></td>
						  </tr>
						</thead>
		
						<tbody>
						<!-- sample row -->
						<tr class="ffSample">
							<td class="left">
								<select name="ms_sellerdropdown_module[0][layout_id]">
									<?php foreach ($layouts as $layout) { ?>
									<option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="left">
								<select name="ms_sellerdropdown_module[0][position]">
									<option value="content_top"><?php echo $ms_config_top; ?></option>
									<option value="content_bottom"><?php echo $ms_config_bottom; ?></option>
									<option value="column_left"><?php echo $ms_config_column_left; ?></option>
									<option value="column_right"><?php echo $ms_config_column_right; ?></option>
								</select>
							</td>				  
							<td class="left">
								<select name="ms_sellerdropdown_module[0][status]">
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
							  	</select>
							</td>
							<td class="right">
								<input type="text" name="ms_sellerdropdown_module[0][sort_order]" value="" size="3" />
							</td>
							<td class="left">
								<a class="button ffRemove"><?php echo $button_remove; ?></a>
							</td>
						  </tr>
						<!-- /sample row -->
		
						<?php $row = 1; ?>
						<?php if (isset($ms_sellerdropdown_module) && is_array($ms_sellerdropdown_module)) { ?>
						<?php foreach ($ms_sellerdropdown_module as $module) { ?>
						<tr>
							<td class="left">
								<select name="ms_sellerdropdown_module[<?php echo $row; ?>][layout_id]">
									<?php foreach ($layouts as $layout) { ?>
									<?php if ($layout['layout_id'] == $module['layout_id']) { ?>
									<option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</td>
							<td class="left">
						   		<select name="ms_sellerdropdown_module[<?php echo $row; ?>][position]">
									<?php if ($module['position'] == 'content_top') { ?>
									<option value="content_top" selected="selected"><?php echo $ms_config_top; ?></option>
									<?php } else { ?>
									<option value="content_top"><?php echo $ms_config_top; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'content_bottom') { ?>
									<option value="content_bottom" selected="selected"><?php echo $ms_config_bottom; ?></option>
									<?php } else { ?>
									<option value="content_bottom"><?php echo $ms_config_bottom; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'column_left') { ?>
									<option value="column_left" selected="selected"><?php echo $ms_config_column_left; ?></option>
									<?php } else { ?>
									<option value="column_left"><?php echo $ms_config_column_left; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'column_right') { ?>
									<option value="column_right" selected="selected"><?php echo $ms_config_column_right; ?></option>
									<?php } else { ?>
									<option value="column_right"><?php echo $ms_config_column_right; ?></option>
									<?php } ?>
								</select>
							</td>				  
							<td class="left">
								<select name="ms_sellerdropdown_module[<?php echo $row; ?>][status]">
									<?php if ($module['status']) { ?>
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
									<option value="1"><?php echo $text_enabled; ?></option>
									<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="right">
								<input type="text" name="ms_sellerdropdown_module[<?php echo $row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" />
							</td>
							<td class="left">
								<a class="button ffRemove"><?php echo $button_remove; ?></a>
							</td>
						</tr>
						<?php $row++; ?>
						<?php } ?>
						<?php } ?>
						</tbody>
						
						<tfoot>
						  <tr>
							<td colspan="4"></td>
							<td class="left">
								<a class="button ffClone"><?php echo $button_add_module; ?></a>
							</td>
						  </tr>
						</tfoot>
					  </table>
					</div>
					<!-- END RANDOM SELLERS -->
					
					<!-- BEGIN NEW SELLERS -->
					<div id="tab-newsellers">
					  <table class="list">
						<thead>
						  <tr>
							<td class="left"><?php echo $ms_config_layout; ?></td>
							<td class="left"><?php echo $ms_config_position; ?></td>				  
							<td class="left"><?php echo $ms_config_limit; ?></td>
							<td class="left"><?php echo $ms_config_image; ?></td>							
							<td class="left"><?php echo $ms_config_status; ?></td>
							<td class="right"><?php echo $ms_config_sort_order; ?></td>
							<td></td>
						  </tr>
						</thead>
		
						<tbody>
						<!-- sample row -->
						<tr class="ffSample">
							<td class="left">
								<select name="ms_newsellers_module[0][layout_id]">
									<?php foreach ($layouts as $layout) { ?>
									<option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="left">
								<select name="ms_newsellers_module[0][position]">
									<option value="content_top"><?php echo $ms_config_top; ?></option>
									<option value="content_bottom"><?php echo $ms_config_bottom; ?></option>
									<option value="column_left"><?php echo $ms_config_column_left; ?></option>
									<option value="column_right"><?php echo $ms_config_column_right; ?></option>
								</select>
							</td>				  
							<td class="left">
								<input type="text" name="ms_newsellers_module[0][limit]" value="" size="1" />
							</td>
							<td class="left">
								<input type="text" name="ms_newsellers_module[0][width]" value="" size="3" />
								<input type="text" name="ms_newsellers_module[0][height]" value="" size="3" />
							  	<span class="error"></span>
							</td>							
							<td class="left">
								<select name="ms_newsellers_module[0][status]">
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
							  	</select>
							</td>
							<td class="right">
								<input type="text" name="ms_newsellers_module[0][sort_order]" value="" size="3" />
							</td>
							<td class="left">
								<a class="button ffRemove"><?php echo $button_remove; ?></a>
							</td>
						  </tr>
						<!-- /sample row -->
		
						<?php $row = 1; ?>
						<?php if (isset($ms_newsellers_module) && is_array($ms_newsellers_module)) { ?>
						<?php foreach ($ms_newsellers_module as $module) { ?>
						<tr>
							<td class="left">
								<select name="ms_newsellers_module[<?php echo $row; ?>][layout_id]">
									<?php foreach ($layouts as $layout) { ?>
									<?php if ($layout['layout_id'] == $module['layout_id']) { ?>
									<option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							</td>
							<td class="left">
						   		<select name="ms_newsellers_module[<?php echo $row; ?>][position]">
									<?php if ($module['position'] == 'content_top') { ?>
									<option value="content_top" selected="selected"><?php echo $ms_config_top; ?></option>
									<?php } else { ?>
									<option value="content_top"><?php echo $ms_config_top; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'content_bottom') { ?>
									<option value="content_bottom" selected="selected"><?php echo $ms_config_bottom; ?></option>
									<?php } else { ?>
									<option value="content_bottom"><?php echo $ms_config_bottom; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'column_left') { ?>
									<option value="column_left" selected="selected"><?php echo $ms_config_column_left; ?></option>
									<?php } else { ?>
									<option value="column_left"><?php echo $ms_config_column_left; ?></option>
									<?php } ?>
									<?php if ($module['position'] == 'column_right') { ?>
									<option value="column_right" selected="selected"><?php echo $ms_config_column_right; ?></option>
									<?php } else { ?>
									<option value="column_right"><?php echo $ms_config_column_right; ?></option>
									<?php } ?>
								</select>
							</td>				  
							<td class="left">
								<input type="text" name="ms_newsellers_module[<?php echo $row; ?>][limit]" value="<?php echo $module['limit']; ?>" size="1" />
							</td>
							<td class="left">
								<input type="text" name="ms_newsellers_module[<?php echo $row; ?>][width]" value="<?php echo $module['width']; ?>" size="3" />
								<input type="text" name="ms_newsellers_module[<?php echo $row; ?>][height]" value="<?php echo $module['height']; ?>" size="3" />
								<?php if (isset($error_image[$row])) { ?>
								<span class="error"><?php echo $error_image[$row]; ?></span>
								<?php } ?>
							</td>							
							<td class="left">
								<select name="ms_newsellers_module[<?php echo $row; ?>][status]">
									<?php if ($module['status']) { ?>
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
									<option value="1"><?php echo $text_enabled; ?></option>
									<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="right">
								<input type="text" name="ms_newsellers_module[<?php echo $row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" />
							</td>
							<td class="left">
								<a class="button ffRemove"><?php echo $button_remove; ?></a>
							</td>
						</tr>
						<?php $row++; ?>
						<?php } ?>
						<?php } ?>
						</tbody>
						
						<tfoot>
						  <tr>
							<td colspan="6"></td>
							<td class="left">
								<a class="button ffClone"><?php echo $button_add_module; ?></a>
							</td>
						  </tr>
						</tfoot>
					  </table>
					</div>
					<!-- END NEW SELLERS -->
				</div>
				<!-- END MODULES TAB -->
				
				<!-- BEGIN MISCELLANEOUS TAB -->
			 	<div id="tab-miscellaneous">
				<table class="form">
					<tr><td colspan="2"><h2><?php echo $ms_config_image_sizes; ?></h2></td></tr>
					<tr>
						<td>
							<span><?php echo $ms_config_seller_avatar_image_size; ?></span>
						</td>
						<td>
							<span><?php echo $ms_config_seller_avatar_image_size_seller_profile; ?></span>
							<input type="text" name="msconf_seller_avatar_seller_profile_image_width" value="<?php echo $msconf_seller_avatar_seller_profile_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_seller_avatar_seller_profile_image_height" value="<?php echo $msconf_seller_avatar_seller_profile_image_height; ?>" size="3" />
							&nbsp
							<span><?php echo $ms_config_seller_avatar_image_size_seller_list; ?></span>
							<input type="text" name="msconf_seller_avatar_seller_list_image_width" value="<?php echo $msconf_seller_avatar_seller_list_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_seller_avatar_seller_list_image_height" value="<?php echo $msconf_seller_avatar_seller_list_image_height; ?>" size="3" />
							&nbsp
							<span><?php echo $ms_config_seller_avatar_image_size_product_page; ?></span>
							<input type="text" name="msconf_seller_avatar_product_page_image_width" value="<?php echo $msconf_seller_avatar_product_page_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_seller_avatar_product_page_image_height" value="<?php echo $msconf_seller_avatar_product_page_image_height; ?>" size="3" />
							&nbsp
							<span><?php echo $ms_config_seller_avatar_image_size_seller_dashboard; ?></span>
							<input type="text" name="msconf_seller_avatar_dashboard_image_width" value="<?php echo $msconf_seller_avatar_dashboard_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_seller_avatar_dashboard_image_height" value="<?php echo $msconf_seller_avatar_dashboard_image_height; ?>" size="3" />
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_image_preview_size; ?></span>
						</td>
						<td>
							<span><?php echo $ms_config_image_preview_size_seller_avatar; ?></span>
							<input type="text" name="msconf_preview_seller_avatar_image_width" value="<?php echo $msconf_preview_seller_avatar_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_preview_seller_avatar_image_height" value="<?php echo $msconf_preview_seller_avatar_image_height; ?>" size="3" />
							&nbsp
							<span><?php echo $ms_config_image_preview_size_product_image; ?></span>
							<input type="text" name="msconf_preview_product_image_width" value="<?php echo $msconf_preview_product_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_preview_product_image_height" value="<?php echo $msconf_preview_product_image_height; ?>" size="3" />
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_product_image_size; ?></span>
						</td>
						<td>
							<span><?php echo $ms_config_product_image_size_seller_profile; ?></span>
							<input type="text" name="msconf_product_seller_profile_image_width" value="<?php echo $msconf_product_seller_profile_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_product_seller_profile_image_height" value="<?php echo $msconf_product_seller_profile_image_height; ?>" size="3" />
							&nbsp
							<span><?php echo $ms_config_product_image_size_seller_products_list; ?></span>
							<input type="text" name="msconf_product_seller_products_image_width" value="<?php echo $msconf_product_seller_products_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_product_seller_products_image_height" value="<?php echo $msconf_product_seller_products_image_height; ?>" size="3" />
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_badge_size; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_badge_width" value="<?php echo $msconf_badge_width; ?>" size="3" />
							x
							<input type="text" name="msconf_badge_height" value="<?php echo $msconf_badge_height; ?>" size="3" />
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_minimum_uploaded_image_size; ?></span>
							<span class="help"><?php echo $ms_config_minimum_uploaded_image_size_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_min_uploaded_image_width" value="<?php echo $msconf_min_uploaded_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_min_uploaded_image_height" value="<?php echo $msconf_min_uploaded_image_height; ?>" size="3" />
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_maximum_uploaded_image_size; ?></span>
							<span class="help"><?php echo $ms_config_maximum_uploaded_image_size_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_max_uploaded_image_width" value="<?php echo $msconf_max_uploaded_image_width; ?>" size="3" />
							x
							<input type="text" name="msconf_max_uploaded_image_height" value="<?php echo $msconf_max_uploaded_image_height; ?>" size="3" />
						</td>
					</tr>
					
					<tr><td colspan="2"><h2><?php echo $ms_config_seo; ?></h2></td></tr>
					<tr>
						<td>
							<span><?php echo $ms_config_enable_seo_urls_seller; ?></span>
							<span class="help"><?php echo $ms_config_enable_seo_urls_seller_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_seo_urls_seller" value="1" <?php if($msconf_enable_seo_urls_seller == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_enable_seo_urls_seller" value="0" <?php if($msconf_enable_seo_urls_seller == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_enable_seo_urls_product; ?></span>
							<span class="help"><?php echo $ms_config_enable_seo_urls_product_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_seo_urls_product" value="1" <?php if($msconf_enable_seo_urls_product == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_enable_seo_urls_product" value="0" <?php if($msconf_enable_seo_urls_product == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>
					
					<!--<tr>
						<td>
							<span><?php //echo $ms_config_enable_update_seo_urls; ?></span>
							<span class="help"><?php //echo $ms_config_enable_update_seo_urls_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_update_seo_urls" value="1" <?php //if($msconf_enable_update_seo_urls == 1) { ?> checked="checked" <?php //} ?>  />
							<?php //echo $text_yes; ?>
							<input type="radio" name="msconf_enable_update_seo_urls" value="0" <?php //if($msconf_enable_update_seo_urls == 0) { ?> checked="checked" <?php //} ?>  />
							<?php //echo $text_no; ?>
					  	</td>
					</tr>-->
					
					<tr>
						<td>
							<span><?php echo $ms_config_enable_non_alphanumeric_seo; ?></span>
							<span class="help"><?php echo $ms_config_enable_non_alphanumeric_seo_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_non_alphanumeric_seo" value="1" <?php if($msconf_enable_non_alphanumeric_seo == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_enable_non_alphanumeric_seo" value="0" <?php if($msconf_enable_non_alphanumeric_seo == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
					  	</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_sellers_slug; ?></span>
							<span class="help"><?php echo $ms_config_sellers_slug_note; ?></span>
						</td>
						<td>
							<input type="text" name="msconf_sellers_slug" value="<?php echo isset($msconf_sellers_slug) ? $msconf_sellers_slug : 'sellers' ; ?>" />
						</td>
					</tr>
					
					<tr><td colspan="2"><h2><?php echo $ms_config_attributes; ?></h2></td></tr>
					<tr>
						<td>
							<span><?php echo $ms_config_attribute_display; ?></span>
							<span class="help"><?php echo $ms_config_attribute_display_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_attribute_display" value="0" <?php if($msconf_attribute_display == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_attribute_display_mm; ?>
							<input type="radio" name="msconf_attribute_display" value="1" <?php if($msconf_attribute_display == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_attribute_display_oc; ?>
							<input type="radio" name="msconf_attribute_display" value="2" <?php if($msconf_attribute_display == 2) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_attribute_display_both; ?>
						</td>
					</tr>
					
					<tr><td colspan="2"><h2><?php echo $ms_config_privacy; ?></h2></td></tr>
					<tr>
						<td>
							<span><?php echo $ms_config_enable_private_messaging; ?></span>
							<span class="help"><?php echo $ms_config_enable_private_messaging_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_enable_private_messaging" value="1" <?php if($msconf_enable_private_messaging == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_enable_private_messaging" value="0" <?php if($msconf_enable_private_messaging == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
							<input type="radio" name="msconf_enable_private_messaging" value="2" <?php if($msconf_enable_private_messaging == 2) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_pm_dialog_only; ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_hide_customer_email; ?></span>
							<span class="help"><?php echo $ms_config_hide_customer_email_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_hide_customer_email" value="1" <?php if($msconf_hide_customer_email == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_hide_customer_email" value="0" <?php if($msconf_hide_customer_email == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_hide_email_in_email; ?></span>
							<span class="help"><?php echo $ms_config_hide_email_in_email_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_hide_emails_in_emails" value="1" <?php if($msconf_hide_emails_in_emails == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_hide_emails_in_emails" value="0" <?php if($msconf_hide_emails_in_emails == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>
					
					<tr><td colspan="2"><h2><?php echo $ms_config_other; ?></h2></td></tr>
					<tr>
						<td>
							<span><?php echo $ms_config_hide_sellers_product_count; ?></span>
							<span class="help"><?php echo $ms_config_hide_sellers_product_count_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_hide_sellers_product_count" value="1" <?php if($msconf_hide_sellers_product_count == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_yes; ?>
							<input type="radio" name="msconf_hide_sellers_product_count" value="0" <?php if($msconf_hide_sellers_product_count == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $text_no; ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<span><?php echo $ms_config_nickname_rules; ?></span>
							<span class="help"><?php echo $ms_config_nickname_rules_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_nickname_rules" value="0" <?php if($msconf_nickname_rules == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_nickname_rules_alnum; ?>
							<input type="radio" name="msconf_nickname_rules" value="1" <?php if($msconf_nickname_rules == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_nickname_rules_ext; ?>
							<input type="radio" name="msconf_nickname_rules" value="2" <?php if($msconf_nickname_rules == 2) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_nickname_rules_utf; ?>
					  	</td>
					</tr>
					<tr>
						<td>
							<span><?php echo $ms_config_avatars_for_sellers; ?></span>
							<span class="help"><?php echo $ms_config_avatars_for_sellers_note; ?></span>
						</td>
						<td>
							<input type="radio" name="msconf_avatars_for_sellers" value="0" <?php if($msconf_avatars_for_sellers == 0) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_avatars_manually; ?>
							<input type="radio" name="msconf_avatars_for_sellers" value="1" <?php if($msconf_avatars_for_sellers == 1) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_avatars_both; ?>
							<input type="radio" name="msconf_avatars_for_sellers" value="2" <?php if($msconf_avatars_for_sellers == 2) { ?> checked="checked" <?php } ?>  />
							<?php echo $ms_config_avatars_predefined; ?>
						</td>
					</tr>
				</table>
				</div>
				<!-- END MISCELLANEOUS TAB -->
			</form>
		</div>
	</div>
  </div>
</div>

<script>
$('#tabs a').tabs();
$('#tabs-modules a').tabs();

$('body').delegate("a.ffRemove", "click", function() {
	$(this).parents('tr').remove();
});

$('body').delegate("a.ffClone", "click", function() {
	var lastRow = $(this).parents('table').find('tbody tr:last input:last').attr('name');
	if (typeof lastRow == "undefined") {
		var newRowNum = 1;
	} else {
		var newRowNum = parseInt(lastRow.match(/[0-9]+/)) + 1;
	}
	console.log(lastRow, newRowNum);
	var newRow = $(this).parents('table').find('tbody tr.ffSample').clone();
	newRow.find('input,select').attr('name', function(i,name) {
		return name.replace('[0]','[' + newRowNum + ']');
	});
	
	$(this).parents('table').find('tbody').append(newRow.removeAttr('class'));
});

$(function() {
	$("#saveSettings").click(function() {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=module/multiseller/savesettings&token=<?php echo $token; ?>',
			data: $('#settings').serialize(),
			success: function(jsonData) {
				if (jsonData.errors) {
					$("#error").html('');
					for (error in jsonData.errors) {
						if (!jsonData.errors.hasOwnProperty(error)) {
							continue;
						}
						$("#error").append('<p>'+jsonData.errors[error]+'</p>');
					}				
				} else {
					window.location.reload();
				}
		   	}
		});
	});
});
</script>  

<?php echo $footer; ?>	
</div>