<?php

require_once VERSIONMANAGEMENT_CORE_URI . 'version_management_api.php';
require_once VERSIONMANAGEMENT_CORE_URI . 'version_object.php';

process_page ();

/**
 * authenticates a user and removes a version if user has level to do
 */
function process_page ()
{
   auth_reauthenticate ();

   $version_id = gpc_get_int ( 'version_id' );
   $version_object = new version_object( $version_id );

   /** check if user has level in related project */
   access_ensure_project_level ( config_get ( 'manage_project_threshold' ), $version_object->get_version_project_id () );

   /** ensure that user confirms request to remove the version */
   helper_ensure_confirmed ( lang_get ( 'version_delete_sure' ) . '<br/>' . lang_get ( 'word_separator' ) .
      string_display_line ( $version_object->get_version_name () ), lang_get ( 'delete_version_button' ) );

   /** remove version */
   $version_object->delete_version ();

   /** redirect to view page */
   print_successful_redirect ( plugin_page ( 'version_view_page', true ) . '&amp;edit=0' );
}