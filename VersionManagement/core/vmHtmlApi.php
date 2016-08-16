<?php
require_once ( __DIR__ . '/vmApi.php' );

/**
 * provides methods for html output
 */
class vmHtmlApi
{
   /**
    * Prints a table row in the plugin config area
    */
   public static function htmlConfigTableRow ()
   {
      if ( vmApi::checkMantisIsDeprecated () )
      {
         echo '<tr ' . helper_alternate_class () . '>';
      }
      else
      {
         echo '<tr>';
      }
   }

   /**
    * triggers whiteboard menu if installed
    */
   public static function htmlPluginTriggerWhiteboardMenu ()
   {
      if ( plugin_is_installed ( 'WhiteboardMenu' ) &&
         file_exists ( config_get_global ( 'plugin_path' ) . 'WhiteboardMenu' )
      )
      {
         require_once __DIR__ . '/../../WhiteboardMenu/core/whiteboard_print_api.php';
         whiteboard_print_api::printWhiteboardMenu ();
      }
   }

   /**
    * prints initial ressources for the page
    */
   public static function htmlInitializeRessources ()
   {
      echo '<script type="text/javascript" src="plugins/VersionManagement/files/version_management.js"></script>';
      echo '<link rel="stylesheet" href="plugins/VersionManagement/files/version_management.css"/>';
   }

   /**
    * Prints a category column in the plugin config area
    *
    * @param $colspan
    * @param $rowspan
    * @param $langString
    */
   public static function htmlConfigCategoryColumn ( $colspan, $rowspan, $langString )
   {
      echo '<td class="category" colspan="' . $colspan . '" rowspan="' . $rowspan . '">';
      echo plugin_lang_get ( $langString );
      echo '</td>';
   }

   /**
    * Prints a title row in the plugin config area
    *
    * @param $colspan
    * @param $langString
    */
   public static function htmlConfigTableTitleRow ( $colspan, $langString )
   {
      echo '<tr>';
      echo '<td class="form-title" colspan="' . $colspan . '">';
      echo plugin_lang_get ( $langString );
      echo '</td>';
      echo '</tr>';
   }

   /**
    * Prints a radio button element in the plugin config area
    *
    * @param $colspan
    * @param $name
    */
   public static function htmlConfigRadioButton ( $colspan, $name )
   {
      echo '<td width="100px" colspan="' . $colspan . '">';
      echo '<label>';
      echo '<input type="radio" name="' . $name . '" value="1"';
      echo ( ON == plugin_config_get ( $name ) ) ? 'checked="checked"' : '';
      echo '/>' . lang_get ( 'yes' );
      echo '</label>';
      echo '<label>';
      echo '<input type="radio" name="' . $name . '" value="0"';
      echo ( OFF == plugin_config_get ( $name ) ) ? 'checked="checked"' : '';
      echo '/>' . lang_get ( 'no' );
      echo '</label>';
      echo '</td>';
   }

   /**
    * Prints a color picker element in the plugin config area
    *
    * @param $colspan
    * @param $name
    * @param $default
    */
   public static function htmlConfigColorPicker ( $colspan, $name, $default )
   {
      echo '<td width="100px" colspan="' . $colspan . '">';
      echo '<label>';
      echo '<input class="color {pickerFace:4,pickerClosable:true}" type="text" name="' . $name . '" value="' . plugin_config_get ( $name, $default ) . '" />';
      echo '</label>';
      echo '</td>';
   }

   /**
    * prints version name column
    *
    * @param vmVersion $version
    */
   public static function htmlVersionViewNameColumn ( vmVersion $version )
   {
      echo '<td>';
      echo '<input type="hidden" name="version_id[]" value="' . $version->getVersionId () . '"/>';
      if ( $_GET[ "edit" ] == 1 )
      {
         echo '<label for="proj-version-new-version">';
         echo '<span class="input" style="width:100%;">';
         echo '<input type="text" id="proj-version-new-version" name="version_name[]" 
         style="width:100%;" maxlength="64" value="' .
            string_attribute ( $version->getVersionName () ) . '" />';
         echo '</span>';
         echo '</label>';
      }
      else
      {
         if ( helper_get_current_project () != $version->getProjectId () )
         {
            echo '[' . project_get_name ( $version->getProjectId () ) . ']&nbsp;' . $version->getVersionName ();
         }
         else
         {
            echo $version->getVersionName ();
         }
      }
      echo '</td>';
   }

   /**
    * prints version released status column
    *
    * @param vmVersion $version
    */
   public static function htmlVersionViewReleasedColumn ( vmVersion $version )
   {
      echo '<td>';
      if ( $_GET[ "edit" ] == 1 )
      {
         echo '<span class="checkbox">';
         echo '<input type="checkbox" id="proj-version-released" name="version_released[]" value="' .
            $version->getVersionId () . '"';
         check_checked ( $version->getReleased (), ON );
         echo '/>';
         echo '</span>';
      }
      else
      {
         echo trans_bool ( $version->getReleased () );
      }
      echo '</td>';
   }

   /**
    * prints version obsolete status column
    *
    * @param vmVersion $version
    */
   public static function htmlVersionViewObsoleteColumn ( vmVersion $version )
   {
      echo '<td>';
      if ( $_GET[ "edit" ] == 1 )
      {
         echo '<span class="checkbox">';
         echo '<input type="checkbox" id="proj-version-obsolete" name="version_obsolete[]" value="' .
            $version->getVersionId () . '"';
         check_checked ( $version->getObsolete (), ON );
         echo '/>';
         echo '</span>';
      }
      else
      {
         echo trans_bool ( $version->getObsolete () );
      }
      echo '</td>';
   }

   /**
    * prints version date order column
    *
    * @param vmVersion $version
    */
   public static function htmlVersionViewDateOrderColumn ( vmVersion $version )
   {
      echo '<td>';
      if ( $_GET[ "edit" ] == 1 )
      {
         echo '<label for="proj-version-date-order">';
         echo '<span class="input">';
         echo '<input type="text" id="proj-version-date-order" name="version_date_order[]"
         class="datetime" size="15" value="' .
            ( date_is_null ( $version->getDateOrder () ) ?
               '' : string_attribute ( date ( config_get ( 'calendar_date_format' ), $version->getDateOrder () ) ) ) . '" />';
         echo '</span>';
         echo '</label>';
      }
      else
      {
         echo date_is_null ( $version->getDateOrder () ) ?
            '' : string_attribute ( date ( config_get ( 'calendar_date_format' ), $version->getDateOrder () ) );
      }
      echo '</td>';
   }

   /**
    * prints version description column
    *
    * @param vmVersion $version
    */
   public static function htmlVersionViewDescriptionColumn ( vmVersion $version )
   {
      echo '<td width="100">';
      if ( $_GET[ "edit" ] == 1 )
      {
         echo '<span class="text">';
         echo '<input type="text" id="proj-version-description" name="version_description[]" value="' .
            string_attribute ( $version->getDescription () ) . '"/>';
         echo '</span>';
      }
      else
      {
         echo string_display ( $version->getDescription () );
      }
      echo '</td>';
   }

   /**
    * prints document type column when document management plugin is installed
    *
    * @param vmVersion $version
    */
   public static function htmlVersionViewDocumentTypeColumn ( vmVersion $version )
   {
      require_once ( __DIR__ . '/../../SpecManagement/core/specmanagement_database_api.php' );
      $dManagementDbApi = new specmanagement_database_api();

      $typeId = $dManagementDbApi->get_type_by_version ( $version->getVersionId () );
      $type = $dManagementDbApi->get_type_string ( $typeId );

      echo '<td>';
      if ( $_GET[ "edit" ] == 1 )
      {
         echo '<span class="select"><select ' . helper_get_tab_index () . ' id="proj-version-type" name="version-type[]">';
         echo '<option value=""></option>';
         $rypeRows = $dManagementDbApi->get_full_types ();
         foreach ( $rypeRows as $rypeRow )
         {
            $availableType = $rypeRow[ 1 ];
            echo '<option value="' . $availableType . '"';
            check_selected ( string_attribute ( $type ), $availableType );
            echo '>' . $availableType . '</option>';
         }
         echo '</select></span>';
      }
      else
      {
         echo string_display ( $type );
      }
      echo '</td>';
   }

   /**
    * prints action column
    *
    * @param vmVersion $version
    */
   public static function htmlVersionViewActionColumn ( vmVersion $version )
   {
      if ( $_GET[ "edit" ] == 0 )
      {
         echo '<td>';
         echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_delete' ) .
            '&amp;version_id=' . $version->getVersionId () . '">';
         echo '<span class="input">';
         echo '<input type="button" value="' . lang_get ( 'delete_link' ) . '" />';
         echo '</span>';
         echo '</a>';
         echo '</td>';
      }
   }

   /**
    * prints closing table tags
    */
   public static function htmlVersionViewTableClose ()
   {
      echo '</table>';
      if ( !vmApi::checkMantisIsDeprecated () )
      {
         echo '</div>';
      }
   }

   /**
    * prints the opening tags for a version row
    *
    * @param vmVersion $version
    */
   public static function htmlVersionViewRowOpen ( vmVersion $version )
   {
      if ( !$version->isVersionIsUsed () )
      {
         echo '<tr style="background-color: ' . plugin_config_get ( 'unused_version_row_color' ) . '">';
      }
      else
      {
         if ( vmApi::checkMantisIsDeprecated () )
         {
            echo '<tr ' . helper_alternate_class () . '>';
         }
         else
         {
            echo '<tr>';
         }
      }
   }

   /**
    * prints the foot table of the version view page
    */
   public static function htmlVersionViewFootTable ()
   {
      self::htmlVersionViewTableOpen ();
      echo '<tbody>';
      echo '<tr>';
      if ( $_GET[ "edit" ] == 1 )
      {
         $currentProjectId = helper_get_current_project ();
         $versions = version_get_all_rows_with_subs ( $currentProjectId, null, vmApi::getObsoleteValue () );
         $initialRowCount = ( count ( $versions ) );

         echo '<td class="left">';
         echo '<input type="button" value="+" onclick="add_version_row()" />&nbsp;';
         echo '<input type="button" value="-" onclick="del_version_row(' . $initialRowCount . ')" />&nbsp;';
         echo '</td>';
      }

      echo '<td colspan="5" class="center">';
      if ( $_GET[ "edit" ] == 1 )
      {
         echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_update' ) . '&amp;edit=1&amp;obsolete=' . $_GET[ 'obsolete' ] . '">';
         echo '<span class="input">';
         echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_foot_edit_done' ) . '" />';
         echo '</span>';
         echo '</a>';
      }
      else
      {
         echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=1&amp;obsolete=' . $_GET[ 'obsolete' ] . '">';
         echo '<span class="input">';
         echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_foot_edit' ) . '" />';
         echo '</span>';
         echo '</a>';
      }
      echo '</td>';
      echo '</tr>';
      echo '</tbody>';
      self::htmlVersionViewTableClose ();
   }

   /**
    * checks the mantis version and prints the opening table tags
    */
   public static function htmlVersionViewTableOpen ()
   {
      if ( vmApi::checkMantisIsDeprecated () )
      {
         echo '<table class="width100">';
      }
      else
      {
         echo '<div class="table-container">';
         echo '<table>';
      }
   }

   /**
    * prints the opening tags for the main table in version view page
    */
   public static function htmlVersionViewMainTableOpen ()
   {
      if ( $_GET[ "edit" ] == 1 )
      {
         echo '<form action="' . plugin_page ( 'version_view_update' ) . '" method="post">';
      }
      if ( vmApi::checkMantisIsDeprecated () )
      {
         echo '<table id="version_view" class="width100">';
      }
      else
      {
         echo '<div class="table-container">';
         echo '<table id="version_view">';
      }
   }

   /**
    * prints the head table in the version view page
    */
   public static function htmlVersionViewHeadTable ()
   {
      self::htmlVersionViewTableOpen ();
      echo '<thead>';
      echo '<tr>';
      echo '<td class="form-title">';
      echo plugin_lang_get ( 'version_view_title' );
      echo '</td>';
      echo '<td class="right">';
      if ( $_GET[ "obsolete" ] == 1 )
      {
         echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=' . $_GET[ "edit" ] . '&amp;obsolete=0">';
         echo '<span class="input">';
         echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_head_hide_obsolete' ) . '" />';
      }
      else
      {
         echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=' . $_GET[ "edit" ] . '&amp;obsolete=1">';
         echo '<span class="input">';
         echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_head_show_obsolete' ) . '" />';
      }
      echo '</span>';
      echo '</a>';
      echo '</td>';
      echo '</tr>';
      echo '</thead>';
      self::htmlVersionViewTableClose ();
   }

   /**
    * prints the head area of the main table
    */
   public static function htmlVersionViewMainTableHead ()
   {
      echo '<thead>';
      echo '<tr class="row-category2">';
      echo '<th>' . plugin_lang_get ( 'version_view_table_head_version' ) . '</th>';
      echo '<th>' . plugin_lang_get ( 'version_view_table_head_released' ) . '</th>';
      echo '<th>' . plugin_lang_get ( 'version_view_table_head_obsolete' ) . '</th>';
      echo '<th>' . plugin_lang_get ( 'version_view_table_head_date' ) . '</th>';
      echo '<th>' . plugin_lang_get ( 'version_view_table_head_description' ) . '</th>';
      if ( vmApi::checkDMManagementPluginIsInstalled () )
      {
         echo '<th>' . plugin_lang_get ( 'version_view_table_head_type' ) . '</th>';
      }
      if ( $_GET[ "edit" ] == 0 )
      {
         echo '<th>' . plugin_lang_get ( 'version_view_table_head_action' ) . '</th>';
      }
      echo '</tr>';
      echo '</thead>';
   }

   /** effort management */

   /**
    * @param $content
    */
   public static function htmlEffortViewColumn ( $content )
   {
      echo '<div class="td">' . $content . '</div>';
   }

   public static function htmlEffoertViewHeadRow ()
   {
      echo '<div class="tr">';
      echo '<div class="td">' . lang_get ( 'version' ) . '</div>';
      echo '<div class="td">' . plugin_lang_get ( 'effort_view_scheduled' ) . '</div>';
      echo '<div class="td">' . plugin_lang_get ( 'effort_view_issuecount' ) . '</div>';
      echo '<div class="td">&#931</div>'; # &#931 Summenzeichen
      echo '<div class="td">' . plugin_lang_get ( 'effort_view_progress' ) . '</div>';
      echo '<div class="td">' . plugin_lang_get ( 'effort_view_remaintime' ) . '</div>';
      echo '<div class="td">' . plugin_lang_get ( 'effort_view_uncertainty' ) . '</div>';
      echo '</div>';
   }
}