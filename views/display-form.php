<div style="max-width: 1000px;" id="pagezii-display-form">
	<?php
	if($submitSuccess){
		echo "<p>";
		echo $pz_settings['thankyou_message'] ? nl2br(stripslashes(sanitize_textarea_field($pz_settings['thankyou_message']))) : "Thank you for submitting your audit request. Your audit report PDF will be sent to the provided email address in a few minutes.";
		echo "</p>";
	} else {
		if($_POST['website'] && !$websiteValid){
			echo "<p class='user-error'>Please enter a valid website.</p>";
		}
		if($_POST['email'] && !$emailTypeValid){
			echo "<p class='user-error'>Please enter a valid business email address.</p>";
		} elseif($_POST['email'] && !$emailValid){
			echo "<p class='user-error'>Please enter a valid email address.</p>";
		}
	}
	?>

	<form method="POST" style="margin-top: 20px;">
		<table class="form-table">
			<tbody>
				<tr class="form-field form-required">
					<th scope="row"><label for="website">Website </label></th>
					<td><input name="website" type="text" id="website" autocapitalize="none" autocorrect="off" placeholder="Website to Analyze" value="<?php echo sanitize_text_field($_POST['website']); ?>" required></td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="full_name">Name </label></th>
					<td><input name="full_name" type="text" id="full_name" autocapitalize="none" autocorrect="off" placeholder="Your Name" value="<?php echo sanitize_text_field($_POST['full_name']); ?>" ></td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="email">Email Address </label></th>
					<td><input name="email" type="text" id="email" autocapitalize="none" autocorrect="off" placeholder="Your Business Email Address" value="<?php echo sanitize_text_field($_POST['email']); ?>" required></td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submitform" id="submitform" class="button button-primary" value="Email Audit Report"></p>
		<?php
		if($pz_settings['show_powered_by']){
			echo "<p class='pagezii-snapshot-link'>Powered by <a href='https://pagezii.com' target='_blank'>Pagezii</a></p>";
		}
		?>
	</form>
</div>
