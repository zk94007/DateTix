<!-- Add mousewheel plugin (this is optional) -->
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

<!-- Add Button helper (this is optional) -->
<link
	rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>

<!-- Add Thumbnail helper (this is optional) -->
<link
	rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

<!-- Add Media helper (this is optional) -->
<script
	type="text/javascript"
	src="<?php echo base_url();?>assets/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6"></script>


<script
	src="<?php echo base_url()?>assets/js/general.js"></script>
<?php
$user_id = $this->session->userdata('user_id');
//$is_premius_member = $this->datetix->is_premium_user($user_id);
?>
<script type="text/javascript">
$(document).ready(function(){
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
			},
			buttons : {}
		}
	});	
	$('.bxslider_profile').bxSlider({
		  pagerCustom: '#bx-pager',
		  infiniteLoop:false,
		  nextSelector: '#slider-next',
          prevSelector: '#slider-prev',
		  nextText: '<img src="<?php echo base_url()?>assets/images/l-arw-img.png" alt="" />',
		  prevText: '<img src="<?php echo base_url()?>assets/images/r-arw-img.png" alt="" />'
	});

	 $('.get-introduce-now a').live('click',function(){
			var parentObj = $(this).parent();
	    	var userBoxId = $(this).attr('lang');
	    	$.ajax({ 
	            url: '<?php echo base_url(); ?>' +"my_intros/get_introduce_now", 
	            type:"post",
	            dataType:"json",
	            data:{'user_intro_id':userBoxId},
	            success: function (response) {
	            	if(response.flag == 'success')
	            	{
	            		 window.location.assign("<?php echo base_url().url_city_name().'/my-intros.html#active';?>");
	            	}
	            }
	    	});    	
	     });
	     
	 <?php if(isset($click_to_action) && $click_to_action == 'sayhi'):?>
	 	<?php if($user_info['website_id'] > 0 && 1==2):?>
	 		$("#date_meTop").trigger('click');
	 	<?php else:?>
	 		$("#sayHiBtn").trigger('click');
	 	<?php endif;?>
	 <?php endif;?>
	 
	 
	 <?php if($id_encoded  = $this->session->userdata('redirect_intro_id')):?>
		defualt_date = 'chatBox_<?php echo $id_encoded;?>';
		if($("#"+defualt_date).length)
		{
	        $('html,body').animate(
	        	{
	        		scrollTop: $("#chatBox_<?php echo $id_encoded;?>").offset().top
	        	}, 1000,function(){
                                        $("#chatBox_<?php echo $id_encoded;?>").find(".chat-input").attr('autofocus',''); 
					$("#chatBox_<?php echo $id_encoded;?>").find(".chat-input").focus(); 
			});
		}
	<?php $this->session->unset_userdata('redirect_intro_id');?>
	<?php endif;?>
	
    setTimeout(function(){
    	load_all_chatbox();
	    if($(".bxslider_profile li").length  > 1)
	    {
	    	jQuery('#slider-next,#slider-prev').fadeIn(); 
	    }
	    else
	    {
	    	jQuery('#slider-next,#slider-prev').fadeOut(); 
	    }
    },500);
});

/* OLD FUNCTION NOW REPLACED BY UserDateInsert function below

function dateMe(user_id,obj)
{
	$(".dateMeMsg").removeClass('error-msg').removeClass('success-msg');
	if(user_id)
	{
		$.ajax({ 
			url:base_url+"user/date_me", 
			type:"post",
			data:{'user_id':user_id},
			dataType:'json',
			success: function (response) {				
				$(".dateMeMsg").text(response.msg).addClass(response.type+'-msg');				
			}  
		});
	}
}
*/
// function added by jigar oza
<?php if($user_info['user_id'] != $user_id && $is_review_resricted == 0):?>
function send_photo_request(requested_user_id,obj)
{
	var htmlLbl = '<span class="pink-colr">'+$(obj).attr('lang')+'</span>';
	$(obj).replaceWith($(htmlLbl).fadeIn());
	
	var url = '<?php echo base_url("user/send_photo_view_request")?>';
	if(requested_user_id)
	{
		$.ajax({ 
			url:url+"/"+requested_user_id, 
			type:"post",
			cache: false,
			dataType:'json',
			success: function (data) {
				if(data.type == "1")
				{
					//$(obj).replaceWith($(htmlLbl).fadeIn());
				}
			}  
		});
	}	
}

function askOut(user_intro_id,intro_id) 
{
	var user_name = $(".username").text();
	var msg = 'Hi '+$.trim(user_name)+', <?php echo translate_phrase("nice to meet you here! Want to meet up sometimes so we can get to know each other better?")?>';
	
	send_message(msg,user_intro_id,intro_id);
	$('html,body').animate({
			scrollTop: $("#chatBox_"+user_intro_id).offset().top
		}, 
		1000,
		function(){
			$("#chatBox_"+user_intro_id).find(".chat-input").focus();
	});
}

function sayHi(user_intro_id,intro_id)
{
	var user_name = $(".username").text();
	var msg = 'Hi '+$.trim(user_name)+', <?php echo translate_phrase("nice to meet you here! How are you doing?")?>';
	
	send_message(msg,user_intro_id,intro_id);
	$('html,body').animate({
			scrollTop: $("#chatBox_"+user_intro_id).offset().top
		}, 
		1000,
		function(){
			$("#chatBox_"+user_intro_id).find(".chat-input").focus();
	});
}
function UserDateInsert(intro_id,intro_user_id){
	
	loading();
	$.ajax({
		url: '<?php echo base_url(); ?>' +"people/get_upcoming_date", 
		type:"post",
		data:{target_user_id:'<?php echo $user_info['user_id']; ?>',decision:'1'},
		dataType:'html',
		success: function (response) {
	    	stop_loading();
	        openFancybox(response);
		}
	});
	
	loading();
	$.ajax({ 
	    url: '<?php echo base_url(); ?>' +"user/insert_user_date", 
	    type:"post",
	    data:{'intro_id':intro_id,'intro_user_id':intro_user_id},
	    success: function (response) {
	    	stop_loading();
	    	//if(response.flag=='success'){
	                $('div#dateSaveMessage_'+intro_id+' h4 span#DarkGreen-color').html(response);
	            //}
	   }
	});
}

function inviteUser(date_id,invite_user_id,decision){
 	loading();
	$.ajax({
		url: '<?php echo base_url(); ?>' +"people/invite_user", 
		type:"post",
		data:{target_user_id:invite_user_id,decision:decision,date_id:date_id},
		dataType:'json',
		success: function (response) {
	    	$.fancybox.close();
	        stop_loading();	        
	        $('#invitemessage').html(response.msg);
		}
	});
}

function follow_user(id,type){
	loading();
	$.ajax({ 
		url: '<?php echo base_url(); ?>' +"user/follow_user", 
		type:"post",
		data:{id:id,type:type},
		success: function (response) {			
			stop_loading();
			if(type=='1'){
				
				$('#followButton').hide();
				$('#unfollowButton').css('display','inline-block');
			}else{    			
    			$('#unfollowButton').hide();
				$('#followButton').css('display','inline-block');
			}
		}
    });
}

<?php endif;?>
</script>

<div class="wrapper">
	<div class="content-part">
		<span class="DarkGreen-color left " id='invitemessage' style='text-align: left'></span>
		<div class="top-jen">
			<?php if ($user_info['gender_id'] == '1'): ?>
				<h1 class="jen-name-blue username"><?php echo $user_info['first_name']; ?></h1>
			<?php else: ?>
				<h1 class="jen-name-pink username"><?php echo $user_info['first_name']; ?></h1>
			<?php endif; ?>
			<p class="jen-span">
			<?php echo ($user_info['user_age'])?$user_info['user_age'].translate_phrase(' years old').', ':''; ?>
			<?php echo ($user_info['user_ethnicity'])?$user_info['user_ethnicity'].',':''; ?>
			<?php if (count($user_info['user_want_gender']) <= 1): ?>
				<?php if (isset($user_info['user_want_gender'][0]['description']) && $user_info['user_want_gender'][0]['description'] == $user_info['user_gender']): ?>
					<?php if ($user_info['user_gender'] == 'Male' || $user_info['user_gender'] == '男' || $user_info['user_gender'] == '男'): ?>
						<?php echo translate_phrase('Gay') ?>
					<?php else: ?>
						<?php echo translate_phrase('Lesbian') ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo translate_phrase('Straight') ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo translate_phrase('Bisexual') ?>
			<?php endif; ?>
			</p>
			<p class="jen-span">
			<?php
			if ($user_info['user_want_relationship_type']) {
				$looks = '';
				foreach ($user_info['user_want_relationship_type'] as $r_type)
				{
					$looks .= $r_type['description'] . ', ';
				}
				echo translate_phrase('Looking for ').trim($looks, ', ');
			}
			?>
			</p>
			<p class="jen-span">
			<?php echo ($user_info['user_current_location']['city_description'])?$user_info['user_current_location']['city_description'].',':''; ?>
			<?php echo ($user_info['user_current_location']['country_description'])?$user_info['user_current_location']['country_description']:''; ?>
			</p>
		</div>
		
		<div class="cont-detail">
			
			<div class="cont-det-left">
			<p class="style01"><?php echo isset($intro['upcoming_event_attendance'])?$intro['upcoming_event_attendance']:''?></p>
			<?php if(1==1 || $user_info['privacy_photos'] == 'SHOW' || $user_info['is_user_view_photo'] || (isset($cur_user_info['user_id']) && $cur_user_info['user_id'] == $user_info['user_id'])) :?>
				<!-- -START PHOTO SECTION -->
				<?php if($user_info['user_photos']): ?>
				<div class="img-left-box">
					<ul class="bxslider_profile">
					<?php foreach ($user_info['user_photos'] as $photo): ?>
						<li class="img-slide"><a class="fancybox-thumb" rel="gallery2"
							href="<?php echo $photo['url'] ;?>"> <img style="height: 180px"
								src="<?php echo $photo['url'] ?>"
								alt="<?php echo $photo['photo'] ?>" /> </a>
						</li>
						<?php endforeach; ?>
					</ul>
					<!-- Following Code is used for generate Next-Prev Link, We can put this div at outside of bxSlider but i have put here for don't messup design :( -->
					<div class="outside">
						<div id="slider-next" class="l-arw-img"></div>
						<div id="slider-prev" class="r-arw-img"></div>
					</div>
				</div>

				<div class="sml-img thumb-list" id="bx-pager">
				<?php if ($user_info['user_photos']): ?>
				<?php foreach ($user_info['user_photos'] as $key => $photo): ?>
					<a data-slide-index="<?php echo $key; ?>" href="javascript:;"> <img
						height="50" src="<?php echo $photo['url'] ?>"
						alt="<?php echo $photo['photo'] ?>" /> </a>
						<?php endforeach; ?>
						<?php endif; ?>
				</div>
				<?php else:?>
				<?php echo translate_phrase('No photos added yet');?>
				<?php endif; ?>
				<?php else:?>
					<?php if(isset($cur_user_info)):?>
						<?php if($user_info['is_user_request_sent']):?>
						<span class="pink-colr"><?php echo translate_phrase('Waiting for ').$user_info['first_name'].translate_phrase(' to approve your photo viewing request');?></span>
						<?php else:?>
						<a href="javascript:;" onclick="send_photo_request(<?php echo $user_info['user_id'] ?>,this)"  lang="<?php echo translate_phrase('Waiting for ').$user_info['first_name'].translate_phrase(' to approve your photo viewing request');?>"> <span class="appr-cen"><?php echo translate_phrase('Request to View Photos');?></span></a>
						<?php endif;?>
					<?php else:?>
						&nbsp;
					<?php endif;?>
				<?php endif;?>
				
				<?php if($user_info['user_id'] != $user_id && $is_review_resricted == 0):?>        
				<div class="edu-main">
					<?php if(isset($intro) && $intro):?>
					<div id="dateSaveMessage_<?php echo $this->utility->encode($intro['user_intro_id'])?>" class="left align-left">
		                    <h4>                     
		                        <span id="DarkGreen-color" color="DarkGreen-color" style="color:green"></span>                                                                        
		                    </h4>
		                </div>
		             <?php endif;?>   
					<div class='btn-group text-center'>
						<?php if(isset($intro) && $intro):?>
						<a href="javascript:;" style="display: inline-block;" onclick="UserDateInsert('<?php echo $this->utility->encode($intro['user_intro_id'])?>','<?php echo $intro['user_id'];?>')">
						<span class="appr-cen">
							<?php echo translate_phrase('Date ');?>
						<?php if($user_info['gender_id'] == '1'):?>
							<?php echo translate_phrase('Him');?>
						<?php else:?>
							<?php echo translate_phrase('Her');?>
						<?php endif;?>																														
						</span></a>
						
						<?php endif;?>
						<?php 
						$chateurl = "";
						if(isset($date_interection_data) && $date_interection_data):
						$chateurl = base_url() . 'dates/chat_history/' . $this->utility->encode( $user_info['user_id']) . '/' . $this->utility->encode( $user_id); 			
						?>
						<div class="cl"></div>     
						<a href="<?php echo $chateurl;?>" style="display: inline-block;">
							<span class="appr-cen btn-blue"><?php echo translate_phrase('Chat');?></span>
						</a>
						<?php endif;?>
						
	                    <a style="display: <?php echo (!$checkFollowing) ? 'inline-block' : 'none' ?>" href="javascript:;" id="followButton" onclick="follow_user('<?php echo $user_info['user_id'];?>','1')">
	                    	<span class="appr-cen btn-blue"><?php echo translate_phrase('Follow');?></span></a>
	                    <a style="display: <?php echo ($checkFollowing) ? 'inline-block' : 'none' ?>" href="javascript:;" id="unfollowButton" onclick="follow_user('<?php echo $user_info['user_id'];?>','0')">
	                    	<span class="appr-cen btn-blue"><?php echo translate_phrase('Unfollow');?></span>
	                    </a>
	                </div>
				</div>
				<?php endif;?>
                <!-- -END PHOTO SECTION -->
			</div>

			<!-- BREAK POINT 1 -->
				<div class="cont-det-right">
				<div class="det-right-first">
					<div class="det-center-inner1">						
						<?php if(isset($cur_user_info['user_id']) && $cur_user_info['user_id'] != $user_info['user_id']):?>							
						<?php 
						
							if(isset($intro))
							{
                                $not_interested = '0000-00-00 00:00:00';
								$intro_not_interested = '0000-00-00 00:00:00';
								if(isset($intro) && $intro['user1_id'] == $user_id)
								{
									$intro_id = $intro['user2_id'];
									$not_interested  = $intro['user1_not_interested_time'];
									$intro_not_interested = $intro['user2_not_interested_time'];
									$is_ticket_paid_user = isset($intro['user1_date_ticket_paid_by'])?$intro['user1_date_ticket_paid_by']:'0';
								}
			
								if(isset($intro) &&  $intro['user2_id'] == $user_id)
								{
									$intro_id = $intro['user1_id'];
									$not_interested  = $intro['user2_not_interested_time'];
									$intro_not_interested  = $intro['user1_not_interested_time'];
									$is_ticket_paid_user = isset($intro['user2_date_ticket_paid_by'])?$intro['user2_date_ticket_paid_by']:0;
								}
								/* OPTIMISED CODE : [5 Seconds] */
								$is_premium_intro = $this->datetix->is_premium_user($intro_id,1);
								//$mutual_friends_on_datetix = $this->datetix->datetix_mutual_friend($user_id,$intro_id);
								//$fb_mutual_friend_use_app = count($mutual_friends_on_datetix );
							
							   if(date("Y-m-d", strtotime($intro['intro_expiry_time'])) < SQL_DATE)
								{
										// Check if intro has expired
										$is_premius_member_re_intros = 0; //$this->datetix->is_premium_user($user_id,PERMISSION_RE_INTRO);
										if($is_premius_member_re_intros)
										{
											?>
											
											<div class="profile-chat-buttons">
													<!--<a href="javascript:;" onclick="askOut('<?php echo $intro['user_intro_id'];?>','<?php echo $intro_id;?>')" ><span class="appr-cen">Ask Her Out</span></a>-->
											<?php if($user_info['gender_id'] == '1'):?>
											<!--		<a href="javascript:;" onclick="sayHi('<?php echo $intro['user_intro_id'];?>','<?php echo $intro_id;?>')"><span class="appr-cen">Say Hi to <?php echo $intro['intro_name']?></span></a>-->
											<?php else:?>
													<!--<a href="javascript:;" onclick="sayHi('<?php echo $intro['user_intro_id'];?>','<?php echo $intro_id;?>')"><span class="appr-cen">Say Hi to <?php echo $intro['intro_name']?></span></a>-->
											<?php endif;?>																														
											</div>
											
											<?php 
										}
								}
								else
								{									
									if(date("Y-m-d", strtotime($intro['intro_available_time'])) <= SQL_DATE)
									{
										?>
										<div class="profile-chat-buttons">
											<!--<a href="javascript:;" onclick="askOut('<?php echo $intro['user_intro_id'];?>','<?php echo $intro_id;?>')" ><span class="appr-cen">Ask Her Out</span></a>-->
										<?php if($user_info['gender_id'] == '1'):?>
											<?php if($user_info['website_id'] > 0 && 1==2):?>
												<div class="dateMeMsg"></div>
												<a href="javascript:;" id="date_meTop" onclick="dateMe('<?php echo $user_info['user_id'];?>',this)"><span class="appr-cen"><?php echo translate_phrase('Date Him');?></span></a>						
											<?php else:?>
											<!--<a href="javascript:;" id="sayHiBtn" onclick="sayHi('<?php echo $intro['user_intro_id'];?>','<?php echo $intro_id;?>')"><span class="appr-cen"><?php echo translate_phrase('Say Hi to Him');?></span></a>-->
											<?php endif;?>
										<?php else:?>
											<?php if($user_info['website_id'] > 0 && 1==2):?>
												<div class="dateMeMsg"></div>
												<a href="javascript:;" id="date_meTop" onclick="dateMe('<?php echo $user_info['user_id'];?>',this)"><span class="appr-cen"><?php echo translate_phrase('Date Her');?></span></a>
											<?php else:?>
												
											<!--<a href="javascript:;" id="sayHiBtn" onclick="sayHi('<?php echo $intro['user_intro_id'];?>','<?php echo $intro_id;?>')"><span class="appr-cen"><?php echo translate_phrase('Say Hi to Her');?></span></a>-->
											<?php endif;?>
										<?php endif;?>																														
										</div>
										<?php 
									}
								}
							}					
					?>
					<?php endif;?>
					
						<div class="mutualLink">
						<?php if(isset($cur_user_info['user_id']) && user_country_id() != FB_RESTRICTED_COUNTRY && $cur_user_info['user_id'] != $user_id):?>
							<?php if (isset($user_info['fb_mutual_friend']) && $user_info['fb_mutual_friend']): ?>
				                <a class="" href="<?php echo base_url().url_city_name().'/mutual-friends.html?fb_id='.$user_info['facebook_id'];?>"><?php echo $user_info['fb_mutual_friend'];?></a>
							<?php else:?>
								<span><?php echo $user_data['facebook_id'] && $user_info['facebook_id']?translate_phrase('No Mutual Friends'):translate_phrase('Mutual Friends Info Not Available');?></span>
							<?php endif; ?>
						<?php endif;?>
						</div>
						<div class="det-center-inner2 comn-top-mar">
							<p class="style01">
							<?php
								if($user_info['useMeters']  == '1' && (!empty($user_info['height']))){
									echo $user_info['height'].' cm';
								}
								else {
									if($user_info['feetFrom']){
										echo $user_info['feetFrom']."' ".$user_info['inchFrom'];	
									}
								}
							?>
							</p>
							<!-- <p class="style01"><?php //echo translate_phrase($user_info['user_gender'])?></p>-->
							<!---gender--->
							<p class="style01"><?php echo $user_info['user_career_stage'] ?></p>
							<p class="style01"><?php echo $user_info['user_neighborhood'] ?></p>
						</div>
						
						<?php if($user_info['birth_date']):?>
						<div class="flg-part">
							<?php $zodiac_sign = getStarSign(strtotime($user_info['birth_date']));?>
							<img style="width: 35px;"
								src="<?php echo base_url().'assets/images/horoscopes/'.$zodiac_sign.'.svg.png'; ?>"
								class="w-flg" alt="" /> <span class="w-flg" style="width: 100%;"><?php echo $zodiac_sign ?>
							</span>
						</div>
						<?php endif;?>
					</div>
					
					<div class="det-center-inner3">
					<!-- Display Company Data First -->
					<?php if (isset($user_info['company_data']) && $user_info['company_data']): ?>
					<?php foreach ($user_info['company_data'] as $cmpny): ?>
						<p class="style01">
							<!-- 
                                	<?php if ($cmpny['job_function_id'] && isset($cmpny['job_function_data'])): ?>
                                        <?php echo $cmpny['job_function_data']['description']; ?>
	                                <?php elseif($cmpny['job_title']): ?>
	                                <?php endif; ?>
	                                 -->
	                                <?php echo $cmpny['job_title']; ?>

	                                <?php if (!$cmpny['show_company_name']): ?>
	                                <?php echo isset($cmpny['industry_description'])?' in '.$cmpny['industry_description'].' '.translate_phrase('industry'):''; ?>
	                                <?php else: ?>
		                                <?php if($cmpny['job_title']): ?>
		                                	<?php echo ' at '; ?>
		                                <?php endif; ?>
		                                <?php echo $cmpny['company_name']; ?>
	                                <?php endif; ?>
	                                <?php

	                                $year_end = $cmpny['years_worked_end'];
	                                if($cmpny['years_worked_start']!="0" && $year_end !="" ){
	                                	if($year_end == 9999)
	                                	{
	                                		$year_end = 'Present';
	                                	}

	                                	echo "(".$cmpny['years_worked_start']." to ".$year_end.")";
	                                }

	                                ?>
	                                <?php if ($cmpny['is_verified']): ?>
							<a href="javascript:;"><img
								src="<?php echo base_url() ?>assets/images/verified.png" alt=""
								class="mar-verify" /> </a>
								<?php endif; ?>
						</p>
						<?php endforeach; ?>
						<?php endif; ?>
						<p class="style01">&nbsp;</p>

						<!-- Now Display Education Data -->
						<?php if (isset($user_info['education_data'])): ?>
						<?php foreach ($user_info['education_data'] as $edu): ?>
						<p class="style01">
						<?php if ($edu['degree_name'] != ''): ?>
							<?php echo $edu['degree_name'] . ' ' . translate_phrase('from') . ' ' ; ?>
						<?php endif; ?>
						<?php echo $edu['school_name']; ?>

						<?php
						$duration = '';
						if ($edu['years_attended_start'] && $edu['years_attended_end'])
						$duration = $edu['years_attended_start'] .  ' ' . translate_phrase('to') .  ' ' . $edu['years_attended_end'];
						elseif (!$edu['years_attended_start'] && $edu['years_attended_end'])
						$duration = $edu['years_attended_end'];
						elseif ($edu['years_attended_start'] && !$edu['years_attended_end'])
						$duration = $edu['years_attended_start'] . ' ' . translate_phrase('to Present');
						if ($duration != '')
						echo "(".$duration.")";
						?>

						<?php if ($edu['is_verified']): ?>
							<a href="javascript:;"><img
								src="<?php echo base_url() ?>assets/images/verified.png" alt=""
								class="mar-verify" /> </a>
								<?php endif; ?>
						</p>
						<?php endforeach; ?>
						<?php endif; ?>
						<p class="style01">&nbsp;</p>

						<?php if (isset($user_info['user_current_location']) && $user_info['user_current_location'] && !empty($user_info['user_annual_income_range'])): ?>
						<p class="style01 lin-hght34">
						<?php echo translate_phrase('Annual income of') . ' ' ?>
						<?php echo $user_info['user_current_location']['currency_description'] ?>
						<?php echo $user_info['user_annual_income_range'] ?>
						</p>
						<?php endif; ?>
					</div>
				
				<!-- End Mutual Friends -->
				</div>

				<div class="affiliations-part">
					<div class="affiliations-ttle"><?php echo translate_phrase('Affiliations') ?></div>
					<div class="affiliations-logo">
					<?php
					$is_affilation = false;
					if (isset($user_info['education_data']) && $user_info['education_data']): ?>
					<?php
					$unique_array = array();
					foreach ($user_info['education_data'] as &$v) {
						if (!isset($unique_array [$v['school_id']]))
						$unique_array [$v['school_id']] =& $v;
					}
					foreach ($unique_array  as $data): ?>
					<?php if ($data['school_id'] && $data['school_data']['logo_url'] != "" && file_exists('school_logos/'.$data['school_data']['logo_url'])): ?>
						<div class="aff-logo">
							<img style="max-width: 100px; max-height: 100px;"
								src="<?php echo base_url().'school_logos/'.$data['school_data']['logo_url']; ?>"
								alt="" />
						</div>
						<?php $is_affilation = true; endif; ?>
						<?php endforeach;?>
						<?php endif; ?>

						<?php  if (isset($user_info['company_data']) && $user_info['company_data']): ?>
						<?php
						$unique_array = array();
						foreach ($user_info['company_data'] as &$v) {
							if ($v['show_company_name'] == '1' && !isset($unique_array [$v['company_id']]))
							$unique_array [$v['company_id']] =& $v;
						}

						foreach ($unique_array as $career): ?>
						<?php if ($career['show_company_name'] && $career['company_id'] && isset($career['company_data']) && $career['company_data']['logo_url'] != "" && file_exists('company_logos/'. $career['company_data']['logo_url'])): ?>
						<div class="aff-logo">
							<img title="<?php echo $career['company_id'];?>"
								style="max-width: 100px; max-height: 100px;"
								src="<?php echo base_url().'company_logos/'. $career['company_data']['logo_url'] ?>"
								alt="" />
						</div>
						<?php $is_affilation = true; endif; ?>
						<?php endforeach;?>
						<?php endif; ?>
						<?php if($is_affilation == false):?>
						<div class="aff-logo">
						<?php echo translate_phrase('None')?>
						</div>
						<?php endif;?>
					</div>

					<div class="affiliations-ttle"><?php echo translate_phrase('Verified Information') ?></div>
					<div class="affiliations-logo">
					<?php $is_varified = false;?>
					<?php if ($user_info['photo_id_is_verified']): ?>
						<div class="aff-logo">
							<img src="<?php echo base_url() ?>assets/images/photo-id.jpg"
								alt="" />
						</div>
						<?php $is_varified = true; endif; ?>

						<?php
						if ($user_info['mobile_phone_is_verified']): ?>
						<div class="aff-logo">
							<img src="<?php echo base_url() ?>assets/images/mb-picn.jpg"
								alt="" />
						</div>
						<?php $is_varified = true; endif; ?>

						<?php if ($user_info['facebook_page_is_verified']): ?>
						<div class="aff-logo">
							<img src="<?php echo base_url() ?>assets/images/fb-icn.jpg"
								alt="" />
						</div>
						<?php $is_varified = true; endif; ?>

						<?php if ($user_info['linkedin_page_is_verified']): ?>
						<div class="aff-logo">
							<img src="<?php echo base_url() ?>assets/images/lindin-icn.jpg"
								alt="" />
						</div>
						<?php $is_varified = true; endif; ?>
						<?php if ($user_info['twitter_username_is_verified']): ?>
						<div class="aff-logo">
							<img src="<?php echo base_url() ?>assets/images/twt-icn.jpg"
								alt="" />
						</div>
						<?php $is_varified = true; endif; ?>
						<?php if($is_varified == false):?>
						<div class="aff-logo">
						<?php echo translate_phrase('None')?>
						</div>
						<?php endif;?>
					</div>
					<?php //if($user_info['website_id'] > 0):?>
						<div class="dateMeMsg"></div>
						<?php //if($user_info['gender_id'] == '1'):?>						
							<!--<a href="javascript:;" id="date_me" onclick="dateMe('<?php echo $user_info['user_id'];?>',this)"><span class="appr-cen"><?php echo translate_phrase('Date Him');?></span></a>
						<?php //else:?>
							<a href="javascript:;" id="date_me" onclick="dateMe('<?php echo $user_info['user_id'];?>',this)"><span class="appr-cen"><?php echo translate_phrase('Date Her');?></span></a>-->
						<?php //endif;?>
					<?php //else:?>
						<?php if(isset($cur_user_info['user_id']) && $cur_user_info['user_id'] != $user_info['user_id']):?>
						<?php
	                     if(isset($intro)){
								include APPPATH.'views/user/include/action_box.php';
								if(date("Y-m-d", strtotime($intro['intro_expiry_time'])) < SQL_DATE && get_assets('website_id','0') != 3)
								{
										if($is_premius_member_re_intros)
										{
												//include APPPATH.'views/user/include/chat_box.php';
										}
										else
										{
										?>
										<!--<p><font color=red><?php echo translate_phrase('Your introduction to ') . $user_info['first_name'] . translate_phrase (' expired on ') . date(DATE_FORMATE,strtotime($intro['intro_expiry_time']))?>
										. <a href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=<?php echo $return_page;?>&tab=expired"><?php echo translate_phrase("Add the Re-Introductions upgrade to your account");?></a><?php echo translate_phrase(' to chat with and date expired introductions')?>
										</font></p>-->
										<?php
										}						
								}
								else{
										if(date("Y-m-d", strtotime($intro['intro_available_time'])) <= SQL_DATE && get_assets('website_id','0') != 3)
										{
												//active intro
												//include APPPATH.'views/user/include/chat_box.php';
										}
								} 
						}					
						?>
						<?php endif;?>
					<?php // endif;?>
				</div>
			</div>		
			<!-- END BREAK POINT -->
			
		</div>

		<?php if(isset($cur_user_info['user_id']) && $cur_user_info['user_id'] != $user_info['user_id']):?>

		<div class="background-part">
			<div class="background-head">
				<h1><img src="<?php echo base_url() ?>assets/images/back-icn1.png" alt="" />
						<?php echo translate_phrase('Compatibility');
							$score = ($cur_usr_demand_cmp['score'] + $view_usr_demand_cmp['score'])/2;
						?>
					- <span class="<?php echo $score < 50 ?'Red-color':'Green-color';?>"><?php echo round($score).'/100'?> </span></h1>
			</div>
			<div class="self-sum-prt">
				<div class="second-box-bor1">
					<p>
					<?php
					//gender_id=1 for male
					if ($user_info['gender_id'] == '1') {
						$vuserfits = translate_phrase('He fits');
						$vuser_looking_for = translate_phrase('of what he is looking for');
					} else {
						$vuserfits = translate_phrase('She fits');
						$vuser_looking_for = translate_phrase('of what she is looking for');
					}
					?>
					<?php echo $vuserfits; ?>
						<span class="second-box-bcolr-red"><?php echo (isset($cur_usr_demand_cmp['score']) ? $cur_usr_demand_cmp['score'].'%' : '') ?>
						</span>
						<?php echo translate_phrase('of what you are looking for') ?>
						.<br /> <strong> <span id="cur_usr_demand_cmp" class="star"></span>
							<br /> <?php echo translate_phrase('Key compatible areas') ?>: </strong><span
							class="second-box-bcolr2"><?php echo ($cur_usr_demand_cmp['match_data'] ? str_replace("_"," ",implode(', ', $cur_usr_demand_cmp['match_data'])) : translate_phrase('None')) ?>
						</span>
					</p>
					<p>
						<strong><?php echo translate_phrase('Key incompatible areas') ?>:</strong>
						<span class="second-box-bcolr2"><?php echo ($cur_usr_demand_cmp['not_matched'] ? str_replace("_"," ",implode(', ', $cur_usr_demand_cmp['not_matched'])) : translate_phrase('None')) ?>
						</span>
					</p>
				</div>

				<div class="second-box-bor1 second-box-pad">
					<p>
					<?php echo translate_phrase('You fit') ?>
						<span class="second-box-bcolr-green"><?php echo (isset($view_usr_demand_cmp['score']) ? $view_usr_demand_cmp['score'].'%' : '') ?>
						</span>
						<?php echo $vuser_looking_for ?>
						.<br /> <span id="view_usr_demand_cmp"></span> <br /> <strong><?php echo translate_phrase('Key compatible areas') ?>:</strong>
						<span class="second-box-bcolr2"><?php echo ($view_usr_demand_cmp['match_data'] ? str_replace("_"," ",implode(', ', $view_usr_demand_cmp['match_data'])) : translate_phrase('None')) ?>
						</span>
					</p>

					<p>
						<strong><?php echo translate_phrase('Key incompatible areas') ?>:</strong>
						<span class="second-box-bcolr2"><?php echo ($view_usr_demand_cmp['not_matched'] ? str_replace("_"," ",implode(', ', $view_usr_demand_cmp['not_matched'])) : translate_phrase('None')) ?>
						</span>
					</p>
				</div>
				<div class="appear-prt second-box-pad">
					<h2><?php echo translate_phrase('Common Interests') ?></h2>
					<div class="appear-prt-but">
					<?if (!empty($commonInterests)):?>
					<?php foreach ($commonInterests as $key => $value):?>
						<div class="appr-cen button-blue">
						<?php echo $value; ?>
						</div>
						<?php endforeach;?>
						<?php else:?>
						<div class="self-para">
						<?php echo translate_phrase("You don't have any interests in common")?>
							.
						</div>
						<?php endif;?>

					</div>
				</div>
				
			</div>
		</div>
		<?php endif;?>


		<div class="background-part">
			<div class="background-head">
				<h1>
					<img src="<?php echo base_url() ?>assets/images/back-icn1.png"
						alt="" />
						<?php echo translate_phrase('Background') ?>
				</h1>
			</div>
			<?php if($user_info['self_summary']):?>
			<div class="self-sum-prt">
				<h2>
				<?php echo translate_phrase('Self-Summary') ?>
				</h2>
				<p class="self-para">
				<?php echo $user_info['self_summary']?'"'.$user_info['self_summary'].'"':'' ?>
				</p>
			</div>
			<?php endif;?>
			
			<?php
				$appearance = array();
				if ($user_info['user_looks'])
				$appearance[] = $user_info['user_looks'] . ' ' . translate_phrase('Looks');

				if ($user_info['user_eye_color'])
				$appearance[] = $user_info['user_eye_color'] . ' ' . translate_phrase('Eyes');

				if ($user_info['user_hair_color'])
				$appearance[] = $user_info['user_hair_color'] . ' ' . translate_phrase('Hair');

				if ($user_info['user_hair_length'])
				$appearance[] = $user_info['user_hair_length'] . ' ' . translate_phrase('Hair');

				if ($user_info['user_skin_tone'])
				$appearance[] = $user_info['user_skin_tone'] . ' ' . translate_phrase('Skin');

				if (isset($user_info['user_eyewear']) && $user_info['user_eyewear'])
				{
					$eye_txt = translate_phrase('Usually Wears').' ';
					foreach ($user_info['user_eyewear'] as $key=>$eye_wear)
					{
						$eye_txt .= $eye_wear['eyewear_description'];
						if(isset($user_info['user_eyewear'][++$key]))
						{
							$eye_txt .=' or ';
						}
					}

					$appearance[] = $eye_txt;
				}
				?>
			<?php if($appearance):?>
			<div class="appear-prt">
				<h2>
				<?php echo translate_phrase('Appearance') ?>
				</h2>
				<div class="appear-prt-but">
				<?php foreach ($appearance as $value): ?>
					<div class="appr-cen button-blue">
					<?php echo $value; ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif;?>
			<?php if (isset($user_info['user_nationality']) && $user_info['user_nationality']): ?>
			<div class="appear-prt">
				<h2>
				<?php echo translate_phrase('Nationality') ?>
				</h2>
				<div class="appear-prt-but">
				
				<?php foreach ($user_info['user_nationality'] as $key => $cntry): ?>

					<div class="flag-container">
						<div class="flageBox">
							<div class="flg-b-im" style="height: 47px;">
							<?php if ($cntry['flag_url']): ?>
								<img style="width: 50px; height: 40px; padding-bottom: 7px"
									src="<?php echo base_url() ?>country_flags/<?php echo $cntry['flag_url']; ?>"
									alt="flag" />
									<?php else:?>
								<!--<img style="width: 50px;height:40px;padding-bottom: 7px" src="<?php echo base_url() ?>country_flags/Canada.png" alt="flag" />-->
									<?php endif; ?>
							</div>

						</div>
						<div class="appr-cen align-left button-blue">
						<?php echo $cntry['description'] ?>
						<?php //if (isset($user_info['user_nationality'][++$key])) echo ' ' ?>
						</div>
					</div>
					<?php endforeach; ?>
					
				</div>
			</div>
			<?php endif; ?>
			
			<?php if (isset($user_info['user_spoken_languages']) && $user_info['user_spoken_languages']): ?>
			<div class="appear-prt">
				<h2>
				<?php echo translate_phrase('Languages') ?>
				</h2>
				<div class="appear-prt-but">
				
				<?php foreach ($user_info['user_spoken_languages'] as $key => $lang): ?>

					<div class="appr-cen button-blue">
					<?php echo $lang['spoken_lang_description'] . ' (' . $lang['spoken_lang_level_description'] . ')'; ?>
					</div>

					<?php endforeach; ?>
					
				</div>
			</div>
			<?php endif; ?>
			
			<?php if (isset($user_info['userCityLivedIn']) && $user_info['userCityLivedIn']): ?>				
			<div class="appear-prt">
				<h2>
				<?php echo translate_phrase('Cities Lived In') ?>
				</h2>
				<div class="appear-prt-but">
				<?php foreach ($user_info['userCityLivedIn'] as $key => $data): ?>
					<div class="appr-cen button-blue">
					<?php echo $data->city_name . ' (' .$data->countryName. ')'; ?>
					<?php 
					if(isset($cur_user_info))
						echo (!empty($cur_user_info['birth_city_name']) && trim($data->city_name) == trim($cur_user_info['birth_city_name']) || $data->country_id == $cur_user_info['birth_country_id']) ? '(Birthplace)' : '' 
					?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if (isset($user_info['user_interest']) && $user_info['user_interest']): ?>
				<div class="appear-prt">
				<h2>
				<?php echo translate_phrase('Interests') ?>
				</h2>
				<div class="appear-prt-but">
				<?php foreach ($user_info['user_interest'] as $key => $data): ?>

					<div class="appr-cen button-blue">
					<?php echo $data['interst_description']; ?>
					</div>

					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
			
			<?php
				$others = array();
				if ($user_info['user_relationship_status'])
				$others [] = $user_info['user_relationship_status'];

				if ($user_info['user_religious_belief'])
				$others [] = $user_info['user_religious_belief'];

				if ($user_info['user_child_status'])
				$others [] = $user_info['user_child_status'];

				if ($user_info['user_smoking_status'])
				$others [] = $user_info['user_smoking_status'];

				if ($user_info['user_drinking_status'])
				$others [] = $user_info['user_drinking_status'];
			?>
			<?php if($others ):?>
				
			<div class="appear-prt">
				<h2><?php echo translate_phrase('Others') ?></h2>
				<div class="appear-prt-but">
				<?php foreach ($others as $value): ?>
					<div class="appr-cen button-blue">
					<?php echo $value; ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
			
			<?php if (isset($user_info['user_date_type']) && $user_info['user_date_type']): ?>
			<div class="appear-prt">
				<h2>
				<?php echo translate_phrase('First Date Preferences') ?>
				</h2>
				<div class="appear-prt-but">
				<?php foreach ($user_info['user_date_type'] as $key => $data): ?>
	
					<div class="appr-cen button-blue">
					<?php echo $data['date_type_description']; ?>
					</div>
	
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		
			<?php if(isset($user_info['preferred_date_days']) && !empty($user_info['preferred_date_days'])):?>
			<div class="appear-prt-but">					
				<div class="appr-cen button-blue">
					<?php echo 'Usually free on' ; ?>
					<?php
					$pref = '';
					$pref1 = '';

					$pref = explode(',', $user_info['preferred_date_days']);
					sort($pref);
					for ($i = 0; $i < count($pref); $i++) {

						if ($pref[$i] == 1) {
							$pref[$i] = translate_phrase('Mondays');
						}

						if ($pref[$i] == 2) {
							$pref[$i] = translate_phrase('Tuesdays');
						}

						if ($pref[$i] == 3) {
							$pref[$i] = translate_phrase('Wednesdays');
						}


						if ($pref[$i] == 4) {
							$pref[$i] = translate_phrase('Thursdays');
						}

						if ($pref[$i] == 5) {
							$pref[$i] = translate_phrase('Fridays');
						}

						if ($pref[$i] == 6) {
							$pref[$i] = translate_phrase('Saturdays');
						}

						if ($pref[$i] == 7) {
							$pref[$i] = translate_phrase('Sundays');
						}

						$pref1 .= $pref[$i] . ', ';
					}
					echo trim($pref1, ', ');
					?>
					</div>	
				</div>
				<?php endif;?>
			</div>
		</div>
		
		<?php if (isset($user_info['education_data'])): ?>
		<div class="background-part">
			<div class="background-head">
				<h1>
					<img src="<?php echo base_url() ?>assets/images/educ-icn.png"
						alt="" />
						<?php echo translate_phrase('Education') ?>
				</h1>
			</div>
			
			<?php foreach ($user_info['education_data'] as $data):?>
			<div class="univer-part">
				<div class="univer-detail">
				<?php
				$yearStartValue = $data['years_attended_start'];
				$yearEndValue   = $data['years_attended_end'];

				$year_start = '';
				if($yearStartValue !="0" || $yearEndValue !="0" ){
					if(!empty($yearStartValue) && !empty($yearEndValue))
					$year_start = ' - '.$yearStartValue." to ".$yearEndValue;
					else if(!empty ($yearEndValue))
					$year_start = ' - '.$yearEndValue;
				}
				?>
					<h2>
					<?php echo ucfirst($data['school_name']);//.$year_start; ?>
					<?php if($data['is_verified'] == '1'):?>
						<img src="<?php echo base_url() ?>assets/images/verified.png"
							class="mar-verify" alt="" />
							<?php endif;?>
					</h2>
					<div class="univer-list">
						<ul>
							<?php if ($data['degree_name'] != ''): ?>
								<li><?php echo ucfirst($data['degree_name']); ?></li>
							<?php endif; ?>
							
							<?php if ($data['school_id']): ?>
							<?php if (isset($data['school_data']['minor_subjects']) && $data['school_data']['minor_subjects']): ?>
							<li><?php echo translate_phrase('Major in') . ' ' ?> <?php foreach ($data['school_data']['minor_subjects'] as $subject): ?>
							<?php echo $subject['description'] . ' '; ?> <?php endforeach; ?>
							</li>
							<?php endif; ?>

							<?php if (isset($data['school_data']['major_subjects']) && $data['school_data']['major_subjects']): ?>
							<li><?php echo translate_phrase('Minor in' . ' ') ?> <?php foreach ($data['school_data']['major_subjects'] as $subject): ?>
							<?php echo $subject['description'] . ' '; ?> <?php endforeach; ?>
							</li>
							<?php endif; ?>
							<li><?php echo $data['school_data']['city_description'] ?>, <?php echo $data['school_data']['country_description'] ?>
							</li>
							<?php
							$duration = '';
							if ($data['years_attended_start'] && $data['years_attended_end'])
							$duration = $data['years_attended_start'] . ' ' . translate_phrase('to') . ' ' . $data['years_attended_end'];
							elseif (!$data['years_attended_start'] && $data['years_attended_end'])
							$duration = $data['years_attended_end'];
							elseif ($data['years_attended_start'] && !$data['years_attended_end'])
							$duration = $data['years_attended_start'] . ' ' . translate_phrase('to Present');
							if ($duration != ''):
							?>
							<li><?php echo $duration; ?></li>
							<?php endif; ?>
							<?php endif; ?>
						</ul>
					</div>
				</div>

				<div class="univer-logo">
				<?php if ($data['school_id'] && file_exists('school_logos/'.$data['school_data']['logo_url'])): ?>
					<img style="max-width: 100px; max-height: 100px;"
						src="<?php echo base_url().'school_logos/'.$data['school_data']['logo_url']; ?>"
						alt="" />
						<?php //else: ?>
					<!-- <img src="<?php echo base_url() . "assets/images/404.jpg"; ?>" width="105" height="32" alt="img" /> -->
						<?php endif; ?>
				</div>

			</div>
			<?php endforeach; ?>
			
		</div>
		<?php endif; ?>
		
		<?php if (isset($user_info['company_data']) && $user_info['company_data']): ?>
		<div class="background-part">
			<div class="background-head">
				<h1>
					<img src="<?php echo base_url() ?>assets/images/carrer-icn.png"
						alt="" class="htag-icn-pad" />
						<?php echo translate_phrase('Career') ?>
				</h1>
			</div>
			<?php foreach ($user_info['company_data'] as $career): ?>
			<?php $industry_shown = false;?>
			<div class="univer-part">
				<div class="univer-detail career-wid1">
					<h2>
					<?php if($career['show_company_name'] == '1'):?>
					<?php if ($career['company_id'] && isset($career['company_data'])): ?>
					<?php echo $career['company_data']['company_name']; ?>
					<?php else: ?>
					<?php echo $career['company_name']; ?>
					<?php endif; ?>
					<?php else:?>
					<?php $industry_shown = true;?>
						<i><?php echo translate_phrase('Company name hidden '); echo isset($career['industry_description'])?'('.$career['industry_description'].' '.translate_phrase('industry').')':''; ?>
						</i>
						<?php endif;?>

						<?php
							
						$year_end   = $career['years_worked_end'];
						if($career['years_worked_start']!="0" && $year_end !="" ){
							if($year_end == 9999)
							{
								$year_end = 'Present';
							}

							//echo '- '.$career['years_worked_start']." to ".$year_end;
						}
							
						?>
						<?php if ($career['is_verified']): ?>
						<img src="<?php echo base_url() ?>assets/images/verified.png"
							class="mar-verify" alt="" />
							<?php endif; ?>
					</h2>
					<div class="univer-list">
						<ul>
							<?php if ( $industry_shown == false && isset($career['industry_description'])): ?>
							<li><?php echo $career['industry_description']; ?></li>
							<?php endif; ?>

							<?php if ($career['job_function_id'] && isset($career['job_function_data'])): ?>
								<li><?php echo $career['job_function_data']['description']; ?></li>
							<?php endif; ?>
							
							<?php if($career['job_title']): ?>
								<li><?php echo $career['job_title']; ?></li>
							<?php endif; ?>

							<?php if ($career['job_city_id'] && isset($career['job_city_data'])): ?>
								<li><?php echo $career['job_city_data']['description']; ?></li>
							<?php elseif ($career['job_city_id'] > '0'): ?>
								<li><?php echo $career['job_city_name']; ?></li>
							<?php endif; ?>
							
							<?php
								$duration = '';
								if ($career['years_worked_start'] && $career['years_worked_end'] && $career['years_worked_end']!= '9999')
									$duration = $career['years_worked_start'] . ' ' . translate_phrase('to') . ' ' . $career['years_worked_end'];
								elseif (!$career['years_worked_start'] && $career['years_worked_end'] == '9999')
									$duration = translate_phrase('Present');
								elseif (!$career['years_worked_start'] && $career['years_worked_end'])
									$duration = $career['years_worked_end'];
								elseif ($career['years_worked_start'] && $career['years_worked_end']== '9999')
									$duration = $career['years_worked_start'] . ' ' . translate_phrase('to Present');
							if ($duration != ''):?>
								<li><?php echo $duration; ?></li>
							<?php endif; ?>
						</ul>
					</div>
				</div>

				<div class="univer-logo">
				<?php if ($career['show_company_name'] == '1' && $career['company_id'] && isset($career['company_data']) && file_exists('company_logos/'. $career['company_data']['logo_url'])): ?>
					<img style="max-width: 100px; max-height: 100px;"
						src="<?php echo base_url().'company_logos/'. $career['company_data']['logo_url'] ?>"
						alt="" />
						<?php endif; ?>

				</div>

			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		
		<?php if (isset($user_info['user_descriptive_words']) && $user_info['user_descriptive_words']): ?>
			<div class="background-part">
			<div class="background-head">
				<h1>
					<img src="<?php echo base_url() ?>assets/images/person-icn.png"
						alt="" />
						<?php echo translate_phrase('Personality') ?>
				</h1>
			</div>
			<div class="appear-prt personality-mar">
				<div class="appear-prt-but">
				<?php foreach ($user_info['user_descriptive_words'] as $value): ?>
					<div class="appr-button">
						<div class="appr-cen button-blue">
						<?php echo $value['dw_description'] ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>			
		</div>
		<?php endif; ?>
		
		<?php if(isset($user_info['selectedIdealMatchFilters']) && $user_info['selectedIdealMatchFilters']):?>		
		<!-- IDEA MATCH START -->
		<div class="background-part">
			<div class="background-head">
				<h1>
					<img src="<?php echo base_url() ?>assets/images/idol-match.png"
						alt="" />
						<?php echo translate_phrase('Ideal Match') ?>
				</h1>
			</div>

			<div class="self-sum-prt">
				<h3>
				<?php echo translate_phrase('Physical'); $show_message = true;?>
				</h3>

				<div class="physical-part-main">

				<?php if(array_search('age',$user_info['selectedIdealMatchFilters']) !== FALSE && $user_info['want_age_range_lower'] && $user_info['want_age_range_upper']): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Age') ?>
							:
						</div>
						<div class="physical-right">
						<?php echo translate_phrase('Between') . ' ' ?>
						<?php echo $user_info['want_age_range_lower'] . ' - ' . $user_info['want_age_range_upper']; ?>
							<span class="physical-blck-span"><?php if ($user_info['want_age_range_importance_description']) echo '(' . $user_info['want_age_range_importance_description'] . ')'; ?>
							</span>
						</div>
					</div>
					<?php $show_message = false;  endif;?>

					<?php if(array_search('height',$user_info['selectedIdealMatchFilters']) !== FALSE && $user_info['want_height_range_lower'] && $user_info['want_height_range_upper']): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Height range') ?>
							:
						</div>
						<?php if ($user_info['want_height_range_upper']): ?>
						<div class="physical-right">
						<?php echo translate_phrase('Between') . ' ' ?>
						<?php echo $user_info['want_height_range_lower'] . 'cm - ' . $user_info['want_height_range_upper'] . 'cm'; ?>
							<span class="physical-blck-span">(<?php echo $user_info['want_height_range_importance_description'] ?>)</span>
						</div>
						<?php else: ?>
						<div class="physical-right">
						<?php echo translate_phrase('Greater than') ?>
						<?php echo $user_info['want_height_range_lower']; ?>

							<span class="physical-blck-span"><?php echo $user_info['want_height_range_importance_description']?'('.$user_info['want_height_range_importance_description'].')':'' ?>
							</span>
						</div>
						<?php endif; ?>
					</div>
					<?php $show_message = false; endif; ?>

					<?php if(array_search('bodyType',$user_info['selectedIdealMatchFilters']) !== FALSE): ?>
					<?php if (isset($user_info['user_want_body_type']) && $user_info['user_want_body_type']): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Body types') ?>
							:
						</div>
						<div class="physical-right">
						<?php foreach ($user_info['user_want_body_type'] as $w_body_type): ?>
						<?php $tmp[] = $w_body_type['description'] ?>
						<?php endforeach; ?>
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						?>
						<?php echo ($user_info['want_body_type_importance_description'])? '<span class="physical-blck-span">('.$user_info['want_body_type_importance_description'].')</span>':'' ?>
						</div>
					</div>
					<?php endif; ?>
					<?php endif; ?>

					<?php if(array_search('looks',$user_info['selectedIdealMatchFilters']) !== FALSE): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Looks') ?>
							:
						</div>
						<div class="physical-right">
						<?php if ($user_info['want_looks_range_lower_id_description']) echo $user_info['want_looks_range_lower_id_description'].' to '; ?>
						<?php if ($user_info['want_looks_range_higher_id_description']) echo $user_info['want_looks_range_higher_id_description']; ?>
							<span class="physical-blck-span"><?php if ($user_info['want_height_range_importance_description']) echo ' (' . $user_info['want_looks_importance_description'] . ')' ?>
							</span>
						</div>
					</div>
					<?php $show_message = false; endif; ?>

					<?php if(array_search('ethnicity',$user_info['selectedIdealMatchFilters']) !== FALSE): ?>
					<?php if (isset($user_info['user_want_ethnicity']) && $user_info['user_want_ethnicity']): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Ethnicities') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php foreach ($user_info['user_want_ethnicity'] as $value): ?>
						<?php $tmp[] = $value['description'] ?>
						<?php endforeach; ?>
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_ethnicity_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_ethnicity_importance_description'] . ') </span>';
						?>
						</div>
					</div>
					<?php endif; ?>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_eye_color']) && $user_info['user_want_eye_color']): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Eye color') ?>
							:
						</div>
						<div class="physical-right">
						<?php foreach ($user_info['user_want_eye_color'] as $value): ?>
						<?php $tmp[] = $value['description'] ?>
						<?php endforeach; ?>
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_eye_color_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_eye_color_importance_description'] . ') </span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_hair_color']) && $user_info['user_want_hair_color']): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Hair color') ?>
							:
						</div>
						<div class="physical-right">
						<?php foreach ($user_info['user_want_hair_color'] as $value): ?>
						<?php $tmp[] = $value['description'] ?>
						<?php endforeach; ?>
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_hair_color_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_hair_color_importance_description'] . ') </span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_hair_length']) && $user_info['user_want_hair_length']): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Hair length') ?>
							:
						</div>
						<div class="physical-right">
						<?php foreach ($user_info['user_want_hair_length'] as $value): ?>
						<?php $tmp[] = $value['description'] ?>
						<?php endforeach; ?>
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_hair_length_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_hair_length_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_skin_tone']) && $user_info['user_want_skin_tone']): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Skin tone') ?>
							:
						</div>
						<div class="physical-right">
						<?php foreach ($user_info['user_want_skin_tone'] as $value): ?>
						<?php $tmp[] = $value['description'] ?>
						<?php endforeach; ?>
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_skin_tone_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_skin_tone_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_eyewear']) && $user_info['user_want_eyewear']): ?>
					<div class="physical-main">
						<div class="physical-left">
						<?php echo translate_phrase('Eyewear') ?>
							:
						</div>
						<div class="physical-right">
						<?php foreach ($user_info['user_want_eyewear'] as $value): ?>
						<?php $tmp[] = $value['description'] ?>
						<?php endforeach; ?>
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_eyewear_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_eyewear_importance_description'] . ') </span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if($show_message): ?>
					<div class="self-para">
					<?php echo translate_phrase('No physical requirements');?>
						.
					</div>
					<?php endif;?>
				</div>
			</div>
			<div class="self-sum-prt">
				<h3>
				<?php echo translate_phrase("Education"); $show_message = true; ?>
				</h3>
				<div class="physical-part-main">
				<?php
				if(array_search('education_level',$user_info['selectedIdealMatchFilters'])):?>
				<?php if (isset($user_info['user_want_education_level']) && $user_info['user_want_education_level']): ?>
				<?php foreach ($user_info['user_want_education_level'] as $value): ?>
				<?php $tmp[] = $value['description'] ?>
				<?php endforeach; ?>
					<div class="physical-main">
						<!--<div class="physical-left physical-width"><?php echo translate_phrase('Education completed or working towards') ?>:</div>-->
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Education completed or working towards') ?>
							:
						</div>

						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_education_level_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_education_level_importance_description'] . ') </span>';
						?>
						</div>
					</div>
					<?php endif; ?>
					<?php endif; ?>
					<?php  if(array_search('school_major',$user_info['selectedIdealMatchFilters'])):?>
					<?php if (isset($user_info['user_want_school_subject']) && $user_info['user_want_school_subject']): ?>
					<?php foreach ($user_info['user_want_school_subject'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Subject areas of study') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_school_subject_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_school_subject_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php endif; ?>
					<?php  if(array_search('school_name',$user_info['selectedIdealMatchFilters'])):?>
					<?php if (isset($user_info['user_want_school']) && $user_info['user_want_school']): ?>
					<?php foreach ($user_info['user_want_school'] as $value): ?>
					<?php $tmp[] = $value['school_name'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Preferred schools') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}

						if ($user_info['want_school_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_school_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>
					<?php endif;//main condition of this section ends?>
					<?php
					if($show_message): ?>
					<div class="self-para">
					<?php echo translate_phrase('No education requirements');?>
						.
					</div>
					<?php endif;?>
				</div>
			</div>

			<div class="self-sum-prt">
				<h3>
				<?php echo translate_phrase('Career'); $show_message = true;  ?>
				</h3>
				<div class="physical-part-main">
				<?php if(array_search('career_stage',$user_info['selectedIdealMatchFilters'])):?>
				<?php if (isset($user_info['user_want_career_stage']) && $user_info['user_want_career_stage']): ?>
				<?php foreach ($user_info['user_want_career_stage'] as $value): ?>
				<?php $tmp[] = $value['description'] ?>
				<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Career stage') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_career_stage_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_career_stage_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>
					<?php endif; ?>

					<?php  if(array_search('income_level',$user_info['selectedIdealMatchFilters'])):?>
					<?php if (isset($user_info['want_annual_income']) && $user_info['want_annual_income']):
					$show_message = false;
					?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Minimum annual income') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php echo $user_info['want_annual_income_currency_description'] . ' ' . number_format($user_info['want_annual_income']); ?>
						<?php
						if ($user_info['want_annual_income_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_annual_income_importance_description'] . ') </span>';
						?>
						</div>
					</div>
					<?php endif; ?>
					<?php endif; ?>

					<?php  if(array_search('job_function',$user_info['selectedIdealMatchFilters'])):?>
					<?php if (isset($user_info['user_want_job_function']) && $user_info['user_want_job_function']): ?>
					<?php foreach ($user_info['user_want_job_function'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Job function') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_job_function_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_job_function_importance_description'] . ') </span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php endif; ?>

					<?php  if(array_search('job_industry',$user_info['selectedIdealMatchFilters'])):?>
					<?php if (isset($user_info['user_want_industry']) && $user_info['user_want_industry']): ?>
					<?php foreach ($user_info['user_want_industry'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Company industry') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_industry_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_industry_importance_description'] . ') </span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php endif; ?>

					<?php  if(array_search('company_name',$user_info['selectedIdealMatchFilters'])):?>
					<?php if (isset($user_info['user_want_company']) && $user_info['user_want_company']): ?>
					<?php foreach ($user_info['user_want_company'] as $value): ?>
					<?php $tmp[] = $value['company_name'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Preferred companies') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_company_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_company_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>
					<?php endif;//condtion of career ends?>
					<?php if($show_message): ?>
					<div class="self-para">
					<?php echo translate_phrase('No career requirements');?>
						.
					</div>
					<?php endif;?>
				</div>
			</div>

			<div class="self-sum-prt">
				<div>
					<h3 style="float: left; width: auto;">
					<?php echo translate_phrase('Personality'); $show_message = true; ?>
					</h3>
					<span
						style="float: left; width: auto; line-height: 30px; margin-left: 10px;">
						<?php echo ($user_info['want_personality_importance_description'])? '<span class="physical-blck-span">('.$user_info['want_personality_importance_description'].')</span>':'' ?>
					</span>
				</div>

				<?php if(array_search('personality',$user_info['selectedIdealMatchFilters']) !== FALSE):?>
				<?php if (isset($user_info['user_want_descriptive_word']) && $user_info['user_want_descriptive_word']): ?>
				<div class="appear-prt personality-mar">
					<div class="appear-prt-but">
					<?php foreach ($user_info['user_want_descriptive_word'] as $value): ?>
						<div class="appr-button">
							<a href="javascript:;">
								<div class="appr-cen button-blue">
								<?php echo $value['description'] ?>
								</div> </a>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php $show_message=false;  endif; ?>
				<!-- 
				<div class="scroll-part-main">
                <?php if (isset($user_info['user_want_personality']) && $user_info['user_want_personality']): ?>
                    	<div class="scroll-part">
                            <div class="appr-button">        
                    			<?php foreach ($user_info['user_want_personality'] as $value): ?>
                        		<div class="appr-cen button-blue">   <?php echo $value['description'] ?></div>
                            	<?php endforeach; ?>
                        	</div>
                        </div>
                        
                         <div style="float:left;">
                        	<?php echo ($user_info['want_personality_importance_description'])? '<span class="physical-blck-span">('.$user_info['want_personality_importance_description'].')</span>':'NO Importance' ?>
						</div>
                        <?php $show_message=false; 
                  endif; ?>
                 </div>
				
				 -->

                  <?php endif;?>
                  <?php if($show_message): ?>
				<div class="scroll-part-main">
					<div class="self-para">
					<?php echo translate_phrase('No personality requirements');?>
						.
					</div>
				</div>
				<?php endif;?>

			</div>

			<div class="self-sum-prt">
			<?php
			//determine if user has selected any of the ideal match prefrences in below section.
			$prefrencesInOthersSection = array('relationshipStatus','residenceType','childPlans','existing_children','religion','smokingStatus','exerciseStatus','other');
			$isOthersSelected = false;
			foreach ($prefrencesInOthersSection as $key => $prefrence)
			{
				if(array_search($prefrence,$user_info['selectedIdealMatchFilters']))
				{
					$isOthersSelected = TRUE;
					break;
				}
			}
			?>

				<h3>
				<?php echo translate_phrase('Others');  $show_message = true; ?>
				</h3>
				<div class="physical-part-main">
				<?php if($isOthersSelected === TRUE):?>

				<?php if (isset($user_info['user_want_relationship_status']) && $user_info['user_want_relationship_status']): ?>
				<?php foreach ($user_info['user_want_relationship_status'] as $value): ?>
				<?php $tmp[] = $value['description'] ?>
				<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Relationship status') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_relationship_status_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_relationship_status_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>


					<?php if (isset($user_info['user_want_residence_type']) && $user_info['user_want_residence_type']): ?>
					<?php foreach ($user_info['user_want_residence_type'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Residence type') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_residence_type_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_residence_type_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_child_status']) && $user_info['user_want_child_status']): ?>
					<?php foreach ($user_info['user_want_child_status'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Children') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}

						if ($user_info['want_child_status_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_child_status_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_child_plan']) && $user_info['user_want_child_plan']): ?>
					<?php foreach ($user_info['user_want_child_plan'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Children plans') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_child_plan_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_child_plan_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_religious_belief']) && $user_info['user_want_religious_belief']): ?>
					<?php foreach ($user_info['user_want_religious_belief'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Religious beliefs') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_religious_belief_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_religious_belief_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_smoking_status']) && $user_info['user_want_smoking_status']): ?>
					<?php foreach ($user_info['user_want_smoking_status'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Smoking limit') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}

						if ($user_info['want_smoking_status_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_smoking_status_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>


					<?php if (isset($user_info['user_want_drinking_status']) && $user_info['user_want_drinking_status']): ?>
					<?php foreach ($user_info['user_want_drinking_status'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Drinking limit') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_drinking_status_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_drinking_status_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['user_want_exercise_frequency']) && $user_info['user_want_exercise_frequency']): ?>
					<?php foreach ($user_info['user_want_exercise_frequency'] as $value): ?>
					<?php $tmp[] = $value['description'] ?>
					<?php endforeach; ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Minimum excercise frequency') ?>
							:
						</div>
						<div class="physical-right physical-width3">
						<?php
						if ($tmp) {
							echo implode(', ', $tmp);
							unset($tmp);
							$show_message = false;
						}
						if ($user_info['want_exercise_frequency_importance_description'])
						echo '<span class="physical-blck-span"> (' . $user_info['want_exercise_frequency_importance_description'] . ')</span>';
						?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (isset($user_info['ideal_date']) && $user_info['ideal_date']): ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase('Other important things an ideal match should have') ?>
							:
						</div>
						<div class="physical-right physical-width3">
							"
							<?php echo $user_info['ideal_date'] ?>
							"
						</div>
					</div>

					<?php  $show_message = false; endif; ?>

					<?php if (isset($user_info['not_want_to_date']) && $user_info['not_want_to_date']): ?>
					<div class="physical-main">
						<div class="physical-left physical-width2">
						<?php echo translate_phrase("Absolutely don't want to date") ?>
							:
						</div>
						<div class="physical-right physical-width3">
							"
							<?php echo $user_info['not_want_to_date'] ?>
							"
						</div>
					</div>
					<?php  $show_message = false; endif; ?>

					<?php endif; ?>
					<?php if($show_message): ?>
					<div class="self-para">
					<?php echo translate_phrase('No other requirements');?>
						.
					</div>
					<?php endif;?>

				</div>

			</div>
		</div>
		<?php endif; //ideal match end?>
	</div>

</div>
<!--*********content close*********-->
</div>
