<div class="wrapper">
	<div class="content-part">
		<div class="Apply-p-Main">
			<!--<div class="A-step-partM">
				<div class="step-backBG">
					<div class="step-BOX-Main">
						<div class="step-bg-Unselected">
							<span>1</span>
						</div>
						<div class="step-ttle">
						<?php echo translate_phrase('Describe Yourself')?>
						</div>
					</div>
					<div class="step-BOX-Main mar-auto">
						<div class="step-bg-Unselected">
							<span>2</span>
						</div>
						<div class="step-ttle">
						<?php echo translate_phrase('Your Dating Preferences')?>
						</div>
					</div>
					<div class="step-BOX-Main fr wh-clr">
						<div class="step-bg-selected">
							<span>3</span>
						</div>
						<div class="step-ttle">
						<?php echo translate_phrase('Submit Application')?>
						</div>
					</div>
				</div>
			</div>-->
			<div class="Ap-con">
				<!--<p class="self-para">Thanks for taking the time to apply! Your application has been submitted to our review team. We will be in touch if we have any questions on your application. </p>
        <p class="self-para">Due to extremely high application volume, the current estimated wait time for us to reach a decision on your application is:<span class="Ap-bold"> 3 to 5 working days.</span> </p>-->

			<?php
			$msg       = 'Due to extremely high application volume,the current estimated wait time for us to reach a decision on your application is: <b>3 to 5 working days</b>.';
			$third_msg = 'While you are waiting, invite more of your single friends to join '.get_assets('name','DateTix').' so that you will be able to <font class="DarkGreen-color"><b>date friends of friends for free</b></font> (if you have at least 1 mutual friend with someone new that we introduce you to, you will be able to date him/her for free)!';
			//$third_msg = 'While you are waiting, please invite your single friends to also apply for a free '.get_assets('name','DateTix').' membership. <b>Enjoy 1 year of premium DateTix membership for free (HDK 2,160 value)</b> if you can invite 3 eligible single friends who we approve as members!';
			?>
				<?php if(isset($event_info)):?>
				<p class="self-para DarkGreen-color">
					<?php //echo translate_phrase('Thanks for letting us know you are coming. We will now go and find the best matches for you among all people attending the event! <b>You will receive your match list both by email prior to the event and by paper at the actual event.</b> We look forward to seeing you on ').date(DATE_FORMATE,strtotime($event_info['event_start_time']));?>
					<?php echo translate_phrase('You have successfully RSVPed for the event! We look forward to seeing you. You may now buy a discounted ticket by clicking the Buy Discounted Member Ticket button. Invite your friends to the event by clicking the Invite Friends button.');?>
				</p>
				<?php else:?>
				
				<!--<p class="self-para bold-line DarkGreen-color">
					Your DateTix ID is <strong><?php echo $this -> session -> userdata('user_id');?></strong>
				</p>-->
				<p class="self-para">
				<?php echo translate_phrase('Thanks for taking the time to apply! Your application has been submitted to our review team. We will be in touch if we have any question in your application.');?>
				</p>
				<p class="self-para">
				<?php echo translate_phrase($msg);?>
				</p>
				<p class="self-para">
				<?php echo translate_phrase($third_msg);?>
				</p>
				<?php endif;?>
                                
                                <?php if(!empty($from_url)):?>
                                    <div class="Ap-Invit-main">
                                            <a
                                                href="<?php echo $from_url;?>">
                                                <div class="appr-cen">
                                                <?php echo translate_phrase('Buy Discounted Member Ticket')?>
                                                </div>
                                            </a>
                                    </div>
                                <?php endif;?>
				<div class="Ap-Invit-main">					
					<a href="<?php echo 'mailto:?subject=Hello&amp;body=Hey! Have you tried '.get_assets('name','DateTix').'? Its a great way to meet new friends and potential dates. You can find out more details and sign up at http://www.datetix.com';?>">                                                                                       
					<div class="appr-cen"><?php echo translate_phrase('Invite Friends');?></div></a>
										
					<!--				<a
						href="<?php echo base_url().url_city_name();?>/invite-friends.html">
						<div class="appr-cen">
						<?php echo translate_phrase('Invite Friends')?>
						</div> </a>-->
					<!--<a
						href="<?php echo base_url().url_city_name();?>/edit-profile.html">
						<div class="appr-cen">
						<?php echo translate_phrase('Edit Profile')?>
						</div>
                                     </a>-->
					
				</div>
			</div>
		
			
		</div>
	</div>
</div>


<!-- OLD CODE
<section id="content" class="inner-content">
    <div class="page-container">
        <div class="edit-profile-content">
            <div>
                <h2 class="page-title"><?php echo translate_phrase('Apply for DATETIX Membership') ?></h2>
                <br/>
                
                <div>
                     <br/>
                    <?php $msg       = 'Due to heavy application volume,the current estimated wait time for us to reach a decision on your application is: <b>7 to 10 working days</b>.';
                          $third_msg = 'While you are waiting,please invite your single friends to also apply for a free membership to DATETIX. <b>We will credit your account with 1 free ticket(HDK 1,880 value) for every friend that you refer and who we later approve as a member!</b>';
                    ?> 
                    <p><?php echo translate_phrase('Thanks for taking the time to apply! Your application has been submitted to our review team. We will be in touch if we have any question in your application.');?></p><br>
                    <p><?php echo translate_phrase($msg);?></p><br>
                    <p><?php echo translate_phrase($third_msg);?></p>
                    <div class="button_centre_align"><a href="<?php echo url_city_name();?>/invite-friends.html" id="ureg_sub" class="button darkblue"><?php echo translate_phrase('Invite Friends')?></a></div>
                </div>
            </div>
         </div>
    </div>
</section>
-->
