<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
            
<?php
if(!empty($gender_want['age_range'])){
    $age=explode('-',$gender_want['age_range']);
    $start_age=$age[0];
    $end_age=$age[1];
}else{
    $start_age = isset($user_data['want_age_range_lower'])?$user_data['want_age_range_lower']:18;
    $end_age = isset($user_data['want_age_range_upper'])?$user_data['want_age_range_upper']:55;
}
?>
<script>

$(document).ready(function () {    
    $(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
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
    
    $(".errorLink").fancybox({
            maxWidth    : 300,
            maxHeight    : 600,
            width        : '70%',
            height       : '70%',
            afterClose: function() {
            },
  
    });
    
  });
  
  
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
        var date_num_tix=$('#date_tickets').val();
        var user_num_tix=$('#user_date_tickets').val();
        
        if(parseFloat(date_num_tix) > parseFloat(user_num_tix)){
            $('#num_tix_error').trigger('click');
            return false;
        }else{
            $("#newdateForm").submit();
        }
        
    }
}
</script>
<?php $user_id = $this->session->userdata('user_id');?>
<!--*********Page start*********-->
<div class="wrapper">
	<div class="content-part mobile-layout">
		<form name="newdate" id="newdateForm"
			action="<?php echo base_url().'dates/new_date_step4';?>"
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
                            
                                <?php
                                        $total=LAST_MINUTE_DATE+@$date_detail['relationship_num_date_tix']+@$date_detail['budget_num_date_tix'];
                                ?>
                                <a href="#showErrorMessage" id="num_tix_error" class="errorLink"></a>
                                <div id="showErrorMessage" style="display: none">
                                    <h4>
                                        Hosting this date will cost you <?php  echo $total;?> date tickets, but you only have <?php  echo $user_data['num_date_tix'];?> date tickets left. Please get more.
                                    </h4>
                                    <a href="<?php echo base_url().url_city_name();?>/get-more-tickets.html" id="ureg_sub" class="Next-butM text-center inline-element">
                                        <?php echo translate_phrase('Get More')?>
                                    </a>
                                </div>
                                <input type="hidden" id="user_date_tickets" name="user_date_tickets" value="<?php  echo $user_data['num_date_tix'];?>">
                                <input type="hidden" id="date_tickets" name="date_tickets" value="<?php  echo $total;?>">
                                
                                
                                <div class="edu-main">
									<div class="btn-group">
					                        <a href='<?php echo base_url('dates/new_date_step3')?>'><span class="disable-butn inline-element"><?php echo translate_phrase('Back')?></span></a>
					                        <button id="ureg_sub" onclick="save_data();" class="btn btn-blue space2"><?php echo translate_phrase('Post Date')?> </button>
					                        
					                </div>
				                </div>
				</div>
			</div>
			<!--*********Apply-Step1-E-Page close*********-->				
		</form>
	</div>
</div>
<!--*********Page close*********-->
