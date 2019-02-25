<script src="<?php echo base_url()?>assets/js/general.js"></script>
<?php
$user_id = $this->session->userdata('user_id');
$is_premius_member = $this->datetix->is_premium_user($user_id,PERMISSION_INSTANT_INTRO);
?>
<!--*********Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<div class="Apply-Step1-a-main">
			
			<div class="emp-B-tabing-prt">
				<div class="emp-B-tabing-M-short" id="active_expire">
					

				</div>
				<!-- END emp-B-tabbing-M -->
			</div>
		</div>
	</div>
</div>
<!--*********Page close*********-->
