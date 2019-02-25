<script>
$("document").ready(function(){

	$(".dropdown-dt ul li a").live('click',function(){
		if($(this).attr('key') == 4)
		{
			$("#other_reason_txt").slideDown();
		}
		else
		{
			$("#other_reason_txt").slideUp();
		}
	});
	
});
</script>

<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="cityTxt fl"><h1><?php echo $page_title ?></h1></div>
			<div class="popup-box popupSmall">	
				
				<form action="<?php echo base_url().'admin/save_verify_data'.$form_para?>" method="post">
					
					<div class="style01"><?php echo translate_phrase("Please select your rejection reason:")?></div>
					<div class="userTop fl comn-top-mar">
						<dl class="dropdown-dt domaindropdown common-dropdown" >
							<dt>
								<a href="javascript:;" key=""><span><?php echo translate_phrase("Select");?></span> </a>
								<input type="hidden" name="reject_reason_id" value="">
							</dt>
							<dd>
								<ul>
									<?php foreach($reject_reason as $item):?>
									<li><a href="javascript:;" key="<?php echo $item['reason_id'];?>"><?php echo $item['description'];?></a></li>
									<?php endforeach;?>
								</ul>
							</dd>
						</dl>						
					</div>
					
					<div id="other_reason_txt" style="display:none;">
						<div class="style01 comn-top-mar"><?php echo translate_phrase("Please select your rejection reason:")?></div>
						<div class="div-row fl comn-top-mar">
							<textarea name="other_reason_txt" class="input-full" style="height: 100px;"></textarea>
						</div>
						
					</div>
					
					<input type="hidden" name="status" value="<?php echo $status;?>"/>
					<div class="userTop comn-top-mar">
						<button type="submit" name="confirm" class="btn btn-pink"><?php echo translate_phrase("Confirm");?></button>
						<button type="button" class="btn btn-blue btn-small" onclick="history.back();"><?php echo translate_phrase("Cancel");?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
