<?php

require( 'config_secret.php' );

# --- Anonymous Access / Signup ---
$g_allow_signup                 = OFF;
$g_allow_anonymous_login        = ON;
$g_anonymous_account            = 'guest';

$g_display_errors = array(
    E_WARNING           => DISPLAY_ERROR_HALT,
    E_ALL               => DISPLAY_ERROR_INLINE,
);

$g_show_detailed_errors = ON;
$g_stop_on_errors = ON;

$g_user_login_valid_regex = '/^[\p{L}\p{N}+_.-]*\p{L}+[\p{L}\p{N}+_.-]*$/u';

$g_session_validation = OFF;
