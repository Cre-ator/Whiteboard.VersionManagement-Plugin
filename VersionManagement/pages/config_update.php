<?php
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmApi.php' );

auth_reauthenticate ();

$optionChange = gpc_get_bool ( 'config_change', false );

if ( $optionChange )
{
   vmApi::updateSingleValue ( 'access_level', ADMINISTRATOR );
   vmApi::updateButton ( 'show_menu' );
   vmApi::updateButton ( 'show_footer' );
   vmApi::updateColor ( 'unused_version_row_color', '#908b2d' );
}

form_security_purge ( 'plugin_VersionManagement_config_update' );

print_successful_redirect ( plugin_page ( 'config_page', true ) );