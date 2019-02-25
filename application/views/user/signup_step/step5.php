<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script type="text/javascript">
$(document).ready(function () {
   
	$('.your-personality ul li a').click(function(e) {
		e.preventDefault();
		var li = $(this).parent();
		if ($(li).hasClass('selected')) {
		  // remove
		  var ids           = new Array();
		  var desc_id       = $('#descriptive_word_id').val(); 
		  ids               = desc_id.split(',');
		  var index         = ids.indexOf(this.id);
		  ids.splice(index, 1);
		  var descriptive_id      = ids.join(); 
		  $("#descriptive_word_id").val(descriptive_id);
		  $(li).removeClass('selected').find('a').removeClass('appr-cen').addClass('disable-butn');
		} else {
		  // check before adding
		  if ($('#your-personality ul li.selected').length < 5) {
			var descriptive_id   = $('#descriptive_word_id').val();
			if(descriptive_id!="")
				var dsc_id       = descriptive_id+','+this.id; 
			else
				var dsc_id       = this.id;

			$("#descriptive_word_id").val(dsc_id);
			$(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
		  }
		}
   });
	
	/*---------------for managing interests------------------*/
	$('.hobbiesAndInterest ul li a').click(function(e) {
		e.preventDefault();
		var li = $(this).parent();
		if ($(li).hasClass('selected')) {
		  // remove
		  var ids           = new Array();
		  var desc_id       = $('#interestWordId').val(); 
		  ids               = desc_id.split(',');
		  var index         = ids.indexOf(this.id);
		  ids.splice(index, 1);
		  var descriptive_id      = ids.join(); 
		  $("#interestWordId").val(descriptive_id);
		  $(li).removeClass('selected').find('a').removeClass('appr-cen').addClass('disable-butn');
		} else {
		  // check before adding
		  //if ($('#your-personality ul li.selected').length < 5) {
			var descriptive_id   = $('#interestWordId').val();
			if(descriptive_id!="")
				var dsc_id       = descriptive_id+','+this.id; 
			else
				var dsc_id       = this.id;

			$("#interestWordId").val(dsc_id);
			$(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
		  //}
		}
	});
	
	$("#submit_button").click(function(){
		$('#signupForm').submit();
	})
});

</script>
<!--*********Apply-Step1-A-Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="signup" id="signupForm"
			action="<?php echo base_url().url_city_name() . '/'.$this->singup_name.'-5.html';?>"
			method="post" enctype="multipart/form-data">
			
			<label class="input-hint error"><?php echo $this->session->flashdata('edit_profile_msg_error');?></label>
			<!--*********Apply-Step1-E-Page personality start*********-->
			<div class="Apply-Step1-a-main">
				<!-- <div class="subttle">&nbsp;</div>-->
				<!--<div class="A-step-partM">
					<div class="step-backBG">
						<div class="step-BOX-Main">
							<div class="step-bg-selected">
								<span>1</span>
							</div>
							<div class="step-ttle">
							<?php echo translate_phrase('Describe Yourself')?>
							</div>
						</div>
						<div class="step-BOX-Main mar-auto">
							<div class="step-bg-Unselected">
								<span>2</span>
							</div>
							<div class="step-ttle">
							<?php echo translate_phrase('Your Dating Preferences')?>
							</div>
						</div>
						<div class="step-BOX-Main fr wh-clr">
							<div class="step-bg-Unselected">
								<span>3</span>
							</div>
							<div class="step-ttle">
							<?php echo translate_phrase('Submit Application')?>
							</div>
						</div>
					</div>
				</div>-->
				<div class="step-form-Main">
					<div class="step-form-Part">
						<div class="edu-main">
							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('What five words would your friends use to describe you')?> ?</h2>
								<div class="f-decrMAIN your-personality">
									<div class="f-decr">
										<ul>
											<?php foreach($descriptive_word as $row){?>
											<li id="<?php echo $row['descriptive_word_id'];?>"><a
												class="disable-butn" href="javascript:;"
												id="<?php echo $row['descriptive_word_id'];?>"><?php echo ucfirst($row['description']);?>
											</a></li>
											<?php }?>
										</ul>
										<input type="hidden" id="descriptive_word_id"
											name="descriptive_word_id" value="">
									</div>
								</div>
							</div>

							<!-----------------------NEW ADDITION--------------------------->
							<div class="aps-d-top">
							<?php
							if(!empty($interests))
							{
								echo '<h2>'.translate_phrase('What are your interests and hobbies').'?</h2>';
								foreach ($interests['parentDetails'] as $id => $catName)
								{
									echo '<div class="f-decrMAIN hobbiesAndInterest">
							   <h3>'.$catName.'</h3>
							   <div class="f-decr">
							   <ul>';
									foreach ($interests['childDetails'][$id] as $key => $value)
									{
										echo '<li id ="'.$value->interest_id.'"><a class="disable-butn" href="javascript:;" id="'.$value->interest_id.'">'.$value->description.'</a></li>';
									}

									echo '</ul></div>
							  </div>';
								}

								echo '<input type="hidden" id="interestWordId" name="interests">';
							}
							?>

							</div>
						</div>
					</div>							
				</div>
				<div class="Nex-mar">
					<input id="submit_button" type="button" class="Next-butM" value="<?php echo translate_phrase('Submit My Profile')?>">
				</div>
			</div>
			<!--*********Apply-Step1-E-Page close*********-->				
		</form>
	</div>
</div>
