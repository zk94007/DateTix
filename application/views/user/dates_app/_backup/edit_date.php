
<?php
if(!empty($gender_want['age_range'])){
    $age=explode('-',$gender_want['age_range']);
    $start_age=$age[0];
    $end_age=$age[1];
}else{
    $start_age=$user_data['want_age_range_lower'];
    $end_age=$user_data['want_age_range_upper'];
}
?>
<script src="<?php echo base_url()?>assets/js/general.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script type="text/javascript">
$(document).ready(function () {
	 $( "#slider-range" ).slider({
      range: true,
      min: 18,
      max: 99,
      values: [ <?php echo (!empty($start_age)) ? $start_age : 0;?>, <?php echo (!empty($end_age)) ? $end_age : 1;?> ],
      slide: function( event, ui ) {
        $( "#amount" ).html( "" + ui.values[ 0 ] + " - " + ui.values[ 1 ] );
        $('#start_age').val(ui.values[ 0 ]);
        $('#end_age').val(ui.values[ 1 ]);
        
      }
    });
    $( "#amount" ).html( "" + $( "#slider-range" ).slider( "values", 0 ) + " - " + $( "#slider-range" ).slider( "values", 1 ) );
    
    
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
});
function calculateTimeFromSliderValue(sliderValue)
{
	var hours = Math.floor(sliderValue/ 60);
    var minutes = sliderValue - (hours * 60);
    if (hours.length == 1) hours = '0' + hours;
    if (minutes.length == 1) minutes = '0' + minutes;
    if (minutes == 0) minutes = '00';        
    if (hours == 0) hours= '00';
  	$( "#date_time_slider_text" ).html( hours + ':' + minutes);
  	$("#date_time").val(hours + ':' + minutes);  	
}

 
  function date_step3_validaion(){
    var flag=1; 
    if($('#gender').val()==''){
        showError('genderError','<?php echo translate_phrase("Please select the gender of the people you want to invite to this date")?>');
        flag=0;
    }
    else
    {
        jQuery('#genderError').text('');
    }

    if($('#start_age').val()=='' || $('#end_age').val() ==''){
        showError('ageError','<?php echo translate_phrase("Please select the age range of the people you want to invite to this date")?>');
        flag=0;
    }
    else
    {
        jQuery('#ageError').text('');
    }
    
    if($('#ethnicity').val()==''){
        showError('ethnicityError','<?php echo translate_phrase("Please select the ethnicity of the people you want to invite to this date")?>');
        flag=0;
    }
    else
    {
        jQuery('#ethnicityError').text('');
    }
    

    if(flag==0)
        return false;
    else   
        return true;

 }
 
function save_data()
{
    if(date_step3_validaion())
    {
         $("#newdateForm").submit();
    }
}
</script>
<!--*********Page start*********-->

<div class="wrapper">
	<div class="content-part">
		<form name="newdate" id="newdateForm"
			action="<?php echo base_url().'dates/edit_date/'.$date_info['date_id'];?>"
			method="post">
			
			<!--*********Apply-Step1-E-Page personality start*********-->
			<div class="Apply-Step1-a-main">
				<div class="jen-name">
					<a class="lin-hght34" href="<?php echo base_url('dates/my_dates')?>"><?php echo translate_phrase('Back');?></a>
				</div>
				<h2 class="">
					<?php echo $date_info['date_type'];?> @ <?php echo $date_info['name'].' '.translate_phrase('on').' '. print_date_day($date_info['date_time']).' '.translate_phrase('at').' '.date('h:i A', strtotime($date_info['date_time']));?>
				</h2>				
				<div class="step-form-Main">
					<div class="step-form-Part">
						<div class="edu-main">							
							
							<div class="aps-d-top">
								<h2> <?php echo translate_phrase('What would you like to invite to apply to this date')?> ?</h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">
                                           <h2 style="float: left; width: 90px;"> <?php echo translate_phrase('Gender')?>   &nbsp;&nbsp;&nbsp;</h2>
											<?php foreach($gender_list as $key=>$row){?>
												 <ul>
                                                	<?php if(in_array($row['gender_id'],explode(',',$gender_want['gender_id']))):?>
													<li class="selected">
                                                      <a id="<?php echo $row['gender_id']; ?>" class="appr-cen" href="javascript:;" >
                                                             <?php echo $row['description']; ?>
                                                         </a>  </li>
                                                 <?php else:?>
                                                         <li> <a id="<?php echo $row['gender_id']; ?>" class="disable-butn" href="javascript:;" >
                                                             <?php echo $row['description']; ?>
                                                         </a>  </li>
                                                 <?php endif;?>
												</ul>
											<?php }?>						
										<input type="hidden" id="gender" name="gender" value="<?php echo $gender_want['gender_id'];?>">
										<label id="genderError" class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>                                                                
							</div>		
							
							<div class="aps-d-top">							
								<h2> <?php echo translate_phrase('Age')?></h2>
								<div class="f-decrMAIN">
									<input type="hidden" id="start_age" name="start_age" value="<?php echo $start_age;?>">
	                                <input type="hidden" id="end_age" name="end_age" value="<?php echo $end_age;?>">
	                                <div id="slider-range"></div>
	                                <p id="amount" class="bold-txt"></p>
									<label id="ageError" class="input-hint error error_indentation error_msg"></label>
								</div>                                                                
							</div> 
							
							<div class="aps-d-top">							
								<h2> <?php echo translate_phrase('Ethnicity')?></h2>
								<div class="f-decrMAIN">
									<div class="f-decr customSelectTag">										
									<?php foreach($ethnicity_list as $key=>$row){?>	
                                    <ul><?php if(in_array($row['ethnicity_id'],explode(',',$ethnicity_want['ethnicity_id']))):?>
                                            <li class="selected">
                                             <a id="<?php echo $row['ethnicity_id']; ?>" class="appr-cen" href="javascript:;" >
                                                    <?php echo $row['description']; ?>
                                                </a>
                                                </li>
                                        <?php else:?>
                                        	<li>
                                                 <a id="<?php echo $row['ethnicity_id']; ?>" class="disable-butn" href="javascript:;" >
                                                    <?php echo $row['description']; ?>
                                                </a></li>
                                        <?php endif;?>
                                    </ul>
									<?php }?>										
									<input type="hidden" id="ethnicity" name="ethnicity" value="<?php echo $ethnicity_want['ethnicity_id'];?>">
                                    <label id="ethnicityError" class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>                                                                
							</div> 		                    
						</div>
					</div>							
				</div>
				<div class="Nex-mar">
				<a href="javascript:;" onclick="save_data();" class="Next-butM"><?php echo translate_phrase('Save')?> </a>
                </div>
			</div>
			<!--*********Apply-Step1-E-Page close*********-->				
		</form>
	</div>
</div>
<!--*********Page close*********-->
