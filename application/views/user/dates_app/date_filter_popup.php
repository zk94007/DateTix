<?php
$user_id = $this->session->userdata('user_id'); 
$date_setting=$this->model_date->get_user_date_setting($user_id);

$start_age=$date_setting['want_age_range_lower'];
$end_age=$date_setting['want_age_range_upper'];
$start_height= 0; //$user_data['want_height_range_lower'];
$end_height= 0 ;//$user_data['want_height_range_upper'];

?>


<script>
$(document).ready(function () {
    
    $('.dateSettingCheckbox ul li input[type="checkbox"]').live('click', function (e) {
            $(this).siblings('a').trigger('click');
        });
        
    $('.dateSettingCheckbox ul li a').live('click',function(e) {
        
    	 e.preventDefault();
            var ele = jQuery(this);
            var li = ele.parent();
            
           
            var hiddenField = jQuery(li).parent().parent().parent().find('input[type="hidden"]');

            if (ele.hasClass('active')) {
                var ids = new Array();
                var hiddenFieldValues = hiddenField.val();
                ids = hiddenFieldValues.split(',');
                var index = ids.indexOf(ele.attr('key'));
                ids.splice(index, 1);
                var newHiddenFieldValues = ids.join();
                ele.removeClass('active');
                var selectedCheckbox = ele.attr('class');
                $(ele).siblings('input[type="checkbox"]').prop('checked', false);
            }
            else {

                var inputValues = jQuery(hiddenField).val();
                if (inputValues != "")
                    var newHiddenFieldValues = inputValues + ',' + ele.attr('key');
                else
                    var newHiddenFieldValues = ele.attr('key');
                selectedCheckbox = ele.attr('class');
                $(ele).addClass('active');
                $(ele).siblings('input[type="checkbox"]').prop('checked', true);
            }
            
            hiddenField.val(newHiddenFieldValues);
            //var selectedCheckbox = hiddenField.attr('id');
            //saveUserDateSetting(selectedCheckbox);
   });
   
   	/* $("#date_time_slider").slider({
            min: 0,
            max: 500,
            step: 10,
            value: '10',
            slide: function (event, ui) {
                //calculateTimeFromSliderValue(ui.value);
                $('#date_time_slider_text').html(ui.value);
                $('#date_setting_distance').val(ui.value);
            }
        });*/
        
        $("#date_setting_age_slider").slider({
            min: 18,
            max: 99,
            step: 1,
            range: true,
            values: [ <?php echo ($start_age) ? $start_age  : '18';?>, <?php echo ($end_age) ? $end_age  : '25';?> ],
            slide: function (event, ui) {
                //calculateTimeFromSliderValue(ui.value);
                
                $('#date_setting_start_age').val(ui.values[ 0 ]);
                $('#date_setting_end_age').val(ui.values[ 1 ]);
                $('#date_setting_age_slider_text').html( "" + ui.values[ 0 ] + " - " + ui.values[ 1 ] )
            }
        });
        
       /*  $( "#date_setting_height_slider" ).slider({
                range: true,
                min: 50,
                max: 250,
                values: [  <?php echo ($start_height) ? $start_height  : '75';?>, <?php echo ($end_height) ? $end_height  : '125';?>],
                slide: function( event, ui ) {
                  //$( ".dateSettingHeight" ).html( "" + ui.values[ 0 ] + " - " + ui.values[ 1 ] );
                  $('#date_setting_start_height').val(ui.values[ 0 ]);
                  $('#date_setting_end_height').val(ui.values[ 1 ]);
                  $('#date_setting_height_slider_text').html( "" + ui.values[ 0 ] + " - " + ui.values[ 1 ] )
                }
              });*/
          
        // to show div parameter values
        $('.head-txt').click(function(){           
           if($(this).next().css('display')=='none'){
               $(this).next().slideDown();
           }else{
               $(this).next().slideUp();
           }    
        });
});   


function saveUserDateSetting(){
	    
    var formData=$('#filterPopup').serializeArray();
    var $inputs = $('#filterPopup :input[type=hidden]');
    // not sure if you wanted this, but I thought I'd add it.
    // get an associative array of just the values.
    var values = {};
    $inputs.each(function() {
        values[this.name] = $(this).val();
    });

    $.ajax({
        url: '<?php echo base_url(); ?>' + "dates/save_user_date_setting",
        type: "post",
        data: {formData:values},
        cache: false,
        success: function (data) {
			window.location.reload();
        }
    });
}

function closeFancyBox(){
	saveUserDateSetting();
   //parent.$.fancybox.close();
   
}
</script>

<div style="clear: both; float: none; ">
    
<div style="width: 320px; " class="mar-top2">
 	
 	<!-- popup content start here -->
    <form  name="filterPopup" id="filterPopup" method="post" action=''> 
        
    
        
<!--                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-distance"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred distance from you'); ?>
                                <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                
                                <div id="date_time_slider" class="column-50"></div>
                                <p id="date_time_slider_text" class="bold-txt">10</p>
                                <input type="hidden" id="date_setting_distance" name="date_setting_distance" value="">
                            </div>
                        </div>                        
                    </div>-->
                
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-datetype"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt">
                                <?php echo translate_phrase('Preferred Date Types'); ?> 
                                <span class="date-icon-arrow"></span>
                            </p>
                            
                            
                            <div class="f-decrMAIN dateSettingCheckbox  <?php if(!$date_setting['date_type']){echo 'dateSettingHide';}?>">
                                <ul> 
                                    <?php $dateType=$this->model_date->get_date_type() ;
									if($dateType):
                                    foreach($dateType as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($val['date_type_id'],explode(',',$date_setting['date_type']))) { echo "checked"; };?>>
                                            <a key="<?php echo $val['date_type_id'];?>" href="javascript:;" class="setting_date_type <?php if(in_array($val['date_type_id'],explode(',',$date_setting['date_type']))) { echo "active"; };?>" >
                                                <?php echo $val['description'];?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; endif;?>
                                    <input type="hidden" id="setting_date_type" name="setting_date_type" value="<?php echo $date_setting['date_type'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                    
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-intention"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Date Intentions'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  <?php if(!$date_setting['relationship_type']){echo 'dateSettingHide';}?>">
                                <ul> 
                                    <?php $dateIntention=$this->model_date->get_relationship_type() ;
                                    foreach($dateIntention as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($val['relationship_type_id'],explode(',',$date_setting['relationship_type']))) { echo "checked";};?>>
                                            <a key="<?php echo $val['relationship_type_id'];?>" href="javascript:;" class="setting_date_intention <?php if(in_array($val['relationship_type_id'],explode(',',$date_setting['relationship_type']))) { echo "active";};?>">
                                                <?php echo $val['description'];?>
                                            </a>
                                        </li>                                      
                                    <?php endforeach; ?>
                                          <input type="hidden" id="setting_date_intention" name="setting_date_intention" value="<?php echo $date_setting['relationship_type'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-gender"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Gender'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  <?php if(!$date_setting['gender_id']){echo 'dateSettingHide';}?>">
                                <ul> 
                                    <?php $dateGender=$this->model_date->get_gender() ;
                                    foreach($dateGender as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($val['gender_id'],explode(',',$date_setting['gender_id']))) { echo "checked";};?>>
                                            <a key="<?php echo $val['gender_id'];?>" href="javascript:;" class="setting_date_gender <?php if(in_array($val['gender_id'],explode(',',$date_setting['gender_id']))) { echo "active";};?>">
                                                <?php echo $val['description'];?>
                                            </a>
                                        </li>                                       
                                    <?php endforeach; ?>
                                         <input type="hidden" id="setting_date_gender" name="setting_date_gender" value="<?php echo $date_setting['gender_id'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                        	<span class="dt-icon filter-icon-padding icon-date-age"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Age'); ?> <span class="date-icon-arrow"></span></p>
                            <div class="f-decrMAIN dateSettingCheckbox">
                                <input type="hidden" id="date_setting_start_age" name="date_setting_start_age" value="<?php echo $start_age;?>">
                                <input type="hidden" id="date_setting_end_age" name="date_setting_end_age" value="<?php echo $end_age;?>">
                                <div class="column-50">
                                	<div  id="date_setting_age_slider"></div> 
                                </div>
                                <div class="column-50"><p class="bold-txt" style="padding-left: 20px" id="date_setting_age_slider_text" ><?php echo ($start_age && $end_age) ? $start_age."-".$end_age : "18-99";?></p></div>                                
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-ethnicity"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Ethnicity'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  <?php if(!$date_setting['ethnicity_id']){echo 'dateSettingHide';}?>">
                                <ul> 
                                    <?php $dateEthnicity=$this->model_date->get_ethnicity() ;
                                    foreach($dateEthnicity as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($val['ethnicity_id'],explode(',',$date_setting['ethnicity_id']))) { echo "checked";};?>>
                                            <a key="<?php echo $val['ethnicity_id'];?>" href="javascript:;" class="setting_date_ethnicity <?php if(in_array($val['ethnicity_id'],explode(',',$date_setting['ethnicity_id']))) { echo "active";};?>">
                                                <?php echo $val['description'];?>
                                            </a>
                                        </li>                                    
                                    <?php endforeach; ?>
                                            <input type="hidden" id="setting_date_ethnicity" name="setting_date_ethnicity" value="<?php echo $date_setting['ethnicity_id'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                    
<!--                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-height"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Height'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <p class="dateSettingHeight"></p>
                                <input type="hidden" id="date_setting_start_height" name="date_setting_start_height" value="<?php echo $start_height;?>">
                                <input type="hidden" id="date_setting_end_height" name="date_setting_end_height" value="<?php echo $end_height;?>">
                                <div id="date_setting_height_slider" class="column-50"></div>    
                                 <p id="date_setting_height_slider_text"  class="bold-txt"><?php echo ($start_height && $end_height) ? $start_height."-".$end_height : "75-125";?></p>
                            </div>
                        </div>                        
                    </div>
                    
                    
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-bodytype"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Body types'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateBodyType = array(); //$this->model_date->get_body_type($this -> language_id) ;
                                    foreach($dateBodyType as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($k,explode(',',$date_setting['body_type_id']))) { echo "checked";};?>>
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_bodytype">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_bodytype" name="setting_date_bodytype" value="<?php echo $date_setting['body_type_id'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-language"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Spoken languages'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateSpokenLanguage = array(); //$this->model_date->get_spoken_language($this -> language_id) ;
                                    foreach($dateSpokenLanguage as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" >
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_language">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_language" name="setting_date_language" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-relationship"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Relationship statuses'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateRelationshipStatus = array(); //$this->model_date->get_relationship_status($this -> language_id) ;
                                    foreach($dateRelationshipStatus as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($k,explode(',',$date_setting['relationship_status']))) { echo "checked";};?>>
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_relationship_status">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_relationship_status" name="setting_date_relationship_status" value="<?php echo $date_setting['relationship_status'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-religious"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Religious Beliefs'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateReligious = array(); //$this->model_date->get_religious_belief($this -> language_id) ;
                                    foreach($dateReligious as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($k,explode(',',$date_setting['religious_belief']))) { echo "checked";};?>>
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_religious">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_religious" name="setting_date_religious" value="<?php echo $date_setting['religious_belief'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-personality"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Personalities'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $datePersonality = array(); //$this->model_date->get_descriptive_word($this -> language_id) ;
                                    foreach($datePersonality as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($k,explode(',',$date_setting['descriptive_word']))) { echo "checked";};?>>
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_personalities">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_personalities" name="setting_date_personalities" value="<?php echo $date_setting['descriptive_word'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-smoking"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Smoking Frequency'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateSmoking = array(); //$this->model_date->get_smoking_status($this -> language_id) ;
                                    foreach($dateSmoking as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($k,explode(',',$date_setting['smoking_status']))) { echo "checked";};?>>
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_smoking">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_smoking" name="setting_date_smoking" value="<?php echo $date_setting['smoking_status'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-drinking"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Drinking Frequency'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateDrinking = array(); //$this->model_date->get_drinking_status($this -> language_id) ;
                                    foreach($dateDrinking as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($k,explode(',',$date_setting['drinking_status']))) { echo "checked";};?>>
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_drinking">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_drinking" name="setting_date_drinking" value="<?php echo $date_setting['drinking_status'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-exercise"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Exercise Frequency'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateExercise = array(); //$this->model_date->get_exercise_frequency($this -> language_id) ;
                                    foreach($dateExercise as $k=>$val):
                                    ?>
                                        <li class="inline-element">
                                            <input type="checkbox" <?php if(in_array($k,explode(',',$date_setting['exercise_frequency']))) { echo "checked";};?>>
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_exercise">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_exercise" name="setting_date_exercise" value="<?php echo $date_setting['exercise_frequency'];?>">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-hobby"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Hobbies'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateHobby = array(); //$this->model_date->getInterests($this -> language_id) ;
                                    foreach($dateHobby['childDetails'][1] as $k=>$val):
                                       
                                    ?>
                                        <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $val->interest_id;?>" href="javascript:;" class="setting_date_hobby">
                                                <?php echo $val->description;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_hobby" name="setting_date_hobby" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-hobby"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Hangouts'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php //$dateHangout=$this->model_date->get_exercise_frequency($this -> language_id) ;
                                    foreach($dateHobby['childDetails'][2] as $k=>$val):
                                    ?>
                                        <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $val->interest_id;?>" href="javascript:;" class="setting_date_hangouts">
                                                <?php echo $val->description;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_hangouts" name="setting_date_hangouts" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-music"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Music'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php //$dateMusic=$this->model_date->get_exercise_frequency($this -> language_id) ;
                                    foreach($dateHobby['childDetails'][4] as $k=>$val):
                                    ?>
                                         <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $val->interest_id;?>" href="javascript:;" class="setting_date_music">
                                                <?php echo $val->description;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_music" name="setting_date_music" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-movie"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Movies'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php //$dateMusic=$this->model_date->get_exercise_frequency($this -> language_id) ;
                                    foreach($dateHobby['childDetails'][3] as $k=>$val):
                                    ?>
                                         <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $val->interest_id;?>" href="javascript:;" class="setting_date_movie">
                                                <?php echo $val->description;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_movie" name="setting_date_movie" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-sport"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Sports'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php //$dateSports=$this->model_date->get_exercise_frequency($this -> language_id) ;
                                    foreach($dateHobby['childDetails'][4] as $k=>$val):
                                    ?>
                                        <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $val->interest_id;?>" href="javascript:;" class="setting_date_sport">
                                                <?php echo $val->description;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_sport" name="setting_date_sport" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
                    
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div>
                            <h2>Premium Filters</h2>
                            <p>Upgrade your account to gain access to premium filters to receive more personalized and higher quality matches</p>
                        </div>
                                                
                    </div>
        
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-home"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Housing'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateHousing = array(); //$this->model_date->get_residence_type($this -> language_id) ;
                                    foreach($dateHousing as $k=>$val):
                                    ?>
                                        <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_house">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_house" name="setting_date_house" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
        
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-education-level"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Education Levels'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateEducationLevel = array(); //$this->model_date->get_education_level($this -> language_id) ;
                                    foreach($dateEducationLevel as $k=>$val):
                                    ?>
                                        <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_education_level">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_education_level" name="setting_date_education_level" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
        
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-school"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Schools'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateSchool = array(); //$this->model_date->get_school($this -> language_id) ;
                                    foreach($dateSchool as $k=>$val):
                                    ?>
                                        <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_school">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_school" name="setting_date_school" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
        
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-income"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Income Level'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php //$dateSports=$this->model_date->get_exercise_frequency($this -> language_id) ;
                                    //foreach($dateHobby['childDetails'][4] as $k=>$val):
                                    ?>
                                        <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $val->interest_id;?>" href="javascript:;" class="setting_date_income">
                                                <?php echo $val->description;?>
                                            </a>
                                        </li>                                        
                                    <?php //endforeach; ?>
                                        <input type="hidden" id="setting_date_income" name="setting_date_income" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
        
                    <div class="aps-d-top inline-element left mar-top2 border-bottom-mar-bottom">
                        <div class="fl setting-left-icon">
                               <span class="dt-icon filter-icon-padding icon-date-company"></span>
                        </div>
                        <div class="fl align-left setting-right-details">                        
                            <p class="head-txt"><?php echo translate_phrase('Preferred Companies'); ?>
                            <span class="date-icon-arrow"></span>
                            </p>
                            <div class="f-decrMAIN dateSettingCheckbox  dateSettingHide">
                                <ul> 
                                    <?php $dateCompany = array(); //$this->model_date->get_company($this -> language_id) ;
                                    foreach($dateCompany as $k=>$val):
                                    ?>
                                        <li class="inline-element"><input type="checkbox">
                                            <a key="<?php echo $k;?>" href="javascript:;" class="setting_date_company">
                                                <?php echo $val;?>
                                            </a>
                                        </li>                                        
                                    <?php endforeach; ?>
                                        <input type="hidden" id="setting_date_company" name="setting_date_company" value="">
                                </ul>
                            </div>
                        </div>                        
                    </div>
        -->
                    
                <!-- popup ends here -->
    <div class="div-row align-left">
       <input type="button" class="btn btn-blue" onclick="return closeFancyBox()" value="OK" id="closefancybox" />
    </div>
    
    </form>        
</div>
</div>
