<h1>Languages</h1>
<?php
if ($message) {
	?>
<div class="feedback">
<?php echo $message; ?>
</div>
<?php
}
if (isset($data)) {
	?>
<table>
	<tr>
		<th>Language</th>
		<!--            <th>Active/Deactive</th>-->
		<!--            <th>Default</th>-->
		<th>Translate</th>
		<th>Google translate new</th>
		<th>Google translate all</th>
	</tr>
	<?php
	foreach ($data as $row) {
		$language_id = $row->display_language_id;
		//            $activate = "";
		//            if ($row->active == 1)
		//                $activate = "Deactivate";
		//            else
		//                $activate = "Activate";
		//            if ($row->default == 1)
		//                $default = "Default";
		//            else
		//                $default = '<a href="' . base_url() . "language/make_default/" . $language_id . '">Make default</a>';
		$translate = '<a href="' . base_url() . "translation/phrases/" . $language_id . '">Translate</a>';
		$translate_new = '<a href="' . base_url() . "translation/translate_new/" . $language_id . '">Google translate new</a>';
		$translate_all = '<a href="' . base_url() . "translation/translate_all/" . $language_id . '">Google translate all</a>';
		if (strtoupper($row->language_code) == "EN") {
			$translate = "";
			$translate_all = "";
			$translate_new = "";
		}
		?>

	<tr>
		<td><?php echo $row->description; ?></td>
		<!--                <td><a href="javascript:void();" onclick="activate('<?php echo $language_id; ?>');"><?php echo $activate; ?></a></td>-->
		<!--                <td><?php echo $default; ?></td>-->
		<td><?php echo $translate; ?></td>
		<td><?php echo $translate_new; ?></td>
		<td><?php echo $translate_all; ?></td>
	</tr>
	<?php }// end foreach ?>
</table>
	<?php
} else {
	echo '<p>No languages  found.</p>';
}// end if $data
?>
