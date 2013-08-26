<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>

<div id="content" class="ms-account-conversation-view">
	<?php echo $content_top; ?>
	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<h1><?php echo $conversation['title']; ?></h1>
	
	<div id="error_text" class="error"></div>
	
	<?php if (isset($success) && ($success)) { ?>
		<div class="success"><?php echo $success; ?></div>
	<?php } ?>
	
	<form id="ms-message-form" class="ms-form">
		<textarea rows="3" cols="50" name="ms-message-text" id="ms-message-text"><?php echo $ms_message_text; ?></textarea>
		
		<input type="hidden" name="conversation_id" value="<?php echo $conversation['conversation_id']; ?>" />

		<div class="right">
			<a class="button" id="ms-message-reply">
				<span><?php echo $ms_post_message; ?></span>
			</a>
		</div>
	</form>	
	
	<div class="ms-messages">
		<div class="ms-message-row ms-message-head">
			<div class="ms-message ms-message-sender">
				<?php echo $ms_sender; ?>
			</div>
			<div class="ms-message ms-message-text">
				<?php echo $ms_message; ?>
			</div>
			<div class="ms-message ms-message-date">
				<?php echo $ms_date; ?>
			</div>
		</div>
		<?php if (isset($messages)) { ?>
			<?php foreach ($messages as $message) { ?>
				<div class="ms-message-row">
					<div class="ms-message ms-message-sender">
						<?php echo ucwords($message['sender']); ?>
					</div>
					<div class="ms-message ms-message-text">
						<?php echo nl2br($message['message']); ?>
					</div>
					<div class="ms-message ms-message-date">
						<?php echo $message['date_created']; ?>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
	
	<?php echo $content_bottom; ?>
</div>

<?php echo $footer; ?>