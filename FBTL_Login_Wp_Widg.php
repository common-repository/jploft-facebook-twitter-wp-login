<?php
include "FBTL_Twitter_auth_Jpl.php";
include "FBTL_Jpl_SLogin_Function.php";

    /*
    * Login Widget
    *
    */
    class FBTL_openid_login_wid extends WP_Widget {

        public function __construct() {
            parent::__construct(
                'FBTL_openid_login_wid',
                'FBTL Social Login Widget',
                array(
                    'description' => __( 'Login using Social Apps like  Facebook and Twitter.' ),
                    'customize_selective_refresh' => true,
                )
            );
        }

        public function widget( $args, $instance ) {
            extract( $args );

            echo $args['before_widget'];
            $this->openidloginForm();

            echo $args['after_widget'];
        }

        public function update( $new_instance, $old_instance ) {
            $instance = array();
            $instance['wid_title'] = strip_tags( $new_instance['wid_title'] );
            return $instance;
        }

        public function openidloginForm()
        {
            if (!FBTL_openid_is_customer_registered()) {
                

                $selected_theme = esc_attr(get_option('FBTL_openid_login_theme'));
                $appsConfigured = get_option('FBTL_openid_google_enable') | get_option('FBTL_openid_salesforce_enable') | get_option('FBTL_openid_facebook_enable') | get_option('FBTL_openid_linkedin_enable') | get_option('FBTL_openid_instagram_enable') | get_option('FBTL_openid_amazon_enable') | get_option('FBTL_openid_windowslive_enable') | get_option('FBTL_openid_twitter_enable') | get_option('FBTL_openid_vkontakte_enable');
                $spacebetweenicons = esc_attr(get_option('FBTL_login_icon_space'));
                $customWidth = esc_attr(get_option('FBTL_login_icon_custom_width'));
                $customHeight = esc_attr(get_option('FBTL_login_icon_custom_height'));
                $customSize = esc_attr(get_option('FBTL_login_icon_custom_size'));
                $customBackground = esc_attr(get_option('FBTL_login_icon_custom_color'));
                $customTheme = esc_attr(get_option('FBTL_openid_login_custom_theme'));
                $customTextofTitle = esc_attr(get_option('FBTL_openid_login_button_customize_text'));
                $customBoundary = esc_attr(get_option('FBTL_login_icon_custom_boundary'));
                $customLogoutName = esc_attr(get_option('FBTL_openid_login_widget_customize_logout_name_text'));
                $customLogoutLink = (get_option('FBTL_openid_login_widget_customize_logout_text'));
                $customTextColor= esc_attr(get_option('FBTL_login_openid_login_widget_customize_textcolor'));
                $customText = esc_html(get_option('FBTL_openid_login_widget_customize_text'));
    
                $facebook_custom_app = esc_attr($this->if_custom_app_exists('facebook'));
                $twitter_custom_app = esc_attr($this->if_custom_app_exists('twitter'));
                
                if (get_option('FBTL_openid_gdpr_consent_enable')) {
                    $gdpr_setting = "disabled='disabled'";
                } else
                    $gdpr_setting = '';

                $url = esc_url(get_option('FBTL_openid_privacy_policy_url'));
                $text = esc_html(get_option('FBTL_openid_privacy_policy_text'));

                if (!empty($text) && strpos(get_option('FBTL_openid_gdpr_consent_message'), $text)) {
                    $consent_message = str_replace(get_option('FBTL_openid_privacy_policy_text'), '<a target="_blank" href="' . $url . '">' . $text . '</a>', get_option('FBTL_openid_gdpr_consent_message'));
                } else if (empty($text)) {
                    $consent_message = get_option('FBTL_openid_gdpr_consent_message');
                }

                if (!is_user_logged_in()) {

                    if ($appsConfigured) {
                        $this->FBTL_openid_load_login_script();
                        ?>

                        <div class="fbtl_openid-app-icons">

                            <p style="color:#<?php echo $customTextColor ?>"><?php echo $customText ?>
                            </p>
                            <?php if (get_option('FBTL_openid_gdpr_consent_enable')) {
                                $consent_message = isset($consent_message) ? $consent_message : ''; ?>
                                <label class="fbtl_consent" style="padding-right: 10px;"><input type="checkbox"
                                                                                              onchange="FBTL_openid_on_consent_change(this,value)"
                                                                                              value="1"
                                                                                              id="FBTL_openid_consent_checkbox"><?php echo $consent_message; ?>
                                </label>
                                <br>
                            <?php }

                            if ($customTheme == 'default') {

                                if (get_option('FBTL_openid_facebook_enable')) {
                                    if ($selected_theme == 'longbutton') {

                                        ?>

                                        <a  rel='nofollow' <?php echo $gdpr_setting; ?>  onClick="fbtl_jplLogin('facebook','<?php echo $facebook_custom_app?>');" style="width:35px !important;padding-top:<?php echo $customHeight-27?>px !important;padding-bottom:<?php echo $customHeight-29 ?>px !important;margin-bottom:<?php echo $spacebetweenicons-5 ?>px !important;border-radius:<?php echo $customBoundary ?>px !important;" class="btn_fbtl btn btn-block btn-social btn-facebook  btn-custom-size login-button"  ><svg xmlns="http://www.w3.org/2000/svg" style="padding-top: <?php echo $customHeight-30?>px;border-right:none;margin-left: 2%;" ><path fill="#fff" d="M22.688 0H1.323C.589 0 0 .589 0 1.322v21.356C0 23.41.59 24 1.323 24h11.505v-9.289H9.693V11.09h3.124V8.422c0-3.1 1.89-4.789 4.658-4.789 1.322 0 2.467.1 2.8.145v3.244h-1.922c-1.5 0-1.801.711-1.801 1.767V11.1h3.59l-.466 3.622h-3.113V24h6.114c.734 0 1.323-.589 1.323-1.322V1.322A1.302 1.302 0 0 0 22.688 0z"/></svg><?php
                                                echo get_option('FBTL_openid_login_button_customize_text'); ?> Facebook</a>
                                        <?php

                                    } else {
                                        ?>
                                        <a class="<?php if (get_option('FBTL_openid_gdpr_consent_enable')) {
                                            
                                        } ?> login-button" rel='nofollow'
                                           title="<?php echo $customTextofTitle ?> Facebook"
                                           onClick="fbtl_jplLogin('facebook','<?php echo $facebook_custom_app ?>');"><img
                                                    alt='Facebook'
                                                    style="width:35px; !important;margin-left:<?php echo $spacebetweenicons - 4 ?>px !important"
                                                    src="<?php echo plugins_url('images/icons/facebook.png', __FILE__) ?>"
                                                    class="<?php echo $selected_theme; ?> <?php if (get_option('FBTL_openid_gdpr_consent_enable')) {
                                                        
                                                    } ?> login-button"></a>
                                    <?php }

                                }

                                

                               

                                if (get_option('FBTL_openid_twitter_enable')) {
                                    if ($selected_theme == 'longbutton') {
                                        ?> <a rel='nofollow' <?php echo $gdpr_setting; ?>
                                              onClick="fbtl_jplLogin('twitter','<?php echo $twitter_custom_app ?>');"
                                              style="width:35px !important;padding-top:<?php echo $customHeight - 29 ?>px !important;padding-bottom:<?php echo $customHeight - 29 ?>px !important;margin-bottom:<?php echo $spacebetweenicons - 5 ?>px !important;border-radius:<?php echo $customBoundary ?>px !important;"
                                              class="btn_fbtl btn btn-block btn-social btn-twitter btn-custom-size login-button">
                                            <i style="padding-top:<?php echo $customHeight - 35 ?>px !important"
                                               class="fa fa-twitter"></i><?php
                                            echo get_option('FBTL_openid_login_button_customize_text'); ?> Twitter</a>
                                    <?php } else { ?>


                                        <a class="<?php if (get_option('FBTL_openid_gdpr_consent_enable')) {
                                            echo "dis";
                                        } ?> login-button" rel='nofollow'
                                           title="<?php echo $customTextofTitle ?> Twitter"
                                           onClick="fbtl_jplLogin('twitter','<?php echo $twitter_custom_app ?>');"><img
                                                    alt='Twitter'
                                                    style="width:35px !important;height:<?php echo $customSize ?>px !important;margin-left:2px !important"
                                                    src="<?php echo plugins_url('images/icons/twitter.png', __FILE__) ?>"
                                                    class="<?php echo $selected_theme; ?> <?php if (get_option('FBTL_openid_gdpr_consent_enable')) {
                                                        echo "dis";
                                                    } ?> login-button"></a>
                                    <?php }
                                }

                           
                            }
                            ?>



                            <?php
                            if ($customTheme == 'custom') {
                                if (get_option('FBTL_openid_facebook_enable')) {
                                    if ($selected_theme == 'longbutton') {
                                        ?> <a rel='nofollow'
                                              <?php echo $gdpr_setting; ?>onClick="fbtl_jplLogin('facebook','<?php echo $facebook_custom_app ?>');"
                                              style="width:35px !important;padding-top:<?php echo $customHeight - 29 ?>px !important;padding-bottom:<?php echo $customHeight - 29 ?>px !important;margin-bottom:<?php echo $spacebetweenicons - 5 ?>px !important;background:<?php echo "#" . $customBackground ?> !important;border-radius:<?php echo $customBoundary ?>px !important;"
                                              class="btn_fbtl btn btn-block btn-social btn-facebook  btn-custom-size login-button">
                                            <i style="padding-top:<?php echo $customHeight - 35 ?>px !important"
                                               class="fa"><svg xmlns="http://www.w3.org/2000/svg" style="padding-top:12%;border-right:none;margin-left: 2%;" ><path fill="#fff" d="M22.688 0H1.323C.589 0 0 .589 0 1.322v21.356C0 23.41.59 24 1.323 24h11.505v-9.289H9.693V11.09h3.124V8.422c0-3.1 1.89-4.789 4.658-4.789 1.322 0 2.467.1 2.8.145v3.244h-1.922c-1.5 0-1.801.711-1.801 1.767V11.1h3.59l-.466 3.622h-3.113V24h6.114c.734 0 1.323-.589 1.323-1.322V1.322A1.302 1.302 0 0 0 22.688 0z"/></svg></i><?php
                                            echo get_option('FBTL_openid_login_button_customize_text'); ?> Facebook</a>
                                    <?php } else { ?>

                                        <a class="<?php if (get_option('FBTL_openid_gdpr_consent_enable')) {
                                            echo "dis";
                                        } ?> login-button" rel='nofollow'
                                           onClick="fbtl_jplLogin('facebook','<?php echo $facebook_custom_app ?>');"
                                           title="<?php echo $customTextofTitle ?> Facebook"><i
                                                    style="margin-top:10px;!important;width:35px !important;margin-left:<?php echo $spacebetweenicons - 4 ?>px !important;background:<?php echo "#" . $customBackground ?> !important;font-size:<?php echo $customSize - 16 ?>px !important;"
                                                    class="fa fa-facebook custom-login-button <?php echo $selected_theme; ?>"></i></a>

                                    <?php }

                                }

                               

                                

                                if (get_option('FBTL_openid_twitter_enable')) {
                                    if ($selected_theme == 'longbutton') {
                                        ?>

                                        <a rel='nofollow' <?php echo $gdpr_setting; ?>
                                           onClick="fbtl_jplLogin('twitter','<?php echo $twitter_custom_app ?>');"
                                           style="width:<?php echo $customWidth ?>px !important;padding-top:<?php echo $customHeight - 29 ?>px !important;padding-bottom:<?php echo $customHeight - 29 ?>px !important;margin-bottom:<?php echo $spacebetweenicons - 5 ?>px !important; background:<?php echo "#" . $customBackground ?> !important;border-radius:<?php echo $customBoundary ?>px !important;"
                                           class="btn btn_fbtl btn-block btn-social btn-customtheme btn-custom-size login-button">
                                            <i style="padding-top:<?php echo $customHeight - 35 ?>px !important"
                                               class="fa fa-twitter"></i><?php
                                            echo get_option('FBTL_openid_login_button_customize_text'); ?> Twitter</a>
                                    <?php } else { ?>
                                        <a class="<?php if (get_option('FBTL_openid_gdpr_consent_enable')) {
                                            echo "dis";
                                        } ?> login-button" rel='nofollow'
                                           onClick="fbtl_jplLogin('twitter','<?php echo $twitter_custom_app ?>');"
                                           title="<?php echo $customTextofTitle ?> Twitter"><i
                                                    style="margin-top:10px;width:<?php echo $customSize ?>px !important;height:<?php echo $customSize ?>px !important;margin-left:<?php echo $spacebetweenicons - 4 ?>px !important;background:<?php echo "#" . $customBackground ?> !important;font-size:<?php echo $customSize - 16 ?>px !important;"
                                                    class="fa fa-twitter custom-login-button <?php echo $selected_theme; ?>"></i></a>
                                        <?php
                                    }
                                }     


                            }
                            ?>
                            <br>
                        </div>
                        <?php


                    } else {
                        ?>
                        <div><p>please configure your app or contact the site admin </p></div>
                        <?php
                    }
                    if ($appsConfigured && get_option('fbtl_jpl_logo_check') == 1) {
                        $logo_html = $this->FBTL_openid_customize_logo();
                        echo $logo_html;
                    }
                    ?>
                    <br/>
                    <?php
                } else {
                    global $current_user;
                    $current_user = wp_get_current_user();
                    $customLogoutName = str_replace('##username##', $current_user->display_name, $customLogoutName);
                    $link_with_username = $customLogoutName;
                    if (empty($customLogoutName) || empty($customLogoutLink)) {
                        ?>
                        <div id="logged_in_user" class="FBTL_openid_login_wid">
                            <li><?php echo $link_with_username; ?> <a href="<?php echo wp_logout_url(site_url()); ?>"
                                                                      title="<?php _e('Logout', 'fbtl'); ?>"><?php _e($customLogoutLink, 'fbtl'); ?></a>
                            </li>
                        </div>
                        <?php

                    } else {
                        ?>
                        <div id="logged_in_user" class="FBTL_openid_login_wid">
                            <li><?php echo $link_with_username; ?> <a href="<?php echo wp_logout_url(site_url()); ?>"
                                                                      title="<?php _e('Logout', 'fbtl'); ?>"><?php _e($customLogoutLink, 'fbtl'); ?></a>
                            </li>
                        </div>
                        <?php
                    }
                }
            }
        }

        public function FBTL_openid_customize_logo(){
            $logo =" <div style='float:left;' class='FBTL_image_id'>
			<a target='_blank' href='https://#'>
			<img alt='logo' src='". plugins_url('/images/logo.png',__FILE__) ."' class='FBTL_openid_image'>
			</a>
			</div>
			<br/>";
            return $logo;
        }

        public function if_custom_app_exists($app_name){
            if(get_option('FBTL_openid_apps_list'))
                $appslist = maybe_unserialize(get_option('FBTL_openid_apps_list'));
            else
                $appslist = array();

            foreach( $appslist as $key => $app){
                $option = 'FBTL_openid_enable_custom_app_' . $key;
                if($app_name == $key && get_option($option) == '1')
                    return 'true';
            }
            return 'false';
        }

        public function openidloginFormShortCode( $atts )
        {

            if (!FBTL_openid_is_customer_registered()) {
                global $post;
                $html = '';
                $selected_theme = isset( $atts['shape'] )? esc_attr($atts['shape']) : esc_attr(get_option('FBTL_openid_login_theme'));
                $appsConfigured = get_option('FBTL_openid_google_enable') | get_option('FBTL_openid_salesforce_enable') | get_option('FBTL_openid_facebook_enable') | get_option('FBTL_openid_linkedin_enable') | get_option('FBTL_openid_instagram_enable') | get_option('FBTL_openid_amazon_enable') | get_option('FBTL_openid_windowslive_enable') |get_option('FBTL_openid_twitter_enable') | get_option('FBTL_openid_vkontakte_enable');
                $spacebetweenicons = isset( $atts['space'] )? esc_attr(intval($atts['space'])) : esc_attr(intval(get_option('FBTL_login_icon_space')));
                $customWidth = isset( $atts['width'] )? esc_attr(intval($atts['width'])) : esc_attr(intval(get_option('FBTL_login_icon_custom_width')));
                $customHeight = isset( $atts['height'] )? esc_attr(intval($atts['height'])) : esc_attr(intval(get_option('FBTL_login_icon_custom_height')));
                $customSize = isset( $atts['size'] )? esc_attr(intval($atts['size'])) : esc_attr(intval(get_option('FBTL_login_icon_custom_size')));
                $customBackground = isset( $atts['background'] )? esc_attr($atts['background']) : esc_attr(get_option('FBTL_login_icon_custom_color'));
                $customTheme = isset( $atts['theme'] )? esc_attr($atts['theme']) : esc_attr(get_option('FBTL_openid_login_custom_theme'));
                $buttonText = esc_html(get_option('FBTL_openid_login_button_customize_text'));
                $customTextofTitle = esc_attr(get_option('FBTL_openid_login_button_customize_text'));
                $logoutUrl = esc_url(wp_logout_url(site_url()));
                $customBoundary = isset( $atts['edge'] )? esc_attr($atts['edge']) : esc_attr(get_option('FBTL_login_icon_custom_boundary'));
                $customLogoutName = esc_attr(get_option('FBTL_openid_login_widget_customize_logout_name_text'));
                $customLogoutLink = (get_option('FBTL_openid_login_widget_customize_logout_text'));
                $customTextColor= isset( $atts['color'] )? esc_attr($atts['color']) : esc_attr(get_option('FBTL_login_openid_login_widget_customize_textcolor'));
                $customText=isset( $atts['heading'] )? esc_html($atts['heading']) :esc_html(get_option('FBTL_openid_login_widget_customize_text'));

                $facebook_custom_app = esc_attr($this->if_custom_app_exists('facebook'));
                $twitter_custom_app = esc_attr($this->if_custom_app_exists('twitter'));
                

                if ($selected_theme == 'longbuttonwithtext') {
                    $selected_theme = 'longbutton';
                }
                if ($customTheme == 'custombackground') {
                    $customTheme = 'custom';
                }

                if (get_option('FBTL_openid_gdpr_consent_enable')) {
                    $gdpr_setting = "disabled='disabled'";
                } else
                    $gdpr_setting = '';

                $url = esc_url(get_option('FBTL_openid_privacy_policy_url'));
                $text = esc_html(get_option('FBTL_openid_privacy_policy_text'));

                if (!empty($text) && strpos(get_option('FBTL_openid_gdpr_consent_message'), $text)) {
                    $consent_message = str_replace(get_option('FBTL_openid_privacy_policy_text'), '<a target="_blank" href="' . $url . '">' . $text . '</a>', get_option('FBTL_openid_gdpr_consent_message'));
                } else if (empty($text)) {
                    $consent_message = get_option('FBTL_openid_gdpr_consent_message');
                }

                if (get_option('FBTL_openid_gdpr_consent_enable')) {
                    $dis = "dis";
                } else {
                    $dis = '';
                }

                if (!is_user_logged_in()) {

                    if ($appsConfigured) {
                        $this->FBTL_openid_load_login_script();
                        $html .= "<div class='fbtl_openid-app-icons'>
	 
					 <p style='color:#" . $customTextColor . "'> $customText</p>";

                        if (get_option('FBTL_openid_gdpr_consent_enable')) {
                            $html .= '<label class="fbtl_consent"><input type="checkbox" onchange="FBTL_openid_on_consent_change(this,value)" value="1" id="FBTL_openid_consent_checkbox">';
                            $html .= $consent_message . '</label><br>';
                        }

                        if ($customTheme == 'default') {

                            if (get_option('FBTL_openid_facebook_enable')) {
                                if ($selected_theme == 'longbutton') {
                                    $html .= "<a  rel='nofollow' " . $gdpr_setting . " style='width: " . $customWidth . "px !important;padding-top:" . ($customHeight - 29) . "px !important;padding-bottom:" . ($customHeight - 29) . "px !important;margin-bottom: " . ($spacebetweenicons - 5) . "px !important;border-radius: " . $customBoundary . "px !important;' class='btn btn_fbtl btn-block btn-social btn-facebook btn-custom-dec login-button' onClick=\"fbtl_jplLogin('facebook','" . $facebook_custom_app . "');\"> <svg xmlns=\"http://www.w3.org/2000/svg\" style=\"padding-top:".($customHeight-31)."px;border-right:none;margin-left: 2%;\" ><path fill=\"#fff\" d=\"M22.688 0H1.323C.589 0 0 .589 0 1.322v21.356C0 23.41.59 24 1.323 24h11.505v-9.289H9.693V11.09h3.124V8.422c0-3.1 1.89-4.789 4.658-4.789 1.322 0 2.467.1 2.8.145v3.244h-1.922c-1.5 0-1.801.711-1.801 1.767V11.1h3.59l-.466 3.622h-3.113V24h6.114c.734 0 1.323-.589 1.323-1.322V1.322A1.302 1.302 0 0 0 22.688 0z\"/></svg>" . $buttonText . " Facebook</a>";
                                } else {
                                    $html .= "<a class='" . $dis . " login-button' rel='nofollow' title= ' " . $customTextofTitle . " Facebook' onClick=\"fbtl_jplLogin('facebook','" . $facebook_custom_app . "');\" ><img alt='Facebook' style='width:35px !important;height: 37px !important;margin-left: " . ($spacebetweenicons) . "px !important' src='" . plugins_url('images/icons/facebook.png', __FILE__) . "' class='" . $dis . " login-button " . $selected_theme . "' ></a>";
                                }

                            }

                            

                           

                            if (get_option('FBTL_openid_twitter_enable')) {
                                if ($selected_theme == 'longbutton') {
                                    $html .= "<a rel='nofollow'  " . $gdpr_setting . " style='width: 35px !important;padding-top:" . ($customHeight - 29) . "px !important;margin-left:5px;padding-bottom:" . ($customHeight - 29) . "px !important;margin-bottom: " . ($spacebetweenicons - 5) . "px !important;border-radius: " . $customBoundary . "px !important;' class='btn btn_fbtl btn-block btn-social btn-twitter btn-custom-dec login-button' onClick=\"fbtl_jplLogin('twitter','" .
                                        $twitter_custom_app .
                                        "');\"> <i style='padding-top:" . ($customHeight - 35) . "px !important' class='fa fa-twitter'></i>" . $buttonText . " Twitter</a>";
                                } else {
                                    $html .= "<a class='" . $dis . " login-button' rel='nofollow' title= ' " . $customTextofTitle . " Twitter' onClick=\"fbtl_jplLogin('twitter','" .
                                        $twitter_custom_app . "');\" ><img alt='Twitter' style=' width:35px !important;height: 37px !important;margin-left: 5px !important' src='" . plugins_url('images/icons/twitter.png', __FILE__) . "' class='" . $dis . " login-button " . $selected_theme . "' ></a>";
                                }

                            }

                      
                        }


                        if ($customTheme == 'custom') {
                            if (get_option('FBTL_openid_facebook_enable')) {
                                if ($selected_theme == 'longbutton') {
                                    $html .= "<a rel='nofollow'   " . $gdpr_setting . " onClick=\"fbtl_jplLogin('facebook','" . $facebook_custom_app . "');\" style='width:" . ($customWidth) . "px !important;padding-top:" . ($customHeight - 29) . "px !important;padding-bottom:" . ($customHeight - 29) . "px !important;margin-bottom:" . ($spacebetweenicons - 5) . "px !important; background:#" . $customBackground . "!important;border-radius: " . $customBoundary . "px !important;' class='btn btn_fbtl btn-block btn-social btn-customtheme btn-custom-dec login-button' > <i style='padding-top:" . ($customHeight - 35) . "px !important' class='fa fa-facebook'></i> " . $buttonText . " Facebook</a>";
                                } else {
                                    $html .= "<a class='" . $dis . " login-button' rel='nofollow' title= ' " . $customTextofTitle . " Facebook' onClick=\"fbtl_jplLogin('facebook','" . $facebook_custom_app . "');\" ><i style='margin-top:10px;width:" . $customSize . "px !important;height:" . $customSize . "px !important;margin-left:" . ($spacebetweenicons) . "px !important;background:#" . $customBackground . " !important;font-size: " . ($customSize - 16) . "px !important;'  class='fa btn_fbtl fa-facebook custom-login-button  " . $selected_theme . "' ></i></a>";
                                }

                            }

                            

                            

                            if (get_option('FBTL_openid_twitter_enable')) {
                                if ($selected_theme == 'longbutton') {
                                    $html .= "<a  rel='nofollow'   " . $gdpr_setting . "onClick=\"fbtl_jplLogin('twitter','" . $twitter_custom_app . "');\" style='width:" . ($customWidth) . "px !important;padding-top:" . ($customHeight - 29) . "px !important;padding-bottom:" . ($customHeight - 29) . "px !important;margin-bottom:" . ($spacebetweenicons - 5) . "px !important; background:#" . $customBackground . "!important;border-radius: " . $customBoundary . "px !important;' class='btn btn_fbtl btn-block btn-social btn-customtheme btn-custom-dec login-button' > <i style='padding-top:" . ($customHeight - 35) . "px !important' class='fa fa-twitter'></i> " . $buttonText . " Twitter</a>";
                                } else {
                                    $html .= "<a class='" . $dis . " login-button' rel='nofollow' title= ' " . $customTextofTitle . " Twitter' onClick=\"fbtl_jplLogin('twitter','" . $twitter_custom_app . "');\" ><i style='margin-top:10px;width:" . $customSize . "px !important;height:" . $customSize . "px !important;margin-left:" . ($spacebetweenicons) . "px !important;background:#" . $customBackground . " !important;font-size: " . ($customSize - 16) . "px !important;'  class='fa btn_fbtl fa-twitter custom-login-button  " . $selected_theme . "' ></i></a>";
                                }

                            }
                            
                         
                        
                        }
                        $html .= '</div> <br>';

                    } else {

                        $html .= '<div>No apps configured. Please contact your administrator.</div>';

                    }
                    if ($appsConfigured && get_option('fbtl_jpl_logo_check') == 1) {
                        $logo_html = $this->FBTL_openid_customize_logo();
                        $html .= $logo_html;
                    }
                    ?>
                    <?php
                } else {
                    global $current_user;
                    $current_user = wp_get_current_user();
                    $customLogoutName = str_replace('##username##', $current_user->display_name, $customLogoutName);
                    $fbtl = __($customLogoutLink, "fbtl");
                    if (empty($customLogoutName) || empty($customLogoutLink)) {
                        $html .= '<div id="logged_in_user" class="FBTL_openid_login_wid">' . $customLogoutName . ' <a href=' . $logoutUrl . ' title=" ' . $fbtl . '"> ' . $fbtl . '</a></div>';
                    } else {
                        $html .= '<div id="logged_in_user" class="FBTL_openid_login_wid">' . $customLogoutName . ' <a href=' . $logoutUrl . ' title=" ' . $fbtl . '"> ' . $fbtl . '</a></div>';
                    }
                }
                return $html;
            }
        }

        private function FBTL_openid_load_login_script() {

            if(!get_option('FBTL_openid_gdpr_consent_enable')){?>
                <script>
                    jQuery(".btn_fbtl").prop("disabled",false);
                </script>
            <?php }
            
            ?>
            <script type="text/javascript">
                function FBTL_openid_on_consent_change(checkbox,value){

                    if (value == 0) {
                        jQuery('#FBTL_openid_consent_checkbox').val(1);
                        jQuery(".btn_fbtl").attr("disabled",true);
                        jQuery(".login-button").addClass("dis");
                    }
                    else {
                        jQuery('#FBTL_openid_consent_checkbox').val(0);
                        jQuery(".btn_fbtl").attr("disabled",false);
                        jQuery(".login-button").removeClass("dis");
                    }
                }

                function fbtl_jplLogin(app_name,fbtl_if_custom_app) {
                    var current_url = window.location.href;
                    var cookie_name = "redirect_current_url";
                    var d = new Date();
                    d.setTime(d.getTime() + (2 * 24 * 60 * 60 * 1000));
                    var expires = "expires="+d.toUTCString();
                    document.cookie = cookie_name + "=" + current_url + ";" + expires + ";path=/";

                    <?php
                    if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
                        $http = "https://";
                    } else {
                        $http =  "http://";
                    }
                    ?>
                    var base_url = '<?php echo esc_url(site_url());?>';
                    var request_uri = '<?php echo $_SERVER['REQUEST_URI'];?>';
                    var http = '<?php echo $http;?>';
                    var http_host = '<?php echo $_SERVER['HTTP_HOST'];?>';
                    var default_nonce = '<?php echo wp_create_nonce( 'fbtl_openid-get-social-login-nonce' ); ?>';
                    var custom_nonce = '<?php echo wp_create_nonce( 'fbtl_openid-oauth-app-nonce' ); ?>';

                   if(fbtl_if_custom_app == 'false'){

                        if ( request_uri.indexOf('wp-login.php') !=-1){
                            var redirect_url = base_url + '/?option=fbtlsociallog&wp_nonce=' + default_nonce + '&app_name=';


                        }else {
                            var redirect_url = http + http_host + request_uri;
                            if(redirect_url.indexOf('?') != -1){
                                redirect_url = redirect_url +'&option=fbtlsociallog&wp_nonce=' + default_nonce + '&app_name=';

                            }
                            else
                            {
                                redirect_url = redirect_url +'?option=fbtlsociallog&wp_nonce=' + default_nonce + '&app_name=';

                            }
                        }
                    }
                    else {

                        if ( request_uri.indexOf('wp-login.php') !=-1){
                            var redirect_url = base_url + '/?option=oauthredirect&wp_nonce=' + custom_nonce + '&app_name=';

                        }else {
                            var redirect_url = http + http_host + request_uri;
                            if(redirect_url.indexOf('?') != -1)
                                redirect_url = redirect_url +'&option=oauthredirect&wp_nonce=' + custom_nonce + '&app_name=';
                            else
                                redirect_url = redirect_url +'?option=oauthredirect&wp_nonce=' + custom_nonce + '&app_name=';
                        }

                    }

                    window.location.href = redirect_url + app_name;

                }
            </script>
            <?php
        }
    }

   


   

    function FBTL_openid_start_session() {
        if( !session_id() ) {
            session_start();
        }
    }

    function FBTL_openid_end_session() {
        session_start();
        session_unset(); //unsets all session variables
    }

    function fbtl_encrpt_data($data, $key) {
        return base64_encode(openssl_encrypt($data, 'aes-128-ecb', $key, OPENSSL_RAW_DATA));
    }

    function fbtl_decrpt_data($data, $key) {

        return openssl_decrypt( base64_decode($data), 'aes-128-ecb', $key, OPENSSL_RAW_DATA);

    }

    function FBTL_openid_login_validate(){

        if((isset($_POST['action'])) && (strpos($_POST['action'], 'delete_social_profile_data') !== false) && isset($_POST['user_id'])){
            // delete first name, last name, user_url and profile_url from usermeta
            $id = sanitize_text_field($_POST['user_id']);
            FBTL_openid_delete_social_profile($id);
        }

        // ajax call -  custom app over default app
        else if ((isset($_POST['selected_app'])) && (isset($_POST['selected_app_value']))){
            if($_POST['selected_app_value'] == 'true'){
                //if custome app enable
                if($_POST['selected_app']=="facebook") {
                    update_option('FBTL_openid_facebook_enable',1);
                }
                $option = 'FBTL_openid_enable_custom_app_' . sanitize_text_field($_POST['selected_app']);
                update_option( $option, '1');
            }
            else{
                //if custome app Disable
                if($_POST['selected_app']=="facebook") {
                    update_option('FBTL_openid_facebook_enable',0);
                }
                $option = 'FBTL_openid_enable_custom_app_' . sanitize_text_field($_POST['selected_app']);
                update_option( $option, '0');
            }
            exit;
        }
        
		else if ((isset($_POST['appname'])) && (isset($_POST['test_configuration']))){
            update_option( 'FBTL_openid_test_configuration', 1);
            exit;
        }
        
		else if( isset($_POST['FBTL_openid_show_profile_form_nonce']) and isset( $_POST['option'] ) and strpos( $_POST['option'], 'FBTL_openid_show_profile_form' ) !== false ){
            $nonce = $_POST['FBTL_openid_show_profile_form_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-user-show-profile-form-nonce' ) ) {
				wp_die('<strong>ERROR</strong>: Invalid Request.');
            } else {
                $last_name = sanitize_text_field($_POST["last_name"]);
                $first_name = sanitize_text_field($_POST["first_name"]);
                $full_name = sanitize_text_field($_POST["user_full_name"]);
                $url = sanitize_text_field($_POST["user_url"]); 
                $user_picture = sanitize_text_field($_POST["user_picture"]); 
                $username_field = sanitize_text_field($_POST['username_field']); 
                $email_field = sanitize_email($_POST['email_field']);
                $decrypted_app_name = sanitize_text_field($_POST["decrypted_app_name"]);
                $decrypted_user_id = sanitize_text_field($_POST["decrypted_user_id"]);
                echo FBTL_openid_profile_completion_form($last_name, $first_name, $full_name, $url, $user_picture, $username_field, $email_field, $decrypted_app_name, $decrypted_user_id);
                exit;
            }
        }

        else if( isset($_POST['FBTL_openid_account_linking_nonce']) and isset( $_POST['option'] ) and strpos( $_POST['option'], 'FBTL_openid_account_linking' ) !== false ){
            $nonce = $_POST['FBTL_openid_account_linking_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-account-linking-nonce' ) ) {
				wp_die('<strong>ERROR</strong>: Invalid Request.');
            } else {
                FBTL_openid_start_session();
                //link account
                if(!isset($_POST['FBTL_openid_create_new_account'])){
                    $nonce = wp_create_nonce( 'fbtl_openid-disable-social-login-nonce' );
                    $url = site_url().'/wp-login.php?option=disable-social-login&wp_nonce=' . $nonce;
                    header('Location:'. $url);
                    exit;
                }
                //create new account
                else {
                    $username = sanitize_text_field($_POST['username']);
                    $user_email = sanitize_email($_POST['user_email']);
                    $first_name = sanitize_text_field($_POST['first_name']);
                    $last_name = sanitize_text_field($_POST['last_name']);
                    $user_full_name = sanitize_text_field($_POST['user_full_name']);
                    $user_url = sanitize_text_field($_POST['user_url']);
                    $user_picture = sanitize_text_field($_POST['user_picture']);
                    $decrypted_app_name = sanitize_text_field($_POST['decrypted_app_name']);
                    $decrypted_user_id = sanitize_text_field($_POST['decrypted_user_id']);

                    FBTL_openid_process_account_linking($username, $user_email, $first_name, $last_name, $user_full_name, $user_url, $user_picture, $decrypted_app_name, $decrypted_user_id);
                }
            }
        }

        else if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'fbtlsociallog' ) !== false ) {
            if(isset($_REQUEST['wp_nonce'])){
                $nonce = $_REQUEST['wp_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-get-social-login-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    FBTL_openid_initialize_social_login();
                }
            }
        }

        else if( isset($_POST['FBTL_openid_profile_form_submitted_nonce']) and isset( $_POST['username_field']) and isset($_POST['email_field']) and $_POST['option'] == 'FBTL_openid_profile_form_submitted' ){
            $nonce = $_POST['FBTL_openid_profile_form_submitted_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-profile-form-submitted-nonce' ) ) {
				wp_die('<strong>ERROR</strong>: Invalid Request.' . $nonce);
            } else {
                $username = sanitize_text_field($_POST['username_field']);
                $user_email = sanitize_email($_POST['email_field']);
                $user_picture = sanitize_text_field($_POST["user_picture"]);
                $user_url = sanitize_text_field($_POST["user_url"]);
                $last_name = sanitize_text_field($_POST["last_name"]);
                $user_full_name = sanitize_text_field($_POST["user_full_name"]);
                $first_name = sanitize_text_field($_POST["first_name"]);
                $decrypted_app_name = sanitize_text_field($_POST["decrypted_app_name"]);
                $decrypted_user_id = sanitize_text_field($_POST["decrypted_user_id"]);

                FBTL_openid_save_profile_completion_form($username, $user_email, $first_name, $last_name, $user_full_name, $user_url, $user_picture, $decrypted_app_name, $decrypted_user_id);
            }
        }

        

        else if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'fbtl_jpl' ) !== false ){
            FBTL_openid_process_social_login();
        }

        else if( isset( $_REQUEST['autoregister'] ) and strpos( $_REQUEST['autoregister'],'false') !== false ) {
            if(!is_user_logged_in()) {
                FBTL_openid_disabled_register_message();
            }
        }

        else if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'oauthredirect' ) !== false ) {
            if(isset($_REQUEST['wp_nonce'])){
                $nonce = $_REQUEST['wp_nonce'];
                if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-oauth-app-nonce' ) ) {
                    wp_die('<strong>ERROR</strong>: Invalid Request.');
                } else {
                    $appname = sanitize_text_field($_REQUEST['app_name']);
                    FBTL_openid_custom_app_oauth_redirect($appname);
                }
            }
        }

        else if( strpos( $_SERVER['REQUEST_URI'], "openidcallback") !== false ||((strpos( $_SERVER['REQUEST_URI'], "oauth_token")!== false)&&(strpos( $_SERVER['REQUEST_URI'], "oauth_verifier") ) )) {
            FBTL_openid_process_custom_app_callback();
        }        
    }

    function FBTL_openid_json_to_htmltable($arr) {
        $str = "<table border='1'><tbody>";
        foreach ($arr as $key => $val) {
            $str .= "<tr>";
            $str .= "<td>$key</td>";
            $str .= "<td>";
            if (is_array($val)) {
                if (!empty($val)) {
                    $str .= FBTL_openid_json_to_htmltable($val);
                }
            } else {
                $str .= "<strong>$val</strong>";
            }
            $str .= "</td></tr>";
        }
        $str .= "</tbody></table>";

        return $str;
    }

    

    function FBTL_openid_username_already_exists($last_name,$first_name,$user_full_name,$user_url,$user_picture,$username,$user_email, $decrypted_app_name, $decrypted_user_id){
        $path = FBTL_openid_get_wp_style();
        $nonce = wp_create_nonce( 'fbtl_openid-user-profile-form-submitted-nonce' );
        $html = '<style>.form-input-validation.is-error {color: #d94f4f;}</style>
				<style>
                        .fbtlcmp {
                                     margin: auto !important;
                                 }
                        @media only screen and (max-width: 600px) {
                          .fbtlcmp {width: 90%;}
                        }
                        @media only screen and (min-width: 600px) {
                          .fbtlcmp {width: 500px;}
                        }
                </style>
				
				
				<head>
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				
          
                <body class="login login-action-login wp-core-ui  locale-en-us">
                <div style="position:fixed;background:#f1f1f1;"></div>
                <div id="add_field" style="position:fixed;top: 0;right: 0;bottom: 0;left: 0;z-index: 1;padding-top:130px;">
                <div class="fbtlcmp">   
                <form name="f" method="post" action="">
                <div style="background: white;margin-top:-15px;padding: 15px;">
               
                   <div style="text-align:center"><span style="font-size: 24px;font-family: Arial">'.esc_html(get_option('FBTL_profile_complete_title')).'</span></div>
                <p><br>
                <label for="user_login">'.esc_html(get_option('FBTL_profile_complete_username_label')).'<br/>
                <input type="text" class="input" name="username_field" value='.esc_attr($username).'  size="20" required>
                <span align="center" class="form-input-validation is-error">'.esc_html(get_option('FBTL_profile_complete_uname_exist')).'</span>
                </label>
                </p>
                <br>
                <p>
                <label for="user_pass">'.get_option('FBTL_profile_complete_email_label').'<br/>
                <input type="email"  name="email_field" class="input" value='.$user_email.' size="20" required></label>						
                </p>
                <input type="hidden" name="first_name" value='.esc_attr($first_name).'>
                <input type="hidden" name="last_name" value='.esc_attr($last_name).'>
                <input type="hidden" name="user_full_name" value='.esc_attr($user_full_name).'>
                <input type="hidden" name="user_url" value='.esc_url($user_url).'>
                <input type="hidden" name="user_picture" value='.esc_url($user_picture).'>
                <input type="hidden" name="decrypted_app_name" value='.esc_attr($decrypted_app_name).'>
                <input type="hidden" name="decrypted_user_id" value='.esc_attr($decrypted_user_id).'>					
                <input type="hidden" name="option" value="FBTL_openid_profile_form_submitted">
                <input type="hidden" name="FBTL_openid_user_profile_form_submitted_nonce" value="'.$nonce.'"/>
                </div>
                <p class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="'.get_option('FBTL_profile_complete_submit_button').'"/>
                </p> ';

        if(get_option('FBTL_openid_oauth')=='1' && get_option('fbtl_jpl_logo_check') == 1) {
            $html .= FBTL_openid_customize_logo();
        }

        $html.=    '</form>
                    </div>
                    </div>
                    </body>';
        return $html;

    }

    

    function FBTL_openid_account_linking_form($username,$user_email,$first_name,$last_name,$user_full_name,$user_url,$user_picture,$decrypted_app_name,$decrypted_user_id){
        $path = FBTL_openid_get_wp_style();
        $nonce = wp_create_nonce( 'fbtl_openid-account-linking-nonce' );
        $html =	"
		        <style>
                    .fbtlcmp {
                                 margin: auto !important;
                             }
                    @media only screen and (max-width: 600px) {
                      .fbtlcmp {width: 90%;}
                    }
                    @media only screen and (min-width: 600px) {
                      .fbtlcmp {width: 500px;}
                    }
                </style>
				<head>
				<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
				
                <body class='login login-action-login wp-core-ui  locale-en-us'>
                <div style=\"background:#f1f1f1;\"></div>
                <div id=\"add_field\" style=\"top: 0;right: 0;bottom: 0;left: 0;z-index: 1;padding-top:2%;\">
                <div class='fbtlcmp'>
                <form name = 'f' method = 'post' action='' style='margin-left: 2%;margin-right: 2%;'>
                <input type = 'hidden' name = 'option' value = 'FBTL_openid_account_linking'/>
                <input type='hidden' name='FBTL_openid_account_linking_nonce' value='". $nonce."'/>
                <input type='hidden' name='user_email' value=".esc_attr($user_email).">
                <input type='hidden' name='username' value=".esc_attr($username).">
                <input type='hidden' name='first_name' value=".esc_attr($first_name).">
                <input type='hidden' name='last_name' value=".esc_attr($last_name).">
                <input type='hidden' name='user_full_name' value=".esc_attr($user_full_name).">
                <input type='hidden' name='user_url' value=".esc_url($user_url).">
                <input type='hidden' name='user_picture' value=".esc_url($user_picture).">
                <input type='hidden' name='decrypted_app_name' value=".esc_attr($decrypted_app_name).">
                <input type='hidden' name='decrypted_user_id' value=".esc_attr($decrypted_user_id).">
                <div  style = 'background-color:white; padding:12px; top:100px; right: 350px; padding-bottom: 20px;left:350px; overflow:hidden; outline:1px black;border-radius: 5px;'>	
                
                <br>
                <div style=\"text-align:center\"><span style='font-size: 24px;font-family: Arial;text-align:center'>".esc_html(get_option('FBTL_account_linking_title'))."</span></div><br>
                <div style='padding: 12px;'></div>
                <div style=' padding: 16px;background-color:rgba(1, 145, 191, 0.117647);color: black;'>".get_option('FBTL_account_linking_new_user_instruction').".<br><br>".get_option('FBTL_account_linking_existing_user_instruction')."".get_option('FBTL_account_linking_extra_instruction')." 
                </div>                   
                <br><br>

                <input type = 'submit' value = '".esc_attr(get_option('FBTL_account_linking_existing_user_button'))."' name = 'FBTL_openid_link_account' class='button button-primary button-large' style = 'margin-left: 3%;margin-right: 0%;'/>
                    
                <input type = 'submit' value = '".esc_attr(get_option('FBTL_account_linking_new_user_button'))."' name = 'FBTL_openid_create_new_account' class='button button-primary button-large' style = 'margin-left: 5%margin-right: 5%;'/>";

        if(get_option('FBTL_openid_oauth')=='1' && get_option('fbtl_jpl_logo_check') == 1) {
            $html .= FBTL_openid_customize_logo();
        }

        $html .=   "</div>
                    </form>
                    </div>
                    </div>
                    </body>";
        return $html;
    }

    function FBTL_openid_decrypt_sanitize($param) {
        if(strcmp($param,'null')!=0 && strcmp($param,'')!=0){
            $customer_token = get_option('FBTL_openid_customer_token');
            $decrypted_token = fbtl_decrpt_data($param,$customer_token);
            // removes control characters and some blank characters
            $decrypted_token_sanitise = preg_replace('/[\x00-\x1F][\x7F][\x81][\x8D][\x8F][\x90][\x9D][\xA0][\xAD]/', '', $decrypted_token);
            //strips space,tab,newline,carriage return,NUL-byte,vertical tab.
            return trim($decrypted_token_sanitise);
        }else{
            return '';
        }
    }

    function FBTL_openid_link_account( $username, $user ){

        if($user){
            $userid = $user->ID;
        }
        FBTL_openid_start_session();

        $user_email =  isset($_SESSION['user_email']) ? sanitize_text_field($_SESSION['user_email']):'';
        $social_app_identifier = isset($_SESSION['social_user_id']) ? sanitize_text_field($_SESSION['social_user_id']):'';
        $social_app_name = isset($_SESSION['social_app_name']) ? sanitize_text_field($_SESSION['social_app_name']):'';

        //if user is coming through default wordpress login, do not proceed further and return
        if(isset($userid) && empty($social_app_identifier) && empty($social_app_name) ) {
            return;
        }
        elseif(!isset($userid)){
            return;
            //wp_die('No user is returned.');
        }

        global $wpdb;
        $db_prefix = $wpdb->prefix;
        $linked_email_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$db_prefix."FBTL_openid_linked_user where linked_email = \"%s\" AND linked_social_app = \"%s\"",$user_email,$social_app_name));

        // if a user with given email and social app name doesn't already exist in the FBTL_openid_linked_user table
        if(!isset($linked_email_id)){
            FBTL_openid_insert_query($social_app_name,$user_email,$userid,$social_app_identifier);
        }
    }

    function FBTL_openid_insert_query($social_app_name,$user_email,$userid,$social_app_identifier){

        // check if none of the column values are empty
        if(!empty($social_app_name) && !empty($user_email) && !empty($userid) && !empty($social_app_identifier)){

           
            $date = date('Y-m-d H:i:s');

            global $wpdb;
            $db_prefix = $wpdb->prefix;
            $table_name = $db_prefix. 'FBTL_openid_linked_user';

            $result = $wpdb->insert(
                $table_name,
                array(
                    'linked_social_app' => $social_app_name,
                    'linked_email' => $user_email,
                    'user_id' =>  $userid,
                    'identifier' => $social_app_identifier,
                    'timestamp' => $date,
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%s'
                )
            );
            if($result === false){
                wp_die('Error in insert query');
                $wpdb->show_errors();
                $wpdb->print_error();
                exit;
            }
        }
    }

    function FBTL_openid_send_email($user_id='', $user_url=''){
        if( get_option('FBTL_openid_email_enable') == 1) {
            global $wpdb;
            $admin_mail = get_option('FBTL_openid_admin_email');
            $user_name = ($user_id == '') ? "##UserName##" : ($wpdb->get_var($wpdb->prepare("SELECT user_login FROM {$wpdb->users} WHERE ID = %d", $user_id)));
            $content = get_option('FBTL_openid_register_email_message');
            $subject = "[" . get_bloginfo('name') . "] New User Registration - Social Login";
            $content = str_replace('##User Name##', $user_name, $content);
            $headers = "Content-Type: text/html";
            wp_mail($admin_mail, $subject, $content, $headers);
        }
    }

    function FBTL_openid_disabled_register_message() {
        $message = get_option('FBTL_openid_register_disabled_message').' Go to <a href="' . site_url() .'">Home Page</a>';
        wp_die($message);
    }

    function FBTL_openid_get_redirect_url() {

        $current_url = isset($_COOKIE["redirect_current_url"]) ? $_COOKIE["redirect_current_url"]:'';
        $pos = strpos($_SERVER['REQUEST_URI'], '/openidcallback');

        if ($pos === false) {
            $url = str_replace('?option=fbtl_jpl','',$_SERVER['REQUEST_URI']);
            $current_url = str_replace('?option=fbtl_jpl','',$current_url);

        } else {
            $temp_array1 = explode('/openidcallback',$_SERVER['REQUEST_URI']);
            $url = $temp_array1[0];
            $temp_array2 = explode('/openidcallback',$current_url);
            $current_url = $temp_array2[0];
        }

        $option = get_option( 'FBTL_openid_login_redirect' );
        $redirect_url = site_url();

        if( $option == 'same' ) {
            if(!is_null($current_url)){
                if(strpos($current_url,get_option('siteurl').'/wp-login.php')!== false)
                {
                    $redirect_url=get_option('siteurl');
                }
                    else
                        $redirect_url = $current_url;
            }
            else{
                if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
                    $http = "https://";
                } else {
                    $http =  "http://";
                }
                $redirect_url = urldecode(html_entity_decode(esc_url($http . $_SERVER["HTTP_HOST"] . $url)));
                if(html_entity_decode(esc_url(remove_query_arg('ss_message', $redirect_url))) == wp_login_url() || strpos($_SERVER['REQUEST_URI'],'wp-login.php') !== FALSE || strpos($_SERVER['REQUEST_URI'],'wp-admin') !== FALSE){
                    $redirect_url = site_url().'/';
                }
            }
        } else if( $option == 'homepage' ) {
            $redirect_url = site_url();
        } else if( $option == 'dashboard' ) {
            $redirect_url = admin_url();
        } else if( $option == 'custom' ) {
            $redirect_url = get_option('FBTL_openid_login_redirect_url');
        }else if($option == 'relative') {
            $redirect_url =  site_url() . (null !== get_option('FBTL_openid_relative_login_redirect_url')?get_option('FBTL_openid_relative_login_redirect_url'):'');
        }

        if(strpos($redirect_url,'?') !== FALSE) {
            $redirect_url .= get_option('FBTL_openid_auto_register_enable') ? '' : '&autoregister=false';
        } else{
            $redirect_url .= get_option('FBTL_openid_auto_register_enable') ? '' : '?autoregister=false';
        }
        return $redirect_url;
    }

    function FBTL_openid_redirect_after_logout($logout_url) {
        if(get_option('FBTL_openid_logout_redirection_enable')){
            $logout_redirect_option = get_option( 'FBTL_openid_logout_redirect' );
            $redirect_url = site_url();
            if( $logout_redirect_option == 'homepage' ) {
                $redirect_url = $logout_url . '&redirect_to=' .home_url()  ;
            }
            else if($logout_redirect_option == 'currentpage'){
                if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
                    $http = "https://";
                } else {
                    $http =  "http://";
                }
                $redirect_url = $logout_url . '&redirect_to=' . $http . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'];
            }
            else if($logout_redirect_option == 'login') {
                $redirect_url = $logout_url . '&redirect_to=' . site_url() . '/wp-admin' ;
            }
            else if($logout_redirect_option == 'custom') {
                $redirect_url = $logout_url . '&redirect_to=' . site_url() . (null !== get_option('FBTL_openid_logout_redirect_url')?get_option('FBTL_openid_logout_redirect_url'):'');
            }
            return $redirect_url;
        }else{
            return $logout_url;
        }

    }

    function FBTL_openid_login_redirect($username = '', $user = NULL){
        FBTL_openid_start_session();
        if(is_string($username) && $username && is_object($user) && !empty($user->ID) && ($user_id = $user->ID) && isset($_SESSION['FBTL_login']) && $_SESSION['FBTL_login']){
            $_SESSION['FBTL_login'] = false;
            wp_set_auth_cookie( $user_id, true );
            $redirect_url = FBTL_openid_get_redirect_url();

            wp_redirect($redirect_url);
            exit;
        }
    }

   


    function FBTL_openid_filter_app_name($decrypted_app_name)
    {
        $decrypted_app_name = strtolower($decrypted_app_name);
        $split_app_name = explode('_', $decrypted_app_name);
        //check to ensure login starts at the click of social login button
        if(empty($split_app_name[0])){
            wp_die(get_option('FBTL_manual_login_error_message'));
        }
        else {
            return $split_app_name[0];
        }
    }

    function FBTL_openid_account_linking($messages) {
        if(isset( $_GET['option']) && $_GET['option'] == 'disable-social-login' ){
            $messages = '<p class="message">'.get_option('FBTL_account_linking_message').'</p>';
        }
        return $messages;
    }

    function FBTL_openid_customize_logo(){
        $logo =" <div style='float:left;' class='FBTL_image_id'>
			<a target='_blank' href='https://#'>
			<img alt='logo' src='". plugins_url('/images/logo.png',__FILE__) ."' class='FBTL_openid_image'>
			</a>
			</div>
			<br/>";
        return $logo;
    }

    //delete rows from account linking table that correspond to deleted user
    function FBTL_openid_delete_account_linking_rows($user_id){
        global $wpdb;
        $db_prefix = $wpdb->prefix;
        $result = $wpdb->get_var($wpdb->prepare("DELETE from ".$db_prefix."FBTL_openid_linked_user where user_id = %s ",$user_id));
        if($result === false){
            wp_die(get_option('FBTL_delete_user_error_message'));
            $wpdb->show_errors();
            $wpdb->print_error();
            exit;
        }
    }

    function FBTL_openid_update_role($user_id='', $user_url=''){
        // save the profile url in user meta // this was added to save facebook url in user meta as it is more than 100 chars
        update_user_meta($user_id, 'fbtl_social_profile_url',$user_url);
        $user = get_user_by('ID',$user_id);
		if(get_option('FBTL_openid_login_role_mapping') && !(empty($user)) ){
			$user->set_role( get_option('FBTL_openid_login_role_mapping') );
		}
    }

    function FBTL_openid_get_wp_style(){
        $path = site_url();
        $path .= '/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load%5B%5D=dashicons,buttons,forms,l10n,login&amp;ver=4.8.1';
        return $path;
    }

    function FBTL_openid_delete_profile_column($value, $columnName, $userId){
        if('FBTL_openid_delete_profile_data' == $columnName){
            global $wpdb;
            $socialUser = $wpdb->get_var($wpdb->prepare('SELECT id FROM '. $wpdb->prefix .'FBTL_openid_linked_user WHERE user_id = %d ', $userId));
            if($socialUser > 0 && !get_user_meta($userId,'FBTL_openid_data_deleted')){
                return '<a href="javascript:void(0)" onclick="javascript:fbtl_jplDeleteSocialProfile(this, '. $userId .')">Delete</a>';
            }
            else
                return '<p>NA</p>';
        }
    }
    add_action('manage_users_custom_column', 'FBTL_openid_delete_profile_column', 9, 3);

    if(get_option('FBTL_openid_logout_redirection_enable') == 1){
        add_filter( 'logout_url', 'FBTL_openid_redirect_after_logout',0,1);
    }
    function FBTL_openid_add_custom_column($columns){
        $columns['FBTL_openid_delete_profile_data'] = 'Delete Social Profile Data';
        return $columns;
    }

    function FBTL_openid_delete_social_profile_script(){
?>
        <script type="text/javascript">
			function fbtl_jplDeleteSocialProfile(elem, userId){
                jQuery.ajax({
                    url:"<?php echo admin_url();?>", //the page containing php script
                    method: "POST", //request type,
                    data: {action : 'delete_social_profile_data', user_id : userId},
                    dataType: 'text',
                    success:function(result){
                        alert('Social Profile Data Deleted successfully. Press OK to continue.');
                      window.location.reload(true);
                    }
                });
            }
		</script>
<?php

    }

    function FBTL_openid_sanitize_user($username, $raw_username, $strict) {

        $username = wp_strip_all_tags( $raw_username );
        $username = remove_accents( $username );
        // Kill octets
        $username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
        $username = preg_replace( '/&.+?;/', '', $username ); // Kill entities


        $username = trim( $username );
        // Consolidate contiguous whitespace
        $username = preg_replace( '|\s+|', ' ', $username );
        return $username;
    }

    add_filter('manage_users_columns', 'FBTL_openid_add_custom_column');

    add_action( 'widgets_init', function(){register_widget( "FBTL_openid_login_wid" );});
   

    add_action( 'init', 'FBTL_openid_login_validate' );
    add_action( 'wp_logout', 'FBTL_openid_end_session',1 );
    add_action( 'FBTL_user_register', 'FBTL_openid_update_role', 1, 2);
    add_action( 'wp_login', 'FBTL_openid_login_redirect', 10, 2);
    add_action( 'wp_login', 'FBTL_openid_link_account', 9, 2);
    add_filter( 'login_message', 'FBTL_openid_account_linking');
    add_action( 'delete_user', 'FBTL_openid_delete_account_linking_rows' );
    add_action( 'FBTL_user_register','FBTL_openid_send_email',1, 2 );
    add_action('admin_head', 'FBTL_openid_delete_social_profile_script');

    //compatibility with international characters
    add_filter('sanitize_user', 'FBTL_openid_sanitize_user', 10, 3);

?>