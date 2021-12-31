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

require_api( 'bug_api.php' );
require_api( 'bugnote_api.php' );
require_api( 'category_api.php' );
require_api( 'columns_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'custom_field_api.php' );
require_api( 'helper_api.php' );
require_api( 'history_api.php' );
require_api( 'html_api.php' );
require_api( 'icon_api.php' );
require_api( 'lang_api.php' );
require_api( 'prepare_api.php' );
require_api( 'print_api.php' );
require_api( 'string_api.php' );
require_api( 'utility_api.php' );
require_api( 'version_api.php' );

/**
 * Prints one entry in the changelog.
 *
 * @param integer $p_issue_id    Issue id.
 * @param integer $p_issue_level Issue level.
 * @return void
 */
function custom_function_override_changelog_print_issue( $p_issue_id, $p_issue_level = 0 ) {
	static $s_status;

	$t_bug = bug_get( $p_issue_id );
	$t_current_user = auth_get_current_user_id();

	if( $t_bug->category_id ) {
		$t_category_name = category_get_name( $t_bug->category_id );
	} else {
		$t_category_name = '';
	}

	$t_category = is_blank( $t_category_name ) ? '' : '<strong>[' . string_display_line( $t_category_name ) . ']</strong> ';

	if( !isset( $s_status[$t_bug->status] ) ) {
		$s_status[$t_bug->status] = get_enum_element( 'status', $t_bug->status, $t_current_user, $t_bug->project_id );
	}

	# choose color based on status
	$t_status_css = html_get_status_css_fg( $t_bug->status, $t_current_user, $t_bug->project_id );
	$t_status_title = string_attribute( get_enum_element( 'status', bug_get_field( $t_bug->id, 'status' ), $t_bug->project_id ) );

	echo utf8_str_pad( '', $p_issue_level * 36, '&#160;' );
	print_icon( 'fa-square', 'fa-status-box ' . $t_status_css, $t_status_title );
	echo ' ' . string_get_bug_view_link( $p_issue_id, false );
	echo ': <span class="label label-light">', $t_category, '</span> ' , string_display_line_links( $t_bug->summary );
	if( $t_bug->reporter_id > 0 ) {
		echo ' (', prepare_user_name( $t_bug->reporter_id ), ')';
	}
	echo '<div class="space-2"></div>';
}

/**
 * Prints one entry in the roadmap.
 *
 * @param integer $p_issue_id    Issue id.
 * @param integer $p_issue_level Issue level.
 * @return void
 */
function custom_function_override_roadmap_print_issue( $p_issue_id, $p_issue_level = 0 ) {
	static $s_status;

	$t_bug = bug_get( $p_issue_id );
	$t_current_user = auth_get_current_user_id();

	if( bug_is_resolved( $p_issue_id ) ) {
		$t_strike_start = '<s>';
		$t_strike_end = '</s>';
	} else {
		$t_strike_start = $t_strike_end = '';
	}

	if( $t_bug->category_id ) {
		$t_category_name = category_get_name( $t_bug->category_id );
	} else {
		$t_category_name = '';
	}

	$t_category = is_blank( $t_category_name ) ? '' : '<strong>[' . string_display_line( $t_category_name ) . ']</strong> ';

	if( !isset( $s_status[$t_bug->status] ) ) {
		$s_status[$t_bug->status] = get_enum_element( 'status', $t_bug->status, $t_current_user, $t_bug->project_id );
	}

	# choose color based on status
	$t_status_css = html_get_status_css_fg( $t_bug->status, $t_current_user, $t_bug->project_id );
	$t_status_title = string_attribute( get_enum_element( 'status', bug_get_field( $t_bug->id, 'status' ), $t_bug->project_id ) );

	echo utf8_str_pad( '', $p_issue_level * 36, '&#160;' );
	print_icon( 'fa-square', 'fa-status-box ' . $t_status_css, $t_status_title );
	echo ' ' . string_get_bug_view_link( $p_issue_id, false );
	echo ': <span class="label label-light">', $t_category, '</span> ', $t_strike_start, string_display_line_links( $t_bug->summary ), $t_strike_end;
	if( $t_bug->reporter_id > 0 ) {
		echo ' (', prepare_user_name( $t_bug->reporter_id ), ')';
	}
	echo '<div class="space-2"></div>';
}
