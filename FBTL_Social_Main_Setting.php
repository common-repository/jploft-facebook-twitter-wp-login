<?php

/*
Plugin Name: Facebook-Twitter-Wp-Login
Plugin URI: http:#
Description: Login wordpresss admin with your facebook and twitter account.
Author: <a href="http://jploft.com" target="_blank">Jploft Solutions Pvt. Ltd.</a>
Version: 1.1
Author URI: http://jploft.com
License:  GPL2
License URI: 

*/
/*-------------------------------------------------------*/
/* Enqueue scripts
/*-------------------------------------------------------*/


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}


require('FBTL_jpl_social_page_settings.php');
include_once dirname( __FILE__ ) . '/FBTL_Login_Wp_Widg.php';
class FBTL_NewId_Social {

	function __construct() {

		add_action( 'admin_menu', array( $this, 'fbtlsocial_openid_menu' ) );
		add_filter( 'plugin_action_links', array($this, 'FBTL_openid_plugin_actions'), 10, 2 );
		add_action( 'admin_init',  array( $this, 'fbtlsocial_openid_save_settings' ) );

		add_action( 'plugins_loaded',  array( $this, 'FBTL_login_widget_text_domain' ) );
        add_action( 'plugins_loaded',  array( $this, 'FBTL_openid_plugin_update' ),1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'FBTL_openid_plugin_settings_style' ) );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'FBTL_openid_plugin_settings_style' ) ,5);
		add_action( 'wp_enqueue_scripts', array( $this, 'FBTL_openid_plugin_script' ) ,5);
        

		register_deactivation_hook(__FILE__, array( $this, 'FBTL_openid_deactivate'));
		register_activation_hook( __FILE__, array( $this, 'FBTL_openid_activate' ) );

		// add social login icons to default login form
		if(get_option('FBTL_openid_default_login_enable') == 1){
			add_action( 'login_form', array($this, 'FBTL_openid_add_social_login') );
			add_action( 'login_enqueue_scripts', array( $this, 'FBTL_custom_login_stylesheet' ) );
		}

		// add social login icons to default registration form
		if(get_option('FBTL_openid_default_register_enable') == 1){
			add_action( 'register_form', array($this, 'FBTL_openid_add_social_login') );
            add_action('login_enqueue_scripts', array( $this, 'FBTL_custom_login_stylesheet'));
		}

		//add shortcode
		add_shortcode( 'fbtlsocial_social_login', array($this, 'FBTL_get_output') );
		

		// add social login icons to comment form
		if(get_option('FBTL_openid_default_comment_enable') == 1 ){
			add_action('comment_form_must_log_in_after', array($this, 'FBTL_openid_add_social_login'));
			add_action('comment_form_top', array($this, 'FBTL_openid_add_social_login'));
		}

		//add social login to woocommerce
		if(get_option('FBTL_openid_woocommerce_login_form') == 1){
			add_action( 'woocommerce_login_form_end', array($this, 'FBTL_openid_add_social_login'));
		}
        if(get_option('FBTL_openid_woocommerce_before_login_form') == 1){
            add_action( 'woocommerce_login_form_start', array($this, 'FBTL_openid_add_social_login'));
        }
        if(get_option('FBTL_openid_woocommerce_center_login_form') == 1){
            add_action( 'woocommerce_login_form', array($this, 'FBTL_openid_add_social_login'));
        }
        if(get_option('FBTL_openid_woocommerce_register_form_start') == 1){
            add_action( 'woocommerce_register_form_start', array($this, 'FBTL_openid_add_social_login'));
        }
        if(get_option('FBTL_openid_woocommerce_center_register_form') == 1){
            add_action( 'woocommerce_register_form', array($this, 'FBTL_openid_add_social_login'));
        }
        if(get_option('FBTL_openid_woocommerce_register_form_end') == 1){
            add_action( 'woocommerce_register_form_end', array($this, 'FBTL_openid_add_social_login'));
        }
        if(get_option('FBTL_openid_woocommerce_before_checkout_billing_form') == 1){
            add_action( 'woocommerce_before_checkout_billing_form', array($this, 'FBTL_openid_add_social_login'));
        }
        if(get_option('FBTL_openid_woocommerce_after_checkout_billing_form') == 1){
            add_action( 'woocommerce_after_checkout_billing_form', array($this, 'FBTL_openid_add_social_login'));
        }

        //add social login to buddypress
        if(get_option('FBTL_openid_bp_before_register_page') == 1){
            add_action( 'bp_before_register_page', array($this, 'FBTL_openid_add_social_login'));
        }
        if(get_option('FBTL_openid_bp_before_account_details_fields') == 1){
            add_action( 'bp_before_account_details_fields', array($this, 'FBTL_openid_add_social_login'));
        }
        if(get_option('FBTL_openid_bp_after_register_page') == 1){
            add_action( 'bp_after_register_page', array($this, 'FBTL_openid_add_social_login'));
        }

		if(get_option('FBTL_openid_logout_redirection_enable') == 0){
			remove_filter( 'logout_url', 'FBTL_openid_redirect_after_logout');
		}

		if(get_option('FBTL_share_options_wc_sp_summary') == 1){
			add_action('woocommerce_after_single_product_summary', array( $this, 'FBTL_openid_social_share' ));
		}

		if(get_option('FBTL_share_options_wc_sp_summary_top') == 1){
			add_action('woocommerce_single_product_summary', array( $this, 'FBTL_openid_social_share' ));
		}

		if(get_option('FBTL_openid_social_comment_fb') == 1 || get_option('FBTL_openid_social_comment_google') == 1 ){
			add_action('comment_form_top', array( $this, 'FBTL_openid_add_comment'));
		}

		if(get_option('FBTL_share_options_bb_forum') == 1){
			if(get_option('FBTL_share_options_bb_forum_position') == 'before')
				add_action('bbp_template_before_single_forum', array( $this, 'FBTL_openid_social_share' ));

			if(get_option('FBTL_share_options_bb_forum_position') == 'after')
				add_action('bbp_template_after_single_forum', array( $this, 'FBTL_openid_social_share' ));

			if(get_option('FBTL_share_options_bb_forum_position') == 'both'){
				add_action('bbp_template_before_single_forum', array( $this, 'FBTL_openid_social_share' ));
				add_action('bbp_template_after_single_forum', array( $this, 'FBTL_openid_social_share' ));
			}
		}

		if(get_option('FBTL_share_options_bb_topic') == 1){
			if(get_option('FBTL_share_options_bb_topic_position') == 'before')
				add_action('bbp_template_before_single_topic', array( $this, 'FBTL_openid_social_share' ));

			if(get_option('FBTL_share_options_bb_topic_position') == 'after')
				add_action('bbp_template_after_single_topic', array( $this, 'FBTL_openid_social_share' ));

			if(get_option('FBTL_share_options_bb_topic_position') == 'both'){
				add_action('bbp_template_before_single_topic', array( $this, 'FBTL_openid_social_share' ));
				add_action('bbp_template_after_single_topic', array( $this, 'FBTL_openid_social_share' ));
			}
		}

		if(get_option('FBTL_share_options_bb_reply') == 1){
			if(get_option('FBTL_share_options_bb_reply_position') == 'before')
				add_action('bbp_template_before_single_reply', array( $this, 'FBTL_openid_social_share' ));

			if(get_option('FBTL_share_options_bb_reply_position') == 'after')
				add_action('bbp_template_after_single_reply', array( $this, 'FBTL_openid_social_share' ));

			if(get_option('FBTL_share_options_bb_reply_position') == 'both'){
				add_action('bbp_template_before_single_reply', array( $this, 'FBTL_openid_social_share' ));
				add_action('bbp_template_after_single_reply', array( $this, 'FBTL_openid_social_share' ));
			}
		}

		add_filter( 'the_content', array( $this, 'FBTL_openid_add_social_share_links' ) );
		add_filter( 'the_excerpt', array( $this, 'FBTL_openid_add_social_share_links' ) );

		//custom avatar
		if(get_option('fbtl_jpl_social_login_avatar')) {
			add_filter( 'get_avatar', array( $this, 'FBTL_social_login_custom_avatar' ), 15, 5 );
			add_filter( 'get_avatar_url', array( $this, 'FBTL_social_login_custom_avatar_url' ), 15, 3);
			add_filter( 'bp_core_fetch_avatar', array( $this, 'FBTL_social_login_buddypress_avatar' ), 10, 2);
		}

		remove_action( 'admin_notices', array( $this, 'FBTL_openid_success_message') );
	    remove_action( 'admin_notices', array( $this, 'FBTL_openid_error_message') );

		//set default values
		add_option( 'FBTL_openid_login_redirect', 'same' );
		add_option( 'FBTL_openid_login_theme', 'longbutton' );
		add_option( 'FBTL_openid_oauth','0');
        add_option('FBTL_openid_new_user','0');
        add_option('FBTL_openid_malform_error','0');
		add_option( 'FBTL_openid_share_theme', 'oval' );
		add_option( 'FBTL_share_options_enable_post_position', 'before');
		add_option( 'FBTL_share_options_home_page_position', 'before');
		add_option( 'FBTL_share_options_static_pages_position', 'before');
		add_option( 'FBTL_share_options_bb_forum_position', 'before');
		add_option( 'FBTL_share_options_bb_topic_position', 'before');
		add_option( 'FBTL_share_options_bb_reply_position', 'before');
		add_option( 'FBTL_openid_default_login_enable', '1');
		add_option('FBTL_login_openid_login_widget_customize_textcolor','000000');
		//add_option( 'FBTL_openid_login_widget_customize_text', 'Connect with:' );
		//add_option( 'FBTL_openid_share_widget_customize_text', 'Share with:' );
		//add_option( 'FBTL_openid_share_widget_customize_text_color', '000000');
		add_option( 'FBTL_openid_login_button_customize_text', 'Login with' );
		//add_option( 'FBTL_openid_share_widget_customize_direction_horizontal','1' );
		//add_option( 'FBTL_sharing_icon_custom_size','35' );
		//add_option( 'FBTL_openid_share_custom_theme', 'default' );
		//add_option( 'FBTL_sharing_icon_custom_color', '000000' );
		//add_option( 'FBTL_sharing_icon_space', '4' );
		//add_option( 'FBTL_sharing_icon_custom_font', '000000' );
		//add_option( 'FBTL_login_icon_custom_size','35' );
		//add_option( 'FBTL_login_icon_space','4' );
		//add_option( 'FBTL_login_icon_custom_width','200' );
		//add_option( 'FBTL_login_icon_custom_height','35' );
		//add_option('FBTL_login_icon_custom_boundary','4');
		//add_option( 'FBTL_openid_login_custom_theme', 'default' );
		//add_option( 'FBTL_login_icon_custom_color', '2B41FF' );
		//add_option( 'FBTL_openid_logout_redirection_enable', '0' );
		add_option( 'FBTL_openid_logout_redirect', 'currentpage' );
		add_option( 'FBTL_openid_auto_register_enable', '1');
		add_option( 'FBTL_openid_account_linking_enable', '0');
		add_option( 'FBTL_openid_email_enable', '1');
		//add_option( 'FBTL_openid_register_disabled_message', 'Registration is disabled for this website. Please contact the administrator for any queries.' );
		//add_option( 'FBTL_openid_register_email_message', 'Hello,<br><br>##User Name## has registered to your site  successfully.<br><br>Thanks,<br>fbtlsocial' );
		add_option( 'fbtl_jpl_social_login_avatar','1' );
		add_option( 'fbtl_jpl_user_attributes','0' );
		add_option( 'FBTL_share_vertical_hide_mobile', '1' );
		add_option( 'FBTL_openid_social_comment_blogpost','1' );
		//add_option( 'FBTL_openid_social_comment_default_label', 'Default Comments' );
		//add_option( 'FBTL_openid_social_comment_fb_label', 'Facebook Comments' );
		//add_option( 'FBTL_openid_social_comment_google_label', 'Google+ Comments' );
		//add_option( 'FBTL_openid_social_comment_disqus_label', 'Disqus Comments' );
		add_option( 'FBTL_openid_social_comment_heading_label', 'Leave a Reply' );
		add_option('FBTL_openid_login_role_mapping','subscriber');
		add_option( 'FBTL_openid_user_number',0);
		add_option( 'FBTL_openid_login_widget_customize_logout_name_text', 'Howdy, ##username## |' );
		add_option( 'FBTL_openid_login_widget_customize_logout_text', 'Logout?' );
		add_option( 'FBTL_openid_share_email_subject','I wanted you to see this site' );
		add_option( 'FBTL_openid_share_email_body','Check out this site ##url##' );
		add_option( 'FBTL_openid_enable_profile_completion','0' );
        add_option( 'fbtl_jpl_logo_check','1' );
        add_option( 'FBTL_openid_test_configuration', 0);


        //profile completion
        add_option( 'FBTL_profile_complete_title','Profile Completion');
        add_option( 'FBTL_profile_complete_username_label','Username');
        add_option( 'FBTL_profile_complete_email_label','Email');
        add_option( 'FBTL_profile_complete_submit_button','Submit');
        add_option( 'FBTL_profile_complete_instruction','If you are an existing user on this site, enter your registered email and username. If you are a new user, please edit/fill the details.');
        add_option( 'FBTL_profile_complete_extra_instruction','We will be sending a verification code to this email to verify it. Please enter a valid email address.');
        add_option( 'FBTL_profile_complete_uname_exist','Entered username already exists. Try some other username');
       
        add_option( 'FBTL_email_verify_back_button','Back');
        add_option( 'FBTL_email_verify_title','Verify your email');
        add_option( 'FBTL_email_verify_message','We have sent a verification code to given email. Please verify your account with it.');
        add_option( 'FBTL_email_verify_verification_code_instruction','Enter your verification code');
       
       

    




        //account linking
        add_option( 'FBTL_account_linking_title','Account Linking');
        add_option( 'FBTL_account_linking_new_user_button','Create a new account?');
        add_option( 'FBTL_account_linking_existing_user_button','Link to an existing account?');
        add_option( 'FBTL_account_linking_new_user_instruction','If you do not have an existing account with a different email address, click on <b>Create a new account</b>');
        add_option( 'FBTL_account_linking_existing_user_instruction','If you already have an existing account with a different email adddress and want to link this account with that, click on <b>Link to an existing account</b>.');
        add_option( 'FBTL_account_linking_extra_instruction','You will be redirected to login page to login to your existing account.');



        //woocommerce display options
        add_option( 'FBTL_openid_woocommerce_login_form','0');
        add_option( 'FBTL_openid_woocommerce_before_login_form','0');
        add_option( 'FBTL_openid_woocommerce_center_login_form','0');
        add_option( 'FBTL_openid_woocommerce_register_form_start','0');
        add_option( 'FBTL_openid_woocommerce_center_register_form','0');
        add_option( 'FBTL_openid_woocommerce_register_form_end','0');
        add_option( 'FBTL_openid_woocommerce_before_checkout_billing_form','0');
        add_option( 'FBTL_openid_woocommerce_after_checkout_billing_form','0');
        //buddypress display options
        add_option( 'FBTL_openid_bp_before_register_page','0');
        add_option( 'FBTL_openid_bp_before_account_details_fields','0');
        add_option( 'FBTL_openid_bp_after_register_page','0');

        //Custom app switch button option
        add_option('FBTL_openid_enable_custom_app_google','1');
        add_option('FBTL_openid_enable_custom_app_facebook','1');
        add_option('FBTL_openid_enable_custom_app_twitter','1');

        //GDPR options
        add_option('FBTL_openid_gdpr_consent_enable', 0);
        add_option( 'FBTL_openid_privacy_policy_text', 'terms and conditions');
        add_option( 'FBTL_openid_gdpr_consent_message','I accept the terms and conditions.');
        //Error messages option
        add_option( 'FBTL_registration_error_message','There was an error in registration. Please contact your administrator.');
        add_option( 'FBTL_email_failure_message','Either your SMTP is not configured or you have entered an unmailable email. Please go back and try again.');
        add_option( 'FBTL_existing_username_error_message','This username already exists. Please ask the administrator to create your account with a unique username.');
        add_option( 'FBTL_manual_login_error_message','There was an error during login. Please try to login/register manually. <a href='.site_url().'>Go back to site</a>');
        add_option( 'FBTL_delete_user_error_message','Error deleting user from account linking table');
        add_option( 'FBTL_account_linking_message','Link your social account to existing WordPress account by entering username and password.');
        add_option('regi_pop_up','');
        add_option('FBTL_openid_tour','0');
        add_option('pop_regi_msg','Your settings are saved successfully. Please enter your valid email address to enable social login.');
        add_option('pop_login_msg','Enter Your Login Credentials.');
    }

    

	function FBTL_openid_deactivate() {
		delete_option('FBTL_openid_host_name');
		delete_option('FBTL_openid_transactionId');
		delete_option('FBTL_openid_admin_password');
		delete_option('FBTL_openid_registration_status');
		delete_option('FBTL_openid_admin_phone');
		delete_option('FBTL_openid_new_registration');
		delete_option('FBTL_openid_admin_customer_key');
		delete_option('FBTL_openid_admin_api_key');
		delete_option('FBTL_openid_customer_token');
		delete_option('FBTL_openid_verify_customer');
		delete_option('FBTL_openid_message');
		delete_option( 'FBTL_openid_admin_customer_valid');
		delete_option( 'FBTL_openid_admin_customer_plan');
	}

    //  create FBTL_openid_linked_user if it doesn't exist
    // + add entries in wp_FBTL_openid_linked_user table
    // + remove columns app name and identifier from wp_users table
	function FBTL_openid_plugin_update(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'FBTL_openid_linked_user';
        $charset_collate = $wpdb->get_charset_collate();

        $time = $wpdb->get_var("SELECT COLUMN_NAME 
                                    FROM information_schema.COLUMNS 
                                    WHERE
                                     TABLE_SCHEMA='$wpdb->dbname'
                                     AND COLUMN_NAME = 'timestamp'");

        // if table FBTL_openid_linked_user doesn't exist or the 'timestamp' column doesn't exist
        if($wpdb->get_var("show tables like '$table_name'") != $table_name || empty($time) ) {
            $sql = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    linked_social_app varchar(55) NOT NULL,
                    linked_email varchar(55) NOT NULL,
                    user_id mediumint(10) NOT NULL,
                    identifier VARCHAR(100) NOT NULL,
                    timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    PRIMARY KEY  (id)
                ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            $identifier = $wpdb->get_var("SELECT COLUMN_NAME 
                                    FROM information_schema.COLUMNS 
                                    WHERE 
                                    TABLE_NAME = '$wpdb->users' 
                                    AND TABLE_SCHEMA='$wpdb->dbname'
                                    AND COLUMN_NAME = 'identifier'");

            if(strcasecmp( $identifier, "identifier") == 0 ){

                $count= $wpdb->get_var("SELECT count(ID) FROM $wpdb->users WHERE identifier not LIKE ''");
                $result= $wpdb->get_results("SELECT * FROM $wpdb->users WHERE identifier not LIKE ''");

                for($icnt = 0; $icnt < $count; $icnt = $icnt + 1){

                    $provider = $result[$icnt]->provider;
                    $split_app_name = explode('_', $provider);
                    $provider = strtolower($split_app_name[0]);
                    $user_email = $result[$icnt]->user_email;
                    $ID = $result[$icnt]->ID;
                    $identifier = $result[$icnt]->identifier;

                    $output = $wpdb->insert(
                        $table_name,
                        array(
                            'linked_social_app' => $provider,
                            'linked_email' => $user_email,
                            'user_id' =>  $ID,
                            'identifier' => $identifier
                        ),
                        array(
                            '%s',
                            '%s',
                            '%d',
                            '%s'
                        )
                    );
                    if($output === false){
                        $wpdb->show_errors();
                        $wpdb->print_error();
                        wp_die('Error in insert Query');
                        exit;
                    }

                }
                $wpdb->get_var("ALTER TABLE $wpdb->users DROP COLUMN provider");
                $wpdb->get_var("ALTER TABLE $wpdb->users DROP COLUMN identifier");
            }
        }

        
    }

	function FBTL_openid_activate() {
		add_option('Activated_Plugin','Plugin-Slug');
        //update_option( 'FBTL_openid_host_name', 'https://auth.fbtlsocial.com' );
	}

	function FBTL_openid_add_social_login(){
        if(!is_user_logged_in() && !FBTL_openid_is_customer_registered() && strpos( $_SERVER['QUERY_STRING'], 'disable-social-login' ) == false){
            $FBTL_login_widget = new FBTL_openid_login_wid();
            $FBTL_login_widget->openidloginForm();
		}
	}

	function FBTL_openid_add_social_share_links($content) {
		global $post;
		$post_content=$content;
		$title = str_replace('+', '%20', urlencode($post->post_title));

		if(is_front_page() && get_option('FBTL_share_options_enable_home_page')==1){
			$html_content = FBTL_openid_share_shortcode('', $title);

			if ( get_option('FBTL_share_options_home_page_position') == 'before' ) {
				return  $html_content . $post_content;
			} else if ( get_option('FBTL_share_options_home_page_position') == 'after' ) {
				 return   $post_content . $html_content;
			} else if ( get_option('FBTL_share_options_home_page_position') == 'both' ) {
				 return $html_content . $post_content . $html_content;
			}
		} else if(is_page() && get_option('FBTL_share_options_enable_static_pages')==1){
			$html_content = FBTL_openid_share_shortcode('', $title);

			if ( get_option('FBTL_share_options_static_pages_position') == 'before' ) {
				return  $html_content . $post_content;
			} else if ( get_option('FBTL_share_options_static_pages_position') == 'after' ) {
				 return   $post_content . $html_content;
			} else if ( get_option('FBTL_share_options_static_pages_position') == 'both' ) {
				 return $html_content . $post_content . $html_content;
			}
		} else if(is_single() && get_option('FBTL_share_options_enable_post') == 1 ){
			$html_content = FBTL_openid_share_shortcode('', $title);

			if ( get_option('FBTL_share_options_enable_post_position') == 'before' ) {
				return  $html_content . $post_content;
			} else if ( get_option('FBTL_share_options_enable_post_position') == 'after' ) {
				 return   $post_content . $html_content;
			} else if ( get_option('FBTL_share_options_enable_post_position') == 'both' ) {
				 return $html_content . $post_content . $html_content;
			}
		} else
			return $post_content;

	}

	function FBTL_openid_social_share(){
		global $post;
		$title = str_replace('+', '%20', urlencode($post->post_title));
		echo FBTL_openid_share_shortcode('', $title);
	}

	function FBTL_openid_add_comment(){
		global $post;
		if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
			$http = "https://";
		} else {
			$http = "http://";
		}
		$url = $http . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'];
		if(is_single() && get_option('FBTL_openid_social_comment_blogpost') == 1 ) {
			FBTL_openid_social_comment($post, $url);
		} else if(is_page() && get_option('FBTL_openid_social_comment_static')==1) {
			FBTL_openid_social_comment($post, $url);
		}
	}

	function FBTL_custom_login_stylesheet()
	{
		wp_enqueue_style( 'fbtl_wp-style',plugins_url('css/FBTL_style.css?version=5.0.6', __FILE__), false );
		wp_enqueue_style( 'fbtl_wp-bootstrap-social',plugins_url('css/bootstrap-social.css', __FILE__), false );
		wp_enqueue_style( 'fbtl_wp-bootstrap-main',plugins_url('css/bootstrap.min.css', __FILE__), false );
		wp_enqueue_style( 'fbtl_wp-font-awesome',plugins_url('css/font-awesome.min.css?version=4.8', __FILE__), false );
		wp_enqueue_style( 'fbtl_wp-font-awesome',plugins_url('css/font-awesome.css?version=4.8', __FILE__), false );
	}

    function FBTL_openid_plugin_settings_style() {
        wp_enqueue_style( 'FBTL_openid_admin_settings_style', plugins_url('css/FBTL_style.css?version=5.0.6', __FILE__));
       
        wp_enqueue_style( 'fbtl_wp-bootstrap-social',plugins_url('css/bootstrap-social.css', __FILE__), false );
        wp_enqueue_style( 'fbtl_wp-bootstrap-main',plugins_url('css/bootstrap.min-preview.css', __FILE__), false );
        wp_enqueue_style( 'fbtl_wp-font-awesome',plugins_url('css/font-awesome.min.css?version=4.8', __FILE__), false );
        wp_enqueue_style( 'fbtl_wp-font-awesome',plugins_url('css/font-awesome.css?version=4.8', __FILE__), false );
       



    }

    function FBTL_openid_plugin_script() {
        wp_enqueue_script( 'js-cookie-script',plugins_url('js/jquery.cookie.min.js', __FILE__), array('jquery'));
        wp_enqueue_script( 'fbtl_social-login-script',plugins_url('js/social_login.js', __FILE__), array('jquery') );
    }

    


    function FBTL_openid_success_message() {
		$message = get_option('FBTL_openid_message'); ?>
		<script>

		jQuery(document).ready(function() {
			var message = "<?php echo $message; ?>";
			jQuery('#FBTL_openid_msgs').append("<div class='error notice is-dismissible FBTL_openid_error_container'> <p class='FBTL_openid_msgs'>" + message + "</p></div>");
		});
		</script>
	<?php }

	function FBTL_openid_error_message() {
		$message = get_option('FBTL_openid_message'); ?>
		<script>
		jQuery(document).ready(function() {
			var message = "<?php echo $message; ?>";
			jQuery('#FBTL_openid_msgs').append("<div class='updated notice is-dismissible FBTL_openid_success_container'> <p class='FBTL_openid_msgs'>" + message + "</p></div>");
		});
		</script>
	<?php }

	private function FBTL_openid_show_success_message() {
		remove_action( 'admin_notices', array( $this, 'FBTL_openid_success_message') );
		add_action( 'admin_notices', array( $this, 'FBTL_openid_error_message') );
	}

	private function FBTL_openid_show_error_message() {
		remove_action( 'admin_notices', array( $this, 'FBTL_openid_error_message') );
		add_action( 'admin_notices', array( $this, 'FBTL_openid_success_message') );
	}
    function FBTL_openid_success_facebook_message() {
        $message = 'Please setup Facebook custom app '; ?>
        <script>

            jQuery(document).ready(function() {
                var message = "<?php echo $message; ?>";
                jQuery('#FBTL_openid_msgs').append("<div class='error notice is-dismissible FBTL_openid_error_container'> <p class='FBTL_openid_msgs'>" + message + "</p></div>");
            });
        </script>
    <?php }

    private function FBTL_openid_show_facebook_error_message() {
        remove_action( 'admin_notices', array( $this, 'FBTL_openid_error_message') );
        add_action( 'admin_notices', array( $this, 'FBTL_openid_success_facebook_message') );
    }

	public function FBTL_openid_check_empty_or_null( $value ) {
		if( ! isset( $value ) || empty( $value ) ) {
			return true;
		}
		return false;
	}

	function  FBTL_login_widget_openid_options() {
        //update_option( 'FBTL_openid_host_name', 'https://auth.fbtlsocial.com' );
		FBTL_register_openid();
	}

	function FBTL_openid_activation_message() {
		$class = "updated";
		$message = get_option('FBTL_openid_message');
		echo "<div class='" . $class . "'> <p>" . $message . "</p></div>";
	}

	function FBTL_login_widget_text_domain(){
		load_plugin_textdomain('fbtl', FALSE, basename( dirname( __FILE__ ) ) .'/languages');
	}

	public function FBTL_oauth_check_empty_or_null( $value ) {
		if( ! isset( $value ) || empty( $value ) ) {
			return true;
		}
		return false;
	}

    public function if_custom_app_exists($app_name){
        if(get_option('FBTL_openid_apps_list'))
            $appslist = maybe_unserialize(get_option('FBTL_openid_apps_list'));
        else
            $appslist = array();

        foreach( $appslist as $key => $app){
            $option = 'FBTL_openid_enable_custom_app_' . $key;
            if($app_name == $key && get_option($option) == '1')
                return true;
        }
        return false;
    }
    function fbtlsocial_openid_save_settings(){

        if ( current_user_can( 'manage_options' )){
            if(is_admin() && get_option('Activated_Plugin')=='Plugin-Slug') {

                delete_option('Activated_Plugin');
                update_option('FBTL_openid_message','Go to plugin <b><a href="admin.php?page=FBTL_openid_settings">settings</a></b> to enable Social Login .');
                add_action('admin_notices', array($this, 'FBTL_openid_activation_message'));
            }

            if( isset($_POST['FBTL_openid_connect_register_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_connect_register_customer" ) {	//register the admin to fbtlsocial
                $nonce = $_POST['FBTL_openid_connect_register_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-connect-register-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    //validation and sanitization
                    $company = '';
                    $first_name = '';
                    $last_name = '';
                    $email = '';
                    $phone = '';
                    $password = '';
                    $confirmPassword = '';
                    $illegal = "#$%^*()+=[]';,/{}|:<>?~";
                    $illegal = $illegal . '"';
                    if( $this->FBTL_openid_check_empty_or_null( $_POST['company'] ) || $this->FBTL_openid_check_empty_or_null( $_POST['email'] ) || $this->FBTL_openid_check_empty_or_null( $_POST['password'] ) || $this->FBTL_openid_check_empty_or_null( $_POST['confirmPassword'] ) ) {
                        update_option( 'FBTL_openid_message', 'All the fields are required. Please enter valid entries.');
                        $this->FBTL_openid_show_error_message();
                        if(get_option('regi_pop_up') =="yes") {
                            update_option('pop_regi_msg', get_option('FBTL_openid_message'));
                           
                        }
                        return;
                    } else if( strlen( $_POST['password'] ) < 6 || strlen( $_POST['confirmPassword'] ) < 6){	//check password is of minimum length 6
                        update_option( 'FBTL_openid_message', 'Choose a password with minimum length 6.');
                        $this->FBTL_openid_show_error_message();
                        if(get_option('regi_pop_up') =="yes"){
                            update_option('pop_regi_msg', get_option('FBTL_openid_message'));

                        }
                        return;
                    } else if(strpbrk($_POST['email'],$illegal)) {
                        update_option( 'FBTL_openid_message', 'Please match the format of Email. No special characters are allowed.');
                        $this->FBTL_openid_show_error_message();
                        if(get_option('regi_pop_up') =="yes"){
                            update_option('pop_regi_msg', get_option('FBTL_openid_message'));
                           
                        }
                        return;
                    } else {
                        $company = sanitize_text_field($_POST['company']);
                        $first_name = sanitize_text_field(isset($_POST['fname'])?$_POST['fname']:'');
                        $last_name = sanitize_text_field(isset($_POST['lname'])?$_POST['lname']:'');
                        $email = sanitize_email( $_POST['email'] );
                        $phone = sanitize_text_field( isset($_POST['phone'])?$_POST['phone']:'' );
                        $password = stripslashes( $_POST['password'] );
                        $confirmPassword = stripslashes( $_POST['confirmPassword'] );

                    }

                    update_option( 'FBTL_openid_admin_company_name', $company);
                    update_option( 'FBTL_openid_admin_first_name', $first_name);
                    update_option( 'FBTL_openid_admin_last_name', $last_name);
                    update_option( 'FBTL_openid_admin_email', $email );
                    update_option( 'FBTL_openid_admin_phone', $phone );

                    
                }

            } else if( isset( $_POST['show_login'] ) )
            {

                FBTL_pop_show_verify_password_page();
            }
            
            
            else if( isset($_POST['FBTL_openid_connect_verify_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_connect_verify_customer" ) {	//register the admin to fbtlsocial
                $nonce = $_POST['FBTL_openid_connect_verify_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-connect-verify-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    //validation and sanitization
                    $email = '';
                    $password = '';
                    $illegal = "#$%^*()+=[]';,/{}|:<>?~";
                    $illegal = $illegal . '"';
                    if( $this->FBTL_openid_check_empty_or_null( $_POST['email'] ) || $this->FBTL_openid_check_empty_or_null( $_POST['password'] ) ) {
                        update_option( 'FBTL_openid_message', 'All the fields are required. Please enter valid entries.');
                        $this->FBTL_openid_show_error_message();
                        return;
                    } else if(strpbrk($_POST['email'],$illegal)) {
                        update_option( 'FBTL_openid_message', 'Please match the format of Email. No special characters are allowed.');
                        $this->FBTL_openid_show_error_message();
                        return;
                    } else{
                        $email = sanitize_email( $_POST['email'] );
                        $password = stripslashes( $_POST['password'] );
                    }

                    update_option( 'FBTL_openid_admin_email', $email );
                    update_option( 'FBTL_openid_admin_password', $password );
                    $customer = new CustomerOpenID();
                    $content = $customer->get_customer_key();
                    $customerKey = json_decode( $content, true );
                    if( isset($customerKey) ) {
                        update_option( 'FBTL_openid_admin_customer_key', $customerKey['id'] );
                        update_option( 'FBTL_openid_admin_api_key', $customerKey['apiKey'] );
                        update_option( 'FBTL_openid_customer_token', $customerKey['token'] );
                        update_option( 'FBTL_openid_admin_phone', $customerKey['phone'] );
                        update_option('FBTL_openid_admin_password', '');
                        update_option( 'FBTL_openid_message', 'Your account has been retrieved successfully.');
                        delete_option('FBTL_openid_verify_customer');
                        $this->FBTL_openid_show_success_message();
                    } else {
                        update_option( 'FBTL_openid_message', 'Invalid username or password. Please try again.');
                        $this->FBTL_openid_show_error_message();
                        if(get_option('regi_pop_up') =="yes") {
                            update_option("pop_login_msg",get_option("FBTL_openid_message"));
                            FBTL_pop_show_verify_password_page();

                        }
                    }
                    update_option('FBTL_openid_admin_password', '');
                }
            }
            else if(isset($_POST['FBTL_openid_forgot_password_nonce']) and isset($_POST['option']) and $_POST['option'] == 'FBTL_openid_forgot_password'){
                $nonce = $_POST['FBTL_openid_forgot_password_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-forgot-password-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    $email ='';
                    if( $this->FBTL_openid_check_empty_or_null( $email ) ) {
                        if( $this->FBTL_openid_check_empty_or_null( $_POST['email'] ) ) {
                            update_option( 'FBTL_openid_message', 'No email provided. Please enter your email below to reset password.');
                            $this->FBTL_openid_show_error_message();
                            if(get_option('regi_pop_up') =="yes"){
                                update_option("pop_login_msg",get_option("FBTL_openid_message"));
                                FBTL_pop_show_verify_password_page();

                            }
                            return;
                        } else {
                            $email = sanitize_email($_POST['email']);

                        }
                    }


                    $customer = new CustomerOpenID();
                    $content = json_decode($customer->forgot_password($email),true);
                    if(strcasecmp($content['status'], 'SUCCESS') == 0){
                        update_option( 'FBTL_openid_message','You password has been reset successfully. Please enter the new password sent to your registered mail here.');
                        $this->FBTL_openid_show_success_message();
                        if(get_option('regi_pop_up') =="yes"){
                            update_option("pop_login_msg",get_option("FBTL_openid_message"));
                            FBTL_pop_show_verify_password_page();

                        }
                    }else{
                        update_option( 'FBTL_openid_message','An error occured while processing your request. Please make sure you are registered with FBTL with the given email address.');
                        $this->FBTL_openid_show_error_message();
                        if(get_option('regi_pop_up') =="yes"){
                            update_option("pop_login_msg",get_option("FBTL_openid_message"));
                            FBTL_pop_show_verify_password_page();

                        }
                    }
                }
            }
            else if(isset($_POST['FBTL_openid_check_license_nonce']) and isset($_POST['option']) and $_POST['option'] == 'FBTL_openid_check_license'){
                $nonce = $_POST['FBTL_openid_check_license_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-check-license-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    if(!FBTL_openid_is_customer_registered()) {
                        $customer = new CustomerOpenID();
                        $content = json_decode($customer->check_customer_valid(),true);
                        if(strcasecmp($content['status'], 'SUCCESS') == 0){
                            update_option( 'FBTL_openid_admin_customer_valid', strcasecmp($content['licenseType'], 'Premium') !== FALSE ? 1 : 0);
                            update_option( 'FBTL_openid_admin_customer_plan', isset($content['licensePlan']) ? base64_encode($content['licensePlan']) : 0);
                            if(get_option('FBTL_openid_admin_customer_valid') && isset($content['licensePlan'])){
                                $license = explode(' -', $content['licensePlan']);
                                $lp = $license[0];
                                update_option( 'FBTL_openid_message','You are on the old ' . $lp . '.');
                            } else
                                update_option( 'FBTL_openid_message','You are on the Free Plan.');
                            $this->FBTL_openid_show_success_message();
                        }else if(strcasecmp($content['status'], 'FAILED') == 0){
                            update_option('FBTL_openid_message', 'You are on Free Plan.');
                            $this->FBTL_openid_show_success_message();
                        }else{
                            update_option( 'FBTL_openid_message','An error occured while processing your request. Please try again.');
                            $this->FBTL_openid_show_error_message();
                        }
                    } else {
                        update_option('FBTL_openid_message', 'Please register an account before trying to check your plan');
                        $this->FBTL_openid_show_error_message();
                    }
                }
            }
            else if( isset($_POST['FBTL_openid_enable_apps_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_enable_apps" ) {
                $nonce = $_POST['FBTL_openid_enable_apps_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-enable-apps-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {

                    update_option( 'FBTL_openid_google_enable', isset( $_POST['FBTL_openid_google_enable']) ? sanitize_text_field($_POST['FBTL_openid_google_enable']) : 0);
                    update_option( 'FBTL_openid_salesforce_enable', isset( $_POST['FBTL_openid_salesforce_enable']) ? sanitize_text_field($_POST['FBTL_openid_salesforce_enable']) : 0);
                    if($this->if_custom_app_exists('facebook')) {
                        update_option('FBTL_openid_facebook_enable', isset($_POST['FBTL_openid_facebook_enable']) ? sanitize_text_field($_POST['FBTL_openid_facebook_enable']) : 0);
                    }
                    else if(isset($_POST['FBTL_openid_facebook_enable'])) {
                        update_option('FBTL_openid_facebook_enable',0);
                        $this->FBTL_openid_show_facebook_error_message();

                    }
                    update_option( 'FBTL_openid_linkedin_enable', isset( $_POST['FBTL_openid_linkedin_enable']) ? sanitize_text_field($_POST['FBTL_openid_linkedin_enable']) : 0);
                    update_option( 'FBTL_openid_windowslive_enable', isset( $_POST['FBTL_openid_windowslive_enable']) ? sanitize_text_field($_POST['FBTL_openid_windowslive_enable']) : 0);
                    update_option( 'FBTL_openid_amazon_enable', isset( $_POST['FBTL_openid_amazon_enable']) ? sanitize_text_field($_POST['FBTL_openid_amazon_enable']) : 0);
                    update_option( 'FBTL_openid_instagram_enable', isset( $_POST['FBTL_openid_instagram_enable']) ? sanitize_text_field($_POST['FBTL_openid_instagram_enable']) : 0);
                    update_option( 'FBTL_openid_twitter_enable', isset( $_POST['FBTL_openid_twitter_enable']) ? sanitize_text_field($_POST['FBTL_openid_twitter_enable']) : 0);
                    update_option( 'FBTL_openid_vkontakte_enable', isset( $_POST['FBTL_openid_vkontakte_enable']) ? sanitize_text_field($_POST['FBTL_openid_vkontakte_enable']) : 0);
                    update_option( 'FBTL_openid_yahoo_enable', isset( $_POST['FBTL_openid_yahoo_enable']) ? sanitize_text_field($_POST['FBTL_openid_yahoo_enable']) : 0);

                    update_option( 'FBTL_openid_default_login_enable', isset( $_POST['FBTL_openid_default_login_enable']) ? sanitize_text_field($_POST['FBTL_openid_default_login_enable']) : 0);
                    update_option( 'FBTL_openid_default_register_enable', isset( $_POST['FBTL_openid_default_register_enable']) ? sanitize_text_field($_POST['FBTL_openid_default_register_enable']) : 0);
                    update_option( 'FBTL_openid_default_comment_enable', isset( $_POST['FBTL_openid_default_comment_enable']) ? sanitize_text_field($_POST['FBTL_openid_default_comment_enable']) : 0);



                    // GDPR options
                    update_option( 'FBTL_openid_gdpr_consent_enable', isset( $_POST['FBTL_openid_gdpr_consent_enable']) ? sanitize_text_field($_POST['FBTL_openid_gdpr_consent_enable']) : 0);
                    if(get_option('FBTL_openid_gdpr_consent_enable') == 1 && (!FBTL_openid_restrict_user())) {
                        update_option('FBTL_openid_privacy_policy_url', isset($_POST['FBTL_openid_privacy_policy_url']) ? sanitize_text_field($_POST['FBTL_openid_privacy_policy_url']) : get_option('FBTL_openid_privacy_policy_url'));
                        update_option('FBTL_openid_privacy_policy_text', isset($_POST['FBTL_openid_privacy_policy_text']) ? sanitize_text_field($_POST['FBTL_openid_privacy_policy_text']) : get_option('FBTL_openid_privacy_policy_text'));
                        update_option('FBTL_openid_gdpr_consent_message', isset($_POST['FBTL_openid_gdpr_consent_message']) ? stripslashes($_POST['FBTL_openid_gdpr_consent_message']) : get_option('FBTL_openid_gdpr_consent_message'));
                    }
                    //Redirect URL
                    update_option( 'FBTL_openid_login_redirect', sanitize_text_field($_POST['FBTL_openid_login_redirect']));
                    update_option( 'FBTL_openid_login_redirect_url', sanitize_text_field($_POST['FBTL_openid_login_redirect_url'] ));
                    update_option( 'FBTL_openid_relative_login_redirect_url', isset( $_POST['FBTL_openid_relative_login_redirect_url']) ? sanitize_text_field($_POST['FBTL_openid_relative_login_redirect_url']) : "" );

                    //Logout Url
                    update_option( 'FBTL_openid_logout_redirection_enable', isset( $_POST['FBTL_openid_logout_redirection_enable']) ? sanitize_text_field($_POST['FBTL_openid_logout_redirection_enable']) : 0);
                    update_option( 'FBTL_openid_logout_redirect', sanitize_text_field($_POST['FBTL_openid_logout_redirect']));
                    update_option( 'FBTL_openid_logout_redirect_url', sanitize_text_field($_POST['FBTL_openid_logout_redirect_url'] ));

                    //auto register
                    update_option( 'FBTL_openid_auto_register_enable', isset( $_POST['FBTL_openid_auto_register_enable']) ? sanitize_text_field($_POST['FBTL_openid_auto_register_enable']) : 0);
                    update_option( 'FBTL_openid_register_disabled_message', sanitize_text_field($_POST['FBTL_openid_register_disabled_message']));


                    //email notification
                    update_option( 'FBTL_openid_email_enable', isset( $_POST['FBTL_openid_email_enable']) ? sanitize_text_field($_POST['FBTL_openid_email_enable']) : 0);

                    //Customized text
                    update_option('FBTL_openid_login_widget_customize_text',sanitize_text_field($_POST['FBTL_openid_login_widget_customize_text'] ));
                    update_option( 'FBTL_openid_login_button_customize_text',sanitize_text_field($_POST['FBTL_openid_login_button_customize_text'] ));

                    //profile completion
                    update_option('FBTL_openid_enable_profile_completion', isset( $_POST['FBTL_openid_enable_profile_completion']) ? sanitize_text_field($_POST['FBTL_openid_enable_profile_completion']) : 0);

                    if(get_option('FBTL_openid_enable_profile_completion') == 1) {

                        update_option('FBTL_profile_complete_title', sanitize_text_field($_POST['FBTL_profile_complete_title']));
                        update_option('FBTL_profile_complete_username_label', sanitize_text_field($_POST['FBTL_profile_complete_username_label']));
                        update_option('FBTL_profile_complete_email_label', sanitize_text_field($_POST['FBTL_profile_complete_email_label']));
                        update_option('FBTL_profile_complete_submit_button', sanitize_text_field($_POST['FBTL_profile_complete_submit_button']));
                        update_option('FBTL_profile_complete_instruction', sanitize_text_field($_POST['FBTL_profile_complete_instruction']));
                        update_option('FBTL_profile_complete_extra_instruction', sanitize_text_field($_POST['FBTL_profile_complete_extra_instruction']));
                        update_option('FBTL_profile_complete_uname_exist', sanitize_text_field($_POST['FBTL_profile_complete_uname_exist']));

                        update_option('FBTL_email_verify_resend_jpl_button', sanitize_text_field($_POST['FBTL_email_verify_resend_jpl_button']));
                        update_option('FBTL_email_verify_back_button', sanitize_text_field($_POST['FBTL_email_verify_back_button']));
                        update_option('FBTL_email_verify_title', sanitize_text_field($_POST['FBTL_email_verify_title']));
                        update_option('FBTL_email_verify_message', sanitize_text_field($_POST['FBTL_email_verify_message']));
                        update_option('FBTL_email_verify_verification_code_instruction', sanitize_text_field($_POST['FBTL_email_verify_verification_code_instruction']));
                        update_option('FBTL_email_verify_wrong_jpl', sanitize_text_field($_POST['FBTL_email_verify_wrong_jpl']));

                        $_POST['custom_jpl_msg']=stripslashes( $_POST['custom_jpl_msg']);
                        update_option('custom_jpl_msg',($_POST['custom_jpl_msg']));
                    }
                    //account-linking
                    update_option( 'FBTL_openid_account_linking_enable', isset( $_POST['FBTL_openid_account_linking_enable']) ? sanitize_text_field($_POST['FBTL_openid_account_linking_enable']) : 0);

                    if(get_option('FBTL_openid_account_linking_enable') == 1 && (!FBTL_openid_restrict_user())) {

                        update_option('FBTL_account_linking_title', sanitize_text_field($_POST['FBTL_account_linking_title']));
                        update_option('FBTL_account_linking_new_user_button', sanitize_text_field($_POST['FBTL_account_linking_new_user_button']));
                        update_option('FBTL_account_linking_existing_user_button', sanitize_text_field($_POST['FBTL_account_linking_existing_user_button']));
                        update_option('FBTL_account_linking_new_user_instruction', sanitize_text_field($_POST['FBTL_account_linking_new_user_instruction']));
                        update_option('FBTL_account_linking_existing_user_instruction', sanitize_text_field($_POST['FBTL_account_linking_existing_user_instruction']));
                        update_option('FBTL_account_linking_extra_instruction', sanitize_text_field($_POST['FBTL_account_linking_extra_instruction']));
                    }

                    update_option('FBTL_openid_login_widget_customize_logout_name_text',sanitize_text_field($_POST['FBTL_openid_login_widget_customize_logout_name_text']));
                    update_option( 'FBTL_openid_login_widget_customize_logout_text',$_POST['FBTL_openid_login_widget_customize_logout_text']);
                    update_option('fbtl_jpl_logo_check', isset( $_POST['fbtl_jpl_logo_check']) ? sanitize_text_field($_POST['fbtl_jpl_logo_check']) : 0);
                    update_option('FBTL_login_openid_login_widget_customize_textcolor',sanitize_text_field($_POST['FBTL_login_openid_login_widget_customize_textcolor']));
                    update_option('FBTL_openid_login_theme',sanitize_text_field($_POST['FBTL_openid_login_theme'] ));
                    update_option( 'FBTL_openid_message', 'Your settings are saved successfully.' );

                    //customization of icons
                    update_option('FBTL_login_icon_custom_size',sanitize_text_field($_POST['FBTL_login_icon_custom_size'] ));
                    update_option('FBTL_login_icon_space',sanitize_text_field($_POST['FBTL_login_icon_space'] ));
                    update_option('FBTL_login_icon_custom_width',sanitize_text_field($_POST['FBTL_login_icon_custom_width'] ));
                    update_option('FBTL_login_icon_custom_height',sanitize_text_field($_POST['FBTL_login_icon_custom_height'] ));
                    update_option('FBTL_openid_login_custom_theme',sanitize_text_field($_POST['FBTL_openid_login_custom_theme'] ));
                    update_option( 'FBTL_login_icon_custom_color', sanitize_text_field($_POST['FBTL_login_icon_custom_color'] ));
                    update_option('FBTL_login_icon_custom_boundary',sanitize_text_field($_POST['FBTL_login_icon_custom_boundary']));

                    // avatar
                    update_option( 'fbtl_jpl_social_login_avatar', isset( $_POST['fbtl_jpl_social_login_avatar']) ? sanitize_text_field($_POST['fbtl_jpl_social_login_avatar']) : 0);


                    if(isset($_POST['mapping_value_default']))
                        update_option('FBTL_openid_login_role_mapping', isset( $_POST['mapping_value_default']) ? sanitize_text_field($_POST['mapping_value_default']) : 'subscriber');

                    if(FBTL_openid_is_customer_valid() && !FBTL_openid_get_customer_plan('Do It Yourself')){
                        //Attribute collection
                        update_option( 'fbtl_jpl_user_attributes', isset( $_POST['fbtl_jpl_user_attributes']) ? sanitize_text_field($_POST['fbtl_jpl_user_attributes']) : 0);
                    }

                    $this->FBTL_openid_show_success_message();
                    if(FBTL_openid_is_customer_registered()) {
                       
                    }
                }

            }else if(isset($_POST['go_to_register'])) {

            } 
            else if( isset($_POST['FBTL_openid_comment_settings_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_save_comment_settings" ) {
                $nonce = $_POST['FBTL_openid_comment_settings_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-comment-settings-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {

                    

                    update_option( 'FBTL_openid_message', 'Your settings are saved successfully.' );
                    $this->FBTL_openid_show_success_message();
                    if(!FBTL_openid_is_customer_registered()) {

                        $redirect = add_query_arg( array('tab' => 'register'), $_SERVER['REQUEST_URI'] );
                        update_option('FBTL_openid_message', 'Your settings are successfully saved. Please  <a href=\" '. $redirect .'\">Register or Login with fbtlsocial</a>  to enable Social Login and Social Sharing.');
                        $this->FBTL_openid_show_error_message();
                    }
                }
            }
            else if( isset($_POST['FBTL_openid_contact_us_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_contact_us_query_option" ) {
                $nonce = $_POST['FBTL_openid_contact_us_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-contact-us-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    // Contact Us query
                    $email = sanitize_email($_POST['FBTL_openid_contact_us_email']);
                    $phone = sanitize_text_field($_POST['FBTL_openid_contact_us_phone']);
                    $query = sanitize_text_field($_POST['FBTL_openid_contact_us_query']);
                    $customer = new CustomerOpenID();
                    if ( $this->FBTL_openid_check_empty_or_null( $email ) || $this->FBTL_openid_check_empty_or_null( $query ) ) {
                        update_option('FBTL_openid_message', 'Please fill up Email and Query fields to submit your query.');
                        $this->FBTL_openid_show_error_message();
                    } else {
                        $submited = $customer->submit_contact_us( $email, $phone, $query );
                        if ( $submited == false ) {
                            update_option('FBTL_openid_message', 'Your query could not be submitted. Please try again.');
                            $this->FBTL_openid_show_error_message();
                        } else {
                            update_option('FBTL_openid_message', 'Thanks for getting in touch! We shall get back to you shortly.');
                            $this->FBTL_openid_show_success_message();
                        }
                    }
                }
            }
            else if( isset($_POST['FBTL_openid_resend_jpl_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_resend_jpl" ) {
                $nonce = $_POST['FBTL_openid_resend_jpl_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-resend-jpl-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    $customer = new CustomerOpenID();
                    $content = json_decode($customer->fbtl_send_jpl('EMAIL'), true);
                    if(strcasecmp($content['status'], 'SUCCESS') == 0) {
                        if(get_option('FBTL_openid_email_jpl_count')){
                            update_option('FBTL_openid_email_jpl_count',get_option('FBTL_openid_email_jpl_count') + 1);
                            update_option('FBTL_openid_message', 'Another One Time Passcode has been sent (' . get_option('FBTL_openid_email_jpl_count') . ') for verification to ' . get_option('FBTL_openid_admin_email'));
                            if(get_option('regi_pop_up') =="yes")
                                FBTL_pop_show_jpl_verification(get_option('FBTL_openid_message'));
                        }else{
                            update_option( 'FBTL_openid_message', ' A passcode is sent to ' . get_option('FBTL_openid_admin_email') . '. Please enter the jpl here to verify your email.');
                            update_option('FBTL_openid_email_jpl_count',1);
                            if(get_option('regi_pop_up') =="yes")
                                FBTL_pop_show_jpl_verification(get_option('FBTL_openid_message'));
                        }
                        update_option('FBTL_openid_transactionId',$content['txId']);
                        update_option('FBTL_openid_registration_status','FBTL_jpl_DELIVERED_SUCCESS');
                        $this->FBTL_openid_show_success_message();
                    }else{

                        update_option('FBTL_openid_message','There was an error in sending email. Please click on Resend jpl to try again.');
                        update_option('FBTL_openid_registration_status','FBTL_jpl_DELIVERED_FAILURE');
                        $this->FBTL_openid_show_error_message();
                        if(get_option('regi_pop_up') =="yes")
                            FBTL_pop_show_jpl_verification(get_option('FBTL_openid_message'));
                    }
                }
            }else if( isset($_POST['FBTL_openid_go_back_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_go_back" ){
                $nonce = $_POST['FBTL_openid_go_back_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-go-back-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    update_option('FBTL_openid_registration_status','');
                    delete_option('FBTL_openid_new_registration');
                    delete_option('FBTL_openid_admin_email');
                    delete_option('FBTL_openid_sms_jpl_count');
                    delete_option('FBTL_openid_email_jpl_count');
                }
            }else if( isset($_POST['FBTL_openid_go_back_login_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_go_back_login" ){
                $nonce = $_POST['FBTL_openid_go_back_login_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-go-back-login-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    update_option('FBTL_openid_registration_status','');
                    delete_option('FBTL_openid_admin_email');
                    delete_option('FBTL_openid_admin_phone');
                    delete_option('FBTL_openid_admin_password');
                    delete_option('FBTL_openid_admin_customer_key');
                    delete_option('FBTL_openid_verify_customer');
                }
            }else if( isset($_POST['FBTL_openid_go_back_registration_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_go_back_registration" ){
                $nonce = $_POST['FBTL_openid_go_back_registration_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-go-back-register-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    update_option('FBTL_openid_verify_customer','true');
                }
            }
            else if( isset($_POST['FBTL_openid_add_custom_nonce']) and isset( $_POST['option'] ) and $_POST['option'] == "FBTL_openid_add_custom_app" ) {
                $nonce = $_POST['FBTL_openid_add_custom_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-add-custom-app-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    if($this->FBTL_oauth_check_empty_or_null($_POST['FBTL_oauth_client_id']) || $this->FBTL_oauth_check_empty_or_null($_POST['FBTL_oauth_client_secret'])) {
                        update_option( 'message', 'Please enter valid Client ID and Client Secret.');
                        $this->FBTL_openid_show_error_message();
                        return;
                    } else{
                        $scope = stripslashes(sanitize_text_field( $_POST['FBTL_oauth_scope'] ));
                        $clientid = stripslashes(sanitize_text_field( $_POST['FBTL_oauth_client_id'] ));
                        $clientsecret = stripslashes(sanitize_text_field( $_POST['FBTL_oauth_client_secret'] ));
                        $appname = stripslashes(sanitize_text_field( $_POST['FBTL_oauth_app_name'] ));

                        if(get_option('FBTL_openid_apps_list'))
                            $appslist = maybe_unserialize(get_option('FBTL_openid_apps_list'));
                        else
                            $appslist = array();

                        $newapp = array();

                        foreach($appslist as $key => $currentapp){
                            if($appname == $key){
                                $newapp = $currentapp;
                                break;
                            }
                        }

                        $newapp['clientid'] = $clientid;
                        $newapp['clientsecret'] = $clientsecret;
                        $newapp['scope'] = $scope;
                        $newapp['redirecturi'] = site_url().'/openidcallback';
                        if($appname=="facebook"){
                            $authorizeurl = 'https://www.facebook.com/dialog/oauth';
                            $accesstokenurl = 'https://graph.facebook.com/v2.8/oauth/access_token';
                            $resourceownerdetailsurl = 'https://graph.facebook.com/me/?fields=id,name,email,age_range,first_name,gender,last_name,link&access_token=';
                        }  else if($appname=="twitter"){
                            $authorizeurl = "https://api.twitter.com/oauth/authorize";
                            $accesstokenurl = "https://api.twitter.com/oauth/access_token";
                            $resourceownerdetailsurl = "https://dev.twitter.com/docs/api/1.1/get/account/verify_credentials?include_email=true";
                        }else {
                            $authorizeurl = stripslashes(sanitize_text_field($_POST['FBTL_oauth_authorizeurl']));
                            $accesstokenurl = stripslashes(sanitize_text_field($_POST['FBTL_oauth_accesstokenurl']));
                            $resourceownerdetailsurl = stripslashes(sanitize_text_field($_POST['FBTL_oauth_resourceownerdetailsurl']));
                            $appname = stripslashes(sanitize_text_field( $_POST['FBTL_oauth_custom_app_name'] ));
                        }

                        $newapp['authorizeurl'] = $authorizeurl;
                        $newapp['accesstokenurl'] = $accesstokenurl;
                        $newapp['resourceownerdetailsurl'] = $resourceownerdetailsurl;
                        $appslist[$appname] = $newapp;
                        update_option('FBTL_openid_apps_list', maybe_serialize($appslist));
                        wp_redirect('admin.php?page=FBTL_openid_settings&tab=custom_app');
                    }
                }
            }
        } 


        if(isset($_POST['FBTL_openid_feedback_close_nonce']) and isset($_POST['FBTL_openid_option']) and $_POST['FBTL_openid_option']=='FBTL_openid_skip_feedback'){
            $nonce = $_POST['FBTL_openid_feedback_close_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-feedback-close-nonce' ) ) {
				wp_die('<strong>ERROR</strong>: Invalid Request.');
			} else {
				update_option('FBTL_openid_feedback_form',1);
				deactivate_plugins( '/fbtlsocial-login-openid/FBTL_Social_Main_Setting.php' );
			}

        }
        
    }
	

	

	function fbtlsocial_openid_menu() {

		//Add FBTL plugin to the menu
		$page = add_menu_page( 'FBTL Facebook-Twitter-Wp-Login ' . __( 'jploft', 'FBTL_openid_settings' ), 'Fb-Tw-Wp-Login', 'manage_options',
		'FBTL_openid_settings', array( $this, 'FBTL_login_widget_openid_options' ),plugin_dir_url(__FILE__) . 'images/logo.png');
	}

	function FBTL_openid_plugin_actions( $links, $file ) {
	 	if( $file == 'fbtlsocial-login-openid/FBTL_Social_Main_Setting.php' && function_exists( "admin_url" ) ) {
			$settings_link = '<a href="' . admin_url( 'tools.php?page=FBTL_openid_settings' ) . '">' . __('Settings') . '</a>';
			array_unshift( $links, $settings_link ); 
		}
		return $links;
	}

	public function FBTL_get_output( $atts ){
		if(!FBTL_openid_is_customer_registered()){
			$fbtlsocial_widget = new FBTL_openid_login_wid();
			$html = $fbtlsocial_widget->openidloginFormShortCode( $atts );
			return $html;
		}
	}

	

	

	public function FBTL_get_comments_output( $atts ){
		if(!FBTL_openid_is_customer_registered()){
			$html = FBTL_openid_comments_shortcode();
			return $html;
		}
	}

	function FBTL_social_login_custom_avatar( $avatar, $mixed, $size, $default, $alt = '' ) {

        if ( is_numeric( $mixed ) AND $mixed > 0 ) {	
            $user_id = $mixed;
        } elseif ( is_string( $mixed ) AND ( $user = get_user_by( 'email', $mixed )) ) {	
        	$user_id = $user->ID;
        } elseif ( is_object( $mixed ) AND property_exists( $mixed, 'user_id' ) AND is_numeric( $mixed->user_id ) ) {		
            $user_id = $mixed->user_id;
        } else {		
            $user_id = null;
        }

        if (  !empty( $user_id ) ) {    
            $filename = '';
           
            if (!(is_dir($filename))) {
                $user_meta_thumbnail = get_user_meta($user_id, 'fbtl_social_avt', true);        
                $user_meta_name = get_user_meta($user_id, 'user_name', true);       
                $user_picture = (!empty($user_meta_thumbnail) ? $user_meta_thumbnail : '');
                if ($user_picture !== false AND strlen(trim($user_picture)) > 0) {    
                    return '<img alt="' . $user_meta_name . '" src="' . $user_picture . '" class="avatar apsl-avatar-social-login avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';
                }
            }
        }
        return $avatar;
	}

	

	function FBTL_social_login_custom_avatar_url( $url, $id_or_email, $args = null ) {
		if ( is_numeric( $id_or_email ) AND $id_or_email > 0 ) {	
			$user_id = $id_or_email;
		} elseif ( is_string( $id_or_email ) AND ( $user = get_user_by( 'email', $id_or_email )) ) {	
			$user_id = $user->ID;
		} elseif ( is_object( $id_or_email ) AND property_exists( $id_or_email, 'user_id' ) AND is_numeric( $id_or_email->user_id ) ) {		
			$user_id = $id_or_email->user_id;
		} else {		
			$user_id = null;
		}

		if (  !empty( $user_id ) ) {
            $filename = '';
            if ($this->FBTL_openid_is_buddypress_active()) {
                $filename = bp_upload_dir();
                $filename = $filename['basedir'] . "/avatars/" . $user_id;
            }
            if (!(is_dir($filename))) {
                $user_meta_thumbnail = get_user_meta($user_id, 'fbtl_social_avt', true);
                $user_picture = (!empty($user_meta_thumbnail) ? $user_meta_thumbnail : $url);
                return $user_picture;
            }
        }
		return $url;
	}

    
}
new FBTL_NewId_Social;



?>