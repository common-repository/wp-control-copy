<?php  
/* 
Plugin Name: WP Control Copy
Plugin URI: http://www.omniwp.com/plugins/wpcc-a-wordpress-plugin/ 
Description: WPCC lets you control copied text from your blog
Version: 1.3
Author: Nimrod Tsabari / omniWP
Author URI: http://www.omniwp.com
*/  
/*  Copyright 2012 Nimrod Tsabari / omniWP  (email : yo@omniwp.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/?>
<?php

define('WPCC_VER', '1.3');
define('WPCC_DIR', plugin_dir_url( __FILE__ ));

/* WPCC : Init */
/* ----------- */

function wpcc_admin_load_scripts() {
		wp_register_style('wpcc_admin_css', WPCC_DIR . '/admin/wpcc.admin.css', false, '1.0.0' );	    
        wp_enqueue_style('wpcc_admin_css');
}

add_action('admin_enqueue_scripts', 'wpcc_admin_load_scripts');

/* WPCC : Activation */
/* -------------------- */

define('WPCC_NAME', 'WPCC');
define('WPCC_SLUG', 'wpcc');

register_activation_hook(__file__,'omni_wpcc_admin_activate');
register_activation_hook(__file__,'wpcc_add_options');
add_action('admin_notices', 'omni_wpcc_admin_notices');	

function wpcc_add_options() {
	$k = get_option('wpcc_options');
	$c = false;
	
	if ( !array_key_exists('more_core', $k ) ) { $k['more_core'] = 'Read More at @cururl@, Copyright &#169; @blogname@'; $c = true; }
	if ( !array_key_exists('more_single', $k ) ) { $k['more_single'] = 'Read More at @cururl@, Written by @author@, Copyright &#169; @blogname@'; $c = true; }
	if ( !array_key_exists('who_can', $k ) ) { $k['who_can'] = 'all'; $c = true; }
	if ( !array_key_exists('where_to_add', $k ) ) { $k['where_to_add'] = 'all'; $c = true; }
	if ( !array_key_exists('specific_pids', $k ) ) { $k['specific_pids'] = ''; $c = true; }
	if ( !array_key_exists('member_clean', $k ) ) { $k['member_clean'] = 'yes'; $c = true; }
	if ( !array_key_exists('show_alerts', $k ) ) { $k['show_alerts'] = 'no'; $c = true; }
	if ( !array_key_exists('alert_only_logged', $k ) ) { $k['alert_only_logged'] = 'Please Login or Sign Up to copy text.'; $c = true; }
	if ( !array_key_exists('alert_disabled', $k ) ) { $k['alert_disabled'] = 'Copying is disabled.'; $c = true; }
	if ( !array_key_exists('alert_success', $k ) ) { $k['alert_success'] = 'Thank you for Copying!'; $c = true; }
	
	delete_option('wpcc_options');
	update_option('wpcc_options',$k);
}

function omni_wpcc_admin_activate() {
	$reason = get_option('omni_plugin_reason');
	if ($reason == 'nothanks') { 
		update_option('omni_plugin_on_list',0);
	} else {		
		add_option('omni_plugin_on_list',0);
		add_option('omni_plugin_reason','');
	}
}

function omni_wpcc_admin_notices() {
	if ( get_option('omni_plugin_on_list') < 2 ){		
		echo "<div class='updated'><p>" . sprintf(__('<a href="%s">' . WPCC_NAME . '</a> needs your attention.'), "options-general.php?page=" . WPCC_SLUG). "</p></div>";
	}
} 

/*  WPCC : Admin Part  */
/* --------------------- */
/* Inspired by Purwedi Kurniawan's SEO Searchterms Tagging 2 Pluging */

function wpcc_admin() {
	if (omni_wpcc_list_status()) include('admin/wpcc.admin.php'); 
}            

function wpcc_admin_init() {
	add_options_page("WP Control Copy", "WP Control Copy", 1, "wpcc", "wpcc_admin");
}

add_action('admin_menu', 'wpcc_admin_init');

function omni_wpcc_list_status() {
	$onlist = get_option('omni_plugin_on_list');
	$reason = get_option('omni_plugin_reason');
	if ( trim($_GET['onlist']) == 1 || $_GET['no'] == 1 ) {
		$onlist = 2;
		if ($_GET['onlist'] == 1) update_option('omni_plugin_reason','onlist');
		if ($_GET['no'] == 1) {
			 if ($reason != 'onlist') update_option('omni_plugin_reason','nothanks');
		}
		update_option('omni_plugin_on_list', $onlist);
	} 
	if ( ((trim($_GET['activate']) != '' && trim($_GET['from']) != '') || trim($_GET['activate_again']) != '') && $onlist != 2 ) { 
		update_option('omni_plugin_list_name', $_GET['name']);
		update_option('omni_plugin_list_email', $_GET['from']);
		$onlist = 1;
		update_option('omni_plugin_on_list', $onlist);
	}
	if ($onlist == '0') {
		omni_wpcc_register_form_1('wpcc_registration');
	} elseif ($onlist == '1') {
		$name = get_option('omni_plugin_list_name');
		$email = get_option('omni_plugin_list_email');
		omni_wpcc_do_list_form_2('wpcc_confirm',$name,$email);
	} elseif ($onlist == '2') {
		return true;
	}
}

function omni_wpcc_register_form_1($fname) {
	global $current_user;
	get_currentuserinfo();
	$name = $current_user->user_firstname;
	$email = $current_user->user_email;
?>
	<div class="register" style="width:50%; margin: 100px auto; border: 1px solid #BBB; padding: 20px;outline-offset: 2px;outline: 1px dashed #eee;box-shadow: 0 0 10px 2px #bbb;">
		<p class="box-title" style="margin: -20px; background: #489; padding: 20px; margin-bottom: 20px; border-bottom: 3px solid #267; color: #EEE; font-size: 30px; text-shadow: 1px 2px #267;">
			Please register the plugin...
		</p>
		<p>Registration is <strong style="font-size: 1.1em;">Free</strong> and only has to be done <strong style="font-size: 1.1em;">once</strong>. If you've register before or don't want to register, just click the "No Thank You!" button and you'll be redirected back to the Dashboard.</p>
		<p>In addition, you'll receive a a detailed tutorial on how to use the plugin and a complimentary subscription to our Email Newsletter which will give you a wealth of tips and advice on Blogging and Wordpress. Of course, you can unsubscribe anytime you want.</p>
		<p><?php omni_wpcc_registration_form($fname,$name,$email);?></p>
		<p style="background: #F8F8F8; border: 1px dotted #ddd; padding: 10px; border-radius: 5px; margin-top: 20px;"><strong>Disclaimer:</strong> Your contact information will be handled with the strictest of confidence and will never be sold or shared with anyone.</p>
	</div>	
<?php
}

function omni_wpcc_registration_form($fname,$uname,$uemail,$btn='Register',$hide=0, $activate_again='') {
	$wp_url = get_bloginfo('wpurl');
	$wp_url = (strpos($wp_url,'http://') === false) ? get_bloginfo('siteurl') : $wp_url;
	$thankyou_url = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'];
	$onlist_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;onlist=1';
	$nothankyou_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;no=1';
	?>
	
	<?php if ( $activate_again != 1 ) { ?>
	<script><!--
	function trim(str){ return str.replace(/(^\s+|\s+$)/g, ''); }
	function imo_validate_form() {
		var name = document.<?php echo $fname;?>.name;
		var email = document.<?php echo $fname;?>.from;
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		var err = ''
		if ( trim(name.value) == '' )
			err += '- Name Required\n';
		if ( reg.test(email.value) == false )
			err += '- Valid Email Required\n';
		if ( err != '' ) {
			alert(err);
			return false;
		}
		return true;
	}
	//-->
	</script>
	<?php } ?>
	<form name="<?php echo $fname;?>" method="post" action="http://www.aweber.com/scripts/addlead.pl" <?php if($activate_again!=1){;?>onsubmit="return imo_validate_form();"<?php }?> style="text-align:center;" >
		<input type="hidden" name="meta_web_form_id" value="1222167085" />
		<input type="hidden" name="listname" value="omniwp_plugins" />  
		<input type="hidden" name="redirect" value="<?php echo $thankyou_url;?>">
		<input type="hidden" name="meta_redirect_onlist" value="<?php echo $onlist_url;?>">
		<input type="hidden" name="meta_adtracking" value="omniwp_plugins_adtracking" />
		<input type="hidden" name="meta_message" value="1">
		<input type="hidden" name="meta_required" value="from,name">
		<input type="hidden" name="meta_forward_vars" value="1">	
		 <?php if ( $activate_again == 1 ) { ?> 	
			 <input type="hidden" name="activate_again" value="1">
		 <?php } ?>		 
		<?php if ( $hide == 1 ) { ?> 
			<input type="hidden" name="name" value="<?php echo $uname;?>">
			<input type="hidden" name="from" value="<?php echo $uemail;?>">
		<?php } else { ?>
			<p>Name: </td><td><input type="text" name="name" value="<?php echo $uname;?>" size="25" maxlength="150" />
			<br />Email: </td><td><input type="text" name="from" value="<?php echo $uemail;?>" size="25" maxlength="150" /></p>
		<?php } ?>
		<input class="button-primary" type="submit" name="activate" value="<?php echo $btn; ?>" style="font-size: 14px !important; padding: 5px 20px;" />
	</form>
    <form name="nothankyou" method="post" action="<?php echo $nothankyou_url;?>" style="text-align:center;">
	    <input class="button" type="submit" name="nothankyou" value="No Thank You!" />
    </form>
	<?php
}

function omni_wpcc_do_list_form_2($fname,$uname,$uemail) {
	$msg = 'You have not clicked on the confirmation link yet. A confirmation email has been sent to you again. Please check your email and click on the confirmation link to register the plugin.';
	if ( trim($_GET['activate_again']) != '' && $msg != '' ) {
		echo '<div id="message" class="updated fade"><p><strong>'.$msg.'</strong></p></div>';
	}
	?>
	<div class="register" style="width:50%; margin: 100px auto; border: 1px dotted #bbb; padding: 20px;">
		<p class="box-title" style="margin: -20px; background: #489; padding: 20px; margin-bottom: 20px; border-bottom: 3px solid #267; color: #EEE; font-size: 30px; text-shadow: 1px 2px #267;">Thank you...</p>
		<p>A confirmation email has just been sent to your email @ "<?php echo $uemail;?>". In order to register the plugin, check your email and click on the link in that email.</p>
		<p>Click on the button below to Verify and Activate the plugin.</p>
		<p><?php omni_wpcc_registration_form($fname.'_0',$uname,$uemail,'Verify and Activate',$hide=1,$activate_again=1);?></p>
		<p>Disclaimer: Your contact information will be handled with the strictest confidence and will never be sold or shared with third parties.</p>
	</div>	
	<?php
}

function wpcc_action() {
	if (!is_admin()) {
		$wpccs = get_option('wpcc_options');
		$core_more = $wpccs['more_core'];
		$single_more = $wpccs['more_single'];
		$who_can = $wpccs['who_can'];
		$where_to_add = $wpccs['where_to_add'];
		$member_clean = $wpccs['member_clean'];
		$show_alerts = $wpccs['show_alerts'];
		
		$wpcc_only_logged = get_option('wpcc_only_logged');
	
		$cururl = 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$blogurl = home_url();
		$blogname = get_bloginfo('name');
		$bloglink = "<a href='". $blogurl . "'>" . $blogname . "</a>";
		$blogdesc = get_bloginfo('description');
		$author	  = get_the_author();
		$title = strip_tags(get_the_title());
		$year = date('Y');
		$date = date('d-m-Y');
		if ((is_single() || is_page()) && !is_front_page()) {
			$curlink = "<a href='". $cururl . "'>" . $title . "</a>";
		} else {
			$curlink = '<a href="'. $cururl . '">' . $blogname  . '</a>';
		}
	
		$core_more = str_replace('@cururl@', $cururl, $core_more);
		$core_more = str_replace('@curlink@', $curlink, $core_more);
		$core_more = str_replace('@blogname@', $blogname, $core_more);
		$core_more = str_replace('@blogurl@', $blogurl, $core_more);
		$core_more = str_replace('@bloglink@', $bloglink, $core_more);
		$core_more = str_replace('@blogdesc@', $blogdesc, $core_more);
		$core_more = str_replace('@author@', $author, $core_more);
		$core_more = str_replace('@title@', $title, $core_more);
		$core_more = str_replace('@year@', $year, $core_more);
		$core_more = str_replace('@date@', $date, $core_more);
		$core_more = htmlspecialchars($core_more, ENT_QUOTES);
		$core_more = nl2br($core_more);
		$core_more = str_replace(array("\r\n", "\n", "\r"), '', $core_more);
	
		$addSingle = ($single_more != '');
		if ($addSingle) {
			$single_more = str_replace('@cururl@', $cururl, $single_more);
			$single_more = str_replace('@curlink@', $curlink, $single_more);
			$single_more = str_replace('@blogname@', $blogname, $single_more);
			$single_more = str_replace('@blogurl@', $blogurl, $single_more);
			$single_more = str_replace('@bloglink@', $bloglink, $single_more);
			$single_more = str_replace('@blogdesc@', $blogdesc, $single_more);
			$single_more = str_replace('@author@', $author, $single_more);
			$single_more = str_replace('@title@', $title, $single_more);
			$single_more = str_replace('@year@', $year, $single_more);
			$single_more = str_replace('@date@', $date, $single_more);
			$single_more = htmlspecialchars($single_more, ENT_QUOTES);
			$single_more = nl2br($single_more);
			$single_more = str_replace(array("\r\n", "\n", "\r"), '', $single_more);
		}

		$skip_members = (($member_clean == 'yes') && is_user_logged_in());
		$skip = $skip_members;

		$only_logged = (($who_can == 'only_logged') && !is_user_logged_in());
		if ($only_logged) $er = $wpccs['alert_only_logged'];
		$nobody = ($who_can == 'disable');
		if ($nobody) $er = $wpccs['alert_disabled'];
		$suc =  $wpccs['alert_success'];
		$disable = ($only_logged) || ($nobody);

		$html_core = '';	
		$html_posts = '';	
		$html_core .= '<script type="text/javascript">
			function wpccAction() {
			    var b = document.getElementsByTagName("body")[0];
			    var s = window.getSelection();;
				var oS = s;
			    var pagelink = "<br /><br />' . $core_more . '";
			    var c = s + pagelink;';
				if ($disable) {
					if (($show_alerts == 'all') || ($show_alerts == "fail")) {
						$html_core .= 'var c = "";
						var er = document.createElement("div");
						er.style.position = "fixed";
						er.style.left = "20px";
						er.style.bottom = "20px";
						er.style.backgroundColor = "red";
						er.style.padding = "10px 20px";
						er.innerHTML = "' . $er . '";
					    window.setTimeout(function() { b.appendChild(er); },0);
					    window.setTimeout(function() { b.removeChild(er); },2000);
						';
					}
				} else {
					if (($show_alerts == 'all') || ($show_alerts == "success")) {
						$html_core .= 'var suc = document.createElement("div");
						suc.style.position = "fixed";
						suc.style.right = "20px";
						suc.style.bottom = "20px";
						suc.style.backgroundColor = "green";
						suc.style.padding = "10px 20px";
						suc.style.color = "white";
						suc.style.borderRadius = "5px";
						suc.innerHTML = "' . $suc . '";
					    window.setTimeout(function() { b.appendChild(suc); },0);
					    window.setTimeout(function() { b.removeChild(suc); },2000);
						';
					}
				}
				$html_core .= 'var d = document.createElement("div");
				d.style.left="-99999px";
				d.style.position="absolute";
			
			    b.appendChild(d);
			    d.innerHTML = c ;
			    s.selectAllChildren(d);
			    window.setTimeout(function() { b.removeChild(d); },0);
			}
		
		document.oncopy = wpccAction;
		</script>';

		$html_posts .= '<script type="text/javascript">
			function wpccAction() {
			    var b = document.getElementsByTagName("body")[0];
			    var s = window.getSelection();;
				var oS = s;
			    var pagelink = "<br /><br />' . $single_more . '";
			    var c = s + pagelink;';
				if ($disable) {
					if (($show_alerts == 'all') || ($show_alerts == "fail")) {
						$html_posts .= 'var c = "";
						var er = document.createElement("div");
						er.style.position = "fixed";
						er.style.right = "20px";
						er.style.bottom = "20px";
						er.style.backgroundColor = "red";
						er.style.padding = "10px 20px";
						er.style.color = "white";
						er.style.borderRadius = "5px";
						er.innerHTML = "' . $er . '";
					    window.setTimeout(function() { b.appendChild(er); },0);
					    window.setTimeout(function() { b.removeChild(er); },2000);
						';
					}
				} else {
					if (($show_alerts == 'all') || ($show_alerts == "success")) {
						$html_posts .= 'var suc = document.createElement("div");
						suc.style.position = "fixed";
						suc.style.right = "20px";
						suc.style.bottom = "20px";
						suc.style.backgroundColor = "green";
						suc.style.padding = "10px 20px";
						suc.style.color = "white";
						suc.style.borderRadius = "5px";
						suc.innerHTML = "' . $suc . '";
					    window.setTimeout(function() { b.appendChild(suc); },0);
					    window.setTimeout(function() { b.removeChild(suc); },2000);
						';
					}
				}
				$html_posts .= 'var d = document.createElement("div");
				d.style.left="-99999px";
				d.style.position="absolute";
			
			    b.appendChild(d);
			    d.innerHTML = c ;
			    s.selectAllChildren(d);
			    window.setTimeout(function() { b.removeChild(d); },0);
			}
		
		document.oncopy = wpccAction;
		</script>';

		if (!$skip) {
			if ($where_to_add == "posts") {
				 if (is_single()) {
				 	if ($addSingle) {
				 		echo $html_posts;
					} else {
						echo $html_core;
					} 
				} 
			} else if ($where_to_add == "posts-pages") {
				 if ((is_single() || is_page()) && ($_SERVER['SERVER_NAME'] !== $blogurl)) {
				 	if ($addSingle) {
				 		echo $html_posts;
					} else {
						echo $html_core;
					} 
				 }
			} else if ($where_to_add == "specific-pids") { 
				$specific_pids = $wpccs['specific_pids'];
				if ($specific_pids !== '') {
					$pids = explode(',',$specific_pids);
					foreach ($pids as $pid) {
						if (trim($pid) == get_the_ID()) {
						 	if ($addSingle) {
							 		echo $html_posts;
							} else {
								echo $html_core;
							} 
						}
					}
				}
			} else {
				if (is_home() || ($_SERVER['SERVER_NAME'] == $blogurl) || is_front_page()) {
					echo $html_core;
				} else if (is_single() || is_page()) {
				 	if ($addSingle) {
				 		echo $html_posts;
					} else {
						echo $html_core;
					} 
				 } else {
				 	echo $html_core;
				 }
			}
 		}
 	}
}

add_action( 'wp_footer', 'wpcc_action');
?>