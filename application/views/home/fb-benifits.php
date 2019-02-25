<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">

			<form novalidate="novalidate" action="<?php echo base_url()?>"
				method="post" accept-charset="utf-8" id="form-signin">
				<div class="popup-box popupMiddle" id="form-container">
					<div class="citypageHed"
						style="padding-top: 0px; border-bottom: none;">
						<h1>
						<?php echo translate_phrase('Benefits of Applying Using Your Facebook Account');?>
						</h1>
						<br />
						<br />
						<ul class="fb-benifits">
							<li style="text-align: left; padding-bottom: 20px"><img
								class="tickImage" style="float: left; padding-right: 15px;"
								src="<?php echo base_url().'assets/images/tickMark.png'?>">
								<p>
								<?php echo translate_phrase('Significantly speed up the application process by automatically filling in basic profile information') ?>
								</p></li>
							<li style="text-align: left; padding-bottom: 20px"><img
								class="tickImage" style="float: left; padding-right: 15px;"
								src="<?php echo base_url().'assets/images/tickMark.png'?>">
								<p>
								<?php echo translate_phrase('Accelerate your application review and approval process by allowing us to more easily verify your identity');?>
								</p></li>
							<li style="text-align: left; padding-bottom: 20px"><img
								class="tickImage" style="float: left; padding-right: 15px;"
								src="<?php echo base_url().'assets/images/tickMark.png'?>">
								<p>
								<?php echo translate_phrase('Enjoy free premium membership by easily inviting your friends to apply to ').get_assets('name','DateTix');?>
								</p></li>
						</ul>
						<p
							style="color: #535353; font-family: 'Conv_MyriadPro-Semibold'; font-weight: bold;">
							<?php echo translate_phrase('Your privacy is of paramount importance to us. We will not post anything to your Facebook without your permission')?>
						</p>
					</div>
					<div class="div-row">
						<div class="btn-group ">
							<a href="javascript:;" style="float: left;"
								onclick="fb_login();return false;" class="facebook-image"
								title="<?php echo translate_phrase('Apply to ').get_assets('name','DateTix')?>"><img
								src="<?php echo base_url()?>assets/images/apply-privatly.png"
								style="width: 276px !important" alt="fb_login">
							</a>
							<button class="btn btn-blue fbBenifitsBackButton">
								<a href="<?php echo base_url()?>"
									style="color: white !important;"><?php echo translate_phrase('Back')?>
								</a>
							</button>
						</div>

					</div>
				</div>
		
		</div>
		</form>
	</div>
</div>
