<div class="wrap">
<h2>General Settings</h2>

<?php
	if($_POST){
		echo '<div class="wp-core-ui"><div class="notice notice-success"><p style="font-size:16px;">Updated Settings</p></div></div>';
	}
?>

<form method="POST" enctype="multipart/form-data">
	<table class="form-table" id="pagezii-snapshot-form">

		<tr class="form-field">
			<th scope="row"><label>What to include in the Report PDF</label></th>
			<td colspan="1">
				<table id="report-types">
					<tr>
						<td style="padding-left:0;padding-top:0;">
							<p><input type="checkbox" name="report_snapshot" id="report_snapshot" value="1" <?php echo $pz_settings['report_snapshot']!=false?'checked="checked"':'';?> >
							<label for="report_snapshot">Initial Snapshot (Typically 2 pages long)</label></p>
							<p><input type="checkbox" name="report_home_seo" id="report_home_seo" value="1" <?php echo $pz_settings['report_home_seo']!=false?'checked="checked"':'';?> >
							<label for="report_home_seo">Homepage SEO (Typically 5 pages long)</label></p>
							<p><input type="checkbox" name="report_home_ux" id="report_home_ux" value="1" <?php echo $pz_settings['report_home_ux']!=false?'checked="checked"':'';?> >
							<label for="report_home_ux">Homepage UX (Typically 5 pages long)</label></p>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row"><label for="only_business_emails">Prevent Personal Email Address </label></th>
			<td>
				<p><input type="checkbox" name="only_business_emails" id="only_business_emails" value="1" <?php echo $pz_settings['only_business_emails']!=false?'checked':''; ?> >
					<label for="only_business_emails">Ensure Visitor Email Address is not Gmail, Hotmail or Yahoo</label>
				</p>
			</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row"><label for="per_ip_limit">Limit Snapshots to Visitor IP Address </label><br>
			<span style="font-size:12px; font-weight: normal;">(Maximum of 15 Snapshots allowed per day)</span></th>
			<td>
				<?php
					$ip_limit = $pz_settings['per_ip_limit'];
					foreach([0,1,2,3] as $i){
						$ipLimit[$i] = $ip_limit == $i ? 'checked' : '';
					}
				?>
				<p><input type="radio" name="per_ip_limit" value="1" <?php echo $ipLimit[1];?>> Limit to 1 per day per IP address</p>
				<p><input type="radio" name="per_ip_limit" value="2" <?php echo $ipLimit[2];?>> Limit to 2 per day per IP address</p>
				<p><input type="radio" name="per_ip_limit" value="3" <?php echo $ipLimit[3];?>> Limit to 3 per day per IP address</p>
				<p><input type="radio" name="per_ip_limit" value="0" <?php echo $ipLimit[0];?>> No limit</p>
			</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row"><label for="thankyou_page">Report Confirmation Page </label></th>
			<td>
				<input name="redirect_to_page" type="checkbox" id="redirect_to_page" value="1" <?php echo $pz_settings['redirect_to_page']!=false?'checked':''; ?> > <label for="redirect_to_page">Send user to confirmation page url:</label>
				<br>
				<input name="thankyou_page" type="text" id="thankyou_page" value="<?php echo esc_url($pz_settings['thankyou_page']); ?>" autocapitalize="none" autocorrect="off" placeholder="https://yourblog.com/confirmation-page" style="margin-top: 10px;">
				<p class='text-muted'>URL to send visitor after they request an audit</p>
				<br>
				<p><label for="thankyou_message">Or show them a confirmation message on the same page.</label></p>
				<textarea name="thankyou_message" id="thankyou_message" autocapitalize="none" autocorrect="off" rows="5" placeholder=""><?php echo stripslashes(sanitize_textarea_field($pz_settings['thankyou_message'])); ?></textarea>
			</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row"><label for="data_sharing">Error Data Sharing </label></th>
			<td>
				<p><input type="checkbox" name="data_sharing" id="data_sharing" value="1" <?php echo $pz_settings['data_sharing']!=false?'checked':''; ?> >
					<label for="data_sharing">Opt-in to share error data to help improve Pagezii plugin</label>
				</p>
			</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row"><label for="show_powered_by">Show 'Powered by Pagezii' </label></th>
			<td>
				<p><input type="checkbox" name="show_powered_by" id="show_powered_by" value="1" <?php echo $pz_settings['show_powered_by']!=false?'checked':''; ?> >
					<label for="show_powered_by">Help support the plugin by including a 'Powered by Pagezii' link beneath the audit form</label>
				</p>
			</td>
		</tr>

	</table>

	<p class="submit"><input type="submit" name="updatesettings" id="updatesettings" class="button button-primary" value="Update Settings"></p>
</form>

<p>Use the shortcode [pagezii] anywhere on a post or a page after you've setup the plugin.</p>
