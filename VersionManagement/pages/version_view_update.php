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
      $version_released = $_POST[ 'version_released' ];
      $version_obsolete = $_POST[ 'version_obsolete' ];
      $version_date_orders = $_POST[ 'version_date_order' ];
      $version_descriptions = $_POST[ 'version_description' ];

      for ( $version_index = 0; $version_index < count ( $version_ids ); $version_index++ )
      {
         $version_id = $version_ids[ $version_index ];
         $version = version_get ( $version_id );

         /** version name */
         if ( !is_null ( $version_names ) )
         {
            $version->version = trim ( $version_names[ $version_index ] );
         }

         /** version released */
         $version->released = set_boolean_value ( $version_released, $version_id );

         /** version obsolete */
         $version->obsolete = set_boolean_value ( $version_obsolete, $version_id );

         /** version date_order */
         if ( !is_null ( $version_date_orders ) )
         {
            $version->date_order = $version_date_orders[ $version_index ];
         }

         /** version description */
         if ( !is_null ( $version_descriptions ) )
         {
            $version->description = $version_descriptions[ $version_index ];
         }

         /** update version */
         version_update ( $version );

         /** trigger event */
         event_signal ( 'EVENT_MANAGE_VERSION_UPDATE', array ( $version_id ) );
      }

      /** redirect to view page */
      form_security_purge ( 'plugin_VersionManagement_version_view_update' );
      print_successful_redirect ( plugin_page ( 'version_view_page', true ) . '&amp;edit=0' );
   }
}

/**
 * checks a given id array for matching a given id. returns 1 when match, 0 otherwise
 *
 * @param $version_boolean_ids
 * @param $version_id
 * @return int
 */
function set_boolean_value ( $version_boolean_ids, $version_id )
{
   /** initialize released and obsolete flag */
   $version_boolean_value = 0;

   /** set released value */
   foreach ( $version_boolean_ids as $version_boolean_id )
   {
      /** version_id-match */
      if ( $version_id == $version_boolean_id )
      {
         $version_boolean_value = 1;
      }
   }

   return $version_boolean_value;
}