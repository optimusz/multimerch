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