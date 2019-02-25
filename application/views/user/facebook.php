<a href="<?php echo $fb_login_url ?>" class="facebook-image"
	title="<?php echo translate_phrase('Apply Using Facebook') ?>"><?php echo translate_phrase('Apply Using Facebook') ?>
</a>
<div class="h-separator">
	<span><?php echo translate_phrase('or Apply Without Facebook') ?> </span>
</div>
<div id="register-popup">
<?php echo form_open('home/start_register', array('id' => 'form-register'));?>
	<input type="text" placeholder="Your email address" class="e-mail"
		name="email" id="email"> <input type="text"
		placeholder="Your first name" class="first-name" name="first_name"
		id="first_name"> <input type="text" placeholder="Your last name"
		class="last-name" name="last_name" id="last_name">
	<button type="submit" title="Apply" class="button darkblue"
		type="submit">
		<?php echo translate_phrase('Apply')?>
	</button>
	<?php echo form_close(); ?>
</div>
<script type="text/javascript">
    $("#form-register").submit(function(e) {
        e.preventDefault();
        $(".suc-msg").remove();
        jQuery.ajax({
            url: '/home/start_register/',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(register) {
                if (register.success == 1) {
                    window.location.href = '/email-verification/' + register.user_id;
                } else {
                    $("#form-register").prepend(register.message);
                }
                $.colorbox.resize();
            }
        });
    });
</script>
