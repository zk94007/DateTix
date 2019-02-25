<html>
<head>
<style type="text/css">
    body {
        margin: 0;
        padding: 0;
        background-color: #ccc;
        font: 12pt "Tahoma";
        color:white;
    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    .page {
        width: 21cm;
        min-height: 29.7cm;
        padding: 2cm;
        margin: 1cm auto;
        border: 1px #D3D3D3 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .subpage {
        padding: 1cm;
        border: 2px red solid;
        height: 237mm;
        outline: 2cm #FFEAEA solid;
        background:#000;
    }
    .sticker{
		width:374px;
		/*height:169px;*/
		
		padding:2px;
		
		border:1px solid red;
		margin:2px;
		
		/*
		
		-webkit-transform: rotate(90deg);
		-moz-transform: rotate(90deg);
		-ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
           transform: rotate(90deg);
		filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=1);

	   font-family: arial, helvetica, sans-serif;
	   */
	   
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
	
    @page {
        size: A4;
        margin: 0;
    }
    @media print {
        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
    }
</style>
</head>

<body>
<div class="book">
    <div class="page">
        <div class="subpage">
			<ul>
				<?php if($event_user_data):?>
				<?php foreach($event_user_data as $user):?>
				<li class="sticker">
					<ul>
						<li class="color-yellow"><?php echo $user['user_info']['first_name'];?>'s People to Meet List</li>
						<li>
							<h3 class="color-white">Hi <?php echo $user['user_info']['first_name'];?>, here is your personalized "People to Meet" list for today's event:</h3>
							
							<!--Print Matches-->
							<?php if($user['matches']):?>
							<ul>
								<?php foreach($user['matches'] as $match):?>
								<li class="<?php echo $match['intro_data']['gender_id'] == 1?'color-blue':'color-pink'?>"><?php echo $match['text'];?></li>
								<?php endforeach;?>
							</ul>
							<?php endif;?>
							<!-- END Print Matches-->
						</li>
						<li class="color-white">To contact your above matches or view their profiles after the event, simply visit www.DateTix.hk  and sign in to your account!</li>
					</ul>
				</li>
				<?php endforeach;?>	
				<?php endif;?>	
			</ul>						
        </div>    
    </div>
</div>
</body>
</html>
