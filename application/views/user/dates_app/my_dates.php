<script src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<style>
	.user-photo-slider{text-align:center;}
	.img-slide img{max-width:100%;}
	.emp-B-tabing-M ul.etabs li{width:auto;}
</style>
<script type="text/javascript">

var currentTab = "";
$(document).ready(function() {
    
    
     $(".rdo_div").live('click tap',function(){
		$(this).siblings().find('span').removeClass('appr-cen').addClass('disable-butn');
		$(this).find('span').removeClass('disable-butn').addClass('appr-cen');
		$(this).parent().find(':input[type="hidden"]').val($(this).attr('key'));
			var selected=($(this).parent().find(':input[type="hidden"]').attr('id'));
	});
        
    $(".grayHoverBox").live('click tap', function (e) {
    	$(this).addClass('dark-grey-bg');
    	
       	var id =$(this).attr('lang');
       	var clicked=e.target.nodeName;
       	if(clicked =='a' || clicked =='span' || clicked =='A' || clicked =='SPAN'){
           //return false;
       	}else{
        	window.location.href='<?php echo base_url();?>dates/view_date/'+id;
       	}
    });
    
   
    var tabName = window.location.hash;
    if(tabName == '' || tabName == '#_=_')
	{
		load_data_by_easy_tabs('#upcoming');
	}
	else
	{
        load_data_by_easy_tabs(tabName);
	}
	$('#my_dates').easytabs();
	$('#my_dates').bind('easytabs:after', function(tab, panel, data){
  		var target_tab_id = panel[0].id;
  		var tabName = panel[0].hash;
  		load_data_by_easy_tabs(tabName)
  		//currentTab = tab;
  		loadPhotoSlider();
    });
    
    
	
	$(".popupLink").fancybox({
            maxWidth    : 300,
            maxHeight    : 600,
            width        : '70%',
            height       : '70%',
            afterClose: function() {
            },
  
    });
    
    $(".viewLink").fancybox({
            
            maxHeight    : 900,
            width        : '50%',
            height       : '90%',
            afterClose: function() {
            },
  
    });
    $(".reviewLink").fancybox({
            maxWidth    : 900,
            maxHeight    : 900,
            width        : '100%',
            height       : '90%',
            afterClose: function() {
            },
  
    });
    $(".chooseApplicant").fancybox({
            maxWidth    : 900,
            maxHeight    : 900,
            width        : '90%',
            height       : '90%',
            afterClose: function() {
            },
  
    });
     $(".refundLink").fancybox({
            maxWidth    : 900,
            maxHeight    : 900,
            width        : '100%',
            height       : '90%',
            afterClose: function() {
            },
  
    });
    
    
    $(".fancybox-thumb").fancybox({
			prevEffect : 'none',
			nextEffect : 'none',
			closeBtn : false,
			helpers : {
				title : {
					type : 'inside'
				},
				thumbs : {
					width : 50,
					height : 50
				}
			}
		});
    loadPhotoSlider();
    
    $('.star').raty({'path':'<?php echo base_url()?>assets/images/',readOnly    : false,score: function() {
    return $(this).attr('data-score');
  } });
  
  
    $(".dropdown-dt").find('dt a').live('click tap', function () {
        $(this).parent().parent().find('ul').toggle();
    
    });
    
    $(".dropdown-dt dd ul li a").live('click tap', function () {
        
        $(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
        $(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'))
        $(this).parent().parent().parent().parent().find("dt a").attr('key', $(this).attr('key'));
    });
    
    
	
});
function load_data_by_easy_tabs(tabName)
{
	var selectedTab = "";
	if(tabName == '#cancel'){
		currentTab = "cancel";
    }
	else if(tabName == '#past')
	{
		currentTab = "past";
	}
	else
	{
		currentTab = 'upcoming';
	}
	
	initialize_data(currentTab);	
}
var upcoming_page_no = "<?php echo $page_no ?>";
var past_page_no = "<?php echo $page_no ?>";
var cancel_page_no  = "<?php echo $page_no ?>";
var preventAjaxCall = [];

var upcomingData = true;
var pastData = true;
var cancelData = true;

//Lazzy Load Pagination..
$(window).scroll(function() {  
	var totalScrollAmount = $(window).scrollTop() + $(window).height() + 80;
	if( totalScrollAmount >= $(document).height()) 
	{
		if(preventAjaxCall.indexOf(currentTab) == -1 && isAjaxCallRunning == false)
		{
			initialize_data(currentTab,"append");
		}
		else
		{
			console.log(preventAjaxCall);
		}
   	}
});

function initialize_data(currentTab,action)
{
	console.log(currentTab+' : '+action)
	var offset = 1;	
	if(action == 'append')
	{
		if(currentTab == 'cancel')
	    {
	    	offset += parseInt(cancel_page_no); 
	    }    
	    if(currentTab == 'upcoming')
	    {
	    	offset += parseInt(upcoming_page_no);
	    }
	    if(currentTab == 'past')
	    {
	    	offset += parseInt(past_page_no);
	    }
	}
	
	if(isAjaxCallRunning == false)
	{
		loading();
		if(action == 'append')
        {
    		$("#"+currentTab+"_container").append('<div class="div_data_loader"></div>');
    	}
    	else
    	{
    		$("#"+currentTab+"_container").html('<div class="div_data_loader"></div>');
    	}
    	$.ajax({ 
            url: '<?php echo base_url(); ?>' +"dates/my_dates/"+currentTab, 
            type:"post",
            data:{'page_no':offset},
            success: function (response) {
            	stop_loading();
            	
	            if(preventAjaxCall.indexOf(currentTab) !=  '-1')
	            {
		            preventAjaxCall.pop(currentTab);
	            }
                if(currentTab == 'upcoming')
                {
                	upcoming_page_no = offset;
                    msg = '<p><span class="no-rows"><?php echo translate_phrase('You currently have no upcoming dates.')?></span></p>';
                }
                if(currentTab == 'past')
                {
                	past_page_no = offset;
                	msg = '<p><span class="no-rows"><?php echo translate_phrase('You currently have no past dates.')?></span></p>';
                }            	            	
            	if(currentTab == 'cancel')
                {
                	cancel_page_no = offset;
                	msg = '<p><span class="no-rows"><?php echo translate_phrase('You currently have no cancel date.')?></span></p>';
                }
                
				if($("#"+currentTab+"_container").css('style') == 'none')
			    {
			    	$("#"+currentTab+"_container").show();
			    }
		    	$("#"+currentTab+"_container").find('.div_data_loader').fadeOut(function(){$(this).remove()});            			
		    	if($.trim(response) != '')
		    	{
		    		if(action == 'append')
            		{
            			$("#"+currentTab+"_container").append($(response).hide().fadeIn(2000));
            		}
            		else
            		{
            			$("#"+currentTab+"_container").html(response);	
            		}
		    		
		        }
		    	else
		    	{
		    		preventAjaxCall.push(currentTab);
		    		if(action != 'append')
            		{
            			$("#"+currentTab+"_container").html(msg);	
            		}
            		
		       	}   
           }
		});
	}
}

var photoSlidersObjs = [];
function loadPhotoSlider()
{
	$.each($('.user-photo-slider'),function(i,item){
		var userBxSliderId = $(item).attr('id');
		if($(item).attr('lang') == '1')
		{
			photoSlidersObjs[userBxSliderId].reloadSlider();
		}
		else
		{
			var userBxSlider = $(item).find('.bxslider_profile');
			if(userBxSlider.length > 0)
			{
				var bxPager = $(item).find('.bx-pager').attr('id');
		    	var nextLink = $(item).find('.slider-next').attr('id');
		    	var prevLink = $(item).find('.slider-prev').attr('id');
		    	
				var slider = userBxSlider.bxSlider({
					   pager: false,
					  infiniteLoop:false,
					  nextSelector: "#"+nextLink,
			          prevSelector: "#"+prevLink,
					  nextText: '<img src="<?php echo base_url()?>assets/images/l-arw-img.png" alt="" />',
					  prevText: '<img src="<?php echo base_url()?>assets/images/r-arw-img.png" alt="" />'
				});
				
				//Slider is created..
				$(item).attr('lang','1');        	
				photoSlidersObjs[userBxSliderId] = slider;	
			}
		}		
    });
}

    function closeFancyBox(){
            $.fancybox.close();
    }

    function choseApplicant(id){
        loading();
        $.ajax({
            url: '<?php echo base_url(); ?>' + "dates/updateDateApplicant",
            type: "post",
            data: {id: id},
            cache: false,
            success: function (data) {
                stop_loading();
                $.fancybox.close();
                window.location.href='<?php echo base_url('dates/match_date');?>/'+id;
            }
        });

    }
    
    function checkReviewValidation(id){
        var flag = 1;
        if ($('#dateReview_'+id+' > form > div > span > input').val() == '') {
            //showError('ratingError', '<?php echo translate_phrase("Please provide a rating for this date.") ?>');
            jQuery('#ratingError_'+id).text('<?php echo translate_phrase("Please provide a rating for this date.") ?>');
            flag = 0;
        }
        else
        {
            jQuery('#ratingError_'+id).text('');
        }


        if (flag == 0){
            return false;
        }else{
            $('#ratingForm').submit();
        }
    }
    
    
    function checkEditReviewValidation(id){
        var flag = 1;
        if ($('#dateEditReview_'+id+' > form > div > span > input').val() == '') {
            //showError('ratingError', '<?php echo translate_phrase("Please provide a rating for this date.") ?>');
            jQuery('#ratingEditError_'+id).text('<?php echo translate_phrase("Please provide a rating for this date.") ?>');
            flag = 0;
        }
        else
        {
            jQuery('#ratingEditError_'+id).text('');
        }
        if (flag == 0){
            return false;
        }else{
            $('#editRatingForm').submit();
        }
    }
</script>
<div class="wrapper">
	<div class="content-part mobile-layout">
		<div class="Apply-Step1-a-main">
			<div id="page-msg-box" class="page-msg-box left" style="padding-bottom: 10px;">
				<span class="DarkGreen-color"><?php echo $this->session->flashdata('page_msg_success');?></span>
				<span class="Red-color"><?php echo $this->session->flashdata('page_msg_error');?></span>					
			</div>
			<div class="emp-B-tabing-prt">				
				<div class="emp-B-tabing-M" id="my_dates">
					<ul class='etabs'>
						<li class='tab tab-nav'>
							<span></span>
							<a id="upcomingTab" href="#upcoming"><?php echo translate_phrase('Upcoming');?></a>
						</li>
						<li class='tab tab-nav'>
							<span></span>
							<a id="pastTab" href="#past"><?php echo translate_phrase('Past');?></a>
						</li>
							<li class='tab tab-nav'>
							<span></span>
							<a id="cancelTab" href="#cancel"><?php echo translate_phrase('Cancelled');?></a>
						</li>
					</ul>
					<div id="upcoming" class="step-form-Main Mar-top-none Top-radius-none">
						<div class="step-form-Part">
							<div id="upcoming_container" class="datesArea bor-none">
								<?php $this->load->view('user/dates_app/include/date_upcoming');?>
							</div>
						</div>
					</div>
					<div id="past" class="step-form-Main Mar-top-none Top-radius-none">
						<div class="step-form-Part">
							<div id="past_container" class="datesArea bor-none">
								<?php $this->load->view('user/dates_app/include/date_past');?>
							</div>
						</div>
					</div>
                                        <div id="cancel" class="step-form-Main Mar-top-none Top-radius-none">
						<div class="step-form-Part">
							<div id="cancel_container" class="datesArea bor-none">
								<?php $this->load->view('user/dates_app/include/date_cancel');?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>