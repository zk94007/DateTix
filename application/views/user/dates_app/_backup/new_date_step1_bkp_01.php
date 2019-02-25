<script src="<?php echo base_url()?>assets/js/general.js"></script>

<script>

$(document).ready(function () {    
    $(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});
        
    $("#show_more_date").live('click',function(){                
                var last_date=$('#last_date').val();
                loading();
		$.ajax({ 
                        url: '<?php echo base_url(); ?>' +"dates/get_more_dates/"+last_date, 
                        type:"json",
                        data:{},
                        cache: false,
                        success: function (data) {
                           stop_loading();
                           var html ='';
                           $.each(JSON.parse(data), function(idx, obj) {
                                    html += "<a href='javascript:;' class='rdo_div' key='"+obj.key+"'><span class='disable-butn'>"+obj.value+"</span></a>";
                            });
                            
                            $("#DateListing").append(html);
                            $('#last_date').val($('#DateListing > a:last').attr('key'));
                        }     
                    });
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
			action="<?php echo base_url().'dates/new_date_step1';?>"
			method="post">
			
			<!--*********Apply-Step1-E-Page personality start*********-->
			<div class="Apply-Step1-a-main">
				
				<div class="step-form-Main">
					<div class="step-form-Part">
						<div class="edu-main">
							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('When are you free for a date')?> ?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag" id="DateListing">
										
											<?php foreach($date_list as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo $row; ?>"> 
                                                                                                <span
                                                                                                    class="disable-butn"><?php echo date('l, F jS', strtotime($row));?>
                                                                                                </span>
                                                                                            </a>
											<?php }?>
										
										<input type="hidden" id="date_free_time" name="date_free_time" value="">
									</div>
								</div>
                                                                <input type="hidden" id="last_date" value="<?php echo $row; ?>">
                                                                <a href="javascript:;" id="show_more_date">Show More</a>
                                                                    
                                                                <p><?php echo translate_phrase('Posting last minute date (less than 24 hours to date time) will cost you 15 date tickets ')?></p>
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
