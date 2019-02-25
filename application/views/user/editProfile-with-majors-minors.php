<script src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script
	src="<?php echo base_url()?>assets/js/general.js"></script>
<link
	rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>assets/fancybox/jquery.fancybox.css?v=2.1.5"
	media="screen" />
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/jquery.fancybox.js?v=2.1.5"></script>
<?php
$page_msg = $this->session->flashdata('edit_profile_msg');
$page_msg_error = $this->session->flashdata('edit_profile_msg_error');
?>
<script type="text/javascript">
var base_url = '<?php echo base_url() ?>';
var pageMsg = '<?php echo $page_msg;?>';
var slider = null;
var user_id = '<?php echo $this->session->userdata('user_id');?>';
var docWidth = jQuery(document).width();
var currnetSchoolName = '';
var currnetCompanyName = '';
function resizeSlider()
{
	//slider.reloadSlider();    
	   var documentWidth = jQuery(document).width(); 
	   //320
	   if(documentWidth <= 640)
	   {
	       if($(".bxslider_profile li").length  > 1)
	       {
	          jQuery('#slider-next,#slider-prev').fadeIn();
	       }
	   }
	   else
	   {
		   	if($(".bxslider_profile li").length  < 5)
		    {
		    	jQuery('#slider-next,#slider-prev').fadeOut(); 
		    }
		   	else
		   	{
		   		jQuery('#slider-next,#slider-prev').fadeIn();
			}
		   /*
		   if(documentWidth <= 640)
		   {
		   	if($(".bxslider_profile li").length  < 3)
			{
			   jQuery('#slider-next,#slider-prev').fadeOut(); 
			}
		   }
		   else
		   {
	                if($(".bxslider_profile li").length  < 5)
	                {
	                    jQuery('#slider-next,#slider-prev').fadeOut(); 
	                }
		   }
	       */
	   }
}

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
                        select : function(){ jQuery('#school_error').text('');}
                });
            }     
        });
    }
}

function auto_complete_city(obj){
	var term = $(obj).val();
    $.ajax({ 
        url: '<?php echo base_url(); ?>'+"user/job_location_autocomplete/"+ term, 
        dataType: "json", 
        type:"post",
        cache: false,
        success: function (data) {
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

var autocompany = true;
function auto_complete_company(){
    if(autocompany)
    {
    	loading();
        $.ajax({
            url: '<?php echo base_url(); ?>' +"user/auto_complete_company/", 
            dataType: "json", 
            type:"post",
            cache: false,
            success: function (data) {
            	autocompany = false;
            	stop_loading();
                var i="0";
                var availableTags=new Array();
                $.each(data,function(i,item) {
                	//console.log(item);
                    availableTags[i] = item.company_name;
                });
                $( "#company_name" ).autocomplete({
                	appendTo:"#auto-company-container",
                        source: availableTags,
                        minLength: 1,
                        select : function(event, ui){ 
                    	show_company_logo(ui.item.value);
                		show_company_domain(ui.item.value)
                    	show_company_industry(ui.item.value)
                    	//show_company_industry(ui.item.value)
                    	jQuery('#selectedFromAvailableCompanies').val('yes')
                    }
                });
            }     
        });
    }
	
}

$(document).ready(function () {

	 //autocomplete
    auto_complete_school();
    auto_complete_company();

    $('.fancybox').fancybox();
	//When click on dropdown then his ul is open..
	$(".dropdown-dt").find('dt a').live('click',function () {
    	$(this).parent().parent().find('ul').toggle();
    });

	//When select a option..
    $(".dropdown-dt dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
    	$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'))
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
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
            	$("#district_block").fadeOut(function(){
            		$(this).html('');
            	});
            }     
        });
	});
	
	$('#current_city_dropdown ul li a').live('click',function(){
		loading();
		$.ajax({ 
            url: '<?php echo base_url(); ?>' +"user/get_district_by_city", 
            type:"post",
            data:{city_id:$(this).attr('key')},
            cache: false,
            success: function (data) {
            	stop_loading();
            	$('#district_block').hide().html(data).fadeIn('slow');
            	
            }     
        });
	});
	
    $(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("dropdown-dt"))
        	$(".dropdown-dt dd ul").hide();

    	if (!$clicked.parents().hasClass("animate-dropdown"))
        	$(".animate-dropdown dd ul").hide();
    	
    });
	// END Dropdown
	
	//When click on dropdown then his ul is open..
	$(".animate-dropdown").find('dt a').live('click',function () {
    	$(this).parent().parent().find('ul').toggle();
    });

	//When select a option..
    $(".animate-dropdown dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
    	$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'))
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
	});

	// END Dropdown
	

	/* Dynamic Checkbox */
	$('.ckbox_multi-select-gender').live('click',function(){
		var lang = $(this).find('img').attr('src');
		$(this).find('img').attr('src',$(this).attr('lang'));
		$(this).attr('lang',lang);
		if($(this).attr('isselected') == 'no')
		{
			$(this).siblings(':input[type="hidden"]').val($(this).attr('key'));
			$(this).attr('isselected','yes');
		}
		else
		{
			$(this).siblings(':input[type="hidden"]').val('');
			$(this).attr('isselected','no');
		}

	});
	/* End Checkbox*/
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
        
    $('#your-personality ul li a').click(function(e) {
		e.preventDefault();
		var li = $(this).parent();
		if ($(li).hasClass('selected')) {
		  // remove
		  var ids           = new Array();
		  var desc_id       = $('#descriptive_word_id').val(); 
		  ids               = desc_id.split(',');
		  var index         = ids.indexOf(this.id);
		  ids.splice(index, 1);
		  var descriptive_id      = ids.join(); 
		  $("#descriptive_word_id").val(descriptive_id);
		  $(li).removeClass('selected').find('a').removeClass('appr-cen').addClass('disable-butn');
		} else {
		  // check before adding
		  if ($('#your-personality ul li.selected').length < 5) {
		    var descriptive_id   = $('#descriptive_word_id').val();
		    if(descriptive_id!="")
		        var dsc_id       = descriptive_id+','+this.id; 
		    else
		        var dsc_id       = this.id;
		
		    $("#descriptive_word_id").val(dsc_id);
		    $(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
		  }
		}
	});
	/* End Checkbox */
	
	$(".rdo_div").live('click',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});
	
	/*---------------for managing interests------------------*/
        $('#hobbiesAndInterest ul li a').click(function(e) {
	        e.preventDefault();
	        var li = $(this).parent();
	        if ($(li).hasClass('selected')) {
	          // remove
	          var ids           = new Array();
	          var desc_id       = $('#interestWordId').val(); 
	          ids               = desc_id.split(',');
	          var index         = ids.indexOf(this.id);
	          ids.splice(index, 1);
	          var descriptive_id      = ids.join(); 
	          $("#interestWordId").val(descriptive_id);
	          $(li).removeClass('selected').find('a').removeClass('appr-cen').addClass('disable-butn');
	        } else {
	          // check before adding
	          //if ($('#your-personality ul li.selected').length < 5) {
	            var descriptive_id   = $('#interestWordId').val();
	            if(descriptive_id!="")
	                var dsc_id       = descriptive_id+','+this.id; 
	            else
	                var dsc_id       = this.id;

	            $("#interestWordId").val(dsc_id);
	            $(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
	          //}
	        }
	   });
        /*---------------for managing interests------------------*/
	/* Dynamic Radio Button */
	$('#rdo_select-gender-female').live('click',function(){
		if($(this).attr('isselected') == 'no')
		{	
			var lang = $(this).find('img').attr('src');
			$(this).find('img').attr('src',$(this).attr('lang'));
			$(this).attr('lang',lang);
			$(this).attr('isselected','yes');
			
			if($("#rdo_select-gender-male").attr('isselected') == 'yes' )
			{
				$("#rdo_select-gender-male").attr('isselected','no');
				var otherFieldLang = $("#rdo_select-gender-male").find('img').attr('src');
				$("#rdo_select-gender-male").find('img').attr('src',$("#rdo_select-gender-male").attr('lang'));
				$("#rdo_select-gender-male").attr('lang',otherFieldLang);
			}
			$("#gender").val($(this).attr('key'));
		}
	});

	//gender
	$('#rdo_select-gender-male').live('click',function(){
		if($(this).attr('isselected') == 'no')
		{	
			var lang = $(this).find('img').attr('src');
			$(this).find('img').attr('src',$(this).attr('lang'));
			$(this).attr('lang',lang);
			$(this).attr('isselected','yes');

			if($("#rdo_select-gender-female").attr('isselected') == 'yes' )
			{
				var otherFieldLang = $("#rdo_select-gender-female").find('img').attr('src');
				$("#rdo_select-gender-female").attr('isselected','no');
				$("#rdo_select-gender-female").find('img').attr('src',$("#rdo_select-gender-female").attr('lang'));
				$("#rdo_select-gender-female").attr('lang',otherFieldLang);
			}
			$("#gender").val($(this).attr('key'));
		}
	});
	/* End Radio Button*/

	
	if($(".bxslider_profile").length == 0)
	{
		 $('#edit-profile').easytabs();
	}

	var total_groups = $(".bxslider_profile").length;
    var i = 0; // counts how many galleries are initted
       

        if(total_groups  > 0)
        {
        
            if($(".bxslider_profile li").length  > 4)
            {
                jQuery('#slider-next,#slider-prev').fadeIn(); 
            }
            
            var i = 0; // counts how many galleries are initted
            slider = $('.bxslider_profile').bxSlider({
                      pagerCustom: '#bx-pager',
                      nextSelector: '#slider-next',
                      prevSelector: '#slider-prev',
                      slideWidth: 200,
                      minSlides: 1,
                      maxSlides: 5,
                      infiniteLoop:false,
                      nextText: '<img src="<?php echo base_url()?>assets/images/p-right-arw.jpg" alt="" />',
                      prevText: '<img src="<?php echo base_url()?>assets/images/p-left-arw.jpg" alt="" />',
                      onSliderLoad: function() {
                        i++;
                        if (i >= total_groups) {
                            $('#edit-profile').easytabs();
                        }
                  }
            }); 
        }

        //320
        if(docWidth <= 640)
        {
            if($(".bxslider_profile li").length  > 1)
            {
                jQuery('#slider-next,#slider-prev').fadeIn(); 
            }
        }

        
        if($(".bxslider_profile li").length  > 4)
        {
            jQuery('#slider-next,#slider-prev').fadeIn(); 
        }
        
        $("#add_profile_photo").fileupload({
            dataType: 'json',
             add: function (e, data) {
	        	 
        	    if(isMobileView == 'No' || isMobileView != 'No')
                {
        	    	var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
	   	        	var uploadErrors = [];
	   	            if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
	   	                 uploadErrors.push(invalidImgType);
	   	            }
	   	             //in bytes
		   	        if(data.originalFiles[0]['size'] > 10000000) {
			   	        uploadErrors.push(invalidImgSize);
		   	        }
                    if(uploadErrors.length > 0) {
                    	if($(this).parent().parent().parent().find('.mobile-error').length <= 0)
                    	{
                    		$(this).parent().parent().parent().append('<label class="input-hint mobile-error error-msg">'+uploadErrors.join("\n")+'</label>');
                    	}
                    }
                    else
                    {
                    	$('body').css('cursor', 'wait');
                    	data.submit();
                   	}
                	
                }
                else
                {
                	if($(this).parent().parent().parent().find('.mobile-error').length <= 0)
                	{
                		$(this).parent().parent().parent().append(mobileErrorMsg);
                	}
            		return false;
                }
            },
            done: function (e, data) {
            	
                if (data.result.success === 1) {
                	var photo_id = data.result.id;
        			var img_url = data.result.url;
        			var liHTML ='';

					if($(this).parent().parent().parent().find('.mobile-error').length > 0)
               {
                		$(this).parent().parent().parent().find('.mobile-error').text('');
               }
                    if($(".bxslider_profile").length == 0)
        			{
        				liHTML += '<ul class="bxslider_profile">';
					}
					liHTML += '<li class="photo_'+photo_id+'">';
                    liHTML += '<a class="upload-part fancybox" rel="gallery1" href="'+img_url+'"><img height="150"  src="'+img_url+'" alt="img" /></a>';
                    if(data.result.set_primary == '1')
                    {
                    	liHTML += '<div class="Photo-dwn-btn"><div class="primary-btn-text"><label lang="'+photo_id+'" class="primary-action" data-url="'+base_url+'user/primary_profile_photo/" ><?php echo translate_phrase("Primary Photo")?></label></div>';
                    }
                    else
                    {
                    	liHTML += '<div class="Photo-dwn-btn"><div class="Primary-Button01"><label lang="'+photo_id+'" class="primary-action" data-url="'+base_url+'user/primary_profile_photo/"  onclick="make_primary_photo('+photo_id+',this)" ><?php echo translate_phrase("Set as Primary")?></label></div>';
                    }
                    liHTML += '<div class="Delete-Primary"><a href="javascript:;" onclick="delete_photos('+photo_id+',this)" data-url="'+base_url+'user/delete_profile_photo/"><?php echo translate_phrase("Delete")?></a></div>';
                    liHTML += '</div></li>';
                                
                    if($(".bxslider_profile").length == 0)
                    {
                    	liHTML += '</ul>';
						$("#user-photo-container").html(liHTML);

                        slider = $('.bxslider_profile').bxSlider({
                        			pagerCustom: '#bx-pager',
                                    nextSelector: '#slider-next',
                                    prevSelector: '#slider-prev',
                                    slideWidth: 151,
                                    minSlides: 1,
                                    maxSlides: 3,
                                    infiniteLoop:false,
                                    nextText: '<img src="<?php echo base_url()?>assets/images/p-right-arw.jpg" alt="" />',
                                    prevText: '<img src="<?php echo base_url()?>assets/images/p-left-arw.jpg" alt="" />',
                                  });
					}
                    else
                    {
                    	$("ul.bxslider_profile").append(liHTML);
                    	slider.reloadSlider();
                    }
                    resizeSlider();
				}
				else
                {
					if($(this).parent().parent().parent().find('.mobile-error').length <= 0)
		 			{
						$(this).parent().parent().parent().append('<label class="input-hint mobile-error error-msg">'+data.result.success+'</label>');
                	}
                }
                $('body').css('cursor', 'auto');
			}
		});
        
        
        /*----------------CUSTOM Select Tag-------------------------------*/
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
                      //unSelectImporance(ele);
                      
    	        } else {
    	          // check before adding
    	          
    	            var prefrencesId   = jQuery(hiddenField).val();
    	            
                        if(prefrencesId !="")
                            var dsc_id       = prefrencesId+','+ele.attr('id'); 
    	            else
    	                var dsc_id       = ele.attr('id');

    	            $(hiddenField).val(dsc_id);
    	            $(li).addClass('selected').find('a').addClass('appr-cen').removeClass('disable-butn');
    	          
    	        }
    	   });
setTimeout(function(){
    <?php if($scroll_to == 'userInterestDiv'): ?>
    $('#personalityTab').click();
    goToScroll('userInterestDiv');
	<?php endif;?>
	
    var current_tab_id = $('#edit-profile ul li.active a').attr('id');
        
    $("#current_tab").val(current_tab_id);
    
    //&& current_tab_id != 'educationTab' && current_tab_id != 'careerTab'
    if(current_tab_id != 'photosTab')
    {
    	$("#edit-submit").show();
    }

    if(current_tab_id == 'educationTab')
    {
    	//goToScroll('school_name');
    	$("#school_name").focus();
    }

    if(current_tab_id == 'basicsTab')
    {
        //alert('sss');
        <?php if($scroll_to != ''): ?>
    	goToScroll('<?php echo $scroll_to ?>')
    	<?php endif;?>
    	//goToScroll('first_name');	
    }

    if(current_tab_id == 'careerTab')
    {
    	//goToScroll('company_name');
    	$("#company_name").focus();
    	
    } 
    
    if(current_tab_id == 'othersTab')
    {
        //$("#city_lived").focus();
    }

    if(pageMsg != '')
    {
        $('body').scrollTo($('#rightPen'),800);;
    }
},500);    

/*--------------------validations---------------------------------------------*/
$('#edit-profile').bind('easytabs:before', function(tab, panel, data){
  
    var target_tab_id = panel[0].id;
    var current_tab_id = $('#edit-profile ul li.active a').attr('id');
    if(!validateThisTab(current_tab_id))
    {
		return false;
    }
    
    $("#current_tab").val(target_tab_id);
    //|| target_tab_id == 'educationTab' || target_tab_id == 'careerTab'
    if(target_tab_id == 'photosTab')
    {
    	$("#edit-submit").hide();
    }  
    else
    {
    	$("#edit-submit").show();
    }  
    
});

$('#edit-profile').bind('easytabs:after', function(tab, panel, data){
	  
    var target_tab_id = panel[0].id;
    if(target_tab_id == 'educationTab')
    {
    	$("#school_name").focus();
    }

    if(target_tab_id == 'basicsTab')
    {
    	//goToScroll('first_name');
    } 

    if(target_tab_id == 'careerTab')
    {
    	$("#company_name").focus();
    }

    if(target_tab_id == 'othersTab')
    {
       //$("#city_lived").focus();
    }
    
});

/*--------------------validations END---------------------------------------*/
});

function validateThisForm()
{

	var current_tab_id = $('#edit-profile ul li.active a').attr('id');
	if($("#submit_form").attr('lang') != 'redirect')
	{
		if(current_tab_id == 'educationTab' && $("#add_schools").css('display') != 'none')
	    {
		    add_school(1);
			return false;
	    }
		else if(current_tab_id == 'careerTab' && $("#add_companies").css('display') != 'none')
	    {
			add_company(1);
			return false;
	    }
	}
	var validationSuccessfull = validateThisTab(current_tab_id);
    if(validationSuccessfull == true)
    {
    	return true;        
	}
    else
    {
    	return false;
    }
}

function validateThisTab(current_tab_id)
{
    if(current_tab_id == 'basicsTab')
    {
        return validateBasicsTab();
    }
    
    if(current_tab_id == 'photosTab')
    {
        return true;
    }
    
    if(current_tab_id == 'educationTab')
    {
       return validateEducationTab();
    }
    
    if(current_tab_id == 'careerTab')
    {
       return validateCareerTab();
    }
    
    if(current_tab_id == 'personalityTab')
    {
        return true;
    }
    
    if(current_tab_id == 'othersTab')
    {
        return true;
    }
}
function scrollToDiv(id)
{
	$('body').scrollTo($('#'+id),800,{'axis':'y'});
}


jQuery(window).resize(function(){
	resizeSlider();
});


</script>

<!--*********Apply-Step1-A-Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<form name="signup" id="ProfileForm"
			action="<?php echo base_url() . url_city_name() . '/edit-profile.html'; ?>"
			method="post" enctype="multipart/form-data">
			<input type="hidden" id="current_tab" name="current_tab"
				value="basicsTab" />
			<div class="Apply-Step1-a-main">
				<div class="Edit-pge-Main">
					<div class="Edit-p-top1">
						<h1>
						<?php echo translate_phrase('Edit My Profile') ?>
						</h1>
					</div>
					<!--<div class="Edit-p-top1">
						<div class="order-btn">
						<a href="<?php echo base_url() . url_city_name() ?>/event.html?id=12&src=<?php echo $this->session->userdata('ad_id')?>">
						<?php echo translate_phrase('Get discounted tickets')?>&nbsp;
						<img src="<?php echo base_url()?>assets/images/c-arw.png" alt="" /></a>
						</div>
					</div>-->
					<div class="Edit-p-top2">
						<div class="Verification-Button">
							<a
								href="<?php  echo base_url() . url_city_name().'/user_info/'.$this->utility->encode($user_data['user_id']);?>"><?php echo translate_phrase('View My Profile') ?>
							</a>
						</div>
					</div>
					<div class="Edit-p-top2">
						<div class="Verification-Button">
							<a
								href="<?php echo base_url() . url_city_name() ?>/ideal-match.html"><?php echo translate_phrase('Edit My Ideal Match') ?>
							</a>
						</div>
					</div>
					<?php if(!$user_data['facebook_id']):?>
					<div class="Edit-p-top2">
						<a class="facebook-import" href="<?php echo base_url()?>fb_login/import_data"> <img src="<?php echo base_url().'assets/images/fb-btn-logo.jpg'?>" /> <?php echo translate_phrase('Connect') ?></a>
					</div>
					<?php endif;?>
					
					<div class="Profile-Completeness-M">
						<div class="Profile-Comp">
						<?php echo translate_phrase('Profile Completeness:'); ?>
						</div>
						<div class="Profile-progress"
							style="background-color: gray; width: 200px; overflow: hidden">
							<?php
							if(trim($profileCompleteness,'%') >= 100)
							{
								$bgColor = 'green';
							}
							else
							{
								$bgColor = 'red';
							}
							?>
							<div style="background-color:<?php echo $bgColor?>;width:<?php echo $profileCompleteness?>;height: 30px;float: left"></div>
						</div>
					</div>											
				</div>

				<?php if(!empty($waysToImproveProfile)):?>
				<div class="Top3-M">
					<div class="Top3-Inner-p">
						<div class="Top3-head">
						<?php echo translate_phrase('Top 3 ways to make your profile more attractive:'); ?>
						</div>
						<div class="fl">
						<?php foreach ($waysToImproveProfile as $key => $value):?>
							<a href="javascript:;" class="Edit-Button01 mar-R"
								id="<?php echo $value['elementDiv'].'_button'?>"
								elementId="<?php echo $value['elementDiv']?>"
								divId="<?php echo $value['divId']?>"
								onclick="goToThisField(this)"><?php echo translate_phrase($value['buttonTxt']); ?>
							</a>
							<?php endforeach;?>
						</div>
					</div>
				</div>
				<?php endif;?>

				<div id="page-msg-box" class="page-msg-box left" style="padding-bottom: 10px;">
					<span class="DarkGreen-color"><?php echo $page_msg;?></span>
					<span class="Red-color"><?php echo $page_msg_error;?></span>
					
				</div>
				<div class="emp-B-tabing-prt">
					<div class="emp-B-tabing-M" id="edit-profile">
						<ul class='etabs'>
							<li class='tab tab-nav'><span></span><a id="basicsTab"
								href="#basics" title="Basics"><?php echo translate_phrase('Basics') ?>
							</a></li>
							<li class='tab tab-nav'><span></span><a id="photosTab"
								href="#photos" title="Photos"><?php echo translate_phrase('Photos') ?>
							</a></li>
							<li class='tab tab-nav'><span></span><a id="educationTab"
								href="#education" title="Education"><?php echo translate_phrase('Education') ?>
							</a></li>
							<li class='tab tab-nav'><span></span><a id="careerTab"
								href="#career" title="Career"><?php echo translate_phrase('Career') ?>
							</a></li>
							<li class='tab tab-nav'><span></span><a id="personalityTab"
								href="#personality" title="Personality"><?php echo translate_phrase('Personality') ?>
							</a></li>
							<li class='tab tab-nav'><span></span><a id="othersTab"
								href="#others" title="Others"><?php echo translate_phrase('Others') ?>
							</a></li>
						</ul>

						<div id="basics"
							class="step-form-Main Mar-top-none Top-radius-none">
							<div class="step-form-Part">
								<div class="Indicate-top">
									*&nbsp;
									<?php echo translate_phrase('Indicates required field') ?>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('First name') ?>:<span>*</span>
									</div>
									<div class="sfp-1-Right">
										<input type="text" class="MyFname-input" name="first_name"
											id="first_name"
											value="<?php echo $user_data['first_name'] ?>"> <label
											id="firstNameError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>

								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('Last name') ?>:<span>*</span>
									</div>
									<div class="sfp-1-Right">
										<input type="text" class="MyFname-input" name="last_name"
											id="last_name" value="<?php echo $user_data['last_name'] ?>">
										<label id="lastNameError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>

								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase("I'm") ?>:<span>*</span>
									</div>
									<div class="sfp-1-Right">
									<?php
									foreach ($gender as $row) {
										$gender_id = $user_data['gender_id'] ? $user_data['gender_id'] : set_value('gender');
										?>

										<?php if ($row['gender_id'] == 2): ?>
										<?php if ($row['gender_id'] == $gender_id): ?>
										<div class="male-icn-but">
											<a href="javascript:;" key="<?php echo $row['gender_id'] ?>"
												id="rdo_select-gender-female" isselected="yes"
												lang="<?php echo base_url() ?>assets/images/femail-icn.png"><img
												src="<?php echo base_url() ?>assets/images/femail-icn-selected.png"
												alt="" /> </a>
										</div>
										<?php else: ?>
										<div class="male-icn-but">
											<a href="javascript:;" key="<?php echo $row['gender_id'] ?>"
												id="rdo_select-gender-female" isselected="no"
												lang="<?php echo base_url() ?>assets/images/femail-icn-selected.png"><img
												src="<?php echo base_url() ?>assets/images/femail-icn.png"
												alt="" /> </a>
										</div>
										<?php endif; ?>
										<?php endif; ?>

										<?php if ($row['gender_id'] == 1): ?>
										<?php if ($row['gender_id'] == $gender_id): ?>
										<div class="male-icn-but">
											<a href="javascript:;" key="<?php echo $row['gender_id'] ?>"
												id="rdo_select-gender-male" isselected="yes"
												lang="<?php echo base_url() ?>assets/images/male-icn.png"><img
												src="<?php echo base_url() ?>assets/images/male-icn-selected.png"
												alt="" /> </a>
										</div>
										<?php else: ?>
										<div class="male-icn-but">
											<a href="javascript:;" key="<?php echo $row['gender_id'] ?>"
												id="rdo_select-gender-male" isselected="no"
												lang="<?php echo base_url() ?>assets/images/male-icn-selected.png"><img
												src="<?php echo base_url() ?>assets/images/male-icn.png"
												alt="" /> </a>
										</div>
										<?php endif; ?>
										<?php endif; ?>

										<?php } //end gender foreach?>
										<input type="hidden" name="gender" id="gender"
											value="<?php echo $user_data['gender_id']?>">
										<div class="sfp-1-Desktop">
										<?php
										if($ethnicity)
										{
											$ethnicity_id = $this->input->post('ethnicity') ? $this->input->post('ethnicity') : $user_data['ethnicity_id'];
											echo form_dt_dropdown('ethnicity', $ethnicity, $ethnicity_id, 'id="ethnicityId" class="dropdown-dt domaindropdown"', translate_phrase('Select ethnicity'), "hiddenfield");
										}
										?>
											<label id="genderError"
												class="input-hint error error_indentation error_msg"></label>
											<label id="ethnicityError"
												class="input-hint error error_indentation error_msg"></label>
										</div>
									</div>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('I want to date') ?>:<span>*</span>
									</div>
									<div class="sfp-1-Right">
									<?php
									$checked_gender = '';
									foreach ($gender as $row) {

										if (!empty($user_want_gender)) {
											foreach ($user_want_gender as $gender_data) {
												if ($gender_data['gender_id'] == $row['gender_id']) {
													$checked = "checked";
													$checked_gender = $gender_data['gender_id'];
												} else {
													$checked = "";
												}
											}
										} else {
											$want_to_date_id = $this->input->post('want_to_date') ? $this->input->post('want_to_date') : array();
											if (in_array($row['gender_id'], $want_to_date_id)) {
												$checked_gender = $want_to_date_id;
												$checked = "checked";
											} else {
												$checked = "";
											}
										}
										?>

										<?php if ($row['gender_id'] == 2): ?>
										<div class="male-icn-but">
											<a href="javascript:;" key="<?php echo $row['gender_id'] ?>"
												class="ckbox_multi-select-gender"
												isselected="<?php echo ($row['gender_id'] == $checked_gender) ? 'yes' : 'no' ?>"
												lang="<?php echo base_url() ?>assets/images/<?php echo ($row['gender_id'] == $checked_gender) ? 'femail-icn.png' : 'femail-icn-selected.png' ?>">
												<img
												src="<?php echo base_url() ?>assets/images/<?php echo ($row['gender_id'] == $checked_gender) ? 'femail-icn-selected.png' : 'femail-icn.png' ?>"
												alt="" /> </a> <input type="hidden" name="want_to_date[]"
												id="want_to_date"
												value="<?php echo ($row['gender_id'] == $checked_gender) ? $row['gender_id'] : '' ?>">
										</div>
										<?php endif; ?>

										<?php if ($row['gender_id'] == 1): ?>
										<div class="male-icn-but">
											<a href="javascript:;" key="<?php echo $row['gender_id'] ?>"
												class="ckbox_multi-select-gender"
												isselected="<?php echo ($row['gender_id'] == $checked_gender) ? 'yes' : 'no' ?>"
												lang="<?php echo base_url() ?>assets/images/<?php echo ($row['gender_id'] == $checked_gender) ? 'male-icn.png' : 'male-icn-selected.png' ?>">
												<img
												src="<?php echo base_url() ?>assets/images/<?php echo ($row['gender_id'] == $checked_gender) ? 'male-icn-selected.png' : 'male-icn.png' ?>"
												alt="" /> </a> <input type="hidden" class="ckb_want_to_date"
												name="want_to_date[]"
												value="<?php echo ($row['gender_id'] == $checked_gender) ? $row['gender_id'] : ''; ?>">
										</div>
										<?php endif; ?>
										<?php } ?>

										<label id="wantToDateError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
								<div class="sfp-1-main" id="looking_fordiv">
									<div class="sfp-1-Left">
									<?php echo translate_phrase("I'm looking for") ?>:<span>*</span>
									</div>
									<div class="sfp-1-Right">

									<?php
									$tmp_arr = array();
									if ($user_want_relationship_type) {

										foreach ($user_want_relationship_type as $value) {
											$tmp_arr[] = $value['relationship_type_id'];
										}
									}
									?>
									<?php
									foreach ($relationship_type as $row) {
										$looking_for_id = $this->input->post('looking_for') ? $this->input->post('looking_for') : $tmp_arr;
										if (in_array($row['relationship_type_id'], $looking_for_id)) {
											$checked_class = "appr-cen";
										} else {
											$checked_class = "disable-butn";
										}
										?>
										<a href="javascript:;" class="ckb_div_lookfor"
											key="<?php echo $row['relationship_type_id']; ?>"> <span
											class="<?php echo $checked_class ?>"><?php echo translate_phrase(ucfirst($row['description'])); ?>
										</span> <input type="hidden" id="looking_for"
											name="looking_for[]"
											value="<?php echo (in_array($row['relationship_type_id'], $looking_for_id)) ? $row['relationship_type_id'] : '' ?>">
										</a>
										<?php } ?>
										<label id="lookingForError"
											class="input-hint error error_indentation error_msg"></label>
									</div>

								</div>
								
								<div class="sfp-1-main">
									<div class="sfp-1-Left"><?php echo translate_phrase("I currently live in") ?>:<span>*</span></div>
									<div class="sfp-1-Right">
										<div class="scemdowndomain menu-Rightmar">
											<?php echo form_dt_dropdown('country',$country,$country_id,'id="current_country" class="dropdown-dt scemdowndomain menu-Rightmar" ', translate_phrase('Select country'), "hiddenfield");?>	
											<label id="liveInError" class="input-hint error error_indentation error_msg"></label>
										</div>
										<div class="scemdowndomain">
											<div id="current_city_dropdown">
											<?php echo form_dt_dropdown('current_city_id',$city,$city_id,'class="dropdown-dt"', translate_phrase('Select city'), "hiddenfield");?>
											</div>
											<label id="liveInCITYError" class="input-hint error error_indentation error_msg"></label>
										</div>
									</div>
								</div>
								<div id="district_block">
									<?php if($has_district && $district):?>
									<div class="sfp-1-main">
										<div class="sfp-1-Left"><?php echo translate_phrase('Neighborhood') ?>:</div>
										<div class="sfp-1-Right">
										<?php 
											$district_id = $this->input->post('district') ? $this->input->post('district') : $user_data['current_district_id'];
											echo form_dt_dropdown('district', $district, $district_id, 'class="dropdown-dt reqdropdown"', translate_phrase('Select neighborhood'), "hiddenfield");
										?>
											<label id="districtError" class="input-hint error" error_txt="<?php echo translate_phrase("Please select neighborhood") ?>"></label>
										</div>
									</div>
									<?php endif;?>
								</div>
								
								<div id="postal_code_block">
									<?php
									if ($postal_code_exist != "0")
										$show_postal_code = 'style="display: block;"';
									else
										$show_postal_code = 'style="display: none;"';
	
									if($country_name == 'United States')
									{
										$zipLabel = 'Zip Code';
									}
									else
									{
										$zipLabel = 'Postal Code';
									}
									?>
									<div class="sfp-1-main" <?php echo $show_postal_code ?>>
										<div class="sfp-1-Left"><?php echo translate_phrase($zipLabel) ?>:</div>
										<div class="sfp-1-Right"><input id="postal_code" class="post-input" name="postal_code" type="text"value="<?php echo $user_data['current_postal_code'] ?>" style="width: 100px"></div>
									</div>
								</div>

								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('I was born on') ?>:<span>*</span>
									</div>
									<div class="sfp-1-Right">
									<?php
									if (!empty($fb_user_data['dob'])) {
										$syear = $fb_user_data['dob']['y'] ? $fb_user_data['dob']['y'] : set_value('year');
										$smonth = $fb_user_data['dob']['m'] ? $fb_user_data['dob']['m'] : set_value('month');
										$sdate = $fb_user_data['dob']['d'] ? $fb_user_data['dob']['d'] : set_value('date');
									} else {
										$syear = $this->input->post('year') ? $this->input->post('year') : date('Y', strtotime($user_data['birth_date']));
										$smonth = $this->input->post('month') ? $this->input->post('month') : date('m', strtotime($user_data['birth_date']));
										$sdate = $this->input->post('date') ? $this->input->post('date') : date('d', strtotime($user_data['birth_date']));
									}
									echo form_dt_dropdown('yearId', $year, $syear, 'id="year" class="dropdown-dt"', translate_phrase('Year'), "hiddenfield");
									echo form_dt_dropdown('monthId', $month, $smonth, 'id="month" class="dropdown-dt dd-menu-mar" ', translate_phrase('Month'), "hiddenfield");
									echo form_dt_dropdown('dateId', $date, $sdate, 'id="day" class="dropdown-dt dd-menu-mar" ', translate_phrase('Day'), "hiddenfield");
									?>
										<label id="dobError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('I was born in')?>:</div>

									<div class="sfp-1-Right">
									<?php
									$born_in_city = $fb_user_data['born_in']['city']?$fb_user_data['born_in']['city']: $user_data['birth_city_name'];
									//$born_incountry_id = $born_in_country?$born_in_country:set_value('country_born');
									$born_incountry_id = $user_data['birth_country_id']?$user_data['birth_country_id']:set_value('country_born');
									$born_in_country   = $fb_user_data['born_in']['country']?$fb_user_data['born_in']['country']:$born_incountry_id;
									echo form_dt_dropdown('country_born',$country,$born_in_country,'id="country_born" class="dropdown-dt scemdowndomain" ', translate_phrase('Select country'), "hiddenfield");
									?>

										<div class="sel-emailR">
											<dl class="majordowndomain" style="width: 220px;">
												<dt>
													<input id="city_born" name="city_born" type="text"
														placeholder="<?php echo translate_phrase('Enter city name');?>"
														value="<?php echo $born_in_city;?>" onblur="setCityName()"
														onfocus="get_born_city('country_born','city_born','city_born_err');"
														class="Degree-input"> <input type="hidden"
														name="born_in_city" id="born_in_city"> <label
														id="city_lived_err" class="input-hint"></label>
												</dt>
												<!-- autocomplete dd -->
												<dd id="auto-city_borned-container" style="width: 220px;"></dd>
											</dl>
										</div>
										<label id="country_born_err"><?php echo translate_phrase(form_error('country_born'));?>
										</label> <label id="city_born_err"><?php echo translate_phrase(form_error('city_born'));?>
										</label>
									</div>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('My height is')?>:<span>*</span>
									</div>
									<div class="sfp-1-Right">
									<?php

									if ($use_meters == '1') {
										$show_height = "style='display:block;'";
										$show_feet = "style='display:none;'";
									} else {
										$show_height = "style='display:none;'";
										$show_feet = "style='display:block;'";
									}

									$feet_id = $this->input->post('feet') ? $this->input->post('feet') : "";
									$inch_id = $this->input->post('inches') ? $this->input->post('inches') : "";
									$cms_id = $this->input->post('cms') ? $this->input->post('cms') : $user_data['height'];
									echo form_dt_dropdown('height', $cms, $cms_id, 'id="cm" class="dropdown-dt" ' . $show_height, '--', "hiddenfield");
									?>
										<input type="hidden" id="use_meters"
											value="<?php echo $use_meters?>">
										<div class="centimeter" <?php echo $show_height ?>>cm</div>
										<?php echo form_dt_dropdown('feet_id', $feet, $feetFrom, 'id="feet" class="dropdown-dt" ' . $show_feet, '--', "hiddenfield"); ?>
										<div class="centimeter" <?php echo $show_feet ?>>feet</div>
										<?php echo form_dt_dropdown('inches_id', $inches, $inchFrom, 'id="inches" class="dropdown-dt" ' . $show_feet, '--', "hiddenfield"); ?>
										<div class="centimeter pad-rightNone" <?php echo $show_feet ?>>inches</div>
										<label id="heightError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>

								<div class="sfp-1-main">
									<div class="sfp-1-Left"><?php echo translate_phrase('My body type is') ?>:</div>
									<div class="sfp-1-Right">
									<?php
									$body_type_id = $this->input->post('body_type') ? $this->input->post('body_type') : $user_data['body_type_id'];
									echo form_dt_dropdown('bodyTypeId', $body_type, $body_type_id, 'id="body_type" class="dropdown-dt dropdownfull"', '', "hiddenfield");
									?>
										<label id="bodyTypeError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left"><?php echo translate_phrase('I believe I look') ?>:</div>
									<div class="sfp-1-Right">

									<?php
									$looks_id = $this->input->post('looks') ? $this->input->post('looks') : $user_data['looks_id'];
									//echo form_dropdown('looks',$looks,$looks_id,'id="looks"');
									echo form_dt_dropdown('lookId', $looks, $looks_id, 'id="looks" class="dropdown-dt dropdownfull"', '', "hiddenfield");
									?>
										<label id="looksError"
											class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('My relationship status is') ?>:</div>
									<div class="sfp-1-Right">
									<?php
									$selected = '';
									foreach ($relationship_status as $row) {
										$relationship_status = $fb_user_data['relationship_status'] ? $fb_user_data['relationship_status'] : $user_data['relationship_status_id'];
										if ($relationship_status == $row['relationship_status_id']) {
											$checked_class = "appr-cen";
											$selected = $relationship_status;
										} else
										$checked_class = "disable-butn";
										?>
										<a href="javascript:;" class="rdo_div"
											key="<?php echo $row['relationship_status_id']; ?>"> <span
											class="<?php echo $checked_class ?>"><?php echo translate_phrase(ucfirst($row['description'])); ?>
										</span> </a>
										<?php } ?>
										<input type="hidden" name="relationship_status" value="<?php echo $selected; ?>">
									</div>
								</div>
								
								<div class="sfp-1-main">
									<div class="sfp-1-Left">
									<?php echo translate_phrase("I'm") ?>:</div>
									<div class="sfp-1-Right">
									<?php
									$religious_belief_id = $this->input->post('religious_belief') ? $this->input->post('religious_belief') : $user_data['religious_belief_id'];
									//echo form_dt_dropdown('religiousBeliefId',$religious_belief,$religious_belief_id,'id="religious_belief" class="dropdown-dt dropdownfull religionDD"','Select religion',"hiddenfield");
									?>
									<?php
										$selected = '';
										foreach ($religious_belief as $id=>$value) {
											if ($religious_belief_id == $id) {
												$checked_class = "appr-cen";
												$selected = $religious_belief_id;
											} else
											$checked_class = "disable-butn";
											?>
											<a href="javascript:;" class="rdo_div" key="<?php echo $id; ?>"> <span class="<?php echo $checked_class ?>"><?php echo $value; ?></span></a>
											<?php } ?>
											<input type="hidden" name="religiousBeliefId" value="<?php echo $selected; ?>">
										<label id="religiousBeliefError" class="input-hint error error_indentation error_msg"></label>
									</div>
								</div>
								<div class="sfp-1-main" id="selfSummaryDiv">
									<div class="sfp-1-Left">
									<?php echo translate_phrase('My self summary') ?>:</div>
									<div class="sfp-1-Right">
										<textarea name="self_summary" cols="" rows=""
											class="as-E-textarea"><?php echo $user_data['self_summary']; ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div id="photos"
							class="step-form-Main Mar-top-none Top-radius-none">

							<div class="step-form-Part">
								<div class="edu-main">
									<div class="Edit-p-top1">
										<h2>
										<?php echo translate_phrase('Your Profile Photos');?>
										</h2>
									</div>
									<div class="fl">
										<div class="skil-check-area-01">
											<ul>
												<li><span> <input type="checkbox"
														onchange="user_privacy_photos(this);" tabindex="4"
														value="First Choice" class="field checkbox" name="Field2"
														id="Field2"
														<?php echo ($user_data['privacy_photos'] == 'SHOW')?'checked':''?>>
														<label for="Field2" class="choice"><?php echo translate_phrase("Viewable by your matches");?>
													</label> </span>
												</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="profile-phot-M">
									<div class="Pleft-arw" id="slider-prev" style="display: none"></div>
									<div class="photo-slider-m">
										<ul class="bxslider_profile">
										<?php if ($user_photos): ?>
										<?php foreach ($user_photos as $key => $photo): ?>
											<li class="photo_<?php echo $photo['user_photo_id'] ?>"><a
												class="upload-part fancybox" rel="gallery1"
												href="<?php echo $photo['url']?$photo['url']:base_url().'assets/images/default-profile.png'; ?>">
													<img height="150"
													src="<?php echo $photo['url']?$photo['url']:base_url().'assets/images/default-profile.png'; ?>"
													alt="<?php echo $photo['photo'] ?>" /> </a>
												<div class="Photo-dwn-btn">
												<?php if ($photo['set_primary'] == '1'): ?>
													<div class="primary-btn-text">
														<label lang="<?php echo $photo['user_photo_id'] ?>"
															class="primary-action"
															data-url="<?php echo base_url() ?>user/primary_profile_photo/"><?php echo translate_phrase('Primary Photo') ?>
														</label>
													</div>
													<?php else: ?>
													<div class="Primary-Button01">
														<label lang="<?php echo $photo['user_photo_id'] ?>"
															class="primary-action"
															onclick="make_primary_photo('<?php echo $photo['user_photo_id'] ?>',this)"
															data-url="<?php echo base_url() ?>user/primary_profile_photo/"><?php echo translate_phrase('Set as Primary') ?>
														</label>
													</div>
													<?php endif; ?>
													<div class="Delete-Primary">
														<a href="javascript:;"
															onclick="delete_photos('<?php echo $photo['user_photo_id'] ?>',this)"
															data-url="<?php echo base_url() ?>user/delete_profile_photo/"><?php echo translate_phrase('Delete') ?>
														</a>
													</div>
												</div>
											</li>
											<?php endforeach; ?>

											<?php endif; ?>
										</ul>
									</div>
									<div class="Pright-arw" id="slider-next" style="display: none"></div>
								</div>
								
								<!-- 
                                  <?php if($this->agent->is_mobile()):?>
									<div style="float:left;margin-top:15px;">
			                        	<label class="input-hint error"> <?php echo translate_phrase('Photo uploading is not yet supported on mobile devices. Please upload photos using your desktop PC after we approve your application.');?></label>
			                        </div>
									<?php else:?>
                                  <?php endif;?>
                                   -->
								<div class="Pf-btnM file-upload">
								<label id="photo-error" class="input-hint error"></label>
									<div class="Next-butM">
										<span class="upload-button">
											<label><?php echo translate_phrase('Add Photo...');?></label>
											<input type="file" data-url="<?php echo base_url() ?>user/upload/fileToUpload" id="add_profile_photo" name="fileToUpload"></span>
									</div>
								</div>
								
								
							</div>
						</div>

						<div id="education" class="step-form-Main Mar-top-none Top-radius-none">
							<div class="step-form-Part">
								<div class="Indicate-top"> *&nbsp; <?php echo translate_phrase('Indicates required field') ?></div>
								<div class="edu-main">
									<h2><?php echo translate_phrase('What education degrees/certificates have you achieved or are working towards') ?>?<span class="Redstar">*</span></h2>
									<div class="skill-select">
										<div class="f-decr customSelectTag">
										<?php
										$selectedEducationLevelValues = (!empty($user_education_level)) ? explode(',', $user_education_level) : array();
										?>
										<?php foreach ($education_level as $key => $value): ?>
											<ul>
												<li
												<?php echo (array_search($key, $selectedEducationLevelValues) !== FALSE) ? 'class="selected"' : '' ?>>
													<a class="disable-butn" id="<?php echo $key ?>"><?php echo $value ?>
												</a>
												</li>
											</ul>
											<?php endforeach; ?>
											<input type="hidden" name="user_education_level"
												id="user_education_level"
												value="<?php echo $user_education_level ?>">
										</div>
										<label id="education_level_err" class="input-hint error"></label>
									</div>
									<div class="skill-select">
										<h2><?php echo translate_phrase('Where and what did you study') ?>?<span class="Redstar">*</span></h2>
										<label id="schoolReqError" class="input-hint error"></label>
									</div>

									<?php
									if ($school_count > 0) {
										$schol_show = 'style="display: none;"';
										$schhol_button_show = 'style="display: block;"';
									} else {
										$schol_show = 'style="display: block;"';
										$schhol_button_show = 'style="display: none;"';
									}

									?>
									<div class="school-inner-container" id="list_school_main"
									<?php echo $schhol_button_show; ?>>
										<div class="study-innr-M" id="list_school">
										<?php
										foreach ($user_school_id as $row) {
											$language_id = $this -> session -> userdata('sess_language_id');
											$school_details = $this->model_user->get_school_details($row);											
											echo $list_school = $this->model_user->list_school_details($row, $school_details, $language_id);
										}
										?>
										</div>
										<div class="Edit-Button01" <?php echo $schhol_button_show; ?>
											id="add_school_button">
											<a onclick="show_div();" href="javascript:;"><?php echo translate_phrase('Add Another School') ?>
											</a>
										</div>
									</div>

								</div>

								<div class="last-bor"></div>
								<div id="add_schools" class="fl" <?php echo $schol_show;?>>
									<div class="sfp-1-main">
										<div class="sfp-1-Left"><?php echo translate_phrase('School name') ?>:<span>*</span></div>
										<div class="sfp-1-Right">
											<div class="drop-down-wrapper-full">
												<dl class="schooldowndomain">
													<dt>
														<span> <input id="school_name" class="livedin-input"
															name="school_name" type="text"
															placeholder="Type Your School Name" value=""
															onblur="show_logo();show_school_domain();">
														</span>
													</dt>
													<!-- autocomplete dd -->
													<dd id="auto-school-container"></dd>
												</dl>
												<label id="schoo_name_err" class="input-hint error"></label>
												<label id="school_error" class="input-hint error"></label> <input
													type="hidden" id="" value="no">
											</div>

											<div class="sch-logoR" id="school_logo"></div>
										</div>
									</div>

									<div class="sfp-1-main">
										<div class="sfp-1-Left"><?php echo translate_phrase('Degree') ?>:<span>*</span></div>
										<div class="sfp-1-Right">
											<input id="degree_name" class="Degree-input"
												name="degree_name" type="text"
												placeholder="<?php echo translate_phrase("e.g. Bachelor of Arts"); ?>">
											<div class="completed-ch">
												<div class="skil-check-area-01">
													<ul>
														<li><span> <input type="checkbox"
																name="is_degree_completed" id="is_degree_completed"
																value="1"><label class="choice"
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
										<div class="sfp-1-Left"><?php echo translate_phrase('Major(s)') ?>:</div>
										<div class="sfp-1-Right">
											<div class="M-topBut">
												<ul id="add_major" class="list_rows"></ul>
											</div>

											<div class="drop-down-wrapper-full">
											<?php echo form_dt_dropdown('major_id', $school_subject, '', 'id="major_id" class="dropdown-dt majordowndomain"',translate_phrase("Select major(s)")); ?>
												<label id="major_err" class="input-hint error"></label>
											</div>

											<div class="add-butM">
												<input type="hidden" name="majors_id" id="majors_id"
													value=""> <a href="javascript:;" onclick="add_majors();"
													class="Edit-Button01"><?php echo translate_phrase('Add') ?>
												</a>
											</div>

										</div>
									</div>

									<div class="sfp-1-main">

										<div class="sfp-1-Left"><?php echo translate_phrase('Minor(s)') ?>:</div>
										<div class="sfp-1-Right">
											<div class="M-topBut">
												<ul id="add_minor" class="list_rows"></ul>
											</div>
											<div class="drop-down-wrapper-full">
											<?php echo form_dt_dropdown('major_id', $school_subject, '', 'id="minor_id" class="dropdown-dt majordowndomain"',translate_phrase("Select minor(s)")); ?>
												<label id="minor_err" class="input-hint error"></label>
											</div>
											<div class="add-butM">
												<a href="javascript:;" onclick="add_minors();"
													class="Edit-Button01"><?php echo translate_phrase('Add') ?>
												</a> <input type="hidden" name="minors_id" id="minors_id"
													value="">
											</div>
										</div>
									</div>

									<div class="sfp-1-main">
										<div class="sfp-1-Left"><?php echo translate_phrase('Years attended') ?>:<span>*</span></div>
										<div class="sfp-1-Right">
										<?php
										for ($i = date('Y'); $i >= 1910; $i--) {
											$school_years[$i] = $i;
										}

										for ($i = date('Y')+5; $i >= 1910; $i--) {
											$school_years_to[$i] = $i;
										}
										?>
										<?php echo form_dt_dropdown('years_attended_start', $school_years, '', 'id="attended_start" class="dropdown-dt"', '-', "hiddenfield"); ?>
											<div class="centimeter">
											<?php echo translate_phrase('to') ?>
											</div>
											<?php echo form_dt_dropdown('years_attended_end', $school_years_to, '', 'id="attended_end" class="dropdown-dt "', '-', "hiddenfield"); ?>
											<div class="centimeter">
											<?php echo translate_phrase('(or expected graduation year)'); ?>
											</div>
											<label id="years_attended_error" class="input-hint error"></label>
										</div>
									</div>
									<div class="edu-ystudy padB-none">
										<h2><?php echo translate_phrase('Optional verification information');?></h2>
										<div class="study-innr-M">
											<div class="sfp-1-main">
												<div class="sfp-1-Left">
													<a href="#"><img
														src="<?php echo base_url() ?>assets/images/question-mark.png"
														class="que-mark" alt="" /> </a>
														<?php echo translate_phrase('School Email')?>
													:
												</div>
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
											<div class="sfp-1-main">
												<div class="sfp-1-Left"><?php echo translate_phrase('Take a photo of your diploma or school ID and upload it here') ?>:</div>
												<div class="sfp-1-Right file-upload">
													<ul class="img-container">
														<li class="upload-part upload-school-pic"><img src="" alt="" data-src="<?php if ($fb_user_data['photo']): ?><?php echo $fb_user_data['photo'] ?><?php endif ?>"></li>
													</ul>
													<div class="upload-Button-main">
														<span class="upload-button"> <label><?php echo translate_phrase('Add Photo...')?>...</label>
															<input type="file" name="photo_diploma"
															id="photo_diploma"
															data-url="<?php echo base_url() ?>user/upload/photo_diploma">
														</span>
														<div class="Delete-Photo01">
															<a href="javascript:;"
																data-url="<?php echo base_url() ?>user/delete_edu_temp_photo/">Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="sfp-1-Right">
										<input type="hidden" value="" name="user_school_id"
											id="user_school_id">
										<div class="Edit-Button01">
											<a onclick="add_school();" href="javascript:;"
												id="school_button"><?php echo translate_phrase('Add School') ?>
											</a>
										</div>
										<div class="Delete-Photo01">
											<a href="javascript:;" onclick="cancel_school();"
												id="cancel_button"><?php echo translate_phrase('Cancel') ?>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div id="career"
							class="step-form-Main Mar-top-none Top-radius-none">
							<div class="step-form-Part">
								<div class="Indicate-top">*&nbsp;<?php echo translate_phrase('Indicates required field') ?></div>
								<div class="edu-main">
									<div class="aps-d-top">
										<h2><?php echo translate_phrase('Where are you in your career') ?>?<span class="Redstar">*</span></h2>
										<div class="care-boxM">
											<!--Change by Hannan-->
										<?php echo form_dt_dropdown('career_stage_id', $carrier_stage, $user_data['career_stage_id'], 'class="dropdown-dt rangedowndomain"', translate_phrase('Select career stage'), "hiddenfield"); ?>
											<label id="careerStageError"
												class="input-hint error error_indentation error_msg"></label>
											<!--Change by Hannan-->
										</div>
									</div>
									<div class="aps-d-top">
										<h2>
										<?php echo translate_phrase('What is your annual income range') ?>
											?
										</h2>
										<div class="care-boxM">
										<?php echo form_dt_dropdown('annual_income_range_id', $annual_income_range, $user_data['annual_income_range_id'], 'class="dropdown-dt rangedowndomain"', translate_phrase('Select annual income range'), "hiddenfield"); ?>
										</div>
									</div>
									<div class="edu-ystudy">
										<h2>
										<?php echo translate_phrase('What kind of work do you do') ?>
											?
										</h2>
										<?php
										if ($company_count > 0) {
											$company_show = 'style="display: none;"';
											$company_button_show = 'style="display: block;"';
										} else {
											$company_show = 'style="display: block;"';
											$company_button_show = 'style="display: none;"';
										}
										?>
										<div class="school-inner-container" id="list_company_main"
										<?php echo $company_button_show; ?>>
											<div class="study-innr-M" id="list_company">
											<?php
											foreach ($user_company_id as $row) {
												$language_id = $this -> session -> userdata('sess_language_id');
												$company_details = $this->model_user->get_company_details($row);
												echo $list_company = $this->model_user->list_company_details($row, $company_details, $language_id);
											}
											?>
											</div>
											<div class="Edit-Button01" id="add_company_button">
												<a onclick="show_div_company();" href="javascript:;"><?php echo translate_phrase('Add Another Job') ?>
												</a>
											</div>
										</div>
									</div>
								</div>
								<div class="last-bor"></div>
								<div id="add_companies" class="fl" <?php echo $company_show?>>
									<span class="suc-msg" id="school_company"></span>

									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Company name') ?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<div class="drop-down-wrapper-full">
												<dl class="schooldowndomain">
													<dt>
														<span> <input class="Degree-input company-name"
															id="company_name" name="company_name" type="text"
															onkeyup="auto_complete_company();"
															onblur="show_company_logo();
                                                        show_company_industry();
                                                        show_company_domain();" />
														</span>
													</dt>
													<!-- autocomplete dd -->
													<dd id="auto-company-container" class="autosuggestfull"></dd>
												</dl>
												<label id="company_name_err" class="input-hint error"></label>
											</div>
											<div class="sch-logoR" id="company_logo"></div>
											<div class="M-topBut">
												<div class="skil-check-area-01">
													<ul>
														<li><span> <input type="checkbox" value="1"
																name="show_company_name" id="show_company_name"
																class="field checkbox" tabindex="4"> <label
																class="choice" for="show_company_name"><?php echo translate_phrase('Show company name to your matches') ?>
															</label> </span>
														</li>
													</ul>
												</div>
											</div>
										</div>
									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Company industry') ?>
											:
										</div>
										<div class="sfp-1-Right" id="company_industry">
										<?php echo form_dt_dropdown('industry_id', $industry, '', 'class="dropdown-dt majordowndomain"', translate_phrase('Select company industry'), "hiddenfield"); ?>
										</div>
									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
											<a href="#"><img
												src="<?php echo base_url() ?>assets/images/question-mark.png"
												class="que-mark" alt="" /> </a>
												<?php echo translate_phrase('Job function') ?>
											:
										</div>
										<div class="sfp-1-Right" id="job_function_dd">
										<?php echo form_dt_dropdown('job_function_id', $job_functions, '', 'class="dropdown-dt majordowndomain"', translate_phrase('Select job function'), "hiddenfield"); ?>
										</div>
									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
											<a href="#"><img
												src="<?php echo base_url() ?>assets/images/question-mark.png"
												class="que-mark" alt="" /> </a>
												<?php echo translate_phrase('Job title') ?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<div class="post-input-wrap">
												<input id="job_title" type="text" class="Degree-input"
													placeholder="e.g. Software Engineer" /> <label
													id="job_title_err" class="input-hint error"></label>
											</div>
										</div>
									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Job location') ?>
											:<span>*</span>
										</div>
										<div class="sfp-1-Right">
											<dl class="schooldowndomain">
												<dt>
													<span>
														<div class="post-input-wrap job_city">
															<input id="job_city" name="job_city" type="text"
																class="Degree-input"
																placeholder="<?php echo translate_phrase('City name, country name')?>"
																onclick="auto_complete_city(this)" /> <label
																id="job_city_id_err" class="input-hint error"></label>
														</div> </span>
												</dt>
												<!-- autocomplete dd -->
												<dd id="auto-city-containter" class="autosuggestfull"></dd>
											</dl>


										</div>
									</div>
									<div class="sfp-1-main">
										<div class="sfp-1-Left">
										<?php echo translate_phrase('Years worked') ?>
											:
										</div>
										<div class="sfp-1-Right">
										<?php
										$job_year_end['9999'] = 'Present';
										for ($i = date('Y'); $i >= 1910; $i--) {
											$job_year_end[$i] = $i;
										}
										?>
										<?php echo form_dt_dropdown('years_worked_start', $school_years, '', 'id="years_worked_start_dl" class="dropdown-dt drop-start-year"', ' - - -', "hiddenfield"); ?>
											<div class="centimeter">
											<?php echo translate_phrase('to') ?>
											</div>
											<?php echo form_dt_dropdown('years_worked_end', $job_year_end, '', 'id="years_worked_end_dl" class="dropdown-dt drop-end-year"', ' - - -', "hiddenfield"); ?>
											<label id="year_work_err" class="input-hint error"></label>
										</div>
									</div>
									<div class="edu-ystudy padB-none">
										<h2>
										<?php echo translate_phrase('Optional verification information') ?>
										</h2>
										<div class="study-innr-M">
											<div class="sfp-1-main">
												<div class="sfp-1-Left">
													<a href="#"><img
														src="<?php echo base_url() ?>assets/images/question-mark.png"
														class="que-mark" alt="" /> </a>
														<?php echo translate_phrase('Company Email')?>
													:
												</div>

												<div class="sfp-1-Right">
													<div class="post-input-wrap">
														<input id="company_email_address"
															name="company_email_address" type="text"
															class="post-input" /> <label id="company_email_error"
															class="input-hint error"></label>
													</div>

													<div class="drop-down-wrapper-school_domain">
														<div class="sel-emailR" id="company_domain"></div>
														<label id="company_domain_err" class="input-hint error"></label>
													</div>
												</div>

											</div>
											<div class="sfp-1-main">
												<div class="sfp-1-Left">
												<?php echo translate_phrase('Take a photo of your business card and upload it here') ?>
													:
												</div>

												<div class="sfp-1-Right file-upload">
													<ul class="img-container">
														<li class="upload-part upload-company-pic">
															<!-- 
                                                          <img src="" alt="profile-photo" data-src="<?php if ($fb_user_data['photo']): ?><?php echo $fb_user_data['photo'] ?><?php else: echo base_url() ?>assets/images/default-profile.png<?php endif ?>">
                                                           --> <img
															src="" alt="" data-src="" />
														</li>
													</ul>
													<div class="upload-Button-main">
														<span class="upload-button"> <label><?php echo translate_phrase('Add Photo...') ?>
														</label> <input type="file" name="photo_business_card"
															id="photo_business_card"
															data-url="<?php echo base_url() ?>user/upload/photo_business_card">
														</span>
														<div class="Delete-Photo01">
															<a href="javascript:;"
																data-url="<?php echo base_url() ?>user/delete_edu_temp_photo/"><?php echo translate_phrase('Delete') ?>
															</a>
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>
									<div class="sfp-1-Right">
										<input type="hidden" value="" name="user_company_id"
											id="user_company_id">
										<div class="Edit-Button01">
											<a onclick="add_company();" href="javascript:;"
												id="company_button"><?php echo translate_phrase('Add Job') ?>
											</a>
										</div>
										<div class="Delete-Photo01">
											<a href="javascript:;" onclick="cancel_company();"
												id="company_cancel_button"><?php echo translate_phrase('Cancel') ?>
											</a>
										</div>
									</div>
								</div>
							</div>

						</div>

						<div id="personality"
							class="step-form-Main Mar-top-none Top-radius-none">
							<div class="step-form-Part">
								<div class="edu-main">
									<div class="aps-d-top" id="descriptiveWrodDiv">
										<h2>
										<?php echo translate_phrase('What five words would your friends use to describe you') ?>
											?
										</h2>
										<div class="f-decrMAIN" id="your-personality">
											<div class="f-decr">
											<?php $selectedDescriptiveWordsArray = (!empty($user_descriptive_word)) ? explode(',', $user_descriptive_word) : array() ?>
												<ul>
												<?php foreach ($descriptive_word as $row) { ?>
													<li id="<?php echo $row['descriptive_word_id']; ?>"
													<?php echo (array_search($row['descriptive_word_id'], $selectedDescriptiveWordsArray) !== FALSE) ? 'class="selected"' : '' ?>>
														<a class="disable-butn" href="javascript:;"
														id="<?php echo $row['descriptive_word_id']; ?>"><?php echo ucfirst($row['description']); ?>
													</a>
													</li>
													<?php } ?>
												</ul>
												<input type="hidden" id="descriptive_word_id"
													name="descriptive_word_id"
													value="<?php echo $user_descriptive_word ?>">
											</div>
										</div>
									</div>
									<!-----------------------NEW ADDITION--------------------------->
									<div class="aps-d-top" id="userInterestDiv">
										<h2>
										<?php echo translate_phrase('What are your interests and hobbies'); ?>
										</h2>
										<?php
										$selectedInterests = (!empty($user_interests)) ? explode(',', $user_interests) : array();
										if (!empty($interests)) {
											foreach ($interests['parentDetails'] as $id => $catName) {

												echo '<div class="f-decrMAIN" id=hobbiesAndInterest>
                                                    <h3>' . $catName . '</h3>
                                                    <div class="f-decr">
                                                    <ul>';
												foreach ($interests['childDetails'][$id] as $key => $value) {
													$className = (array_search($value->interest_id, $selectedInterests) !== FALSE) ? 'class="selected"' : '';
													echo '<li id ="' . $value->interest_id . '" ' . $className . '>
                                                    <a class="disable-butn" href="javascript:;" id="' . $value->interest_id . '">' . $value->description . '</a>
                                                  </li>';
												}

												echo '</ul></div>
                                                    </div>';
											}

											echo '<input type="hidden" id="interestWordId" name="interests" value="' . $user_interests . '">';
										} else {
											echo translate_phrase('No interest found');
										}
										?>

									</div>

								</div>
							</div>
						</div>

						<div id="others"
							class="step-form-Main Mar-top-none Top-radius-none">
							<div class="sfp-1-main" id="smokignStatusDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('I smoke'); ?>
									:
								</div>

								<div class="sfp-1-Right">
								<?php
								$selected = '';
								foreach ($smoking_status as $key => $row) {
									$selected = $user_data['smoking_status_id'];
									if ($selected == $row['smoking_status_id'])
									$checked_class = "Intro-Button-sel";
									else
									$checked_class = "Intro-Button";
									?>
									<div
										class="<?php echo $checked_class; ?><?php echo ($key != 0) ? ' Bor-left-None' : '' ?>">
										<a href="javascript:;"
											onclick="radio_select('smoking_status', '<?php echo $row['smoking_status_id']; ?>', this)"><?php echo translate_phrase(ucfirst($row['description'])); ?>
										</a>
									</div>
									<?php } ?>
									<input type="hidden" name="smoking_status" id="smoking_status"
										value="<?php echo $selected; ?>">
								</div>

							</div>
							<div class="sfp-1-main" id="drinkignStatusDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('I drink'); ?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								$selected = '';
								foreach ($drinking_status as $key => $row) {
									$selected = $user_data['drinking_status_id'];
									if ($selected == $row['drinking_status_id'])
									$checked_class = "Intro-Button-sel";
									else
									$checked_class = "Intro-Button";
									?>
									<div
										class="<?php echo $checked_class; ?><?php echo ($key != 0) ? ' Bor-left-None' : '' ?>">
										<a href="javascript:;"
											onclick="radio_select('drinking_status', '<?php echo $row['drinking_status_id']; ?>', this)"><?php echo translate_phrase(ucfirst($row['description'])); ?>
										</a>
									</div>
									<?php } ?>
									<input type="hidden" id="drinking_status"
										name="drinking_status" value="<?php echo $selected; ?>">
								</div>
							</div>
							<div class="sfp-1-main" id="exerciseFrequencyDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('I exercise'); ?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								$selected = '';
								foreach ($exercise_frequency as $key => $row) {
									$selected = $user_data['exercise_frequency_id'];
									if ($selected == $row['exercise_frequency_id'])
									$checked_class = "Intro-Button-sel";
									else
									$checked_class = "Intro-Button";
									?>
									<div
										class="<?php echo $checked_class; ?><?php echo ($key != 0) ? ' Bor-left-None' : '' ?>">
										<a href="javascript:;"
											onclick="radio_select('exercise_frequency', '<?php echo $row['exercise_frequency_id']; ?>', this)"><?php echo translate_phrase(ucfirst($row['description'])); ?>
										</a>
									</div>
									<?php } ?>
									<input type="hidden" id="exercise_frequency"
										name="exercise_frequency" value="<?php echo $selected; ?>">
								</div>
							</div>
							<div class="sfp-1-main" id="residenceTypeDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('I live in a'); ?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								$residence_type_id = $this->input->post('residence_type') ? $this->input->post('residence_type') : $user_data['residence_type'];
								echo form_dt_dropdown('residence_type', $residence_type, $residence_type_id, 'class="dropdown-dt domaindropdown"', translate_phrase("Select residence type"), "hiddenfield");
								?>
								</div>
							</div>
							<div class="sfp-1-main" id="childStatusDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('I have'); ?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								$child_status_id = $this->input->post('child_status_id') ? $this->input->post('child_status_id') : $user_data['child_status_id'];
								echo form_dt_dropdown('child_status', $child_status, $child_status_id, 'class="dropdown-dt domaindropdown"', translate_phrase("Select"), "hiddenfield");								
								?>
								</div>
							</div>
							<div class="sfp-1-main" id="childPlanDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('I want'); ?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								$child_plan_id = $this->input->post('child_plan_id') ? $this->input->post('child_plan_id') : $user_data['child_plan_id'];
								echo form_dt_dropdown('child_plan', $child_plans, $child_plan_id, 'class="dropdown-dt domaindropdown"', translate_phrase("Select children plans"), "hiddenfield");
								?>
								</div>
							</div>
							<div class="sfp-1-main" id="eyeColorDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('My eye color is'); ?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								$eye_color_id = $this->input->post('eye_color_id') ? $this->input->post('eye_color_id') : $user_data['eye_color_id'];
								echo form_dt_dropdown('eye_color', $eye_color, $eye_color_id, 'class="dropdown-dt domaindropdown"', translate_phrase("Select"), "hiddenfield");
								?>
								</div>
							</div>
							<div class="sfp-1-main" id="hairColorDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('My hair color is'); ?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								$hair_color_id = $this->input->post('hair_color_id') ? $this->input->post('hair_color_id') : $user_data['hair_color_id'];
								echo form_dt_dropdown('hair_color', $hair_color, $hair_color_id, 'class="dropdown-dt domaindropdown"', translate_phrase("Select"), "hiddenfield");
								?>
								</div>
							</div>
							<div class="sfp-1-main" id="hairLengthDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('My hair length is'); ?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								$hair_length_id = $this->input->post('hair_length_id') ? $this->input->post('hair_length_id') : $user_data['hair_length_id'];
								echo form_dt_dropdown('hair_length', $hair_length, $hair_length_id, 'class="dropdown-dt domaindropdown"', translate_phrase("Select"), "hiddenfield");
								?>
								</div>
							</div>
							<div class="sfp-1-main" id="skinToneDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('My skin tone is'); ?>
									:
								</div>
								<div class="sfp-1-Right">
								<?php
								$skin_tone_id = $this->input->post('skin_tone_id') ? $this->input->post('skin_tone_id') : $user_data['skin_tone_id'];
								echo form_dt_dropdown('skin_tone', $skin_tone, $skin_tone_id, 'class="dropdown-dt domaindropdown"', translate_phrase("Select"), "hiddenfield");
								?>
								</div>
							</div>

							<div class="sfp-1-main" id="eyeWearDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('I usually wear'); ?>
									:
								</div>

								<div class="sfp-1-Right customSelectTag">
								<?php
								$eye_wear_ids = $this->input->post('usually_wear') ? $this->input->post('usually_wear') : $userEyeWear;
								$eye_wear_id = implode(',', $eye_wear_ids);
								foreach ($eye_wear as $value)
								{
									$key = $value['eyewear_id'];
									?>
									<ul>
										<li
										<?php echo (array_search($key,$eye_wear_ids) !== FALSE)? 'class="selected"':'' ?>>
											<a id="<?php echo $key?>" class="disable-butn"
											href="javascript:;"><?php echo $value['description']?> </a>
										</li>
									</ul>

									<?php } ?>
									<input type="hidden" name="usually_wear"
										value="<?php echo $eye_wear_id; ?>">
								</div>
							</div>

							<div class="sfp-1-main" id="spokenLanguageDiv">
								<div class="sfp-1-Left">
								<?php echo translate_phrase('I speak'); ?>
									:
								</div>
								<div class="sfp-1-Right">
									<label id="spk_lang_err" class="input-hint"></label> <label
										id="prof_err" class="input-hint"></label>
									<div class="M-topBut">
										<ul id="add_lang" class="list_rows">
										<?php
										$spoken_language_id = "";
										$spoken_language_level_id = "";
										$proficiency_id = $this->model_user->get_language_level_id(translate_phrase('Fluent'));
										if (!empty($fb_user_data['languages'])) {
											foreach ($fb_user_data['languages'] as $Key => $value) {
												$spoken_language_id .= $Key . ',';
												$spoken_language_level_id .= $proficiency_id . ',';
												?>
											<li class="Fince-But" id="lang<?php echo $Key; ?>"><a
												href="javascript:;"> <?php echo translate_phrase($value); ?>(<?php echo translate_phrase('Fluent'); ?>)
													<img src="<?php echo base_url() ?>assets/images/cross.png"
													onclick="remove_language('<?php echo $Key; ?>', '<?php echo $proficiency_id; ?>');"
													title="Remove"> </a>
											</li>
											<?php
											}
											$spoken_language_id = rtrim($spoken_language_id, ',');
											$spoken_language_level_id = rtrim($spoken_language_level_id, ',');
										} else {
											?>
											<?php if (!empty($user_spoken_language)): ?>
											<?php foreach ($user_spoken_language as $key => $value): ?>
											<?php $fluency_id = $this->model_user->get_language_level_id(translate_phrase($user_spoken_language_fluency[$key])); ?>
											<li class="Fince-But" id="lang<?php echo $key ?>"><a> <?php echo translate_phrase($value) ?>(<?php echo $user_spoken_language_fluency[$key] ?>)
													<img src="<?php echo base_url() ?>assets/images/cross.png"
													onclick="remove_language('<?php echo $key ?>','<?php echo $fluency_id ?>')">
											</a>
											</li>
											<?php endforeach; ?>
											<?php
											$spoken_language_id = $languageCSV;
											$spoken_language_level_id = $fluencyCSV;
											?>
											<?php endif; ?>
											<? }
											?>
										</ul>
									</div>
									<?php echo form_dt_dropdown('spoken_language', $spoken_language, '', 'id="spoken_language" class="animate-dropdown scemdowndomain menu-Rightmar"'); ?>
									<?php echo form_dt_dropdown('proficiency', $proficiency, '', 'id="proficiency" class="animate-dropdown Profedowndomain"'); ?>
									<div class="add-butM menu-Rightmar">
										<div class="Edit-Button01">
											<input type="hidden" name="spoken_language_id"
												id="spoken_language_id"
												value="<?php echo $spoken_language_id; ?>"> <input
												type="hidden" name="spoken_language_level_id"
												id="spoken_language_level_id"
												value="<?php echo $spoken_language_level_id; ?>"> <a
												href="javascript:;" onclick="add_languages();"><?php echo translate_phrase('Add') ?>
											</a>
										</div>
									</div>
								</div>
							</div>
							<div class="sfp-1-main" id="nationalityDiv">
								<div class="sfp-1-Left">My nationality is:</div>
								<div class="sfp-1-Right">
									<div class="M-topBut">
										<ul id="add_nationality" class="list_rows">
										<?php if (!empty($user_nationality)): ?>
										<?php foreach ($user_nationality as $key => $value): ?>
											<li class="Fince-But" id="nationality<?php echo $key ?>"><a
												href="javascript:;"><?php echo $value ?><img
													src="<?php echo base_url() ?>assets/images/cross.png"
													onclick="remove_nationality(<?php echo "'$key'" ?>);"
													title="Remove"> </a></li>
													<?php endforeach; ?>
													<?php endif ?>

										</ul>
									</div>
									<?php echo form_dt_dropdown('nationality_id', $nationality, '', 'id="nationality" class="dropdown-dt majordowndomain"'); ?>
									<div class="add-butM">
										<div class="Edit-Button01">
											<input type="hidden" value="<?php echo $nationalityCSV ?>"
												name="nationality_id" id="nationality_id"> <a
												href="javascript:;" onclick="add_nationality();"><?php echo translate_phrase('Add') ?>
											</a>
										</div>
									</div>

								</div>
							</div>
							<div class="sfp-1-main" id="livedInDiv">
								<div class="sfp-1-Left">I have lived in:</div>
								<div class="sfp-1-Right">
									<div class="M-topBut">
										<ul id="add_living" class="list_rows">
										<?php if (!empty($user_lived_in_city)): ?>
										<?php foreach ($user_lived_in_city as $key => $value): ?>
											<li class="Fince-But"
												id="<?php echo 'living_' . $value->country_id . '_' . $value->city_name ?>">
												<a href="javascript:;"><?php echo $value->city_name . ',' . $value->countryName ?><img
													src="<?php echo base_url() ?>assets/images/cross.png"
													onclick="remove_lived_city('<?php echo $value->country_id ?>', '<?php echo $value->city_name ?>')"
													title="Remove"> </a>
											</li>
											<?php endforeach; ?>
											<?php endif; ?>
										</ul>
									</div>
									<?php echo form_dt_dropdown('country_lived', $country, '', 'id="country_lived" class="dropdown-dt majordowndomain"'); ?>
									<label id="country_lived_err" class="input-hint"></label>
									<div class="M-topBut">
										<dl class="majordowndomain spokenLanguage">
											<dt>
												<span> <input type="text" class="livedin-input"
													id="city_lived" name="city_lived"
													placeholder="<?php echo translate_phrase('Enter city name'); ?>"
													onfocus="get_city('country_lived', 'city_lived', 'city_liv_err');" />
													<label id="country_city_lived_err" class="input-hint"></label>
												</span>
											</dt>
											<!-- autocomplete dd -->
											<dd id="auto-city_lived-container"></dd>
										</dl>


										<div class="add-butM media-padd-add">
											<input type="hidden" name="lived_country_id"
												id="lived_country_id"
												value="<?php echo $livedInCityCountryHiddenValues ?>"> <input
												type="hidden" name="lived_city_id" id="lived_city_id"
												value="<?php echo $livedInCityHiddenValues ?>" on>
											<div class="Edit-Button01">
												<a href="javascript:;" onclick="add_living_country();"><?php echo translate_phrase('Add') ?>
												</a>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>

				</div>
				<!--emp-B-tabing-prt-->
				<div class="Nex-mar Save-mar-width" id="edit-submit"
					style="display: none">
					<input type="submit" id="submit_form" lang=""
						onclick="return validateThisForm()" class="Next-butM"
						value="<?php echo translate_phrase('Save Changes')?>"
						name="submit">
				</div>
			</div>
		</form>
	</div>
	<!--content-part-->
</div>

<script type="text/javascript">

/* Validation FROM general.js*/

function next_step1() {
    
    var active_li = $('.edit-profile-content ul li.active');
    var active_div = $(active_li).children('a').attr('href').substr(1);
    
     if(active_div=='basics'){
        if(validateBasicsTab()){
            next_active_div();
        }
    }
    else if(active_div=='photos'){
    	if(photos_validaion()){
            next_active_div();
        }
    }
    else if(active_div=='education'){
   	if(education_validaion()){
           next_active_div();
       }
   }
   else if(active_div=='career'){
   	if(validateCareerTab()){
           next_active_div();
       }
   }
   else if(active_div=='personality'){
		return next_active_div();
        
    }
    else{
	    return true;
    }   
 }
 
 function next_active_div(){
     var active_li = $('.edit-profile-content ul li.active');
     
    if ($(active_li).next().length > 0) {
        $(active_li).next().children('a').trigger('click');
    }else{
        $('#ureg_sub').hide();
        $('#submit_button').show();
    }
    return true;
 }
 
 
function photos_validaion(){
    var flag=1;
    var use_meters = jQuery('#use_meters').val();
    if(use_meters == 1)
    {
        if ($('#height').val()=='') {
        showError('heightError','<?php echo translate_phrase("Height  is required")?>');
        flag=0;
        }
        else
        {
            jQuery('#heightError').text('');

        }
    }
    else
    {
        var feet = jQuery('#feet_id').val();
        var inch = jQuery('#inches_id').val();
        if(feet == "" || inch == "")
        {
            showError('heightError','<?php echo translate_phrase("Height  is required")?>');
            flag=0;
        }
        else
        {
            jQuery('#heightError').text('');
        }
    }
    
    
    if ($('#ethnicity').val()=='') {
      	showError('ethnicityError','<?php echo translate_phrase("Please specify your ethnicity");?>');
        flag=0;
    }
    else
    {
        jQuery('#ethnicityError').text('');
    }
    
    if ($('#bodyTypeId').val()=='') {
        showError('bodyTypeError','<?php echo translate_phrase("Body type is required");?>');
        flag=0;
    }
    else
    {
        jQuery('#bodyTypeError').text('');
        
    }
    
    if ($('#lookId').val()=='') {
        showError('looksError','<?php echo translate_phrase("Please select your looks preferences");?>');
        flag=0;
    }
    else
    {
        jQuery('#looksError').text('');
        
    }
    
    if ($('#religiousBeliefId').val()=='') {
        showError('religiousBeliefError','<?php echo translate_phrase("Please specify your religious beliefs")?>');
        flag=0;
    }
    else
    {
        jQuery('#religiousBeliefError').text('');
    }
    
    if ($('#district').val()=='') {
        showError('districtError',$('#districtError').attr('error_txt'));
        flag=0;
    }
    else
    {
        $('#districtError').text('');
    }
    
    
    if($(".bxslider_profile li").length == 0)
    {
    	flag = 0;
    	$("#user-photo-container").siblings('.file-upload').append('<label class="input-hint mobile-error error-msg"> <?php echo translate_phrase("Please upload profile picture.")?></label>');    	
    }
    else
    {
    	$("#user-photo-container").siblings('.file-upload').find('label.mobile-error').slideUp();
    }
    
    if(flag==0)
        return false;
    else   
        return true;
}
function validateBasicsTab()
{
    var flag = true;
    if(jQuery.trim(jQuery('#first_name').val()) == "")
    {
        // show first name error
        showError('firstNameError','<?php echo translate_phrase("First name is required")?>');
        // jQuery('#first_name').focus();
        jQuery('body').scrollTo(jQuery('#first_name'),800);
        flag = false;
    }
    else
    {
        // hide first name error
        hideError('firstNameError');
        // flag = true;
    }
    
    
    if(jQuery.trim(jQuery('#last_name').val()) == "")
    {
        // show last name error
        showError('lastNameError','<?php echo translate_phrase("Last name is required")?>');
        // jQuery('#last_name').focus();
        jQuery('body').scrollTo(jQuery('#last_name'),800);
        flag = false;
    }
    else
    {
        // hide last name error
        hideError('lastNameError');
        // flag = true;
    }
    
    
    if(jQuery('#gender').val() == "")
    {
        // show gender error
        showError('genderError','<?php echo translate_phrase("Gender is required")?>');
        jQuery('body').scrollTo(jQuery('#gender').parent(),800);
        flag = false;
    }
    else
    {
        // hide gender error
        hideError('genderError');
        // flag = true;
    }
    
    if(jQuery('#ethnicity').val() == "")
    {
        // show ethnicity error
        showError('ethnicityError','<?php echo translate_phrase("Please specify your ethnicity");?>');
        jQuery('body').scrollTo(jQuery('#ethnicity').parent(),800);
        flag = false;
    }
    else
    {
        // hide ethnicity error
        hideError('ethnicityError');
        // flag = true;
    }
    
    /*-----Want to date input is an array hence foreach is needed*/
    var lookingForInputs = jQuery('input[name="want_to_date[]"]');
    if(jQuery(lookingForInputs[0]).val() == "" && jQuery(lookingForInputs[1]).val() == "")
    {
        showError('wantToDateError','<?php echo translate_phrase("Please specify who you want to date");?>');
        jQuery('body').scrollTo(jQuery('#want_to_date').parent(),800);
        flag = false;
    }
    else
    {
        hideError('wantToDateError');
    }
    /*-----------------------------------------------------------*/
    
    /*---------------------looking for relationship type---------------*/
    var wantGender = jQuery('input[name="looking_for[]"]');
    if(jQuery(wantGender[0]).val() == "" && jQuery(wantGender[1]).val() == "" && jQuery(wantGender[2]).val() == "" && jQuery(wantGender[3]).val() == "" && jQuery(wantGender[4]).val() == "")
    {
        showError('lookingForError','<?php echo translate_phrase("What kind of relationship are you looking for");?>');
        jQuery('body').scrollTo(jQuery('#looking_for').parent(),800);
        flag = false;
    }
    else
    {
        hideError('lookingForError');
        // flag = true;
    }
    
    if($('#yearId').val()=="" || $('#monthId').val()=="" || $('#dateId').val()=="")
    {
        // show bday error
        showError('dobError','<?php echo translate_phrase("Please enter your full birthdate")?>');
        jQuery('body').scrollTo(jQuery('#dobError').parent(),800);
        flag = false;
    }
    else
    {

    	var year = $('#yearId').val();
    	var dayz = 28;
    	if(((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0))
    	{
    		dayz = 29;
    	}
    	
    	if(($('#monthId').val() == 2) && ($('#dateId').val() > dayz))
    	{
    		showError('dobError','<?php echo translate_phrase("Please enter a valid date for your birthdate");?>');
    		jQuery('body').scrollTo(jQuery('#dobError').parent(),800);
    		flag = false;
    	}
    	else
    	{
    		 hideError('dobError');
    	}
    	
        // hide bday error
        //hideError('dobError');
        // flag = true;
    }
    
    
    if(validateHeight() === false)
    {
        // show height error
        showError('heightError','Please specify your height');
        jQuery('body').scrollTo(jQuery('#heightError').parent(),800);
        flag = false;
    }
    else
    {
        // hide height error
        hideError('heightError');
        // flag = true;
    }
    
    
     
    if ($('#district').val()=='') {
        showError('districtError',$('#districtError').attr('error_txt'));
        flag=0;
    }
    else
    {
        $('#districtError').text('');
    }
    
    if ($('#country').val()=='' && $('#current_city_id').val()=='') {
        
        showError('liveInError','<?php echo translate_phrase("Please select the country and city you live in");?>');
       	flag=0;
    }
    else if ($('#current_city_id').length == 0 || $('#current_city_id').val()=='') {
        
        showError('liveInCITYError','<?php echo translate_phrase("Please select the city you live in");?>');
       	flag=0;
    }
    else
    {
        jQuery('#liveInError').text('');
        jQuery('#liveInCITYError').text('');
        
    }
    if(flag == true)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function validateEducationTab()
{
    var flag = true;
    
    if(jQuery('#user_education_level').val() == "")
    {
        showError('education_level_err','<?php echo translate_phrase("Please specify your education level");?>');
        jQuery('body').scrollTo(jQuery('#education_level_err').parent(),800);
        flag = false;
        // return false;
    }
    else
    {
        hideError('education_level_err');
        // return true;
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


 function education_validaion()
 {
    var flag = true;
        
     var educationMilestone = checkEducationMilestones();
     
     if(educationMilestone != true)
     {
         jQuery('#education_level_err').text('<?php echo translate_phrase("Please specify your education status")?>');
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
         showError('schoolReqError','<?php echo translate_phrase("You should add atleast one school")?>');
         flag = false;
     }
     else
     {
         hideError('schoolReqError');
         // flag = true;
     }
     
     
     return flag;
 }
 
function validateCareerTab()
{
    if(jQuery('#career_stage_id').val() == "")
    {
        showError('careerStageError','<?php echo translate_phrase("Please specify your career stage");?>');
        jQuery('body').scrollTo(jQuery('#careerStageError').parent(),800);
        return false;
    }
    else
    {
        hideError('careerStageError');
        return true;
    }
}

function validateHeight()
{
    var use_meters = jQuery('#use_meters').val();
    if(use_meters == 1)
    {
        if ($('#height').val()=='') 
        {
            // show_val_message('Please specify your height', 'error', 'signup',
			// "height_err");
            showError('heightError','<?php echo translate_phrase("Height  is required");?>');
            return false;
        }
        else
        {
            jQuery('#heightError').text('');
            return true;
        }
    }
    else
    {
        var feet = jQuery('#feet_id').val();
        var inch = jQuery('#inches_id').val();
        if(feet == "" || inch == "")
        {
            showError('heightError','<?php echo translate_phrase("Height  is required");?>');
            flag=0;
            return false;
        }
        else
        {
            jQuery('#heightError').text('');
            return true;
        }
    }
}
/* END Validation */


 var prevInsertCompany = '';
 function get_city(country,city,error){
        var country_id = $('#'+country).find('dt a').attr('key');
        
        //$( "#"+city ).select();
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

                    $("#"+city).autocomplete({
                    appendTo: "#auto-city_lived-container",
                	minLength: 1,
                        source: availableTags
                    });
                }     
            });
         }
}

 function get_born_city(country,city,error){
     var country_id = $('#'+country).find('dt a').attr('key');
     
     //$( "#"+city ).select();
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

                 $("#"+city).autocomplete({
                 appendTo: "#auto-city_borned-container",
             	minLength: 1,
                     source: availableTags
                 });
             }     
         });
      }
}

 
    
    
    function add_living_country(){
        var id             = $("#country_lived").find('dt a').attr('key').trim();
        var country_lived  = $("#country_lived").find('dt a span').html().trim();
        var city_lived     = $("#city_lived").val().trim();
        lived_country_id   = $("#lived_country_id").val().trim();
        lived_city_id      = $("#lived_city_id").val().trim();
        
        if(id!="" && city_lived!=""){
            $('#country_lived_err').html('');
            $('#country_city_lived_err').html(''); 
            
            if(lived_country_id!="") 
                var country_id    = lived_country_id+','+id; 
            else  
                var  country_id   = id;
           

            //city add
            if(lived_city_id!="") 
                var city_id    = lived_city_id+','+city_lived; 
            else  
                var  city_id   = city_lived;
           
            if (((lived_city_id.toLowerCase()).indexOf(city_lived.toLowerCase()) == -1) || (lived_country_id.indexOf(id) == -1)) {
                
                $("#lived_country_id").val(country_id);
                $("#lived_city_id").val(city_id);
                var city_lived_id  = city_lived.split(' ').join('');
                $("#add_living").append('<li class="Fince-But" id="living_'+id+'_'+city_lived_id+'" ><a href="javascript:;">'+city_lived+', '+country_lived+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_lived_city('+"'"+id+"','"+city_lived+"'"+');" title="Remove"></a></li>');
                $("#city_lived").val('');
            }
       }else{
    	   if(id=="")
                $('#country_lived_err').addClass('error error_msg').html('<?php echo translate_phrase("Country is required")?>');
           
           if(city_lived == '')
           {
        	   $('#country_city_lived_err').addClass('error error_msg').html('<?php echo translate_phrase("City is required")?>');
          	}
       }
    }
    function remove_lived_city(country_id,city_id){
        var lived_country_array           = new Array();
        var lived_city_array              = new Array();
        var lived_country_id              = document.getElementById('lived_country_id').value;
        var lived_city_id                 = document.getElementById('lived_city_id').value;
        
        //remove from language hidden field
        var lived_country_array           = lived_country_id.split(','); 
        var country_index                 = lived_country_array.indexOf(country_id);
        lived_country_array.splice(country_index, 1);
        var lived_country_id              = lived_country_array.join(); 
        
        //remove from proficiency hidden field
        var lived_city_array              = lived_city_id.split(','); 
        var city_index                    = lived_city_array.indexOf(city_id);
        lived_city_array.splice(city_index, 1);
        var lived_city_id                 = lived_city_array.join(); 
       
        city_id  = city_id.split(' ').join('');
        $('#living_'+country_id+'_'+city_id).remove();
        $("#lived_city_id").val(lived_city_id);
        $("#lived_country_id").val(lived_country_id);

    }
    function add_interests(){
        var interest      = $('#interest').val().trim();
        var added_id      = $('#interest_id').val();
        if(interest!=""){
            if(added_id!="") 
                var interesr_ids    = added_id+','+interest; 
            else  
                var  interesr_ids   = interest;
            interest_id  = interest.split(' ').join('');

            if (added_id.toLowerCase().indexOf(interest.toLowerCase()) == -1) {
              $("#interest_id").val(interesr_ids);
              $("#add_interest").append('<li id="interest'+interest_id+'" class="delete_list"><span class="plain-text" >'+interest+'</span><span><a href="javascript:remove_interest('+"'"+interest+"'"+');" title="Remove"><img src="<?php echo base_url() ?>assets/images/delete.png"></a></span></li>');
            };

            $("#interest").val('');
       }
    }
    function remove_interest(interest){
        var interest_array     = new Array();
        var added_id           = document.getElementById('interest_id').value;
        var interest_array     = added_id.split(','); 
        var interest_index     = interest_array.indexOf(interest);
        interest_array.splice(interest_index, 1);
        var added_id              = interest_array.join(); 
        interest_id  = interest.split(' ').join('');
        $('#interest'+interest_id).remove();
        $("#interest_id").val(added_id);
    }

    function add_descriptive_word(decriptive_word_id){
        var desc   = $('#descriptive_hidden'+decriptive_word_id).val();
        if(desc=='0'){
            $('#descriptive_hidden'+decriptive_word_id).val('1'); 
            url = '<?php echo base_url(); ?>' +"user/add_descriptive_word/";
        }if(desc=='1'){
            $('#descriptive_hidden'+decriptive_word_id).val('0'); 
            url = '<?php echo base_url(); ?>' +"user/remove_descriptive_word/";
        }
        
        $.ajax({ 
            url:url, 
            type:"post",
            data:"decriptive_word_id="+decriptive_word_id+'&keyword=',
            cache: false,
            success: function (data) {
            }     
       });
       
    }
/*--------------------------------CPOIED from step1---------------------------------*/
	function add_school(from_validate){
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
      
       if(school_name!="" && degree!="" && (yr_start !=""  && yr_start != 0) && (yr_end != "" && yr_end != 0) && yearsAttendedRangeIsCorrect === true){
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
                                 goToScroll('user_school'+user_school_id);
                                 $('#add_schools').fadeOut();
                            }
                            else{
                            $("#list_school").html(data);
                            $("#school_error").html('');
                            hideError('schoolReqError');
                            $('#add_schools').fadeOut();
                            goToScroll('list_school_main');
                            }
                            $("#list_school").fadeIn();
                            $('#school_button').html('<?php echo translate_phrase("Add School")?>');
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
                            $('#years_attended_error').html('');
                            
                            $('#degree_err').html('');
                            $('#school_email_error').html('');
                           // $('#add_schools.file-upload img').attr('src', $('#add_companies .file-upload img').attr('data-src'));
                            $('#add_school_button').fadeIn();
                            $('#list_school_main').fadeIn();
                            $("#school_photo_id").html('');
                            $('#user_school_id').val('');
                        	currnetSchoolName = '';
                        }
                        
                        if( typeof from_validate != undefined && from_validate)
                        {
                        	console.log('submitting..');
                            $("#submit_form").attr('lang','redirect').click();
                    		 //HTMLFormElement.prototype.submit.call($('#ProfileForm')[0]);
                        }
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
	            
       		}
      
    }
    
    function add_company(from_validate){
        var company_name      = $('#company_name').val();
        var industry_id       = $('#industry_id').val();
        var job_functions     = $('#job_function_id').val();
        var job_title         = $('#job_title').val();
        var show_company_name = '';
        if(jQuery('#show_company_name').is(':checked') == true)
        {
            show_company_name = 1;
        }
        else
        {
            show_company_name = 0;
        }
        var job_city_id       = $('#job_city').val();
        var year_work_start   = $('#years_worked_start').val();
        var year_work_end     = $('#years_worked_end').val();
        var company_email     = $('#company_email_address').val();
        var company_domain    = $('#company_email_domain_id').val();
        var user_company_id   = $('#user_company_id').val();

         if(company_domain!='undefined')
            var domain        = company_domain;
        else 
            var domain        = "";

         if(year_work_end == '0')
         {
        	 var job_end = new Date().getFullYear()
         }
         else
         {
        	 var job_end = year_work_end;
         }
         
         if(year_work_start == '' && year_work_end != '')
         {
             $('#year_work_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Please specify both start and end years")?></div>');
             return false;
         }
         else if(year_work_start != '' && year_work_end == '')
         {
             $('#year_work_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Please specify both start and end years")?></div>');
             return false;
         }
         else if(year_work_start > year_work_end)
         {
             $('#year_work_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("End year must be equal to or after start year")?></div>');
             return false;
         }
         else
         {
             $('#year_work_err').html('');
         }
         
        if(job_title !="" && job_city_id !="" && company_name !=""){
            loading();
             $.ajax({ 
                     url:'<?php echo base_url("user/add_company_applyPage")?>', 
                     type:"post",
                     data:{"company_name":company_name,'industry_id':industry_id,'job_function_id':job_functions,'job_title':job_title,'show_company_name':show_company_name,'job_city_id':job_city_id,'year_work_start':year_work_start,'year_work_end':year_work_end,'company_email':company_email,'company_domain':domain,'user_company_id':user_company_id},
                     cache: false,
                     success: function (data) {
                         stop_loading();
//                         if(data==0){
//                             $("#school_company").html('<?php echo translate_phrase("Company already added")?>'); 
//                         } 
                         if(data==1)
                             $("#company_email_error").html('<div class="error_indentation error_msg"><?php echo translate_phrase("Please enter a valid company email address")?></div>');
                         else{

                        	 domainSelectedCompany = '';
                        if(user_company_id!="")
                             $("#user_company"+user_company_id).remove();
                             //$("#list_company").append(data);
                             $("#list_company").html(data);
                             $("#school_company").html('');    
                             $('#add_companies').fadeOut();
                             $('#company_button').html('<?php echo translate_phrase("Add Job")?>');
                         }
                             
                         if(data!=1){
                        	 $("#year_work_err").html('');
                             $("#job_title_err").html('');
                             $("#job_city_id_err").html('');
                             $("#company_name_err").html('');
                             $("#time_period_err").html('');
                             $("#company_name").val('');
                             $("#company_email_address").val('');
                             $("#job_title").val('');
                             $("#job_city").val('');
                             
                             $('#show_company_name').prop('checked',false);
                             $("#add_companies select").prop("selectedIndex","");

                            $('#add_companies input[type=text]').val('');
           		    		$('#add_companies input[type=hidden]').val('');
                            $("#add_companies dl dt a span").html('select');

                             $("#company_email_error").html('');
                             $('#company_logo').html('');
                             $('#add_companies .file-upload img').attr('src', '');
                             $('#add_companies .file-upload .Delete-Photo01').hide();

                             $('#list_company').fadeIn();
                             $('#list_company_main').fadeIn();
                             $('#add_company_button').fadeIn();
                             scrollToDiv('list_company_main');
                             $('#user_company_id').val('');
                         }

                         if( typeof from_validate != undefined && from_validate)
                         {
                             console.log('submitting..');
                             $("#submit_form").attr('lang','redirect').click();
                             //HTMLFormElement.prototype.submit.call($('#ProfileForm')[0]);
                         }
                         
                         return false;
                         
                     }     
             });
        }
        else{
             if(company_name=="")
                 $('#company_name_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Company name is required")?></div>');
             if(job_title=="")
                 $('#job_title_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Job title is required")?></div>');
             if(job_city_id=="")
                 $('#job_city_id_err').html('<div class="error_indentation error_msg"><?php echo translate_phrase("Job location is required")?></div>');
             return false;
         }
     }
     
     function edit_company(company_id){
    	 $('.mobile-error').remove();
        $('#user_company_id').val(company_id);
        scrollToDiv('add_companies');
        $('#add_companies').fadeIn();
       	$('#list_company').fadeOut();
        //$('#company_cancel_button').show();
        $('#add_company_button').hide();
        $("#company_domain").html('');
        $('#com_atsymbol').hide();
        $('#company_button').html('<?php echo translate_phrase("Update");?>');
        $.ajax({ 
             url:'<?php echo base_url("user/edit_company")?>', 
             type:"post",
             dataType: "json", 
             data:"user_company_id="+company_id,
             cache: false,
             success: function (data) {
            	 $('#company_name').val(data.company_name).focus();
                if(data.company_name == prevInsertCompany)
                {	
                    var selectInd = $("#company_industry").find('dd li a[key='+data.industry_id+']').text();
                    if(selectInd == '')
                    {
                    	selectInd = 'Select company industry';
					}
                	$("#company_industry").find('dt a span').text(selectInd);
                }
                else
                {
                	show_company_industry(data.company_name,data.industry_id);
                    prevInsertCompany = data.company_name;
                }
                 domainSelectedCompany = data.company_name;
                 
                 if(data.show_company_name == 1){
                     $('#show_company_name').prop('checked',true);
                 }
                 else
                 {
                	 $('#show_company_name').prop('checked',false);
                 }
                 $('#job_function_id').val(data.job_function_id);
                 $('#job_title').val(data.job_title);
                 $('#job_city').val(data.job_city);

                 	if(data.years_worked_start == 0)
                    {
                    	$("#years_worked_start_dl").find('dt a span').text('---');
                        jQuery('#years_worked_start').val('');

                    }
                       else
                       {
                           $("#years_worked_start_dl").find('dt a span').text(data.years_worked_start);
                           jQuery('#years_worked_start').val(data.years_worked_start);
                       }
                 	if(data.years_worked_end == 0)
                    {
                       $("#years_worked_end_dl").find('dt a span').text('---');      
                       $("#years_worked_end").val('');      
                    }
                	else if(data.years_worked_end == 9999)
                   {
                      $("#years_worked_end_dl").find('dt a span').text('Present');      
                      $("#years_worked_end").val('9999');      
                   }
                   else
                   {
                       $("#years_worked_end_dl").find('dt a span').text(data.years_worked_end);      
                       $("#years_worked_end").val(data.years_worked_end);      
                   }
                  //$('#years_worked_start').val(data.years_worked_start);
                 //$('#years_worked_end').val(data.years_worked_end);
                 
                 $('#company_email_address').val(data.adress);

                 /*
                 	var selectedIndustry = jQuery('#company_industry dl dd ul li a[key='+data.industry_id+']').text()
                 	jQuery('#company_industry dl dt a span').html(selectedIndustry);
                 */

                 var selectedJobFunction = jQuery('#job_function_dd dl dd ul li a[key='+data.job_function_id+']').text()
                 if(selectedJobFunction == '')
                 {
                 	jQuery('#job_function_dd dl dt a span').html('<?php echo translate_phrase("Select job function")?>');
                  }
                 else
                 {
                 	 jQuery('#job_function_dd dl dt a span').html(selectedJobFunction);
                 }
                 
                 
                 if(data.domain)
                 {
                	 Dropdownli = '';
						var defultSelected = ''
                     $.each(data.domain,function(id,option) {
							Dropdownli +='<li><a key="'+id+'">'+option+'</a></li>';
							defultSelected='<dt><a key="'+id+'" href="javascript:;"><span>'+option+'</span></a><input type="hidden" value="'+id+'" name="company_email_domain_id" id="company_email_domain_id"></dt>';
						});
							    
						var DropDownHTML ='<dl class="dropdown-dt" id="email_domain" name="drop_company_email_domain_id">'+defultSelected+'<dd><ul>';
						DropDownHTML +=Dropdownli + '</ul></dd></dl>';
                     	$('#company_domain').html(DropDownHTML);
						    
                     $('#com_atsymbol').show();
                     $('#company_email_address').removeClass("medium");
                 }else{
                     $('#com_atsymbol').hide();
                     $('#company_email_address').addClass("medium");
                 }
                 if(data.photo_business_card){
                     var path  = '<?php echo base_url();?>'+'user_photos/user_'+'<?php echo $this->session->userdata('user_id');?>'+'/'+data.photo_business_card;
                     $('#add_companies .file-upload img').attr('src',path);
                     $('#add_companies .file-upload .Delete-Photo01').show();
                 }
                 $('#user_company_id').val(data.user_company_id);
                 if(data.company_logo)
                 {
                	$("#company_logo").html('');
                    var url = '<?php echo base_url();?>company_logos/';
                    if(data.company_logo !="")
                         $("#company_logo").html('<img height="50" width="50" src="'+url+data.company_logo+'" >');
                }
                 
             }     
        });
     }
     
     function cancel_company(){
    	 $('.mobile-error').remove();
         $('#company_button').html('<?php echo translate_phrase("Add Job")?>');
         $('#user_company_id').val('');

         $('#add_companies').fadeOut();

         $('#add_company_button').show();         
         $("#year_work_err").html('');
 		 $("#job_title_err").html('');
         $("#job_city_id_err").html('');
         $("#company_name_err").html('');
         $("#time_period_err").html('');
         $("#company_name").val('');
         $("#company_email_address").val('');
         $("#job_title").val('');
         $("#job_city").val('');
         $('#add_companies .file-upload img').attr('src', '');
         $('#add_companies .file-upload .Delete-Photo01').hide();
         $('#com_atsymbol').hide();
         $('#company_email_address').addClass('medium');
         $('#show_company_name').prop('checked',false);
         $("#add_companies select").prop("selectedIndex","");
         //$("#add_companies ul").empty();
         $("#add_companies dl dt a span").html('<?php echo translate_phrase("Select company industry");?>');
         $('#company_logo').html('');

         
         if($("#list_company").find('.Univ-logoSec').length > 0)
         {
         	$("#list_company").fadeIn();
         }
         else
         {
         	$("#list_company").fadeOut();
         }
         $('#list_company_main').fadeIn();
         
         scrollToDiv('list_company');
     }
     
    function edit_school(school_id){
    	//goToScroll('add_schools');
    	scrollToDiv('add_schools')
    	$('#add_schools').fadeIn();
       	$('#list_school_main').fadeOut();
       	$('.mobile-error').remove();
    	$('#cancel_button').show();
       	
       	$('#add_school_button').hide();
       	$("#atsymbol").hide;

       $('#user_school_id').val(school_id);
       $("#school_error").html('');
       $("#school_photo_id").html('');
       
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
               			if(ids=='photo_diploma' && values)
                    	{
                        	var path  = base_url +'user_photos/user_'+user_id+'/'+values;
                            $('#add_schools .file-upload img').attr('src',path);
                            $('#add_schools .file-upload .Delete-Photo01').show();
                        }
               			else
                    	{
                    		$('#add_schools .file-upload img').attr('src','');
                            $('#add_schools .file-upload .Delete-Photo01').hide();
                       	}

                        if(ids == 'school_name')
                        {
                        	currnetSchoolName = values;
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
                                //$("#attended_end").find('dt a span').text(values);
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
                       	{
                           	$('#'+ids).val(values);
                       	}
                    	if(ids=='is_degree_completed'&& values=="1")
                       	{
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
           		$('#school_name').focus()
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

    	$('#list_school_main').fadeOut();
    	$('#add_schools').fadeIn();
    	$('#school_name').focus();
    }
    
	function show_div_company(){
    	show_company_industry();
    	$('.mobile-error').remove();
    	$("#job_function_id").val('');
        $("#job_title_err").html('');
        $("#job_city_id_err").html('');
        $("#company_name_err").html('');
        $("#time_period_err").html('');

        $('#add_companies input[type=text]').val('');
        $('#add_companies input[type=hidden]').val('');
       
        $('#add_companies .file-upload img').attr('src', '');
        $('#add_companies .file-upload .Delete-Photo01').hide();
        $('#com_atsymbol').hide();
        $('#company_email_address').addClass('medium');
        $('#show_company_name').prop('checked',true);
        $("#add_companies select").prop("selectedIndex","");
        jQuery('.drop-start-year dt a span').text('--');
        jQuery('.drop-end-year dt a span').text('--');
        $('#company_domain').html('');
        
        
        //$("#add_companies ul").empty();
        $('#company_logo').html('');
        $('#add_companies').show();
        $('#list_company').hide();
        $('#add_company_button').hide();
        $('#company_cancel_button').show();
        
        jQuery('#company_industry dl dt a span').html('<?php echo translate_phrase("Select company industry");?>');
        jQuery('#job_function_dd dl dt a span').html('<?php echo translate_phrase("Select job function");?>');
        $("#company_name").focus();
        //alert(jQuery('#company_industry_dd_id dt a span').html());
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

    
    function show_logo(){
        var school_name = $('#school_name').val();
        $.ajax({ 
            url: '<?php echo base_url(); ?>' +"user/show_school_logo/", 
            type:"post",
            data:'school_name='+school_name,
            cache: false,
            success: function (data) {
                if (data.length > 0) {
                    var url = '<?php echo base_url();?>school_logos/'+data;
                    $('#school_logo').html('<img src="'+url+'" height="50" width="50">');
                }else
                     $('#school_logo').html('');
            }     
        });
    }
    
    function show_school_domain(){
        var school_name = $('#school_name').val();
		if(currnetSchoolName != school_name)
		{
	        $.ajax({ 
	            url: '<?php echo base_url(); ?>' +"user/show_school_domain/", 
	            type:"post",
	            data:'school_name='+school_name,
	            cache: false,
	            success: function (data) {
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
            $("#add_minor").append('<li class="Fince-But" id="major'+minor_id+'" ><a href="javascript:;">'+minors+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_major('+"'"+minor_id+"'"+');" title="Remove"></a></li>');
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
   
    function show_company_logo(company_name){
    	 if(typeof(company_name) == 'undefined' || company_name == '')
         {
         	var company_name      = $('#company_name').val();
 		}
  		
        $.ajax({ 
            url: '<?php echo base_url(); ?>' +"user/show_company_logo/", 
            type:"post",
            data:'company_name='+company_name,
            cache: false,
            success: function (data) {
                if (data.length > 0) {
                  var url = '<?php echo base_url();?>company_logos/'+data; 
                  $('#company_logo').html('<img src="'+url+'" height="37" width="50">');
                }else
                     $('#company_logo').html(''); 
            }     
        });
    }
    
    function show_company_industry(company_name,industry_id){

        if(typeof(company_name) == 'undefined' || company_name == '')
        {
        	var company_name      = $('#company_name').val();
		}

        if(typeof(industry_id) == 'undefined' || industry_id == '')
        {
            var industry_id = 0;
        }
        
        if(company_name != prevInsertCompany)
        {
	        $.ajax({ 
	            url: '<?php echo base_url(); ?>' +"user/show_company_industry/", 
	            type:"post",
	            data:'company_name='+company_name,
	            cache: false,
	            success: function (data) {
	                if(data!="0")
	                {
	                    $('#company_industry').html(data);
	                    var sel_industry = $(data).find('dd li a[key='+industry_id+']').text()
						if(industry_id != 0 && sel_industry != '')
						{
							$('#industry_id').val(industry_id);
							$("#company_industry").find('dt a span').text($(data).find('dd li a[key='+industry_id+']').text());
						}
						prevInsertCompany = company_name;
	                }
	            }     
	        });
        }
    }

    var domainSelectedCompany = '';
    function show_company_domain(company_name){
    	if(typeof(company_name) == 'undefined' || company_name == '')
        {
        	var company_name      = $('#company_name').val();
		}
        
        if(company_name != domainSelectedCompany)
        {
        	$.ajax({ 
                url: '<?php echo base_url(); ?>' +"user/show_company_domain/", 
                type:"post",
                data:'company_name='+company_name,
                cache: false,
                success: function (data) {
                	domainSelectedCompany = company_name;
                    $('#company_domain').html(data);
                    if (data.length > 0) {
                        $('#com_atsymbol').show();
                        $('#company_email_address').removeClass("medium");
                    } else{
                        $('#com_atsymbol').hide();
                        $('#company_email_address').addClass("medium");
                    }   
                }     
            });
        }
        
    }
    function remove_company(user_company_id){
         $("#user_company"+user_company_id).remove();
         $.ajax({ 
            url:'<?php echo base_url("user/remove_company")?>', 
            type:"post",
            data:"user_company_id="+user_company_id,
            cache: false,
            success: function (data) {
                if(data=="0"){
                	prevInsertCompany = '';
                     $('#list_company_main').fadeOut();
                     $('#add_companies').fadeIn();
                     scrollToDiv('add_companies');
                     $("#company_name").focus();
                }
            }     
       }); 
    }
    function get_height(value){
        if(value=='height'){ 
            var height  = $('#height').val();
            $('#user_height').val(height.trim());
        }
        if(value=='feet' || value=='inches'){
            var feet             = $('#feet').val();
            var inches           = $('#inches').val();
            if(feet!="" && inches!=""){
                var inch_to_feet = inches* 0.083333;
                feet             =  parseInt(feet)+inch_to_feet;
                var cm           = feet * 30.48;
                $('#user_height').val(Math.round(cm));
               
            }else{
                 $('#user_height').val('');
            }
        }
    }
   
    function user_privacy_photos (obj)
    {
    	var option = '';
        if(($(obj).is(':checked')))
        {
			option = 'SHOW';
       	}
        else
        {
        	option = 'HIDE';
        }
        
    	 $.ajax({ 
             url: '<?php echo base_url()."user/user_photo_privacy/"; ?>' +option, 
             type:"post",
             success: function (data) {
                  //console.log(data);
             }
         });
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
   function go_next_to_school(){
        if( $('#school_name').val()!="" || $('#degree_name').val()!="" || $('#majors_id').val()!="" || $('#minors_id').val()!="" || $('#years_attended_start').val()!="" ||$('#years_attended_end').val()!="" || $('#school_email_address').val()!="")
            add_school();
        else {
            next_step1();
        }
   }
   function go_next_to_company(){
       if( $('#company_name').val() !="" || $('#job_title').val()!="" || $('#job_city').val()!="" )
            add_company();
       else
            next_step1();
   }


function radio_select(inputId,inputVal,Obj)
{
	$(Obj).parent().addClass('Intro-Button-sel').removeClass('Intro-Button');
	$(Obj).parent().siblings().removeClass('Intro-Button-sel').addClass('Intro-Button');
	$("#"+inputId).val(inputVal);
	
}

function add_nationality(){
    var minor_id      = $('#nationality').find('dt a').attr('key');
    var minor_ids     = $('#nationality_id').val();
    var minors        = $('#nationality').find('dt a span').html();
    
    if (minor_id) {
      if(minor_ids!="")
        minors_ids     = minor_ids+','+minor_id; 
      else
          minors_ids     = minor_id;
      if(minor_ids.indexOf(minor_id)== -1){ 
        $("#nationality_id").val(minors_ids);
        $("#add_nationality").append('<li class="Fince-But" id="nationality'+minor_id+'" ><a href="javascript:;">'+minors+'<img src="'+base_url+'assets/images/cross.png" onclick="remove_nationality('+"'"+minor_id+"'"+');" title="Remove"></a></li>');
       }  
    }
}
function remove_nationality(id){
    var ids           = new Array();
    var minor_ids     = document.getElementById('nationality_id').value;
    var ids           = minor_ids.split(','); 
    var index         = ids.indexOf(id);
    ids.splice(index, 1);
    var minor_id      = ids.join(); 
    $('#nationality'+id).remove();
    $("#nationality_id").val(minor_id);
}

function add_languages(){
    var lang                  = $('#spoken_language').find('dt a').attr('key');
    var prof                  = $('#proficiency').find('dt a').attr('key');
    var spoken_language       = $('#spoken_language').find('dt a span').html();
    var proficiency           = $('#proficiency').find('dt a span').html()
    
    var language_id           = document.getElementById('spoken_language_id').value;
    var language_level_id     = document.getElementById('spoken_language_level_id').value;

     $('#spk_lang_err').removeClass('error error_msg');
    $('#prof_err').removeClass('error error_msg');
    
    if(lang!="" && prof!=""){
        
        $('#spk_lang_err').html('');
        $('#prof_err').html('');
        
        
        if(language_id!="") 
            var spoken_language_id    = language_id+','+lang; 
        else  
            var  spoken_language_id   = lang;
        
        if(language_level_id!="")   
            var spoken_language_level_id    = language_level_id+','+prof; 
        else    
            var  spoken_language_level_id   = prof;
        
        if (language_id.indexOf(lang) == -1) {
          $("#spoken_language_id").val(spoken_language_id);
          $("#spoken_language_level_id").val(spoken_language_level_id);
          $("#add_lang").append('<li class="Fince-But" id="lang'+lang+'" ><a href="javascript:;">'+spoken_language+' ('+proficiency+')<img src="'+base_url+'assets/images/cross.png" onclick="remove_language('+"'"+lang+"','"+prof+"'"+');" title="Remove"></a></li>');
          
        };
   }else{ 
        if(lang==""){
            $('#spk_lang_err').addClass('error error_msg');
            $('#spk_lang_err').html('<?php echo translate_phrase("Language is required")?>');
        }
        if(prof==""){
            $('#prof_err').addClass('error error_msg');
            $('#prof_err').html('<?php echo translate_phrase("Proficiency is required")?>');
        }    
   }
}

function remove_language(language_id,language_level_id){
    var sp_language_id             = new Array();
    var sp_lang_level_id           = new Array();

    
    var spoken_language_id         = document.getElementById('spoken_language_id').value;
    var spoken_language_level_id   = document.getElementById('spoken_language_level_id').value;
    
    //remove from language hidden field
    var sp_language_id             = spoken_language_id.split(','); 
    var lang_index                 = sp_language_id.indexOf(language_id);
    sp_language_id.splice(lang_index, 1);
    var spoken_language_id         = sp_language_id.join(); 
    
    //remove from proficiency hidden field
    var sp_lang_level_id           = spoken_language_level_id.split(','); 
    var lang_level_index           = sp_lang_level_id.indexOf(language_level_id);
    sp_lang_level_id.splice(lang_level_index, 1);
    var spoken_language_level_id   = sp_lang_level_id.join(); 
    
    $('#lang'+language_id).remove();
    $("#spoken_language_id").val(spoken_language_id);
    $("#spoken_language_level_id").val(spoken_language_level_id);
    
}



function make_primary_photo(id,btn)
{
	$.post($(btn).attr('data-url'), {'id': id}, function(data) {
		$("#photo-error").text("");
		if(data == '1')
		{
			$.each($(".photo-slider-m .primary-btn-text").find("label"),function(i,item){
				$(item).parent().removeClass('primary-btn-text').addClass('Primary-Button01');
				$(item).attr('onclick',"make_primary_photo('"+$(item).attr('lang')+"',this)").text('Set as Primary');
			})
			
			$(".photo_"+id).find(".Primary-Button01").removeClass('Primary-Button01').addClass('primary-btn-text');
			$(".photo_"+id).find(".primary-action").attr("onclick","").text('Primary Photo');
			if($('.bxslider_profile').length > 1)
            {        
             	$('.photo_'+id).fadeOut(function(){
             		var firstLi = jQuery('.bxslider_profile').children().first();
	                jQuery(this).insertBefore(jQuery(firstLi));
	                slider.reloadSlider();	                	
            	});
            }         
		}
	});
}

function delete_photos(id,btnDelete)
{
	$.post($(btnDelete).attr('data-url'), {'id': id}, function(data) {
		if(data == "1")
		{
			$(".photo_"+id).remove();
			if(jQuery(".bxslider_profile li").length == 0)
			{
				jQuery('#slider-next,#slider-prev').fadeOut();
				jQuery(".bx-viewport").parent().fadeOut();
			}
			$("#photo-error").text("");
			$(this).remove();
			slider.goToPrevSlide();
			resizeSlider();
			slider.reloadSlider();
			
		}
		else
		{
			$("#photo-error").text('<?php echo translate_phrase("At least one profile photo is required.")?>');
		}
   });
}

/*This function is called when user clicks on any of the buttons in "Ways to improve your profile" section at top*/
function goToThisField(button)
{
    var divId       = jQuery(button).attr('divId');
    var elementDiv  = jQuery(button).attr('elementId');
    
    //go to that particular tab
    jQuery('.etabs').find('li #'+divId+'Tab').click();
    //got to that particular element div
    setTimeout(function(){
    	jQuery('body').scrollTo(jQuery('#'+elementDiv),800,function(){
    		jQuery('#'+elementDiv).find('input, textarea').focus();
        })    	
    },500);    
}

function customScroll(elementDiv)
{
    jQuery('body').scrollTo(jQuery('#'+elementDiv),800);
}
function setCityName()
{
	var tbValue = jQuery('#city_born').val();
    jQuery('#born_in_city').val(tbValue);
}
</script>

