<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- viewport meta to reset iPhone inital scale -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0"/>

<!--[if lt IE 9]>
	<script src="js/css3-mediaqueries.js"></script>
<![endif]-->

<?php
$cur_city_date = $this->model_city->get($this->session->userdata('sess_city_id'),$this->session->userdata('sess_language_id'));
?>

<title><?php echo get_assets('name','DateTix');?> <?php echo isset($cur_city_date->description)?$cur_city_date->description.' | ':'';?>
<?php echo $page_title; ?></title>
<link rel="shortcut icon"
	href="<?php echo base_url()?>assets/images/favicon.ico" />
<link href="<?php echo base_url().get_assets('css_url','assets/css/stylesheet.css');?>" rel="stylesheet" type="text/css" />

<!-- <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'/> -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link href="<?php echo base_url()?>assets/css/media-query.css"
	rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/css/developer.css"
	rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/css/sidetogglemenu.css"
	rel="stylesheet" />
<script src="<?php echo base_url()?>assets/js/jquery-1.7.2.js"></script>
<script type="text/javascript"
	src="<?php echo base_url()?>assets/js/jquery.touchSwipe.js"></script>
<script type="text/javascript"
	src="<?php echo base_url()?>assets/js/jquery.raty.js"></script>
<script src="<?php echo base_url()?>assets/js/jquery.bxslider.min.js"></script>
<script type="text/javascript"
	src="<?php echo base_url()?>assets/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript"
	src="<?php echo base_url()?>assets/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/scrollTo.js"></script>
<script type="text/javascript">
var base_url = '<?php echo base_url() ?>';
var isMobileView = '<?php echo ($this->agent->is_mobile())?$this->agent->mobile():"No"?>';
var mobileErrorMsg = '<span class="mobile-error input-hint error-msg"><?php echo translate_phrase("Upload photo is not yet supported on your mobile device. Please upload photos using your desktop PC.")?></span>';
var invalidImgSize = '<?php echo translate_phrase("Image is too big")?>';
var invalidImgType = '<?php echo translate_phrase("Not an accepted file type")?>';

var isPenOpen = false;
var pageMsg = '';
$(document).ready(function () {
	 //Enable swiping...
    $(".sidetogglemenu").swipe( {
            //Generic swipe handler for all directions
            swipe:function(event, direction, distance, duration, fingerCount) {
            	$(".sidetogglemenu").css('z-index','-9999999');
        		$("#rightPen").css('position','relative')
        		isPenOpen = false;
        		$("#rightPen").animate({"left":"0px"});
            
            },
            //Default is 75px, set to 0 for demo so any distance triggers swipe
       threshold:0
    });

    //Enable swiping...
    $("#rightPen").swipe( {
            //Generic swipe handler for all directions
            swipeLeft:function(event, direction, distance, duration, fingerCount) {
            	$(".sidetogglemenu").css('z-index','-9999999');
        		$("#rightPen").css('position','relative')
        		isPenOpen = false;
        		$("#rightPen").animate({"left":"0px"});
            },
            //Default is 75px, set to 0 for demo so any distance triggers swipe
       threshold:0
    });
    
	
    
	$('#cur_usr_demand_cmp').raty({'path':'<?php echo base_url()?>assets/images/',readOnly    : true, <?php echo (isset($cur_usr_demand_cmp["score"]) && $cur_usr_demand_cmp["score"] ) ? " score : '".$cur_usr_demand_cmp["score"]/20 ."'" : ""?>});
	
	$('#view_usr_demand_cmp').raty({'path':'<?php echo base_url()?>assets/images/',readOnly    : true, <?php echo (isset($view_usr_demand_cmp["score"]) && $view_usr_demand_cmp["score"] ) ? " score :".$view_usr_demand_cmp["score"]/20 : ""?>});
	//$('form:first *:input[type!=hidden]:first').focus();
	
	
    //**** [START] COMMON DROPDOWN ANIMATION WITH VALUE RETRIVAL ***//
	$(".common-dropdown").find('dt a').live('click',function () {
    	$(this).parent().parent().find('ul').toggle();
    });

	//When select a option..
    $(".common-dropdown dd ul li a").live('click',function () {
		$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());

		$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'));
    	$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
	});

	
    $(document).live('click', function (e) {
   		var $clicked = $(e.target);
    	if (!$clicked.parents().hasClass("common-dropdown"))
        	$(".common-dropdown dd ul").hide();
    });
  //**** [END] COMMON DROPDOWN ANIMATION WITH VALUE RETRIVAL ***//
  
   function getSelectedValue(id) {
   		return $("#" + id).find("dt a span.value").html();
   }

	//Toggle By Rajnish
	$("#toggleRightPen").bind('click',function(){
		if(isPenOpen == false)
		{
			//OPEN ..
			isPenOpen = true;
			$("#rightPen").css('position','fixed')
			$("#rightPen").animate({"left":"208px"});
			$(".sidetogglemenu").css('z-index','100');
		}
		else
		{
			//Close OPEN ..
			$(".sidetogglemenu").css('z-index','-9999999');
			$("#rightPen").css('position','relative')
			isPenOpen = false;
			$("#rightPen").animate({"left":"0px"});
		}
	});
});
jQuery(window).resize(function(){
	$("#rightPen").css({'position': 'relative','left':'0px'});
	$(".sidetogglemenu").css({'z-index': '-999999'});
	
});
</script>
</head>
<body>
<?php

	$fb_app_id = $this->config->item('appId');?>
	<div id="fb-root"></div>
	<script>
	window.fbAsyncInit = function() {
    FB.init({
      appId      : '<?php echo $fb_app_id ?>', // App ID
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });

	FB.Event.subscribe('edge.create', function(response) {
		if(typeof(facebook_like_callback) == "function")
		{
			facebook_like_callback(1);
		}
	});
	
	FB.Event.subscribe('edge.remove', function(response) {
		if(typeof(facebook_like_callback) == "function")
		{
			facebook_like_callback(0);
		}
	});
    // Additional initialization code here
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));


	//Set Notifications
	$.ajax({ 
		url: '<?php echo base_url(); ?>' +"user/load_notification", 
		type:"post",
		dataType:'json',
		success: function (response) {
			console.log(response);
			if(response.active_intro_notification > 0)
			{
				$("#active_intro_notification").html($('<div class="notif-inner2"><div class="Notification-icn">'+response.active_intro_notification+'</div></div>').fadeOut())
			}
			if(response.active_dates_notification > 0)
			{
				$("#active_dates_notification").html($('<div class="notif-inner2"><div class="Notification-icn">'+response.active_dates_notification+'</div></div>').fadeOut())
			}
		}
	});
</script>

<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_UNLIMITED_DATES);
//Facebook Friends and Potential Matches
//$user_friends_with_datetix = $this->model_user->get_fb_friends_with_datetix($user_id);
$user_friends_with_datetix = array();
$potential_matches = 0;
if($user_friends_with_datetix)
{
	$all_friends = array();
	foreach ($user_friends_with_datetix as $friend)
	{
		$friends_of_friend = $this->model_user->get_fb_friends_with_datetix($friend['user_id']);
			
		if($all_friends)
		{
			$all_friends = array_map("unserialize", array_unique(array_map("serialize", array_merge($all_friends,$friends_of_friend))));
		}
		else
		{
			$all_friends = $friends_of_friend;
		}
	}
	$potential_matches = count($all_friends) - 1;
}
?>
	<div id="rightPen" class="main">
		<!--*********Header start*********-->
		<div class="header-bg">
			<div class="L-wrapper">
				<div class="Head-M-top">
					<div class="Head-leftside">
						<div class="L-logo-part">
							<div class="bBut">
								<button id="toggleRightPen" class="sideviewtoggle">
								<?php echo translate_phrase('Toggle') ?>
								</button>
							</div>
							<div class="Head-Logo">
								<a href="<?php echo base_url() . url_city_name() ;?>/my-intros.html">
									<img src="<?php echo get_assets('logo_url',base_url().'assets/images/logo.png');?>" name="logo" title="Go to <?php echo get_assets('name','DateTix');?>" alt="<?php echo get_assets('name','DateTix');?> Logo" />
								</a>
							</div>
						</div>
					</div>
					<div class="Head-rightside">
						<div class="NEWHead-nav">
							<div class="Head-nav">
								<ul>
									<li class="spc-non">
										<span id="active_intro_notification"></span>
										<a class="<?php echo $this->uri->segment('2') == 'my-intros.html'?'active':''?>"
										href="<?php echo base_url() . url_city_name() ?>/my-intros.html"><?php echo translate_phrase('My Intros') ?>
									</a>
									</li>
									<li>
										<a class="<?php echo $this->uri->segment('2') == 'edit-profile.html'?'active':''?>"
										href="<?php echo base_url() . url_city_name() ?>/edit-profile.html"><?php echo translate_phrase('My Profile') ?></a>
									</li>
									<!--<li><?php if($user_data['num_date_tix'] > 0):?>
									<span id="active_dates_notification"></span>
										<a class="<?php echo $this->uri->segment('2') == 'my-date.html'?'active':''?>"
										href="<?php echo base_url() . url_city_name() ?>/my-date.html"><?php echo translate_phrase('My Dates') ?></a>
									<?php else: ?>
									<li><a class="<?php echo $this->uri->segment('2') == 'get-more-tickets.html'?'active':''?>"
										href="<?php echo base_url() . url_city_name() ?>/get-more-tickets.html"><?php echo translate_phrase('My Dates') ?></a>
									<?php endif;?>
									</li>-->
								</ul>
							</div>
						</div>
						<div class="Move3-friend">
							<div class="Invite-Btn-area">
								<div class="Top-Button-Tittle" style="height: 32px;">
									
								<?php if($_SERVER['REMOTE_ADDR'] =='180.211.107.130' || $_SERVER['REMOTE_ADDR'] =='127.0.0.1'):?>
									<span> Total Time: {elapsed_time}</span>
								<?php endif;?>
								
								<?php if(user_country_id() != FB_RESTRICTED_COUNTRY):?>
								<?php if($user_friends_with_datetix):?>
									<a
										href="<?php echo base_url() . url_city_name() ?>/invite-friends.html"><?php echo count($user_friends_with_datetix). translate_phrase(' Facebook friends') ?>
									</a>
									<?php echo translate_phrase('connecting you to ').$potential_matches.translate_phrase(' potential matches') ?>
									<?php else:?>
									<?php if($user_data['facebook_id'] && 1==2):?>
									<?php echo translate_phrase('No Facebook Friends On '.get_assets('name','DateTix'))?>
									<?php else: ?>
									&nbsp;
									<?php endif;?>
								<?php endif;?>
								<?php endif;?>
								</div>

								<div class="Upg-ac-btn" style="position: relative; bottom: 0px;">
									<a href="<?php echo base_url() . url_city_name() ?>/edit-profile.html"><span class="Headrespon-but"><?php echo translate_phrase('Edit My Profile');?>
									<?php if($this->session->userdata('ad_id') == 12):?>
									<!--<a href="<?php echo 'mailto:?subject=Hello&amp;body=Hey! Have you tried '.get_assets('name','DateTix').'? Its a great way to meet new friends and potential dates. You can find out more details and sign up at http://www.datetix.hk/mnc';?>"><span class="Headrespon-but"><?php echo translate_phrase('Invite Friends');?>-->
									<?php else: ?>
									<!--<a href="<?php echo 'mailto:?subject=Hello&amp;body=Hey! Have you tried '.get_assets('name','DateTix').'? Its a great way to meet new friends and potential dates. You can find out more details and sign up at http://www.datetix.com';?>"><span class="Headrespon-but"><?php echo translate_phrase('Invite Friends');?>-->
									<!--<a href="<?php echo base_url() . url_city_name() ?>/invite-friends.html"><span class="Headrespon-but"><?php echo translate_phrase('Invite Friends');?>-->
									<?php endif;?>
									</span> </a>
								</div>
							</div>
						</div>
						<div class="Head-Top-Button">
							<div class="Top-Button022">
								<div class="Top-Button-Tittle">&nbsp;
								<!--<a href="<?php echo base_url() . url_city_name() ?>/get-more-tickets.html"><?php echo translate_phrase('You have ').$user_data['num_date_tix'].translate_phrase(' date '); echo $user_data['num_date_tix'] == 1?translate_phrase(' ticket left.'):translate_phrase(' tickets left.'); ?></a>-->
								</div>
								<?php if(get_assets('website_id','0') != 3):?>
								<a href="<?php echo base_url() . url_city_name() ?>/events.html"><div class="Headrespon-but"><?php echo translate_phrase('RSVP for Events') ?></div></a>
								<?php else: ?>
								<a href="<?php  echo base_url() . url_city_name().'/user_info/'.$this->utility->encode($user_data['user_id']);?>"><div class="Headrespon-but"><?php echo translate_phrase('View My Profile') ?></div></a>
								<?php endif;?>
									<!--<a
										href="<?php echo base_url() . url_city_name() ?>/get-more-tickets.html"><?php echo translate_phrase('Get More Date Tickets ') ?>
									</a>-->								
							</div>

							<div class="yellow-btn">
								<a
									href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo $is_premius_member?translate_phrase('Upgrade Account'):translate_phrase('Upgrade Account')?>
								</a>
							</div>
						</div>
						<div class="Head-Right-part">
							<div class="Head-Updates">
								<dl id="sample" class="Johndownfilter common-dropdown">
									<dt>
										<a id="Johndropdown"><span><?php echo isset($user_data['first_name'])? $user_data['first_name'].' '.$user_data['last_name'] :'Not Login';?>
										</span> </a>
									</dt>
									<?php if(isset($user_data)):?>
									<dd>
										<ul id="Johnul">
											<li><a
												href="<?php echo base_url() . url_city_name() ?>/edit-profile.html"><img
													src="<?php echo base_url()?>assets/images/edit-icn.png"
													alt="" /> <?php echo translate_phrase('Edit My Profile') ?>
											</a></li>
											<li><a
												href="<?php  echo base_url() . url_city_name().'/user_info/'.$this->utility->encode($user_data['user_id']);?>"><img
													src="<?php echo base_url()?>assets/images/view-icn.png"
													alt="" /> <?php echo translate_phrase('View My Profile') ?>
											</a></li>
											<li><a
												href="<?php echo base_url() . url_city_name() ?>/ideal-match.html"><img
													src="<?php echo base_url()?>assets/images/ideal-icn.png"
													alt="" /> <?php echo translate_phrase('Edit My Ideal Match') ?>
											</a></li>
											<li><a
												href="<?php echo base_url() . url_city_name() ?>/setting.html"><img
													src="<?php echo base_url()?>assets/images/setting-icn.png"
													alt="" /> <?php echo translate_phrase('Settings') ?> </a></li>
											<li><a href="<?php echo base_url() . 'user/logout';?>"><img
													src="<?php echo base_url()?>assets/images/signout-icn.png"
													alt="" /> <?php echo translate_phrase('Sign Out') ?> </a></li>
										</ul>
									</dd>
									<?php endif;?>
								</dl>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--*********Header close*********-->
		<?php $this->load->view($page_name); ?>
		<!--*********Footer start*********-->
		<div class="footer-main">
			<div class="wrapper">
				<div class="footer-left">
					<div class="language-part">
						<?php if($language_data = language_bar()):?>
						<span class="sel-lang"><?php echo translate_phrase('Select language:');?></span>
						<?php echo $language_data;?>
						<?php endif;?>						
					</div>
					<?php if(user_country_id() != FB_RESTRICTED_COUNTRY):?>
					<div class="footer-social-link">
						<?php if(get_assets('website_id','0') != 3):?>
						<a href="http://www.facebook.com/datetix" title="Facebook" target="_blank"><img src="<?php echo base_url()?>assets/images/fb-like.jpg" alt="" /></a>												
						<?php else:?>
						<a href="http://www.facebook.com/SmartimeMatching" title="Facebook" target="_blank"><img src="<?php echo base_url()?>assets/images/fb-like.jpg" alt="" /></a>
						<?php endif;?>
					</div>
					<?php endif;?>
					<!--<div class="footer-social-link">
						<a href="#"><img
							src="<?php echo base_url()?>assets/images/twt-like.jpg" alt="" />
						</a>
					</div>
					<div class="footer-social-link">
						<a href="#"><img
							src="<?php echo base_url()?>assets/images/p-follow.jpg" alt="" />
						</a>
					</div>
					<div class="footer-social-link">
						<a href="#"> <img
							src="<?php echo base_url()?>assets/images/in-follow.jpg" alt="" />
						</a>
					</div>-->
				</div>
				<div class="footer-right">
					<?php if(get_assets('website_id','0') != 3):?>
					<div class="footer-link">
						<ul id="footer-nav">
							<li><a href="<?php echo base_url().url_city_name() ?>/about-us.html" title="About Datetix"><?php echo translate_phrase('About') ?> </a></li>
							<li><a href="<?php echo base_url().url_city_name() ?>/press.html" title="Press"><?php echo translate_phrase('Press') ?></a></li>
							<li><a href="<?php echo base_url().url_city_name() ?>/career.html" title="Careers at Datetix"><?php echo translate_phrase('Careers') ?></a></li>
							<li><a href="<?php echo base_url().url_city_name() ?>/terms.html" title="Terms Of Use "><?php echo translate_phrase('Terms') ?></a></li>
							<li><a href="<?php echo base_url().url_city_name() ?>/privacy.html" title="Privacy Policy"><?php echo translate_phrase('Privacy') ?></a></li>
							<li class="bg-none"><a href="<?php echo base_url().url_city_name() ?>/help.html" title="Help"><?php echo translate_phrase('Help') ?></a></li>
						</ul>
					</div>
					<div class="copy-right">
					<?php echo translate_phrase('Copyright © 2015 DateTix Limited') ?>
					</div>
					<?php endif;?>
				</div>
				
				<script>
				  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
				
				  ga('create', 'UA-42340725-1', 'datetix.com');
				  ga('require', 'displayfeatures');
				  ga('send', 'pageview');
				
				</script>
				
				<!-- Start of Async HubSpot Analytics Code -->
				  <script type="text/javascript">
				    (function(d,s,i,r) {
				      if (d.getElementById(i)){return;}
				      var n=d.createElement(s),e=d.getElementsByTagName(s)[0];
				      n.id=i;n.src='//js.hs-analytics.net/analytics/'+(Math.ceil(new Date()/r)*r)+'/458759.js';
				      e.parentNode.insertBefore(n, e);
				    })(document,"script","hs-analytics",300000);
				  </script>
				<!-- End of Async HubSpot Analytics Code -->
				
			</div>
		</div>
		<!--*********Footer close*********-->
	</div>
	<div class="sidetogglemenu">
		<div class="msg-main">
			<div class="Invite-Btn-area">
				<div class="Top-Button-Tittle">
				<?php if(user_country_id() != FB_RESTRICTED_COUNTRY):?>
				<?php if($user_friends_with_datetix):?>
					<div class="Top-Button-Tittle">
						<a
							href="<?php echo base_url() . url_city_name() ?>/invite-friends.html"><?php echo count($user_friends_with_datetix). translate_phrase(' Facebook friends') ?>
						</a>
						<?php echo translate_phrase('connecting you to ').$potential_matches.translate_phrase(' potential matches') ?>
					</div>
					<?php else:?>
					<?php if($user_data['facebook_id'] && 1==2):?>
					<?php echo translate_phrase('No Facebook Friends On '.get_assets('name','DateTix').'')?>
					<?php endif;?>
					<?php endif;?>
					<?php endif;?>
				</div>

				<div class="Upg-ac-btn">
					<?php if($this->session->userdata('ad_id') == 12):?>
					<a href="<?php echo 'mailto:?subject=Hello&amp;body=Hey! Have you tried '.get_assets('name','DateTix').'? Its a great way to meet new friends and potential dates. You can find out more details and sign up at http://www.datetix.hk/mnc';?>"><span class="Headrespon-but"><?php echo translate_phrase('Invite Friends');?>
					<?php else: ?>
					<a href="<?php echo 'mailto:?subject=Hello&amp;body=Hey! Have you tried '.get_assets('name','DateTix').'? Its a great way to meet new friends and potential dates. You can find out more details and sign up at http://www.datetix.com';?>"><span class="Headrespon-but"><?php echo translate_phrase('Invite Friends');?>
					<!--<a href="<?php echo base_url() . url_city_name() ?>/invite-friends.html"><span class="Headrespon-but"><?php echo translate_phrase('Invite Friends');?>-->
					<?php endif;?>
					</span> </a>
				</div>
			</div>
			
			<div class="Top-Button022">
				<div class="Top-Button-Tittle">
				<!--<a href="<?php echo base_url() . url_city_name() ?>/get-more-tickets.html"><?php echo translate_phrase('You have ').$user_data['num_date_tix'].translate_phrase(' date '); echo $user_data['num_date_tix'] == 1?translate_phrase(' ticket left.'):translate_phrase(' tickets left.'); ?></a>-->
				</div>
				<?php if(get_assets('website_id','0') != 3):?>
				<a href="<?php echo base_url() . url_city_name() ?>/events.html"><div class="Headrespon-but"><?php echo translate_phrase('RSVP for Events') ?></div></a>
				<?php else: ?>
				<a href="<?php  echo base_url() . url_city_name().'/user_info/'.$this->utility->encode($user_data['user_id']);?>"><div class="Headrespon-but"><?php echo translate_phrase('View My Profile') ?></div></a>
				<?php endif;?>
			</div>
		</div>
		<div class="yellow-btn">
			<a
				href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html"><?php echo $is_premius_member?translate_phrase('Upgrade Account'):translate_phrase('Upgrade Account')?>
			</a>
		</div>
		<div class="toogle-listM">
			<?php if(isset($user_data)):?>
			<ul>
				<li><a
					href="<?php echo base_url() . url_city_name() ?>/edit-profile.html"><img
						src="<?php echo base_url()?>assets/images/edit-icn.png" alt="" />
						<?php echo translate_phrase('Edit My Profile') ?> </a></li>
				<li><a
					href="<?php  echo base_url() . url_city_name().'/user_info/'.$this->utility->encode($user_data['user_id']);?>"><img
						src="<?php echo base_url()?>assets/images/view-icn.png" alt="" />
						<?php echo translate_phrase('View My Profile') ?> </a></li>
				<li><a
					href="<?php echo base_url() . url_city_name() ?>/ideal-match.html"><img
						src="<?php echo base_url()?>assets/images/ideal-icn.png" alt="" />
						<?php echo translate_phrase('Edit My Ideal Match') ?> </a></li>
				<li><a
					href="<?php echo base_url() . url_city_name() ?>/setting.html"><img
						src="<?php echo base_url()?>assets/images/setting-icn.png" alt="" />
						<?php echo translate_phrase('Settings') ?> </a></li>
				<li><a href="<?php echo base_url() . 'user/logout';?>"><img
						src="<?php echo base_url()?>assets/images/signout-icn.png" alt="" />
						<?php echo translate_phrase('Sign Out') ?> </a></li>
			</ul>
			<?php endif;?>
		</div>
	</div>
</body>
</html>
