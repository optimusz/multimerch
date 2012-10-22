<div id="ms-pdfgen-dialog" title="<?php echo $ms_pdfgen_title; ?>">
	<div class="ms-form">
	<form class="dialog">
		<input type="hidden" name="ms-pdfgen-filename" value="<?php echo $fileName; ?>" />
		<label for="ms-pdfgen-pages"><?php echo $ms_pdfgen_file; ?>:</label>
		<span class="ms-filename"><?php echo $fileMask; ?></span>
		<br />
		
		<label for="ms-pdfgen-pages"><?php echo $ms_pdfgen_pages; ?></label>
		<input type="text" name="ms-pdfgen-pages" id="ms-pdfgen-pages" value="0-<?php echo $filePages; ?>"></input>
		<p class="ms-note"><?php echo $ms_pdfgen_note; ?></p>
	</form>
	</div>
</div>