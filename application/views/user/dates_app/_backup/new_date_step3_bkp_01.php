<script src="<?php echo base_url()?>assets/js/general.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
  
  
<script>

$(document).ready(function () {    
    $(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});

    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 100,
      values: [ 0, 1 ],
      slide: function( event, ui ) {
        $( "#amount" ).html( "" + ui.values[ 0 ] + " - " + ui.values[ 1 ] );
        $('#start_age').val(ui.values[ 0 ]);
        $('#end_age').val(ui.values[ 1 ]);
        
      }
    });
    $( "#amount" ).html( "" + $( "#slider-range" ).slider( "values", 0 ) +
      " - " + $( "#slider-range" ).slider( "values", 1 ) );
  });
</script>
<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_INSTANT_INTRO);
?>
<!--*********Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="newdate" id="newdateForm"
			action="<?php echo base_url().'dates/new_date_step3';?>"
			method="post">
			
			<!--*********Apply-Step1-E-Page personality start*********-->
			<div class="Apply-Step1-a-main">				
				<div class="step-form-Main">
					<div class="step-form-Part">
						<div class="edu-main">
							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('What would you like to invite to apply to this date')?> ?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
											<?php foreach($gender_list as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo $row['gender_id']; ?>"> 
                                                                                                <span
                                                                                                    class="disable-butn"><?php echo $row['description']; ?>
                                                                                                </span>
                                                                                            </a>
											<?php }?>										
										<input type="hidden" id="gender" name="gender" value="">
									</div>
								</div>                                                                
							</div>							
						</div>
					</div>							
				
					<div class="step-form-Part">
						<div class="edu-main">							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('Age')?></h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
                                                                            <p id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;"></p>																				
									</div>
                                                                        <input type="hidden" id="start_age" name="start_age" value="">
                                                                        <input type="hidden" id="end_age" name="end_age" value="">
                                                                        <div id="slider-range"></div>
								</div>                                                                
							</div>							
						</div>                                            
					</div>
                                    
                                        <div class="step-form-Part">
						<div class="edu-main">							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('Ethnicity')?></h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
											<?php foreach($ethnicity_list as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo $row['ethnicity_id']; ?>"> 
                                                                                                <span
                                                                                                    class="disable-butn"><?php echo $row['description']; ?>
                                                                                                </span>
                                                                                            </a>
											<?php }?>										
										<input type="hidden" id="ethnicity" name="ethnicity" value="">
									</div>
								</div>                                                                
							</div>							
						</div>                                            
					</div>
				</div>
                            
				<div class="Nex-mar">
					<input id="submit_button" type="submit" class="Next-butM" value="<?php echo translate_phrase('Next')?>">
				</div>
			</div>
			<!--*********Apply-Step1-E-Page close*********-->				
		</form>
	</div>
</div>
<!--*********Page close*********-->
