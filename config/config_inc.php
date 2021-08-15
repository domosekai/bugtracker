<?php

require( 'config_secret.php' );

# --- Anonymous Access / Signup ---
$g_allow_signup                 = OFF;
$g_allow_anonymous_login        = ON;
$g_anonymous_account            = 'guest';

# debug
$g_display_errors = array(
    E_WARNING           => DISPLAY_ERROR_HALT,
    E_ALL               => DISPLAY_ERROR_INLINE,
);
$g_show_detailed_errors = ON;
$g_stop_on_errors = ON;

# allow international username
$g_user_login_valid_regex = '/^[\p{L}\p{N}+_.-]*\p{L}+[\p{L}\p{N}+_.-]*$/u';

# turn off buggy IP validation
$g_session_validation = OFF;

# inline preview of images
$g_preview_attachments_inline_max_size = 2048576;
$g_preview_max_width = 540;
$g_preview_max_height = 960;

# type of attachments
$g_allowed_files = '';
$g_disallowed_files = 'exe,com,dll,vbs,apk,bat,cmd,msi';

# system logging
$g_show_version = ON;
$g_show_timer = ON;

# manager and above can view private issues and notes
$g_private_bug_threshold = MANAGER;
$g_private_bugnote_threshold = MANAGER;

# cookie length
$g_cookie_time_length = 60 * 60 * 24 * 30;

# allow user to edit its own issue
$g_bugnote_user_edit_threshold = REPORTER;
