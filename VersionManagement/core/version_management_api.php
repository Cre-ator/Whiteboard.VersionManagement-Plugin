<?php

/**
 * Class version_management_api
 *
 * Contains functions for the plugin specific content
 */
class version_management_api
{
   public static function check_specmanagement_is_installed ()
   {
      return plugin_is_installed ( 'SpecManagement' ) && file_exists ( config_get_global ( 'plugin_path' ) . 'SpecManagement' );
   }

   public static function check_mantis_version_is_released ()
   {
      return substr ( MANTIS_VERSION, 0, 4 ) == '1.2.';
   }

   /**
    * returns true, if there is a duplicate entry.
    *
    * @param $array
    * @return bool
    */
   public static function check_array_for_duplicates ( $array )
   {
      return count ( $array ) !== count ( array_unique ( $array ) );
   }

   /**
    * checks a given id array for matching a given id. returns 1 when match, 0 otherwise
    *
    * @param $version_boolean_ids
    * @param $version_id
    * @return int
    */
   public static function set_boolean_value ( $version_boolean_ids, $version_id )
   {
      /** initialize released and obsolete flag */
      $boolean_value = 0;

      if ( !empty( $version_boolean_ids ) )
      {
         /** set released value */
         foreach ( $version_boolean_ids as $version_boolean_id )
         {
            /** version_id-match */
            if ( $version_id == $version_boolean_id )
            {
               $boolean_value = 1;
            }
         }
      }

      return $boolean_value;
   }

   /**
    * creates an array with a hash_string consisting of the name and its assigned project id
    *
    * @param $version_ids
    * @param $version_names
    * @return array
    */
   public static function get_version_hash_array ( $version_ids, $version_names )
   {
      $version_hash_array = array ();
      for ( $version_index = 0; $version_index < count ( $version_ids ); $version_index++ )
      {
         $version_hash_string = $version_names[ $version_index ] . '_' . version_get_field ( $version_ids[ $version_index ], 'project_id' );
         array_push ( $version_hash_array, $version_hash_string );
      }

      return $version_hash_array;
   }

   /**
    * sets the temp version name for each version
    *
    * @param $version_ids
    */
   public static function set_temp_version_name ( $version_ids )
   {
      for ( $version_index = 0; $version_index < count ( $version_ids ); $version_index++ )
      {
         $temp_version_name = 'tmp_version_name_' . $version_index;

         $version_id = $version_ids[ $version_index ];
         $version_object = new version_object( $version_id );

         /** update version name */
         $version_object->update_version_value ( 'version', $temp_version_name );

         /** trigger event */
         event_signal ( 'EVENT_MANAGE_VERSION_UPDATE', array ( $version_id ) );
      }
   }

   /**
    * sets the version data for all versions
    *
    * @param $version_ids
    * @param $version_names
    */
   public static function set_new_version_data ( $version_ids, $version_names )
   {
      $version_released = $_POST[ 'version_released' ];
      $version_obsolete = $_POST[ 'version_obsolete' ];
      $version_date_orders = $_POST[ 'version_date_order' ];
      $version_descriptions = $_POST[ 'version_description' ];

      for ( $version_index = 0; $version_index < count ( $version_ids ); $version_index++ )
      {
         $version_id = $version_ids[ $version_index ];
         $version_object = new version_object( $version_id );

         /** update version name */
         if ( !empty ( $version_names ) )
         {
            $version_object->update_version_value ( 'version', trim ( $version_names[ $version_index ] ) );
         }

         /** update version released */
         $version_object->update_version_value ( 'released', version_management_api::set_boolean_value ( $version_released, $version_id ) );

         /** update version obsolete */
         $version_object->update_version_value ( 'obsolete', version_management_api::set_boolean_value ( $version_obsolete, $version_id ) );

         /** update version date_order */
         if ( !empty ( $version_date_orders ) )
         {
            $version_object->update_version_value ( 'date_order', $version_date_orders[ $version_index ] );
         }

         /** update version description */
         if ( !empty ( $version_descriptions ) )
         {
            $version_object->update_version_value ( 'description', trim ( $version_descriptions[ $version_index ] ) );
         }

         /** trigger event */
         event_signal ( 'EVENT_MANAGE_VERSION_UPDATE', array ( $version_id ) );
      }
   }
}
