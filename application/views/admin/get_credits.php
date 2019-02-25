<script src="<?php echo base_url() ?>assets/js/general.js"></script>
<script type="text/javascript">
$(document).ready(function(){

    
});
</script>
<?php $selected_key = 3; ?>
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			<div class="Edit-pge-Main">
				<div class="Edit-p-top1">
					<h1>
					<?php echo translate_phrase('Get Credits')?>
					</h1>
				</div>
			</div>
			<div class="emp-B-tabing-prt">
				<div class="error-msg left">
				<?php echo $this->session->flashdata('paypal');?>
				</div>

				<div class="step-form-Main Mar-top-none Top-radius-none">
					<div class="step-form-Part">
						<div class="upgrade-top-txt">
							<?php echo translate_phrase('Call Michael at +852 6684-2770 to purchase more credits to connect more of your clients with suitable matches from our marketplace!')?>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
