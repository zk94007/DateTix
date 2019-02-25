<script src="<?php echo base_url()?>assets/js/general.js"></script>

<script>

$(document).ready(function () {    
    $(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});
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
			action="<?php echo base_url().'dates/new_date_step2';?>"
			method="post">
			
			<!--*********Apply-Step1-E-Page personality start*********-->
			<div class="Apply-Step1-a-main">				
				<div class="step-form-Main">
					<div class="step-form-Part">
						<div class="edu-main">
							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('What kind of date do you want to have')?> ?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
											<?php foreach($date_type as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo $row['date_type_id']; ?>"> 
                                                                                                <span
                                                                                                    class="disable-butn"><?php echo $row['description']; ?>
                                                                                                </span>
                                                                                            </a>
											<?php }?>										
										<input type="hidden" id="date_type" name="date_type" value="">
									</div>
								</div>                                                                
							</div>							
						</div>
					</div>							
				
					<div class="step-form-Part">
						<div class="edu-main">							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('What are you looking for on this date')?> ?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
											<?php foreach($relationship_type as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo $row['relationship_type_id']; ?>"> 
                                                                                                <span
                                                                                                    class="disable-butn"><?php echo $row['description']; ?>
                                                                                                </span>
                                                                                            </a>
											<?php }?>										
										<input type="hidden" id="looking_for" name="looking_for" value="">
									</div>
								</div>                                                                
							</div>							
						</div>
                                            <p>Serious relationship date will cost you 20 date tickets</p>
					</div>
                                    
                                        <div class="step-form-Part">
						<div class="edu-main">							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('Who will pay for this date')?> ?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
											<?php foreach($date_payer as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo $row['date_payer_id']; ?>"> 
                                                                                                <span
                                                                                                    class="disable-butn"><?php echo $row['description']; ?>
                                                                                                </span>
                                                                                            </a>
											<?php }?>										
										<input type="hidden" id="date_payer" name="date_payer" value="">
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
