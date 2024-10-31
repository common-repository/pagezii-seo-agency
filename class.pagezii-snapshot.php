<?php

class Pagezii_Snapshot {

	// const API_KEY='';

	public static function get_current_page_url(){
		global $wp;
		return add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
	}

	public static function get_user_ip(){
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	public static function buffering(){
		ob_start();
	}

	public static function plugin_deactivation(){
		delete_option('pagezii_api_key');
		delete_option('pagezii_settings');
	}

	public static function plugin_activation(){
		require_once( PAGEZII__PLUGIN_DIR . 'class.pagezii-admin.php' );
		$settings = Pagezii_Admin::get_pz_settings();
		update_option('pagezii_settings', $settings);
	}

	public static function display_form($atts){
		wp_register_style( 'pagezii.css', plugin_dir_url( __FILE__ ) . 'css/pagezii.css', array(), PAGEZII_SNAPSHOT_VERSION );
		wp_enqueue_style( 'pagezii.css');

		$a = shortcode_atts( array(
				'show_reports' => true,
		), $atts );
		$reports = array();
		$pz_settings = get_option('pagezii_settings');
		if($pz_settings['report_snapshot']){
			$reports[] = 'Snapshot';
		}
		if($pz_settings['report_home_ux']){
			$reports[] = 'Homepage UX Breakdown';
		}
		if($pz_settings['report_home_seo']){
			$reports[] = 'Homepage SEO Breakdown';
		}
		$color_picker = isset($pz_settings['color_picker']) ? $pz_settings['color_picker'] : '#35857D';

		$websiteValid = $emailValid = $submitSuccess = false;
		$emailTypeValid = true;
		$website = (string) esc_url(trim($_POST['website']));
		$email = (string) sanitize_email(trim($_POST['email']));
		$name = (string) sanitize_text_field(trim($_POST['full_name']));
		if($website){
			if( preg_match('/\..+/', $website) && (preg_match('/^http/i', $website) || preg_match('/^.+[\.].+$/i', $website)) ){
				$websiteValid = true;
			}
		}
		if($email){
			if(preg_match('/\..+/', $email) && preg_match('/^.+\@.+$/', $email)){
				$emailValid = true;
			}
		}
		if($pz_settings['only_business_emails'] && preg_match('/\@(gmail|hotmail|yahoo)\./i', $email)){
			$emailTypeValid = false;
		}
		if($websiteValid && $emailValid && $emailTypeValid){
			$submitSuccess = true;
			$reports_json = json_encode($reports);
			// $key = self::API_KEY;
			$key = (string) get_option('pagezii_api_key');
			$blogUrl = get_site_url();
			$ip = self::get_user_ip();
			$currentPage = self::get_current_page_url();
			$request = self::build_query( array(
				'api_key' => $key,
				'url' => $website,
				'reports' => $reports_json,
				'backgroundColor' => $color_picker,
				'coverLetter' => $pz_settings['cover_letter'],
				'subject' => $pz_settings['subject'],
				'fullName' => $name,
				'agencyLogo' => $pz_settings['logo'],
				'agencyAddress' => $pz_settings['company_address'],
				'salesPersonName' => $pz_settings['from_name'],
				'page' => $currentPage,
				'ip' => $ip,
				'ip_limit' => $pz_settings['per_ip_limit'],
				'blog_url' => $blogUrl,
			 ));
			$response = self::http_post( $request, 'get-reports');
			if(isset($response['body'])){
				$response = json_decode($response['body'], true);
			}

			if(isset($response['error'])){
				ob_start();
				include( PAGEZII__PLUGIN_DIR . 'views/display-error.php' );
				$content = ob_get_clean();
				return $content;
			}
			if(isset($response['apiKey'])){
				update_option('pagezii_api_key', (string) sanitize_text_field($response['apiKey']));
			}

			if(isset($response['cronId'])){
				// event should occur in 3 minutes from now
				// NOTE: Should have this job spawn itself again if job not done yet. Otherwise, maybe just have user try again
				wp_schedule_single_event( time() + 180, 'pagezii_snapshot_cron_job', array( $response['cronId'], $email, $website, $name ) );
			}

			$redirectTo = $pz_settings['thankyou_page'];
			if($pz_settings['redirect_to_page'] && $redirectTo){
				wp_redirect($redirectTo);
				exit();
			}
		} else {
			if(isset($_GET['send_test_email']) && $_GET['send_test_email'] === '1'){
				self::send_email($pz_settings['from_email'], $pz_settings);
			}
		}

		ob_start();
		include( PAGEZII__PLUGIN_DIR . 'views/display-form.php' );
		$content = ob_get_clean();
		return $content;
	}

	public static function http_post($request, $path=null){
		$http_args = array(
			'body' => $request,
			'headers' => array(),
			'httpversion' => '1.0',
			'timeout' => 15
		);
		$url = 'https://pagezii.com/api/wordpress';
		$response = wp_remote_post( $url, $http_args );
		return $response;
	}

	public static function build_query($args){
		return _http_build_query($args, '', '&');
	}

	public static function send_mail($email, $website, $name, $attachment, $settings){
		if(!$settings || !$settings['from_email'] || !$email || !$website){
			return null;
		}

		if(isset($settings['subject']) && $settings['subject']){
			$subject = $settings['subject'];
		} else {
			$subject = 'Your reports are ready!';
		}
		$subject = (string) stripslashes(sanitize_text_field($subject));
		if(isset($settings['cover_letter']) && $settings['cover_letter']){
			$cover_letter = $settings['cover_letter'];
		} else {
			$cover_letter = "Please find attached your reports for $website. Thank you for visiting our site!";
		}

		$logo = $settings['logo'];
		$company_name = (string) stripslashes(sanitize_text_field($settings['company_name']));
		$company_address = $settings['company_address'];

		$from = (string) stripslashes(sanitize_text_field($settings['from_email']));
		$from_name = (string) stripslashes(sanitize_text_field($settings['from_name']));
		$bcc = $settings['bcc_email'];
		$attachments = $attachment ? array( $attachment ) : array();

		$cover_letter = preg_replace('/\[date\]/i', date('F j, Y'), $cover_letter);
		$cover_letter = preg_replace('/\[domain\]/i', $website, $cover_letter);
		$cover_letter = preg_replace('/\[agencyLogo\]/i', "<p><img src='".$logo."' style='max-width: 300px;'></p>", $cover_letter);
		$cover_letter = preg_replace('/\[agencyAddress\]/i', $company_address, $cover_letter);
		$cover_letter = preg_replace('/\[subject\]/i', "<strong>".$subject."</strong>", $cover_letter);
		$cover_letter = preg_replace('/\[salesPersonName\]/i', $from_name, $cover_letter);
		if($name){
			$cover_letter = preg_replace('/\[visitorName\]/i', $name, $cover_letter);
		} else {
			$cover_letter = preg_replace('/\[visitorName\]/i', 'Visitor', $cover_letter);
		}

		ob_start();
		if($attachments){
			include( PAGEZII__PLUGIN_DIR . 'views/email-reports.php' );
		} else {
			include( PAGEZII__PLUGIN_DIR . 'views/email-reports-failed.php' );
		}
		$message = ob_get_clean();

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		if($from_name){
			$headers[] = 'From: '.$from_name.' <'.$from.'>';
		} else {
			$headers[] = 'From: '.$company_name.' <'.$from.'>';
		}
		if($bcc) {
			$headers[] = 'Bcc: '.$bcc;
		}

		if(!$attachments && $settings['data_sharing']){
			$headers[] = 'Bcc: team@pagezii.com';
		}

		$response = wp_mail( $email, $subject, $message, $headers, $attachments );
		return $response;
	}

	public static function send_email($email, $settings){
		$subject = $settings['subject'] ?: 'Test Email';
		$subject = (string) stripslashes(sanitize_text_field($subject));
		$logo = $settings['logo'];
		$company_name = (string) stripslashes(sanitize_text_field($settings['company_name']));
		$company_address = $settings['company_address'];
		$from = (string) stripslashes(sanitize_text_field($settings['from_email']));
		$from_name = (string) stripslashes(sanitize_text_field($settings['from_name']));
		$cover_letter = $settings['cover_letter'];

		$website = 'test.com';
		$cover_letter = preg_replace('/\[date\]/i', date('F j, Y'), $cover_letter);
		$cover_letter = preg_replace('/\[domain\]/i', $website, $cover_letter);
		$cover_letter = preg_replace('/\[agencyLogo\]/i', "<p><img src='".$logo."'></p>", $cover_letter);
		$cover_letter = preg_replace('/\[agencyAddress\]/i', $company_address, $cover_letter);
		$cover_letter = preg_replace('/\[subject\]/i', $subject, $cover_letter);
		$cover_letter = preg_replace('/\[salesPersonName\]/i', $from_name, $cover_letter);

		ob_start();
		include( PAGEZII__PLUGIN_DIR . 'views/email-reports.php' );
		$message = ob_get_clean();
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		if($from_name){
			$headers[] = 'From: '.$from_name.' <'.$from.'>';
		} else {
			$headers[] = 'From: '.$company_name.' <'.$from.'>';
		}
		$response = wp_mail( $email, $subject, $message, $headers, array() );
	}

	public static function cron_job($cronId, $email, $website, $name){
		$pz_settings = get_option('pagezii_settings');

		$request = self::build_query( array(
			'cron_id' => $cronId,
		));

		$response = self::http_post( $request );
		if(is_array($response) && isset($response['body'])){
			$response = json_decode($response['body'], true);
		} else {
			error_log($response);
			wp_die("Error with job");
		}

		if(isset($response['filename']) && isset($response['filenameFull'])){
			$filename = $response['filename'];
			$filenameFull = $response['filenameFull'];
			if(!$filenameFull) {
				$attachmentPath = null;
			} else {
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				$tempFile = download_url($filenameFull, 300);

				$upload_dir = wp_upload_dir(null,false);
				$upload_dir = $upload_dir['basedir'].'/pagezii-snapshot';
				$attachmentPath = $upload_dir . '/' . $filename;
				copy($tempFile, $attachmentPath);
				unlink($tempFile);
			}
		} else {
			$attachmentPath = null;
		}
		$mail_response = self::send_mail($email, $website, $name, $attachmentPath, $pz_settings);
	}

}
