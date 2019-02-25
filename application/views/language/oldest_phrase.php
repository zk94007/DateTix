<h1>Oldest phrases</h1>
<?php if (isset($feedback)){ ?>
<div class="feedback">
<?php echo $feedback; ?>
</div>
<?php } ?>
<form method="post" name="month_form"
	action="<?php echo base_url() . "translation/oldest_phrase/"; ?>">
	<div class="search_box">
		Phrase fetched before <select id="month" name="month">
		<?php
		for ($i = 1; $i < 6; $i++) {
			$sel = "";
			if ($month == $i)
			$sel = "selected";
			?>
			<option value="<?php echo $i; ?>" <?php echo $sel; ?>>
			<?php echo $i ?>
				month
			</option>
			<?php
		}
		?>
		</select>
	</div>
</form>
<br>
		<?php if (isset($phrases)) { ?>
<form method="post" name="phrase_form"
	action="<?php echo base_url() . "translation/delete_phrase/"; ?>">
	<table width="100%">
		<tr>
			<th></th>
			<th>Phrase</th>
		</tr>
		<?php
		foreach ($phrases as $row) {
			$phrase_id = $row["phrase_id"];
			?>
		<tr>
			<td><input type="checkbox" name="phrase_id[]"
				value="<?php echo $phrase_id; ?>"></td>
			<td><?php echo wordwrap($row["phrase"], 50, "\n"); ?></td>
		</tr>
		<?php } ?>
	</table>
	<ul class="form">
		<li><input type="submit" name="delete" value="Delete"></li>
	</ul>
</form>
<div class="pagination">
<?php echo $links; ?>
</div>
<?php
		} else {
			echo '<p>No phrases  found.</p>';
		}
		?>
<script>
    $("#month").change(function(){
        document.month_form.submit();
    });
</script>
