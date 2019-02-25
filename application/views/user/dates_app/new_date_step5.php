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
			action="<?php echo base_url().'dates/new_date_step5';?>"
			method="post">
			
			<!--*********Apply-Step1-E-Page personality start*********-->
			<div class="Apply-Step1-a-main">				
				<div class="step-form-Main">
					<div class="step-form-Part">
						<div class="edu-main">
							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('Choose a date package')?> ?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
                                                                                 <?php foreach($package_list as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo @$row['package_id']; ?>"> 
                                                                                                <span
                                                                                                    class="disable-butn"><?php echo $row['description']; ?>
                                                                                                </span>
                                                                                            </a>
											<?php }?>           
										<input type="hidden" id="date_package_id" name="date_package_id" value="">
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
