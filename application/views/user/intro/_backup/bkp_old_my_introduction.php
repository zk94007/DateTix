<script src="<?php echo base_url()?>assets/js/jquery.easytabs.min.js"></script>
<script
	src="<?php echo base_url()?>assets/js/general.js"></script>
<!--*********Page start*********-->
<script type="text/javascript">
var active_page_no = "<?php echo $page_no ?>";
var upcoming_page_no = "<?php echo $page_no ?>";
var expired_page_no = "<?php echo $page_no ?>";
var preventAjaxCall = [];
$(document).ready(function(){
	
	$('#active_expire').easytabs();
	//When "My Dates" tab is clicked and there is 0 row in Pending tab but >0 row in Upcoming tab, then show Upcoming tab by default (instead of showing Pending tab by default)
	var tabName = window.location.hash;
	if(tabName == '' || tabName =='#_=_')
	{
		<?php if($redirect_tab_name  = $this->session->userdata('redirect_tab_name')):?>
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
	
	/*
	$('#active_expire').bind('easytabs:after', function(tab, panel, data){
		load_all_chatbox();
	});
	*/
	
	//When click on dropdown then his ul is open..
	$(".sort_dl").find('dt a').live('click',function () {
    	$(this).parent().parent().find('ul').toggle();
    });

	//When select a option..
    $(".sort_dl dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());

		$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'));
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
		var default_page = 1;

    	var currentTab = '';
    	if($('#active_expire ul li.active a').attr('href') == '#active')
    	{
    		currentTab = 'active';
            active_page_no = default_page;
    	}
    	
    	if($('#active_expire ul li.active a').attr('href') == '#upcoming')
    	{
    		currentTab = 'upcoming';
    		upcoming_page_no = default_page;
    	}
    	
    	if($('#active_expire ul li.active a').attr('href') == '#expired')
    	{
    		currentTab = 'expired';
    		expired_page_no = default_page;
    	}
    	if(isAjaxCallRunning == false)
    	{
	    	loading();
	    	$.ajax({ 
	            url: '<?php echo base_url(); ?>' +"my_intros/load_intro/"+currentTab, 
	            type:"post",
	            data:{'sort_by':$(this).attr('key')},
	            success: function (response) {
		            if(preventAjaxCall.indexOf(currentTab) !=  '-1')
		            {
			            preventAjaxCall.pop(currentTab);
		            }
		            else
		            {
			            console.log("NOT INDEX OF:"+preventAjaxCall.indexOf(currentTab));
			        }
		            
	            	stop_loading();
	                $("#"+currentTab+"_container").html(response);
	           }
			});
    	}
	});

	
    $(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("dropdown-dt"))
        	$(".dropdown-dt dd ul").hide();
    });

  	//
    $('.get-introduce-now a').live('click',function(){
    	var parentObj = $(this).parent();
    	var userBoxId = $(this).attr('lang');

    	loading();
    	$.ajax({ 
            url: '<?php echo base_url(); ?>' +"my_intros/get_introduce_now", 
            type:"post",
            dataType:"json",
            data:{'user_intro_id':userBoxId},
            success: function (response) {
            	stop_loading();
            	if(response.flag == 'success')
            	{
            		$("#intro_"+userBoxId).slideUp('slow','swing',function(){

            			$("#intro_"+userBoxId).removeClass('border-bottom-mar-bottom').find('.Half-datebox').removeClass('hidden');
            			$(parentObj).parent().addClass('hidden');

            			var str = $("#intro_"+userBoxId).find('.userTopRowTxt').attr('lang'); 
            			var spantxt = $("#intro_"+userBoxId).find('.userTopRowTxt').attr('lang_span');
            			$("#intro_"+userBoxId).find('.userTopRowTxt').html(str+' <span class="Red-color">'+spantxt+'</span>');
            			
            			var boxHTML = '<div class="userBox" id="intro_'+userBoxId+'">'+$("#intro_"+userBoxId).html()+'</div>';
            			if($('#active_expire ul li.active a').attr('href') != '#active')
            			{
            				$('#active_expire ul li a[href="#active"]').click();
                		}

						if($("#active_container").css('display') == 'none')
						{
							$("#active_container").css('display','block');
							$("#active_container").siblings().css('display','block');
							$("#active_container").siblings('span').css('display','none');
						}
						
            			$("#active_container").prepend(boxHTML);
            			$(this).remove();
            			$('body').scrollTo($("#intro_"+userBoxId),800);
            			if($("#upcoming_container").find('.MainBox div').length == 0)
            			{
            				$("#upcoming_container").fadeOut();
            				$("#upcoming .step-form-Part p").fadeOut();
            				$("#upcoming .sortby").fadeOut();
            				
            				
							$("#upcoming").find('.no-rows').removeClass('hidden');
                		}
            		});
            	}
            }
    	});    	
     });
});


//Lazzy Load Pagination..
$(window).scroll(function() {  
	var totalScrollAmount = $(window).scrollTop() + $(window).height() + 80;
	if( totalScrollAmount >= $(document).height()) 
	{
		
		var currentTab = '';
		var offset = 1;
		
		if($('#active_expire ul li.active a').attr('href') == '#active')
		{
			currentTab = 'active';
			offset += parseInt(active_page_no); 
		}
		
		if($('#active_expire ul li.active a').attr('href') == '#upcoming')
		{
			currentTab = 'upcoming';
			offset += parseInt(upcoming_page_no);
		}
		
		if($('#active_expire ul li.active a').attr('href') == '#expired')
		{
			currentTab = 'expired';
			offset += parseInt(expired_page_no);
		}
		
		if(preventAjaxCall.indexOf(currentTab) == -1 && isAjaxCallRunning == false)
		{
			$("#"+currentTab+"_container").append('<div class="div_data_loader"></div>');

			loading();
			$.ajax({ 
	            url: '<?php echo base_url(); ?>' +"my_intros/load_intro/"+currentTab, 
	            type:"post",
	            data:{'sort_by':$("#intro_order").val(),'page_no':offset},
	            success: function (response) {
	            	stop_loading();
	            	
	            	if($.trim(response) != '')
	            	{
	            		if(currentTab == 'active')
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
		else
		{
			console.log('Sorry No more intros '+preventAjaxCall);
		}
   }
});
</script>
	<?php
	$user_id = $this->session->userdata('user_id');
	$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_UNLIMITED_DATES);
	$is_premium_instant_intro = $this->datetix->is_premium_user($user_id,PERMISSION_INSTANT_INTRO);
	$is_premium_more_intro = $this->datetix->is_premium_user($user_id,PERMISSION_MORE_INTRODUCTIONS);
	$up_where = 'DATE(intro_available_time) > CURDATE()  AND (user1_id = "'.$user_id.'" OR user2_id = "'.$user_id.'") ';
	$this->general->set_table('user_intro');
	$upcoming_intros = $this->general->custom_get("user_intro_id",$up_where);
	$total_user_upcoming_intro = count($upcoming_intros);
	?>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="emp-B-tabing-prt">
				<div class="emp-B-tabing-M-short" id="active_expire">
					<!--<div class="btn-group right">
						<a href="<?php echo base_url() . url_city_name().'/events.html'?>"><div class="Headrespon-but"><?php echo translate_phrase("Meet More People") ?></div></a>
					</div>-->
					<ul class='etabs'>
						<li class='tab tab-nav'><span></span><a id="ActiveDateTab" href="#active"><?php echo translate_phrase('Active') ?>
						</a></li>
						<li class='tab tab-nav'><span></span><a id="UpcominEasyTab"
							href="#upcoming"><?php echo translate_phrase('Upcoming') ?> </a>
						</li>
						<li class='tab tab-nav'><span></span><a href="#expired" id="PastDateTab"><?php echo translate_phrase('Expired') ?></a></li>
					</ul>
					<!-- Active Tab content Start -->
					<div class="step-form-Main Mar-top-none Top-radius-none"
						id="active">
						<div class="step-form-Part">
						<?php
						$is_active_intro = 0;
						if(isset($intros_data['active']) && $intros_data['active']){
							$is_active_intro = 1;
						}?>
							<!--<p style="display:<?php echo $is_active_intro?'block':'none;'?>">
							<?php echo translate_phrase('We would like to introduce you to the below amazing single professionals, each carefully selected based on your profile');?>
								. <span class="Ap-bold"><?php echo translate_phrase('Please respond to each now');?>
								</span>
								<?php echo translate_phrase('to let us know who you would be interested in meeting')?>
								.
							</p>-->
							<!--<div style="display:<?php echo $is_active_intro?'block':'none;'?>" class="sortby bor-none Mar-top-none srtby-pad">
								<div class="sortbyTxt">
								<?php echo translate_phrase('Sort by');?>
									:
								</div>
								<div class="sortbyDown">
									<dl class="sort_dl dropdown-dt domaindropdown">
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
							</div>-->
							<div id="active_container" style="display:<?php echo $is_active_intro?'block':'none;'?>">
							<?php $this->load->view('user/intro/active_intro');?>
							</div>
							<p>							
							<span style="display:<?php echo $is_active_intro?'none':'block;'?>"><?php echo translate_phrase('You currently have no active intros.')?>
							</span>
							</p>
						</div>
					</div>
					<!-- Active Tab Content END -->

					<div class="step-form-Main Mar-top-none Top-radius-none"
						id="upcoming">
						<div class="step-form-Part">
						<?php if(isset($intros_data['upcoming']) && $intros_data['upcoming']):?>

							<p>
							<?php if($total_user_upcoming_intro >= 3 && !$is_premium_more_intro):?>
							<?php echo ('You have reached your limit of at most 3 upcoming intros at any given time. ');?>
								<a
									href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-intros.html&tab=upcoming"
									class="blu-color"><?php echo translate_phrase('Add the More Introductions account upgrade')?>
								</a>
								<?php echo (' to increase that limit!');?>
								<?php endif;?>
							</p>
							<!--<div class="sortby bor-none Mar-top-none srtby-pad">
								<div class="sortbyTxt">
								<?php echo translate_phrase('Sort by');?>
									:
								</div>
								<div class="sortbyDown">
									<dl class="sort_dl dropdown-dt domaindropdown">
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
							</div>-->

							<div id="upcoming_container">
							<?php $this->load->view('user/intro/upcoming_intro');?>
							</div>
							<?php else:?>


							<p>
								<span class="no-rows"> <!--		                  	<?php if($is_premium_instant_intro ):?>
		                  		<?php echo translate_phrase('You have no upcoming intros because you have the');?>
						<a href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-intros.html&tab=upcoming" class="blu-color"><?php echo translate_phrase('Instant Introductions account upgrade');?></a>
						<?php echo translate_phrase('that introduces all your matches to you instantly without waiting');?>!
						<?php else:?>
                  				<?php echo translate_phrase('You currently have no upcoming intros.')?>
                  				<?php endif;?>--> <?php echo translate_phrase('You currently have no upcoming intros.')?>
								</span>
							</p>
							<?php endif;?>
						</div>
					</div>

					<!-- Expired Tab content Start -->
					<div class="step-form-Main Mar-top-none Top-radius-none"
						id="expired">
						<div class="step-form-Part">
						<?php if(isset($intros_data['expired']) && $intros_data['expired']):?>
							<!--<div class="sortby bor-none Mar-top-none srtby-pad">
								<div class="sortbyTxt">
								<?php echo translate_phrase('Sort by');?>
									:
								</div>
								<div class="sortbyDown">
									<dl class="sort_dl dropdown-dt domaindropdown">
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
							</div>-->
							<div id="expired_container">
							<?php $this->load->view('user/intro/expired_intro');?>
							</div>
							<?php else:?>
							<p>
							<span class="no-rows"><?php echo translate_phrase('You currently have no expired intros.')?>
							</span>
							</p>
							<?php endif;?>
						</div>
					</div>
					<!-- Expired Tab content END -->

				</div>
				<!-- END emp-B-tabbing-M -->
			</div>
		</div>
	</div>
</div>
<!--*********Page close*********-->
