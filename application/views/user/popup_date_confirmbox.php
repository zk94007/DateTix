<!--*********Suggest date ideal Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="popup-box Mar-top-none popupMiddle">
				<div class="My-int-head">
					<h1>
					<?php echo $heading_txt;?>
					</h1>
				</div>

				<p class="cityTxt">
				<?php echo $content;?>
				</p>
				<div class="btn-group center personality-mar">
					<input type="button"
						onclick="window.location.href='<?php echo base_url().$return_url?>'"
						class="btn btn-pink"
						value="<?php echo isset($btn_txt)?$btn_txt:'ok';?>" />
				</div>
			</div>
		</div>
	</div>
</div>
<!--*********Suggest date ideal -Page close*********-->
