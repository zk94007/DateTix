<?php $user_id = $this->session->userdata('user_id');?>
	<div id="chatBox_<?php echo $intro['user_intro_id']?>" class="chatBox">
		<div class="cardRow Black-color bold-txt">
		<?php echo translate_phrase('Chat with ');?>
		<?php echo $intro['intro_name'];?> <span class="Red-color">(<?php echo translate_phrase('chat line expires on ').date(DATE_FORMATE,strtotime($intro['intro_expiry_time']));?>)</span>
		</div>
		<?php if(!$intro['chat_history']):?>
			<div class="cardRow Black-color">
			<?php echo $intro['intro_name'].translate_phrase(' is waiting to hear from you, so go ahead and say hello.');?>
			</div>
		<?php endif;?>
		
		<div class="Chat-area-m" id="chat_container_<?php echo $intro['user_intro_id']?>" style="display:<?php echo ($intro['chat_history'])?'block':'none';?>">
			<div class="Chat-area-Inner">
				<ul class="chat-list"
					id="mgs_conversation_<?php echo $intro['user_intro_id']?>">
				<?php if($intro['chat_history']):?>
					<?php $print_date = '';?>
					
					<?php foreach ($intro['chat_history'] as $msg):?>
					<?php $cur_msg_time = date('Y-m-d',strtotime($msg['chat_message_time']));?>
					
					<?php if($print_date != $cur_msg_time):?>
					<li class="Month-section"><?php echo date(DATE_FORMATE,strtotime($msg['chat_message_time']));?>
					</li>
					<?php $print_date=$cur_msg_time; endif;?>
					<li>
						<div class="Time-line">
							<div class="Time-section">
							<?php echo date('h:i A',strtotime($msg['chat_message_time']))?>
							</div>
						</div>
						<div class="susan-chat">
							<span><?php echo ($msg['user_id'] == $user_id) ?translate_phrase('You'):$intro['intro_name'];?>
								: </span>
								<?php echo $msg['chat_message'];?>
						</div>
					</li>
					<?php endforeach;?>
					
					<?php if($cur_msg_time != date('Y-m-d',strtotime('now'))):?>
						<li class="Month-section today-date" style="display: none;"><?php echo date(DATE_FORMATE,strtotime('now'));?></li> 
					<?php endif;?>
				<?php else:?>
					<li class="Month-section today-date" style="display: none;"><?php echo date(DATE_FORMATE,strtotime('now'));?></li>
				<?php endif;?>
				
				</ul>
			</div>
		</div>
		<div class="Send-area-m" id="btn_group_<?php echo $intro['user_intro_id']?>" style="display:<?php echo ($intro['chat_history'])?'block':'block';?>">
			<input type="text" class="chat-input" lang="<?php echo $intro['user_intro_id']?>" user_id="<?php echo $intro['user_id']?>" />
			
			<div class="Edit-Button01">
				<a href="javascript:;"
					onclick="send_message($('#btn_group_<?php echo $intro['user_intro_id']?> .chat-input').val(),'<?php echo $intro['user_intro_id']?>','<?php echo $intro['user_id']?>')"><?php echo translate_phrase('Send')?>
				</a>
			</div>
			<div class="respond-now-txt chat-error-msg" lang="<?php echo translate_phrase('Please ') ?> <a class='blu-color' href='<?php echo base_url() . url_city_name() ?>/upgrade-account.html'><?php echo translate_phrase('purchase the unlimited date tickets account upgrade') ?></a> <?php echo translate_phrase('or') ?> <a href='<?php echo base_url() . url_city_name() ?>/get-more-tickets.html' class='blu-color'><?php echo translate_phrase('use a date ticket') ?></a> <?php echo translate_phrase('to begin sending messages to ').$intro['intro_name'] ?>"></div>		
		</div>
		
	</div>
