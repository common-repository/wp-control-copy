<?php
/* WPCC Admin Page
 * -----------------
 * Date : 16/09/2012
 */


define(WPCC_DIR, plugin_dir_url(__FILE__));

if (!empty( $_POST) && check_admin_referer('wpcc_update_options', 'wpcc_update_options_nonce')) {

	$wpccs_update['more_core'] = $_POST['wpcc_core_more'];
	$wpccs_update['more_single'] = $_POST['wpcc_single_more'];
	$wpccs_update['who_can'] = $_POST['wpcc_who_can'];
	$wpccs_update['where_to_add'] = $_POST['wpcc_where_to_add'];
	$wpccs_update['specific_pids'] = $_POST['wpcc_specific_pids'];
	$wpccs_update['member_clean'] = $_POST['wpcc_member_clean'];
	$wpccs_update['show_alerts'] = $_POST['wpcc_show_alerts'];
	$wpccs_update['alert_only_logged'] = $_POST['wpcc_alert_only_logged'];
	$wpccs_update['alert_disabled'] = $_POST['wpcc_alert_disabled'];
	$wpccs_update['alert_success'] = $_POST['wpcc_alert_success'];
	
	delete_option('wpcc_options');
	update_option('wpcc_options', $wpccs_update);

	echo '<div class="updated fade"><p><strong>' . __( 'Settings Saved.', 'wpcc' ) . "</strong></p></div>\n";
}

$wpccs = get_option('wpcc_options');
$core_more = $wpccs['more_core'];
$single_more = $wpccs['more_single'];
$who_can = $wpccs['who_can'];
$where_to_add = $wpccs['where_to_add'];
$member_clean = $wpccs['member_clean'];
$show_alerts  = $wpccs['show_alerts'];
$alert_only_logged = $wpccs['alert_only_logged'];
$alert_disabled = $wpccs['alert_disabled'];
$alert_success = $wpccs['alert_success'];
$specific_pids = $wpccs['specific_pids'];

?>
<div id="wpcc-admin" class="clearfix">
	<p class="page_title">WP Control Copy | <span>Take Control Over Copied Text</span></p>
	<div id="wpcc-content">
	<form method="post" action="<?php echo get_bloginfo( 'wpurl' ).'/wp-admin/options-general.php?page=wpcc' ?>">
		<table class="wpcc_options_table wpcc-gray">
			<thead>
				<tr>
					<td>
						<h2>WPCC Control Panel</h2>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<div class="wpcc_checkbox">
							<label for="wpcc_who_can">Who can copy text ? </label>
							<select id="wpcc_who_can" name="wpcc_who_can">
								<option value="all" <?php if ($who_can == "all") echo 'selected="selected"' ?>>Everybody</option>
								<option value="only_logged" <?php if ($who_can == "only_logged") echo 'selected="selected"' ?>>Only Logged In Users</option>
								<option value="disable" <?php if ($who_can == "disable") echo 'selected="selected"' ?>>Nobody!</option>
							</select>
							<p>Limit the privilege of copying text from your blog to Logged In Users, Everybody - or diable it entirely by choosing 'Nobody!'</p>
						</div>
						<div class="wpcc_checkbox">
							<label for="wpcc_member_clean">Logged In Users get clean copy ? </label>
							<select id="wpcc_member_clean" name="wpcc_member_clean">
								<option value="yes" <?php if ($member_clear == "yes") echo 'selected="selected"' ?>>Yes!</option>
								<option value="no" <?php if ($member_clean == "no") echo 'selected="selected"' ?>>No</option>
							</select>
							<p>Would text copied by members be clean without added text ?</p>
						</div>
						<div class="wpcc_checkbox">
							<label for="wpcc_where_to_add">Where to add WPCC ? </label>
							<select id="wpcc_where_to_add" name="wpcc_where_to_add" onchange="switchSelect(this.value)">
								<option value="all" <?php if ($where_to_add == "all") echo 'selected="selected"' ?>>Everywhere</option>
								<option value="posts" <?php if ($where_to_add == "posts") echo 'selected="selected"' ?>>Only Posts</option>
								<option value="posts-pages" <?php if ($where_to_add == "posts-pages") echo 'selected="selected"' ?>>Only Posts & Pages</option>
								<option value="specific-pids" <?php if ($where_to_add == "specific-pids") echo 'selected="selected"' ?>>Specific Post/Page IDs</option>
							</select>
							<div id="specific-pids-box">
								<label for="wpcc_specific_pids" class="wpcc-light">IDs (Comma Seperated)</label>
								<input type="text" name="wpcc_specific_pids" id="wpcc_specific_pids" value="<?php echo $specific_pids; ?>" class="wpcc_alerts" />
							</div>
							<p>Decide where to activate WPCC. You can choose to activate it site-wide, limit it to only Posts, Posts/Pages or Specific Posts & Pages.</p>
						</div>
						<div id="wpcc_core_box" class="wpcc_checkbox">
							<label for="wpcc_core_more">Core Added Text : <span style="font-weight:400;">(Leave empty to disable)</span></label>
							<textarea type="text" name="wpcc_core_more" id="wpcc_core_more" class="wpcc_core_more" rows="3"><?php echo $core_more; ?></textarea>
							<p>Core Added text will be added to the end of each copied text from your site. If Page/Post Added Text is left empty, this will be used for the entire site</p>
						</div>
						<div id="wpcc_single_box" class="wpcc_checkbox">
							<label for="single_core_more">Page/Post Added Text : <span style="font-weight:400;">(Leave empty to disable)</span></label>
							<textarea type="text" name="wpcc_single_more" id="wpcc_single_more" class="wpcc_core_more" rows="3"><?php echo $single_more; ?></textarea>
							<p>Page/Post Added Text will be added only to Posts/Pages, so you can use Post's Title and Author. If kept empty, Core Added Text will take over.</p>
						</div>
						<div id="wpcc-legend">
							<p class="wpcc-title">Legend for Added Text :</p>
							<p>Add this code to the added text sections, and it will be replaced with the relevant values.</p>
							<ul>
								<li><span class="wpcc-code">@cururl@</span> - The current URL.</li>
								<li><span class="wpcc-code">@curlink@</span> - The current Link (clickable).</li>
								<li><span class="wpcc-code">@blogname@</span> - The Blog's Name (<?php echo get_bloginfo('name'); ?>).</li>
								<li><span class="wpcc-code">@blogurl@</span> - The Blog's URL (<?php echo site_url(); ?>).</li>
								<li><span class="wpcc-code">@bloglink@</span> - The Blog'd link (clickable).</li>
								<li><span class="wpcc-code">@blogdesc@</span> - The Blog's Description.</li>
								<li><span class="wpcc-code">@author@</span> - The Page's/Post's Author.</li>
								<li><span class="wpcc-code">@title@</span> - The Page's/Post's Title.</li>
								<li><span class="wpcc-code">@year@</span> - The current Year (<?php echo date('Y'); ?>).</li>
								<li><span class="wpcc-code">@date@</span> - The current Date (<?php echo date('d-m-Y'); ?>).</li>
							</ul>
						</div>
						<div id="wpcc_alerts_box" class="wpcc_checkbox">
							<label for="wpcc_show_alerts">Display Alerts ?</label>
							<select id="wpcc_show_alerts" name="wpcc_show_alerts">
								<option value="all" <?php if ($show_alerts == "all") echo 'selected="selected"' ?>>Yes! All of Them!</option>
								<option value="fail" <?php if ($show_alerts == "fail") echo 'selected="selected"' ?>>Only When Fail.</option>
								<option value="success" <?php if ($show_alerts == "success") echo 'selected="selected"' ?>>Only When Success.</option>
								<option value="no" <?php if ($show_alerts == "no") echo 'selected="selected"' ?>>No.</option>
							</select>
							<p>Alerts elegantly show themselves when a user tries to copy text but is not allowed, or a user copy text succesfully.</p>
							<label for="wpcc_alert_only_logged" class="wpcc-light">Only Members Alert : </label>
							<input type="text" name="wpcc_alert_only_logged" id="wpcc_alert_only_logged" value="<?php echo $alert_only_logged; ?>" class="wpcc_alerts" />
							<label for="wpcc_alert_disabled" class="wpcc-light">Copy Disabled Alert : </label>
							<input type="text" name="wpcc_alert_disabled" id="wpcc_alert_disabled" value="<?php echo $alert_disabled; ?>" class="wpcc_alerts" />
							<label for="wpcc_alert_success" class="wpcc-light">Copy Success Alert : </label>
							<input type="text" name="wpcc_alert_success" id="wpcc_alert_success" value="<?php echo $alert_success; ?>" class="wpcc_alerts" />
						</div>
					</td>
				</tr>
			</tbody>
			<tfoot class="wpcc_submit_section">
				<tr>
					<td colspan="2">
						<?php wp_nonce_field('wpcc_update_options', 'wpcc_update_options_nonce'); ?>
						<input name="wpcc_pids_old" id="wpcc_pids_old" type="hidden" value="<?php echo $keys['pids']; ?>">
						<input name="wpcc_pids" id="wpcc_pids" type="hidden" value="<?php echo $keys['pids']; ?>">
						<input id="wpcc_submit_keymap" type="submit" name="Submit" class="button-primary" value="Save WPCC"/>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
</div>
<script type="text/javascript">
	function d(id) { return document.getElementById(id); }

	function switchSelect(val) {
			if (val == 'specific-pids') {
				s = d('specific-pids-box');
				s.className = 'wpcc-admin-show';
			} else {
				h = d('specific-pids-box');
				h.className = 'wpcc-admin-hidden';
			}
		}
		
	jQuery(document).ready(function($) { 
		if ($('#wpcc_where_to_add').val() == 'specific-pids') {
			$('#specific-pids-box').attr('class','wpcc-admin-show');
		} else {
			console.log($('#wpcc_where_to_add').val());
			$('#specific-pids-box').attr('class','wpcc-admin-hidden');
		}
	});
</script>
<?php
?>