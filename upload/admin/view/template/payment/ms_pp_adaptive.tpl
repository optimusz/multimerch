<?php echo $header; ?>

<style>
.ffTable .center {
	text-align: center;
}
</style>

<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="warning" style="display: none"></div>
  <div class="success" style="display: none"></div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
		<div class="buttons">
			<a class="button" id="saveSettings"><?php echo $button_save; ?></a>
			<a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a>
		</div>
    </div>
    <div class="content">
      <form id="settings">
        <table class="form">
		<tr>
			<td>
				<span><span class="required">*</span> <?php echo $ppa_receiver; ?></span>
				<span class="help"><?php echo $ppa_receiver_note; ?></span>
			</td>
			<td>
				<input type="text" name="msppaconf_receiver" value="<?php echo $msppaconf_receiver; ?>" size="30"/>
			</td>
		</tr>
		<tr>
			<td>
				<span><span class="required">*</span> <?php echo $ppa_api_username; ?></span>
				<span class="help"><?php echo $ppa_api_username_note; ?></span>
			</td>
			<td>
				<input type="text" name="msppaconf_api_username" value="<?php echo $msppaconf_api_username; ?>" size="30"/>
			</td>
		</tr>
		
		<tr>
			<td>
				<span><span class="required">*</span> <?php echo $ppa_api_password; ?></span>
				<span class="help"><?php echo $ppa_api_password_note; ?></span>
			</td>
			<td>
				<input type="text" name="msppaconf_api_password" value="<?php echo $msppaconf_api_password; ?>" size="30"/>
			</td>
		</tr>
		
		<tr>
			<td>
				<span><span class="required">*</span> <?php echo $ppa_api_signature; ?></span>
				<span class="help"><?php echo $ppa_api_signature_note; ?></span>
			</td>
			<td>
				<input type="text" name="msppaconf_api_signature" value="<?php echo $msppaconf_api_signature; ?>" size="30"/>
			</td>
		</tr>
		
		<tr>
			<td>
				<span><span class="required">*</span> <?php echo $ppa_api_appid; ?></span>
				<span class="help"><?php echo $ppa_api_appid_note; ?></span>
			</td>
			<td>
				<input type="text" name="msppaconf_api_appid" value="<?php echo $msppaconf_api_appid; ?>" size="30"/>
			</td>
		</tr>

		<tr>
			<td>
				<span><span class="required">*</span> <?php echo $ppa_secret; ?></span>
				<span class="help"><?php echo $ppa_secret_note; ?></span>
			</td>
			<td>
				<?php echo $ppa_secret_key; ?>: <input type="text" name="msppaconf_secret_key" value="<?php echo $msppaconf_secret_key; ?>" size="15"/>
				<?php echo $ppa_secret_value; ?>: <input type="text" name="msppaconf_secret_value" value="<?php echo $msppaconf_secret_value; ?>" size="15"/>
			</td>
		</tr>

		<tr>
			<td>
				<span><?php echo $ppa_payment_type; ?></span>
				<span class="help"><?php echo $ppa_payment_type_note; ?></span>
			</td>
			<td>
				<select name="msppaconf_payment_type">
					<!--<option value="SIMPLE" <?php if($msppaconf_payment_type == 'SIMPLE') { ?> selected="selected" <?php } ?>><?php echo $ppa_payment_type_simple; ?></option>-->
					<option value="PARALLEL" <?php if($msppaconf_payment_type == 'PARALLEL') { ?> selected="selected" <?php } ?>><?php echo $ppa_payment_type_parallel; ?></option>
					<option value="CHAINED" <?php if($msppaconf_payment_type == 'CHAINED') { ?> selected="selected" <?php } ?>><?php echo $ppa_payment_type_chained; ?></option>
				</select>
			</td>
		</tr>

		<tr>
			<td>
				<span><?php echo $ppa_feespayer; ?></span>
				<span class="help"><?php echo $ppa_feespayer_note; ?></span>
			</td>
			<td>
				<select name="msppaconf_feespayer">
					<option value="SENDER" <?php if($msppaconf_feespayer == 'SENDER') { ?> selected="selected" <?php } ?>><?php echo $ppa_feespayer_sender; ?></option>
					<option value="PRIMARYRECEIVER" <?php if($msppaconf_feespayer == 'PRIMARYRECEIVER') { ?> selected="selected" <?php } ?>><?php echo $ppa_feespayer_primaryreceiver; ?></option>
					<option value="EACHRECEIVER" <?php if($msppaconf_feespayer == 'EACHRECEIVER') { ?> selected="selected" <?php } ?>><?php echo $ppa_feespayer_eachreceiver; ?></option>
					<option value="SECONDARYONLY" <?php if($msppaconf_feespayer == 'SECONDARYONLY') { ?> selected="selected" <?php } ?>><?php echo $ppa_feespayer_secondaryonly; ?></option>		          
				</select>
			</td>
		</tr>

		<!--
		<tr>
			<td>
				<span><span class="required">*</span> <?php echo $ppa_receivers; ?></span>
				<span class="help"><?php echo $ppa_receivers_note; ?></span>
			</td>

			<td>
				<table class="ffTable">
					<thead class="center">
						<td><?php echo $ppa_receiver; ?></td>
						<td><?php echo $ppa_receiver_email; ?></td>
						<td><?php echo $ppa_receiver_amount; ?></td>
					</thead>
			        <?php for ($i = 0; $i < 6; $i++) {  ?> 
					<tr>
						<td class="center">
							<span><?php if ($i == 0) { ?> <span class="required">*</span><?php } ?>#<?php echo $i+1; ?></span>
							<span class="help"><?php echo $ppa_receiver_note; ?></span>
						</td>
						<td>
							<input size="30" type="text" name="msppaconf_receivers[<?php echo $i; ?>][email]" value="<?php if (isset($receivers[$i]['email']) && $receivers[$i]['email']) { echo $receivers[$i]['email']; } ?>" />
						</td>
						<td>
							<input type="text" name="msppaconf_receivers[<?php echo $i; ?>][percentage]" value="<?php if (isset($receivers[$i]['percentage']) && $receivers[$i]['percentage']) { echo $receivers[$i]['percentage']; } ?>" size="3"/>%				
						</td>
					</tr>
					<?php } ?>
				</table>
			</td>
		</tr>
		-->
		
		<tr>
			<td>
				<span><?php echo $ppa_invalid_email; ?></span>
				<span class="help"><?php echo $ppa_invalid_email_note; ?></span>
			</td>
			<td>
				<input type="radio" name="msppaconf_invalid_email" value="0" <?php if($msppaconf_invalid_email == 0) { ?> checked="checked" <?php } ?>  />
				<?php echo $ppa_disable_module; ?>
				<input type="radio" name="msppaconf_invalid_email" value="1" <?php if($msppaconf_invalid_email == 1) { ?> checked="checked" <?php } ?>  />
				<?php echo $ppa_balance_transaction; ?>
			</td>
		</tr>
		
		<!--
		<tr>
			<td>
				<span><?php echo $ppa_too_many_receivers; ?></span>
				<span class="help"><?php echo $ppa_too_many_receivers_note; ?></span>
			</td>
			<td>
				<input type="radio" name="msppaconf_too_many_receivers" value="0" <?php if($msppaconf_too_many_receivers == 0) { ?> checked="checked" <?php } ?>  />
				<?php echo $ppa_disable_module; ?>
				<input type="radio" name="msppaconf_too_many_receivers" value="1" <?php if($msppaconf_too_many_receivers == 1) { ?> checked="checked" <?php } ?>  />
				<?php echo $ppa_balance_transaction; ?>
			</td>
		</tr>		
		-->
		
		<tr>
			<td>
				<span><?php echo $ppa_sandbox; ?></span>
				<span class="help"><?php echo $ppa_sandbox_note; ?></span>
			</td>
			<td>
				<input type="radio" name="msppaconf_sandbox" value="1" <?php if($msppaconf_sandbox == 1) { ?> checked="checked" <?php } ?>  />
				<?php echo $text_yes; ?>
				<input type="radio" name="msppaconf_sandbox" value="0" <?php if($msppaconf_sandbox == 0) { ?> checked="checked" <?php } ?>  />
				<?php echo $text_no; ?>
			</td>
		</tr>

		<tr>
			<td>
				<span><?php echo $ppa_debug; ?></span>
				<span class="help"><?php echo $ppa_debug_note; ?></span>
			</td>
			<td>
                <input type="radio" name="msppaconf_debug" value="1" <?php if($msppaconf_debug == 1) { ?> checked="checked" <?php } ?>  />
                <?php echo $text_yes; ?>
                <input type="radio" name="msppaconf_debug" value="0" <?php if($msppaconf_debug == 0) { ?> checked="checked" <?php } ?>  />
                <?php echo $text_no; ?>
          	</td>
        </tr>

		<tr>
        	<td>
				<span><?php echo $ppa_total; ?></span>
				<span class="help"><?php echo $ppa_total_note; ?></span>
			</td>
			<td><input type="text" name="ms_pp_adaptive_total" value="<?php echo $ms_pp_adaptive_total; ?>" /></td>
		</tr>
		
          <tr>
            <td><?php echo $ppa_completed_status; ?></td>
            <td><select name="msppaconf_completed_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $msppaconf_completed_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          
          <tr>
            <td><?php echo $ppa_pending_status; ?></td>
            <td><select name="msppaconf_pending_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $msppaconf_pending_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>          
          
          <tr>
            <td><?php echo $ppa_error_status; ?></td>
            <td><select name="msppaconf_error_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $msppaconf_error_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          
          <tr>
            <td><?php echo $ppa_geo_zone; ?></td>
            <td><select name="ms_pp_adaptive_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $ms_pp_adaptive_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          
          <tr>
            <td><?php echo $ppa_status; ?></td>
            <td><select name="ms_pp_adaptive_status">
                <?php if ($ms_pp_adaptive_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $ppa_sort_order; ?></td>
            <td><input type="text" name="ms_pp_adaptive_sort_order" value="<?php echo $ms_pp_adaptive_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>

<script>
$(function() {
	$("#saveSettings").click(function() {
	    $.ajax({
			type: "POST",
			dataType: "json",
			url: 'index.php?route=payment/ms_pp_adaptive/savesettings&token=<?php echo $token; ?>',
			data: $('#settings').serialize(),
			success: function(jsonData) {
				$(".warning").html('').hide();
				if (jsonData.errors) {
					for (error in jsonData.errors) {
					    if (!jsonData.errors.hasOwnProperty(error)) {
					        continue;
					    }
					    $(".warning").append('<p>'+jsonData.errors[error]+'</p>').show();
					}				
				} else {
					$(".success").append('<p>'+jsonData.success+'</p>').show();
					window.location.reload();
				}
	       	}
		});
	});
});
</script>

<?php echo $footer; ?> 