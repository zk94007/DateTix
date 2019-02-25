<?php if(isset($users) && $users):?>
<?php foreach($users as $user):?>
<div class="userBox" lang="<?php echo $user['user_id'];?>">
	<?php include APPPATH.'views/franchise/include_member_info.php';?>
</div>
<?php endforeach;?>
<?php endif;?>
