<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content"><?php echo $content_top; ?>

  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  
  <h1><?php echo $ms_account_withdraw_heading; ?></h1>
  
  <?php if (isset($error_warning) && ($error_warning)) { ?>
  	<div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php echo $ms_account_withdraw_balance; ?> <b><?php echo $balance; ?></b>
  
    <div class="buttons">
    	<div class="right"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_continue; ?></span></a></div>
    </div>
  
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>