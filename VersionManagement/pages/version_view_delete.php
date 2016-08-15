<?php
require_once ( __DIR__ . '/../core/vmVersion.php' );

/**
 * authenticates a user and removes a version if user has level to do
 */
auth_reauthenticate ();

$versionId = gpc_get_int ( 'version_id' );
$version = new vmVersion( $versionId );

/** check if user has level in related project */
access_ensure_project_level ( config_get ( 'manage_project_threshold' ), $version->getProjectId () );

/** delete without request, if the version is not used anywhere */
if ( !$version->checkVersionIsUsed () )
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

/** redirect to view page */
print_successful_redirect ( plugin_page ( 'version_view_page', true ) . '&amp;edit=0' );
