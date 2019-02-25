<html>
<head>
<style type="text/css">
    body {
        margin: 0;
        padding: 0;
        
        font: 12pt "Tahoma";
        color:white;
    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    .page {
        width: 30cm;
        min-height: 20cm;
        border: 1px #D3D3D3 solid;
        border-radius: 5px;
        background-color: #FFFFFF;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        font-size:12px;
    }
   
    .sticker{
		float:left;
		display:block;
		height:368px;
		width:181px;
		padding:2px;		
		border:0px solid red;
		margin:2px;
		font-family: arial, helvetica, sans-serif;
	   	overflow:hidden;
	}
	ul,ol{
		list-style:none;
		margin:0;
		padding:0;
	}
	li{
		text-align:left;
		font-weight:bold;
	}
	
	.color-yellow{ color:#000000;}
	.color-red{ color:#000000;}
	.color-blue{ color:#00679A;}
	.color-pink{ color:#ed217c;}
	.color-white{ color:#000000;}
	.margin-top2 {margin-top:2px;}
</style>
</head>

<body>
	<?php if($event_user_data):?>
	<ul class="page">
	<?php 
	$list_no = 1;
	foreach($event_user_data as $key=>$user):?>
	<?php $num_matches=0;?>
	<?php 
		if($key!=0 && $key % 12 == 0)
		{
			$list_no =1;
			echo '</ul>'; //close old flag
			echo '<ul class="page">';
		}
		else
		{
			$list_no++;
		}
	?>
	<li class="sticker" lang="<?php echo $list_no;?>">
		<div class="color-white"><br/><h3>Hi <?php echo $user['user_info']['first_name'];?>, here are some people to meet today:</h3></div>
			<!--Print Matches-->
			<?php if($user['matches']):?>
			<ul>
				<?php foreach($user['matches'] as $match):?>
				<?php $num_matches++;?>
				<?php if($num_matches<=3):?>
				<li class="<?php echo $match['intro_data']['gender_id'] == 1?'color-blue':'color-pink'?>"><br/><?php echo $match['name'];?><?php echo $match['text'];?></li>
				<?php endif;?>
				<?php endforeach;?>
			</ul>
			<?php endif;?>
			<!-- END Print Matches-->
		<div class="color-white margin-top2"><br/><?php echo translate_phrase('To view and chat with all your intros, sign in to your account at')?>
		<font color="#ed217c">
		<?php if(base_url() == "http://datetix.hk/"):?>
		 DateTix.hk
		<?php else:?>		 
		 www.DateTix.com
		<?php endif;?>		 
		</font>
		</div>
	</li>
	<?php endforeach;?>
	
	<?php foreach($event_user_data as $key=>$user):?>
	{
	<li class="sticker" lang="<?php echo $list_no;?>">
		<div class="color-white"><br/><h2><font color=green>HK$200 CREDIT</font> to our next private invite-only event on 1 Aug, 2014</h2></div>
		<div class="color-white"><br/><h3>WhatsApp Michael at 6684-2770 for event details and to RSVP</h3></div>
	</li>
	}
	<?php endforeach;?>
	</ul>	
	<?php endif;?>
</body>
</html>
