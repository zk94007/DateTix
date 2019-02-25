<script>

var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
//Additional JS functions here

// Initiate Facebook JS SDK
window.fbAsyncInit = function() {
	FB.init({
		appId   : '<?php echo $this->config->item('appId'); ?>', // Your app id
		cookie  : true,  // enable cookies to allow the server to access the session
		xfbml   : false,  // disable xfbml improves the page load time
		version : 'v2.3' // use version 2.3
	});
	
	FB.getLoginStatus(function(response) {
		console.log('getLoginStatus', response);
		
	});
};
// Get login status
function loginCheck()
{
	FB.getLoginStatus(function(response) {
		direct_to_signup();
	});
}
// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
function getUser()
{
	FB.api('/me', function(response) {
		console.log('getUser', response);
	});
}


	
(function(d, s, id){
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
	

function direct_to_signup () {
	var redirect_to ='';
	$('body').css({'cursor':'wait'})
	
	$.get(base_url +'fb_login/facebook/',
		
		{'invite_id': '<?php  echo isset($invite_id)?$invite_id:"" ?>' }, 
		function(data) {
			
			$('body').css({'cursor':'default'})
			
	        if(typeof(data.url) != 'undefined' && data.url !="")
	        {
	          redirect_to = data.url;  
	        }
	        else
	        {
	            if(data.user_exsist == -1)
	            {
					//alert(data.msg);
					redirect_to = '<?php echo base_url().'fb_login/login_with_email' ?>';
	            }
	        	else if (data.user_exsist == 0) {
	              redirect_to = '<?php echo base_url() . url_city_name() ?>/signup-step-1.html';
	            } 
	            else
	            {   
	                if (data.step == 0) {
	                  redirect_to = '<?php echo base_url() . url_city_name() ?>/signup-step-1.html';
	                } 
	                else
	                {	 
	                    redirect_to = '<?php echo base_url()?>user/check_signup';
	                }
	            };
	        }
	       window.location.assign(redirect_to);
	    },
	'json');
}

$(document).ready(function(){
	// Trigger login
	$('.btn-fbLogin').click(function() {
		
		if( iOS ){
			direct_to_signup();
		}
		else
		{
			FB.getLoginStatus(function(response) {
				
				if (response.status === 'connected')
				{
					direct_to_signup();
				}
				else if (response.status === 'not_authorized')
				{
					// User logged into facebook, but not to our app.
				}
				else
				{
					FB.login(function(){
						loginCheck();
					}, {scope: '<?php echo $this->config->item('fb_scope'); ?>'});	
					
				}
			});
			
		}
		
	});
})
</script>
