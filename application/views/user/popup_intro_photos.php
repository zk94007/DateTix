<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="popup-box popupBigger">
				<div class="citypageHed">
					<h1>
					<?php echo translate_phrase('Profile Photos'); ?>
					</h1>
					<br />
					<br />

					<div class="two-column-school-container" id="schoolContainer">
						<ul class="list_rows">
						<?php foreach ($user_photos as $photo): ?>
							<li class="img-left-box" style="width: auto;"><img
								style="max-width: 200px; height: 180px;"
								src="<?php echo $photo['url'] ?>"
								alt="<?php echo $photo['photo'] ?>" />
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<div class="Nex-mar">
					<a href="<?php echo base_url().$return_url?>" class="Next-butM"><?php echo translate_phrase('Ok') ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
