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
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<script src="<?php echo base_url()?>assets/js/jquery-1.7.2.js"></script>
	<script type="text/javascript" src="<?php echo base_url()?>assets/js/jquery-ui-1.9.2.custom.js"></script>
	<script type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.fileupload.js"></script>
	<script type="text/javascript" src="<?php echo base_url()?>assets/js/scrollTo.js"></script>
	<script type="text/javascript">
	var base_url = '<?php echo base_url() ?>';
	var isMobileView = '<?php echo ($this->agent->is_mobile())?$this->agent->mobile():"No"?>';
	var mobileErrorMsg = '<span class="mobile-error input-hint error-msg"><?php echo translate_phrase("Upload photo is not yet supported on your mobile device. Please upload photos using your desktop PC after we approve your application.")?></span>';
	var invalidImgSize = '<?php echo translate_phrase("Image is too big")?>';
	var invalidImgType = '<?php echo translate_phrase("Not an accepted file type")?>';
	
	$(document).ready(function () {
	
		$(".dropdown-dt").find('dt a').live('click',function () {
			$(this).parent().parent().find('ul').toggle();
		});

		$(".dropdown-dt dd ul li a").live('click',function () {
			$(this).parent().parent().hide().parent().parent().find("dt a span").html($(this).html());

			$(this).parent().parent().parent().parent().find("dt :input[type='hidden']").val($(this).attr('key'));
			$(this).parent().parent().parent().parent().find("dt a").attr('key',$(this).attr('key'));
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
	<div class="main admin">
		<!--*********Header start*********-->
		<div class="header-bg">
			<div class="L-wrapper">
				<div class="L-header-left">
					<div class="L-logo-part">

						<div class="L-logo">
							<a href="<?php echo base_url($this->admin_url)?>"
								title="DateTix"><img
								src="<?php echo base_url()?>assets/images/logo.png" name="logo"
								title="logo" alt="logo" /> </a>
						</div>
					</div>
				</div>
				<div class="L-header-right">
					<h2 class="white-color">Welcome <span class="pink-colr"><?php $userdata = $this->session->userdata('event_admin_logged_in'); echo $userdata['login']?></span></h2>
					<a class="white-color" href="<?php echo base_url($this->admin_url.'/logout')?>" title="DateTix"><?php echo translate_phrase('Logout');?></a>
				</div>
			</div>
		</div>
		<?php $this->load->view($this->folder.'/'.$page_name); ?>
		<div class="footer-main">
			<div class="wrapper">
				<div class="footer-left">
					<div class="footer-social-link">
						<a href="http://www.facebook.com/datetix" title="Facebook"
							target="_blank"><img
							src="<?php echo base_url()?>assets/images/fb-like.jpg" alt="" />
						</a>
					</div>
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