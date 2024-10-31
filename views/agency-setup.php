<div class="wrap">
<h2>Your Agency's Information</h2>

<?php
	if($_POST){
		echo '<div class="wp-core-ui"><div class="notice notice-success"><p style="font-size:16px;">Updated Settings</p></div></div>';
	}
?>

<form method="POST" enctype="multipart/form-data">
	<table class="form-table" id="pagezii-snapshot-form">
		<tbody>
		<tr class="form-field form-required">
			<th scope="row"><label for="company_name">Agency Name </label></th>
			<td>
				<input name="company_name" type="text" id="company_name" value="<?php echo stripslashes(sanitize_text_field($pz_settings['company_name'])); ?>" autocapitalize="none" autocorrect="off" maxlength="60" placeholder="Best Agency-Acme">
				<p class='text-muted'>Name of your Agency.</p>
			</td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="company_address">Agency Address </label></th>
			<td>
				<textarea name="company_address" id="company_address" rows="5" placeholder="101 Main Street, Kalamazoo, ON K0K 2A1"><?php echo stripslashes(sanitize_textarea_field($pz_settings['company_address'])); ?></textarea>
				<p class='text-muted'>Physical mailing address of your Agency. This is included in the cover letter to provide a level of professionalism to set your agency apart from the rest.</p>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="logo">Agency Logo</label></th>
			<td>
				<input name="logo" type="url" id="logo" value="<?php echo esc_url($pz_settings['logo']); ?>" style="max-width: 260px;" readonly>
				<input type="hidden" name="MAX_FILE_SIZE" value="1200000" />
				<input type="file" id="logobutton" name="logobutton" style="display:none;">
				<label for="logobutton" type="button" class="button hide-if-no-js">Browse...</label>
				<p class='text-muted'>Choose a logo 200 px wide, preferably against a white background.</p>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row"><label for="color_picker">Brand Color</label></th>
			<td>
				<input name="color_picker" id="color_picker" value="<?php echo sanitize_hex_color($pz_settings['color_picker']);?>">
				<p class='text-muted'>Choose a brand color to accent the PDF report.</p>
			</td>
		</tr>


		</tbody>
	</table>

	<p class="submit"><input type="submit" name="updatesettings" id="updatesettings" class="button button-primary" value="Update Settings"></p>
</form>

<p>Use the shortcode [pagezii] anywhere on a post or a page after you've setup the plugin.</p>
