<?php $no_feedback = true;?>
<?php
$user_id = $this->session->userdata('user_id');
//5 - View Past Date Feedback
$is_premius_member = $this->datetix->is_premium_user($user_id,5);
$intro_noun = $user_info['gender_id'] == 1?translate_phrase('him'):translate_phrase('her');

?>
<!--*********Suggest date ideal Page start*********-->
<div class="wrapper">
	<div class="content-part">
		<div class="cityPage">
			<div class="popup-box popupSmall">
				<div class="popup-header">
					<h1>
					<?php echo $heading_txt;?>
					</h1>
				</div>
				<?php if($user_feedback):?>
				<div class="edu-main">
				<?php if(isset($user_feedback['date_looks']) && $user_feedback['date_looks']):?>
				<?php $no_feedback = false;?>
					<div class="aps-d-top">
						<h2>
						<?php echo translate_phrase('How did you look') ?>
							?
						</h2>
						<div class="f-decrMAIN">
							<div class="f-decr">
							<?php if($looks):?>
							<?php unset($looks['']);?>
							<?php foreach ($looks as $key=>$look):?>
							<?php if(isset($user_feedback['date_looks']) && $user_feedback['date_looks'] == $key):?>
								<div class="Intro-Button-sel">
									<span><?php echo $look?> </span>
								</div>
								<?php endif;?>
								<?php endforeach;?>
								<?php endif;?>
							</div>
						</div>
					</div>
					<?php endif;?>
					<?php
					$sel_val = '';
					if(isset($user_feedback['date_attitude']))
					{
						switch ($user_feedback['date_attitude']) {
							case 5:
								$sel_val = translate_phrase('Not at All');
								break;
							case 4:
								$sel_val = translate_phrase('Slightly');
								break;

							case 3:
								$sel_val = translate_phrase('Average');
								break;
									
							case 2:
								$sel_val = translate_phrase('Very');
								break;
									
							case 1:
								$sel_val = translate_phrase('Extremely');
								break;
									
							default:
								$sel_val = '';
								break;
						}
					}
					?>
					<?php if($sel_val != ''):?>
					<?php $no_feedback = false;?>
					<div class="aps-d-top">
						<h2>
						<?php echo translate_phrase('Did you appear friendly and personable')?>
							?
						</h2>
						<div class="f-decrMAIN">
							<div class="f-decr">
								<div class="Intro-Button-sel">
									<span><?php echo $sel_val?> </span>
								</div>
							</div>
						</div>
					</div>
					<?php endif;?>

					<?php
					$sel_val = '';
					if(isset($user_feedback['date_conversation']))
					{
						switch ($user_feedback['date_conversation']) {
							case 5:
								$sel_val = translate_phrase('Very Boring ');
								break;
							case 4:
								$sel_val = translate_phrase('Boring');
								break;

							case 3:
								$sel_val = translate_phrase('Average');
								break;

							case 2:
								$sel_val = translate_phrase('Interesting');
								break;

							case 1:
								$sel_val = translate_phrase('Very Interesting');
								break;

							default:
								$sel_val = '';
								break;
						}
					}
					?>
					<?php if($sel_val != ''):?>
					<?php $no_feedback = false;?>
					<div class="aps-d-top">
						<h2>
						<?php echo translate_phrase('How was the level of conversation and engagement?')?>
						</h2>
						<div class="f-decrMAIN">
							<div class="f-decr">
								<div class="Intro-Button-sel">
									<span><?php echo $sel_val?> </span>
								</div>
							</div>
						</div>
					</div>
					<?php endif;?>

					<?php
					$sel_val = '';
					if(isset($user_feedback['date_chemistry']))
					{
						switch ($user_feedback['date_chemistry']) {
							case 5:
								$sel_val = translate_phrase('Not at All');
								break;
							case 4:
								$sel_val = translate_phrase('Slightly');
								break;

							case 3:
								$sel_val = translate_phrase('Average');
								break;
									
							case 2:
								$sel_val = translate_phrase('Very');
								break;
									
							case 1:
								$sel_val = translate_phrase('Extremely');
								break;
									
							default:
								$sel_val = '';
								break;
						}
					}
					?>
					<?php if($sel_val != ''):?>
					<?php $no_feedback = false;?>
					<div class="aps-d-top">
						<h2>
						<?php echo translate_phrase('How much chemistry and interest level did ').$user_info['first_name'].translate_phrase(' feel with you?')?>
						</h2>
						<div class="f-decrMAIN">
							<div class="f-decr">
								<div class="Intro-Button-sel">
									<span><?php echo $sel_val?> </span>
								</div>
							</div>
						</div>
					</div>
					<?php endif;?>


					<?php
					$sel_val = '';
					if(isset($user_feedback['date_overall']))
					{
						switch ($user_feedback['date_overall']) {
							case 5:
								$sel_val = translate_phrase('Not at All');
								break;
							case 4:
								$sel_val = translate_phrase('Slightly');
								break;

							case 3:
								$sel_val = translate_phrase('Average');
								break;

							case 2:
								$sel_val = translate_phrase('Very');
								break;

							case 1:
								$sel_val = translate_phrase('Extremely');
								break;

							default:
								$sel_val = '';
								break;
						}
					}
					?>
					<?php if($sel_val != ''):?>
					<?php $no_feedback = false;?>
					<div class="aps-d-top">
						<h2>
						<?php echo translate_phrase('How much ').$user_info['first_name'].translate_phrase(' enjoyed the date with you:')?>
						</h2>
						<div class="f-decrMAIN">
							<div class="f-decr">
								<div class="Intro-Button-sel">
									<span><?php echo $sel_val?> </span>
								</div>
							</div>
						</div>
					</div>
					<?php endif;?>

					<?php if(isset($user_feedback['descriptive_words']) && $user_feedback['descriptive_words']):?>
					<?php $no_feedback = false;?>
					<div class="aps-d-top">
						<h2>
						<?php echo $user_info['first_name'].translate_phrase(' describes you as: ')?>
						</h2>
						<div class="f-decrMAIN your-personality">
							<div class="f-decr">
								<ul>
								<?php foreach($user_feedback['descriptive_words'] as $row):?>
									<li><span class="appr-cen"><?php echo ucfirst($row['description']);?>
									</span></li>
									<?php endforeach;?>
								</ul>
							</div>
						</div>
					</div>
					<?php endif;?>

					<?php if($is_premius_member):?>
					<?php if($no_feedback):?>
					<div class="aps-d-top">
					<?php if($user_feedback['date_showed_up'] == 0):?>
						<h2>
						<?php echo $user_info['first_name'].translate_phrase(" told us that you didn't show up for the date!")?>
						</h2>
						<?php else:?>
						<h2>
						<?php echo translate_phrase('No feedback found from ').$user_info['first_name']?>
						</h2>
						<?php endif;?>
					</div>
					<?php endif;?>
					<?php endif;?>
				</div>
				<?php endif;?>

				<?php if(!$is_premius_member):?>
				<div class="two-column-school-container">
					<div class="cityArea">
					<?php echo translate_phrase(' You may only view').' '.$user_info['first_name'] ."'s ".translate_phrase(' feedback of you if you ')?>
						<span class="blu-color"><a
							href="<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-dates.html&tab=past"><?php echo translate_phrase('add the View Past Date Feedback upgrade to your account')?>
						</a> </span>.
					</div>
				</div>
				<?php endif;?>

				<div class="btn-group center cl">
				<?php if(!$is_premius_member):?>
					<input type="button" class="btn btn-pink"
						onclick="window.location.href='<?php echo base_url() . url_city_name() ?>/upgrade-account.html?return_to=my-dates.html&tab=past'"
						value="<?php echo translate_phrase('Upgrade Account')?>" />
						<?php endif;?>
					<input type="button"
						onclick="window.location.href='<?php echo base_url().$return_url?>'"
						class="btn btn-blue" value="<?php echo translate_phrase('Ok') ?>" />
				</div>
			</div>
		</div>
	</div>
</div>
<!--*********Suggest date ideal -Page close*********-->
