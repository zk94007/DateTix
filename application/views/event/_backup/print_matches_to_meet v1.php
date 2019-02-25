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
		border:1px solid red;
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
	.color-blue{ color:blue;}
	.color-pink{ color:pink;}
	.color-white{ color:white;}
	.margin-top2 {margin-top:2px;}
</style>
</head>

<body>
	<?php if($event_user_data):?>
	<ul class="page">
	<?php foreach($event_user_data as $key=>$user):?>
	<?php 
		if($key!=0 && $key % 12 == 0)
		{
			echo '<ul class="page">';
		}
	?>
	<li class="sticker">
		<div class="color-yellow">Hi <?php echo $user['user_info']['first_name'];?>, here is your personalized "People to Meet" list for today's event:</div>
			<!--Print Matches-->
			<?php if($user['matches']):?>
			<span class="color-white margin-top2">Hi <?php echo $user['user_info']['first_name'];?>, here is your personalized "People to Meet" list for today's event:</span>					
			<ul>
				<?php foreach($user['matches'] as $match):?>
				<li class="<?php echo $match['intro_data']['gender_id'] == 1?'color-blue':'color-pink'?>"><?php echo $match['text'];?></li>
				<?php endforeach;?>
			</ul>
			<?php endif;?>
			<!-- END Print Matches-->
		<div class="color-white margin-top2">To contact your above matches or view their profiles after the event, simply visit www.DateTix.hk  and sign in to your account!</div>
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
