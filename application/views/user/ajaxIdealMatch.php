<?php
$user_id = $this->session->userdata('user_id');
//Access to Premium Filters = 2
$is_premius_member = $this->datetix->is_premium_user($user_id,2);
?>
<?php if($filters) :?>
<label
	id="importanceSelectionError" style="font-size: 18px"
	class="input-hint error error_indentation error_msg"></label>
<?php
foreach ($filters as $filter)
{
	switch ($filter['id_to_map'])
	{
case "age":
			?>
<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('What age range are you open to dating?');?>
	</h2>
	<div class="f-decrMAIN">
		<div class="sfp-1-main">
			<div class="Left-coll01">
			<?php echo translate_phrase('between')?>
			</div>
			<div class="Right-coll01">
			<?php
			echo form_dt_dropdown('ageRangeLowerLimit',$year,$tmp_usr_data['want_age_range_lower'],'id="year" class="dropdown-dt"',translate_phrase('-'),"hiddenfield");
			?>
				<div class="centimeter">
				<?php echo translate_phrase('to')?>
				</div>
				<?php
				echo form_dt_dropdown('ageRangeUpperLimit',$year,$tmp_usr_data['want_age_range_upper'],'id="year" class="dropdown-dt"',translate_phrase('-'),"hiddenfield");
				?>
			</div>
			<label id="ageRangeError"
				class="input-hint error error_indentation error_msg"></label>
		</div>
		<div class="f-decr importanceRange">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php  echo ($key == $tmp_usr_data['want_age_range_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo ($key != $tmp_usr_data['want_age_range_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantAgeRangeImportance"
				id="wantAgeRangeImportance"
				value="<?php echo $tmp_usr_data['want_age_range_importance']?>">
		</div>
	</div>
</div>
				<?php
				break;

case "ethnicity":

	$all_selected = 1;
	$ethnicityValues = (!empty($selectedValues['want_ethnicity'])) ? explode(',',$selectedValues['want_ethnicity']):array();
	foreach ($ethnicity as $key => $value)
	{
		if(array_search($key,$ethnicityValues) === false)
		{
			$all_selected = 0;
		}
	}
	?>
<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('What ethnicities are you open to dating?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
	<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>

	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($ethnicity as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$ethnicityValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key?>" class="disable-butn" href="javascript:;"><?php echo $value?>
				</a>
				</li>
			</ul>

			<?php } ?>
			<input type="hidden" name="ethnicityPreference"
				id="ethnicityPreference"
				value="<?php echo $selectedValues['want_ethnicity']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_ethnicity_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_ethnicity_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantEthnicityImportance"
				id="wantEthnicityImportance"
				value="<?php echo $tmp_usr_data['want_ethnicity_importance']?>">
		</div>
	</div>
</div>

				<?php
				break;
					
case "relationshipGoal":
	?>
<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('How important is it to date someone who share the same relationship goals?')?>
	</h2>
	<div class="f-decrMAIN">

		<div class="f-decr importance" style="padding-bottom: 10px;">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_looking_for_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_looking_for_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantRelationshipGoalImportance"
				id="wantRelationshipGoalImportance"
				value="<?php echo $tmp_usr_data['want_looking_for_importance'] ?>">
		</div>

		<div class="sfp-1-main">
			<div class="Left-coll01">
				<span class="italic"><?php echo translate_phrase('You are looking for:')?>
				</span>
			</div>
			<div class="Right-coll01">
			<?php if(!empty($want_relationship_type)):?>
			<?php foreach ($want_relationship_type as $key => $value) :?>
				<div class="appr-cen">
				<?php echo $value?>
				</div>
				<?php endforeach;?>
				<?php endif;?>
				<div class="Edit-Button01 martop-edit">
					<a
						href="<? echo base_url().  url_city_name().'/edit-profile.html?scroll_to=looking_fordiv' ?>"><?php echo translate_phrase('Edit')?>
					</a>
				</div>
			</div>
		</div>

	</div>
</div>

				<?php
				break;
case "common_interest":
	?>
<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('How important is it for your match to share common interests with you?')?>
	</h2>
	<div class="f-decrMAIN">

		<div class="f-decr importance" style="padding-bottom: 10px;">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_common_interest_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_common_interest_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="want_common_interest_importance"
				id="want_common_interest_importance"
				value="<?php echo $tmp_usr_data['want_common_interest_importance'] ?>">
		</div>

		<div class="sfp-1-main">
			<div class="Right-coll01">
				<div class="Left-coll01">
					<span class="italic"><?php echo translate_phrase('Your interests')?>:</span>
				</div>
				<?php if(!empty($user_interests)):?>
				<?php foreach ($user_interests as $key => $value) :?>
				<div class="appr-cen">
				<?php echo $value['description']?>
				</div>
				<?php endforeach;?>
				<?php endif;?>
				<div class="Edit-Button01 martop-edit">
					<a
						href="<? echo base_url().  url_city_name().'/edit-profile.html?scroll_to=userInterestDiv' ?>"><?php echo translate_phrase('Edit')?>
					</a>
				</div>
			</div>
		</div>

	</div>
</div>

				<?php
				break;
case "looks":
	?>
<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('How good looking do you want your match to be?')?>
	</h2>
	<div class="f-decrMAIN">
		<div class="sfp-1-main">
			<div class="Right-coll01">
			<?php
			echo form_dt_dropdown('looksFrom',$looks,$tmp_usr_data['want_looks_range_lower_id'],'id="looks" class="dropdown-dt looksdowndomain"',translate_phrase('Please select'),"hiddenfield");
			?>
				<div class="centimeter">
					&nbsp;
					<?php echo translate_phrase('to')?>
				</div>
				<?php
				echo form_dt_dropdown('looksTo',$looks,$tmp_usr_data['want_looks_range_higher_id'],'class="dropdown-dt looksdowndomain"',translate_phrase('Please select'),"hiddenfield");
				?>
				<label id="looks_err" class="input-hint error"></label>
			</div>
		</div>
		<div class="f-decr importanceRange">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php  echo ($key == $tmp_usr_data['want_looks_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_looks_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantLooksImportance"
				id="wantLooksImportance"
				value="<?php echo $tmp_usr_data['want_looks_importance']?>">
		</div>
	</div>
</div>

				<?php
				break;
case "height":
	?>
<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('What are your height requirements?')?>
	</h2>

	<div class="f-decrMAIN">
		<input type="hidden" id="useMeters" value="<?php echo $useMeters ?>">
		<?php if($useMeters == 1):?>
		<div class="sfp-1-main">
			<div class="Left-coll01">between</div>
			<div class="Right-coll01">
			<?php
			echo form_dt_dropdown('centemetersFrom',$centemeters,$tmp_usr_data['want_height_range_lower'],'class="dropdown-dt feetdd"',translate_phrase('-'),"hiddenfield");
			?>
				<div class="centimeter">
				<?php echo translate_phrase('to')?>
				</div>
				<?php
				echo form_dt_dropdown('centemetersTo',$centemeters,$tmp_usr_data['want_height_range_upper'],'class="dropdown-dt feetdd"',translate_phrase('-'),"hiddenfield");
				?>
				<div class="centimeter pad-rightNone">cm</div>
				<label id="heightRangeError"
					class="input-hint error error_indentation error_msg"></label>
			</div>
		</div>
		<?php else:?>
		<div class="sfp-1-main">
			<div class="Left-coll01">between</div>
			<div class="Right-coll01">
			<?php
			echo form_dt_dropdown('feetFrom',$feet,$feetFrom,'class="dropdown-dt feetdd"',translate_phrase('-'),"hiddenfield");
			?>
				<div class="centimeter">feet</div>
				<?php
				echo form_dt_dropdown('inchFrom',$inches,$inchFrom,'class="dropdown-dt inchdd"',translate_phrase('-'),"hiddenfield");
				?>
				<div class="centimeter pad-rightNone">inches</div>
				<label id="heightRangeError"
					class="input-hint error error_indentation error_msg"></label>
			</div>
		</div>
		<div class="sfp-1-main">
			<div style="margin-left: 45px" class="Left-coll01">to</div>
			<div class="Right-coll01">
			<?php
			echo form_dt_dropdown('feetTo',$feet,$feetTo,'class="dropdown-dt feetdd"',translate_phrase('-'),"hiddenfield");
			?>
				<div class="centimeter">feet</div>
				<?php
				echo form_dt_dropdown('inchTo',$inches,$inchTo,'class="dropdown-dt inchdd"',translate_phrase('-'),"hiddenfield");
				?>
				<div class="centimeter pad-rightNone">inches</div>
				<label id="heightRangeError"
					class="input-hint error error_indentation error_msg"></label>
			</div>
		</div>
		<!----->
		<?php endif; ?>

		<div class="f-decr importanceRange">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php  echo ($key == $tmp_usr_data['want_height_range_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_height_range_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantHeightImportance"
				id="wantHeightImportance"
				value="<?php echo $tmp_usr_data['want_height_range_importance']?>">
		</div>
		<label id="heighRangeError" style="font-size: 16px"
			class="input-hint error error_indentation error_msg"></label>
	</div>
</div>

				<?php
				break;
case "bodyType":
	?>
	<?php
	$all_selected = 1;
	$bodyTyepValues = (!empty($selectedValues['want_body_type'])) ? explode(',',$selectedValues['want_body_type']):array();
	foreach ($body_type as $key => $value)
	{
		if(array_search($key,$bodyTyepValues) === false)
		{
			$all_selected = 0;
		}
	}
	?>
<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('What kinds of body types do you prefer?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
	<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($body_type as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$bodyTyepValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key?>" class="disable-butn" href="javascript:;"><?php echo $value?>
				</a>
				</li>
			</ul>

			<?php }

			?>
			<input type="hidden" name="bodyTypePreference"
				id="bodyTypePreference"
				value="<?php echo $selectedValues['want_body_type']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php  echo ($key == $tmp_usr_data['want_body_type_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_body_type_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantBodyTypeImportance"
				id="wantBodyTypeImportance"
				value="<?php echo $tmp_usr_data['want_body_type_importance']?>">
		</div>
	</div>
</div>

				<?php
				break;
case "personality":
	?>
	<?php
	$all_selected = 1;
	$personalityValues = (!empty($selectedValues['want_descriptive_word'])) ? explode(',',$selectedValues['want_descriptive_word']) : array();
	foreach ($descriptive_word as $key => $value)
	{
		if(array_search($key,$personalityValues) === false)
		{
			$all_selected = 0;
		}
	}
	?>
<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('What types of personalities do you like?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
	<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php //NOTE : Please reffer model where this data is being stored, before making any changes
			
		foreach ($descriptive_word as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$personalityValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?}
			?>
			<input type="hidden" name="personalityPreference"
				id="personalityPreference"
				value="<?php echo $selectedValues['want_descriptive_word']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_personality_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_personality_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantPersonalityImportance"
				id="wantPersonalityImportance"
				value="<?php echo $tmp_usr_data['want_personality_importance']?>">
		</div>
	</div>
</div>

				<?php break;
case "education_level": ?>
<?php
$all_selected = 1;
$educationLevelValues = explode(',', $selectedValues['want_education_level']);
foreach ($education_level as $key => $value)
{
	if(array_search($key,$educationLevelValues) === false)
	{
		$all_selected = 0;
	}
}
?>
<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('What are your education level requirements?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">

		<?php
		$educationLevelValues = explode(',', $selectedValues['want_education_level']);
		foreach ($education_level as $key => $value)
		{?>
			<ul>
				<li
				<?php echo (array_search($key,$educationLevelValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?php }
			?>
			<input type="hidden" name="educationPreference"
				id="educationPreference"
				value="<?php echo $selectedValues['want_education_level']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_education_level_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_education_level_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantEducationImportance"
				id="wantEducationImportance"
				value="<?php echo $tmp_usr_data['want_education_level_importance']?>">
		</div>
	</div>
</div>

				<?php break;
case "school_major": ?>
<div class="aps-d-top">
<?php
$all_selected = 1;
$schoolSubjectValues =  (!empty($selectedValues['want_school_subject'])) ? explode(',',$selectedValues['want_school_subject']) : array();
foreach ($school_subject as $key => $value)
{
	if(array_search($key,$schoolSubjectValues) === false)
	{
		$all_selected = 0;
	}
}?>
	<h2>
		<label class="left"><?php echo translate_phrase('Any preference on subject areas of study?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($school_subject as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$schoolSubjectValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?php }
			?>
			<input type="hidden" name="subjectPreference" id="subjectPreference"
				value="<?php echo $selectedValues['want_school_subject']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_school_subject_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_school_subject_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantSubjectImportance"
				id="wantSubjectImportance"
				value="<?php echo $tmp_usr_data['want_school_subject_importance']?>">
		</div>
	</div>
</div>

				<?php break;
case "school_name": ?>
<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('Do you want to date people who attended specific schools')?>
		?
	</h2>
	<div class="f-decrMAIN">
	<?php if($is_premius_member):?>
		<div class="f-decr">
			<ul id="showSelectedSchools" class="list_rows">
			<?php $sel_school_ids = array();?>
			<?php if($user_want_school):?>
			<?php foreach ($user_want_school as $school):?>
			<?php $sel_school_ids[] = $school['school_id'];?>
				<li class="Fince-But"
					id="user_want_school<?php echo $school['school_id']?>"><a
					href="javascript:;"><?php echo $school['school_name']?><img
						src="<?php echo base_url()?>assets/images/cross.png"
						onclick="remove_prefrence('user_want_school','user_want_school_ids','<?php echo $school['school_id']?>');"
						title="Remove"> </a></li>
						<?php endforeach;?>
						<?php endif;?>
			</ul>
		</div>
		<div class="f-decrMAIN">
			<div class="sfp-1-main">
				<div class="Left-coll01">
				<?php echo translate_phrase('School name')?>
					:
				</div>
				<div class="Right-coll01">
					<div class="drop-down-wrapper-full">
						<dl class="schooldowndomain">
							<dt>
								<span> <input id="school_name" class="livedin-input"
									name="school_name" type="text"
									placeholder="Type Your School Name" value=""
									onkeyup="auto_complete_school();"> </span>
							</dt>
							<dd id="auto-school-container"></dd>
						</dl>
						<label id="schoo_name_err" class="input-hint error"></label>
					</div>

					<div class="sch-logoR" id="school_logo"></div>
					<div class="add-butM">
						<div class="Edit-Button01">
							<input type="hidden" name="user_want_school_ids"
								id="user_want_school_ids"
								value="<?php echo implode(',', $sel_school_ids)?>"> <a
								href="javascript:;" onclick="add_school_prefrence()">Add</a>
						</div>
					</div>
				</div>
			</div>

			<div class="f-decr importance">
				<ul>
				<?php
				foreach ($importance as $key => $value)
				{ ?>
					<li
					<?php echo ($key == $tmp_usr_data['want_school_importance']) ? 'class="Intro-Button-sel"':''?>>
						<a
						<?php echo  ($key != $tmp_usr_data['want_school_importance']) ? 'class="Intro-Button"':'' ?>
						href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
					</a>
					</li>
					<? } ?>
				</ul>
				<input type="hidden" name="wantSchoolImportance"
					id="wantSchoolImportance"
					value="<?php echo $tmp_usr_data['want_school_importance']?>">
			</div>
		</div>

		<?php else:?>
		<h3>
			<span class="italic"><?php echo translate_phrase('This filter is only available to members who have the ')?><a
				class="blu-color"
				href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo translate_phrase("Premium Filter Access account upgrade")?>
			</a>.</span>
		</h3>
		<div class="f-decrMAIN">
			<div class="yellow-btn Upgrd-Mar fl">
				<a
					href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=ideal-match.html"><?php echo translate_phrase('Upgrade Account')?>
				</a>
			</div>
		</div>
		<?php endif;?>
	</div>
</div>
		<?php break;
case "career_stage": ?>
<?php
$all_selected = 1;
$selectedCareerStages = !empty($selectedValues['want_career_stage']) ? explode(',',$selectedValues['want_career_stage']) : array();
foreach ($carrier_stage as $key => $value)
{
	if(array_search($key,$selectedCareerStages) === FALSE )
	{
		$all_selected = 0;
	}
}
?>

<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('What career stage do you want your match to be in?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($carrier_stage as $key => $value)
		{?>
			<ul>
				<li
				<?php echo (array_search($key,$selectedCareerStages) !== FALSE )? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value;?>
				</a>
				</li>
			</ul>
			<?php }
			?>

			<input type="hidden" name="openForDatingPreference"
				id="openForDatingPreference"
				value="<?php echo $selectedValues['want_career_stage']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_career_stage_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_career_stage_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantOpenForDatingImportance"
				id="wantOpenForDatingImportance"
				value="<?php echo $tmp_usr_data['want_career_stage_importance']?>">
		</div>
	</div>
</div>

				<?php break;
case "income_level": ?>
<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('What income level requirements do you have?');?>
	</h2>
	<div class="f-decrMAIN">
		<div class="sfp-1-main" id="p_income">
			<div class="Left-coll01">
			<?php echo translate_phrase('Minimum annual income:');?>
			</div>
			<div class="Right-coll01">
			<?php
			$defult_curreny_id  = $tmp_usr_data['want_annual_income_currency_id']?$tmp_usr_data['want_annual_income_currency_id']:$currency_id;
			echo form_dt_dropdown('incomePrefrence',$currency,$defult_curreny_id,'class="dropdown-dt currencydd"',translate_phrase('Please Select'),"hiddenfield");
			?>
				<div class="sel-emailR HKd-Media-pad">
					<input name="incomeAmount" id="incomeAmount" type="text"
						class="Degree-input"
						value="<?php echo $tmp_usr_data['want_annual_income']?>" />
				</div>
			</div>
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_annual_income_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_annual_income_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantIncomePrefrenceImportance"
				id="wantIncomePrefrenceImportance"
				value="<?php echo $tmp_usr_data['want_annual_income_importance']?>">
		</div>
	</div>
</div>

				<?php break;
case "company_name": ?>
<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('Do you want to date people who work for specific companies')?>
		?
	</h2>
	<div class="f-decrMAIN">
	<?php if($is_premius_member):?>
		<div class="f-decr">
			<ul id="showSelectedCompanies" class="list_rows">
			<?php $sel_company_ids = array();?>
			<?php if($user_want_company):?>
			<?php foreach ($user_want_company as $company):?>
			<?php $sel_company_ids[] = $company['company_id'];?>
				<li class="Fince-But"
					id="user_want_company<?php echo $company['company_id']?>"><a
					href="javascript:;"><?php echo $company['company_name']?><img
						src="<?php echo base_url()?>assets/images/cross.png"
						onclick="remove_prefrence('user_want_company','user_want_company_ids','<?php echo $company['company_id']?>');"
						title="Remove"> </a></li>
						<?php endforeach;?>
						<?php endif;?>
			</ul>
		</div>

		<div class="f-decrMAIN">
			<div class="sfp-1-main">
				<div class="Left-coll01">
				<?php echo translate_phrase('Company name')?>
					:
				</div>
				<div class="Right-coll01">
					<div class="drop-down-wrapper-full">
						<dl class="schooldowndomain">
							<dt>
								<span> <input id="company_name" lang="" class="livedin-input"
									name="company_name" type="text" placeholder="Type Company Name"
									value="" onkeyup="auto_complete_company();"> </span>
							</dt>
							<dd id="auto-company-container"></dd>
						</dl>
						<label id="company_name_err" class="input-hint error"></label>
					</div>

					<div class="sch-logoR" id="company_logo"></div>
					<div class="add-butM">
						<div class="Edit-Button01">
							<input type="hidden" name="user_want_company_ids"
								id="user_want_company_ids"
								value="<?php echo implode(',', $sel_company_ids)?>"> <a
								href="javascript:;" onclick="add_company_prefrence()"><?php echo translate_phrase('Add')?>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="f-decr importance">
				<ul>
				<?php
				foreach ($importance as $key => $value)
				{ ?>
					<li
					<?php echo ($key == $tmp_usr_data['want_company_importance']) ? 'class="Intro-Button-sel"':''?>>
						<a
						<?php echo  ($key != $tmp_usr_data['want_company_importance']) ? 'class="Intro-Button"':'' ?>
						href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
					</a>
					</li>
					<? } ?>
				</ul>
				<input type="hidden" name="wantCompanyImportance"
					id="wantCompanyImportance"
					value="<?php echo $tmp_usr_data['want_company_importance']?>">
			</div>
		</div>

		<?php else:?>
		<h3>
			<span class="italic"><?php echo translate_phrase('This filter is only available to members who have the ')?><a
				class="blu-color"
				href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo translate_phrase("Premium Filter Access account upgrade")?>
			</a>.</span>
		</h3>
		<div class="f-decrMAIN">
			<div class="yellow-btn Upgrd-Mar fl">
				<a
					href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=ideal-match.html"><?php echo translate_phrase('Upgrade Account')?>
				</a>
			</div>
		</div>
		<?php endif;?>
	</div>
</div>

		<?php break;
case "job_function": ?>
<div class="aps-d-top">
<?php
$all_selected = 1;
$jobFunctionsValues = (!empty($selectedValues['want_job_function']))? explode(',',$selectedValues['want_job_function']):array();
foreach ($job_functions as $key => $value)
{
	if(array_search($key,$jobFunctionsValues) === false)
	{
		$all_selected= 0;
	}
}
?>
	<h2>
		<label class="left"><?php echo translate_phrase('Any preference on the types of work that your match does?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">

		<?php
		foreach ($job_functions as $key => $value)
		{?>
			<ul>
				<li
				<?php echo (array_search($key,$jobFunctionsValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value?>
				</a>
				</li>
			</ul>
			<?php } ?>
			<input type="hidden" name="jobPreference" id="jobPreference"
				value="<?php echo $selectedValues['want_job_function']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_job_function_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_job_function_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantParticularJobImportance"
				id="wantParticularJobImportance"
				value="<?php echo $tmp_usr_data['want_job_function_importance']?>">
		</div>
	</div>
</div>

				<?php break;
case "job_industry": ?>
<div class="aps-d-top">
<?php
$all_selected = 1;
$industryValues = (!empty($selectedValues['want_industry']))? explode(',', $selectedValues['want_industry']) : array();
foreach ($industry as $key => $value)
{
	if(array_search($key,$industryValues) === false)
	{
		$all_selected = 0;
	}
}?>
	<h2>
		<label class="left"><?php echo translate_phrase('Any preference on what industries your match works in?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">

		<?php
		foreach ($industry as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$industryValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?}
			?>
			<input type="hidden" name="industryPreference"
				id="industryPreference"
				value="<?php echo $selectedValues['want_industry']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_industry_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_industry_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantParticularIndustryImportance"
				id="wantParticularIndustryImportance"
				value="<?php echo $tmp_usr_data['want_industry_importance']?>">
		</div>
	</div>
</div>

				<?php break;
case "relationshipStatus": ?>
<?php
$all_selected = 1;
$relationshipStatusValues = (!empty($selectedValues['want_relationship_status']))? explode(',',$selectedValues['want_relationship_status']) : array();
foreach ($relationship_status as $key => $value)
{
	if(array_search($key,$relationshipStatusValues) === false)
	{
		$all_selected = 0;
	}
}
?>
<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('What relationship statuses do you prefer your match to have?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($relationship_status as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$relationshipStatusValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?php }
			?>
			<input type="hidden" name="replationshipStatusPreference"
				id="replationshipStatusPreference"
				value="<?php echo $selectedValues['want_relationship_status']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_relationship_status_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_relationship_status_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantRelationshipStatusImportance"
				id="wantRelationshipStatusImportance"
				value="<?php echo $tmp_usr_data['want_relationship_status_importance']?>">
		</div>
	</div>
</div>

				<?php break;
case "religion": ?>
<?php
$all_selected = 1;
$religiousBeliefValues = (!empty($selectedValues['want_religious_belief'])) ? explode(',',$selectedValues['want_religious_belief']):array();
foreach ($religious_belief as $key => $value)
{
	if(array_search($key,$religiousBeliefValues) === false)
	{
		$all_selected = 0;
	}
}?>
<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('What religious beliefs do you prefer your match to have?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($religious_belief as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$religiousBeliefValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<? }
			?>
			<input type="hidden" name="religiousBeliefPreference"
				id="religiousBeliefPreference"
				value="<?php echo $selectedValues['want_religious_belief']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_religious_belief_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_religious_belief_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantRegligiousBeliefImportance"
				id="wantRegligiousBeliefImportance"
				value="<?php echo $tmp_usr_data['want_religious_belief_importance']?>">
		</div>
	</div>
</div>
				<?php break;

case "smokingStatus": ?>
<?php
$all_selected = 1;
$smokingStatusValues = (!empty($selectedValues['want_smoking_status'])) ? explode(',',$selectedValues['want_smoking_status']) : array();
foreach ($smoking_status as $key => $value)
{
	if(array_search($key,$smokingStatusValues) === false)
	{
		$all_selected =0;
	}
}?>

<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('Any preferences on how often your match smokes?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($smoking_status as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$smokingStatusValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?php }
			?>
			<input type="hidden" name="smokingPreference" id="smokingPreference"
				value="<?php echo $selectedValues['want_smoking_status']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_smoking_status_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_smoking_status_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantSmokingImportance"
				id="wantSmokingImportance"
				value="<?php echo $tmp_usr_data['want_smoking_status_importance']?>">
		</div>
	</div>
</div>

				<?php
				break;
case "drinkingStatus":
	?>
	<?php
	$all_selected = 1;
	$drinkingStatusValues = (!empty($selectedValues['want_drinking_status'])) ? explode(',',$selectedValues['want_drinking_status']) : array();
	foreach ($drinking_status as $key => $value)
	{
		if(array_search($key,$drinkingStatusValues) === false)
		{
			$all_selected =0;
		}
	}?>

<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('Any preferences on how often your match drinks?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
	<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($drinking_status as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$drinkingStatusValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?  }
			?>
			<input type="hidden" name="drinkingPreference"
				id="drinkingPreference"
				value="<?php echo $selectedValues['want_drinking_status']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_drinking_status_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_drinking_status_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantDrinkingImportance"
				id="wantDrinkingImportance"
				value="<?php echo $tmp_usr_data['want_drinking_status_importance']?>">
		</div>
	</div>
</div>

				<?php
				break;
case "exerciseStatus":
	?>
	<?php
	$all_selected = 1;
	$exerciseFrequencyValues = (!empty($selectedValues['want_exercise_frequency'])) ? explode(',',$selectedValues['want_exercise_frequency']):array();
	foreach ($exercise_frequency as $key => $value)
	{
		if(array_search($key,$exerciseFrequencyValues) === false)
		{
			$all_selected = 0;
		}
	}
	?>
<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('Any preferences on how often your match exercises?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
	<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($exercise_frequency as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$exerciseFrequencyValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?}
			?>
			<input type="hidden" name="excersisePreference"
				id="excersisePreference"
				value="<?php echo $selectedValues['want_exercise_frequency']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_exercise_frequency_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_exercise_frequency_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantExcersiseImportance"
				id="wantExcersiseImportance"
				value="<?php echo $tmp_usr_data['want_exercise_frequency_importance']?>">
		</div>
	</div>
</div>

				<?php
				break;
case "residenceType":
	?>
	<?php
	$all_selected = 1;
	$residenceTypeValues = (!empty($selectedValues['want_residence_type'])) ? explode(',',$selectedValues['want_residence_type']) : array();
	foreach ($residence_type as $key => $value)
	{
		if(array_search($key,$residenceTypeValues) === false)
		{
			$all_selected = 0;
		}
	}
	?>
<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('Any preferences on what type of housing your match lives in?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
	<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($residence_type as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$residenceTypeValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?  }
			?>
			<input type="hidden" name="livingPlacePreference"
				id="livingPlacePreference"
				value="<?php echo $selectedValues['want_residence_type']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_residence_type_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_residence_type_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantLivingPlaceImportance"
				id="wantLivingPlaceImportance"
				value="<?php echo $tmp_usr_data['want_residence_type_importance']?>">
		</div>
	</div>
</div>

				<?php break;
case "existing_children": ?>
<?php
$all_selected = 1;
$childStatusValues = (!empty($selectedValues['want_child_status'])) ? explode(',',$selectedValues['want_child_status']) : array();
foreach ($child_status as $key => $value)
{
	if(array_search($key,$childStatusValues) === false)
	{
		$all_selected = 0;
	}
}?>
<div class="aps-d-top">
	<h2>
		<label class="left"><?php echo translate_phrase('Are you open to dating people who have children')?>?</label>
		<a <?php if($all_selected):?> style="display: none;" <?php endif;?>
			class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag" id="dateGirlsWithChildrens">
		<?php
		foreach ($child_status as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$childStatusValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?  }
			?>
			<input type="hidden" id="dateGirlsWithChildrensPreference"
				name="dateGirlsWithChildrensPreference"
				value="<?php echo $selectedValues['want_child_status']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php echo ($key == $tmp_usr_data['want_child_status_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo  ($key != $tmp_usr_data['want_child_status_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantDateGirlsWithChildrensImportance"
				id="wantDateGirlsWithChildrensImportance"
				value="<?php echo $tmp_usr_data['want_child_status_importance']?>">
		</div>
	</div>
</div>
				<?php break;
case "childPlans": ?>
<div class="aps-d-top">
<?php
$all_selected = 1;
$childPlansValues = (!empty($selectedValues['want_child_plan'])) ? explode(',',$selectedValues['want_child_plan']) : array();
foreach ($child_plans as $key => $value)
{
	if(array_search($key,$childPlansValues) === false)
	{
		$all_selected = 0;
	}
}?>
	<h2>
		<label class="left"><?php echo translate_phrase('Any preferences on how many children your match wants to have?')?>
		</label> <a <?php if($all_selected):?> style="display: none;"
<?php endif;?> class="disable-butn left no-margin"
			onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
		</a>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr customSelectTag">
		<?php
		foreach ($child_plans as $key => $value)
		{ ?>
			<ul>
				<li
				<?php echo (array_search($key,$childPlansValues) !== FALSE)? 'class="selected"':'' ?>>
					<a id="<?php echo $key ?>" class="disable-butn" href="javascript:;"><?php echo $value ?>
				</a>
				</li>
			</ul>
			<?php }
			?>
			<input type="hidden" name="childrensPlanPreference"
				id="childrensPlanPreference"
				value="<?php echo $selectedValues['want_child_plan']?>">
		</div>
		<div class="f-decr importance">
			<ul>
			<?php
			foreach ($importance as $key => $value)
			{ ?>
				<li
				<?php  echo ($key == $tmp_usr_data['want_child_plan_importance']) ? 'class="Intro-Button-sel"':''?>>
					<a
					<?php echo ($key != $tmp_usr_data['want_child_plan_importance']) ? 'class="Intro-Button"':'' ?>
					href="javascript:;" importanceVal="<?php echo $key?>"><?php echo translate_phrase($value)?>
				</a>
				</li>
				<? } ?>
			</ul>
			<input type="hidden" name="wantChildrensPlanImportance"
				id="wantChildrensPlanImportance"
				value="<?php echo $tmp_usr_data['want_child_plan_importance']?>">
		</div>
	</div>
</div>

				<?php
				break;
case "other":
	?>
<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('What other things are important to you in your ideal match?');?>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr">
			<textarea name="ideal_date" cols="" rows="" class="as-E-textarea">
			<?php echo $tmp_usr_data['ideal_date']?>
			</textarea>
		</div>
	</div>
</div>

<div class="aps-d-top">
	<h2>
	<?php echo translate_phrase('What kinds of people would you absolutely not want to date?');?>
	</h2>
	<div class="f-decrMAIN">
		<div class="f-decr">
			<textarea name="not_want_to_date" cols="" rows=""
				class="as-E-textarea">
				<?php echo $tmp_usr_data['not_want_to_date']?>
			</textarea>
		</div>
	</div>
</div>
				<?php
				break;

default:
	break;
	}
}
?>
<?php endif; // Main filter end?>