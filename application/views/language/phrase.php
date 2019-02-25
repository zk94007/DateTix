<h1>
<?php echo $language; ?>
	translations
</h1>
<form action="<?php echo base_url() . "translation/search/" ?>"
	name="search_frm" id="search_frm" method="get">
	<div class="search_box">
		Language<br> <select id="language_id" name="language_id">
		<?php
		if (!empty($language_array)) {
			foreach ($language_array as $key => $value) {
				if (strtoupper($value->description) != "ENGLISH") {
					$sel = "";
					if ($value->display_language_id == $language_id)
					$sel = "selected";
					?>
			<option value="<?php echo $value->display_language_id; ?>"
			<?php echo $sel; ?>>
				<?php echo $value->description; ?>
			</option>
			<?php
				}
			}
		}
		?>
		</select>
	</div>
	<div class="search_box">
		Search text<br> <input type="text" name="search_text"
			value="<?php echo $search_text; ?>" id="search_text">
	</div>
	<span class="clear"></span> <input type="submit" value="Search"> <span
		class="clear"></span>
</form>

		<?php if (isset($phrases)) { ?>
<form method="post" name="phrase_form"
	action="<?php echo base_url() . "translation/save_phrases/"; ?>">
	<table width="100%">
		<input type="hidden" name="language_id"
			value="<?php echo $language_id; ?>">
		<tr>
			<th>Phrase</th>
			<th>Translation</th>
			<th>Save</th>
			<!--<th>Save&Approve</td>-->
			<th>Approve</th>
		</tr>
		<?php
		foreach ($phrases as $row) {
			$phrase_id = $row->phrase_id;
			?>
		<tr id="tr_<?php echo $phrase_id; ?>">
			<td valign="top"><?php echo wordwrap($row->phrase, 50, "\n"); ?></td>
			<td><textarea cols="50" id="translation_<?php echo $phrase_id; ?>"
					name="translation[<?php echo $phrase_id; ?>]">
					<?php echo $row->translation; ?>
				</textarea></td>
			<td align="center"><img
				src="<?php echo base_url(); ?>images/save.png"
				onclick="save_phrase('<?php echo $phrase_id; ?>','save')"></td>
			<!--<td><input type="button" value="Save & Approve" onclick="save_phrase('<?php echo $phrase_id; ?>','save_approve')" ></td>-->
			<td align="center"><img
				src="<?php echo base_url(); ?>images/accept.png"
				onclick="save_phrase('<?php echo $phrase_id; ?>','approve')"></td>
		</tr>
		<?php } ?>
	</table>
	<ul class="form">
		<li><input type="submit" name="save_all" value="Save all"></li>
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
    $(document).ready(function(){
        $("#language_id").change(function(){
            document.forms[0].submit();
        }); 
    });
    function save_phrase(phrase_id,save)
    {
        
        var language_id = $("#language_id").val();
        var el_id = "translation_"+phrase_id;
        var translation = $("#"+el_id).val();
        tr_id = "tr_"+phrase_id;
        if(translation!="")
        {
            $.ajax({
                type: "POST",
                async:false,
                dataType: 'json',
                data: "phrase_id="+phrase_id+"&language_id="+language_id+"&translation="+translation+"&save="+save,
                url: "<?php echo base_url() . "translation/translate_phrase"; ?>",
                success: function(data){
                    if(save == "approve")
                        alert("Apporved translation");
                    else if(save == "save_approve")
                        alert("Saved and apporved translation");
                    else
                        alert("Updated translation");
                }
            });    
            $("#"+tr_id).css('background-color', 'green');
            setTimeout(function(){
                $("#"+tr_id).css('background-color', 'white');
            }, 1500);          
        }else{
            alert("Please fill the translation");
        }

    }
</script>
