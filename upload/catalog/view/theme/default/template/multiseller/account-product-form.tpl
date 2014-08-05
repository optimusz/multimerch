<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-account-product-form">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<h1><?php echo $heading; ?></h1>
	
	<p class="warning main"></p>
	<form id="ms-new-product" method="post" enctype="multipart/form-data">
		<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
		<input type="hidden" name="action" id="ms_action" />
		
		<input type="hidden" name="list_until" value="<?php echo $list_until; ?>" />
		
		<div class="content">
     	<div id="general-tabs" class="htabs">
     		<a href="#tab-general"><?php echo $ms_account_product_tab_general; ?></a>
			
			<?php
			$data_tab_fields = array('model', 'sku', 'upc', 'ean', 'jan', 'isbn', 'mpn', 'manufacturer', 'taxClass', 'subtract', 'stockStatus', 'dateAvailable');
			$intersection_fields = array_intersect($data_tab_fields, $this->config->get('msconf_product_included_fields'));
			?>
     		<?php if (!empty($intersection_fields)) { ?>
     		<a href="#tab-data"><?php echo $ms_account_product_tab_data; ?></a>
     		<?php } ?>

     		<a href="#tab-options"><?php echo $ms_account_product_tab_options; ?></a>
     		
            <?php if ($this->config->get('msconf_allow_specials')) { ?>
     		<a href="#tab-specials"><?php echo $ms_account_product_tab_specials; ?></a>
     		<?php } ?>
     		
     		<?php if ($this->config->get('msconf_allow_discounts')) { ?>
     		<a href="#tab-discounts"><?php echo $ms_account_product_tab_discounts; ?></a>
     		<?php } ?>
     	</div>
     	
     	<!-- general tab -->
     	<div id="tab-general">
     		<?php if (count($languages) > 1) { ?>
			<div class="htabs" id="language-tabs">
				<?php foreach ($languages as $language) { ?>
				<a class="lang" href="#language<?php echo $language['language_id']; ?>"><img src="image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
				<?php } ?>
			</div>
			<?php } ?>
			
			<?php
			reset($languages); $first = key($languages);
			foreach ($languages as $k => $language) {
				$langId = $language['language_id'];
				?>
				
				<div class="ms-language-div" id="language<?php echo $langId; ?>">
				<table class="ms-product">
					<tr><td colspan="2"><h3><?php echo $ms_account_product_name_description; ?></h3></td></tr>
					
					<tr>
						<td><span class="required"><?php if ($k == $first) { echo '*'; } ?></span> <?php echo $ms_account_product_name; ?></td>
						<td>
							<input type="text" name="languages[<?php echo $langId; ?>][product_name]" value="<?php echo $product['languages'][$langId]['name']; ?>" />
							<p class="ms-note"><?php echo $ms_account_product_name_note; ?></p>
							<p class="error" id="error_product_name_<?php echo $langId; ?>"></p>
						</td>
					</tr>
					
					<tr>
						<td><span class="required"><?php if ($k == $first) { echo '*'; } ?></span> <?php echo $ms_account_product_description; ?></td>
						<td>
							<!-- todo strip tags if rte disabled -->
							<textarea name="languages[<?php echo $langId; ?>][product_description]" class="<?php echo $this->config->get('msconf_enable_rte') ? "ckeditor" : ''; ?>"><?php echo $this->config->get('msconf_enable_rte') ? htmlspecialchars_decode($product['languages'][$langId]['description']) : strip_tags(htmlspecialchars_decode($product['languages'][$langId]['description'])); ?></textarea>
							<p class="ms-note"><?php echo $ms_account_product_description_note; ?></p>
							<p class="error" id="error_product_description_<?php echo $langId; ?>"></p>
						</td>
					</tr>

                    <?php if (in_array('metaDescription', $this->config->get('msconf_product_included_fields'))) { ?>
					<tr>
						<td><?php echo $ms_account_product_meta_description; ?></td>
						<td>
							<!-- todo strip tags if rte disabled -->
							<textarea name="languages[<?php echo $langId; ?>][product_meta_description]"><?php echo strip_tags(htmlspecialchars_decode($product['languages'][$langId]['meta_description'])); ?></textarea>
							<p class="ms-note"><?php echo $ms_account_product_meta_description_note; ?></p>
							<p class="error" id="error_product_meta_description_<?php echo $langId; ?>"></p>
						</td>
					</tr>
                    <?php } ?>

                    <?php if (in_array('metaKeywords', $this->config->get('msconf_product_included_fields'))) { ?>
					<tr>
						<td><?php echo $ms_account_product_meta_keyword; ?></td>
						<td>
							<!-- todo strip tags if rte disabled -->
							<textarea name="languages[<?php echo $langId; ?>][product_meta_keyword]"><?php echo strip_tags(htmlspecialchars_decode($product['languages'][$langId]['meta_keyword'])); ?></textarea>
							<p class="ms-note"><?php echo $ms_account_product_meta_keyword_note; ?></p>
							<p class="error" id="error_product_meta_keyword_<?php echo $langId; ?>"></p>
						</td>
					</tr>
                    <?php } ?>

					<tr>
						<td><?php echo $ms_account_product_tags; ?></td>
						<td>
							<input type="text" name="languages[<?php echo $langId; ?>][product_tags]" value="<?php echo $product['languages'][$langId]['tags']; ?>" />
							<p class="ms-note"><?php echo $ms_account_product_tags_note; ?></p>
							<p class="error" id="error_product_tags_<?php echo $langId; ?>"></p>
						</td>
					</tr>

					<?php if (isset($multilang_attributes) && !empty($multilang_attributes)) { ?>
					<?php foreach ($multilang_attributes as &$attr) { ?>
					<tr>
						<td><?php if ($attr['required'] && $k == $first) { ?><span class="required">*</span> <?php } ?><?php echo $attr['mad.name']; ?></td>
						<td>
							<?php if ($attr['attribute_type'] == MsAttribute::TYPE_TEXT) { ?>
								<input type="text" name="languages[<?php echo $langId; ?>][product_attributes][<?php echo $attr['attribute_id']; ?>][value]" value="<?php echo isset($multilang_attribute_values[$attr['attribute_id']][$langId]) ? $multilang_attribute_values[$attr['attribute_id']][$langId]['value'] : '' ?>" />
								<input type="hidden" name="languages[<?php echo $langId; ?>][product_attributes][<?php echo $attr['attribute_id']; ?>][value_id]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo $multilang_attribute_values[$attr['attribute_id']][$langId]['value_id']; } ?>" />
							<?php } ?>
							
							<?php if ($attr['attribute_type'] == MsAttribute::TYPE_TEXTAREA) { ?>
								<textarea name="languages[<?php echo $langId; ?>][product_attributes][<?php echo $attr['attribute_id']; ?>][value]"><?php echo isset($multilang_attribute_values[$attr['attribute_id']][$langId]) ? $multilang_attribute_values[$attr['attribute_id']][$langId]['value'] : '' ?></textarea>
								<input type="hidden" name="languages[<?php echo $langId; ?>][product_attributes][<?php echo $attr['attribute_id']; ?>][value_id]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo $multilang_attribute_values[$attr['attribute_id']][$langId]['value_id']; } ?>" />
							<?php } ?>
							<p class="ms-note"><?php echo $attr['description']; ?></p>
							<p class="error"></p>
						</td>
					</tr>
					<?php } ?>
					<?php } ?>
				</table>
				</div>
			<?php } ?>
			
			
			<table class="ms-product">
				<tr><td colspan="2"><h3><?php echo $ms_account_product_price_attributes; ?></h3></td></tr>
				
				<tr>
					<td><span class="required">*</span> <?php echo $ms_account_product_price; ?></td>
					<td>
						<span class="vertical-align: auto"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
						<input type="text" name="product_price" value="<?php echo $product['price']; ?>" <?php if (isset($seller['commissions']) && $seller['commissions'][MsCommission::RATE_LISTING]['percent'] > 0) { ?>class="ms-price-dynamic"<?php } ?> />
						<span class="vertical-align: auto"><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
						<p class="ms-note"><?php echo $ms_account_product_price_note; ?></p>
						<p class="error" id="error_product_price"></p>
					</td>
				</tr>
				
				<tr>
					<td><span class="required">*</span> <?php echo $ms_account_product_category; ?></td>
					
					<td id="product_category_block">
						<?php if (!$msconf_allow_multiple_categories) { ?>

						<select name="product_category">
							<option value=""><?php echo ''; ?></option>
							<?php foreach ($categories as $category) { ?>
                                <?php if($msconf_enable_categories && $msconf_enable_shipping == 2) { ?>
                                    <?php if($product['shipping'] == 1 || $product['shipping'] == NULL) { ?>
                                        <?php if(in_array($category['category_id'],$msconf_physical_product_categories)) { ?>
                                            <option value="<?php echo $category['category_id']; ?>" <?php if (in_array($category['category_id'], explode(',',$product['category_id'])) && !$category['disabled']) { ?>selected="selected"<?php } ?> <?php echo ($category['disabled'] ? 'disabled' : ''); ?>><?php echo $category['name']; ?></option>
                                    <?php }} else { ?>
                                        <?php if(in_array($category['category_id'],$msconf_digital_product_categories)) { ?>
                                            <option value="<?php echo $category['category_id']; ?>" <?php if (in_array($category['category_id'], explode(',',$product['category_id'])) && !$category['disabled']) { ?>selected="selected"<?php } ?> <?php echo ($category['disabled'] ? 'disabled' : ''); ?>><?php echo $category['name']; ?></option>
                                    <?php }} ?>
                                <?php } else { ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php if (in_array($category['category_id'], explode(',',$product['category_id'])) && !$category['disabled']) { ?>selected="selected"<?php } ?> <?php echo ($category['disabled'] ? 'disabled' : ''); ?>><?php echo $category['name']; ?></option>
							<?php }} ?>
						</select>
						
						<?php } else { ?>
						
						<div class="scrollbox">
						<?php $class = 'odd'; ?>
						<?php foreach ($categories as $category) { ?>
                            <?php if($msconf_enable_categories && $msconf_enable_shipping == 2) { ?>
                                <?php if($product['shipping'] == 1 || $product['shipping'] == NULL) { ?>
                                    <?php if(in_array($category['category_id'],$msconf_physical_product_categories)) { ?>
                                        <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                        <div class="<?php echo $class; ?> <?php echo ($category['disabled'] ? 'disabled' : ''); ?>">
                                            <input type="checkbox" name="product_category[]" value="<?php echo $category['category_id']; ?>" <?php if (in_array($category['category_id'], explode(',',$product['category_id'])) && !$category['disabled']) { ?>checked="checked"<?php } ?> <?php if ($category['disabled']) { ?>disabled="disabled"<?php } ?>/>
                                            <?php echo $category['name']; ?>
                                        </div>
                                <?php }} else { ?>
                                     <?php if(in_array($category['category_id'],$msconf_digital_product_categories)) { ?>
                                        <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                        <div class="<?php echo $class; ?> <?php echo ($category['disabled'] ? 'disabled' : ''); ?>">
                                            <input type="checkbox" name="product_category[]" value="<?php echo $category['category_id']; ?>" <?php if (in_array($category['category_id'], explode(',',$product['category_id'])) && !$category['disabled']) { ?>checked="checked"<?php } ?> <?php if ($category['disabled']) { ?>disabled="disabled"<?php } ?>/>
                                            <?php echo $category['name']; ?>
                                        </div>
                                <?php }} ?>
                            <?php } else { ?>
                                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                <div class="<?php echo $class; ?> <?php echo ($category['disabled'] ? 'disabled' : ''); ?>">
                                    <input type="checkbox" name="product_category[]" value="<?php echo $category['category_id']; ?>" <?php if (in_array($category['category_id'], explode(',',$product['category_id'])) && !$category['disabled']) { ?>checked="checked"<?php } ?> <?php if ($category['disabled']) { ?>disabled="disabled"<?php } ?>/>
                                    <?php echo $category['name']; ?>
                                </div>
                            <?php }} ?>
						</div>
						
						<?php } ?>
						
						<p class="ms-note"><?php echo $ms_account_product_category_note; ?></p>
						<p class="error" id="error_product_category"></p>
					</td>
				</tr>
				
				<?php if ($msconf_enable_shipping == 2) { ?>
				<tr>
					<td><?php echo $ms_account_product_enable_shipping; ?></td>
					<td>
						<input type="radio" name="product_enable_shipping" value="1" <?php if($product['shipping'] == 1) { ?> checked="checked" <?php } ?>/>
						<?php echo $text_yes; ?>
						<input type="radio" name="product_enable_shipping" value="0" <?php if($product['shipping'] == 0) { ?> checked="checked" <?php } ?>/>
						<?php echo $text_no; ?>
						<p class="ms-note"><?php echo $ms_account_product_enable_shipping_note; ?></p>
						<p class="error" id="error_product_enable_shipping"></p>
					</td>
				</tr>
				<?php } ?>
				
				<tr <?php if ($msconf_enable_quantities == 0 || ($msconf_enable_shipping != 1 && $msconf_enable_quantities == 2 && isset($product['shipping']) && $product['shipping'] == 0) || (isset($seller_group['product_quantity']) && $seller_group['product_quantity'] != 0)) { ?>style="display: none"<?php } ?>>
					<td><?php echo $ms_account_product_quantity; ?></td>
					<td>
						<input type="text" name="product_quantity" value="<?php echo $product['quantity']; ?>" <?php if ($msconf_enable_quantities < 2 || (isset($seller_group['product_quantity']) && $seller_group['product_quantity'] != 0)) { ?>class="ffUnchangeable"<?php } ?> />
						<p class="ms-note"><?php echo $ms_account_product_quantity_note; ?></p>
						<p class="error" id="error_product_quantity"></p>
					</td>
				</tr> 
				
				
				
				<?php if (isset($normal_attributes) && !empty($normal_attributes)) { ?>
				<?php foreach ($normal_attributes as $attr) { ?>
				<tr>
					<td>
						<?php if ($attr['required']) { ?> <span class="required">*</span> <?php } ?><?php echo $attr['name']; ?>
					</td>
					
					<td>
						<?php if ($attr['attribute_type'] == MsAttribute::TYPE_SELECT) { ?>
							<select name="product_attributes[<?php echo $attr['attribute_id']; ?>]">
							<option value=""><?php echo $text_select; ?></option>
							<?php foreach ($attr['values'] as $attr_value) { ?>
							<option value="<?php echo $attr_value['attribute_value_id']; ?>" <?php if (isset($normal_attribute_values[$attr['attribute_id']]) && array_key_exists($attr_value['attribute_value_id'], $normal_attribute_values[$attr['attribute_id']])) { ?>selected="selected"<?php } ?>><?php echo $attr_value['name']; ?></option>
							<?php } ?>
							</select>
						<?php } ?>
						
						<?php if ($attr['attribute_type'] == MsAttribute::TYPE_RADIO) { ?>
						<?php foreach ($attr['values'] as $attr_value) { ?>
							<input type="radio" name="product_attributes[<?php echo $attr['attribute_id']; ?>]" value="<?php echo $attr_value['attribute_value_id']; ?>" <?php if (isset($normal_attribute_values[$attr['attribute_id']]) && array_key_exists($attr_value['attribute_value_id'], $normal_attribute_values[$attr['attribute_id']])) { ?>checked="checked"<?php } ?> />
							<label><?php echo $attr_value['name']; ?></label>
							<br />
						<?php } ?>
						<?php } ?>

						<?php if ($attr['attribute_type'] == MsAttribute::TYPE_IMAGE) { ?>
						<?php foreach ($attr['values'] as $attr_value) { ?>
							<input type="radio" name="product_attributes[<?php echo $attr['attribute_id']; ?>]" value="<?php echo $attr_value['attribute_value_id']; ?>" <?php if (isset($normal_attribute_values[$attr['attribute_id']]) && array_key_exists($attr_value['attribute_value_id'], $normal_attribute_values[$attr['attribute_id']])) { ?>checked="checked"<?php } ?> style="vertical-align: middle"/>
							<label><?php echo $attr_value['name']; ?></label>
							<img src="<?php echo $attr_value['image']; ?>" style="vertical-align: middle; padding: 1px; border: 1px solid #DDDDDD; margin-bottom: 10px" />
							<br />
						<?php } ?>
						<?php } ?>
						
						<?php if ($attr['attribute_type'] == MsAttribute::TYPE_CHECKBOX) { ?>
						<?php foreach ($attr['values'] as $attr_value) { ?>
							<input type="checkbox" name="product_attributes[<?php echo $attr['attribute_id']; ?>][]" value="<?php echo $attr_value['attribute_value_id']; ?>" <?php if (isset($normal_attribute_values[$attr['attribute_id']]) && array_key_exists($attr_value['attribute_value_id'], $normal_attribute_values[$attr['attribute_id']])) { ?>checked="checked"<?php } ?> />
							<label><?php echo $attr_value['name']; ?></label>
							<br />
						<?php } ?>
						<?php } ?>
						
						<?php if ($attr['attribute_type'] == MsAttribute::TYPE_TEXT) { ?>
							<input type="text" name="product_attributes[<?php echo $attr['attribute_id']; ?>][value]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo current(reset($normal_attribute_values[$attr['attribute_id']])); } ?>" />
							<input type="hidden" name="product_attributes[<?php echo $attr['attribute_id']; ?>][value_id]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo key($normal_attribute_values[$attr['attribute_id']]); } ?>" />
						<?php } ?>
						
						<?php if ($attr['attribute_type'] == MsAttribute::TYPE_TEXTAREA) { ?>
							<textarea name="product_attributes[<?php echo $attr['attribute_id']; ?>][value]" cols="40" rows="5"><?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo current(reset($normal_attribute_values[$attr['attribute_id']])); } ?></textarea>
							<input type="hidden" name="product_attributes[<?php echo $attr['attribute_id']; ?>][value_id]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo key($normal_attribute_values[$attr['attribute_id']]); } ?>" />
						<?php } ?>
						
						<?php if ($attr['attribute_type'] == MsAttribute::TYPE_DATE) { ?>
							<input type="text" name="product_attributes[<?php echo $attr['attribute_id']; ?>][value]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo current(reset($normal_attribute_values[$attr['attribute_id']])); } ?>" class="date" />
							<input type="hidden" name="product_attributes[<?php echo $attr['attribute_id']; ?>][value_id]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo key($normal_attribute_values[$attr['attribute_id']]); } ?>" />							
						<?php } ?>
						
						<?php if ($attr['attribute_type'] == MsAttribute::TYPE_DATETIME) { ?>
							<input type="text" name="product_attributes[<?php echo $attr['attribute_id']; ?>][value]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo current(reset($normal_attribute_values[$attr['attribute_id']])); } ?>" class="datetime" />
							<input type="hidden" name="product_attributes[<?php echo $attr['attribute_id']; ?>][value_id]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo key($normal_attribute_values[$attr['attribute_id']]); } ?>" />
						<?php } ?>
						
						<?php if ($attr['attribute_type'] == MsAttribute::TYPE_TIME) { ?>
							<input type="text" name="product_attributes[<?php echo $attr['attribute_id']; ?>][value]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo current(reset($normal_attribute_values[$attr['attribute_id']])); } ?>" class="time" />
							<input type="hidden" name="product_attributes[<?php echo $attr['attribute_id']; ?>][value_id]" value="<?php if (isset($normal_attribute_values[$attr['attribute_id']])) { echo key($normal_attribute_values[$attr['attribute_id']]); } ?>" />
						<?php } ?>
						
						<p class="ms-note"><?php echo $attr['description']; ?></p>
						<p class="error"></p>
					</td>
				</tr>
				<?php } ?>
				<?php } ?>
				
				
				<tr><td colspan="2"><h3><?php echo $ms_account_product_files; ?></h3></td></tr>
					
				<tr>
					<td><?php if ($msconf_images_limits[0] > 0) { ?><span class="required">*</span><?php } ?> <?php echo $ms_account_product_image; ?></td>
					<td>
						<!--<input type="file" name="ms-file-addimages" id="ms-file-addimages" />-->
						<a name="ms-file-addimages" id="ms-file-addimages" class="button"><span><?php echo $ms_button_select_images; ?></span></a>
						<p class="ms-note"><?php echo $ms_account_product_image_note; ?></p>
						<div class="error" id="error_product_image"></div>
						<div class="image progress"></div>
						<div class="product_image_files">
						<?php if (isset($product['images'])) { ?>
						<?php $i = 0; ?> 	
						<?php foreach ($product['images'] as $image) { ?>
							<div class="ms-image">
								<input type="hidden" name="product_images[]" value="<?php echo $image['name']; ?>" />
								<img src="<?php echo $image['thumb']; ?>" />
								<img class="ms-remove" src="catalog/view/theme/default/image/remove.png" />
							</div>
						<?php $i++; ?>		
						<?php } ?>
						<?php } ?>
						</div>
					</td>
				</tr>
				
				<tr>
					<td><?php if ($msconf_downloads_limits[0] > 0) { ?><span class="required">*</span><?php } ?> <?php echo $ms_account_product_download; ?></td>
					<td>
						<!--<input type="file" name="ms-file-addfiles" id="ms-file-addfiles" />-->
						<a name="ms-file-addfiles" id="ms-file-addfiles" class="button"><span><?php echo $ms_button_select_files; ?></span></a>
						<p class="ms-note"><?php echo $ms_account_product_download_note; ?></p>
						<div class="error" id="error_product_download"></div>
						<div class="download progress"></div>
						<div class="product_download_files">
						<?php if (isset($product['downloads'])) { ?>
						<?php $i = 0; ?> 
						<?php foreach ($product['downloads'] as $download) { ?>
							<div class="ms-download">
								<input type="hidden" name="product_downloads[<?php echo $i; ?>][download_id]" value="<?php echo isset($clone) ? '' : $download['id']; ?>" />
								<input type="hidden" name="product_downloads[<?php echo $i; ?>][filename]" value="<?php echo (isset($clone)) ? $download['src'] : ''; ?>" />
								<span class="ms-download-name"><?php echo $download['name']; ?></span>
								<div class="ms-buttons">
									<a href="<?php echo $download['href']; ?>" class="ms-button-download" title="<?php echo $ms_download; ?>"></a>
										<!--<input id="ms-update-<?php echo $download['id']; ?>" name="ms-update-<?php echo $download['id']; ?>" class="ms-file-updatedownload" type="file" multiple="false" />-->
									<a id="ms-update-<?php echo $download['id']; ?>" name="ms-update-<?php echo $download['id']; ?>" class="ms-file-updatedownload ms-button-update" title="<?php echo $ms_update; ?>"></a>
									<a class="ms-button-delete" title="<?php echo $ms_delete; ?>"></a>
								</div>
							</div>
						<?php $i++; ?>
						<?php } ?>
						<?php } ?>
						</div>
						
						<div style="display: none">
							<input type="checkbox" name="push_downloads" id="push_downloads" />
							<label><?php echo $ms_account_product_push; ?></label>
							<p class="ms-note"><?php echo $ms_account_product_push_note; ?></p>
						</div>
					</td>
				</tr>
				
				<?php if ($seller['ms.product_validation'] == MsProduct::MS_PRODUCT_VALIDATION_APPROVAL) { ?>
				<tr><td colspan="2"><h3><?php echo $ms_account_product_message_reviewer; ?></h3></td></tr>
				
				<tr>
					<td><?php echo $ms_account_product_message; ?></td>
					<td>
						<textarea name="product_message"></textarea>
						<p class="ms-note"><?php echo $ms_account_product_message_note; ?></p>
						<p class="error" id="error_product_message"></p>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>

        <!-- data tab -->
        <div id="tab-data">
            <table class="ms-product">
                <?php if (in_array('model', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><span class="required">*</span> <?php echo $ms_account_product_model; ?></td>
                    <td>
                        <input type="text" name="product_model" value="<?php echo $product['model']; ?>" />
                        <p class="error" id="error_product_model; ?>"></p>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('sku', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_sku; ?></td>
                    <td>
                        <input type="text" name="product_sku" value="<?php echo $product['sku']; ?>" />
                        <p class="ms-note"><?php echo $ms_account_product_sku_note; ?></p>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('upc', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_upc; ?></td>
                    <td>
                        <input type="text" name="product_upc" value="<?php echo $product['upc']; ?>" />
                        <p class="ms-note"><?php echo $ms_account_product_upc_note; ?></p>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('ean', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_ean; ?></td>
                    <td>
                        <input type="text" name="product_ean" value="<?php echo $product['ean']; ?>" />
                        <p class="ms-note"><?php echo $ms_account_product_ean_note; ?></p>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('jan', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_jan; ?></td>
                    <td>
                        <input type="text" name="product_jan" value="<?php echo $product['jan']; ?>" />
                        <p class="ms-note"><?php echo $ms_account_product_jan_note; ?></p>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('isbn', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_isbn; ?></td>
                    <td>
                        <input type="text" name="product_isbn" value="<?php echo $product['isbn']; ?>" />
                        <p class="ms-note"><?php echo $ms_account_product_isbn_note; ?></p>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('mpn', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_mpn; ?></td>
                    <td>
                        <input type="text" name="product_mpn" value="<?php echo $product['mpn']; ?>" />
                        <p class="ms-note"><?php echo $ms_account_product_mpn_note; ?></p>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('manufacturer', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_manufacturer; ?></td>
                    <td>
                        <input type="text" name="product_manufacturer" value="<?php echo $product['manufacturer'] ?>" />
                        <input type="hidden" name="product_manufacturer_id" value="<?php echo $product['manufacturer_id']; ?>" />
                        <p class="ms-note"><?php echo $ms_account_product_manufacturer_note; ?></p>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('taxClass', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_tax_class; ?></td>
                    <td>
                        <select name="product_tax_class_id">
                            <option value="0"><?php echo $text_none; ?></option>
                            <?php foreach ($tax_classes as $tax_class) { ?>
                            <?php if ($tax_class['tax_class_id'] == $product['tax_class_id']) { ?>
                            <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('subtract', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_subtract; ?></td>
                    <td>
                        <select name="product_subtract">
                            <?php if ($product['subtract']) { ?>
                            <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                            <option value="0"><?php echo $text_no; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_yes; ?></option>
                            <option value="0" selected="selected"><?php echo $text_no; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('stockStatus', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_stock_status; ?></td>
                    <td>
                        <select name="product_stock_status_id">
                            <?php foreach ($stock_statuses as $stock_status) { ?>
                            <?php if ($stock_status['stock_status_id'] == $product['stock_status_id']) { ?>
                            <option value="<?php echo $stock_status['stock_status_id']; ?>" selected="selected"><?php echo $stock_status['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $stock_status['stock_status_id']; ?>"><?php echo $stock_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } ?>
                <?php if (in_array('dateAvailable', $this->config->get('msconf_product_included_fields'))) { ?>
                <tr>
                    <td><?php echo $ms_account_product_date_available; ?></td>
                    <td><input type="text" name="product_date_available" value="<?php echo $date_available; ?>" size="12" class="date" /></td>
                </tr>
                <?php } ?>
            </table>
        </div>

		<!-- options tab -->
		<div id="tab-options"></div>

		<!-- specials tab -->
		<?php if ($this->config->get('msconf_allow_specials')) { ?>
		<div id="tab-specials">
			<h3><?php echo $ms_account_product_tab_specials; ?></h3>
			<p class="error" id="error_specials"></p>
			
			<table class="list">
				<thead>
				<tr>
					<td><span class="required">*</span><?php echo $ms_account_product_priority; ?></td>
					<td><span class="required">*</span><?php echo $ms_account_product_price; ?></td>
					<td><span class="required">*</span><?php echo $ms_account_product_date_start; ?></td>
					<td><span class="required">*</span><?php echo $ms_account_product_date_end; ?></td>
					<td></td>
				</tr>
				</thead>
				
				<tbody>				
				
				<!-- sample row -->
				<tr class="ffSample">				
					<td><input type="text" name="product_specials[0][priority]" value="" size="2" /></td>
					<td><input type="text" name="product_specials[0][price]" value="" /></td>
					<td><input type="text" name="product_specials[0][date_start]" value="" class="date" /></td>
					<td><input type="text" name="product_specials[0][date_end]" value="" class="date" /></td>
					<td><a class="ms-button-delete" title="<?php echo $ms_delete; ?>"></a></td>
				</tr>
				
				<?php if (isset($product['specials'])) { ?>
				<?php $special_row = 1; ?>
				<?php foreach ($product['specials'] as $product_special) { ?>
				<tr>
					<td><input type="text" name="product_specials[<?php echo $special_row; ?>][priority]" value="<?php echo $product_special['priority']; ?>" size="2" /></td>
					<td><input type="text" name="product_specials[<?php echo $special_row; ?>][price]" value="<?php echo $this->MsLoader->MsHelper->uniformDecimalPoint($product_special['price']); ?>" /></td>
					<td><input type="text" name="product_specials[<?php echo $special_row; ?>][date_start]" value="<?php echo $product_special['date_start']; ?>" class="date" /></td>
					<td><input type="text" name="product_specials[<?php echo $special_row; ?>][date_end]" value="<?php echo $product_special['date_end']; ?>" class="date" /></td>
					<td><a class="ms-button-delete" title="<?php echo $ms_delete; ?>"></a></td>
				</tr>
				<?php $special_row++; ?>
				<?php } ?>
				<?php } ?>
				</tbody>

				<tfoot>
				<tr>
				<td colspan="5"><a class="button ffClone"><?php echo $ms_button_add_special; ?></a></td>
				</tr>
				</tfoot>
			</table>
		</div>
		<?php } ?>
		
		<!-- Quantity Discounts tab -->
		<?php if ($this->config->get('msconf_allow_discounts')) { ?>
		<div id="tab-discounts">
			<h3><?php echo $ms_account_product_tab_discounts; ?></h3>
			<p class="error" id="error_quantity_discounts"></p>
			
			<table class="list">
				<thead>
				<tr>
					<td><span class="required">*</span><?php echo $ms_account_product_priority; ?></td>
					<td><span class="required">*</span><?php echo $ms_account_product_quantity; ?></td>
					<td><span class="required">*</span><?php echo $ms_account_product_price; ?></td>
					<td><span class="required">*</span><?php echo $ms_account_product_date_start; ?></td>
					<td><span class="required">*</span><?php echo $ms_account_product_date_end; ?></td>
					<td></td>
				</tr>
				</thead>
				
				<tbody>				
				
				<!-- sample row -->
				<tr class="ffSample">				
					<td><input type="text" name="product_discounts[0][priority]" value="" size="2" /></td>
					<td><input type="text" name="product_discounts[0][quantity]" value="" size="2" /></td>
					<td><input type="text" name="product_discounts[0][price]" value="" /></td>
					<td><input type="text" name="product_discounts[0][date_start]" value="" class="date" /></td>
					<td><input type="text" name="product_discounts[0][date_end]" value="" class="date" /></td>
					<td><a class="ms-button-delete" title="<?php echo $ms_delete; ?>"></a></td>
				</tr>
				
				<?php if (isset($product['discounts'])) { ?>
				<?php $discount_row = 1; ?>
				<?php foreach ($product['discounts'] as $product_discount) { ?>
				<tr>
					<td><input type="text" name="product_discounts[<?php echo $discount_row; ?>][priority]" value="<?php echo $product_discount['priority']; ?>" size="2" /></td>
					<td><input type="text" name="product_discounts[<?php echo $discount_row; ?>][quantity]" value="<?php echo $product_discount['quantity']; ?>" size="2" /></td>
					<td><input type="text" name="product_discounts[<?php echo $discount_row; ?>][price]" value="<?php echo $this->MsLoader->MsHelper->uniformDecimalPoint($product_discount['price']); ?>" /></td>
					<td><input type="text" name="product_discounts[<?php echo $discount_row; ?>][date_start]" value="<?php echo $product_discount['date_start']; ?>" class="date" /></td>
					<td><input type="text" name="product_discounts[<?php echo $discount_row; ?>][date_end]" value="<?php echo $product_discount['date_end']; ?>" class="date" /></td>
					<td><a class="ms-button-delete" title="<?php echo $ms_delete; ?>"></a></td>
				</tr>
				<?php $discount_row++; ?>
				<?php } ?>
				<?php } ?>
				</tbody>

				<tfoot>
				<tr>
					<td colspan="6"><a class="button ffClone"><?php echo $ms_button_add_discount; ?></a></td>
				</tr>
				</tfoot>
			</table>
		</div>		
		<?php } ?>
		</div>
		</form>
		
		<?php if (isset($seller['commissions']) && ($seller['commissions'][MsCommission::RATE_LISTING]['percent'] > 0 || $seller['commissions'][MsCommission::RATE_LISTING]['flat'] > 0)) { ?>
			<?php if ($seller['commissions'][MsCommission::RATE_LISTING]['percent'] > 0) { ?>
			<p class="attention ms-commission">
				<?php echo sprintf($this->language->get('ms_account_product_listing_percent'),$this->currency->format($seller['commissions'][MsCommission::RATE_LISTING]['flat'], $this->config->get('config_currency'))); ?>
				<?php echo $ms_commission_payment_type; ?>
			</p>
			<?php } else if ($seller['commissions'][MsCommission::RATE_LISTING]['flat'] > 0) { ?>
			<p class="attention ms-commission">
				<?php echo sprintf($this->language->get('ms_account_product_listing_flat'),$this->currency->format($seller['commissions'][MsCommission::RATE_LISTING]['flat'], $this->config->get('config_currency'))); ?>
				<?php echo $ms_commission_payment_type; ?>
			</p>
			<?php } ?>
			
			<?php if(isset($payment_form)) { ?><div class="ms-payment-form"><?php echo $payment_form; ?></div><?php } ?>
		<?php } ?>
		
		<?php if (isset($list_until) && $list_until != NULL) { ?>
			<p class="attention">
				<?php echo sprintf($this->language->get('ms_account_product_listing_until'), date($this->language->get('date_format_short'), strtotime($list_until))); ?>
			</p>
		<?php } ?>
		
		<div class="buttons">
			<div class="left">
				<a href="<?php echo $back; ?>" class="button">
					<span><?php echo $ms_button_cancel; ?></span>
				</a>
			</div>
			<div class="right">
				<a class="button" id="ms-submit-button">
					<span><?php echo $ms_button_submit; ?></span>
				</a>
			</div>
		</div>
	
	<?php echo $content_bottom; ?>
</div>

<?php $timestamp = time(); ?>
<script>
	var msGlobals = {
		timestamp: '<?php echo $timestamp; ?>',
		token : '<?php echo md5($salt . $timestamp); ?>',
		session_id: '<?php echo session_id(); ?>',
		product_id: '<?php echo $product['product_id']; ?>',
		button_generate: '<?php echo htmlspecialchars($ms_button_generate, ENT_QUOTES, "UTF-8"); ?>',
		text_delete: '<?php echo htmlspecialchars($ms_delete, ENT_QUOTES, "UTF-8"); ?>',
		uploadError: '<?php echo htmlspecialchars($ms_error_file_upload_error, ENT_QUOTES, "UTF-8"); ?>',
		formError: '<?php echo htmlspecialchars($ms_error_form_submit_error, ENT_QUOTES, "UTF-8"); ?>',
		formNotice: '<?php echo htmlspecialchars($ms_error_form_notice, ENT_QUOTES, "UTF-8"); ?>',
		config_enable_rte: '<?php echo $this->config->get('msconf_enable_rte'); ?>'
	};
</script>
<?php echo $footer; ?>