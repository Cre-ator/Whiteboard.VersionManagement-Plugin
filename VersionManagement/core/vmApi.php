<?php
require_once ( __DIR__ . DIRECTORY_SEPARATOR . 'vmVersion.php' );

/**
 * Class version_management_api
 *
 * Contains functions for the plugin specific content
 */
class vmApi
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
    * returns array with 1/0 values when plugin comprehensive table is installed
    *
    * @return array
    */
   public static function checkWhiteboardTablesExist ()
   {
      $boolArray = array ();

      $boolArray[ 0 ] = self::checkTable ( 'menu' );

      return $boolArray;
   }

   /**
    * checks if given table exists
    *
    * @param $tableName
    * @return bool
    */
   private static function checkTable ( $tableName )
   {
      $mysqli = self::initializeDbConnection ();

      $query = /** @lang sql */
         'SELECT COUNT(id) FROM mantis_plugin_whiteboard_' . $tableName . '_table';
      $result = $mysqli->query ( $query );
      $mysqli->close ();
      if ( $result->num_rows != 0 )
      {
         return true;
      }
      else
      {
         return false;
      }
   }

   public static function checkPluginIsRegisteredInWhiteboardMenu ()
   {
      $pluginName = plugin_lang_get ( 'menu_title', 'VersionManagement' );

      $mysqli = self::initializeDbConnection ();

      $query = /** @lang sql */
         'SELECT COUNT(id) FROM mantis_plugin_whiteboard_menu_table
         WHERE plugin_name=\'' . $pluginName . '\'';

      $result = $mysqli->query ( $query );
      $mysqli->close ();
      if ( $result->num_rows != 0 )
      {
         $resultCount = mysqli_fetch_row ( $result )[ 0 ];
         if ( $resultCount > 0 )
         {
            return true;
         }
      }
      return false;
   }

   /**
    * register plugin in whiteboard menu
    */
   public static function addPluginToWhiteboardMenu ()
   {
      $pluginName = plugin_lang_get ( 'menu_title', 'VersionManagement' );
      $pluginAccessLevel = ADMINISTRATOR;
      $pluginShowMenu = ON;
      $pluginMenuPath = '<a href="' . plugin_page ( 'version_view_page' ) . '&amp;sort=0&amp;edit=0&amp;obsolete=0">' . plugin_lang_get ( 'menu_title' ) . '</a >';

      $mysqli = self::initializeDbConnection ();

      $query = /** @lang sql */
         'INSERT INTO mantis_plugin_whiteboard_menu_table (id, plugin_name, plugin_access_level, plugin_show_menu, plugin_menu_path)
         SELECT null,\'' . $pluginName . '\',' . $pluginAccessLevel . ',' . $pluginShowMenu . ',\'' . $pluginMenuPath . '\'
         FROM DUAL WHERE NOT EXISTS (
         SELECT 1 FROM mantis_plugin_whiteboard_menu_table
         WHERE plugin_name=\'' . $pluginName . '\')';

      $mysqli->query ( $query );
      $mysqli->close ();
   }

   /**
    * edit plugin data in whiteboard menu
    *
    * @param $field
    * @param $value
    */
   public static function editPluginInWhiteboardMenu ( $field, $value )
   {
      $pluginName = plugin_lang_get ( 'menu_title', 'VersionManagement' );

      $mysqli = self::initializeDbConnection ();

      $query = /** @lang sql */
         'UPDATE mantis_plugin_whiteboard_menu_table
         SET ' . $field . '=\'' . $value . '\'
         WHERE plugin_name =\'' . $pluginName . '\'';

      $mysqli->query ( $query );
      $mysqli->close ();
   }

   /**
    * remove plugin from whiteboard menu
    */
   public static function removePluginFromWhiteboardMenu ()
   {
      $pluginName = plugin_lang_get ( 'menu_title', 'VersionManagement' );

      $mysqli = self::initializeDbConnection ();

      $query = /** @lang sql */
         'DELETE FROM mantis_plugin_whiteboard_menu_table
         WHERE plugin_name=\'' . $pluginName . '\'';

      $mysqli->query ( $query );
      $mysqli->close ();
   }

   /**
    * retrns true, if the documentmanagement plugin is installed
    *
    * @return bool
    */
   public static function checkDMManagementPluginIsInstalled ()
   {
      return plugin_is_installed ( 'SpecManagement' ) && file_exists ( config_get_global ( 'plugin_path' ) . 'SpecManagement' );
   }

   /**
    * returns true, if there is a duplicate entry.
    *
    * @param $array
    * @return bool
    */
   public static function checkArrayDuplicates ( $array )
   {
      return count ( $array ) !== count ( array_unique ( $array ) );
   }

   /**
    * checks a given id array for matching a given id. returns 1 when match, 0 otherwise
    *
    * @param $versionBoolIds
    * @param $versionId
    * @return int
    */
   private static function setBoolean ( $versionBoolIds, $versionId )
   {
      /** initialize released and obsolete flag */
      $bool = 0;

      if ( !empty( $versionBoolIds ) )
      {
         /** set released value */
         foreach ( $versionBoolIds as $versionBoolId )
         {
            /** version_id-match */
            if ( $versionId == $versionBoolId )
            {
               $bool = 1;
            }
         }
      }

      return $bool;
   }

   /**
    * returns the ids of all version to a given project
    *
    * @return array
    */
   public static function getVersionObsoleteHashArray ()
   {
      $currentProjectId = helper_get_current_project ();
      $versions = version_get_all_rows_with_subs ( $currentProjectId, null, true );

      $versionObsoleteHashArray = array ();
      foreach ( $versions as $version )
      {
         array_push ( $versionObsoleteHashArray, $version[ 'version' ] . '_' . $version[ 'project_id' ] );
      }

      return $versionObsoleteHashArray;
   }

   /**
    * creates an array with a hash_string consisting of the name and its assigned project id
    *
    * @param $versionIds
    * @param $versionNames
    * @return array
    */
   public static function getVersionValidHashArray ( $versionIds, $versionNames )
   {
      $versionValidHashArray = array ();
      for ( $index = 0; $index < count ( $versionIds ); $index++ )
      {
         /** ignore this item, if the version is obsolete. it will used later */
         if ( version_get_field ( $versionIds[ $index ], 'obsolete' ) == 1 )
         {
            continue;
         }
         /** gemerate hash string and push to hash array */
         $hashString = $versionNames[ $index ] . '_' . version_get_field ( $versionIds[ $index ], 'project_id' );
         array_push ( $versionValidHashArray, $hashString );
      }

      return $versionValidHashArray;
   }

   /**
    * returns true if the new version data is completely valid
    *
    * @param $versionIds
    * @param $versionNames
    * @return bool
    */
   public static function checkVersionDataIsValid ( $versionIds, $versionNames )
   {
      $postVersionReleased = $_POST[ 'version_released' ];
      $postVersionObsolete = $_POST[ 'version_obsolete' ];
      $postVersionDateOrder = $_POST[ 'version_date_order' ];

      for ( $index = 0; $index < count ( $versionIds ); $index++ )
      {
         $versionId = $versionIds[ $index ];
         $versionName = trim ( $versionNames[ $index ] );
         $versionReleased = self::setBoolean ( $postVersionReleased, $versionId );
         $versionObsolete = self::setBoolean ( $postVersionObsolete, $versionId );
         $versionDateOrder = self::formatDate ( $postVersionDateOrder[ $index ] );

         if (
            ( strlen ( $versionName ) == 0 ) ||
            ( ( $versionReleased < 0 ) || ( $versionReleased > 1 ) ) ||
            ( ( $versionObsolete < 0 ) || ( $versionObsolete > 1 ) ) ||
            ( !is_numeric ( $versionDateOrder ) )
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
    * @param $versionIds
    * @param $versionNames
    */
   public static function setVersionData ( $versionIds, $versionNames )
   {
      $postVersionDescription = $_POST[ 'version_description' ];
      $postVersionReleased = $_POST[ 'version_released' ];
      $postVersionObsolete = $_POST[ 'version_obsolete' ];
      $postVersionDateOrder = $_POST[ 'version_date_order' ];
      $postVersionDocumentType = $_POST[ 'version-type' ];

      for ( $index = 0; $index < count ( $versionIds ); $index++ )
      {
         $versionId = $versionIds[ $index ];
         $version = new vmVersion( $versionId );
         $version->setVersionName ( trim ( $versionNames[ $index ] ) );
         $version->setDescription ( trim ( $postVersionDescription[ $index ] ) );
         $version->setReleased ( self::setBoolean ( $postVersionReleased, $versionId ) );
         $version->setObsolete ( self::setBoolean ( $postVersionObsolete, $versionId ) );
         $version->setDateOrder ( self::formatDate ( $postVersionDateOrder[ $index ] ) );
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
   public static function setNewVersionData ( $versionIds, $versionNames )
   {
      $postVersionDateOrder = $_POST[ 'version_date_order' ];
      $postVersionDescription = $_POST[ 'version_description' ];

      $newVersionIndex = count ( $versionIds );

      if ( count ( $versionNames ) > count ( $versionIds ) )
      {
         for ( $index = $newVersionIndex; $index < count ( $versionNames ); $index++ )
         {
            $newVersionName = trim ( $versionNames[ $index ] );
            $newVersionDateOrder = self::formatDate ( $postVersionDateOrder[ $index ] );
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
    * @param $date
    * @return int
    */
   private static function formatDate ( $date )
   {
      if ( !is_numeric ( $date ) )
      {
         if ( $date == '' )
         {
            $date = time ();
         }
         else
         {
            $date = strtotime ( $date );
            if ( $date === false )
            {
               trigger_error ( ERROR_INVALID_DATE_FORMAT, ERROR );
            }
         }
      }

      return $date;
   }


   /**
    * Adds the "#"-Tag if necessary
    *
    * @param $color
    * @return string
    */
   private static function includeLeadingColorIdentifier ( $color )
   {
      if ( "#" == $color[ 0 ] )
      {
         return $color;
      }
      else
      {
         return "#" . $color;
      }
   }

   /**
    * Updates a specific color value in the plugin
    *
    * @param $fieldName
    * @param $defaultColor
    */
   public static function updateColor ( $fieldName, $defaultColor )
   {
      $defaultColor = self::includeLeadingColorIdentifier ( $defaultColor );
      $color = self::includeLeadingColorIdentifier ( gpc_get_string ( $fieldName, $defaultColor ) );

      if ( plugin_config_get ( $fieldName ) != $color && plugin_config_get ( $fieldName ) != '' )
      {
         plugin_config_set ( $fieldName, $color );
      }
      elseif ( plugin_config_get ( $fieldName ) == '' )
      {
         plugin_config_set ( $fieldName, $defaultColor );
      }
   }

   /**
    * Updates the value set by a button
    *
    * @param $config
    */
   public static function updateButton ( $config )
   {
      $button = gpc_get_int ( $config );

      if ( plugin_config_get ( $config ) != $button )
      {
         plugin_config_set ( $config, $button );
      }
   }

   /**
    * Updates the value set by an input text field
    *
    * @param $value
    * @param $constant
    */
   public static function updateSingleValue ( $value, $constant )
   {
      $actualValue = null;

      if ( is_int ( $value ) )
      {
         $actualValue = gpc_get_int ( $value, $constant );
      }

      if ( is_string ( $value ) )
      {
         $actualValue = gpc_get_string ( $value, $constant );
      }

      if ( plugin_config_get ( $value ) != $actualValue )
      {
         plugin_config_set ( $value, $actualValue );
      }
   }

   /**
    * gets and returns the obsolete GET parameter
    *
    * @return bool|null
    */
   public static function getObsoleteValue ()
   {
      $obsolete = false;
      if ( $_GET[ 'obsolete' ] == 1 )
      {
         $obsolete = null;
      }

      return $obsolete;
   }

   /**
    * @author mantis bt team
    *
    * Return all versions for the specified project, including subprojects
    * @param int $p_project_id
    * @param int $p_released
    * @param bool $p_obsolete
    * @return array
    */
   public static function versionGetAllRowsWithSubsNameAsc ( $p_project_id, $p_released = null, $p_obsolete = false )
   {
      $t_project_where = helper_project_specific_where ( $p_project_id );

      $t_param_count = 0;
      $t_query_params = array ();

      if ( $p_released === null )
      {
         $t_released_where = '';
      }
      else
      {
         $c_released = db_prepare_int ( $p_released );
         $t_released_where = "AND ( released = " . db_param ( $t_param_count++ ) . " )";
         $t_query_params[] = $c_released;
      }

      if ( $p_obsolete === null )
      {
         $t_obsolete_where = '';
      }
      else
      {
         $c_obsolete = db_prepare_bool ( $p_obsolete );
         $t_obsolete_where = "AND ( obsolete = " . db_param ( $t_param_count++ ) . " )";
         $t_query_params[] = $c_obsolete;
      }

      $t_project_version_table = db_get_table ( 'mantis_project_version_table' );

      $query = "SELECT *
				  FROM $t_project_version_table
				  WHERE $t_project_where $t_released_where $t_obsolete_where
				  ORDER BY version ASC";
      $result = db_query_bound ( $query, $t_query_params );
      $count = db_num_rows ( $result );
      $rows = array ();
      for ( $i = 0; $i < $count; $i++ )
      {
         $row = db_fetch_array ( $result );
         $rows[] = $row;
      }
      return $rows;
   }
}
