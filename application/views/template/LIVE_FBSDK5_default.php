<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- viewport meta to reset iPhone inital scale -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0"/>

<!--[if lt IE 9]>
	<script src="js/css3-mediaqueries.js"></script>
<![endif]-->
<?php $cur_city_date = $this->model_city->get($this->session->userdata('sess_city_id'),$this->session->userdata('sess_language_id')); ?>
<title>
<?php if(isset($page_name) && isset($event_info)):?>
	<?php echo $page_title;?>
<?php else:?>
<?php echo isset($cur_city_date->description)?$cur_city_date->description.translate_phrase("'s best way to meet new friends and potential dates").' |':'';?> DateTix
<?php endif;?>
</title>
	
<link rel="shortcut icon" href="<?php echo base_url()?>assets/images/favicon.ico" />
<link href="<?php echo base_url().get_assets('css_url','assets/css/stylesheet.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/css/developer.css" rel="stylesheet" type="text/css" />

<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css' />
<link href="<?php echo base_url()?>assets/css/media-query.css" rel="stylesheet" type="text/css" />

<script src="<?php echo base_url()?>assets/js/jquery-1.7.2.js"></script>
<script type="text/javascript"
	src="<?php echo base_url()?>assets/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript"
	src="<?php echo base_url()?>assets/js/jquery.fileupload.js"></script>
<script type="text/javascript"
	src="<?php echo base_url()?>assets/js/scrollTo.js"></script>

<meta property="og:title"
	content="<?php echo isset($cur_city_date->description)?$cur_city_date->description.translate_phrase("'s best online dating site").' |':'';?> DateTix" />
<meta property="og:image"
	content="<?php echo base_url()?>assets/images/logo.png" />
<meta property="og:site_name" content="DateTix" />
<meta property="og:description"
	content="<?php echo isset($cur_city_date->description)?$cur_city_date->description.translate_phrase("'s best online dating site").' |':'';?> DateTix" />
<meta property="qc:admins" content="255524164454106375" />
<meta property="wb:webmaster" content="d51f8633c4330191" />

<!-- <script src="http://connect.facebook.net/en_US/all.js"></script> -->
<script>
 	var base_url = '<?php echo base_url() ?>';
 	var pageMsg = '';
  	// Additional JS functions here
	
	
	var isMobileView = '<?php echo ($this->agent->is_mobile())?$this->agent->mobile():"No"?>';
	var mobileErrorMsg = '<span class="mobile-error input-hint error-msg"><?php echo translate_phrase("Upload photo is not yet supported on your mobile device. Please upload photos using your desktop PC after we approve your application.")?></span>';
	var invalidImgSize = '<?php echo translate_phrase("Image is too big")?>';
	var invalidImgType = '<?php echo translate_phrase("Not an accepted file type")?>';
	$(document).ready(function () {
	  	//$('form:first *:input[type!=hidden]:first').focus();
		//$("#school_name").focus();
		//$("#company_name").focus();
	});   
</script>
			
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

</head>
<body>

<?php $this->load->view('fb_script');?>

<?php $language_data = language_bar();?>	
	<div class="main">
		<div class="hidden" id="facepile-banner">
			<div class="background-wrapper facepile-wrapper"></div>
			<?php if(user_country_id() != FB_RESTRICTED_COUNTRY):?>
			<div class="content-wrapper facepile-wrapper">
				<div class="banner-content">
					<!--<div class="left-banner"></div>
					<div class="right-banner"></div>-->
					<div class="apply-privatly">
							<a class="xl-fb-btn" href="javascript:;" onclick="fb_login();return false;">
								<img src="<?php echo base_url().'assets/images/fb-icn-big.jpg'?>" />
								<?php echo translate_phrase('Apply quicker with') ?> <b>Facebook</b>
							</a>
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
					<div class="L-logo-part">
						<div class="L-logo">
							<a href="<?php echo base_url();?>">
								<img src="<?php echo get_assets('logo_url',base_url().'assets/images/logo.png');?>" name="logo" title="Go to <?php echo get_assets('name','Datetix');?>" alt="<?php echo get_assets('name','Datetix');?> Logo" />
							</a>
						</div>
						<div class="header-lng">
							<div class="L-change-location">
								<div class="loc-name"><!--<?php echo get_current_city() ?>--></div>
								<div class="rest-loc">
									<!--<a href="<?php echo base_url() . url_city_name() ?>/change-city.html?return_to=<?php echo return_url() ?>" title="<?php echo translate_phrase('Change City') ?>" id="POPUP_city_change">Change</a>-->
								</div>
							</div>
							<div class="language-part">
								<?php if($language_data):?>
								<?php echo $language_data;?>
								<?php endif;?>
							</div>
						</div>						
					</div>
				</div>
				<div class="L-header-right">
					<?php if(user_country_id() != FB_RESTRICTED_COUNTRY):?>
					<div class="L-sign-inFB">
						<a href="javascript:;" class="btn-fbLogin facebook-image">
							<img src="<?php echo base_url()?>assets/images/sign-in-fb.jpg" alt="fb_login"/>
						</a>
					</div>
					<?php endif;?>

					<div class="L-signIN-Text">
						<a
							href="<?php echo base_url() . url_city_name() ?>/signin.html?highlight=1"
							id="POPUP_signin_email"
							title="<?php echo translate_phrase('Sign In Using Email') ?>"><?php echo translate_phrase('Sign In Using Email') ?>
						</a>
					</div>
				</div>
			</div>
		</div>
		
		<?php $this->load->view($page_name); ?>
		
		<div class="footer-main">
			<div class="wrapper">
				<div class="footer-left">
					<div class="language-part">
						<?php if($language_data):?>
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
						</a>
					</div>
					<?php endif;?>
					<!--<div class="footer-social-link"> <a href="#"><img src="<?php echo base_url()?>assets/images/twt-like.jpg" alt="" /></a></div>
	      <div class="footer-social-link"> <a href="#"><img src="<?php echo base_url()?>assets/images/p-follow.jpg" alt="" /></a></div>
	      <div class="footer-social-link"> <a href="#"> <img src="<?php echo base_url()?>assets/images/in-follow.jpg" alt="" /></a></div>-->
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
			</div>
		</div>
	</div>
</body>
</html>
