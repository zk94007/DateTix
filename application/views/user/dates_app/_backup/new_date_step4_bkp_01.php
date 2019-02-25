<script src="<?php echo base_url()?>assets/js/general.js"></script>

<script>
var base_url = '<?php echo base_url() ?>';
var user_id = '<?php echo $this->session->userdata('user_id');?>';
$(document).ready(function () {  
    
    $(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		//$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
                $('#merchant_id').val($(this).attr('key'));
	});
        
    $('.customSelectTag ul li a').live('click',function(e) {
    	e.preventDefault();
        var ele = jQuery(this);
        var li = ele.parent();
        var hiddenField = jQuery(li).parent().parent().find('input[type="hidden"]');

		if ($(li).hasClass('selected')) {
          // remove
          var ids                  = new Array();
          var hiddenFieldValues    = $(li).parent().parent().find('input[type="hidden"]').val(); 
          ids                      = hiddenFieldValues.split(',');
          var index                = ids.indexOf(ele.attr('id'));
          ids.splice(index, 1);
          var newHiddenFieldValues = ids.join(); 
          jQuery(hiddenField).val(newHiddenFieldValues);
          $(li).removeClass('selected').find('a').removeClass('appr-cen').addClass('disable-butn');
           //count how many prefrences are selected.if 0 and importance is selected then unselect the importance and clear its hidden fileds value.
           unSelectImporance(ele);
        } 
       	else {
          //check before adding
          
          var prefrencesId   = jQuery(hiddenField).val();
          if(prefrencesId !="")
          	var dsc_id       = prefrencesId+','+ele.attr('id'); 
          else
          	var dsc_id       = ele.attr('id');

  			$(hiddenField).val(dsc_id);
        	$(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
        }

		var allSelected = true;
		//de-select 
		$.each($(li).parent().parent().find('ul'),function(i,item){
            if(!$(item).find('li').hasClass('selected'))
            {
            	allSelected = false;
            }
        });

		if(allSelected)
		{
			$(li).parent().parent().parent().siblings('h2').find('a').fadeOut();//removeClass('disable-butn').addClass('appr-cen');
		}
		else
		{
			$(li).parent().parent().parent().siblings('h2').find('a').fadeIn();//removeClass('appr-cen').addClass('disable-butn');
		}
   });
    
   /* $(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});*/
        
    $(".dropdown-dt").find('dt a').live('click',function () {
		$(this).parent().parent().find('ul').toggle();
    });
    
    $(".dropdown-dt dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
                $(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'))
                $(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
                
                
	});
        
    $(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("dropdown-dt"))
        	$(".dropdown-dt dd ul").hide();
    });
    
    $('#current_country ul li a').live('click',function(){
		loading();
		$.ajax({ 
                        url: '<?php echo base_url(); ?>' +"signup/get_city_by_country", 
                        type:"post",
                        data:{id:$(this).attr('key')},
                        cache: false,
                        success: function (data) {
                            stop_loading();
                            $("#current_city_dropdown").html(data);
                        }     
                    });
	});
        
        
    $(".customSelectTag ul li a").live('click',function(){                
                var neighbourhood=$('#neighbourhood').val();
                var cuisine=$('#cuisine').val();
                var budget_id=$('#budget_id').val();
                var sortby=$('#sortby').val();
                
		loading();
		$.ajax({ 
                        url: '<?php echo base_url(); ?>' +"dates/get_merchant_list", 
                        type:"post",
                        data:{neighbourhood:neighbourhood,cuisine:cuisine,budget_id:budget_id,sortby:sortby},
                        cache: false,
                        success: function (data) {
                            stop_loading();
                            $("#merchantList").html(data);
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
			action="<?php echo base_url().'dates/new_date_step4';?>"
			method="post">
			
			<!--*********Apply-Step1-E-Page personality start*********-->
			<div class="Apply-Step1-a-main">				
				<div class="step-form-Main">
                                    
                                        <h2> <?php echo translate_phrase('Where do you want to host your date')?> ?</h2>
					<div class="step-form-Part">
						<div class="edu-main">
							
							<div class="aps-d-top">
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">																														
										<div class="scemdowndomain menu-Rightmar">
                                                                                        <?php echo form_dt_dropdown('country',$country,'','id="current_country" class="dropdown-dt scemdowndomain menu-Rightmar" ', translate_phrase('Select country'), "hiddenfield");?>	
                                                                                        <label id="liveInError" class="input-hint error error_indentation error_msg"></label>
                                                                                </div>
                                                                                <div class="scemdowndomain">
                                                                                        <div id="current_city_dropdown"></div>
                                                                                        <label id="liveInCITYError" class="input-hint error error_indentation error_msg"></label>
                                                                                </div>
									</div>
								</div>                                                                
							</div>							
						</div>
					</div>							
				
					<div class="step-form-Part">
						<div class="edu-main">							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('Neighbourhood')?></h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
											<?php foreach($neighbourhood_list as $key=>$row){?>
                                                                                                <ul>
                                                                                                    <li>
                                                                                                        <a id="<?php echo $row['neighborhood_id']; ?>" class="disable-butn" href="javascript:;" >
                                                                                                            <?php echo $row['description']; ?>
                                                                                                        </a>
                                                                                                    </li>
                                                                                                </ul>

											<?php }?>									
										<input type="hidden" id="neighbourhood" name="neighbourhood" value="">
									</div>
								</div>                                                                
							</div>							
						</div>                                            
					</div>
                                    
                                        <div class="step-form-Part">
						<div class="edu-main">							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('Cuisine')?></h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
											<?php foreach($cuisine_list as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo $row['cuisinet_id']; ?>"> 
                                                                                                <span
                                                                                                    class="disable-butn"><?php echo $row['description']; ?>
                                                                                                </span>
                                                                                            </a>
											<?php }?>										
										<input type="hidden" id="cuisinet" name="cuisine" value="">
									</div>
								</div>                                                                
							</div>							
						</div>                                            
					</div>
                                    
                                        <div class="step-form-Part">
						<div class="edu-main">							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('Budget')?></h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
											<?php foreach($budget_list as $key=>$row){?>											                                                                           
                                                                                            <a href="javascript:;" class="rdo_div"
                                                                                                    key="<?php echo $row['merchant_budget_id']; ?>"> 
                                                                                                <span
                                                                                                    class="disable-butn"><?php echo $row['description']; ?>
                                                                                                </span>
                                                                                            </a>
											<?php }?>										
										<input type="hidden" id="budget_id" name="budget_id" value="">
									</div>
								</div>                                                                
							</div>							
						</div> 
                                            <p>Casual Venues will cost you 5 date tickets</p>
					</div>
                                    
                                        <div class="step-form-Part">
						<div class="edu-main">							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('Sort By :')?></h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
										
                                                                                    <?php foreach($sortby_list as $key=>$row){?>											                                                                           
                                                                                        <a href="javascript:;" class="rdo_div"
                                                                                                key="<?php echo $row; ?>"> 
                                                                                            <span
                                                                                                class="disable-butn"><?php echo $row; ?>
                                                                                            </span>
                                                                                        </a>
                                                                                    <?php }?>										
										<input type="hidden" id="sortby" name="sortby" value="">																				
									</div>
								</div>                                                                
							</div>							
						</div> 
                                           
					</div>
                                        
                                        <div class="step-form-Part" id="merchantList"></div>
                                    
                                    
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
