<script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    
	//*************************  Login box  ***************************//
	$('#form-register').validate({
		rules: { 
			coupon_promo_code: {required: true},
		},    
		highlight: function(element) {
			$(element).closest('.div-row').removeClass('success').addClass('error');
		},
		success: function(element) {
				element.closest('.div-row').removeClass('error').addClass('success');
		}
	});
});
function is_datetix_user(status,obj)
{
	var hiddenVal = '';
	var text = '';	
	$(obj).find('span').removeClass('disable-butn').addClass('appr-cen');	
	$(obj).siblings('a').find('span').removeClass('appr-cen').addClass('disable-butn');
	if(status == 1)
	{
		hiddenVal = 1;
	}
	else
	{
		hiddenVal = 0;
	}
	$('#is_datetix_member').val(hiddenVal)
}
</script>
<?php
	$return = $this->session->flashdata("returnErrorData");
	$coupon_promo_code = (isset($return['coupon_promo_code']))?$return['coupon_promo_code']:set_value('coupon_promo_code');
?>
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<form name="form-register" id="form-register" action="<?php echo base_url() . $this->uri->segment(1)?>" method="post" autocomplete="off">
				<div class="popup-box popupSmall">
					<h1><?php echo $page_title;?></h1>
					<div class="cityTxt comn-top-mar fl"><?php echo $this->session->flashdata('dispMessage');?></div>					
					<div class="div-row">
						<input type="text" placeholder="<?php echo translate_phrase('Enter your promo Code') ?>" class="input-full" name="coupon_promo_code" id="coupon_promo_code" value="<?php echo $coupon_promo_code;?>"> 
						<label class="input-hint error" for="coupon_promo_code"><?php echo form_error('coupon_promo_code')?></label>
					</div>
					<?php if(!$this->session->userdata('user_id')):?>
					<div class="div-row">
						<span class="fl L-post-anyThing" style="line-height:35px; height:35px;margin-right:5px; width:auto; font-size:17px;"><?php echo translate_phrase('Already a ').get_assets('name','DateTix').translate_phrase(' member?');?></span>
						<a onclick="is_datetix_user(1,this)" href="javascript:;"><span class="disable-butn">Yes</span></a>
						<a onclick="is_datetix_user(0,this)" href="javascript:;"><span class="appr-cen">No</span></a>
						<input id="is_datetix_member" type="hidden" name="is_datetix_member" value="0"/>
						
					</div>
					<?php endif;?>
					
					<div class="div-row">
						<div class="order-btn">
							<input type="submit" class="btn btn-blue" value="<?php echo translate_phrase('Apply Promo Code')?>" /> 
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
