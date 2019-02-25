<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="cityTxt fl"><h1><?php echo translate_phrase("Facebook info") ?></h1></div>
			<div class="popup-box popupSmall">					
				<?php if(isset($fb_data['id'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Facebook ID')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['id']; ?></div>
				</div>
				<?php endif;?>
				
				<?php if(isset($fb_data['first_name'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('First Name')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['first_name']; ?></div>
				</div>
				<?php endif;?>
				
				
				<?php if(isset($fb_data['last_name'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Last Last')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['last_name']; ?></div>
				</div>
				<?php endif;?>
				
				<?php if(isset($fb_data['gender'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Gender')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['gender']; ?></div>
				</div>
				<?php endif;?>
				
				<?php if(isset($fb_data['relationship'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Relationship')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['relationship']; ?></div>
				</div>
				<?php endif;?>
				
				
				<?php if(isset($fb_data['bio'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Myself')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['bio']; ?></div>
				</div>
				<?php endif;?>
				
				
				<?php if(isset($fb_data['birthday'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Birthday')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo date(DATE_FORMATE,strtotime($fb_data['birthday'])); ?></div>
				</div>
				<?php endif;?>
				
				<?php if(isset($fb_data['location']['name'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Location')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['location']['name']; ?></div>
				</div>
				<?php endif;?>
				
				
				<?php if(isset($fb_data['hometown']['name'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Hometown')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['hometown']['name']; ?></div>
				</div>
				<?php endif;?>
				
				<?php if(isset($fb_data['email'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Email')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['email']; ?></div>
				</div>
				<?php endif;?>
				
				<?php if(isset($fb_data['education'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Education')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php 
						foreach($fb_data['education'] as $val)
							echo $val['school']['name']."<br/>"
					 ?></div>
				</div>
				<?php endif;?>
				
				<?php if(isset($fb_data['work'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Work')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php 
						foreach($fb_data['work'] as $val)
						{
							echo isset($val['position']['name'])?"<b>".$val['position']['name']."</b>, ":"";
							echo isset($val['employer']['name'])?"".$val['employer']['name']." - ":"";
							echo isset($val['location']['name'])?"".$val['location']['name']."<br/>":"<br/>";
						}
					 ?></div>
				</div>
				<?php endif;?>
				
				<?php if(isset($fb_data['language'])):?>
				<div class="infoRow">
					<div class="infoRowLeft bold"><?php echo translate_phrase('Language')?>:</div>
					<div class="infoRowRight infoRowRightText"><?php echo $fb_data['language']; ?></div>
				</div>
				<?php endif;?>
				
				<div class="Nex-mar">
					<a href="<?php echo base_url().'admin'?>" class="Next-butM"><?php echo translate_phrase("Back");?></a>
				</div>
				
			</div>
		</div>
	</div>
</div>
