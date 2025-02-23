<?php

function FBTL_openid_delete_social_profile($id){
    // delete first name, last name, user_url and profile_url from usermeta
    global $wpdb;
    $metakey1 = 'first_name'; 
    $metakey2 = 'last_name'; 
    $metakey3 = 'fbtl_social_avt'; 
    $metakey4 = 'fbtl_social_profile_url';
    $wpdb->query($wpdb->prepare('DELETE from '.$wpdb->prefix.'usermeta where user_id = %d and (meta_key = %s or meta_key = %s  or meta_key = %s  or meta_key = %s)',$id,$metakey1,$metakey2,$metakey3,$metakey4));
    update_user_meta($id,'FBTL_openid_data_deleted','1');
    exit;
}

function FBTL_openid_process_account_linking($username, $user_email, $first_name, $last_name, $user_full_name, $user_url, $user_picture, $decrypted_app_name, $decrypted_user_id){
    FBTL_openid_start_session();

    if(get_option('FBTL_openid_auto_register_enable')) {

        $random_password = wp_generate_password( 10, false );
        global $wpdb;
        $db_prefix = $wpdb->prefix;
        $username_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " .$db_prefix."users where user_login = %s", $username));

        if( !empty($username_user_id) ){
            $email_explode = explode('@',$user_email );
            $user_new_name = $email_explode[0];
            $username_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " .$db_prefix."users where user_login = %s", $user_new_name));
            $i = 1;
            $uname='';
            while(!empty($username_user_id) ){
                $uname=$user_new_name .'_'.$i;
                $username_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " .$db_prefix."users where user_login = %s", $uname));
                $i++;
                if(empty($username_user_id)){
                    $user_new_name=$uname;
                }
            }

            if( !empty($username_user_id) ){
                wp_die("Error Code 1: ".get_option('FBTL_existing_username_error_message'));
            }
            $username = $user_new_name;
        }

        $meta_user_url = $user_url;

        if(isset($decrypted_app_name) && !empty($decrypted_app_name) && $decrypted_app_name =='facebook'){
            $user_url = '';
        }

        $userdata = array(
            'user_login'  => $username,
            'user_email'    => $user_email,
            'user_pass'   =>  $random_password,
            'display_name' => $user_full_name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_url' => $user_url,
        );

        $user_id 	= wp_insert_user( $userdata);

        if(is_wp_error( $user_id )) {
            print_r($user_id);  
            wp_die("Error Code 1: ".get_option('FBTL_registration_error_message'));
        }

        update_option('FBTL_openid_user_count',get_option('FBTL_openid_user_count')+1);

        $user  = get_user_by('id', $user_id );

        if(get_option('fbtl_jpl_social_login_avatar') && isset($user_picture)){
            update_user_meta($user_id, 'fbtl_social_avt',$user_picture);
        }

        FBTL_openid_start_session();

        $_SESSION['FBTL_login'] = true;
        do_action( 'FBTL_user_register', $user_id,$meta_user_url);
        do_action( 'fbtlsocial_collect_attributes_for_authenticated_user', $user, FBTL_openid_get_redirect_url());
        do_action( 'wp_login', $user->user_login, $user );
        wp_set_auth_cookie( $user_id, true );
    }
    //end of create account block

    $redirect_url = FBTL_openid_get_redirect_url();
    wp_redirect($redirect_url);
    exit;
}

function FBTL_openid_initialize_social_login(){
    $client_name = "wordpress";
    $timestamp = round( microtime(true) * 1000 );
    $api_key = get_option('FBTL_openid_admin_api_key');
    $token = $client_name . ':' . number_format($timestamp, 0, '', ''). ':' . $api_key;

    $customer_token = get_option('FBTL_openid_customer_token');
    $encrypted_token = fbtl_encrpt_data($token,$customer_token);
    $encoded_token = urlencode( $encrypted_token );

    $userdata = get_option('fbtl_jpl_user_attributes')?'true':'false';

    $http = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? "https://" : "http://";

    $parts = parse_url($http . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    parse_str($parts['query'], $query);
    $post = isset( $query['p'] ) ? '?p=' . $query['p'] : '';

    $base_return_url =  $http . $_SERVER["HTTP_HOST"] . strtok($_SERVER["REQUEST_URI"],'?') . $post;

    $return_url = strpos($base_return_url, '?') !== false ? urlencode( $base_return_url . '&option=fbtl_jpl' ): urlencode( $base_return_url . '?option=fbtl_jpl' );

    $url = get_option('FBTL_openid_host_name') . '/moas/openid-connect/client-app/authenticate?token=' . $encoded_token . '&userdata=' . $userdata. '&id=' . get_option('FBTL_openid_admin_customer_key') . '&encrypted=true&app=' . $_REQUEST['app_name'] . '_oauth&returnurl=' . $return_url . '&encrypt_response=true';
    wp_redirect($url);
    exit;
}





function FBTL_openid_process_social_login(){
    if( is_user_logged_in()){
        return;
    }

    //Decrypt all entries
    $decrypted_email = isset($_POST['email']) ? FBTL_openid_decrypt_sanitize($_POST['email']): '';
    $decrypted_user_name = isset($_POST['username']) ? FBTL_openid_decrypt_sanitize($_POST['username']): '';
    $decrypted_user_picture = isset($_POST['profilePic']) ? FBTL_openid_decrypt_sanitize($_POST['profilePic']): '';
    $decrypted_user_url = isset($_POST['profileUrl']) ? FBTL_openid_decrypt_sanitize($_POST['profileUrl']): '';
    $decrypted_first_name = isset($_POST['firstName']) ? FBTL_openid_decrypt_sanitize($_POST['firstName']): '';
    $decrypted_last_name = isset($_POST['lastName']) ? FBTL_openid_decrypt_sanitize($_POST['lastName']): '';
    $decrypted_app_name = isset($_POST['appName']) ? FBTL_openid_decrypt_sanitize($_POST['appName']): '';
    $decrypted_user_id = isset($_POST['userid']) ? FBTL_openid_decrypt_sanitize($_POST['userid']): '';
    
    
    $decrypted_user_name = str_replace(' ', '-', $decrypted_user_name);
    $decrypted_user_name = sanitize_user($decrypted_user_name, true);
                        
    if($decrypted_user_name == '-' || $decrypted_user_name == ''){
        $splitemail = explode('@', $decrypted_email);
        $decrypted_user_name = $splitemail[0];
    }

    $decrypted_app_name = FBTL_openid_filter_app_name($decrypted_app_name);

    if(isset( $raw_first_name ) && isset( $raw_last_name )){
        if(strcmp($decrypted_first_name, $decrypted_last_name)!=0)
            $user_full_name = $decrypted_first_name.' '.$decrypted_last_name;
        else
            $user_full_name = $decrypted_first_name;
        $first_name = $decrypted_first_name;
        $last_name = $decrypted_last_name;
    }
    else{
        $user_full_name = $decrypted_user_name;
        $first_name = '';
        $last_name = '';
    }
    //Set Display Picture
    $user_picture = $decrypted_user_picture;

    //Set User URL
    $user_url = $decrypted_user_url;

    //if email or username not returned from app
    if ( empty($decrypted_email) || empty($decrypted_user_name) ){

        //check if provider + identifier group exists
        global $wpdb;
        $db_prefix = $wpdb->prefix;
        $id_returning_user = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$db_prefix."FBTL_openid_linked_user where linked_social_app = \"%s\" AND identifier = %s",$decrypted_app_name,$decrypted_user_id));
        if(empty($decrypted_email)){
            $email_user_id = NULL;
        }
        else {
            $email_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users where user_email = \"%s\"", $decrypted_email));
        }
        FBTL_openid_start_session();
        // if returning user whose appname + identifier exists, log him in
        if((isset($id_returning_user)) || (isset($email_user_id)) ){
            if ((!isset($id_returning_user)) && (isset($email_user_id)) ){
                $id_returning_user = $email_user_id;
                FBTL_openid_insert_query($decrypted_app_name,$decrypted_email,$id_returning_user,$decrypted_user_id);
            }
            $user 	= get_user_by('id', $id_returning_user );
            if(get_option('fbtl_jpl_social_login_avatar') && isset($user_picture))
                update_user_meta($id_returning_user, 'fbtl_social_avt', $user_picture);
            $_SESSION['FBTL_login'] = true;
            $_SESSION['social_app_name'] = $decrypted_app_name;
            $_SESSION['user_email'] = $decrypted_email;
            $_SESSION['social_user_id'] = $decrypted_user_id;
            do_action( 'fbtlsocial_collect_attributes_for_authenticated_user', $user, FBTL_openid_get_redirect_url());
            do_action( 'wp_login', $user->user_login, $user );
            wp_set_auth_cookie( $id_returning_user, true );
        }
        // if new user and profile completion is enabled
        elseif (get_option('FBTL_openid_enable_profile_completion')){
            echo FBTL_openid_profile_completion_form($last_name, $first_name, $user_full_name, $user_url, $user_picture, $decrypted_user_name, $decrypted_email, $decrypted_app_name, $decrypted_user_id);
            exit;
        }
        // if new user and profile completion and account linking is disabled, auto create dummy data and register user
        else{
            // auto registration is enabled
            if(get_option('FBTL_openid_auto_register_enable')) {

                if(!empty($decrypted_email))
                {
                    $split_email  = explode('@',$decrypted_email);
                    $username = $split_email[0];
                    $user_email = $decrypted_email;
                }
                else if(!empty($decrypted_user_name))
                {
                    $split_app_name = explode('_',$decrypted_app_name);
                    $username = $decrypted_user_name;
                    $user_email = $decrypted_user_name.'@'.$split_app_name[0].'.com';
                }
                else
                {
                    $split_app_name = explode('_',$decrypted_app_name);
                    $username = 'user_'.get_option('FBTL_openid_user_count');
                    $user_email =  'user_'.get_option('FBTL_openid_user_count').'@'.$split_app_name[0].'.com';
                }
                // remove  white space from email
                $user_email = str_replace(' ', '', $user_email);

                //account linking
                if ( get_option('FBTL_openid_account_linking_enable') && (!FBTL_openid_restrict_user())){
                    FBTL_openid_start_session();
                    $_SESSION['username'] = $decrypted_user_name;
                    $_SESSION['user_email'] = $user_email;
                    $_SESSION['user_full_name'] = $user_full_name;
                    $_SESSION['first_name'] = $first_name;
                    $_SESSION['last_name'] = $last_name;
                    $_SESSION['user_url'] = $user_url;
                    $_SESSION['user_picture'] = $user_picture;
                    $_SESSION['social_app_name'] = $decrypted_app_name;
                    $_SESSION['social_user_id'] = $decrypted_user_id;

                    echo FBTL_openid_account_linking_form($decrypted_user_name,$user_email,$first_name,$last_name,$user_full_name,$user_url,$user_picture,$decrypted_app_name,$decrypted_user_id);
                    exit;
                }

                $random_password 	= wp_generate_password( 10, false );
                $user_profile_url  = $user_url;

                if(isset($decrypted_app_name) && !empty($decrypted_app_name) && $decrypted_app_name=='facebook'){
                    $user_url = '';
                }

                $userdata = array(
                    'user_login'  =>  $username,
                    'user_email'    =>  $user_email,
                    'user_pass'   =>  $random_password,
                    'display_name' => $user_full_name,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_url' => $user_url,
                );

                $user_id 	= wp_insert_user( $userdata);
                if(is_wp_error( $user_id )) {
                    print_r($user_id);
                    wp_die("Error Code 3: ".get_option('FBTL_registration_error_message'));
                }

                update_option('FBTL_openid_user_count',get_option('FBTL_openid_user_count')+1);

                $_SESSION['social_app_name'] = $decrypted_app_name;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['social_user_id'] = $decrypted_user_id;

                $user	= get_user_by('id', $user_id );

                if(get_option('fbtl_jpl_social_login_avatar') && isset($user_picture)){
                    update_user_meta($user_id, 'fbtl_social_avt', $user_picture);
                }
                $_SESSION['FBTL_login'] = true;

                //registration hook
                do_action( 'FBTL_user_register', $user_id, $user_profile_url);
                do_action( 'fbtlsocial_collect_attributes_for_authenticated_user', $user, FBTL_openid_get_redirect_url());
                //login hook
                do_action( 'wp_login', $user->user_login, $user );
                wp_set_auth_cookie( $user_id, true );
            }

            $redirect_url = FBTL_openid_get_redirect_url();
            wp_redirect($redirect_url);
            exit;
        }
    }
    //email and username are both returned..dont show profile completion
    else{

        global $wpdb;
        $user_email = sanitize_email($decrypted_email);
        $username = $decrypted_user_name;

        //Checking if email or username already exist
        $username_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users where user_login = %s", $username));

        $db_prefix = $wpdb->prefix;
        $linked_email_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$db_prefix."FBTL_openid_linked_user where linked_social_app = \"%s\" AND identifier = %s",$decrypted_app_name,$decrypted_user_id));

        $email_user_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$db_prefix."FBTL_openid_linked_user where linked_email = \"%s\"",$decrypted_email));
        
        if(empty($decrypted_email)){
            $existing_email_user_id = NULL;
        }
        else {
            $existing_email_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users where user_email = \"%s\"", $decrypted_email));
        }

        FBTL_openid_start_session();
        if((isset($linked_email_id)) || (isset($email_user_id)) || isset($existing_email_user_id)) { // user is a member

            if ((!isset($linked_email_id)) && (isset($email_user_id)) ){
                $linked_email_id = $email_user_id;
                FBTL_openid_insert_query($decrypted_app_name,$user_email,$linked_email_id,$decrypted_user_id);
            }

            if(isset($linked_email_id)){
                $user = get_user_by('id', $linked_email_id );
                $user_id = $user->ID;
            }
            else if(isset($email_user_id)){
                $user = get_user_by('id', $email_user_id );
                $user_id = $user->ID;
            }
            else{
                $user = get_user_by('id', $existing_email_user_id );
                $user_id = $user->ID;
            }

            if(get_option('fbtl_jpl_social_login_avatar') && isset($user_picture))
                update_user_meta($user_id, 'fbtl_social_avt', $user_picture);
            $_SESSION['FBTL_login'] = true;
            $_SESSION['social_app_name'] = $decrypted_app_name;
            $_SESSION['social_user_id'] = $decrypted_user_id;
            $_SESSION['user_email'] = $user_email;
            do_action( 'fbtlsocial_collect_attributes_for_authenticated_user', $user, FBTL_openid_get_redirect_url());
            do_action( 'wp_login', $user->user_login, $user );
            wp_set_auth_cookie( $user_id, true );
        }
        else if ( get_option('FBTL_openid_account_linking_enable')&& (!FBTL_openid_restrict_user())){
            FBTL_openid_start_session();
            $_SESSION['username'] = $decrypted_user_name;
            $_SESSION['user_email'] = $user_email;
            $_SESSION['user_full_name'] = $user_full_name;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['user_url'] = $user_url;
            $_SESSION['user_picture'] = $user_picture;
            $_SESSION['social_app_name'] = $decrypted_app_name;
            $_SESSION['social_user_id'] = $decrypted_user_id;
            echo FBTL_openid_account_linking_form($decrypted_user_name,$user_email,$first_name,$last_name,$user_full_name,$user_url,$user_picture,$decrypted_app_name,$decrypted_user_id);
            exit;
        }
        else {
            // this user is a guest
            // auto registration is enabled
            if(get_option('FBTL_openid_auto_register_enable')) {
                $random_password 	= wp_generate_password( 10, false );

                if( isset($username_user_id) ){
                    $email = explode('@', $user_email);
                    $username = $email[0];
                    $username_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users where user_login = %s", $username));

                    $i = 1;
                    while(!empty($username_user_id) ){
                        $uname=$username.'_' . $i;
                        $username_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " .$db_prefix."users where user_login = %s", $uname));
                        $i++;
                        if(empty($username_user_id)){
                            $username= $uname;
                        }
                    }
                    if( isset($username_user_id) ){
                        echo '<br/>'."Error Code 2: ".get_option('FBTL_existing_username_error_message');
                        exit();
                    }
                }

                $user_profile_url  = $user_url;

                if(isset($decrypted_app_name) && !empty($decrypted_app_name) && $decrypted_app_name=='facebook'){
                    $user_url = '';
                }

                $userdata = array(
                    'user_login'  =>  $username,
                    'user_email'    =>  $user_email,
                    'user_pass'   =>  $random_password,
                    'display_name' => $user_full_name,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_url' => $user_url
                );


                $user_id 	= wp_insert_user( $userdata);

                if(is_wp_error( $user_id )) {
                    print_r($user_id);
                    wp_die("Error Code 4: ".get_option('FBTL_registration_error_message'));
                }

                $_SESSION['social_app_name'] = $decrypted_app_name;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['social_user_id'] = $decrypted_user_id;

                $user	= get_user_by('email', $user_email );

                if(get_option('fbtl_jpl_social_login_avatar') && isset($user_picture)){
                    update_user_meta($user_id, 'fbtl_social_avt', $user_picture);
                }
                $_SESSION['FBTL_login'] = true;

                //registration hook
                do_action( 'FBTL_user_register', $user_id,$user_profile_url);
                do_action( 'fbtlsocial_collect_attributes_for_authenticated_user', $user, FBTL_openid_get_redirect_url());
                //login hook
                do_action( 'wp_login', $user->user_login, $user );
                wp_set_auth_cookie( $user_id, true );
            }
            $redirect_url = FBTL_openid_get_redirect_url();
            wp_redirect($redirect_url);
            exit;
        }
    }
}

function FBTL_openid_custom_app_oauth_redirect($appname){
    if(isset($_REQUEST['test']))
        setcookie("FBTL_oauth_test", true);
    else
        setcookie("FBTL_oauth_test", false);

    // NEW
    if(get_option('FBTL_openid_apps_list')) {
        $appslist = maybe_unserialize(get_option('FBTL_openid_apps_list'));
    }
    else {
        $appslist = array();
    }
    if(get_option('FBTL_openid_malform_error')){
        if(get_option( 'permalink_structure' )) {
            $social_app_redirect_uri = site_url() .'/openidcallback/'.$appname;
        }
        else {
            $social_app_redirect_uri = site_url() . '/?openidcallback='.$appname;
        }
    }
    else {
        if(get_option( 'permalink_structure' )) {
            $social_app_redirect_uri = site_url() . '/openidcallback';
        }
        else{
            $social_app_redirect_uri = site_url() .'/?openidcallback';
        }
    }

    FBTL_openid_start_session();

    foreach($appslist as $key=>$currentapp){

        if($key == "facebook" && $appname == "facebook"){
            $_SESSION["appname"] = "facebook";
            $client_id = $currentapp['clientid'];
            $scope = $currentapp['scope'];
            $login_dialog_url = "https://www.facebook.com/v2.11/dialog/oauth?client_id=".$client_id. '&redirect_uri='. $social_app_redirect_uri .'&response_type=code&scope='.$scope;
            break;
        }
       
        else if($key == "twitter" && $appname == "twitter")
        {	$_SESSION['appname'] = "twitter";
            $client_id			 = $currentapp['clientid'];
            $client_secret		 = $currentapp['clientsecret'];
            $twiter_getrequest_object = new FBTL_Openid_Twitter_OAuth($client_id,$client_secret);	//creating the object of FBTL_Openid_Twitter_OAuth class
            $oauth_token = $twiter_getrequest_object->FBTL_twitter_get_request_token();			//function call
            $login_dialog_url = "https://api.twitter.com/oauth/authenticate?oauth_token=" . $oauth_token;
            break;
        }
    }

    header('Location:'. $login_dialog_url);
    exit;
}

function FBTL_openid_process_custom_app_callback(){
    if( is_user_logged_in() && get_option('FBTL_openid_test_configuration') != 1){
        return;
    }

    $code = $profile_url = $client_id = $current_url = $client_secret = $access_token_uri = $postData = $oauth_token = $user_url = $username = $email = '';
    $oauth_access_token = $redirect_url = $option = $oauth_token_secret = $screen_name = $profile_json_output = $oauth_verifier = $twitter_oauth_token = $access_token_json_output =[];

    FBTL_openid_start_session();
    if(strpos( $_SERVER['REQUEST_URI'], "oauth_verifier") !== false) {
        $_SESSION['appname'] = "twitter";
    }

    if($_SESSION['appname']) {
        $appname = sanitize_text_field($_SESSION['appname']);
    }else {
       

        if ((strpos($_SERVER['REQUEST_URI'], "openidcallback/facebook") !== false) || (strpos($_SERVER['REQUEST_URI'], "openidcallback=facebook") !== false )) {
            $appname = "facebook";

        }
    }

    if($appname == "twitter"){
        $dirs = explode('&', $_SERVER['REQUEST_URI']);
        $oauth_verifier = explode('=', $dirs[1]);
        $twitter_oauth_token = explode('=', $dirs[0]);
    }
    else{
        if(isset($_REQUEST['code'] )){
            $code = sanitize_text_field($_REQUEST['code']);
        }
        else if(isset( $_REQUEST['error_reason'] )){

            echo sanitize_text_field($_REQUEST['error_description']) . "<br>";
            wp_die("Allow access to your profile to get logged in. Click <a href=".get_site_url().">here</a> to go back to the website.");
        }
    }

    if(get_option('FBTL_openid_apps_list')){
        $appslist = maybe_unserialize(get_option('FBTL_openid_apps_list'));
    }
    else{
        $appslist = array();
    }
    if(get_option('FBTL_openid_malform_error')){
        if(get_option( 'permalink_structure' )) {

            $social_app_redirect_uri = site_url() .'/openidcallback/'.$appname;

        }
        else{
            $social_app_redirect_uri = site_url() . '/?openidcallback='.$appname;
        }

    }
    else{
        if(get_option( 'permalink_structure' )) {
            $social_app_redirect_uri = site_url() . '/openidcallback';

        }
        else{
            $social_app_redirect_uri = site_url() .'/?openidcallback';
        }
    }

    foreach($appslist as $key=>$currentapp){
        if($key == "facebook" && $appname == "facebook"){
            $client_id = $currentapp['clientid'];
            $client_secret = $currentapp['clientsecret'];
            $access_token_uri = 'https://graph.facebook.com/v2.11/oauth/access_token';
            $postData = 'client_id=' .$client_id .'&redirect_uri=' . $social_app_redirect_uri . '&client_secret=' . $client_secret . '&code=' .$code;
            break;
        }
        
        else if($key == "twitter" && $appname == "twitter")
        {
            $client_id = $currentapp['clientid'];
            $client_secret = $currentapp['clientsecret'];
            $twitter_getaccesstoken_object = new FBTL_Openid_Twitter_OAuth($client_id,$client_secret);
            $oauth_token = $twitter_getaccesstoken_object->FBTL_twitter_get_access_token($oauth_verifier[1],$twitter_oauth_token[1]);
            break;
        }
    }

    if($appname != "twitter"){

	    $headers='';
        if($appname == "google")
        {
            $headers = array("Content-Type"=>"application/x-www-form-urlencoded");
        }

        $args = array(
            'method' => 'POST',
            'body' => $postData,
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers
        );

        $result = wp_remote_post($access_token_uri,$args);

        if(is_wp_error($result)){
            update_option( 'FBTL_openid_test_configuration', 0);
            echo $result['body'];
            exit();
        }


        $access_token_json_output = json_decode($result['body'], true);
        // this handles incorrect client secret for all apps.
        if ((array_key_exists('error', $access_token_json_output)) || array_key_exists('error_message', $access_token_json_output)){
            if( is_user_logged_in() && get_option('FBTL_openid_test_configuration') == 1 ) {
                update_option('FBTL_openid_test_configuration', 0);
                //Test configuration failed window.
                echo '<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">There Is A Error</div>
                <div style="color: #a94442;font-size:14pt; margin-bottom:20px;">WARNING: Client secret is incorrect for this app. Please check the client secret and try again.<br/>';
                print_r($access_token_json_output);
                echo '</div>';
                exit;
            }
        }

    }
    else{
        $oauth_token_array = explode('&', $oauth_token);
        $oauth_access_token = isset($oauth_token_array[0]) ? $oauth_token_array[0] : null;
        $oauth_access_token = explode('=', $oauth_access_token);
        $oauth_token_secret = isset($oauth_token_array[1]) ? $oauth_token_array[1] : null;
        $oauth_token_secret = explode('=', $oauth_token_secret);
        $screen_name = isset($oauth_token_array[3]) ? $oauth_token_array[3] : null;
        $screen_name = explode('=', $screen_name);
    }
    FBTL_openid_start_session();
    foreach($appslist as $key=>$currentapp){
        if($key == "facebook" && $appname == "facebook"){
            $profile_url ='https://graph.facebook.com/me/?fields=id,name,email,picture.height(961),age_range,first_name,gender,last_name,link&access_token=' .$access_token_json_output['access_token'];
            break;
        }
        
        else if($key == "twitter" && $appname == "twitter"){
            $twitter_getprofile_signature_object = new FBTL_Openid_Twitter_OAuth($client_id,$client_secret);
            $oauth_access_token1 =     isset($oauth_access_token[1]) ? $oauth_access_token[1] : '';
            $oauth_token_secret1 =    isset($oauth_token_secret[1]) ? $oauth_token_secret[1] : '';
            $screen_name1    =   isset($screen_name[1]) ? $screen_name[1] : '';
            $profile_json_output = $twitter_getprofile_signature_object->FBTL_twitter_get_profile_signature($oauth_access_token1,$oauth_token_secret1,$screen_name1);
            break;
        }
    }

    if($appname != "twitter"){

        $access_token_header = "application/x-www-form-urlencoded" . $access_token_json_output['access_token'];
        $headers = array("Authorization"=>$access_token_header);
        $args = array();

        $result = wp_remote_get($profile_url,$args);
        if(is_wp_error($result)){
            update_option( 'FBTL_openid_test_configuration', 0);
            echo $result['body'];
            exit();
        }

        $profile_json_output = json_decode($result['body'], true);
    }
    //Test Configuration
    if( is_user_logged_in() && get_option('FBTL_openid_test_configuration') == 1 ){
        update_option( 'FBTL_openid_test_configuration', 0);
        $print = '<div style="color: #3c763d;
background-color: #dff0d8; padding:2%;margin-bottom:20px;text-align:center; border:1px solid #AEDB9A; font-size:18pt;">TEST SUCCESSFUL</div>
<div style="display:block;text-align:center;margin-bottom:1%;"><img style="width:15%;"src="'. plugin_dir_url(__FILE__) . '/images/green_check.png"></div>';


        $print .= FBTL_openid_json_to_htmltable($profile_json_output);
        echo $print;
        exit;
    }
    $social_app_name = $appname;
    $first_name = $last_name  = $email = $user_name  =  $user_url  = $user_picture  = $social_user_id = '';

    if ($appname == "facebook"){
        $first_name = isset( $profile_json_output['first_name']) ?  $profile_json_output['first_name'] : '';
        $last_name = isset( $profile_json_output['last_name']) ?  $profile_json_output['last_name'] : '';
        $email = isset( $profile_json_output['email']) ?  $profile_json_output['email'] : '';
        $user_name = isset( $profile_json_output['name']) ?  $profile_json_output['name'] : '';
        $user_url = isset( $profile_json_output['link']) ?  $profile_json_output['link'] : '';
        $user_picture = isset( $profile_json_output['picture']['data']['url']) ?  $profile_json_output['picture']['data']['url'] : '';
        $social_user_id = isset( $profile_json_output['id']) ?  $profile_json_output['id'] : '';
    }
    
    else if($appname == "twitter") {
        if (isset($profile_json_output['name'])) {
            $full_name = explode(" ", $profile_json_output['name']);
            $first_name = isset( $full_name[0]) ?  $full_name[0] : '';
            $last_name = isset( $full_name[1]) ?  $full_name[1] : '';
        }
        $user_name = isset( $profile_json_output['screen_name']) ?  $profile_json_output['screen_name'] : '';
        $email = isset( $profile_json_output['email']) ?  $profile_json_output['email'] : '';
        $user_url = isset( $profile_json_output['url']) ?  $profile_json_output['url'] : '';
        $user_picture = isset( $profile_json_output['profile_image_url']) ?  $profile_json_output['profile_image_url'] : '';
        $social_user_id = isset( $profile_json_output['id_str']) ?  $profile_json_output['id_str'] : '';
    }
    
    $user_name = str_replace(' ', '-', $user_name);
    $user_name = sanitize_user($user_name, true);
                        
    if($user_name == '-' || $user_name == ''){
        $splitemail = explode('@', $email);
        $user_name = $splitemail[0];
    }

    //Set User Full Name
    if(isset( $first_name ) && isset( $last_name )){
        if(strcmp($first_name, $last_name)!=0)
            $user_full_name = $first_name.' '.$last_name;
        else
            $user_full_name = $first_name;
    }
    else{
        $user_full_name = $user_name;
        $first_name = '';
        $last_name = '';
    }

    // if email and user name is empty
    if ( empty($email) || empty($user_name) ){
        global $wpdb;
        $db_prefix = $wpdb->prefix;
        $id_returning_user = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$db_prefix."FBTL_openid_linked_user where linked_social_app = \"%s\" AND identifier = %s",$social_app_name,$social_user_id));
        if(empty($email)){
            $email_user_id = NULL;
        }
        else {
            $email_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users where user_email = \"%s\"", $email));
        }

        FBTL_openid_start_session();

        // if returning user whose appname + identifier exists, log him in
        if((isset($id_returning_user)) || (isset($email_user_id))){
            if ((!isset($id_returning_user)) && (isset($email_user_id)) ){
                $id_returning_user = $email_user_id;
                FBTL_openid_insert_query($social_app_name,$email,$id_returning_user,$social_user_id);
            }
            $user 	= get_user_by('id', $id_returning_user );
            if(get_option('fbtl_jpl_social_login_avatar') && isset($user_picture))
                update_user_meta($id_returning_user, 'fbtl_social_avt', $user_picture);

            $_SESSION['FBTL_login'] = true;
            $_SESSION['social_app_name'] = $social_app_name;
            $_SESSION['user_email'] = $email;
            $_SESSION['social_user_id'] = $social_user_id;

            do_action( 'fbtlsocial_collect_attributes_for_authenticated_user', $user, FBTL_openid_get_redirect_url());
            do_action( 'wp_login', $user->user_login, $user );
            wp_set_auth_cookie( $id_returning_user, true );
        }
        // if new user and profile completion is enabled
        elseif (get_option('FBTL_openid_enable_profile_completion')){
            echo FBTL_openid_profile_completion_form($last_name, $first_name, $user_full_name, $user_url, $user_picture, $user_name, $email, $social_app_name, $social_user_id);
            exit;
        }
        // if new user and profile completion is disabled, auto create dummy data and register user
        else{
            // auto registration is enabled
            if(get_option('FBTL_openid_auto_register_enable')) {

                if(!empty($email))
                {
                    $split_email  = explode('@',$email);
                    $username = $split_email[0];
                    $user_email = $email;
                }
                else if(!empty($user_name))
                {
                    $split_app_name = explode('_',$social_app_name);
                    $username = $user_name;
                    $user_email = $user_name.'@'.$split_app_name[0].'.com';
                }
                else
                {
                    $split_app_name = explode('_',$social_app_name);
                    $username = 'user_'.get_option('FBTL_openid_user_count');
                    $user_email =  'user_'.get_option('FBTL_openid_user_count').'@'.$split_app_name[0].'.com';
                }
                $user_email = str_replace(' ', '', $user_email);

                if ( get_option('FBTL_openid_account_linking_enable')&& (!FBTL_openid_restrict_user())){
                    FBTL_openid_start_session();
                    $_SESSION['username'] = $username;
                    $_SESSION['user_email'] = $user_email;
                    $_SESSION['user_full_name'] = $user_full_name;
                    $_SESSION['first_name'] = $first_name;
                    $_SESSION['last_name'] = $last_name;
                    $_SESSION['user_url'] = $user_url;
                    $_SESSION['user_picture'] = $user_picture;
                    $_SESSION['social_app_name'] = $social_app_name;
                    $_SESSION['social_user_id'] = $social_user_id;
                    echo FBTL_openid_account_linking_form($username,$user_email,$first_name,$last_name,$user_full_name,$user_url,$user_picture,$social_app_name,$social_user_id);
                    exit;
                }

                $random_password 	= wp_generate_password( 10, false );

                $user_profile_url  = $user_url;

                if(isset($social_app_name) && !empty($social_app_name) && $social_app_name=='facebook'){
                    $user_url = '';
                }

                $userdata = array(
                    'user_login'  =>  $username,
                    'user_email'    =>  $user_email,
                    'user_pass'   =>  $random_password,
                    'display_name' => $user_full_name,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_url' => $user_url,
                );

                $user_id 	= wp_insert_user( $userdata);

                if(is_wp_error( $user_id )) {
                    print_r($user_id);
                    wp_die("Error Code 5: ".get_option('FBTL_registration_error_message'));
                }

                update_option('FBTL_openid_user_count',get_option('FBTL_openid_user_count')+1);
                $_SESSION['FBTL_login'] = true;
                $_SESSION['social_app_name'] = $social_app_name;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['social_user_id'] = $social_user_id;

                $user	= get_user_by('email', $user_email );

                if(get_option('fbtl_jpl_social_login_avatar') && isset($user_picture)){
                    update_user_meta($user_id, 'fbtl_social_avt', $user_picture);
                }

                //registration hook
                do_action( 'FBTL_user_register', $user_id, $user_profile_url);
                do_action( 'fbtlsocial_collect_attributes_for_authenticated_user', $user, FBTL_openid_get_redirect_url());
                //login hook
                do_action( 'wp_login', $user->user_login, $user );
                wp_set_auth_cookie( $user_id, true );
            }
            $redirect_url = FBTL_openid_get_redirect_url();
            wp_redirect($redirect_url);
            exit;
        }
        $redirect_url = FBTL_openid_get_redirect_url();
        wp_redirect($redirect_url);
        exit;
    }
    //email and username are both returned..dont show profile completion
    else{
        global $wpdb;
        $user_email = sanitize_email($email);
        $username = $user_name;

        //Checking if username already exist
        $username_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users where user_login = %s", $username));

        $db_prefix = $wpdb->prefix;
        $linked_email_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$db_prefix."FBTL_openid_linked_user where linked_social_app = \"%s\" AND identifier = %s",$social_app_name,$social_user_id));

        $email_user_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$db_prefix."FBTL_openid_linked_user where linked_email = \"%s\"",$user_email));
        if(empty($user_email)){
            $existing_email_user_id = NULL;
        }
        else {
            $existing_email_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users where user_email = \"%s\"", $user_email));
        }

        FBTL_openid_start_session();
        if((isset($linked_email_id)) || (isset($email_user_id)) || (isset($existing_email_user_id)) ) { // user is a member
            if ((!isset($linked_email_id)) && (isset($email_user_id)) ){

                $linked_email_id = $email_user_id;
                FBTL_openid_insert_query($social_app_name,$user_email,$linked_email_id,$social_user_id);
            }

            if(isset($linked_email_id)){
                $user = get_user_by('id', $linked_email_id );
                $user_id = $user->ID;
            }
            else if(isset($email_user_id)){
                $user = get_user_by('id', $email_user_id );
                $user_id = $user->ID;
            }
            else{
                $user = get_user_by('id', $existing_email_user_id );
                $user_id = $user->ID;
            }

            if(get_option('fbtl_jpl_social_login_avatar') && isset($user_picture))
                update_user_meta($user_id, 'fbtl_social_avt', $user_picture);
            $_SESSION['FBTL_login'] = true;
            $_SESSION['social_app_name'] = $social_app_name;
            $_SESSION['social_user_id'] = $social_user_id;
            $_SESSION['user_email'] = $user_email;
            do_action( 'fbtlsocial_collect_attributes_for_authenticated_user', $user, FBTL_openid_get_redirect_url());
            do_action( 'wp_login', $user->user_login, $user );
            wp_set_auth_cookie( $user_id, true );

        }
        //if account  is enable
        else if ( get_option('FBTL_openid_account_linking_enable')&& (!FBTL_openid_restrict_user())){
            FBTL_openid_start_session();
            $_SESSION['username'] = $user_name;
            $_SESSION['user_email'] = $user_email;
            $_SESSION['user_full_name'] = $user_full_name;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['user_url'] = $user_url;
            $_SESSION['user_picture'] = $user_picture;
            $_SESSION['social_app_name'] = $social_app_name;
            $_SESSION['social_user_id'] = $social_user_id;

            echo FBTL_openid_account_linking_form($user_name,$user_email,$first_name,$last_name,$user_full_name,$user_url,$user_picture,$social_app_name,$social_user_id);
            exit;
        }
        else {

            // this user is a guest
            // auto registration is enabled
            if(get_option('FBTL_openid_auto_register_enable')) {
                $random_password 	= wp_generate_password( 10, false );

                if( isset($username_user_id) ){
                    $email_array = explode('@', $user_email);
                    $username = $email_array[0];
                    $username_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users where user_login = %s", $username));
                    $i = 1;
                    while(!empty($username_user_id) ){
                        $uname=$username.'_' . $i;
                        $username_user_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " .$db_prefix."users where user_login = %s", $uname));
                        $i++;
                        if(empty($username_user_id)){
                            $username= $uname;
                        }
                    }

                    if( isset($username_user_id) ){
                        echo '<br/>'."Error Code 3: ".get_option('FBTL_existing_username_error_message');
                        exit();
                    }
                }

                $user_profile_url  = $user_url;

                if(isset($social_app_name) && !empty($social_app_name) && $social_app_name=='facebook'){
                    $user_url = '';
                }

                $userdata = array(
                    'user_login'  =>  $username,
                    'user_email'    =>  $user_email,
                    'user_pass'   =>  $random_password,
                    'display_name' => $user_full_name,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_url' => $user_url,
                );

                $user_id 	= wp_insert_user( $userdata);
                if(is_wp_error( $user_id )) {
                    print_r($user_id);
                    wp_die("Error Code 6: ".get_option('FBTL_registration_error_message'));
                }

                FBTL_openid_start_session();
                $_SESSION['username'] = $user_name;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['user_full_name'] = $user_full_name;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['user_url'] = $user_url;
                $_SESSION['user_picture'] = $user_picture;
                $_SESSION['social_app_name'] = $social_app_name;
                $_SESSION['social_user_id'] = $social_user_id;

                $user	= get_user_by('id', $user_id );
                if(get_option('fbtl_jpl_social_login_avatar') && isset($user_picture)){
                    update_user_meta($user_id, 'fbtl_social_avt', $user_picture);
                }
                $_SESSION['FBTL_login'] = true;

                //registration hook
                do_action( 'FBTL_user_register', $user_id, $user_profile_url);
                do_action( 'fbtlsocial_collect_attributes_for_authenticated_user', $user, FBTL_openid_get_redirect_url());
                //login hook

                do_action( 'wp_login', $user->user_login, $user );
                wp_set_auth_cookie( $user_id, true );
            }
            $redirect_url = FBTL_openid_get_redirect_url();
            wp_redirect($redirect_url);
            exit;
        }
        
        $redirect_url = FBTL_openid_get_redirect_url();
        wp_redirect($redirect_url);
        exit;
    }
}