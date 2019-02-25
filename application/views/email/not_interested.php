<?php  echo $this->load->view('email/include/header'); ?>

<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">

			<div class="content">
				<table>
					<tr>
						<td>
							<h3>Sorry !!</h3>
							<p class="lead">
							<?php echo $user_data['first_name'];?>
								has declined to meet you for a date.
							</p>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
							<?php  echo $this->load->view('email/include/footer'); ?>