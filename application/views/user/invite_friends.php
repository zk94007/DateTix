<?php $method = 0; ?>
<script
	src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url() ?>assets/js/general.js"></script>

<script
	src="http://connect.facebook.net/en_US/all.js"></script>
<div id="fb-root"></div>
<script>
// assume we are already logged in
FB.init({appId: '<?php echo $fb_app_id ?>', xfbml: true, cookie: true});
</script>

<!--*********content start*********-->
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="inviteContent">
				<h1><?php echo $page_title;?></h1>
				<?php if ($facebook_user):?>
				<p><?php echo translate_phrase('Invite more of your single friends to join '.get_assets('name','DateTix').' so that you will be able to ')?>
					<font class="DarkGreen-color"><b><?php echo translate_phrase('date friends of friends for free')?>
					</b> </font>
					<?php echo translate_phrase('(if you have at least 1 mutual friend with someone new that we introduce you to, you will be able to date him/her for free)')?>!
				</p>
				<div class="methodArea">
					<h2>
						<span class="methodHedPink"><?php echo translate_phrase('Method').'&nbsp;'.++$method; ?>
						</span>
						<?php echo ':&nbsp;' . translate_phrase('Invite your single friends on') ?>
						&nbsp;<img
							src="<?php echo base_url()?>assets/images/facebook-frnd.jpg" />
					</h2>
					<form action="#" method="post" id="fb_search">
						<div class="searchInput">
							<input name="friend_name" type="text"
								value="<?php echo isset($fb_search)?$fb_search:''?>" /> <input
								onclick="$('#fb_search').submit()" type="button" />
						</div>
					</form>
					<?php  $friendDivOpen = false;?>
					<div class="friendsArea">

					<?php if(isset($fb_friends) && count($fb_friends) > 0):?>
					<?php for ($i = 0; $i < 8 && $i < count($fb_friends); $i++) : ?>
					<?php  if($i % 2 == 0): $friendDivOpen = true;?>
						<div class="friendsRow">
						<?php endif;?>
							<div class="friendBox">

								<div class="friendsPhoto">
									<img
										src="https://graph.facebook.com/<?php echo $fb_friends[$i]['uid']; ?>/picture?width=145&height=159"
										width="145" height="159"
										alt="<?php echo $fb_friends[$i]['name'] ?>">
								</div>

								<div class="friendsRight">
									<div class="friendsName">
									<?php echo $fb_friends[$i]['name'] ?>
									</div>
									<div class="friendsCity">
									<?php echo isset($fb_friends[$i]['current_location']['name'])?$fb_friends[$i]['current_location']['name']:'' ?>
									</div>
									<a class="inviteBtn"
										onclick="facebook_send_message('<?php echo $fb_friends[$i]['uid'] ?>')"><?php echo translate_phrase('Invite') ?>
									</a> <label class="mobile-error input-hint error-msg"
										id="fb_<?php echo $fb_friends[$i]['uid'] ?>"></label>
								</div>
							</div>
							<?php if($i % 2 != 0 && $friendDivOpen): $friendDivOpen = false;?>
						</div>
						<?php endif;?>
						<?php endfor ?>

						<?php if($friendDivOpen): $friendDivOpen = false; ?>
					</div>
					<?php endif; //facebook user?>
					<?php else:?>

					<?php endif;//endcount?>
				</div>
				<?php endif; ?>
			</div>

			<div class="methodArea">
				<h2>
					<span class="methodHedPink"><?php echo translate_phrase('Method') .'&nbsp;' .++$method . '</span> :&nbsp;' . translate_phrase('Send an email invitation to your single friends') ?>
						<div class="gmailID" style="display: none;">
							<a href="#"><img
								src="<?php echo base_url()?>assets/images/gmail-icon.jpg" /> <?php echo translate_phrase('Connect to Gmail')?>
							</a>
						</div>
				
				</h2>
				<?php if(isset($list)){?>
				<div class="Thanks-verify">
					<span class="Th-highlight"><?php echo translate_phrase('The invitations were sent successfully to').'&nbsp;'; ?>
					<?php $i=1;foreach ($list as $val){?> <?php echo $val;?> <?php if($i==count($list)){}else{echo ",";}?>
					<?php $i++;}?> </span>
				</div>
				<?php }?>
				<?php
				echo form_open(url_city_name() .  '/invite-friends.html', array('id' => 'send-email-invite', 'name' => 'send-email-invite'));
				/*echo form_hidden(array(
				 'email_subject' => $email_subject,
				 'email_body'    => $email_body,
				 ));
					*/
				$msg_first_name = translate_phrase("Friend's first name");
				$msg_last_name  = translate_phrase("Friend's last name");
				$msg_email      = translate_phrase("Friend's email");
				?>

				<div class="inputArea">

				<?php for ($i = 0; $i < 5; $i++): ?>
					<div class="inputAreaRow">
						<div class="firstname">
							<input type="text" class="input-placeholder" value=""
								name="invitation[first_name][]"
								placeholder="<?php echo $msg_first_name;?>"> <label
								class="input-hint error"></label>
						</div>

						<div class="lastname">
							<input type="text" class="input-placeholder" value=""
								name="invitation[last_name][]"
								placeholder="<?php echo $msg_last_name;?>"> <label
								class="input-hint error"></label>
						</div>
						<div class="emailinput">
							<input type="text" class="input-placeholder MyEmailRule" value=""
								name="invitation[email][]"
								placeholder="<?php echo $msg_email;?>"> <label
								for="invitation[email][]" class="input-hint error"></label>
						</div>
					</div>
					<?php endfor;?>
				</div>
				<div class="invitationBottom">
					<button type="submit" class="sendBtn">
					<?php echo translate_phrase('Send Invitations') ?>
					</button>
					<div class="textLink">
						<a href="javascript:;" id="prevTemplate"><?php echo translate_phrase('Preview and Edit Your Invitation Email') ?>
						</a>
					</div>
				</div>

				<div id="edit-invitation-email" class="popup-box popupSmall"
					style="display: none;">
					<div class="cityTxt">
					<?php echo translate_phrase('Preview and Edit Your Invitation Email');?>
					</div>
					<br />
					<div class="div-row">
						<label class="text-label"><?php echo translate_phrase('Subject:') ?>
						</label> <input type="text" id="new_email_subject"
							name="email_subject" class="input-full80"
							value="<?php echo $email_subject?>" />
					</div>
					<div class="div-row">
						<label class="text-label"><?php echo translate_phrase('Message:') ?>
						</label>
						<textarea id="email_body" name="email_body" class="input-full80" style="height: 240px;"><?php echo $email_body;?></textarea>
					</div>

					<div class="btn-group right">
						<button type="button" class="btn btn-blue"
							onclick="update_template(1);">
							<?php echo translate_phrase('Update') ?>
						</button>
						<button type="button" class="btn btn-blue"
							onclick="update_template(0);">
							<?php echo translate_phrase('Cancel') ?>
						</button>
					</div>
				</div>
				<?php echo form_close(); ?>
			</div>
			<div class="methodArea">
				<h2>
					<span class="methodHedPink"><?php echo translate_phrase('Method ') .'&nbsp;' .++$method;?>
						:</span>
						<?php echo translate_phrase('Use Facebook or Twitter to Share your '.get_assets('name','DateTix').' invitation link with your friend(s) and followers:') ?>
				</h2>
				<div class="socialRow">
					<input type="text" name=""
						value="<?php echo base_url(url_city_name().'/?invite_id=' . $user_id);?>"
						id="invite_url">
					<div class="socialBtn">
						<a href="javascript:;" onclick="facebook_share();"><img
							src="<?php echo base_url()?>assets/images/friend-share.jpg" /> </a>

					</div>
					<div class="socialBtn">
						<a
							href="https://twitter.com/share?url=<?php echo base_url('/invite_id/' . $user_id);?>"
							target="_blank"><img
							src="<?php echo base_url()?>assets/images//friend-twiter.jpg" />
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<!--*********content close*********-->

<script type="text/javascript">

    $(document).ready(function() {
		$( "#prevTemplate" ).click(function() {
		  $( "#edit-invitation-email" ).toggle( "slow", function() {
		    // Animation complete.
		  });
		});
		
        $("#send-email-invite").submit(function(e) {
            var errors = 0;
            $(this).find('.inputAreaRow').each(function(i) {
            	var inputs = $(this).find('input');
                if (inputs.length > 0) {
                	$(inputs[2]).parent().find('.input-hint').html('');
                	
                    var user_input = 0;
                    var msg        = '';
                    
                    /*
                    if (inputs[0].value == '<?php echo $msg_first_name ?>') {
                        
                    	$(inputs[0]).parent().find('.input-hint').html('<?php echo translate_phrase("Invalid First Name") ?>');
                    	 e.preventDefault();
                         return false;
                         
                    }
                    else {
                        user_input++;
                    };
                    
                    if (inputs[1].value == '<?php echo $msg_last_name ?>') {
                    	$(inputs[1]).parent().find('.input-hint').html('<?php echo translate_phrase("Invalid Last Name") ?>');
                   	 e.preventDefault();
                        return false;
                    } else {
                        user_input++;
                    };
                    */
                    
                    if(inputs[2].value != '')
                    {    
                        if (validate_email(inputs[2].value) == false) {
                            $(inputs[2]).parent().find('.input-hint').html('<?php echo translate_phrase("Invalid Email") ?>');
                            e.preventDefault();
                            return false;
                            //msg += ', <?php echo translate_phrase('Invalid Email') ?>';
                        };
                    }    
                    if (user_input > 0 && user_input < 3) {
                        errors++;
                    };
                };
            });

            if (errors) {
                e.preventDefault();
            };
        });
    });
    function facebook_send_message(to) {

    	/*location.href = 'http://www.facebook.com/dialog/send?app_id=<?php echo $fb_app_id ?>&link=<?php echo base_url() ?>&to='+to+'&redirect_uri=<?php echo base_url() . url_city_name() ?>/invite-friends.html&display=touch';*/
    	if(isMobileView == 'No')
    	{
	        FB.ui({
		            method: 'send',
		        	name: 'Datetix',
		            link: '<?php echo base_url() ?>',
		            picture: '<?php echo base_url() ?>assets/images/datetix.png',
		            description:'<?php echo $fb_desc ?>',
		            to:to
	        	},
	        	function(response) {
	        		if (response && response.post_id) {
	        			console.log('Post was published.');
	        		} else {
	        			console.log('Post was not published.');
	        		}
				});
	        $("#fb_"+to).text(' ');
    	}
    	else
    	{
			$("#fb_"+to).text('<?php echo translate_phrase("The invite your friend feature is not yet supported on your mobile device. Please invite your friends using your desktop PC.")?>');
        }
    }
    function validate_email(value)
    {
    	 //var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var regex = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i;
        var email = jQuery.trim(value);
        return regex.test(email);

    }
    function update_template (is_update) {

    	/*
        if (is_update) {
            $("[name=email_subject]").val($("#new_email_subject").val());
            $("[name=email_body]").val($("#new_email_body").val());
        } else {
            $("#new_email_subject").val($("[name=email_subject]").val());
            $("#new_email_body").val($("[name=email_body]").val());
        }
		*/
        $( "#edit-invitation-email" ).toggle( "slow", function() {
		    // Animation complete.
		  });
    }
    function facebook_share () {
        FB.ui({
            method: 'feed',
            name: 'Datetix',
            caption: '<?php echo $email_subject ?>',
            link: '<?php echo base_url() ?>',
            picture: '<?php echo base_url() ?>images/datetix.png',
            description:'<?php echo $fb_desc ?>',
            redirect_uri: '<?php echo base_url() ?>'
        });
    }
</script>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
