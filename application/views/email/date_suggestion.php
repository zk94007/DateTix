<?php  echo $this->load->view('email/include/header'); ?>
<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">

			<div class="content">
				<table>
					<tr>
						<td>
							<h3>
							<?php echo $email_title;?>
							</h3>
							<p class="lead">
							<?php echo $email_content;?>
							</p>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
<?php  echo $this->load->view('email/include/footer'); ?>