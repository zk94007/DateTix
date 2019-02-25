<ul>
<?php if(isset($language_id)){ ?>
	<li><?php echo anchor("translation/notapproved/$language_id?na=1","Unapproved phrases"); ?>
	</li>
	<li><?php echo anchor("translation/remove/$language_id","Remove all phrases"); ?>
	</li>
	<?php } ?>
	<li><?php echo anchor("translation/oldest_phrase","Show oldest phrases"); ?>
	</li>
</ul>
