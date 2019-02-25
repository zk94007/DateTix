<script src="<?php echo base_url()?>assets/js/general.js"></script>
<script type="text/javascript">
var base_url = '<?php echo base_url() ?>';
var user_id = '<?php echo $this->session->userdata('user_id');?>';
$(document).ready(function () {
	 auto_complete_school();
	/*  Dynamic Checkbox based on selecting a Div element*/
	$(".ckb_div_lookfor").live('click',function(){
		if($(this).find('span').hasClass('disable-butn') == true)
		{
			//Add Selected [checked]
			$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
			$(this).find(':input[type="hidden"]').val($(this).attr('key'));
		}
		else
		{
			//Remove Selected [checked]
			$(this).find('span').removeClass('appr-cen').addClass('disable-butn');
			$(this).find(':input[type="hidden"]').val('');
		}
	});
		
});

var autoschool = true;
function auto_complete_school(){
    if(autoschool)
    {
    	$.ajax({ 
            url: '<?php echo base_url(); ?>' +"user/autocomplete_school/", 
            dataType: "json", 
            type:"post",
            cache: false,
            success: function (data) {
            	autoschool = false;
                var i="0";
                var availableTags=new Array();
               	$.each(data,function(i,item) {
                	//console.log(item);
                    availableTags[i] = item.school_name;
                });
                
                $( "#school_name" ).autocomplete({
                	appendTo: "#auto-school-container",
            		minLength: 1,
                        source: availableTags,
                        select : function(){jQuery('#school_error').text('');}
                });
            }     
        });
    }
}
function scrollToDiv(id)
{
	$('body').scrollTo($('#'+id),800,{'axis':'y'});
}
</script>
<!--*********Apply-Step1-A-Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="signup" id="signupForm"
			action="<?php echo base_url().url_city_name() . '/'.$this->singup_name.'-3.html';?>"
			method="post" enctype="multipart/form-data">
			
			<label class="input-hint error"><?php echo $this->session->flashdata('edit_profile_msg_error');?></label>
			<div class="Apply-Step1-a-main">
						<!-- FORM Header start (Progress) -->
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
						<!-- Header complete -->

						<div class="step-form-Main">
							<div class="step-form-Part">
								<div class="Indicate-top">
									*&nbsp;
									<?php echo translate_phrase('Indicates required field')?>
								</div>
								<div class="edu-main">
									<h2>
									<?php echo translate_phrase('What education degrees/certificates have you obtained or are working towards')?>
										?<span class="Redstar">*</span>
									</h2>
									<div class="skill-select">
									<?php foreach($education_level as $row){
										$relationship_status  = (isset($fb_user_data['education_level']) && $fb_user_data['education_level'])?$fb_user_data['education_level']:set_value('education_level');

										if($relationship_status==$row['education_level_id'])
										$checked_class="appr-cen";
										else
										$checked_class= "disable-butn";
										?>
										<a href="javascript:;" class="ckb_div_lookfor"
											key="<?php echo $row['education_level_id'];?>"> <span
											class="<?php echo $checked_class?>"><?php echo translate_phrase(ucfirst($row['description']));?>
										</span> <input type="hidden" name="education_level[]" value="">
										</a>
										<?php } ?>
										<label id="education_level_err" class="input-hint error"></label>
									</div>
									<div class="skill-select">
										<h2>
										<?php echo translate_phrase('Where and what did you study')?>
											?<span class="Redstar">*</span>
										</h2>
										<label id="schoolReqError" class="input-hint error"></label>
									</div>
									<?php if($school_count>0){
										$schol_show         = 'style="display: none;"';
										$schhol_button_show = 'style="display: block;"';
									} else{
										$schol_show          = 'style="display: block;"';
										$schhol_button_show  = 'style="display: none;"';
									}
									?>
									<div class="school-inner-container" id="list_school_main"
									<?php echo $schhol_button_show;?>>
										<div class="study-innr-M" id="list_school">
										<?php
										foreach($user_school_id as $row ){
											$language_id = $this -> session -> userdata('sess_language_id');
											$school_details = $this->model_user->get_school_details($row);											
											echo $list_school = $this->model_user->list_school_details($row, $school_details, $language_id);
										}
										?>
										</div>
										<div class="Edit-Button01" id="add_school_button">
											<a onclick="show_div();" href="javascript:;"><?php echo translate_phrase('Add Another School') ?>
											</a>
										</div>
									</div>

								</div>

								<div class="last-bor"></div>
								<div id="add_schools" class="fl" <?php echo $schol_show;?>>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('School name')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<div class="drop-down-wrapper-full">
												<dl class="schooldowndomain">
													<dt>
														<span> <input id="school_name"
															class="Degree-input school-name" name="school_name"
															type="text" value="" 
															onblur="show_logo();show_school_domain();">
														</span>
													</dt>
													<!-- autocomplete dd -->
													<dd id="auto-school-container"></dd>
												</dl>
												<label id="schoo_name_err" class="input-hint error"></label>
												<label id="school_error" class="input-hint error"></label>
											</div>

											<div class="sch-logoR" id="school_logo"></div>
										</div>
									</div>

									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Degree')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<input id="degree_name" class="Degree-input DegreeInput"
												name="degree_name" type="text"
												placeholder="<?php echo translate_phrase("e.g. Bachelor of Arts");?>">
											<div class="completed-ch">
												<div class="skil-check-area-01">
													<ul>
														<li><span> <input type="checkbox" checked="checked"
																name="is_degree_completed" id="is_degree_completed"
																value="1"> <label class="choice"
																for="is_degree_completed"><?php echo translate_phrase('Completed');?>
															</label> </span>
														</li>
													</ul>
												</div>
											</div>
											<label id="degree_err" class="input-hint error"></label>
										</div>

									</div>

									<div class="sfp-1-main">
										<div class="sfp-1-Left"> <?php echo translate_phrase('School Email')?> : </div>
										<div class="sfp-1-Right">
											<div class="post-input-wrap">
												<input id="school_email_address"
													name="school_email_address" type="text"
													class="post-input" /> <label id="school_email_error"
													class="input-hint error"></label>
											</div>

											<div class="drop-down-wrapper-school_domain">
												<div class="sel-emailR" id="school_domain"></div>
												<label id="school_domain_err" class="input-hint error"></label>
											</div>
										</div>
									</div>
									<!--<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Major(s)')?>
											:
										</div>
										<div class="sfp-1-Right">
											<div class="M-topBut">
												<ul id="add_major" class="list_rows"></ul>
											</div>

											<div class="drop-down-wrapper-full">
											<?php   
											if(isset($school_subject))
												echo form_dt_dropdown('major_id',$school_subject,'','id="major_id" class="dropdown-dt majordowndomain"',translate_phrase("Select major(s)"));
											?>
												<label id="major_err" class="input-hint error"></label>
											</div>

											<div class="add-butM">
												<input type="hidden" name="majors_id" id="majors_id"
													value=""> <a href="javascript:;" onclick="add_majors();"
													class="Edit-Button01"><?php echo translate_phrase('Add')?>
												</a>
											</div>

										</div>
									</div>

									<div class="sfp-1-main">

										<div class="sfp-1-Left">
										<?php echo translate_phrase('Minor(s)')?>
											:
										</div>
										<div class="sfp-1-Right">
											<div class="M-topBut">
												<ul id="add_minor" class="list_rows"></ul>
											</div>
											<div class="drop-down-wrapper-full">
											<?php 
												if(isset($school_subject))
													echo form_dt_dropdown('major_id',$school_subject,'','id="minor_id" class="dropdown-dt majordowndomain"',translate_phrase("Select minor(s)"));?>
												<label id="minor_err" class="input-hint error"></label>
											</div>
											<div class="add-butM">
												<a href="javascript:;" onclick="add_minors();"
													class="Edit-Button01"><?php echo translate_phrase('Add')?>
												</a> <input type="hidden" name="minors_id" id="minors_id"
													value="">
											</div>
										</div>
									</div>

									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Years attended')?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">

										<?php
										//from years
										for($i=date('Y');$i>=1910;$i--){
											$school_years[$i] = $i;
										}

										//to years
										for($i=date('Y')+5;$i>=1910;$i--){
											$school_years_to[$i] = $i;
										}

										?>
										<?php echo form_dt_dropdown('years_attended_start',$school_years,'','id="attended_start" class="dropdown-dt"','---',"hiddenfield");?>
											<div class="centimeter">
											<?php echo translate_phrase('to')?>
											</div>
											<?php echo form_dt_dropdown('years_attended_end',$school_years_to,'','id="attended_end" class="dropdown-dt "','---',"hiddenfield");?>
											<div class="centimeter">
											<?php echo translate_phrase('(or expected graduation year)');?>
											</div>
											<label id="years_attended_error" class="input-hint error"></label>
										</div>
									</div>-->
									
									<div class="sfp-1-Right">
										<input type="hidden" value="" name="user_school_id"
											id="user_school_id">
										<div class="Edit-Button01">
											<a onclick="add_school();" href="javascript:;"
												id="school_button"><?php echo translate_phrase('Add School')?>
											</a>
										</div>
										<div class="Delete-Photo01">
											<a href="javascript:;" onclick="cancel_school();"
												id="cancel_button"><?php echo translate_phrase('Cancel')?> </a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="Nex-mar"> <a href="javascript:;"  onclick="add_school('next_step');" class="Next-butM"><?php echo translate_phrase('Next')?> </a></div>
					</div>
		</form>
	</div>
</div>
<script>
	var currnetSchoolName = '';
    var prevInsertCompany = '';
    
    
    function get_city(country,city,error){
        var country_id = document.getElementById(country).value;
        $( "#"+city ).select();
        if(country_id!=""){
            $.ajax({ 
                url: '<?php echo base_url(); ?>' +"user/autocomplete_city/", 
                dataType: "json", 
                type:"post",
                data:"id="+country_id,
                cache: false,
                success: function (data) {
                    var i="0";
                    var availableTags=new Array();
                    $.each(data,function(id,description) {
                        availableTags[i]= description  ;
                        i= parseInt(i)+parseInt(1);
                    });
                    $( "#"+city ).autocomplete({
                        source: availableTags
                    });
                }     
            });
         }
    }
    
 function education_validaion()
 {
    var flag = true;
        
     var educationMilestone = checkEducationMilestones();
     
     if(educationMilestone != true)
     {
         jQuery('#education_level_err').text('<?php echo translate_phrase("Please specify your education status");?>');
         flag = false;
     }
     else
     {
         jQuery('#education_level_err').text('');
         // flag = true;
     }
     
     var schoolCount = jQuery('#list_school div.Univ-logoSec').length;
     if(schoolCount < 1)
     {
         showError('schoolReqError','<?php echo translate_phrase("You should add atleast one school");?>');
         flag = false;
     }
     else
     {
         hideError('schoolReqError');
         // flag = true;
     }
     
     
     return flag;
 }
 
function add_school(step){

	if($("#add_schools").css('display') == 'none'&& typeof(step) != 'undefined' && step=='next_step')
	{
		//form is not open
	if(education_validaion())
	{
		$("#signupForm").submit();
		}			
	}
	var school_name       = $('#school_name').val();
	var degree            = $('#degree_name').val();
	var degree_completed  = "0";
	var majors            = $('#majors_id').val();
	var minors            = $('#minors_id').val();
   	var yr_start          = $('#years_attended_start').val();
   var yr_end            = $('#years_attended_end').val();
   var school_email      = $('#school_email_address').val();
   var user_school_id    = $('#user_school_id').val();
   var school_domain     = $('#school_email_domain_id').val();
   //check if year's attended range in correct.
       
   var yearsAttendedRangeIsCorrect = true;
   if(yr_end < yr_start)
   {
        yearsAttendedRangeIsCorrect = false;
   }
	if (document.signup.is_degree_completed.checked) {
            degree_completed = $('#is_degree_completed').val();
       }
       $("#school_error").html('');
       $("#school_error").text('');
       $("#years_attended_error").html('');
       $('#degree_err').html('');
       if(school_domain!='undefined')
           var domain        = school_domain;
       else 
           var domain        = "";
       
       if(school_name!="" && degree!="" && (yr_start !=""  && yr_start != 0) && (yr_end != "" && yr_end != 0) && yearsAttendedRangeIsCorrect === true)
       {
    	   loading();
            $.ajax({ 
                    //url:'<?php echo base_url("user/add_school")?>', 
                    url:'<?php echo base_url("user/add_school_applyPage")?>', 
                    type:"post",
                    data:{'user_school_id':user_school_id,'school_name':school_name,'degree':degree,'degree_completed':degree_completed,'majors':majors,'minors':minors,'yr_start':yr_start,'yr_end':yr_end,'school_email':school_email,'school_domain':domain},
                    cache: false,
                    success: function (data) { 
                    	stop_loading();
						if(data==0)
						$("#school_error").html('<div class="error_msg"><?php echo translate_phrase("School already added")?></div>');
                        if(data==1)
                            $("#school_email_error").html('<div class="error_indentation error_msg"><?php echo translate_phrase("Please enter a valid school email address")?></div>');
                        else{
							
                            if(user_school_id!=""){
                                 //$("#user_school"+user_school_id).html(data);
                                 $("#list_school").html(data);
                                 $('#add_schools').fadeOut();
                                 scrollToDiv('user_school'+user_school_id);
                            }
                            else{
                                
                            	$("#list_school").html(data);
                            	$("#school_error").html('');
                            	hideError('schoolReqError');
                            	$('#add_schools').fadeOut();
                            	scrollToDiv('list_school_main');
                            }
                            
                            $("#list_school").fadeIn();
                            $('#school_button').html('<?php echo translate_phrase("Add School")?>');
                            if(typeof(step) != 'undefined' && step=='next_step')
                        	{
                            	//form is not open
								if(education_validaion())
								{
									$("#signupForm").submit();
								}
                        	}
                        }
                        
                        if(data!=1){
                        	
                            $("#school_name").val('');
                            $("#degree_name").val('');
                            $("#school_email_address").val('');
                            $("#majors_id").val('');
                            /*
                            	Edited by Rajnish
                            */
                            
                            $('#add_schools input[type=text]').val('');
                            $('#add_schools input[type=hidden]').val('');
        
                            //$('#is_degree_completed').prop('checked',false);

                            $("#add_schools select").prop("selectedIndex","");
                            $("#major_id").find('dt a span').text('Select major(s)');
                            $("#minor_id").find('dt a span').text('Select minor(s)');
                            $("#attended_start").find('dt a span').text('---');
                            $("#attended_end").find('dt a span').text('---');

                            $("ul.list_rows").empty();
                            $('#school_logo').html('');
                            $('#schoo_name_err').html('');
                            $('#degree_err').html('');
                            $('#school_email_error').html('');
                           // $('#add_schools.file-upload img').attr('src', $('#add_companies .file-upload img').attr('data-src'));
                            $('#add_school_button').show();
                            $('#list_school_main').fadeIn();
                            $("#school_photo_id").html('');
                            $('#user_school_id').val('');
                        	currnetSchoolName = '';
                        }
                        return false;
                   }
            });
            
       }else{
           
           if(school_name=="")
           {
               $('#schoo_name_err').html('<?php echo translate_phrase("School name is required")?>');
           }
	   else
           {
               $('#schoo_name_err').html('');
           }
           
           if((yr_start != "" || yr_start != 0) && (yr_end != "" || yr_end != 0) && yearsAttendedRangeIsCorrect === false)
           {
                $('#years_attended_error').addClass('error_indentation error_msg')
                $('#years_attended_error').html('<?php echo translate_phrase("End year must be equal to or after start year")?>');
           }
           else if(yr_start == ""|| yr_end == "" || yr_start == 0 || yr_end == 0)
           {
               $('#years_attended_error').addClass('error_indentation error_msg')
               $('#years_attended_error').html('<?php echo translate_phrase("Please tell us during which years you attended this school")?>');
          }
          else
          {
                $('#years_attended_error').html('');
          }
           
            if(degree==""){
                $('#degree_err').addClass('error_indentation error_msg')
                $('#degree_err').html('<?php echo translate_phrase("Degree is required")?>');
            }
            else
            {
                $('#degree_err').html('');
            }
            return true;
       }
      
    }
    
    function edit_school(school_id){
       
    	scrollToDiv('add_schools')
    	$('#add_schools').fadeIn();
       	$('#list_school_main').fadeOut();
       	$('.mobile-error').remove();
    	$('#cancel_button').show();
    	
       $('#user_school_id').val(school_id);
       $('#add_schools').find('.error_msg').html('');
       $('.mobile-error').remove();       
       $("#school_error").html('');
       $("#school_photo_id").html('');
       $("#atsymbol").hide;
       $('#school_domain').html('');
       
       if($('#user_school_id').val())
          	$('#school_button').html('<?php echo translate_phrase("Update")?>');

        $.ajax({ 
            url:'<?php echo base_url("user/edit_school")?>', 
            type:"post",
            dataType: "json", 
            data:"user_school_id="+school_id,
            cache: false,
            success: function (data) {
           		$.each(data,function(id,value) {
                	if(id=='0'){
                    	$.each(value,function(ids,values) {
                        	
                        	if(ids=='photo_diploma' && values){
                                   var path  = base_url +'user_photos/user_'+user_id+'/'+values;
                                   $('#add_schools .file-upload img').attr('src',path);
                                   $('#add_schools .file-upload .Delete-Photo01').show();
                             }
                        	else
                        	{
                        		$('#add_schools .file-upload img').attr('src','');
                                $('#add_schools .file-upload .Delete-Photo01').hide();
                           	}

                              if(ids == 'years_attended_start')
                               {
                            	  if(values == 0)
                                  {
                                      $("#attended_start").find('dt a span').text('---');
                                      jQuery('#years_attended_start').val('');

                                  }
                                  else
                                  {
                                      $("#attended_start").find('dt a span').text(values);
                                      jQuery('#years_attended_start').val(values);
                                  }
                               }

                               if(ids == 'years_attended_end')
                               {
                            	   if(values == 0)
                                   {
                                      $("#attended_end").find('dt a span').text('---');      
                                      $("#years_attended_end").val('');      
                                   }
                                   else
                                   {
                                       $("#attended_end").find('dt a span').text(values);      
                                       $("#years_attended_end").val(values);      
                                   }
                              
                               
                               }
                               
                           if(ids!='is_degree_completed' &&  ids!='school_email_address' && ids != 'photo_diploma')
                               	$('#'+ids).val(values);
                                
                        	if(ids=='is_degree_completed'&& values=="1"){
                        		$('#is_degree_completed').prop('checked',true);
                        	}
                      });
                 }

                if(id=='majors'){
                    $("#add_major").html('');
                    	$.each(value,function(ids,values) {
   							var major = '<li class="Fince-But" id="major'+ids+'" ><a href="javascript:;">'+values+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_major('+"'"+ids+"'"+');" title="Remove"></a></li>';
                            $("#add_major").append(major);
                            if( $('#majors_id').val()){
                            	var maj = $('#majors_id').val()+','+ids;
                                $('#majors_id').val(maj);
                            }    
                            else
                   				$('#majors_id').val(ids);
                   });
                }
                if(id=='minors'){
						$("#add_minor").html('');
                        $.each(value,function(ids,values) {
                        	var minor = '<li class="Fince-But" id="minor'+ids+'" ><a href="javascript:;">'+values+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_minor('+"'"+ids+"'"+');" title="Remove"></a></li>';
                            $("#add_minor").append(minor);
                            if( $('#minors_id').val()){
                            	var min = $('#minors_id').val()+','+ids;
                                $('#minors_id').val(min);
                            }
                            else
                                $('#minors_id').val(ids);
                        });
                    }

                    if(id=='school_logo'){
                    	$("#school_logo").html('');
                        var url = '<?php echo base_url();?>school_logos/';
                        if(value!="")
                             $("#school_logo").html('<img height="50" width="50" src="'+url+value+'" >');
                    }
                       
                   if(id=='email'){
                       
                   		$.each(value,function(ids,values) {
							if(ids=='domain')
                            {
                                Dropdownli = '';
								var defultSelected = ''
                                $.each(values,function(id,option) {
    								Dropdownli +='<li><a key="'+id+'">'+option+'</a></li>';
    								defultSelected='<dt><a key="'+id+'" href="javascript:;"><span>'+option+'</span></a><input type="hidden" value="'+id+'" name="school_email_domain_id" id="school_email_domain_id"></dt>';
    							});
    							
								var DropDownHTML ='<dl class="dropdown-dt" id="email_domain" name="drop_school_email_domain_id">'+defultSelected+'<dd><ul>';
								DropDownHTML +=Dropdownli + '</ul></dd></dl>';

                                $('#school_domain').html(DropDownHTML);
   								if(values!="")
   	   							{
                                    $('#atsymbol').show();
                                	$('#school_email_address').removeClass("medium");
                                }
                                else
                                {
                                	$('#atsymbol').hide();
                                    $('#school_email_address').addClass("medium");
                                }
                             }

                            if(ids=='adress'){
                            	$('#school_email_address').val(values);
                            }
                            
                         });
                       }
                  });
              
            }     
       }); 
    }

function cancel_school(){
    	
    	$('#school_button').html('<?php echo  translate_phrase("Add School")?>');        

		$('#add_schools').fadeOut();
        $('#list_school_main').fadeIn();
        $('#add_school_button').fadeIn();
        		
		$('#add_schools input[type=text]').val('');
        $('#add_schools input[type=hidden]').val('');
        $("#major_id").find('dt a span').text('<?php echo translate_phrase("Select major(s)")?>');
        $("#minor_id").find('dt a span').text('<?php echo translate_phrase("Select minor(s)")?>');
        $('#is_degree_completed').prop('checked',false);
        $("ul.list_rows").empty();
        $('#school_logo').html('');
        $('#add_schools .file-upload img').attr('src', '');
        $('#add_schools .file-upload .Delete-Photo01').hide();
        $('#years_attended_error').html('');
        $('#school_error').html('');
        $('#schoo_name_err').html('');
        $('#years_attended_error').html('');        
        $('#degree_err').html('');
        $('#email_error').html('');
        $('#school_domain').html('');
        
        if($("#list_school").find('.Univ-logoSec').length > 0)
        {
        	$("#list_school").fadeIn();
        }
        else
        {
        	$("#list_school").fadeOut();
        }  
        //goToScroll('list_school_main');
        scrollToDiv('list_school_main');
    	$('.mobile-error').remove();
              
    }

function show_div(div){

	$('.mobile-error').remove();
    $('#add_schools input[type=text]').val('');
    $('#add_schools input[type=hidden]').val('');
    $('#is_degree_completed').prop('checked',false);

    $("#add_schools select").prop("selectedIndex","");
    $("#major_id").find('dt a span').text('<?php echo translate_phrase("Select major(s)")?>');
    $("#minor_id").find('dt a span').text('<?php echo translate_phrase("Select minor(s)")?>');
    $("#attended_start").find('dt a span').text('---');
    $("#attended_end").find('dt a span').text('---');

    //$("#add_schools dl dt a span").html('select');
    $("ul.list_rows").empty();
    $('#school_logo').html('');

    
    $('#school_error').html('');
    $('#schoo_name_err').html('');
    $('#years_attended_error').html('');
    
    
    $('#degree_err').html('');
    $('#email_error').html('');
    $('#school_domain').html('');
    $('#atsymbol').hide();
    $('#school_email_address').addClass('medium');
    
	$('#add_schools .file-upload img').attr('src', '');
	$('#add_schools .file-upload .Delete-Photo01').hide();
	$('#cancel_button').show();
	
	$('#add_schools').fadeIn();
	$('#list_school_main').fadeOut();    	
    $('#school_name').focus();
    //scrollToDiv('add_schools');
}

function remove_school(user_school_id){
	 $("#user_school"+user_school_id).remove();
	 $.ajax({ 
		url:'<?php echo base_url("user/remove_school")?>', 
		type:"post",
		data:"user_school_id="+user_school_id,
		cache: false,
		success: function (data) {
			if(data==0){
				$('#add_school_button').hide();
				$('#list_school_main').fadeOut();
				$('#add_schools').fadeIn();
				scrollToDiv('add_schools');
				$("#school_name").focus();                    
			}
		}     
   }); 
}
function auto_complete_city(obj){
	loading();
	var term = $(obj).val();
	$.ajax({ 
		url: '<?php echo base_url(); ?>'+"user/job_location_autocomplete/"+ term, 
		dataType: "json", 
		type:"post",
		cache: false,
		success: function (data) {
			stop_loading();
			var i="0";
			var availableTags=new Array();
			$.each(data,function(id,description) {
				availableTags[i]= description  ;
				i= parseInt(i)+parseInt(1);
			});
			$( "#job_city" ).autocomplete({
				appendTo: "#auto-city-containter",
				minLength: 1,
				source: availableTags
			});
		}     
	});
}	
function show_logo(){
	loading();
	var school_name = $('#school_name').val();
	$.ajax({ 
		url: '<?php echo base_url(); ?>' +"user/show_school_logo/", 
		type:"post",
		data:'school_name='+school_name,
		cache: false,
		success: function (data) {
			stop_loading();
			if (data.length > 0) {
				var url = '<?php echo base_url();?>school_logos/'+data;
				$('#school_logo').html('<img src="'+url+'" height="50" width="50">');
			}else
			{
				 $('#school_logo').html('');
			}
		}     
	});
}
    
function show_school_domain(){

	var school_name = $('#school_name').val();
	loading();
	$.ajax({ 
		url: '<?php echo base_url(); ?>' +"user/show_school_domain/", 
		type:"post",
		data:'school_name='+school_name,
		cache: false,
		success: function (data) {
			stop_loading();
			$('#school_domain').html(data);
			if (data.length > 0){
				$('#atsymbol').show();
				$('#school_email_address').removeClass("medium");
			} else{
				$('#atsymbol').hide();
				$('#school_email_address').addClass("medium");
			}   
		}     
	});
}
    
function add_majors(){

	var major_id = $('#major_id').find('dt a').attr('key');
	var major_ids = $('#majors_id').val();
	var majors = $('#major_id').find('dt a span').html();
	
	if (major_id) {
		if(major_ids != "")
			majors_ids     = major_ids+','+major_id; 
		else
		 majors_ids     = major_id;
		 if(major_ids.indexOf(major_id)== -1){ 
			$("#majors_id").val(majors_ids);
			$("#add_major").append('<li class="Fince-But" id="major'+major_id+'" ><a href="javascript:;">'+majors+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_major('+"'"+major_id+"'"+');" title="Remove"></a></li>');
		}
	 }
}

function remove_major(id){
	var ids           = new Array();
	var major_ids     = document.getElementById('majors_id').value;
	var ids           = major_ids.split(','); 
	var index         = ids.indexOf(id);
	ids.splice(index, 1);
	var major_id      = ids.join(); 
	$('#major'+id).remove();
	$("#majors_id").val(major_id);
	
}
function add_minors(){
	var minor_id      = $('#minor_id').find('dt a').attr('key');
	var minor_ids     = $('#minors_id').val();
	var minors        = $('#minor_id').find('dt a span').html();
	
	if (minor_id) {
	  if(minor_ids!="")
		minors_ids     = minor_ids+','+minor_id; 
	  else
		  minors_ids     = minor_id;
	  if(minor_ids.indexOf(minor_id)== -1){ 
		$("#minors_id").val(minors_ids);
		$("#add_minor").append('<li class="Fince-But" id="minor'+minor_id+'" ><a href="javascript:;">'+minors+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_minor('+"'"+minor_id+"'"+');" title="Remove"></a></li>');
	   }  
	}
}
function remove_minor(id){
	var ids           = new Array();
	var minor_ids     = document.getElementById('minors_id').value;
	var ids           = minor_ids.split(','); 
	var index         = ids.indexOf(id);
	ids.splice(index, 1);
	var minor_id      = ids.join(); 
	$('#minor'+id).remove();
	$("#minors_id").val(minor_id);
}
function verify_email(id,value){
	if(value=="school")
		var verification_code  = $('#school_verification_code'+id).val();
	else
		var verification_code  = $('#company_verification_code'+id).val();
	if(verification_code!=""){
		loading();
		$.ajax({ 
			url: '<?php echo base_url(); ?>' +"user/verify_email/", 
			type:"post",
			data:'verification_code='+verification_code+'&value='+value+'&id='+id,
			cache: false,
			success: function (data) {
				stop_loading();
				if(data=='1'){
					if(value=="school"){
						$('#sc_verified'+id).hide();
						$('#sc_verified_label'+id).find('span').html('<img class="mar-verify" alt="" src="<?php echo base_url()?>assets/images/verified.png">');
						$('#link'+id).remove();
						$('#verified'+id).remove();
					} else{
						
						$('#com_verified'+id).hide();
						$('#com_verified_label'+id).find('span').html('<img class="mar-verify" alt="" src="<?php echo base_url()?>assets/images/verified.png">');
						
						/*$('#com_verified'+id).html('<font color="green"><b>*<?php echo translate_phrase("verified");?>*</b></font>');
						 $('#link_com'+id).remove();*/
						$('#company_verified'+id).remove();
					}    
				}else{
					if(value=="school")
						$('#email_error'+id).html('<?php echo translate_phrase('Invalid verification code. Please check your verification email again')?>');
					else
						$('#company_error'+id).html('<?php echo translate_phrase('Invalid verification code. Please check your verification email again')?>');
				}
			}     
		});
	}else{
		if(value=="school")
			$('#email_error'+id).html('<?php echo translate_phrase('Please enter verification code')?>');
		else
			$('#company_error'+id).html('<?php echo translate_phrase('Please enter verification code')?>');
	}
}    
function remove_photo(id,type){
	$.ajax({ 
		url: '<?php echo base_url(); ?>' +"user/remove_photo/", 
		type:"post",
		data:"id="+id+'&type='+type+'&name='+name,
		cache: false,
		success: function (data) {
			 if(type=='school_photo')
				 $('#school_ph_id').remove();
			 else if(type=='company_photo') 
				 $('#company_ph_id').remove();
			 else 
				 $('#list_photo').remove();
		}     
	});
}
function send_verification_mail(user_school_id,mail_for,email){
	var url = '<?php echo base_url("user/send_verification_email")?>';
	loading();
	$.ajax({ 
		url:url, 
		type:"post",
		data:'id='+user_school_id+'&mail_for='+mail_for+'&email='+email,
		cache: false,
		success: function (data) {
		stop_loading();
			if(data==1){
				if(mail_for=='school'){
					$('#error'+user_school_id).html('');
					//$('#link'+user_school_id).remove();
					$('#verified'+user_school_id).remove();
					var varifyDiv = '<div id="verified'+user_school_id+'" class="verfiy-message" ><div class="varify-text"><?php echo translate_phrase("A verification email has been sent to ");?> '+email+'</div><div class="inline-form"><label class="input-label"><?php echo translate_phrase("Enter the verification code in the verification email you just received");?>:</label><div class="input-wrapper"><input class="Degree-input" name="school_verification_code'+user_school_id+'" id="school_verification_code'+user_school_id+'"><label class="input-hint error" id="email_error'+user_school_id+'" ></label></div><a class="Edit-Button01" href="javascript:;" onclick="verify_email('+user_school_id+',\'school\');">Verify</a></div></div>'
					//$('#user_school'+user_school_id).append(varifyDiv);
					$('#user_school'+user_school_id).find('.Verification-Button').after(varifyDiv);
					$('#sc_verified'+user_school_id).find('a').text("<?php echo translate_phrase('Re-send Verification Code')?>");
				}
				if(mail_for=='company'){
					$('#error_com'+user_school_id).html('');
					  var varifyDiv = '<div id="company_verified'+user_school_id+'" class="verfiy-message" ><div class="varify-text"><?php echo translate_phrase("A verification email has been sent to ");?> '+email+'</div><div class="inline-form"><label class="input-label"><?php echo translate_phrase("Enter the verification code in the verification email you just received");?>:</label><div class="input-wrapper"><input class="Degree-input" name="company_verification_code'+user_school_id+'" id="company_verification_code'+user_school_id+'"><label class="input-hint error" id="company_error'+user_school_id+'" ></label></div><a class="Edit-Button01" href="javascript:;" onclick="verify_email('+user_school_id+',\'company\');">Verify</a></div></div>'
					  //$('#comp_email_verified'+user_school_id).append(varifyDiv);
					  var varbut = '<div class="Verification-Button" id="com_verified'+user_school_id+'">'+$('#comp_email_verified'+user_school_id).find('.Verification-Button').html()+'</div>';
					  $('#comp_email_verified'+user_school_id).html(varbut+varifyDiv);
				  
					
					//$('#link_com'+user_school_id).remove();
					//$('#company_verified'+user_school_id).remove();
					//$('#comp_email_verified'+user_school_id).after('<div id="company_verified'+user_school_id+'"><span>'+'<?php echo translate_phrase("A verification email has been sent to ");?><b>'+email+'</b></span><div><label>'+'<?php echo translate_phrase("Enter the verification code found in the verification email you just received");?>'+':</label><span><input name="company_verification_code'+user_school_id+'" style="margin-right:14px;" id="company_verification_code'+user_school_id+'"><button type="button" value="Verify Email" class="button darkblue" onclick="verify_email('+user_school_id+',\'company\');">'+'<?php echo translate_phrase("Verify Email");?>'+'</button><div id="company_error'+user_school_id+'" style="padding-left:7px;color: #FD2080;"></div></span></div></li>');
					 $('#com_verified'+user_school_id).find('a').text("<?php echo translate_phrase('Re-send Verification Code')?>");
					  
				}
				 //$('#sc_verified'+user_school_id).find('a').text("<?php echo translate_phrase('Re-send Verification Email')?>");
			}else{  
				if(mail_for=='school')
					$('#error'+user_school_id).html(data);
				else
					$('#error'+user_school_id).html(data);
			}
		} 
	});
}
</script>
