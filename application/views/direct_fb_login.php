<script src="<?php echo base_url()?>assets/js/jquery-1.7.2.js"></script>
<?php $fb_app_id = $this->config->item('appId');?>
<div id="fb-root"></div>
<script>
	var base_url = '<?php echo base_url()?>';
	window.fbAsyncInit = function() {
    FB.init({
      appId      : '<?php echo $fb_app_id ?>', // App ID
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
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
</script>