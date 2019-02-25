<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="cityTxt fl"><h1><?php echo $page_title ?></h1></div>
			<div class="popup-box popupSmall">	
				
				<form action="<?php echo base_url().'admin/change_user_account_status'.$form_para?>" method="post">
					
					<div class="userTop">
						<p><?php echo $msg;?></p>
					</div>
					<div class="detail-list">
						<?php echo $extra;?>
					</div>
										
					<input type="hidden" name="status" value="<?php echo $status;?>"/>
					
					<div class="btn-group ">
						<button type="submit" name="confirm" class="btn btn-pink"><?php echo translate_phrase("Confirm");?></button>
						<button type="button" class="btn btn-blue btn-small" onclick="history.back();"><?php echo translate_phrase("Cancel");?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
