<?php
ob_start();
function FBTL_register_openid() {

            
	if( isset( $_GET[ 'tab' ]) && $_GET[ 'tab' ] !== 'register' ) {
		$active_tab = $_GET[ 'tab' ];
	} else if(FBTL_openid_is_customer_registered()) {
		$active_tab = 'login';
	} else if(!isset($_GET[ 'tab' ])) {
        $active_tab = 'login';
	}
	else{
        $active_tab = $_GET[ 'tab' ];
    }

	if(FBTL_openid_is_curl_installed()==0){ ?>
		<p style="color:red;">(Warning: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a> is not installed or disabled) Please go to Troubleshooting for steps to enable curl.</p>
	<?php
	}?>
    <div>
        <table>
        <tr><td>
            <img id="logo" style="margin-top: 25px" src="<?php echo plugin_dir_url(__FILE__);?>images/logo.png">
            </td> <td>&nbsp;<h1 style="color: #8ab25b;font-family: sans-serif;">Facebook-Twitter-Wp-Login</h1></td>
            </tr> </table>
    </div>
<div id="tab">
	<h2 class="nav-tab-wrapper">
		<a id="social_login" class="nav-tab <?php echo $active_tab == 'login' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'login'), $_SERVER['REQUEST_URI'] ); ?>">Select Social Platform</a>
		<a id="custom_app" class="nav-tab <?php echo $active_tab == 'custom_app' ? 'nav-tab-active' : ''; ?>" href="admin.php?page=FBTL_openid_settings&tab=custom_app">My New App</a>
      
        <?php if(FBTL_openid_is_customer_registered()) { ?>
            <a class="nav-tab <?php echo $active_tab == 'register' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'register'), $_SERVER['REQUEST_URI'] ); ?>">Account Setup</a>
        <?php } ?>
    </h2>
</div>



<div id="FBTL_openid_settings">

	<div class="FBTL_container">
		<div id="FBTL_openid_msgs"></div>
			<table style="width:100%;">
				<tr>
                    <?php
							  if($active_tab == 'login'){
								FBTL_openid_apps_config();
							}
                            
							else if($active_tab == 'custom_app') {
								?>
                    <td style="vertical-align:top;width:65%;">
                                    <?php FBTL_openid_custom_app_config();?>
								</td>
								
								</tr></table></div></div>
    <?php
							}
						
						?>
			</table>
    <?php
}



function FBTL_openid_apps_config() {

	?>
	<td style="vertical-align:top;width:65%;">
		<!-- Google configurations -->
				<form id="form-apps" name="form-apps" method="post" action="">
					<input type="hidden" name="option" value="FBTL_openid_enable_apps" />
					<input type="hidden" name="FBTL_openid_enable_apps_nonce"
                   					value="<?php echo wp_create_nonce( 'fbtl_openid-enable-apps-nonce' ); ?>"/>
					
					<div class="FBTL_openid_table_layout">
						
                               <table>
									<tr>
										<td colspan="2">
											<h3>Social Login</h3>
												<b>Select applications to enable login for your users.</b>
										</td>

									</tr>
								</table>
							

							<table>
                               <tr id="select_app"><td><table style="width: 100%"> <h3>Select Apps</h3>
								<h4>Select Default Apps:</h4>

								<tr >
									<td>
                                        <table style="width:100%">
											<tr >
                                            	
												
												<td>
												<input type="checkbox" id="twitter_enable" class="app_enable" name="FBTL_openid_twitter_enable" value="1" onchange="previewLoginIcons();"
												<?php checked( get_option('FBTL_openid_twitter_enable') == 1 );?> /><strong>Twitter</strong>
												</td>
												
												<td>
												<input type="checkbox" id="facebook_enable" class="app_enable tooltip" name="FBTL_openid_facebook_enable" value="1" <?php $x=fbtl_if_custom_app('facebook');echo "onchange='previewLoginIcons(),redirectto_custom_tab(".$x.")'";?>
                                                        <?php checked( get_option('FBTL_openid_facebook_enable') == 1 );?> /><strong> Facebook </strong>
												</td>
											</tr>
											
										</table>
									</td>
								</tr>
								
								
								</table></td></tr>
								<tr>
					            <td>

						
						
							

		<tr style="display:none">

				<td class="FBTL_openid_table_td_checkbox" >
					<input type="radio"    name="FBTL_openid_login_theme" value="circle"
								<?php checked( get_option('FBTL_openid_login_theme') == 'circle' );?> checked>Round

				<span style="margin-left:106px;">
					<input type="radio" id="FBTL_openid_login_default_radio"  name="FBTL_openid_login_custom_theme" value="default" 
								<?php checked( get_option('FBTL_openid_login_custom_theme') == 'default' );?> checked>Default

				</span>


				</td>
		</tr>

	
		
	</table></td></tr>
	<tr>
	
                                         <tr style="display:none"><td>
                                                <hr>
                                               <tr id="display_opt"></tr>

											<tr style="display:none">
												<td class="FBTL_openid_table_td_checkbox">
												<input type="checkbox" id="default_login_enable" name="FBTL_openid_default_login_enable" value="1"
														<?php checked( get_option('FBTL_openid_default_login_enable') == 1 );?> checked />Default Login Form</td>
											</tr>
											
                                           

                                          </table></td></tr>
                                

							
								
								<tr style="display:none">
									<td style="display:none">
										
										<input type="checkbox" id="auto_register_enable" name="FBTL_openid_auto_register_enable" value="1"
											<?php checked( get_option('FBTL_openid_auto_register_enable') == 1 );?> / checked style="display:none">
										
										
										</td>
								</tr>
                                
                               
                                <tr>
                                    <td>
                                        <br/>
                                        <b>Assign roll to new user </b>
                                        <br><br>
                                        <select name="mapping_value_default" style="width:30%" id="default_group_mapping">
                                            <?php
                                            if(get_option('FBTL_openid_login_role_mapping'))
                                                $default_role = get_option('FBTL_openid_login_role_mapping');
                                            else
                                                $default_role = get_option('default_role');
                                            wp_dropdown_roles($default_role); ?>
                                        </select>
                                    </td>
                                </tr>
								<br>
                              
				<tr id="acc_link"><td> </td></tr>

		
		</tr>

	
		 <tr id="prof_completion"><td> </td></tr>

		
		<?php if(FBTL_openid_is_customer_valid() && !FBTL_openid_get_customer_plan('Do It Yourself')) { ?>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td><input type="checkbox" id="fbtl_jpl_user_attributes" name="fbtl_jpl_user_attributes" value="1"  <?php checked( get_option('fbtl_jpl_user_attributes') == 1 );?> /><b>Extended User Attributes</b>
			</td>
		</tr>
		<?php } else { 
			if(get_option('fbtl_jpl_user_attributes')) update_option('fbtl_jpl_user_attributes', 0);
		} ?>
    <tr>
        <td><br /><input type="submit" name="submit" value="Save" style="width:100px;" class="button button-primary button-large" />
        </td>
    </tr> 
		</table>

	</div>
</form>

<script>
jQuery(function() {
				jQuery('#tab2').removeClass('disabledTab');
});
</script>
</td>
		
<?php
}








function FBTL_openid_other_settings(){
	
?>
<td style="vertical-align:top;width:65%;">
	<form name="f" method="post" id="settings_form" action="">
	<input type="hidden" name="option" value="FBTL_openid_save_other_settings" />
	<input type="hidden" name="FBTL_openid_save_other_settings_nonce"
                   					value="<?php echo wp_create_nonce( 'fbtl_openid-save-other-settings-nonce' ); ?>"/>
	<div class="FBTL_openid_table_layout">

								
	
	<table class="FBTL_openid_settings_table">
        <tr id="sel_apps"><td><table><h3>Select Social Apps</h3>
		<p>Select applications to enable social sharing</p>
		<tr>
			<td class="FBTL_openid_table_td_checkbox">

					<tr>
						<td style="width:20%">
							<input type="checkbox" id="facebook_share_enable" class="app_enable" name="FBTL_openid_facebook_share_enable" value="1" 
							onclick="addSelectedApps();"  <?php checked( get_option('FBTL_openid_facebook_share_enable') == 1 );?> />
							<strong>Facebook</strong>
						</td>
						<td style="width:20%">
							<input type="checkbox"
									id="twitter_share_enable" class="app_enable" name="FBTL_openid_twitter_share_enable" value="1" onclick="addSelectedApps();"
								<?php checked( get_option('FBTL_openid_twitter_share_enable') == 1 );?> />
							<strong>Twitter </strong>
						</td>
						
						
						
					</tr>
					
				
					

			</td>
		</tr>
                </table>
        </td>
        </tr>
									

		
		
		<tr id="cust_icon"><td><table>
							
		
		
		
		<tr>
			<td>
		
				<div>
					<img class="FBTL_sharing_icon_preview" id="FBTL_sharing_icon_preview_facebook" src="<?php echo plugins_url( 'images/icons/facebook.png', __FILE__ )?>" />
					<img class="FBTL_sharing_icon_preview" id="FBTL_sharing_icon_preview_twitter" src="<?php echo plugins_url( 'images/icons/twitter.png', __FILE__ )?>" />
					</div>
		
				<div>
					<i class="FBTL_custom_sharing_icon_preview fa fa-facebook" id="FBTL_custom_sharing_icon_preview_facebook"  style="color:#ffffff;text-align:center;margin-top:5px;"></i>
					<i class="FBTL_custom_sharing_icon_preview fa fa-twitter" id="FBTL_custom_sharing_icon_preview_twitter" style="color:#ffffff;text-align:center;margin-top:5px;" ></i>
					</div>
											
				<div>
					<i class="FBTL_custom_sharing_icon_font_preview fa fa-facebook" id="FBTL_custom_sharing_icon_font_preview_facebook"  style="text-align:center;margin-top:5px;"></i>
					<i class="FBTL_custom_sharing_icon_font_preview fa fa-twitter" id="FBTL_custom_sharing_icon_font_preview_twitter" style="text-align:center;margin-top:5px;" ></i>
					</div>
	
			</td>
		</tr></table></td></tr>
		

						
    </table>		
	</div>

</form>
<script>
jQuery(function() {
				jQuery('#tab1').removeClass("nav-tab-active");
				jQuery('#tab2').addClass("nav-tab-active");
				
		});
</script>
</td>
		
<?php
}









function FBTL_openid_custom_app_config(){?>
	<style>
		.tableborder {
			border-collapse: collapse;
			width: 100%;
			border-color:#eee;
		}

		.tableborder th, .tableborder td {
			text-align: left;
			padding: 8px;
			border-color:#eee;
		}

		.tableborder tr:nth-child(even){background-color: #f2f2f2}
	</style>
	<div class="FBTL_table_layout" style="min-height: 400px;">
	<?php
	
	if(isset($_GET['action']) && $_GET['action']=='delete'){
		if(isset($_GET['wp_nonce'])){
			$nonce = $_GET['wp_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-delete-selected-app-nonce' ) ) {
				wp_die('<strong>ERROR</strong>: Invalid Request.');
			} else {
				if(isset($_GET['app'])){
					$app = sanitize_text_field($_GET['app']);
					fbtl_delete_your_new_app($app);
				}
			}
		}
	} else if(isset($_GET['action']) && $_GET['action']=='instructions'){
		if(isset($_GET['app'])){
			$app = sanitize_text_field($_GET['app']);
			FBTL_custom_app_instructions($_GET['app']);
		}
	}
	
	if(isset($_GET['action']) && $_GET['action']=='add'){
		if(isset($_GET['wp_nonce'])){
			$nonce = $_GET['wp_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-add-selected-app-nonce' ) ) {
				wp_die('<strong>ERROR</strong>: Invalid Request.');
			} else {
				your_new_app();
			}
		}
	} 
	else if(isset($_GET['action']) && $_GET['action']=='update'){
		if(isset($_GET['wp_nonce'])){
			$nonce = $_GET['wp_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'fbtl_openid-update-selected-app-nonce' ) ) {
				wp_die('<strong>ERROR</strong>: Invalid Request.');
			} else {
				if(isset($_GET['app'])) {
					$app = sanitize_text_field($_GET['app']);
					fbtl_update_your_new_app($app);
					
				}
			}
		}
	}
		else if(get_option('FBTL_openid_apps_list')){
            if(strpos($_SERVER['REQUEST_URI'], "setup_msg")!== false)
            {
                ?><div id="upgrade_notice" class="notice notice-success is-dismissible" style="width: 92.5%;margin-left: 0%;"><p><strong>Please enable the Facebook custom app.</strong></p></div>
                <?php
            }
			$appslist = maybe_unserialize(get_option('FBTL_openid_apps_list'));
			$nonce = wp_create_nonce( 'fbtl_openid-add-selected-app-nonce' );
			echo "<br><input onclick='window.location.href=\"admin.php?page=FBTL_openid_settings&tab=custom_app&action=add&wp_nonce=".$nonce."\"' type='button' class='button button-primary button-large' style='float:right;text-align:center;' value='Click me to add your custom app'>";
			echo "<h3>Applications List</h3>";
			echo "<table class='tableborder'>";
            echo "<tr><th><b>Name</b></th><th>Action</th><th>Enable Custom app</tr></tr>";
			foreach($appslist as $key => $app){

                $option = 'FBTL_openid_enable_custom_app_' . $key;

                if(get_option($option) == '1'){
                    $enable_status = 'checked';
                }
                else{
                    $enable_status = '';
                }
                if($key=='facebook')
                {
                    $test_config=" <a onclick=\"testconfiguration('".$key."')\"><u>Test  Settings</u></a> ";
                }
                else {
                    $test_config = '';
				}
				$nonce_update = wp_create_nonce( 'fbtl_openid-update-selected-app-nonce' );
                echo "<tr><td>".$key."</td><td><a href='admin.php?page=FBTL_openid_settings&tab=custom_app&action=update&app=".$key."&wp_nonce=" . $nonce_update . "'>Edit</a> | ".$test_config."
                </td><td><label class='fbtl_switch'>
 				<input type='checkbox' ". $enable_status ." onclick='enable_custom_app(\"".$key."\");  ' id='FBTL_id_".$key."'  >
                <div class='fbtl_slider round' id='switch_checkbox' >
                </div>            
            	</label></td></tr>";
            }
			echo "</table>";
            echo "<br><br><br><br><br><br><br><br>";
            echo "<div style='text-align: center'><p>
                  </p></div>";
	
		}elseif (get_option('FBTL_openid_apps_list')==null){
			$nonce = wp_create_nonce( 'fbtl_openid-add-selected-app-nonce' );
            echo "<div style='text-align: center'><p> Please click on below button to add your own new app.</p>";
            echo "<br><input type='button'  onclick='window.location.href=\"admin.php?page=FBTL_openid_settings&tab=custom_app&action=add&wp_nonce=" . $nonce . "\"' class='button button-primary button-large' style='text-align:center;' value='Click me to add your custom app'>";
            echo "<br><br><br><br><br>";?>
           

<?php
        }?>
		</div>


		</td>
    <script>
        //ajax call for custom app over default app
        function enable_custom_app(appname){

            var checkbox_id = 'FBTL_id_'+appname;
            var value = document.getElementById(checkbox_id).checked;

            jQuery.ajax({
                url:"<?php echo admin_url();?>", //the page containing php script
                method: "POST", //request type,
                data: {selected_app: appname, selected_app_value : value},
                dataType: 'text',
                success:function(result){

                }
            });
        }
        function testconfiguration(selected_app) {
            jQuery.ajax({
                url:"<?php echo admin_url();?>", //the page containing php script
                method: "POST", //request type,
                data: {appname: selected_app, test_configuration : true},
                dataType: 'text',
                success:function(result){
                    var myWindow = window.open('<?php echo home_url(); ?>' + '/?option=oauthredirect&app_name='+selected_app+'&wp_nonce='+'<?php echo wp_create_nonce( 'fbtl_openid-oauth-app-nonce' ); ?>',"", "width=950, height=600");
                }
            });
        }
    </script>

<?php
}

function your_new_app(){
    if(strpos( $_SERVER['REQUEST_URI'], "facebook")!== false){?><div id="upgrade_notice" class="notice notice-success is-dismissible" style="width: 92.5%;margin-left: 0%;"><p><strong>Please configure the Facebook custom app and test your configuration.</strong></p></div>
    <?php }
	?>

		<script>

                function selectapp(app_name) {
                    var appname
                    if(app_name=="facebook")
                    {
                        appname="facebook";
                    }
                    else{
                        appname = document.getElementById("FBTL_oauth_app").value;
                    }
				document.getElementById("instructions").innerHTML  = "";
               
				
			}

		</script>
    <div id="toggle2" class="panel_toggle">
        <h3>Click me to add your custom app </h3>
    </div>
    <form id="form-common" name="form-common" method="post" action="admin.php?page=FBTL_openid_settings&tab=custom_app">
        <input type="hidden" name="option" value="FBTL_openid_add_custom_app" />
		<input type="hidden" name="FBTL_openid_add_custom_nonce"
                   					value="<?php echo wp_create_nonce( 'fbtl_openid-add-custom-app-nonce' ); ?>"/>
        <table class="FBTL_settings_table">
            <tr>
                <td><strong><font color="#FF0000">*</font>Select Application:</strong></td>
                <td>
                    <select class="FBTL_table_textbox" style="width:500px;" required="true" name="FBTL_oauth_app_name" id="FBTL_oauth_app" onchange="selectapp('')">
                        <option value="">Select Application</option>
                        <?php if(!FBTL_openid_restrict_user())echo "<option value='google'>Google</option>";?>
                        <option value="facebook" <?php if(strpos( $_SERVER['REQUEST_URI'], "facebook")!== false) echo"selected";?> ">Facebook</option>
                        <option value="twitter">Twitter</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong><font color="#FF0000">*</font>Client ID(FB)/API key(TW):</strong></td>
                <td><input class="FBTL_table_textbox" required="" style="height: 27px; width:500px" type="text" name="FBTL_oauth_client_id" value=""></td>
            </tr>
            <tr>
                <td><strong><font color="#FF0000">*</font>Client Secret(FB)/API secret key(TW):</strong></td>
                <td><input class="FBTL_table_textbox" required="" style="height: 27px; width:500px" type="text"  name="FBTL_oauth_client_secret" value=""></td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" name="submit" value="Save settings" class="button button-primary button-large" />
                    <input type="button" name="back" onclick="goBack();" value="Back" class="button button-primary button-large" />
                </td>
            </tr>
        </table>
    </form>

    <div id="instructions">

    </div> <?php if(strpos( $_SERVER['REQUEST_URI'], "facebook")!== false){?><script>selectapp("facebook");</script><?php } ?>
    <script>
        function goBack(){

            var base_url = '<?php echo get_admin_url();?>';
            base_url += 'admin.php?page=FBTL_openid_settings&tab=custom_app';
            window.location.href= base_url ;
        }
    </script>

    <?php
}
function fbtl_update_your_new_app($appname){
	
	$appslist = maybe_unserialize(get_option('FBTL_openid_apps_list'));
	foreach($appslist as $key => $app){
		if($appname == $key){
			$currentappname = $appname;
			$currentapp = $app;
			break;
		}
	}
	
	if(!$currentapp)
		return;
	?>
		
		<div id="toggle2" class="panel_toggle">
			<h3>Update Application</h3>
		</div>
		<form id="form-common" name="form-common" method="post" action="admin.php?page=FBTL_openid_settings&tab=custom_app">
		<input type="hidden" name="option" value="FBTL_openid_add_custom_app" />
            <input type="hidden" name="FBTL_openid_add_custom_nonce"
                   value="<?php echo wp_create_nonce( 'fbtl_openid-add-custom-app-nonce' ); ?>"/>
		<table class="FBTL_settings_table">
			<tr>
			<td><strong><font color="#FF0000">*</font>Application:</strong></td>
			<td>
				<input class="FBTL_table_textbox" required="" type="hidden" name="FBTL_oauth_app_name" value="<?php echo $currentappname;?>">
				<input class="FBTL_table_textbox" required="" type="hidden" name="FBTL_oauth_custom_app_name" value="<?php echo $currentappname;?>">
				<?php echo esc_html($currentappname);?><br><br>
			</td>
			</tr>
			<tr>
				<td><strong><font color="#FF0000">*</font>Client ID(FB)/API key(TW):</strong></td>
				<td><input class="FBTL_table_textbox" required="" style="height: 27px; width:500px" type="text" name="FBTL_oauth_client_id" value="<?php echo esc_html($currentapp['clientid']);?>"></td>
			</tr>
			<tr>
				<td><strong><font color="#FF0000">*</font>Client Secret(FB)/API secret key(TW):</strong></td>
				<td><input class="FBTL_table_textbox" required="" style="height: 27px; width:500px" type="text" name="FBTL_oauth_client_secret" value="<?php echo esc_html($currentapp['clientsecret']);?>"></td>
			</tr>
			
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="submit" value="Save settings" class="button button-primary button-large" />
                    <input type="button" name="back" onclick="goBack();" value="Back" class="button button-primary button-large" />
                </td>
			</tr>
		</table>
		</form>
        <script>
            function goBack(){

                var base_url = '<?php echo site_url();?>';
                base_url += '/wp-admin/admin.php?page=FBTL_openid_settings&tab=custom_app';
                window.location.href= base_url ;
            }
        </script>
		<?php
}





function FBTL_openid_is_customer_registered() {
			$email 			= get_option('FBTL_openid_admin_email');
			$customerKey 	= get_option('FBTL_openid_admin_customer_key');
			if( ! $email || ! $customerKey || ! is_numeric( trim( $customerKey ) ) ) {
				return 0;
			} else {
				return 1;
			}
}

function fbtlsocial_openid_support(){
	global $current_user;
	global $current_user;
	$current_user = wp_get_current_user();
?>
	
	</div>
	</div>
	</div>
	
<?php
}

function FBTL_openid_is_customer_valid(){
	$valid = sanitize_text_field(get_option('FBTL_openid_admin_customer_valid'));
	if(isset($valid) && get_option('FBTL_openid_admin_customer_plan'))
		return $valid;
	else
		return false;
}

function FBTL_openid_get_customer_plan($customerPlan){
	$plan = sanitize_text_field(get_option('FBTL_openid_admin_customer_plan'));
	$planName = isset($plan) ? base64_decode($plan) : 0;
	if($planName) {
		if(strpos($planName, $customerPlan) !== FALSE)
			return true;
		else
			return false;
	} else
		return false;
}

function FBTL_openid_is_extension_installed($name) {
	if  (in_array  ($name, get_loaded_extensions())) {
		return true;
	}
	else {
		return false;
	}
}

function FBTL_openid_is_curl_installed() {
    if (in_array ('curl', get_loaded_extensions())) {
        return 1;
    } else
        return 0;
}

function FBTL_openid_restrict_user() {
    if((get_option('FBTL_openid_admin_customer_key')>151617) || (get_option('FBTL_openid_new_user')==1)|| !FBTL_openid_is_customer_registered()) {
        return true;
    } else {
        return false;
    }
}
function FBTL_openid_malform_error() {
    if(get_option("FBTL_openid_malform_error")=='1'){
        return true;
    } else {
        return false;
    }
}





function fbtl_if_custom_app($app_name){

    if(get_option('FBTL_openid_apps_list'))
        $appslist = maybe_unserialize(get_option('FBTL_openid_apps_list'));
    else
        $appslist = array();

    $flag=0;
    foreach( $appslist as $key => $app){
        if($app_name == $key ){
            $flag=1;
            break;
        }
    }
    $option = 'FBTL_openid_enable_custom_app_' .$app_name;
    if($flag==0)
    {
        return 0;
    }
    if($flag==1&&get_option($option) == '0'){
        return 1;
    }
    if($flag==1&&get_option($option) == '1')
    {
        return 2;
    }
}
?>