<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- viewport meta to reset iPhone inital scale -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<!--[if lt IE 9]>
		<script src="js/css3-mediaqueries.js"></script>
	<![endif]-->
	<title> DateTix Broadcast Message</title>
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
		<!--*********Header start*********-->
		<div class="header-bg">
			<div class="L-wrapper">
				<div class="L-header-left">
					<div class="L-logo-part">

						<div class="L-logo">
							<a href="<?php echo base_url()?>"
								title="DateTix"><img
								src="<?php echo base_url()?>assets/images/logo.png" name="logo"
								title="logo" alt="logo" /> </a>
						</div>
					</div>
				</div>
				<div class="L-header-right">
					<h2 class="white-color">Broadcast Message</h2>
					<div class="L-signIN-Text">
						<a href="<?php echo base_url()?>broadcast/logout" ><?php echo translate_phrase('Logout') ?>
						</a>
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
							<li><a target="_blank" href="<?php echo base_url().url_city_name() ?>/about-us.html" title="About Datetix"><?php echo translate_phrase('About') ?> </a></li>
							<li><a target="_blank" href="<?php echo base_url().url_city_name() ?>/press.html" title="Press"><?php echo translate_phrase('Press') ?></a></li>
							<li><a target="_blank" href="<?php echo base_url().url_city_name() ?>/career.html" title="Careers at Datetix"><?php echo translate_phrase('Careers') ?></a></li>
							<li><a target="_blank" href="<?php echo base_url().url_city_name() ?>/terms.html" title="Terms Of Use "><?php echo translate_phrase('Terms Of Use') ?></a></li>
							<li><a target="_blank" href="<?php echo base_url().url_city_name() ?>/privacy.html" title="Privacy Policy"><?php echo translate_phrase('Privacy Policy') ?></a></li>
							<li class="bg-none"><a target="_blank" href="<?php echo base_url().url_city_name() ?>/help.html" title="Help"><?php echo translate_phrase('Help') ?></a></li>
						</ul>
					</div>
					<div class="copy-right">
						<?php echo translate_phrase('Copyright © 2014 DateTix Limited') ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
