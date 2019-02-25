<?php
$user_id = $this->session->userdata('user_id');
//Access to Premium Filters = 2
$is_premius_member = $this->datetix->is_premium_user($user_id,2);
?>
<?php if($filters) :?>
<div class="edu-main">
	<label id="importanceSelectionError" style="font-size: 18px"
		class="input-hint error error_indentation error_msg"></label>
		<?php
		$is_auto_focus = 0;
		foreach ($filters as $filter)
		{
			switch ($filter['id_to_map'])
			{
case "age":
					?>
	<div class="aps-d-top" id="age">
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
				echo form_dt_dropdown('ageRangeLowerLimit',$year,'','id="year" class="dropdown-dt"',translate_phrase('-'),"hiddenfield");
				?>
					<div class="centimeter">
					<?php echo translate_phrase('to')?>
					</div>
					<?php
					echo form_dt_dropdown('ageRangeUpperLimit',$year,'','id="year" class="dropdown-dt"',translate_phrase('-'),"hiddenfield");
					?>
				</div>
				<label id="ageRangeError"
					class="input-hint error error_indentation error_msg"></label>
			</div>
			<div class="f-decr importanceRange">
				<ul>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Mandatory')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Very Important')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Important')?>
					</a></li>
				</ul>
				<input type="hidden" name="wantAgeRangeImportance"
					id="wantAgeRangeImportance">
			</div>
		</div>
	</div>
	<?php

case "ethnicity":
	?>
	<div class="aps-d-top" id="ethnicity">
		<h2>
			<label class="left"><?php echo translate_phrase('What ethnicities are you open to dating?')?>
			</label> <a class="disable-butn left no-margin"
				onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
			</a>
		</h2>
		<div class="f-decrMAIN">
			<div class="f-decr customSelectTag">
			<?php
			foreach ($ethnicity as $key => $value)
			{
				echo '<ul>
					                                <li><a id="'.$key.'" class="disable-butn" href="javascript:;" >'.translate_phrase($value).'</a></li>
					                              </ul>';
			}
			?>
				<input type="hidden" name="ethnicityPreference"
					id="ethnicityPreference">
			</div>

			<div class="f-decr importance">
				<ul>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Mandatory')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Very Important')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Important')?>
					</a></li>
				</ul>
				<input type="hidden" name="wantEthnicityImportance"
					id="wantEthnicityImportance">
			</div>
		</div>
	</div>
	<?php
	break;
case "height":
	?>
	<div class="aps-d-top" id="height">
		<h2>
		<?php echo translate_phrase('What are your height requirements')?>
			?
		</h2>
		<div class="f-decrMAIN">
		<?php if($use_meters == 1):?>
			<div class="sfp-1-main">
				<div class="Left-coll01">between</div>
				<div class="Right-coll01">
				<?php
				echo form_dt_dropdown('centemetersFrom',$centemeters,'','class="dropdown-dt feetdd"',translate_phrase('-'),"hiddenfield");
				?>
					<div class="centimeter" style="padding-right: 23px;">to</div>
					<?php
					echo form_dt_dropdown('centemetersTo',$centemeters,'','class="dropdown-dt feetdd"',translate_phrase('-'),"hiddenfield");
					?>
					<div class="centimeter pad-rightNone">cm</div>
					<label id="heightRangeError"
						class="input-hint error error_indentation error_msg"></label>
				</div>
			</div>
			<?php endif; ?>
			<!----->
			<?php if($use_meters == 0):?>
			<div class="sfp-1-main">
				<div class="Left-coll01">
				<?php echo translate_phrase('between')?>
				</div>
				<div class="Right-coll01">
				<?php
				echo form_dt_dropdown('feetFrom',$feet,'','class="dropdown-dt feetdd"',translate_phrase('Please select'),"hiddenfield");
				?>
					<div class="centimeter">
					<?php echo translate_phrase('feet')?>
					</div>
					<?php
					echo form_dt_dropdown('inchFrom',$inches,'','class="dropdown-dt inchdd"',translate_phrase('Please select'),"hiddenfield");
					?>
					<div class="centimeter pad-rightNone">
					<?php echo translate_phrase('inches')?>
					</div>
					<label id="heightRangeError"
						class="input-hint error error_indentation error_msg"></label>
				</div>
			</div>
			<div class="sfp-1-main">
				<div style="margin-left: 45px" class="Left-coll01">
				<?php echo translate_phrase('to')?>
				</div>
				<div class="Right-coll01">
				<?php
				echo form_dt_dropdown('feetTo',$feet,'','class="dropdown-dt feetdd"',translate_phrase('Please select'),"hiddenfield");
				?>
					<div class="centimeter">
					<?php echo translate_phrase('feet')?>
					</div>
					<?php
					echo form_dt_dropdown('inchTo',$inches,'','class="dropdown-dt inchdd"',translate_phrase('Please select'),"hiddenfield");
					?>
					<label id="heightRangeError"
						class="input-hint error error_indentation error_msg"></label>
					<div class="centimeter pad-rightNone">
					<?php echo translate_phrase('inches')?>
					</div>
				</div>
			</div>
			<?php endif;?>
			<!----->
			<div class="f-decr importanceRange">
				<ul>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Mandatory')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Very Important')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Important')?>
					</a></li>
				</ul>
				<input type="hidden" name="wantHeightImportance"
					id="wantHeightImportance">
			</div>
		</div>
	</div>
	<?php
	break;
case "personality":
	?>
	<div class="aps-d-top" id="personality">
		<h2>
			<label class="left"><?php echo translate_phrase('What types of personalities do you like?')?>
			</label> <a class="disable-butn left no-margin"
				onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
			</a>
		</h2>
		<div class="f-decrMAIN">
			<div class="f-decr customSelectTag">
			<?php
			foreach ($descriptive_word as $key => $value)
			{
				echo '<ul>
		                                        <li><a id="'.$value['descriptive_word_id'].'" class="disable-butn" href="javascript:;" >'.$value['description'].'</a></li>
		                                     </ul>';
			}
			?>
				<input type="hidden" name="personalityPreference"
					id="personalityPreference">
			</div>
			<div class="f-decr importance">
				<ul>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Mandatory')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Very Important')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Important')?>
					</a></li>
				</ul>
				<input type="hidden" name="wantPersonalityImportance"
					id="wantPersonalityImportance">
			</div>
		</div>
	</div>
	<?php break;
case "education_level": ?>
	<div class="aps-d-top" id="education">
		<h2>
			<label class="left"><?php echo translate_phrase('What are your education level requirements?')?>
			</label> <a class="disable-butn left no-margin"
				onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
			</a>
		</h2>
		<div class="f-decrMAIN">
			<div class="f-decr customSelectTag">

			<?php
			foreach ($education_level as $key => $value)
			{
				echo '<ul>
                                        <li><a id="'.$value['education_level_id'].'" class="disable-butn" href="javascript:;" >'.$value['description'].'</a></li>
                                     </ul>';
			}
			?>
				<input type="hidden" name="educationPreference"
					id="educationPreference">
			</div>
			<div class="f-decr importance">
				<ul>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Mandatory')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Very Important')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Important')?>
					</a></li>
				</ul>
				<input type="hidden" name="wantEducationImportance"
					id="wantEducationImportance">
			</div>
		</div>
	</div>
	<?php break;;
case "job_industry": ?>
	<div class="aps-d-top" id="industry">
		<h2>
			<label class="left"><?php echo translate_phrase('Any preference on what industries your match works in?')?>
			</label> <a class="disable-butn left no-margin"
				onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?>
			</a>
		</h2>
		<div class="f-decrMAIN">
			<div class="f-decr customSelectTag">
			<?php
			foreach ($industry as $key => $value)
			{
				echo '<ul>
			                                        <li><a id="'.$key.'" class="disable-butn" href="javascript:;" >'.$value.'</a></li>
			                                     </ul>';                  
			}
			?>
				<input type="hidden" name="industryPreference"
					id="industryPreference">
			</div>
			<div class="f-decr importance">
				<ul>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Mandatory')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Very Important')?>
					</a></li>
					<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Important')?>
					</a></li>
				</ul>
				<input type="hidden" name="wantParticularIndustryImportance"
					id="wantParticularIndustryImportance">
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
</div>
		<?php endif; // Main filter end?>