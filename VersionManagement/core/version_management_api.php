<?php
require_once ( __DIR__ . '/vmVersion.php' );

/**
 * Class version_management_api
 *
 * Contains functions for the plugin specific content
 */
class version_management_api
{
   /**
    * get database connection infos and connect to the database
    *
    * @return mysqli
    */
   public static function initializeDbConnection ()
   {
      $dbPath = config_get ( 'hostname' );
      $dbUser = config_get ( 'db_username' );
      $dbPass = config_get ( 'db_password' );
      $dbName = config_get ( 'database_name' );

      $mysqli = new mysqli( $dbPath, $dbUser, $dbPass, $dbName );
      $mysqli->connect ( $dbPath, $dbUser, $dbPass, $dbName );

      return $mysqli;
   }

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
    * returns true if the new version data is completely valid
    *
    * @param $version_ids
    * @param $version_names
    * @return bool
    */
   public static function check_version_data_is_valid ( $version_ids, $version_names )
   {
      $post_version_released = $_POST[ 'version_released' ];
      $post_version_obsolete = $_POST[ 'version_obsolete' ];
      $post_version_date_orders = $_POST[ 'version_date_order' ];

      for ( $version_index = 0; $version_index < count ( $version_ids ); $version_index++ )
      {
         $version_id = $version_ids[ $version_index ];
         $version_name = trim ( $version_names[ $version_index ] );
         $version_released = version_management_api::set_boolean_value ( $post_version_released, $version_id );
         $version_obsolete = version_management_api::set_boolean_value ( $post_version_obsolete, $version_id );
         $version_date_order = version_management_api::format_date ( $post_version_date_orders[ $version_index ] );

         if (
            ( strlen ( $version_name ) == 0 ) ||
            ( ( $version_released < 0 ) || ( $version_released > 1 ) ) ||
            ( ( $version_obsolete < 0 ) || ( $version_obsolete > 1 ) ) ||
            ( !is_numeric ( $version_date_order ) )
         )
         {
            return false;
         }
      }

      return true;
   }

   /**
    * sets the temp version name for each version
    *
    * @param $versionIds
    */
   public static function setTmpVersionName ( $versionIds )
   {
      for ( $index = 0; $index < count ( $versionIds ); $index++ )
      {
         $tmpVersionName = 'tmp_version_name_' . $index;

         $versionId = $versionIds[ $index ];
         $version = new vmVersion( $versionId );
         $version->setVersionName ( $tmpVersionName );
         $version->triggerUpdateInDb ();

         /** trigger event */
         event_signal ( 'EVENT_MANAGE_VERSION_UPDATE', array ( $versionId ) );
      }
   }

   /**
    * sets the version data for all versions
    *
    * @param $version_ids
    * @param $version_names
    */
   public static function setVersionData ( $version_ids, $version_names )
   {
      $postVersionDescription = $_POST[ 'version_description' ];
      $postVersionReleased = $_POST[ 'version_released' ];
      $postVersionObsolete = $_POST[ 'version_obsolete' ];
      $postVersionDateOrder = $_POST[ 'version_date_order' ];
      $postVersionDocumentType = $_POST[ 'version-type' ];

      for ( $index = 0; $index < count ( $version_ids ); $index++ )
      {
         $versionId = $version_ids[ $index ];
         $version = new vmVersion( $versionId );
         $version->setVersionName ( trim ( $version_names[ $index ] ) );
         $version->setDescription ( trim ( $postVersionDescription[ $index ] ) );
         $version->setReleased ( version_management_api::set_boolean_value ( $postVersionReleased, $versionId ) );
         $version->setObsolete ( version_management_api::set_boolean_value ( $postVersionObsolete, $versionId ) );
         $version->setDateOrder ( version_management_api::format_date ( $postVersionDateOrder[ $index ] ) );
         $version->triggerUpdateInDb ();

         $versionDocumentType = $postVersionDocumentType[ $index ];

         /** update version document type */
         if ( !empty( $postVersionDocumentType ) )
         {
            require_once ( __DIR__ . '/../../SpecManagement/core/specmanagement_database_api.php' );
            $specmanagementDatabaseApi = new specmanagement_database_api();
            $versionProjectId = $version->getProjectId ();
            if ( strlen ( $versionDocumentType ) > 0 )
            {
               $typeId = $specmanagementDatabaseApi->get_type_id ( $versionDocumentType );
               $specmanagementDatabaseApi->update_version_associated_type ( $versionProjectId, $versionId, $typeId );
            }
            else
            {
               $specmanagementDatabaseApi->update_version_associated_type ( $versionProjectId, $versionId, 9999 );
            }
         }

         /** trigger event */
         event_signal ( 'EVENT_MANAGE_VERSION_UPDATE', array ( $versionId ) );
      }
   }

   /**
    * sets the version data for new added versions
    *
    * @param $versionIds
    * @param $versionNames
    */
   public static function set_new_version_data ( $versionIds, $versionNames )
   {
      $postVersionDateOrder = $_POST[ 'version_date_order' ];
      $postVersionDescription = $_POST[ 'version_description' ];

      $newVersionIndex = count ( $versionIds );

      if ( count ( $versionNames ) > count ( $versionIds ) )
      {
         for ( $index = $newVersionIndex; $index < count ( $versionNames ); $index++ )
         {
            $newVersionName = trim ( $versionNames[ $index ] );
            $newVersionDateOrder = version_management_api::format_date ( $postVersionDateOrder[ $index ] );
            $newVersionDescription = trim ( $postVersionDescription[ $index ] );

            if ( strlen ( $newVersionName ) > 0 )
            {
               $currentProjectId = helper_get_current_project ();
               if ( $currentProjectId > 0 )
               {
                  $newVersion = new vmVersion();
                  $newVersion->setProjectId ( $currentProjectId );
                  $newVersion->setVersionName ( $newVersionName );
                  $newVersion->setDescription ( $newVersionDescription );
                  $newVersion->setReleased ( 0 );
                  $newVersion->setObsolete ( 0 );
                  $newVersion->setDateOrder ( $newVersionDateOrder );
                  $newVersion->triggerInsertIntoDb ();

                  /** trigger event */
                  event_signal ( 'EVENT_MANAGE_VERSION_UPDATE', array ( $newVersion->getVersionId () ) );
               }
            }
         }
      }
   }

   /**
    * format a given value to a valid integer
    * returns actual time, if the date value is null or empty
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
            $date_value = time ();
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
