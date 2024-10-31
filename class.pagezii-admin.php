<?php

class Pagezii_Admin {

	private static $initiated = false;

	public static function init() {
		self::init_hooks();
	}

	public static function init_hooks() {
		self::$initiated = true;
		add_action( 'admin_menu', array( 'Pagezii_Admin', 'my_plugin_menu') );
		add_action( 'admin_enqueue_scripts', array( 'Pagezii_Admin', 'load_resources' ) );
	}

	public static function load_resources(){
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'pagezii-snapshot-scripts', plugins_url( 'js/scripts.js', __FILE__ ), array( 'wp-color-picker', 'jquery' ), PAGEZII_SNAPSHOT_VERSION, true );
		wp_register_style( 'pagezii.css', plugin_dir_url( __FILE__ ) . 'css/pagezii.css', array(), PAGEZII_SNAPSHOT_VERSION );
		wp_enqueue_style( 'pagezii.css');
	}

	public static function my_plugin_menu() {
		$capabilities = 'manage_options';
		$icon_url =  plugin_dir_url( __FILE__ ). 'images/logo-small.png';
		$position = null;
		add_menu_page( 'Agency Settings', 'Pagezii', $capabilities, 'pzseo-agency', array( 'Pagezii_Admin', 'agency_menu'), $icon_url, $position );
		add_submenu_page( 'pzseo-agency', 'Email Settings', 'Email Settings', $capabilities, 'pzseo-emailsettings', array( 'Pagezii_Admin', 'email_menu') );
		add_submenu_page( 'pzseo-agency', 'General Settings', 'General Settings', $capabilities, 'pzseo-generalsettings', array( 'Pagezii_Admin', 'general_menu') );
		global $submenu;
		if ( current_user_can( 'manage_options' ) )  {
			$submenu['pzseo-agency'][0][0] = __( 'Agency Setup', 'pzseo-agency' );
		}
	}

	public static function agency_menu() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		if($_POST){
			$pz_settings = self::update_pz_settings('agency');
		} else {
			$pz_settings = self::get_pz_settings();
		}
		require_once( PAGEZII__PLUGIN_DIR . 'views/agency-setup.php' );
	}

	public static function email_menu() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		if($_POST){
			$pz_settings = self::update_pz_settings('email');
		} else {
			$pz_settings = self::get_pz_settings();
		}
		require_once( PAGEZII__PLUGIN_DIR . 'views/email-setup.php' );
	}

	public static function general_menu() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		if($_POST){
			$pz_settings = self::update_pz_settings('general');
		} else {
			$pz_settings = self::get_pz_settings();
		}
		require_once( PAGEZII__PLUGIN_DIR . 'views/general-setup.php' );
	}

	public static function update_pz_settings($page){
		$pz_settings = get_option('pagezii_settings', []);

		if($page == 'agency') {
			$pz_settings['company_name'] = (string) sanitize_text_field($_POST['company_name']);
			$pz_settings['company_address'] = (string) sanitize_textarea_field($_POST['company_address']);
			$brandColor = sanitize_hex_color($_POST['color_picker']);
			if($brandColor){
				$pz_settings['color_picker'] = (string) $brandColor;
			} else {
				$pz_settings['color_picker'] = '#35857D';
			}

			// handle logo upload
			$upload_dir = wp_upload_dir(null,false);
			$upload_dir_url = $upload_dir['baseurl'].'/pagezii-snapshot';
			$upload_dir = $upload_dir['basedir'];
			$upload_dir = $upload_dir.'/pagezii-snapshot';
			wp_mkdir_p( $upload_dir );

			$file = isset($_FILES['logobutton']) ? $_FILES['logobutton'] : null;
			if($file && (!$file['error'] || $file['error'] == UPLOAD_ERR_OK)){
				$name = (string) sanitize_file_name($file['name']);
				if(validate_file($name) === 0){
					$new_location = "$upload_dir/$name";
					move_uploaded_file($file['tmp_name'], $new_location);
					$new_location = "$upload_dir_url/$name";
				} else {
					wp_die("Invalid file");
				}
			} else {
				$new_location = $_POST['logo'];
			}
			if($new_location){
				$pz_settings['logo'] = (string) esc_url_raw($new_location);
			} else {
				$pz_settings['logo'] = '';
			}
		} elseif($page == 'email') {
			$pz_settings['from_email'] = (string) sanitize_email($_POST['from_email']);
			$pz_settings['from_name'] = (string) sanitize_text_field($_POST['from_name']);
            if(strpos($_POST['bcc_email'], ',')){
                $emails = $_POST['bcc_email'];
                $validEmails = true;
                foreach(preg_split('/,[\s]*/', $emails) as $val){
                    if(!filter_var($val, FILTER_VALIDATE_EMAIL)){
                        $validEmails = false;
                        break;
                    }
                }
                if($validEmails){
                    $pz_settings['bcc_email'] = (string) sanitize_text_field($_POST['bcc_email']);
                } else {
                    $pz_settings['bcc_email'] = '';
                }
            } else {
                $pz_settings['bcc_email'] = (string) sanitize_email($_POST['bcc_email']);
            }
			$pz_settings['subject'] = (string) sanitize_text_field($_POST['subject']);
			$pz_settings['cover_letter'] = (string) sanitize_textarea_field($_POST['cover_letter']);
		} elseif($page == 'general') {
			$pz_settings['report_snapshot'] = isset($_POST['report_snapshot']) ? (bool) absint($_POST['report_snapshot']) : false;
			$pz_settings['report_home_ux'] = isset($_POST['report_home_ux']) ? (bool) absint($_POST['report_home_ux']) : false;
			$pz_settings['report_home_seo'] = isset($_POST['report_home_seo']) ? (bool) absint($_POST['report_home_seo']) : false;
			$pz_settings['only_business_emails'] = isset($_POST['only_business_emails']) ? (bool) absint($_POST['only_business_emails']) : false;
			$pz_settings['thankyou_page'] =  (string) esc_url_raw($_POST['thankyou_page']);
			$pz_settings['thankyou_message'] =  (string) sanitize_textarea_field($_POST['thankyou_message']);
			$pz_settings['redirect_to_page'] = isset($_POST['redirect_to_page']) ? (bool) absint($_POST['redirect_to_page']) : false;
			$pz_settings['per_ip_limit'] =  (string) sanitize_text_field($_POST['per_ip_limit']);
			$pz_settings['data_sharing'] = isset($_POST['data_sharing']) ? (bool) absint($_POST['data_sharing']) : false;
			$pz_settings['show_powered_by'] = isset($_POST['show_powered_by']) ? (bool) absint($_POST['show_powered_by']) : false;
		}
		update_option('pagezii_settings', $pz_settings);
		return $pz_settings;
	}

	public static function get_pz_settings(){
		$pz_settings = get_option('pagezii_settings');
		if(!$pz_settings){
			$pz_settings = array(
				'company_name' => '',
				'company_address' => '',
				'from_email' => '',
				'from_name' => '',
				'bcc_email' => '',
				'subject' => 'Snapshot Audit report',
				'logo' => '',
				'color_picker' => '#35857D',
				'cover_letter' => '',
				'report_snapshot' => true,
				'report_home_ux' => true,
				'report_home_seo' => true,
				'only_business_emails' => false,
				'thankyou_page' => '',
				'thankyou_message' => "Thank you for submitting your audit request. Your audit report PDF will be sent to the provided email address in a few minutes.",
				'redirect_to_page' => false,
				'per_ip_limit' => 0,
				'data_sharing' => false,
				'show_powered_by' => false,
			);
		}
		return $pz_settings;
	}

}
