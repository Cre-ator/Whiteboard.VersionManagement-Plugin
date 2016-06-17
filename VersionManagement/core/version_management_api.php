<?php

/**
 * Class version_management_api
 *
 * Contains functions for the plugin specific content
 */
class version_management_api
{
   /**
    * retrns true, if the documentmanagement plugin is installed
    *
    * @return bool
    */
   public static function check_document_management_is_installed ()
   {
      return plugin_is_installed ( 'SpecManagement' ) && file_exists ( config_get_global ( 'plugin_path' ) . 'SpecManagement' );
   }

   /**
    * returns true, if the used mantis version is release 1.2.x
    *
    * @return bool
    */
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
    * returns the ids of all version to a given project
    *
    * @return array
    */
   public static function get_version_obsolete_hash_array ()
   {
      $current_project_id = helper_get_current_project ();
      $versions = version_get_all_rows_with_subs ( $current_project_id, null, true );

      $version_obsolete_hash_array = array ();
      foreach ( $versions as $version )
      {
         array_push ( $version_obsolete_hash_array, $version[ 'version' ] . '_' . $version[ 'project_id' ] );
      }

      return $version_obsolete_hash_array;
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
         /** ignore this item, if the version is obsolete. it will used later */
         if ( version_get_field ( $version_ids[ $version_index ], 'obsolete' ) == 1 )
         {
            continue;
         }
         /** gemerate hash string and push to hash array */
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
         $version = version_get ( $version_id );
         $version_object = new version_object( $version->id, $version->project_id, $version->version,
            $version->description, $version->date_order, $version->released, $version->obsolete );

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
   public static function set_version_data ( $version_ids, $version_names )
   {
      $post_version_released = $_POST[ 'version_released' ];
      $post_version_obsolete = $_POST[ 'version_obsolete' ];
      $post_version_date_orders = $_POST[ 'version_date_order' ];
      $post_version_descriptions = $_POST[ 'version_description' ];

      for ( $version_index = 0; $version_index < count ( $version_ids ); $version_index++ )
      {
         $version_id = $version_ids[ $version_index ];
         $version = version_get ( $version_id );
         $version_name = trim ( $version_names[ $version_index ] );
         $version_released = version_management_api::set_boolean_value ( $post_version_released, $version_id );
         $version_obsolete = version_management_api::set_boolean_value ( $post_version_obsolete, $version_id );
         $version_date_order = version_management_api::format_date ( $post_version_date_orders[ $version_index ] );
         $version_description = trim ( $post_version_descriptions[ $version_index ] );
         if ( strlen ( $version_name > 0 ) )
         {
            $version_object = new version_object( $version->id, $version->project_id, $version->version,
               $version->description, $version->date_order, $version->released, $version->obsolete );

            /** update version name */
            $version_object->update_version_value ( 'version', $version_name );

            /** update version released */
            $version_object->update_version_value ( 'released', $version_released );

            /** update version obsolete */
            $version_object->update_version_value ( 'obsolete', $version_obsolete );

            /** update version date_order */
            if ( strlen ( $version_date_order > 0 ) )
            {
               $version_object->update_version_value ( 'date_order', $version_date_order );
            }

            /** update version description */
            $version_object->update_version_value ( 'description', $version_description );

            /** trigger event */
            event_signal ( 'EVENT_MANAGE_VERSION_UPDATE', array ( $version_id ) );
         }
      }
   }

   /**
    * sets the version data for new added versions
    *
    * @param $version_ids
    * @param $version_names
    */
   public static function set_new_version_data ( $version_ids, $version_names )
   {
      $version_date_orders = $_POST[ 'version_date_order' ];
      $version_descriptions = $_POST[ 'version_description' ];

      $new_version_start_index = count ( $version_ids );

      if ( count ( $version_names ) > count ( $version_ids ) )
      {
         for ( $new_version_index = $new_version_start_index; $new_version_index < count ( $version_names ); $new_version_index++ )
         {
            $new_version_name = trim ( $version_names[ $new_version_index ] );
            $new_version_date_order = version_management_api::format_date ( $version_date_orders[ $new_version_index ] );
            $new_version_description = trim ( $version_descriptions[ $new_version_index ] );

            if ( strlen ( $new_version_name ) > 0 )
            {
               $current_project = helper_get_current_project ();
               $version_object = new version_object( null, $current_project, 'new_' . $new_version_index, '', time (), 0, 0 );
               $new_version_id = $version_object->insert_version ();
               $version_object->set_version_id ( $new_version_id );

               /** update version name */
               $version_object->update_version_value ( 'version', $new_version_name );

               /** update project id */
               $version_object->update_version_value ( 'project_id', $current_project );

               /** update version date_order */
               if ( strlen ( $new_version_date_order > 0 ) )
               {
                  $version_object->update_version_value ( 'date_order', $new_version_date_order );
               }

               /** update version description */
               $version_object->update_version_value ( 'description', $new_version_description );

               /** trigger event */
               event_signal ( 'EVENT_MANAGE_VERSION_UPDATE', array ( $new_version_id ) );
            }
         }
      }
   }

   /**
    * format a given value to a valid integer
    *
    * @param $date_value
    * @return int
    */
   public static function format_date ( $date_value )
   {
      if ( !is_numeric ( $date_value ) )
      {
         if ( $date_value == '' )
         {
            $date_value = date_get_null ();
         }
         else
         {
            $date_value = strtotime ( $date_value );
            if ( $date_value === false )
            {
               trigger_error ( ERROR_INVALID_DATE_FORMAT, ERROR );
            }
         }
      }

      return $date_value;
   }
}
