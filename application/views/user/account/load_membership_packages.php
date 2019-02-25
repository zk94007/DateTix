
<?php if($ticket_packages):?>
<?php foreach ($ticket_packages as $ke=>$package):?>
<li><a href="javascript:;"
	currency_id="<?php echo $package['currency_id']?>"
        class="<?php echo $ke == $selected_key ? 'selected':'' ?>">
		<div class="selectDate">
			<div class="select-Button01">
			<?php echo translate_phrase('Select')?>
			</div>
		</div>

		<div class="package">
			<div class="package-innr-row">
				<div class="package-innr-row-left"
				<?php if($package['name'] == 12) echo 'id="optionValuePerYearText"'?>>
					<span class="plan_name" lang="<?php echo $package['name']?>"><?php echo $package['description']?>
					</span> - <span class="green-color"><?php echo $package['currency'].number_format($package['per_month_price'])?>
					</span>/
					<?php echo translate_phrase('month')?>
				</div>
				<?php if($package['save_per']):?>
				<div class="selectDateText width-saveTxt font-italic">
				<?php echo translate_phrase('Save ').$package['save_per'].'%';?>
					<sup>1</sup>
				</div>
				<?php endif;?>

				<?php if($package['extra']):?>
				<div class="guaranteeText width-garanteeTxt font-italic">
				<?php echo $package['extra']?>
				</div>
				<?php endif;?>
			</div>
			<div class="guarantee">
				<p>
				<?php echo translate_phrase('One easy ')?>
					<span class="currency"><?php echo $package['currency'];?> </span><span
						class="plan_amount"><?php echo number_format($package['total']);?>
					</span>
					<?php echo translate_phrase('payment ')?>
					<?php if($package['save_amount']) echo '('.translate_phrase('Save ').$package['currency'].number_format($package['save_amount']).')';?>
				</p>
			</div>
		</div> </a>
</li>
					<?php endforeach;?>
					<?php endif;?>