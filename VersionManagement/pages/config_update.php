<?php
require_once ( __DIR__ . '/../core/constant_api.php' );
require_once ( __DIR__ . '/../core/vmApi.php' );

auth_reauthenticate ();
access_ensure_global_level ( config_get ( 'access_level' ) );

$optionChange = gpc_get_bool ( 'config_change', false );
$optionReset = gpc_get_bool ( 'config_reset', false );

if ( $optionReset )
{
   /* todo reset plugin data */
}

if ( $optionChange )
{
   vmApi::updateSingleValue ( 'access_level', ADMINISTRATOR );
   vmApi::updateButton ( 'show_menu' );
   vmApi::updateButton ( 'show_footer' );
   vmApi::updateColor ( 'unused_version_row_color', '#908b2d' );
}

form_security_purge ( 'plugin_VersionManagement_config_update' );

print_successful_redirect ( plugin_page ( 'config_page', true ) );