<!-- Added By Jigar Oza-->
<?php if(isset($totalRecords)) {?>
<input type="hidden" class="totalResultCount" value="<?php echo count($totalRecords);?>" />
<?php }?>

<?php if(isset($review_applications) && $review_applications):?>
<?php foreach($review_applications as $user):?>
<div class="userBox" lang="<?php echo $user['user_id'];?>">
	<?php include APPPATH.'views/admin/include_member_info.php';?>
	<div class="userBox-wrap">
		<!--<div class="userTop">
			<div class="selectedTxt div-row"><?php echo translate_phrase("Send Email to ").$user['first_name'];?>:</div>
			<div class="div-row"><input name="subject" class="Degree-input" type="text" value="DateTix Question"></div>
			<div class="div-row">
				<textarea name="email_body" class="input-full" style="height: 160px;"><?php echo $user['thanks_mail_body'];?></textarea>
			</div>
			<div class="btn-group right">
				<button type="button" class="btn btn-blue send_mail" lang="<?php echo $user['user_id'];?>"><?php echo translate_phrase("Send Mail");?></button>
			</div>
			<label></label>
		</div>-->
		
		<div class="userTop">
			<div class="selectedTxt div-row"><?php echo translate_phrase("Approval/Rejection Message");?>: </div>
			<div class="div-row">
				<textarea name="email_body" class="input-full" style="height: 100px;"><?php echo $user['approve_mail_body'];?></textarea>
			</div>

			<label class="input-hint"></label>
			<div class="btn-group center" lang="<?php echo $user['user_id'];?>">
				<button type="button" class="btn btn-pink update_status" lang="approve"><?php echo translate_phrase("Approve");?></button>
			<!--	<button type="button" class="btn btn-blue update_status" lang="reject"><?php echo translate_phrase("Reject");?></button>-->
			</div>			
		</div>
	</div>
</div>
<?php endforeach;?>
<?php endif;?>
