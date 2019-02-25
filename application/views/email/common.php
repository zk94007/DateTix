<?php  echo $this->load->view('email/include/header'); ?>

<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">

			<div class="content">
				<table>
					<tr>
						<td>
							<h3><?php echo $email_title;?></h3>
							<p class="lead"><?php echo $email_content;?></p>
							 
							<?php if(isset($btn_link) && isset($btn_text)):?>
							<a style="text-decoration: none; background: #FF499A; color: #FFF; padding: 10px 16px; font-weight: bold; margin-right: 10px; text-align: center; cursor: pointer; display: inline-block; border: 1px; -webkit-appearance: none; border-radius: 0;"
							href="<?php echo $btn_link;?>"><?php echo $btn_text?> </a> <?php endif;?>
							
							<?php if(isset($btn_link2) && isset($btn_text2)):?>
							<a style="text-decoration: none; background: #aaa; color: #FFF; padding: 10px 16px; font-weight: bold; margin-right: 10px; text-align: center; cursor: pointer; display: inline-block; border: 1px; -webkit-appearance: none; border-radius: 0;"
							href="<?php echo $btn_link2;?>"><?php echo $btn_text2?> </a> <?php endif;?>
							
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
<?php  echo $this->load->view('email/include/footer'); ?>