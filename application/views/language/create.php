<ul class="form">
<?php
if ($action == "edit")
echo form_open_multipart(base_url() . 'language/create/edit/' . $language_id);
else
echo form_open_multipart(base_url() . 'language/create/');
?>
<?php
if ($action == "edit") {
	$language_id = $language_details[0]->language_id;
	$language = $language_details[0]->language;
	$language_code = $language_details[0]->language_code;
	$flag = $language_details[0]->flag;
} else {
	$language_id = 0;
	$language = "";
	$language_code = "";
	$flag = "";
}
?>
	<input type="hidden" id="language_id" name="language_id"
		value="<?php echo $language_id; ?>" />
	<li>Languages * <br /> <?php
	if ($action == "edit") {
		echo $language;
		?> <input type="hidden" id="language_code" name="language_code"
		value="<?php echo $language_code; ?>" /> <?php
	} else {
		?> <select name="language_code" id="language_code">
			<option>Select</option>
			<?php
			foreach ($all_languages as $language_key_code => $language_value) {
				$sel = "";
				if (!in_array($language_key_code, $language_code_array)) {
					if ($language_key_code == $language_code)
					$sel = "selected";
					?>
			<option value="<?php echo $language_key_code; ?>" <?php echo $sel; ?>>
			<?php echo $language_value; ?>
			</option>
			<?php
				}
			}
			?>
	</select> <?php } ?> <?php echo form_error('language'); ?></li>
	<!--    <li>
        <?php if (isset($upload_message)) echo $upload_message; ?>
        Flag 
        <br /><input id="flag" type="file" name="flag" />
        <?php // echo form_error('subject');    ?>
    </li>-->
	<li><?php echo form_submit('submit', 'Submit'); ?></li>
	<?php echo form_close(); ?>
</ul>
