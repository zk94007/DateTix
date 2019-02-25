<?php
$user_id = $this->session->userdata('user_id');
//$is_premius_member = $this->datetix->is_premium_user($user_id, PERMISSION_INSTANT_INTRO);
?>
<?php 
	$current_profileurl = base_url() . 'user/user_info/' . $this->utility->encode($user_id); 
	if($chat_history['current_photo'])
	{
		$profile_photo = $chat_history['current_photo']['0']['url']; 
	}
	else {
		$profile_photo = base_url('404.jpg');
	}
	?>
<!--*********Page start*********-->

<script type="text/javascript">
var is_shift_pressed = false;
$( document ).ready(function() { 
	$('#reply_message').focus();
	var chatContainerHeight = $(window).height() - $("#chatHeader").height();	
	//$("#chatContainer").css('height',chatContainerHeight);	
	
	
	/*
	 Chat box Js
	 */
	
	//Keydown events for
	$('#reply_message').live('keyup',function(e) {
		e.stopPropagation();
		switch (e.keyCode) {
			case 16:
				is_shift_pressed = true;
			break;
			default:
				break;
		}
	});
	
	$('#reply_message').live('keydown',function(e) {
		//stopevent propagation
		e.stopPropagation();
		switch (e.keyCode) 
		{
			case 13:
				sendReplyMessage();
				break;
			default:
				break;
		}
	});
	
});
function sendReplyMessage(){
    var otherid=$('#other_user_id').val();
    var msg = $('#reply_message').val();
    
    if(msg==''){
       var popupHTML=$('#errorMesssageDiv').html();
        openFancybox(popupHTML);
        return false;
   }else{
   	//Show current time
	var currentTime = new Date();
	var hours = currentTime.getHours();
	var minutes = currentTime.getMinutes();
	if (minutes < 10)
		minutes = "0" + minutes;
	
	var suffix = "AM";
	if (hours >= 12) {
		suffix = "PM";
		hours = hours - 12;
	}
	
	if (hours == 0) {
		hours = 12;
	}
		
   	loading();
	$.ajax({
        url: '<?php echo base_url(); ?>' + "dates/sendChatMessage",
        type: "post",
        data: {otherid: otherid,msg:msg},
        cache: false,
        dataType:'json',
        success: function (data) {
            stop_loading();  
            if(data.type = 'success')
            {            	
            	var response = '<li class="self"> <div class="avatar"> <a href="<?php echo $current_profileurl;?>"> <img src="<?php echo $profile_photo;?>"> </a> </div> <div class="messages"> <p>'+data.chat_msg+'</p> <span> '+ hours + ':' + minutes + ' ' + suffix + '</span> </div> </li>';   	
				$('#reply_message').val('');
	            $("#mgs_conversation").append(
					$(response).hide().slideDown("slow",function(){scrollToLastLi();})
				);
            }
        }
    }); 
         
   } 
}
function scrollToLastLi()
{

		$("#mgs_conversation").parent().animate({
			scrollTop:$("#mgs_conversation li:last-child").offset().top+ 'px'
		},200);
	
}
</script>
<div class="wrapper">
    <div class="content-part  mobile-layout">
        <div class="Apply-Step1-a-main" style="margin-bottom: 20px;">
            <div class="userBox" id="chatHeader">
                <div class="dateRow">		
					<div class="userBoxLeft">
						<?php $profileurl=base_url() . 'user/user_info/' . $this->utility->encode($chat_history['other_user_id']); ?>
						<?php 
						if($chat_history['other_photo'])
						{
							$other_url_profile_photo = $chat_history['other_photo']['0']['url']; 
						}
						else {
							$other_url_profile_photo = base_url('404.jpg');
						}
						?>
						
			            <a href="<?php echo $profileurl;?>">
			                <img class="img-circle user-img" alt="img"  src="<?php echo $other_url_profile_photo;?>">
			            </a>
			            		
					</div>
					<div class="userBoxRight">
						<div class="div-row ">
							<div class="fl left width-50 mar-R">
				                <?php echo $chat_history['other_first_name'];?>
				            </div>					
				            <div class="txt-left mar-left2">
				                <div class="astro <?php echo $chat_history['user_gender']=='Male'?'male':'female'?>">
				                    <span></span><?php echo $chat_history['user_age']?></div>                                    
				            </div>
						</div> 
						<?php if($interaction_detail):?>
						<div class="box-line normal-text">
							<a class="blue-colr" href="<?php echo base_url('dates/view_date/'.$interaction_detail['date_id']);?>">
							<?php echo translate_phrase('First Connected Via ').$interaction_detail['date_type']; ?> @ <?php echo ($interaction_detail['name']) ; ?>
		                    <?php echo translate_phrase('on ').date('D, jS M, Y @ h:i A', strtotime($interaction_detail['date_time'])); ?>                    
		                    </a>
						</div>
		                <?php endif;?>
					</div>
				</div>                    
            </div>
            <hr>
            <div class="emp-B-tabing-prt" id="userDateListing">                        
                <section class="module" id="chatContainer">
                    <ol class="discussion" id="mgs_conversation">
                        <?php foreach ($chat_history['chat_detail'] as $key => $val): ?>  
                        
                        <?php if(date('Y-m-d',strtotime($chat_history['chat_detail'][$key]['chat_message_time']))==date('Y-m-d',strtotime(@$chat_history['chat_detail'][$key-1]['chat_message_time']))): ?>
                        <?php else:?>                            
                        <li class="time">
                           <p class="chatTime"><?php echo chat_date_detail(($val['chat_message_time'])); ?></p>
                        </li>   
                        <?php endif;?>
                        
                        
                            <?php if ($val['from_user_id'] == $user_id): ?>                                                       
                                <li class="self">
                                    <div class="avatar">
                                        <a href="<?php echo $current_profileurl;?>">
											<img src="<?php echo $profile_photo; ?>" />
                                        </a>
                                    </div>
                                    <div class="messages">
                                        <p><?php echo show_chat_txt($val['chat_message']); ?></p>
                                        <span><?php echo date('h:i A', strtotime($val['chat_message_time'])); ?></span>
                                    </div>                                    
                                </li>                                
                            <?php else: ?>     
                                <li class="other">
                                    <div class="avatar">
                                        <a href="<?php echo $profileurl;?>">
                                        <img src="<?php echo $other_url_profile_photo;?>" />
                                        </a>
                                    </div>
                                    <div class="messages">
                                        <p><?php echo show_chat_txt($val['chat_message']); ?></p>
                                        <span><?php echo date('h:i A', strtotime($val['chat_message_time'])); ?></span>
                                    </div>
                                </li>                                
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </ol>
                </section>
            </div>
            
            <hr>
            <div  style="width:100%;float:left;margin-bottom: 10%">
                <div  style="width:100%;float:left">
                        <input type="hidden" name="other_user_id" id="other_user_id"value="<?php echo $other_user?>"/>
                        <input type="hidden" name="current_user_id" id="current_user_id"value="<?php echo $current_user?>"/>
                        <p class="Red-color txt-left bold-txt mar-top2">
                            <?php if($chat_history['expiry_date']) :?>
                                Chat line closes on <?php echo date('D, jS M, Y @ h:i A', strtotime($chat_history['expiry_date']));?>!
                            <?php endif;?>
                        </p>
                </div>
                
                <div class="Send-area-m no-border-background no-padding">
					<input name="reply_message" id="reply_message"  type="text" class="chat-input"/>
					
					<div class="Edit-Button01">
						<a href="javascript:;" id="send_reply" onclick="sendReplyMessage();"  ><?php echo translate_phrase('Send')?>
						</a>
					</div>
					
				</div>
				
                
            </div>
            
            <div id="errorMesssageDiv" style="display:none">
                <h2>You can't send an empty message.</h2>
                <div class="column-100">
                    <div class="Nex-mar">
                        <input type="button" onclick="$.fancybox.close();" class="btn btn-blue disable-butn right" value="Ok">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--*********Page close*********-->
