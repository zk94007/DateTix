<?php  echo $this->load->view('email/include/header'); ?>
<!-- BODY -->
<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">

			<div class="content">
				<table>
					<tr>
						<td>
							<h3>
								Hi <?php echo $user; ?>,
							</h3>
							<p class="lead"><?php echo $first_line?>
							</p>
							<p class="lead">							
							<a
							style="text-decoration: none; background: #FF499A; color: #FFF; padding: 10px 16px; font-weight: bold; margin-right: 10px; text-align: center; cursor: pointer; display: inline-block; border: 1px; -webkit-appearance: none; border-radius: 0;"
							href="<?php echo $backLink;?>">Chat with <?php echo $intro_with?></a>
							<ul>
							<?php if (isset($intro_age) && $intro_age):?>
								<li><?php echo $intro_gender?> is <?php echo $intro_age?> years
									old</li>
									<?php endif;?>

									<?php if(isset($intro_works) && $intro_works):?>
								<li><?php echo $intro_works;?></li>
								<?php endif;?>

								<?php if(isset($intro_study) && $intro_study):?>
								<li><?php echo $intro_study;?></li>
								<?php endif;?>

								<?php if(isset($intro_likes) && $intro_likes):?>
								<li><?php echo $intro_likes;?></li>
								<?php endif;?>
								
								<?php if(isset($intro_common_interest) && $intro_common_interest):?>
								<li><?php echo $intro_common_interest;?></li>
								<?php endif;?>
								
								<?php if(isset($intro_fb_friend) && $intro_fb_friend):?>
								<li><?php echo $intro_fb_friend;?></li>
								<?php endif;?>

								<?php if(isset($intro_photo) && $intro_photo):?>
								<li><?php echo $intro_photo;?></li>
								<?php endif;?>

							</ul>
							</p>

							<p class="lead">
								<?php echo translate_phrase('We think you two would be a great match because:');?>
								<?php if(isset($match_with_intro) && $match_with_intro):?>
								<ul>
								<?php foreach ($match_with_intro as $match):?>
									<li> <?php echo translate_phrase("Your ").str_replace("_"," ",$match).translate_phrase(" match what each other is looking for")?></li>
										<?php endforeach;?>
								</ul> <?php endif;?>
							</p> <!-- Callout Panel -->
							
							
							<?php if(isset($cur_usr_demand_cmp) && $cur_usr_demand_cmp):
							$score = ($cur_usr_demand_cmp['score'] + $view_usr_demand_cmp['score'])/2; ?>								
							<br/>	
								<table>
									<tr>
										<td><h2><?php echo translate_phrase('Compatibility');?> - <span style="color:<?php echo $score < 50 ?'#F00 !important':'#61a723 !important';?>"><?php echo round($score).'/100'?> </span></h2></td>
									</tr>
									<tr>
										<td>
											<h4><?php echo $vuserfits; ?> <?php echo (isset($cur_usr_demand_cmp['score']) ? $cur_usr_demand_cmp['score'].'%' : '') ?> <?php echo translate_phrase('of what you are looking for') ?></h4>
										<p>
										<strong><?php echo translate_phrase('Key compatible areas') ?>: </strong>
											<?php echo ($cur_usr_demand_cmp['match_data'] ? str_replace("_"," ",implode(', ', $cur_usr_demand_cmp['match_data'])) : translate_phrase('None')) ?>
											<br/>
											<strong><?php echo translate_phrase('Key incompatible areas') ?>:</strong>
											<?php echo ($cur_usr_demand_cmp['not_matched'] ? str_replace("_"," ",implode(', ', $cur_usr_demand_cmp['not_matched'])) : translate_phrase('None')) ?></p>

										</td>
									</tr>
									<tr>
										<td> <hr> <br/></td>
									</tr>
									
									<tr>
										<td>
											<h4><?php echo translate_phrase('You fit') ?> <?php echo (isset($view_usr_demand_cmp['score']) ? $view_usr_demand_cmp['score'].'%' : '') ?> </span> <?php echo $vuser_looking_for ?>.</h4>
											<p>
												<strong><?php echo translate_phrase('Key compatible areas') ?>:</strong>
												<?php echo ($view_usr_demand_cmp['match_data'] ? str_replace("_"," ",implode(', ', $view_usr_demand_cmp['match_data'])) : translate_phrase('None')) ?>
											<br/>
												<strong><?php echo translate_phrase('Key incompatible areas') ?>:</strong>
												<?php echo ($view_usr_demand_cmp['not_matched'] ? str_replace("_"," ",implode(', ', $view_usr_demand_cmp['not_matched'])) : translate_phrase('None')) ?></p>
										</td>
									</tr>
								</table>								
							<?php endif;?>
							
							<p class="callout">
								This introduction
								<?php echo '<span style="color:#F00 !important; font-weight:bold;"> expires '.$intro_expiry_date.'</span>';?>
								, so <strong>suggest a date idea today</strong> to take things
								to the next stage!
							</p> <a
							style="text-decoration: none; background: #FF499A; color: #FFF; padding: 10px 16px; font-weight: bold; margin-right: 10px; text-align: center; cursor: pointer; display: inline-block; border: 1px; -webkit-appearance: none; border-radius: 0;"
							href="<?php echo $backLink;?>&action=sayhi">Say Hi to <?php echo $intro_with?></a> <!-- /Callout Panel -->
						</td>
					</tr>
				</table>
			</div> <!-- /content -->
		</td>
		<td></td>
	</tr>
</table>
<!-- /BODY -->
								<?php  echo $this->load->view('email/include/footer'); ?>

