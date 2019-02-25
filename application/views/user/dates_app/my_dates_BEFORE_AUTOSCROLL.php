<script src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<style>
	.user-photo-slider{text-align:center;}
	.img-slide img{max-width:100%;}
</style>
<script type="text/javascript">
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
    
   
    
	$('#my_dates').easytabs();
	$('#my_dates').bind('easytabs:after', function(tab, panel, data){
  		var target_tab_id = panel[0].id;
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
        
        console.log('hi');
        $(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());
        $(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'))
        $(this).parent().parent().parent().parent().find("dt a").attr('key', $(this).attr('key'));
    });
});
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
							<a id="pastTab" href="#cancel"><?php echo translate_phrase('Cancelled');?></a>
						</li>
					</ul>
					<div id="upcoming" class="step-form-Main Mar-top-none Top-radius-none">
						<div class="step-form-Part">
							<div id="upcoming_container">
								<?php $this->load->view('user/dates_app/include/date_upcoming');?>
							</div>
						</div>
					</div>
					<div id="past" class="step-form-Main Mar-top-none Top-radius-none">
						<div class="step-form-Part">
							<div id="expired_container">
								<?php $this->load->view('user/dates_app/include/date_expired');?>
							</div>
						</div>
					</div>
                                        <div id="cancel" class="step-form-Main Mar-top-none Top-radius-none">
						<div class="step-form-Part">
							<div id="expired_container">
								<?php $this->load->view('user/dates_app/include/date_cancelled');?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>