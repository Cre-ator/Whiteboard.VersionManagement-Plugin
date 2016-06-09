<?php
/**
 * Created by PhpStorm.
 * User: stefan.schwarz
 * Date: 09.06.2016
 * Time: 13:33
 */

require_once VERSIONMANAGEMENT_CORE_URI . 'version_management_api.php';
require_once VERSIONMANAGEMENT_CORE_URI . 'version_processor.php';

process_delete ();

function process_delete ()
{
   auth_reauthenticate ();

   $version_id = $_GET[ 'version_id' ];
   $version_proc = new version_processor( $version_id );

   access_ensure_project_level ( config_get ( 'manage_project_threshold' ), $version_proc->get_version_project_id () );

   helper_ensure_confirmed ( lang_get ( 'version_delete_sure' ) .
      '<br/>' . lang_get ( 'word_separator' ) . string_display_line ( $version_proc->get_version_name () ),
      lang_get ( 'delete_version_button' ) );

   $version_proc->delete_version ();

   print_successful_redirect ( plugin_page ( 'version_view', true ) . '&amp;edit=0' );
}
