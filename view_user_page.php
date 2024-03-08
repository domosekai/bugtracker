<?php
# MantisBT - A PHP based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * View User Page
 *
 * @package MantisBT
 * @copyright Copyright 2002  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 *
 * @uses core.php
 * @uses access_api.php
 * @uses authentication_api.php
 * @uses config_api.php
 * @uses constant_inc.php
 * @uses error_api.php
 * @uses gpc_api.php
 * @uses helper_api.php
 * @uses html_api.php
 * @uses lang_api.php
 * @uses print_api.php
 * @uses string_api.php
 * @uses user_api.php
 * @uses utility_api.php
 */

require_once( 'core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'error_api.php' );
require_api( 'gpc_api.php' );
require_api( 'helper_api.php' );
require_api( 'html_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'string_api.php' );
require_api( 'user_api.php' );
require_api( 'utility_api.php' );
require_css( 'status_config.php' );

auth_ensure_user_authenticated();

# extracts the user information and prefixes it with u_
$f_user_id = gpc_get_int( 'id', auth_get_current_user_id() );
$t_row = user_get_row( $f_user_id );
if( !$t_row ) {
	error_parameters( $f_user_id );
	trigger_error( ERROR_USER_BY_ID_NOT_FOUND, ERROR );
}

# set user id for my_view_inc.php
$t_current_user_id = $f_user_id;
$t_current_project_id = helper_get_current_project();
$t_hide_status_default = META_FILTER_NONE;

extract( $t_row, EXTR_PREFIX_ALL, 'u' );

$t_can_manage = access_has_global_level( config_get( 'manage_user_threshold' ) ) &&
	access_has_global_level( $u_access_level );

$t_can_see_realname = $t_can_manage || user_show_realname() ||
    access_has_project_level( config_get( 'show_user_realname_threshold' ) );
$t_can_see_email = $t_can_manage || access_has_project_level( config_get( 'show_user_email_threshold' ) );
$t_can_impersonate = auth_can_impersonate( $f_user_id );

# In case we're using LDAP to get the email address... this will pull out
#  that version instead of the one in the DB
$u_email = user_get_email( $u_id );
$u_realname = user_get_realname( $u_id );

$t_date_format = config_get( 'normal_date_format' );

layout_page_header();

layout_page_begin();

$f_page_number = gpc_get_int( 'page_number', 1 );

$t_per_page = config_get( 'user_view_bug_count' );
$t_bug_count = null;
$t_page_count = null;

if( $t_current_project_id == ALL_PROJECTS ) {
	$t_project_ids_to_check = null;
} else {
	$t_test_filter = filter_ensure_valid_filter( array( FILTER_PROPERTY_PROJECT_ID => [$t_current_project_id]) );
	$t_project_ids_to_check = filter_get_included_projects( $t_test_filter );
}

$t_boxes = array_filter( config_get( 'user_view_boxes' ) );
foreach( $t_boxes as $t_box_title => $t_box_display ) {
	if( # Remove "Assigned to Me" box for users that can't handle issues
		(  $t_box_title == 'assigned'
		&& ( !access_has_any_project_level('handle_bug_threshold', $t_project_ids_to_check, $f_user_id ) )
		) ||
		# Remove "Monitored by Me" box for users that can't monitor issues
		(  $t_box_title == 'monitored'
		&& ( !access_has_any_project_level( 'monitor_bug_threshold', $t_project_ids_to_check, $f_user_id ) )
		) ||
		# Remove display of "Reported by Me", "Awaiting Feedback" and
		# "Awating confirmation of resolution" boxes for users that can't report bugs
		(  in_array( $t_box_title, array( 'reported', 'feedback', 'verify' ) )
		&& ( !access_has_any_project_level( 'report_bug_threshold', $t_project_ids_to_check, $f_user_id ) )
		)
	) {
		unset( $t_boxes[$t_box_title] );
	}
}
asort( $t_boxes );

$t_timeline_view_threshold_access = access_has_project_level( config_get( 'timeline_view_threshold' ) );
$t_timeline_view_class = ( $t_timeline_view_threshold_access ) ? "col-md-7" : "col-md-12";
?>
<div class="<?php echo $t_timeline_view_class ?> col-xs-12">
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<?php print_icon( 'fa-user', 'ace-icon' ); ?>
		<?php echo lang_get('view_account_title') ?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive">
<table class="table table-bordered table-condensed table-striped">
	<fieldset>
	<tr>
		<th class="category">
			<?php echo lang_get( 'username' ) ?>
		</th>
		<td>
			<?php echo string_display_line( $u_username ) ?>
		</td>
	</tr>
	<?php
		if( $t_can_see_realname ) { ?>
			<tr>
				<th class="category">
					<?php echo lang_get( 'realname' ) ?>
				</th>
				<td>
					<?php echo string_display_line( $u_realname ); ?>
				</td>
			</tr>
	<?php } ?>
	<?php
		if( $t_can_see_email ) { ?>
			<tr>
				<th class="category">
					<?php echo lang_get( 'email' ) ?>
				</th>
				<td>
				    <?php
						if( !is_blank( $u_email ) ) {
							print_email_link( $u_email, $u_email );
						}
					?>
				</td>
			</tr>
	<?php } ?>
	<?php
		if( $t_can_manage ) { ?>
			<tr>
				<th class="category">
					<?php echo lang_get( 'access_level' ) ?>
				</th>
				<td>
					<?php echo string_display_line( get_enum_element( 'access_levels', $u_access_level ) ); ?>
				</td>
			</tr>
			<tr>
				<th class="category">
					<?php echo lang_get( 'enabled' ) ?>
				</th>
				<td>
					<?php echo $u_enabled ? lang_get( 'yes' ) : lang_get( 'no' ); ?>
				</td>
			</tr>
			<tr>
				<th class="category">
					<?php echo lang_get( 'protected' ) ?>
				</th>
				<td>
					<?php echo $u_protected ? lang_get( 'yes' ) : lang_get( 'no' ); ?>
				</td>
			</tr>
			<tr>
				<th class="category">
					<?php echo lang_get( 'date_created' ) ?>
				</th>
				<td>
					<?php echo date( $t_date_format, $u_date_created ); ?>
				</td>
			</tr>
			<tr>
				<th class="category">
					<?php echo lang_get( 'last_visit' ) ?>
				</th>
				<td>
					<?php echo date( $t_date_format, $u_last_visit ); ?>
				</td>
			</tr>
	<?php } ?>
	</fieldset>
</table>
	</div>
	</div>
	<?php if( $t_can_manage || $t_can_impersonate ) { ?>
	<div class="widget-toolbox padding-8 clearfix">
		<?php if( $t_can_manage ) { ?>
		<form id="manage-user-form" method="get" action="manage_user_edit_page.php" class="pull-left">
			<fieldset>
				<input type="hidden" name="user_id" value="<?php echo $f_user_id ?>" />
				<input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get( 'manage_user' ) ?>" /></span>
			</fieldset>
		</form>
		<?php } ?>
		<?php if( $t_can_impersonate ) { ?>
		<form id="manage-user-impersonate-form" method="post" action="manage_user_impersonate.php" class="pull-right">
			<fieldset>
				<?php echo form_security_field( 'manage_user_impersonate' ) ?>
				<input type="hidden" name="user_id" value="<?php echo $f_user_id ?>" />
				<span><input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get( 'impersonate_user_button' ) ?>" /></span>
			</fieldset>
		</form>
		<?php } ?>
	</div>
	<?php } ?>
</div>
</div>

<?php
define( 'MY_VIEW_INC_ALLOW', true );
echo '<div class="space-10"></div>';

foreach( $t_boxes as $t_box_title => $t_box_display ) {
    include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'my_view_inc.php' );
    echo '<div class="space-10"></div>';
}
?>
</div>

<?php
if( $t_timeline_view_threshold_access ) {
	# Build a filter to show all bugs in current projects
	$g_timeline_filter = array();
	$g_timeline_filter[FILTER_PROPERTY_HIDE_STATUS] = array( META_FILTER_NONE );
	$g_timeline_filter = filter_ensure_valid_filter( $g_timeline_filter );
	$g_timeline_user = $f_user_id;
	?>
	<div class="col-md-5 col-xs-12">
		<?php include( $g_core_path . 'timeline_inc.php' ); ?>
		<div class="space-10"></div>
	</div>
<?php } ?>

<?php
layout_page_end();
