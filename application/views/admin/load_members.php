<!-- Added By Jigar Oza-->
<?php if(isset($totalRecords)) {?>
<input type="hidden" class="totalResultCount" value="<?php echo count($totalRecords);?>" />
<?php }?>

<?php if(isset($approved_applications) && $approved_applications):?>
<?php foreach($approved_applications as $user):?>
<div class="userBox" lang="<?php echo $user['user_id'];?>">
	<?php include APPPATH.'views/admin/include_member_info.php';?>        
	<!--<div class="userTopRowHed"><a href="javascript:;" class="find_matches" lang="<?php echo $user['user_id'];?>" tab="manage_member"><span class="appr-cen"><?php echo translate_phrase("Get Internal Matches");?></span></a></div>-->
	<!--<div class="userTopRowHed"><a href="javascript:;" class="find_matches" lang="<?php echo $user['user_id'];?>" tab="marketplace"><span class="appr-cen"><?php echo translate_phrase("Get Marketplace Matches");?></span></a></div>-->
<!--	<div class="userBox-wrap">
		<div class="userTop">
			<div class="selectedTxt div-row"><?php echo translate_phrase("Send Email to ").$user['first_name'];?>:</div>
			<div class="div-row"><input name="subject" class="Degree-input" type="text" value="Datetix Application"></div>
			<div class="div-row">
				<textarea name="email_body" class="input-full" style="height: 160px;"><?php echo $user['thanks_mail_body'];?></textarea>
			</div>
			<div class="btn-group right">
				<button type="button" class="btn btn-blue send_mail" lang="<?php echo $user['user_id'];?>"><?php echo translate_phrase("Send Mail");?></button>
			</div>
			<label></label>
		</div>		
	</div>-->
</div>
<?php endforeach;?>
<?php endif;?>
