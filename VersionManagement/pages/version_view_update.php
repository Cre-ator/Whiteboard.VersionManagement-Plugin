<?php

require_once VERSIONMANAGEMENT_CORE_URI . 'version_management_api.php';
require_once VERSIONMANAGEMENT_CORE_URI . 'version_object.php';

process_page ();

/**
 * authenticates a user and updates a version if user has level to do
 */
function process_page ()
{
   if ( isset( $_POST[ 'version_id' ] ) )
   {
      auth_reauthenticate ();
      $version_ids = $_POST[ 'version_id' ];
      $version_names = $_POST[ 'version_name' ];

      $version_hash_array = version_management_api::get_version_hash_array ( $version_ids, $version_names );

      if ( !version_management_api::check_array_for_duplicates ( $version_hash_array ) )
      {
         /** update all version names with temp values */
         version_management_api::set_temp_version_name ( $version_ids );

         /** update all versions with new values */
         version_management_api::set_new_version_data ( $version_ids, $version_names );
      }
      else
      {
         echo 'duplikate in den versionsnamen enthalten';
      }

      /** redirect to view page */
      print_successful_redirect ( plugin_page ( 'version_view_page', true ) . '&edit=0&obsolete=0' );
   }
}