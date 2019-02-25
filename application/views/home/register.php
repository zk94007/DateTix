
<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    
	//*************************  Login box  ***************************//
	$('#form-register').validate({
                    rules: { 
						email: {required: true, email:true},
						first_name: {required: true},
		           		last_name:{required: true},
						password:{ required: true},
						confirm_password:{required: true,equalTo: "#password"},
					},    
				    highlight: function(element) {
					    $(element).closest('.div-row').removeClass('success').addClass('error');
					},
                    success: function(element) {
                            element.closest('.div-row').removeClass('error').addClass('success');
                    }
				});
});
</script>
<?php
$return = $this->session->flashdata("returnErrorData");

$first_name = (isset($return['first_name']))?$return['first_name']:set_value('first_name');
$last_name = (isset($return['last_name']))?$return['last_name']:set_value('last_name');
$email = (isset($return['email']))?$return['email']:set_value('email');

?>
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">

			<form name="form-register" id="form-register"
				action="<?php echo base_url()?>home/start_register" method="post"
				autocomplete="off">
				<div class="popup-box popupSmall">
					
					<?php 
					if($free_rsvp_data = $this->session->userdata('post_data'))
					{
						$lable_txt = "RSVP";
					}
					else if(isset($event_info) && $event_info):
						$lable_txt = "RSVP";
					?>
						<h1><?php echo translate_phrase('RSVP for ') . $event_info['event_name'] . translate_phrase(' on ') . date(DATE_FORMATE,strtotime($event_info['event_start_time']));?></h1>
						<!--<div class="userBox-wrap fl">
							<div class="L-post-anyThing mar-top2"><?php echo translate_phrase("To get your matches at the event on ").date(DATE_FORMATE,strtotime($event_info['event_start_time'])).translate_phrase(', please tell us a bit about who you are and who you would like to meet.');?></div>
							<div class="L-post-anyThing"><?php echo translate_phrase("Already a ").get_assets('name','DateTix').translate_phrase(" member? ");?><a class="blu-color" href="<?php echo base_url().''.url_city_name() ?>/signin.html?highlight=1"  ><?php echo translate_phrase("Sign in to your account now");?></a><?php echo translate_phrase(" to skip this step")?>!</div>
						</div>-->
					<?php else:?>
						<?php $lable_txt = "Apply";?>
						<h1><?php echo translate_phrase('Apply for a Free ').get_assets('name','DateTix').translate_phrase(' Membership');?></h1>
					<?php endif;?>
					
					<div class="cityTxt"><?php echo $this->session->flashdata('dispMessage');?></div>
					<?php if(user_country_id() != FB_RESTRICTED_COUNTRY):?>					
					<div class="apply-privatly" style="padding-top: 10px">
						<a class="xl-fb-btn" href="javascript:;" onclick="fb_login();return false;"><img src="<?php echo base_url().'assets/images/fb-icn-big.jpg'?>" /><?php echo translate_phrase($lable_txt. ' quicker with <b>Facebook</b>') ?></a>
					</div>
					<div class="user-Top">
						<div class="L-post-anyThing"><span style="color: darkgreen;"><b>*<?php echo translate_phrase("Recommended");?>*</b></span> (<a class="blu-color" href="<?php echo base_url() . url_city_name().'/benifits-of-facebook.html' ?>"><?php echo translate_phrase("We won't post anything to your Facebook, but we want to make sure we don't intro you to your friends!") ?></a>)</div>
						<div class="last-bor"></div>
					</div>
					<?php endif;?>
					
					<?php if(!$this->input->get('highlight')):?>
						<div class="L-Find-Sub">
							<span><a href="<?php echo base_url() . url_city_name() ?>/apply.html?highlight=1&event_ticket_id=<?php echo isset($event_info['event_ticket_id'])?$this -> utility -> encode($event_info['event_ticket_id']):0?>&src=<?php echo $this->input->get('src')?>"><?php echo translate_phrase($lable_txt . ' Without Facebook')?></a></span>
						</div>
					<?php else:?>
						<div class="div-row">
						<h1>
							<span><?php echo translate_phrase($lable_txt . ' Without Facebook')?> </span>
						</h1>
						</div>
						<div class="div-row">
							<input type="text"
								placeholder="<?php echo translate_phrase('Your email address') ?>"
								class="input-full" name="email" id="email"
								value="<?php echo $email;?>"
								<?php echo ($this->input->get('highlight')) ? 'autofocus="autofocus"':'' ?>>
							<label class="input-hint error" for="email"><?php echo form_error('email')?>
							</label>
						</div>
	
						<div class="div-row">
							<div class="half-input-wrap">
								<input type="text" class="input-full"
									placeholder="<?php echo translate_phrase('Your first name') ?>"
									name="first_name" id="first_name"
									value="<?php echo $first_name;?>"> <label for="first_name"
									class="error input-hint"><?php echo form_error('first_name')?> </label>
							</div>
	
							<div class="half-input-wrap mar-left2">
								<input type="text" class="input-full"
									placeholder="<?php echo translate_phrase('Your last name') ?>"
									name="last_name" id="last_name" value="<?php echo $last_name;?>">
								<label for="last_name" class="input-hint error"><?php echo form_error('last_name')?>
								</label>
							</div>
	
							<!-- 
				    			<input type="text" placeholder="<?php echo translate_phrase('Your first name') ?>" class="input-haf" name="first_name" id="first_name" value="<?php echo $first_name;?>">
				    			<input type="text" placeholder="<?php echo translate_phrase('Your last name') ?>" class="input-haf mar-left2" name="last_name" id="last_name" value="<?php echo $last_name;?>">
				    			<label for="fullName" class="error input-hint"><?php echo form_error('first_name')?></label>
				    			 -->
						</div>
	
						<div class="div-row">
							<input type="password"
								placeholder="<?php echo translate_phrase('Enter password') ?>"
								class="input-full" name="password" id="password" value=""> <label
								class="input-hint error" for="password"><?php echo form_error('password')?>
							</label>
						</div>
	
						<div class="div-row">
							<input type="password"
								placeholder="<?php echo translate_phrase('Confirm password') ?>"
								class="input-full" name="confirm_password" id="confirm_password"
								value=""> <label class="input-hint error" for="confirm_password"><?php echo form_error('confirm_password')?>
							</label>
						</div>
	
						<div class="div-row">
							<div class="btn-group left">
								<input type="hidden" name="invite_id" id="invite_id"
									value="<?php echo $this->input->get('invite_id');?>"> <input
									type="submit" class="btn btn-pink"
									value="<?php echo $lable_txt?>" /> <input
									type="button" onclick="history.back();" class="btn btn-blue"
									value="<?php echo translate_phrase('Cancel') ?>" />
							</div>
						</div>
					<?php endif;?>
				</div>
			</form>
		</div>
	</div>
</div>
