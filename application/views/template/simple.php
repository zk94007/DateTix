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
<link href="<?php echo base_url()?>assets/css/stylesheet.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url()?>assets/css/developer.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css' />
<link href="<?php echo base_url()?>assets/css/media-query.css" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url()?>assets/js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/js/scrollTo.js"></script>

<meta property="og:title" content="<?php echo isset($cur_city_date->description)?$cur_city_date->description.translate_phrase("'s best online dating site").' |':'';?> DateTix" />
<meta property="og:image" content="<?php echo base_url()?>assets/images/logo.png" />
<meta property="og:site_name" content="DateTix" />
<meta property="og:description" content="<?php echo isset($cur_city_date->description)?$cur_city_date->description.translate_phrase("'s best online dating site").' |':'';?> DateTix" />

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-42340725-1', 'datetix.com');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>
</head>
<body>
	<div class="main">
		<?php $this->load->view($page_name); ?>		
	</div>
</body>
</html>