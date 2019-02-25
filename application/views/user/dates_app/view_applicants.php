<style>
	.user-photo-slider1{text-align:center;}
	.img-slide img{max-width:100%;}
</style>
<?php
$requested_user_id=$date_info['requested_user_id'];
$did=$date_info['date_id'];
$checkApplicantChosen=$this->db->query("select * from date_applicant  where date_id='".$did."' and is_chosen='1'")->result_array();
?>
<!--*********Page start*********-->

<div class="wrapper">
	<div class="content-part" style="overflow-x: hidden;">
		<div class="Apply-Step1-a-main">
			<div class="girl-msg-main">
				<ul class="detail-list">
					<li>
						<?php echo print_date_day($date_info['date_time']).' @ '.date('h:i A', strtotime($date_info['date_time']));?>		
					</li>
					<li>
						<?php echo $date_info['date_type']. translate_phrase(' at '). $date_info['name'];?>										
						<a target="_blank" href="http://maps.google.com/?q=<?php echo $date_info['address'];?>" ><?php echo translate_phrase('View Map');?></a>
							
					</li>
					<li><?php echo translate_phrase('Looking for').' '.$date_info['gender'].translate_phrase(' for ').' '. $date_info['intention_type'];?></li>
				</ul>
			</div>
			<div class="step-form-Part">
				<div class="datesArea">
				<?php if($date_applications):?>
				<?php foreach($date_applications as $key=>$date_info):
                $date_id = $date_info['date_id']; ?>
					<div class="dateRow" id="date_<?php echo $date_info['date_id'];?>">
						<div class="userBoxLeft" id="photo_slider_<?php echo $key;?>">				
							<?php if ($date_info['user_photos']): ?>
							<a href="<?php echo base_url();?>user/user_info/<?php echo $this->utility->encode($requested_user_id);?>">                        
                            	<?php
                                	$iid=$date_info['user_photos']['user_id'];
                                    $pic=$date_info['user_photos']['photo'];
                                    $url=base_url() . "user_photos/user_$iid/" . $pic;
                                ?>
                                <img class="img-circle user-img" src="<?php echo $url ?>" alt="<?php echo $date_info['user_photos']['photo'] ?>" />
                            </a>
                            <?php else: ?>
                            <?php echo translate_phrase('No photos added yet'); ?>
                            <?php endif; ?>	
						</div>
						<div class="userBoxRight">
						
							<?php if($key != 0):?>
							<div class="divider"></div>
							<?php endif;?>
							<div class="mar-top2">
								<div class="column-50">
									<div><?php echo $date_info['applicant_by_name'].', '.$date_info['age'];?></div>
									<div class="userbox-innr comn-top-mar">
                                    	<p class="DarkGreen-color"> Offering <?php  echo $date_info['num_date_tickets'];?> date tickets </p>
										<p class="font-italic"><?php echo translate_phrase('Applied').' '.$date_info['applied_time'];?></p>												
									</div>	
								</div>
								<div class="column-50">
									<a href="<?php echo base_url();?>dates/chat_history/<?php echo $this->utility->encode($date_info['applicant_user_id']) ;?>/<?php echo $this->utility->encode($requested_user_id);?>" ><span class="appr-cen btn-pink"><?php echo translate_phrase('Chat') ?></span></a>
									<?php if(empty($checkApplicantChosen)):?>
                                    <a href="#chooseApplicant_<?php echo $date_id.$key;?>" class="chooseApplicant">
                                    <span class="appr-cen btn-blue"><?php echo translate_phrase('Choose Applicant') ?></span>
                                    </a>
                                    <?php endif;?>
                                    <?php if(@$checkApplicantChosen[0]['applicant_user_id']==$date_info['applicant_user_id']):?> 
                                    <p class="DarkGreen-color">Chosen Applicant</p>
                                    <?php endif;?>
								</div>
							</div>
						</div>		
					</div>
                    <div id="chooseApplicant_<?php echo $date_id.$key;?>" style="display: none">
                    	
                    	<h2>Are you sure you want to chosen <?php echo $date_info['applicant_by_name'];?> for this date ?</h2>
                        
                        <div class="div-row mar-top2">
                        <p>We will notify <?php echo $date_info['applicant_by_name'];?> to chosen for this date and your contact info will be exchanged with each other.</p>
						</div>
                        <a href="#" onclick="choseApplicant('<?php echo $date_info['date_applicant_id'];?>')">
                        <span class="appr-cen btn-blue"><?php echo translate_phrase('Ok') ?></span>
                        </a>
                        <a href="javascript:;" onclick="closeFancyBox()">
                        <span class="disable-butn"><?php echo translate_phrase('Cancel') ?></span>
                        </a>	
					</div>
				<?php endforeach;?>
				<?php else:?>
				<p><span class="no-rows"><?php echo translate_phrase('You currently have no applicants for this date.')?></span></p>
				<?php endif;?>	
				</div>
			</div>				
		</div>
	</div>
</div>