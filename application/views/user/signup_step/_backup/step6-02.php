<script src="<?php echo base_url()?>assets/js/general.js"></script>
<?php $this->load->view('user/include/ideal_match_js');?>
<script>
function save_data()
{
	if(validateStep2bRange() && validateStep2b())
	{
		$("#signupForm").submit();
	}
}
</script>
<!--*********Apply-Step1-A-Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="signup" id="signupForm"
			action="<?php echo base_url().url_city_name() . '/'.$this->singup_name.'-6.html';?>"
			method="post" enctype="multipart/form-data">
			
			<label class="input-hint error"><?php echo $this->session->flashdata('edit_profile_msg_error');?></label>
			
			<div class="Apply-Step1-a-main" id="step2_a">
				<div class="step-form-Main">
					<div class="step-form-Part">
					<?php if($filters) :?>
					<div class="edu-main">
						<?php 
						foreach ($filters as $filter){
							switch($filter['id_to_map']){
								case 'age':
							?>						
						<!-- AGE Start -->
						<div class="aps-d-top" id="age"> 
							<h2><?php echo translate_phrase('What age range are you open to dating?');?></h2>
							<div class="f-decrMAIN">
								<div class="sfp-1-main">
									<div class="Left-coll01"> <?php echo translate_phrase('between')?> </div>
									<div class="Right-coll01">
										<?php echo form_dt_dropdown('ageRangeLowerLimit',$year,'','id="year" class="dropdown-dt"',translate_phrase('-'),"hiddenfield"); ?>
										<div class="centimeter"> <?php echo translate_phrase('to')?> </div>
										<?php echo form_dt_dropdown('ageRangeUpperLimit',$year,'','id="year" class="dropdown-dt"',translate_phrase('-'),"hiddenfield"); ?>
									</div>
									<label id="ageRangeError" class="input-hint error"></label>
								</div>
								<div class="f-decr importanceRange">
									<ul>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Very Important')?> </a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Not Important')?></a></li>
									</ul>
									<input type="hidden" name="wantAgeRangeImportance" id="wantAgeRangeImportance">
								</div>
							</div>
						</div>
						<?php 
							break;
							case "ethnicity":
						?>
						<?php if($ethnicity) :?>
						<!-- Ethnicity -->
						<div class="aps-d-top" id="ethnicity">
							<h2> <label class="left"><?php echo translate_phrase('What ethnicities are you open to dating?')?> </label> <a class="disable-butn left no-margin" onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?></a> </h2>
							<div class="f-decrMAIN">
								<div class="f-decr customSelectTag">
									<?php foreach ($ethnicity as $key => $value):?>
										<ul><li><a id="<?php echo $key;?>" class="disable-butn" href="javascript:;" ><?php echo translate_phrase($value);?></a></li></ul>
									<?php endforeach; ?>
									<input type="hidden" name="ethnicityPreference" id="ethnicityPreference">
								</div>
																	
								<div class="f-decr importance">
									<ul> 
										<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Very Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Not Important')?></a></li>
									</ul>
									<input type="hidden" name="wantEthnicityImportance" id="wantEthnicityImportance">
								</div>
							</div>
						</div>
						<?php endif;?>
						<?php 
							break;
							case "height":
						?>
						<!-- Height -->								
						<div class="aps-d-top" id="height">
							<h2> <?php echo translate_phrase('What are your height requirements')?> ?</h2>
							<div class="f-decrMAIN">
								
								<?php if($use_meters == 1):?>
								<div class="sfp-1-main">
									<div class="Left-coll01"><?php echo translate_phrase('between')?></div>
									<div class="Right-coll01"><?php echo form_dt_dropdown('centemetersFrom',$centemeters,'','class="dropdown-dt feetdd"',translate_phrase('-'),"hiddenfield"); ?>
										<div class="centimeter" style="padding-right: 23px;">to</div>
										<?php echo form_dt_dropdown('centemetersTo',$centemeters,'','class="dropdown-dt feetdd"',translate_phrase('-'),"hiddenfield");?>
										<div class="centimeter pad-rightNone">cm</div>
										<label id="heightRangeError" class="input-hint error"></label>
									</div>
								</div>
								<?php endif; ?>
								
								<?php if($use_meters == 0):?>
								<div class="sfp-1-main">
									<div class="Left-coll01"><?php echo translate_phrase('between')?></div>
									<div class="Right-coll01">
										<?php echo form_dt_dropdown('feetFrom',$feet,'','class="dropdown-dt feetdd"',translate_phrase('Please select'),"hiddenfield");?>
										<div class="centimeter"> <?php echo translate_phrase('feet')?> </div>
										<?php echo form_dt_dropdown('inchFrom',$inches,'','class="dropdown-dt inchdd"',translate_phrase('Please select'),"hiddenfield");?>
										<div class="centimeter pad-rightNone"><?php echo translate_phrase('inches')?></div>
										<label id="heightRangeError" class="input-hint error "></label>
									</div>
								</div>
								
								<div class="sfp-1-main">
									<div style="margin-left: 45px" class="Left-coll01"> <?php echo translate_phrase('to')?></div>
									<div class="Right-coll01">
										<?php echo form_dt_dropdown('feetTo',$feet,'','class="dropdown-dt feetdd"',translate_phrase('Please select'),"hiddenfield"); ?>
										<div class="centimeter"><?php echo translate_phrase('feet')?></div>
										<?php echo form_dt_dropdown('inchTo',$inches,'','class="dropdown-dt inchdd"',translate_phrase('Please select'),"hiddenfield"); ?>
										<label id="heightRangeError" class="input-hint error"></label>
										<div class="centimeter pad-rightNone"><?php echo translate_phrase('inches')?></div>
									</div>
								</div>										
								<?php endif;?>

								<div class="f-decr importanceRange">
									<ul>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Very Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Not Important')?></a></li>
									</ul>
									<input type="hidden" name="wantHeightImportance" id="wantHeightImportance">
								</div>
							</div>
						</div>
						
						<?php 
							break;
							case "personality":
						?>
						<!-- Personality -->
						<div class="aps-d-top" id="personality">
							<h2>
								<label class="left"><?php echo translate_phrase('What types of personalities do you like?')?> </label>
								<a class="disable-butn left no-margin" onclick="select_toggle(this,'customSelectTag')" href="javascript:;"><?php echo translate_phrase('Select All')?></a>
							</h2>
							
							<div class="f-decrMAIN">
								<div class="f-decr customSelectTag">
								<?php
									foreach ($descriptive_word as $key => $value)
									{
										echo '<ul><li><a id="'.$value['descriptive_word_id'].'" class="disable-butn" href="javascript:;" >'.$value['description'].'</a></li></ul>';
									}
								?>
									<input type="hidden" name="personalityPreference" id="personalityPreference">
								</div>
							
								<div class="f-decr importance">
									<ul>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Very Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Not Important')?></a></li>
									</ul>
									<input type="hidden" name="wantPersonalityImportance" id="wantPersonalityImportance">
								</div>									
							</div>
						</div>
						
						<?php 
							break;
							case "education_level":
						?>
						<!--Education Level -->
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
											echo '<ul><li><a id="'.$value['education_level_id'].'" class="disable-butn" href="javascript:;" >'.$value['description'].'</a></li></ul>';
										}
									?>
									<input type="hidden" name="educationPreference" id="educationPreference">
								</div>
								<div class="f-decr importance">
									<ul>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Very Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Not Important')?></a></li>
									</ul>
									<input type="hidden" name="wantEducationImportance" id="wantEducationImportance">
								</div>
							</div>
						</div>
						
						
						<?php 
							break;
							case "job_industry":
						?>
						<!-- Job Industry -->
						<!--<div class="aps-d-top" id="industry">
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
										echo '<ul><li><a id="'.$key.'" class="disable-butn" href="javascript:;" >'.$value.'</a></li></ul>';                  
									}
									?>
									<input type="hidden" name="industryPreference" id="industryPreference">
								</div>
								<div class="f-decr importance">
									<ul>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="1"><?php echo translate_phrase('Very Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="2"><?php echo translate_phrase('Important')?></a></li>
										<li><a class="Intro-Button" href="javascript:;" importanceVal="3"><?php echo translate_phrase('Not Important')?></a></li>
									</ul>
									<input type="hidden" name="wantParticularIndustryImportance" id="wantParticularIndustryImportance">
								</div>
							</div>
						</div>-->							
					
						<?php } //end switch
						
						 }?>								
					</div><!--END edu-main-->	
					<?php endif;?>						
				</div>
			</div>			
			<div class="Nex-mar">
				<div class="Next-butM" onclick="save_data()"><?php echo translate_phrase('Submit My Ideal Match');?></div>
			</div>
		</div>					
		</form>
	</div>
</div>
