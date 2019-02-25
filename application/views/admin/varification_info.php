<script>
var user_id = "<?php echo $user_data['user_id']?>";

function verify_data(section,field,status,obj)
{
	if(user_id)
	{
		$('#status').val(status);
		$('#field').val(field);
		$('#section').val(section);
		$('#field_val').val($(obj).parent().attr('lang'));
		$('#field_name').val($(obj).parent().attr('field_name'));
		
		$("#tmp_verify_form").submit();
		
		/*
		loading();
		$.ajax({ 
			url: base_url +"admin/verify_data/"+user_id,
			type:"post",
			data:postData,
			dataType:'json',
			success: function (response) {
				stop_loading();
				if(response.type == "success"){
					
					var opp_status = 1;
					var status_class = "btn Red-color";
					var btn_class = 'btn-pink';
					if(status == 1){
						status_class = "btn DarkGreen-color";
						btn_class = 'btn-blue';
						opp_status = 2;
					}					
					if($(obj).siblings('button').length == 0){
						$(obj).siblings('span').replaceWith('<button onclick="verify_data(\''+section+'\',\''+field+'\','+opp_status+',this)" type="button" class="'+btn_class+' btn btn-small" lang="'+$(obj).siblings('span').text()+'">'+$(obj).siblings('span').attr('lang')+'</button>');
					}
					
					$(obj).replaceWith('<span class="'+status_class+'" lang="'+$(obj).text()+'">'+$(obj).attr('lang')+'</span>');
				}				
		   }
		});
		*/
	}
}
</script>
<div class="wrapper">
	
	<form id="tmp_verify_form" action="<?php echo base_url().'admin/verify_data/'.$user_data['user_id'] ?>" method="post">
		 <input type="hidden" name="status" id="status"/>
		 <input type="hidden" name="field" id="field"/>
		 <input type="hidden" name="section" id="section"/>
		 <input type="hidden" name="field_val" id="field_val"/>
		 <input type="hidden" name="field_name" id="field_name"/>		 
	</form>
	
	
	<div class="content-part">
		<div class="cityPage">
			<div class="cityTxt fl"><h1><?php echo translate_phrase("Verification info") ?></h1></div>
			<div class="popup-box emp-B-tabing-M-short">	
				
				<?php if (isset($user_data['hear_about_us_description'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('How You heared about')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $user_data['how_you_heard_about_us_other']?$user_data['hear_about_us_description'].' - '.$user_data['how_you_heard_about_us_other']:$user_data['hear_about_us_description'];?></div>
				</div>
				<?php endif;?>
				
				<?php if($user_data['mobile_phone_number'] && 1 == 2):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Mobile Number')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo '(+'.$user_data['country_code'].') '.formate_mobile_number($user_data['mobile_phone_number']); echo $user_data['mobile_phone_is_verified']?'<span class="DarkGreen-color mar-verify bold">'.translate_phrase("Verified").'</span>':'' ;?></div>
				</div>
				<?php endif;?>
				
				
				<?php if($user_data['facebook_page']):?>
				<!--Facebook Section -->
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Facebook page')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $user_data['facebook_page'];?></div>
				</div>
				
				<div class="infoRow">
					<div class="infoRowLeft"><img src="<?php echo base_url()?>assets/images/facebook-icn01.jpg" /></div>
					<div class="infoRowRight infoRowRightText" field_name="<?php echo translate_phrase('Facebook page');?>">
						
						<?php if($user_data['facebook_page_is_verified'] == 1):?>
							<span class="btn DarkGreen-color" lang="<?php echo translate_phrase("Approve");?>"><?php echo translate_phrase("Approved");?></span>
							<button onclick="verify_data('profile','facebook_page','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
						<?php elseif($user_data['facebook_page_is_verified'] == 2):?>
							<button onclick="verify_data('profile','facebook_page','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
							<span class="btn Red-color" lang="<?php echo translate_phrase("Reject");?>"><?php echo translate_phrase("Rejected");?></span>
						<?php else:?>
							<button onclick="verify_data('profile','facebook_page','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
							<button onclick="verify_data('profile','facebook_page','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
						<?php endif;?>											
					</div>
				</div>
				<?php endif;?>
				
				<?php if($user_data['linkedin_page']):?>
				<!--Linked in Section -->
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('LinkedIn page')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $user_data['linkedin_page'];?></div>
				</div>
				
				<div class="infoRow">
					<div class="infoRowLeft"><img src="<?php echo base_url()?>assets/images/linked-logo.jpg" /></div>
					<div class="infoRowRight infoRowRightText" field_name="<?php echo translate_phrase('LinkedIn page');?>">
						<?php if($user_data['linkedin_page_is_verified'] == 1):?>
							<span class="btn DarkGreen-color" lang="<?php echo translate_phrase("Approve");?>"><?php echo translate_phrase("Approved");?></span>
							<button onclick="verify_data('profile','linkedin_page','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
						<?php elseif($user_data['linkedin_page_is_verified'] == 2):?>
							<button onclick="verify_data('profile','linkedin_page','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
							<span class="btn Red-color" lang="<?php echo translate_phrase("Reject");?>"><?php echo translate_phrase("Rejected");?></span>
						<?php else:?>
							<button onclick="verify_data('profile','linkedin_page','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
							<button onclick="verify_data('profile','linkedin_page','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
						<?php endif;?>
					</div>
				</div>
				
				<?php endif;?>
				
				<?php if($user_data['wechat_id']):?>
				<!--Wechat Section -->
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('WeChat username')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $user_data['wechat_id'];?></div>
				</div>
				
				<div class="infoRow">
					<div class="infoRowLeft"><img src="<?php echo base_url()?>assets/images/wechat-logo.jpg" /></div>
					<div class="infoRowRight infoRowRightText" field_name="<?php echo translate_phrase('WeChat username');?>">
						<?php if($user_data['wechat_id_is_verified'] == 1):?>
							<span class="btn DarkGreen-color" lang="<?php echo translate_phrase("Approve");?>"><?php echo translate_phrase("Approved");?></span>
							<button onclick="verify_data('profile','wechat_id','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
						<?php elseif($user_data['wechat_id_is_verified'] == 2):?>
							<button onclick="verify_data('profile','wechat_id','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
							<span class="btn Red-color" lang="<?php echo translate_phrase("Reject");?>"><?php echo translate_phrase("Rejected");?></span>
						<?php else:?>
							<button onclick="verify_data('profile','wechat_id','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
							<button onclick="verify_data('profile','wechat_id','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
						<?php endif;?>
					</div>
				</div>
				<?php endif;?>
				
				<?php if($user_data['user_photos']):?>
				<!--User Photos Section -->
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Profile photo review')?>:</div>
					<div class="infoRowRight">
						
						<div class="simple-photo-list">
							<?php foreach($user_data['user_photos'] as $val):?>
							<span class="list">
								<img style="height: 180px" src="<?php echo base_url().'user_photos/user_'.$user_data['user_id'].'/'.$val['photo']?>">
								<span class="btns" lang="<?php echo $val['user_photo_id'];?>" field_name="<?php echo translate_phrase('profile photo');?>">
									<?php if($val['is_approved'] == 1):?>
									<span class="btn DarkGreen-color" lang="<?php echo translate_phrase("Approve");?>"><?php echo translate_phrase("Approved");?></span>
										<button onclick="verify_data('photo','is_approved','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
									<?php elseif($val['is_approved'] == 2):?>
										<button onclick="verify_data('photo','is_approved','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
										<span class="btn Red-color" lang="<?php echo translate_phrase("Reject");?>"><?php echo translate_phrase("Rejected");?></span>
									<?php else:?>
										<button onclick="verify_data('photo','is_approved','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
										<button onclick="verify_data('photo','is_approved','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
									<?php endif;?>
								</span>
							</span>
							<?php endforeach;?>							
						</div>
						<!-- end slider -->	
					</div>
				</div>
				<?php endif;?>
				
				<?php if($user_data['photo_id']):?>
				<!--Photo ID Section -->
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Profile ID verification')?>:</div>
					<div class="infoRowRight">
						<div class="photo-wrapper">
							<div class="upload-part"><img src="<?php echo base_url().'user_photos/user_'.$user_data['user_id'].'/'.$user_data['photo_id']?>" alt=""></div>
							<div class="btn-group" field_name="<?php echo translate_phrase('photo ID');?>">
								<?php if($user_data['photo_id_is_verified'] == 1):?>
									<span class="btn DarkGreen-color" lang="<?php echo translate_phrase("Approve");?>"><?php echo translate_phrase("Approved");?></span>
									<button onclick="verify_data('profile','photo_id','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
								<?php elseif($user_data['photo_id_is_verified'] == 2):?>
									<button onclick="verify_data('profile','photo_id','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
									<span class="btn Red-color" lang="<?php echo translate_phrase("Reject");?>"><?php echo translate_phrase("Rejected");?></span>
								<?php else:?>
									<button onclick="verify_data('profile','photo_id','1',this)" type="button" class="btn btn-pink btn-small" lang="<?php echo translate_phrase("Approved");?>"><?php echo translate_phrase("Approve");?></button>
									<button onclick="verify_data('profile','photo_id','2',this)" type="button" class="btn btn-blue btn-small" lang="<?php echo translate_phrase("Rejected");?>"><?php echo translate_phrase("Reject");?></button>
								<?php endif;?>
							</div>
						</div>					
					</div>
				</div>
				<?php endif;?>
				
				<?php if($school_data):?>
				<!--School Photo  Section -->
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('School photo verification')?>:</div>
					<div class="infoRowRight">
						
						<?php foreach($school_data as $val):?>
						<div class="scroll-part">
							<p><?php echo $val['school_id']?$val['school_name']:$val['my_school_name'];?></p>
							<div class="photo-wrapper comn-top-mar">
								<div class="upload-part"><img src="<?php echo base_url().'user_photos/user_'.$user_data['user_id'].'/'.$val['photo_diploma']?>" alt=""></div>
								<div class="btn-group">
									<button type="button" class="btn btn-pink btn-small" lang="approve"><?php echo translate_phrase("Approve");?></button>
									<button type="button" class="btn btn-blue btn-small" lang="reject"><?php echo translate_phrase("Reject");?></button>
								</div>
							</div>
						</div>
						<?php endforeach;?>
					</div>
				</div>
				<?php endif;?>
				
				<?php if($company_data):?>
				<!--Job Photo  Section -->
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Job photo verification')?>:</div>
					<div class="infoRowRight">
						
						<?php foreach($company_data as $val):?>
						<div class="scroll-part">
							<p><?php echo $val['company_id']?$val['company_name']:$val['my_company_name'];?></p>
							<div class="photo-wrapper comn-top-mar">
								<div class="upload-part"><img src="<?php echo base_url().'user_photos/user_'.$user_data['user_id'].'/'.$val['photo_business_card']?>" alt=""></div>
								<div class="btn-group">
									<button type="button" class="btn btn-pink btn-small" lang="approve"><?php echo translate_phrase("Approve");?></button>
									<button type="button" class="btn btn-blue btn-small" lang="reject"><?php echo translate_phrase("Reject");?></button>
								</div>
							</div>
						</div>
						<?php endforeach;?>
					</div>
				</div>
				<?php endif;?>
				
				<div class="Nex-mar">
					<a href="<?php echo base_url().'admin'?>" class="Next-butM"><?php echo translate_phrase("Back");?></a>
				</div>
				
			</div>
		</div>
	</div>
</div>
