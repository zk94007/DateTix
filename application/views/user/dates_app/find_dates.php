<link rel='stylesheet'  href="<?php echo base_url();?>assets/FlexSlider/flexslider.css" />
<script src="<?php echo base_url();?>assets/FlexSlider/jquery.flexslider.js"></script>


<!-- Add Media helper (this is optional) -->
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css">


<?php $user_id = $this->session->userdata('user_id');?>
<script type="text/javascript">
var offset = 1;
$(document).ready(function () { 
	
	loadSlider();
	 
	 /*----------------Importance Tag-------------------------------*/
    $('.importance ul li a').live('click',function(e) {
	    e.preventDefault();
	    var ele = jQuery(this);
                
		//check if prefrences for this particular field is selected or not. If not then dont allow to select importance.
	    var prefrenceHiddenField = ele.parent().parent().parent().prev().find("input[type='hidden']").val();
	    if(prefrenceHiddenField =="")
	    {    
	    	//return false; 
	    }
                
        var checkid = ele.parent().parent().parent().prev().attr('id');
	    var parentUl = ele.parent().parent();
		var li = jQuery(parentUl).find('li.Intro-Button-sel');
	    $(li).removeClass('Intro-Button-sel').find('a').addClass('Intro-Button');
	    $(ele).parent().addClass('Intro-Button-sel').find('a').removeClass('Intro-Button');
	    
	    //set the hidden field value for this prefrence
	    var selectedImportance = jQuery(parentUl).find('li.Intro-Button-sel a').attr('importanceVal');
	    parentUl.parent().find('input[type="hidden"]').val(selectedImportance);
	});
        
        
        $(".commonInterest").fancybox({
            maxWidth    : 300,
            maxHeight    : 600,
            width        : '70%',
            height       : '70%',
            afterClose: function() {
            },
  
    });
    
    
   
	
});
function loadSlider()
{
	$('.flexslider').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: true,
		 smoothHeight: true,
		slideshow: true		
	});
}
function getNextDate(obj){

	
	var width = $("#userDateListing").width()+10;	
	$("#userDateListing .dates-details").animate({'margin-right': width}).css('float','right');
	
	loading();
	$.ajax({ 
	    url: '<?php echo base_url(); ?>' +"dates/get_next_date_list", 
	    type:"post",
	    data:{offset:offset},
	    cache: false,
	    success: function (response) {
	        stop_loading();
	        offset++;
	        
	        $("#userDateListing").html($(response).css('margin-left',width).animate({'margin-left': '0'}));
	        loadSlider();
	    }     
	});
}

function userDatePreference(date_id,decision,obj){
	
	loading();
	
	var num_date_tickets = $("#num_date_tickets").val();	
	$.ajax({
        url: '<?php echo base_url(); ?>' +"dates/date_decision", 
        type:"post",
        data:{date_id:date_id,decision:decision,num_date_tickets:num_date_tickets},
        dataType:'json',
        success: function (response) {
        	stop_loading();
        	if(response.type == 'error')
        	{
        		if(typeof(response.redirectUrl) != 'undefined')
        		{
        			var msg =response.msg;
        			var popupHTML = '<div class="edu-main"> <p class="text-center">'+msg+'.</p><p class="inline-element text-center"><a href="'+response.redirectUrl+'"><span class="appr-cen button-blue inline-element"><?php echo translate_phrase("Get More")?></span></a></p></div>';
		 			openFancybox(popupHTML);
        		}        		
        	}
        	else
        	{
        		$('.user_num_date_tix').text(response.num_date_tix);
        		$("#pageMsg").html(response.msg);
        		getNextDate();	
        	}
            
        }     
    });
}

function dateFilterPopup(){
    
	loading();
    //var popupHTML=$('#dateFilterPopup').html();
    $("#filterButton").css("cursor", "wait");
    $("body").css("cursor", "wait");
    $.ajax({
        url: '<?php echo base_url(); ?>' +"dates/filter_date", 
        type:"html",
        data:{},
        dataType:'html',
        success: function (response) {
        	stop_loading();
        	 openFancybox(response);
                 //$("body").css("cursor", "pointer");
                 $("#filterButton").css("cursor", "pointer");
                 $("body").css("cursor", "pointer");
            
        }     
    });
}

function mutualFriendPopup(id,other_id){
    loading();
    $.ajax({
        url: '<?php echo base_url(); ?>' +"dates/mutual_friend/"+id+"/"+other_id, 
        type:"html",
        data:{},
        dataType:'html',
        success: function (response) {
        	stop_loading();
        	 openFancybox(response);
            
        }     
    });
   // openFancybox(popupHTML);
}

function banUserDate(){
    var html = $('#banContent').html();
    openFancybox(html);
}

function banUser(id,target_user_id){
    loading();
     $.ajax({
        url: '<?php echo base_url(); ?>' +"dates/ban_user_date", 
        type:"post",
        data:{id:id,target_user_id:target_user_id},
        dataType:'json',
        success: function (response) {
        	stop_loading();
        	 $.fancybox.close();
                 getNextDate();	
            
        }     
    });
}
</script>
<div class="wrapper">
	<div class="content-part mobile-layout">
		<div class="Apply-Step1-a-main">
<!--			<div class="Top3-M">
				<div class="Top3-Inner-p">
					<div class="fl">
						<a href="<?php echo base_url(); ?>dates/my_dates" class="Edit-Button01 mar-R"> <?php echo translate_phrase('My Dates')
						?>
						( <?php //echo $user_date_cnt; ?>
						) </a>
					</div>
				</div>
			</div>-->
			<p class="mar-top2 DarkGreen-color bold" id='pageMsg'><?php echo $this->session->flashdata('pageMsg');?></p>
				
			<div class="step-form-Main mar-top2">
				
                <div id="dateFilterPopup" style="display: none">
                        <?php //$this -> load -> view('user/dates_app/date_filter_popup');?>
				</div>
                            
				<div class="datesArea bor-none" id="userDateListing" style="margin: 0px;padding: 0px">
                                    
					<?php $this -> load -> view('user/dates_app/next_date_detail_ajax');?>
				</div>
			</div>
		</div>
	</div>
</div>
