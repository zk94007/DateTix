<script src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script src="<?php echo base_url()?>assets/js/general.js"></script>
<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_INSTANT_INTRO);
?>
<!--*********Page start*********-->
<script type="text/javascript">
var user_id ="<?php echo $user_id ?>";
var defualt_date = '';
$(document).ready(function(){
	$('#active_expire').easytabs();
	//When "My Dates" tab is clicked and there is 0 row in Pending tab but >0 row in Upcoming tab, then show Upcoming tab by default (instead of showing Pending tab by default)
	var tabName = window.location.hash;
	if(tabName == '' || tabName =='#_=_')
	{
		<?php if($redirect_tab_name = $this->session->userdata('redirect_tab_name')):?>
			$('#<?php echo $redirect_tab_name?>').click();
		<?php $this->session->unset_userdata('redirect_tab_name');?>
		<?php else:?>
			<?php if((!isset($intros_data['active']) || count($intros_data['active']) ==0 ) && (isset($intros_data['upcoming']) && count($intros_data['upcoming']) > 0)):?>
				$('#UpcominEasyTab').click();
				$('body').scrollTo($('#rightPen'),800);
			<?php endif;?>
		<?php endif;?>
	}

	<?php if($id_encoded  = $this->session->userdata('redirect_intro_id')):?>
		defualt_date = 'intro_<?php echo $id_encoded;?>';
		console.log('redirecting....: '+defualt_date);
		if($("#"+defualt_date).length)
		{
			<?php if($this->session->userdata('type') == 'message'):?>
				
		        setTimeout(function(){
		        	$('html,body').animate({
				          scrollTop: $("#chatBox_<?php echo $id_encoded;?>").offset().top
				        }, 1000,function(){
					    	 $("#chatBox_<?php echo $id_encoded;?>").find(".chat-input").focus(); 
						 });
			     	},1000);
				//$('body').scrollTo($("#chatBox_<?php echo $id_encoded;?>"),2000,{easing:'easeInOutExpo', onAfter: function() { $("#chatBox_<?php echo $id_encoded;?>").find(".chat-input").focus(); }});
			<?php $this->session->unset_userdata('type');?>
			<?php else:?>
			goToScroll(defualt_date);
			<?php endif;?>
			defualt_date = '';
			//don't scroll again in next time pagination...
		}
		<?php $this->session->unset_userdata('redirect_intro_id');?>
	<?php endif;?>
		
	setTimeout(function(){
		load_all_chatbox();
	},700)
	
	$('#active_expire').bind('easytabs:after', function(tab, panel, data){
		if(panel[0].id != "ActiveDateTab")
		{
			load_all_chatbox();
		}
	});
	
	$(".rdo_div").live('click',function(){
		$(this).parent().siblings('div').removeClass('Intro-Button-sel').addClass('Intro-Button');
		$(this).parent().removeClass('Intro-Button').addClass('Intro-Button-sel');
		$(this).parent().parent().find(':input[type="hidden"]').val($(this).attr('key'));
	});

	$(".rdo_div_show_up").live('click',function(){
		$(this).parent().siblings('div').removeClass('Intro-Button-sel').addClass('Intro-Button');
		$(this).parent().removeClass('Intro-Button').addClass('Intro-Button-sel');

		$(this).parent().parent().find(':input[type="hidden"]').val($(this).attr('key'));

		$("#"+$(this).attr('lang')).fadeIn(300);
		$("#"+$(this).parent().siblings('div').find('a').attr('lang')).fadeOut(300);
		
	});

	
	$(".toggleBtn").live('click',function(){
		var containerId = "#"+$(this).attr('lang');
		if($(containerId).is(':visible')){
			$(containerId).fadeOut(300); 
			//$(this).addClass('Upgrd-blue');
		} 
	 	else {
			$(containerId).fadeIn(300);
			//$(this).removeClass('Upgrd-blue');
	 	}
	});
	
	$('.your-personality ul li a').live('click',function(e) {
		var user_date_id = $(this).parent().parent().parent().parent().attr('lang');
		e.preventDefault();
        var li = $(this).parent();
        if ($(li).find('a').hasClass('appr-cen')) 
        {
        	var ids           = new Array();
        	var desc_id       = $("#descriptive_word_id_"+user_date_id).val(); 
          	ids               = desc_id.split(',');
          	var index         = ids.indexOf(this.id);
          	ids.splice(index, 1);
          	var descriptive_id      = ids.join(); 
          	$("#descriptive_word_id_"+user_date_id).val(descriptive_id);
          	$(li).find('a').removeClass('appr-cen').addClass('disable-butn');
        } 
        else 
		{
			// check before adding
          	if ($('.your-personality[lang="'+user_date_id+'"] ul li a.appr-cen').length < 5) 
			{
            	var descriptive_id   = $("#descriptive_word_id_"+user_date_id).val();
            	if(descriptive_id!="")
					var dsc_id       = descriptive_id+','+this.id; 
				else
                	var dsc_id       = this.id;

            	$("#descriptive_word_id_"+user_date_id).val(dsc_id);
            	$(li).find('a').addClass('appr-cen').removeClass('disable-butn');
          }
        }
   });
	   
	//When click on dropdown then his ul is open..
	$(".active_sort").find('dt a').live('click',function () {
    	$(this).parent().parent().find('ul').toggle();
    });

	//When select a option..
    $(".active_sort dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());

		$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'));
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
    	if(isAjaxCallRunning == false)
    	{
    		loading();
        	$.ajax({ 
                url: '<?php echo base_url(); ?>' +"my_dates/load_dates/pending", 
                type:"post",
                data:{'sort_by':$(this).attr('key')},
                success: function (response) {
                	currentTab = 'pending';
        			active_page_no = 1; 
                	if(preventAjaxCall.indexOf(currentTab) !=  '-1')
		            {
			            preventAjaxCall.pop(currentTab);
		            }
		            else
		            {
			            console.log("NOT INDEX OF:"+preventAjaxCall.indexOf(currentTab));
			        }
			        
                	stop_loading();
                    $("#pending_container").html(response);
               }
    		});
        }
	});

    $(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("active_sort"))
        	$(".active_sort dd ul").hide();
    });
});

function scrollToDiv(id)
{
	$('body').scrollTo($('#'+id),800,{'axis':'y'});
}


var active_page_no = "<?php echo $page_no ?>";
var upcoming_page_no = "<?php echo $page_no ?>";
var expired_page_no = "<?php echo $page_no ?>";

var preventAjaxCall = [];
//Lazzy Load Pagination..
$(window).scroll(function() {

	var totalScrollAmount = $(window).scrollTop() + $(window).height() + 80;
	if( totalScrollAmount >= $(document).height()) 
	{

		var offset = 1;
		var currentTab = '';
			
		if($('#active_expire ul li.active a').attr('href') == '#pending')
		{
			currentTab = 'pending';
			offset += parseInt(active_page_no); 
		}
		
		if($('#active_expire ul li.active a').attr('href') == '#upcoming')
		{
			currentTab = 'upcoming';
			offset += parseInt(upcoming_page_no);
		}
		
		if($('#active_expire ul li.active a').attr('href') == '#past')
		{
			currentTab = 'expired';
			offset += parseInt(expired_page_no);
		}
		if(preventAjaxCall.indexOf(currentTab) == -1 && isAjaxCallRunning == false && offset != 1)
		{
			$("#"+currentTab+"_container").append('<div class="div_data_loader"></div>');
			loading();
			$.ajax({ 
	            url: '<?php echo base_url(); ?>' +"my_dates/load_dates/"+currentTab, 
	            type:"post",
	            data:{'sort_by':$("#intro_order").val(),'page_no':offset},
	            success: function (response) {
	            	stop_loading();
	            	
	            	if($.trim(response) != '')
	            	{
	            		if(currentTab == 'pending')
	            		{
	            			active_page_no = offset;
	            		}
	            		
	            		if(currentTab == 'upcoming')
	            		{
	            			upcoming_page_no = offset;
	            		}
	            		
	            		if(currentTab == 'expired')
	            		{
	            			expired_page_no = offset;
	            		}
	            		
	            		$("#"+currentTab+"_container").find('.div_data_loader').fadeOut();
	            		$("#"+currentTab+"_container").append($(response).hide().fadeIn(2000));
	            		if(defualt_date != '')
	            		{
		            		scrollToDiv(defualt_date);
		            	}
	                }
	            	else
	            	{
	            		$("#"+currentTab+"_container").find('.div_data_loader').fadeOut();
	            		preventAjaxCall.push(currentTab);
						//alert('No more data')
	               	}
	           }
			});
		}
   }
});

function submit_feedback_form(formId)
{
	if(validate_my_form($("#form_"+formId)))
	{
		$("#form_"+formId).find('form').submit();
	}
	else
	{
		console.log('validation false');
	}	
}
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="page-msg-box Red-color left">
			<?php echo $this->session->flashdata('date_error_msg');?>
			</div>
			<div class="page-msg-box DarkGreen-color left">
			<?php echo $this->session->flashdata('date_success_msg');?>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="emp-B-tabing-M-short" id="active_expire">
					<ul class='etabs'>
						<li class='tab tab-nav'><span></span><a href="#pending"
							id="ActiveDateTab"><?php echo translate_phrase('Pending') ?> </a>
						</li>
						<li class='tab tab-nav'><span></span><a href="#upcoming"
							id="UpcominEasyTab"><?php echo translate_phrase('Upcoming') ?> </a>
						</li>
						<li class='tab tab-nav'><span></span><a href="#past"
							id="PastDateTab"><?php echo translate_phrase('Past') ?> </a></li>
					</ul>

					<!-- Pending Tab content Start -->
					<div class="step-form-Main Mar-top-none Top-radius-none"
						id="pending">
						<div class="step-form-Part">
						<?php if(isset($intros_data['active']) && $intros_data['active']):?>
						<?php if($received_date_request):?>
							<div class="dateBoxHed align-left">
							<?php echo translate_phrase('You have received ');
							echo $received_date_request == 1 ? $received_date_request.translate_phrase(' date request.'):$received_date_request.translate_phrase(' date requests.');
							echo translate_phrase(' Please respond below now');?>
								!
							</div>
							<?php endif;?>
							<?php if($sent_date_request):?>
							<div class="dateBoxHed align-left Black-color comn-top-mar">
							<?php echo translate_phrase('You have sent ');
							echo $sent_date_request==1?$sent_date_request.translate_phrase(' date request.'):$sent_date_request.translate_phrase(' date requests.');
							echo translate_phrase(' You may change or cancel them below');?>
								.
							</div>
							<?php endif;?>

							<div class="sortby bor-none Mar-top-none srtby-pad">
								<div class="sortbyTxt">
								<?php echo translate_phrase('Sort by');?>
									:
								</div>
								<div class="sortbyDown">
									<dl class="active_sort dropdown-dt domaindropdown">
										<dt>
											<a href="javascript:;" key="1"><span><?php echo translate_phrase('Intro Date').' ('.translate_phrase('recent to old').')';?>
											</span> </a> <input type="hidden" name="intro_order"
												id="intro_order" value="1">
										</dt>
										<dd>
											<ul>
												<li><a href="javascript:;" key="1"><?php echo translate_phrase('Intro Date').' ('.translate_phrase('recent to old').')';?>
												</a></li>
												<li><a href="javascript:;" key="2"><?php echo translate_phrase('Intro Date').' ('.translate_phrase('old to recent').')';?>
												</a></li>

												<li><a href="javascript:;" key="3"><?php echo translate_phrase('Age').' ('.translate_phrase('young to old').')';?>
												</a></li>
												<li><a href="javascript:;" key="4"><?php echo translate_phrase('Age').' ('.translate_phrase('old to young').')';?>
												</a></li>

												<li><a href="javascript:;" key="5"><?php echo translate_phrase('First Name').' ('.translate_phrase('A to Z').')';?>
												</a></li>
												<li><a href="javascript:;" key="6"><?php echo translate_phrase('First Name').' ('.translate_phrase('Z to A').')';?>
												</a></li>
											</ul>
										</dd>
									</dl>
								</div>
							</div>

							<div id="pending_container">
							<?php $this->load->view('user/dates/load_pending_dates');?>
							</div>
							<?php else:?>
							<span><?php echo translate_phrase('You currently have no pending dates');?>.</span>
							<?php endif;?>
						</div>
					</div>
					<!-- Pending Tab Content END -->

					<!-- Upcoming Tab content Start -->
					<div class="step-form-Main Mar-top-none Top-radius-none"
						id="upcoming">
						<div class="step-form-Part">
						<?php if(isset($intros_data['upcoming']) && $intros_data['upcoming']):?>
							<div class="upgrade-top-txt">
							<?php echo translate_phrase('Here are your upcoming dates');?>
								. <span><?php echo translate_phrase('We recommend that you chat with your date in advance')?>
								</span>
								<?php echo translate_phrase('to exchange contacts and confirm location/time')?>
								.
							</div>
							<div id="upcoming_container">
							<?php $this->load->view('user/dates/load_upcoming_dates');?>
							</div>

							<?php else:?>
							<span><?php echo translate_phrase('You currently have no upcoming dates');?>.</span>
							<?php endif;?>

						</div>
					</div>
					<!-- Upcoming Tab content END -->

					<!-- past Tab content Start -->
					<div class="step-form-Main Mar-top-none Top-radius-none" id="past">
						<div class="step-form-Part">
						<?php if(isset($intros_data['expired']) && $intros_data['expired']):?>
							<div class="dateBoxHed align-left Black-color">
								<span><?php echo translate_phrase("It's essential that you leave feedback for your past dates")?>
								</span>
								<?php echo translate_phrase("so that we can find you an even better date next time")?>
								!
							</div>
							<div id="expired_container">
							<?php $this->load->view('user/dates/load_expired_dates');?>
							</div>
							<?php else:?>
							<span><?php echo translate_phrase('You currently have no past dates');?>.</span>
							<?php endif;?>
						</div>
					</div>
					<!-- past Tab content END -->
				</div>
				<!-- END emp-B-tabbing-M -->
			</div>
		</div>
	</div>
</div>
<!--*********Page close*********-->
