<?php
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmVersion.php' );

/**
 * authenticates a user and removes a version if user has level to do
 */
auth_reauthenticate ();

$pluginWriteAccessLevel = plugin_config_get ( 'access_level' );

$userId = auth_get_current_user_id ();
$versionId = gpc_get_int ( 'version_id' );
$version = new vmVersion( $versionId );
$versionAccessLevel = vmApi::getProjectUserAccessLevel ( $version->getProjectId (), $userId );

if ( ( $versionAccessLevel >= $pluginWriteAccessLevel ) || ( user_is_administrator ( $userId ) ) )
{
   /** delete without request, if the version is not used anywhere */
   if ( !$version->isVersionIsUsed () )
   {
      /** remove version */
      $version->triggerDeleteFromDb ();
   }
   else
   {
      /** ensure that user confirms request to remove the version */
      helper_ensure_confirmed ( lang_get ( 'version_delete_sure' ) . '<br/>' . lang_get ( 'word_separator' ) .
         string_display_line ( $version->getVersionName () ), lang_get ( 'delete_version_button' ) );

      /** remove version */
      $version->triggerDeleteFromDb ();
   }
}

/** redirect to view page */
print_successful_redirect ( plugin_page ( 'version_view_page', true ) . '&amp;edit=0' );
