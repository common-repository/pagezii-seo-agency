<div class="wrap">
<h2>Email Settings</h2>

<?php
	if($_POST){
		echo '<div class="wp-core-ui"><div class="notice notice-success"><p style="font-size:16px;">Updated Settings</p></div></div>';
	}
?>

<form method="POST" enctype="multipart/form-data">
	<table class="form-table" id="pagezii-snapshot-form">
		<tr class="form-field form-required">
			<th scope="row"><label for="from_name">Sales Person Name </label></th>
			<td>
				<input name="from_name" type="from_name" id="from_name" value="<?php echo stripslashes(sanitize_text_field($pz_settings['from_name'])); ?>" placeholder="John Smith">
				<p class='text-muted'>The name of the person you want the report to be emailed from.</p>
			</td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="from_email">Sales Person Email </label></th>
			<td>
				<input name="from_email" type="from_email" id="from_email" value="<?php echo sanitize_email($pz_settings['from_email']); ?>" placeholder="jsmith@bestagency-acme.com">
				<p class='text-muted'>The email address for the lead to respond to.</p>
			</td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="subject">Report Subject </label></th>
			<td>
				<input name="subject" type="text" id="subject" value="<?php echo stripslashes(sanitize_text_field($pz_settings['subject'])); ?>" autocapitalize="none" autocorrect="off" maxlength="60" placeholder="Email Subject Line">
				<p class='text-muted'>The subject of the email.</p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="cover_letter">Cover Letter </label></th>
			<?php
			$defaultCoverLetter = "[AgencyLogo]
[AgencyAddress]

[Date]
[Subject]

Dear [VisitorName],
Thank you for requesting a preliminary audit of [Domain].

I've attached an audit report detailing your domain's digital marketing footprint. The report also includes suggestions for potential site improvements.

Please take the time to review the report in detail. I look forward to discussing your digital marketing strategy and how to optimize your efforts moving forward.

Sincerely yours,

[SalesPersonName]

Tel: XXX-XXX-XXXX
Email: email@domain.com";
			 ?>
			<td>
				<textarea style="display:none;" id="defaultCoverLetter"><?php echo sanitize_textarea_field($defaultCoverLetter); ?></textarea>
				<textarea name="cover_letter" id="cover_letter" rows="10" placeholder=""><?php echo stripslashes(sanitize_textarea_field($pz_settings['cover_letter'])) ?: sanitize_textarea_field($defaultCoverLetter); ?></textarea>
				<p class='text-muted'>Cover letter template is included in the email and PDF report.</p>
				<p class='text-muted'>Supported Codes:</p>
				<div id="pagezii-snapshot-email-codes" class="text-muted">
					<div>
						[AgencyLogo]<br>
						[AgencyAddress]<br>
						[Date]<br>
						[Subject]<br>
					</div>
					<div>
						[VisitorName]<br>
						[Domain]<br>
						[SalesPersonName]
					</div>
				</div>
				<p><a class="pz-btn-reset button button-secondary">Reset Email</a></p>
				<p class='text-muted'>Reset email template to default email provided by Pagezii.</p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="bcc_email">BCC Email </label></th>
			<td>
				<input name="bcc_email" type="text" id="bcc_email" value="<?php echo sanitize_text_field($pz_settings['bcc_email']); ?>" placeholder="info@bestagency-acme.com">
				<p class='text-muted'>Enter BCC emails to send the report to (e.g. your other sales or marketing staff)</p>
			</td>
		</tr>

	</table>

	<p class="submit"><input type="submit" name="updatesettings" id="updatesettings" class="button button-primary" value="Update Settings"></p>
</form>

<p>Use the shortcode [pagezii] anywhere on a post or a page after you've setup the plugin.</p>
