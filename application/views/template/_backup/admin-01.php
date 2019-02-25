<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- viewport meta to reset iPhone inital scale -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<!--[if lt IE 9]>
		<script src="js/css3-mediaqueries.js"></script>
	<![endif]-->
	<title> DateTix Admin Panel </title>
	<link rel="shortcut icon" href="<?php echo base_url()?>assets/images/favicon.ico" />
	<link href="<?php echo base_url()?>assets/css/stylesheet.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url()?>assets/css/developer.css" rel="stylesheet" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css' />
	<link href="<?php echo base_url()?>assets/css/media-query.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo base_url()?>assets/js/jquery-1.7.2.js"></script>
	<script type="text/javascript" src="<?php echo base_url()?>assets/js/scrollTo.js"></script>
	<!--<script src="<?php echo base_url()?>assets/js/general.js"></script>-->	
	<script type="text/javascript">
	var base_url = '<?php echo base_url() ?>';
	$(document).ready(function () {
	
		$(".dropdown-dt").find('dt a').live('click',function () {
			$(this).parent().parent().find('ul').toggle();
		});

		$(".dropdown-dt dd ul li a").live('click',function () {
			$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());

			$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'));
			$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
		});

		$('.multipleSelectTag ul li a').live('click',function(e) {

			e.preventDefault();
			var ele = jQuery(this);
			var li = ele.parent();
			var hiddenField = jQuery(li).parent().parent().find('input[type="hidden"]');
			var hiddenFieldValues    = $(hiddenField).val();
		  
				 
			if ($(ele).hasClass('appr-cen')) {
				var ids = new Array();
				ids = hiddenFieldValues.split(',');
				ids.splice( ids.indexOf(ele.attr('key')), 1);
				var newHiddenFieldValues = ids.join();
				jQuery(hiddenField).val(newHiddenFieldValues);
				$(ele).removeClass('appr-cen').addClass('disable-butn');              
			} else {
				var prefrencesId   = jQuery(hiddenField).val();
				if(prefrencesId !="")
					var dsc_id       = prefrencesId+','+ele.attr('key');
				else
					var dsc_id       = ele.attr('key');

				$(hiddenField).val(dsc_id);
				$(ele).addClass('appr-cen').removeClass('disable-butn');
				$(this).siblings(':input[type="text"]').val(next_year_date());
			}
	   });
		
		$('.chooseImportance ul li a').live('click',function(e) {
			var li = $(this).parent();	
			var parentUl = $(li).parent();		
			var selectedImportance = $(this).attr('importanceVal');
			
			if($(li).hasClass('Intro-Button'))
			{			
				$(parentUl).find('li').removeClass('Intro-Button-sel').addClass('Intro-Button');
				$(li).removeClass('Intro-Button').addClass('Intro-Button-sel');
			}		
			parentUl.parent().find('input[type="hidden"]').val(selectedImportance);
		});

		$(".cal_ico").live('click',function(){
			$(this).siblings(':input[type="text"]').val(next_year_date());
		});

		
		$(document).live('click', function (e) {
			var $clicked = $(e.target);
			if (!$clicked.parents().hasClass("dropdown-dt"))
				$(".dropdown-dt dd ul").hide();
		});
	});
	
	var isAjaxCallRunning = false;
	function loading(){
		isAjaxCallRunning = true;
		$('body').css('cursor', 'wait');
	}

	function validateEmail(email) { 
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	} 

	function stop_loading(){
		isAjaxCallRunning = false;
		// $('body').css('cursor', 'wait');
		 $('body').css('cursor', 'default');   
	}
	function openNewWindow(obj) {
		popupWin = window.open($(obj).attr('data-url'), 'open_window', 'menubar, toolbar, location, directories, status, scrollbars, resizable, dependent, width=640, height=480, left=0, top=0')
	}
	function capitaliseFirstLetter(string)
	{
		return string.charAt(0).toUpperCase() + string.slice(1);
	}
	function goToScroll(id)
	{
		if(pageMsg == '' )
		{
			$('body').scrollTo($('#'+id),800);
			$('#'+id).focus();
		}	
	}
</script>			
</head>
<body>
	<div class="main">
		<div class="hidden" id="facepile-banner">
			<div class="background-wrapper facepile-wrapper"></div>
			<?php if(user_country_id() != FB_RESTRICTED_COUNTRY):?>
			<div class="content-wrapper facepile-wrapper">
				<div class="banner-content">
					<!--<div class="left-banner"></div>-->
					<div class="right-banner">
						<div class="apply-privatly">
							<a href="#" onclick="fb_login();return false;"><img
								src="<?php echo base_url()?>assets/images/apply-privatly.png"
								alt="" /> </a>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<?php endif;?>
		</div>
		<!--*********Header start*********-->
		<div class="header-bg">
			<div class="L-wrapper">
				<div class="L-header-left">
					<!--<div class="L-logo-part">

						<div class="L-logo">
							<a href="<?php echo base_url()?>"
								title="DateTix"><img
								src="<?php echo base_url()?>assets/images/logo.png" name="logo"
								title="logo" alt="logo" /> </a>
						</div>
					</div>-->
				</div>
				<div class="L-header-right">
					<?php if(!$is_review_resricted):?>
						<h2 class="white-color">Admin - Super</h2>
					<?php else:?>
						<h2 class="white-color">Admin - <?php echo $this -> session -> userdata('superadmin_logged_in')['company_name'];?></h2>
					<?php endif;?>
					<div class="L-signIN-Text">
						<?php if($is_review_resricted):?>
						<a href="<?php echo base_url()?>admin/get-credits.html"><?php echo translate_phrase('You have ').$this -> session -> userdata('superadmin_logged_in')['credits']; echo $this -> session -> userdata('superadmin_logged_in')['credits'] == 1?translate_phrase(' credit left.'):translate_phrase(' credits left.'); ?></a>
						&nbsp;
						<?php endif;?>
						<a href="<?php echo base_url()?>admin/logout" ><?php echo translate_phrase('Logout') ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php $this->load->view($page_name); ?>
		<div class="footer-main">
			<div class="wrapper">
				<div class="footer-left">
					<div class="footer-social-link">
						<a href="http://www.facebook.com/datetix" title="Facebook"
							target="_blank"><img
							src="<?php echo base_url()?>assets/images/fb-like.jpg" alt="" />
						</a>
					</div>
					<!--<div class="footer-social-link"> <a href="#"><img src="<?php echo base_url()?>assets/images/twt-like.jpg" alt="" /></a></div>
	      				<div class="footer-social-link"> <a href="#"><img src="<?php echo base_url()?>assets/images/p-follow.jpg" alt="" /></a></div>
	      				<div class="footer-social-link"> <a href="#"> <img src="<?php echo base_url()?>assets/images/in-follow.jpg" alt="" /></a></div>-->
				</div>
				<div class="footer-right">
					<div class="footer-link">
						<ul id="footer-nav">
							<li><a target="_blank" href="<?php echo base_url()?>/" title="About Datetix"><?php echo translate_phrase('About') ?> </a></li>
							<li><a target="_blank" href="<?php echo base_url()?>/" title="Press"><?php echo translate_phrase('Press') ?></a></li>
							<li><a target="_blank" href="<?php echo base_url()?>/" title="Careers at Datetix"><?php echo translate_phrase('Careers') ?></a></li>
							<li><a target="_blank" href="<?php echo base_url()?>/" title="Terms Of Use "><?php echo translate_phrase('Terms Of Use') ?></a></li>
							<li><a target="_blank" href="<?php echo base_url()?>/" title="Privacy Policy"><?php echo translate_phrase('Privacy Policy') ?></a></li>
							<li class="bg-none"><a target="_blank" href="<?php echo base_url()?>/help.html" title="Help"><?php echo translate_phrase('Help') ?></a></li>
						</ul>
					</div>
					<div class="copy-right">
						<?php echo translate_phrase('Copyright © 2015 MatchLink Limited') ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
