<script>

$(document).ready(function () {    
    $(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
                var selected=($(this).parent().find(':input[type="hidden"]').attr('id'));
                
                var selected=($(this).parent().find(':input[type="hidden"]').attr('id'));
                if(selected=='looking_for'){
                    var value=$('#looking_for').val();
                    displaymessage(value)
                 }
	});
});

function date_step2_validaion(){
                var flag=1; 
                if($('#date_type').val()==''){
                    showError('datetypeError','<?php echo translate_phrase("Please select the kind of date you want to have")?>');
                    flag=0;
                }
                else
                {
                    jQuery('#datetypeError').text('');
                }

                if($('#looking_for').val()==''){
                    showError('relationshipError','<?php echo translate_phrase("Please select what you are looking for on this date")?>');
                    flag=0;
                }
                else
                {
                    jQuery('#relationshipError').text('');
                }
                
                if($('#date_payer').val()==''){
                    showError('datepayerError','<?php echo translate_phrase("Please select who will pay for this date")?>');
                    flag=0;
                }
                else
                {
                    jQuery('#datepayerError').text('');
                }
                

                if(flag==0)
                    return false;
                else   
                    return true;

             }
             
            function save_data()
            {
                    if(date_step2_validaion())
                    {
                            $("#newdateForm").submit();
                }
            }
            
            function displaymessage(id){
            $.ajax({ 
                        url: '<?php echo base_url(); ?>' +"dates/get_looking_message", 
                        type:"post",
                        data:{id:id},
                        cache: false,
                        success: function (data) {
                            $("#message").html(data);
                        }     
                    });
}
</script>
<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_INSTANT_INTRO);
?>
<!--*********Page start*********-->
<div class="wrapper">
	<div class="content-part mobile-layout">
		<form name="newdate" id="newdateForm"
			action="<?php echo base_url().'dates/new_date_step2';?>"
			method="post">
			
			<!--*********Apply-Step1-E-Page personality start*********-->
			<div class="Apply-Step1-a-main">				
				<div class="step-form-Main">
					<div class="step-form-Part">
						<div class="edu-main">
							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('What kind of date do you want to have')?>?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">										
										<?php foreach($date_type as $key=>$row){?>											                                                                           
                                            <a href="javascript:;" class="rdo_div"
                                                    key="<?php echo $row['date_type_id']; ?>"> 
                                                <?php if(!empty($last_host_date) && $last_host_date['date_type_id']==$row['date_type_id']):?>
                                                    <span class="appr-cen"><?php echo $row['description']; ?></span>
                                                <?php else:?>
                                                    <span class="disable-butn"><?php echo $row['description']; ?></span>
                                                <?php endif;?>
                                                
                                            </a>
										<?php }?>										
										<input type="hidden" id="date_type" name="date_type" value="<?php echo @$last_host_date['date_type_id'];?>">
                                    	<label id="datetypeError" class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>                                                                
							</div>							
						</div>
					</div>							
				
					<div class="step-form-Part">
						<div class="edu-main">							
							
								<h2> <?php echo translate_phrase('What are you looking for on this date')?>?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">										
											<?php foreach($relationship_type as $key=>$row){?>											                                                                           
		                                    <a href="javascript:;" class="rdo_div"
		                                            key="<?php echo $row['relationship_type_id']; ?>"> 
		                                            
		                                        <?php if(!empty($last_host_date) && $last_host_date['date_intention_id']==$row['relationship_type_id'] ):?>
		                                            <span class="appr-cen"><?php echo $row['description']; ?></span>
		                                        <?php else:?>
		                                            <span class="disable-butn"><?php echo $row['description']; ?></span>
		                                        <?php endif;?>
		                                        
		                                    </a>
											<?php }?>										
										<input type="hidden" id="looking_for" name="looking_for" value="<?php echo @$last_host_date['date_intention_id'];?>">
                                                                                <label id="relationshipError" class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>                                                                
														
						</div>
						<p id="message" class="DarkGreen-color font-italic page-msg-box" ></p>
					</div>
                                    
                                        <div class="step-form-Part">
						<div class="edu-main">							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('Who will pay for this date')?>?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
											<?php foreach($date_payer as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo $row['date_payer_id']; ?>"> 
                                                                                                <?php if(!empty($last_host_date) && $last_host_date['date_payer_id']==$row['date_payer_id']):?>
                                                                                                    <span class="appr-cen"><?php echo $row['description']; ?></span>
                                                                                                <?php else:?>
                                                                                                    <span class="disable-butn"><?php echo $row['description']; ?></span>
                                                                                                <?php endif;?>
                                                                                            </a>
											<?php }?>										
										<input type="hidden" id="date_payer" name="date_payer" value="<?php echo @$last_host_date['date_payer_id'];?>">
                                                                                <label id="datepayerError" class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>                                                                
							</div>							
						</div>                                            
					</div>
				</div>
				<div class="edu-main">
					<div class="btn-group">
	                        <a href='<?php echo base_url('dates/new_date_step1')?>'><span class="disable-butn inline-element"><?php echo translate_phrase('Back')?></span></a>
	                        <button id="ureg_sub" onclick="save_data();" class="btn btn-blue space2"><?php echo translate_phrase('Next')?> </button>
	                        
	                </div>
                </div>
                
			</div>
			<!--*********Apply-Step1-E-Page close*********-->				
		</form>
	</div>
</div>
<!--*********Page close*********-->
