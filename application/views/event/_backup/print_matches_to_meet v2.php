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
        width: 25cm;
        min-height: 20cm;
        border: 1px #D3D3D3 solid;
        border-radius: 5px;
        background-color: #000;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        font-size:12px;
    }
   
    .sticker{
		float:left;
		display:block;
		height:368px;
		width:152px;
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
	
	.color-yellow{ color:yellow;}
	.color-red{ color:red;}
	.color-blue{ color:#61bcea;}
	.color-pink{ color:#ed217c;}
	.color-white{ color:white;}
	.margin-top2 {margin-top:2px;}
</style>
</head>

<body>
	<?php if($event_user_data):?>
	<ul class="page">
	<?php foreach($event_user_data as $key=>$user):?>
	<?php $num_matches=0;?>
	<?php 
		if($key!=0 && $key % 12 == 0)
		{
			echo '<ul class="page">';
		}
	?>
	<li class="sticker">
		<font color="white"><br/>Hi <?php echo $user['user_info']['first_name'];?>, here are the top 3 people you should find and chat with today:</font>
			<!--Print Matches-->
			<?php if($user['matches']):?>
			<ul>
				<?php foreach($user['matches'] as $match):?>
				<?php $num_matches++;?>
				<?php if($num_matches<=3):?>
				<li class="<?php echo $match['intro_data']['gender_id'] == 1?'color-blue':'color-pink'?>"><br/><font color="yellow"><?php echo $match['name'];?></font><?php echo $match['text'];?></li>
				<?php endif;?>
				<?php endforeach;?>
			</ul>
			<?php endif;?>
			<!-- END Print Matches-->
		<div class="color-white margin-top2"><br/><?php echo translate_phrase('To view your complete "People to Meet" list and contact them, sign in to your account at')?>
		<?php if(base_url() == "http://datetix.hk/"):?>
		 www.DateTix.hk
		<?php else:?>		 
		 www.DateTix.com
		<?php endif;?>		 
		</font>
	</li>
	<?php 
		if($key!=0 && $key % 11 == 0)
		{
			echo "</ul>";
		}
	?>
	<?php endforeach;?>
	</ul>	
	<?php endif;?>
</body>
</html>
