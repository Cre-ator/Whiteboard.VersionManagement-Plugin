<?php

require_once ( __DIR__ . '/../core/version_management_api.php' );
require_once ( __DIR__ . '/../core/vmVersion.php' );

process_page ();

function process_page ()
{

   html_page_top1 ( plugin_lang_get ( 'menu_title' ) );
   echo '<script type="text/javascript" src="plugins/VersionManagement/files/version_management.js"></script>';
   echo '<link rel="stylesheet" href="plugins/VersionManagement/files/version_management.css"/>';
   html_page_top2 ();

   if ( plugin_is_installed ( 'WhiteboardMenu' ) &&
      file_exists ( config_get_global ( 'plugin_path' ) . 'WhiteboardMenu' )
   )
   {
      require_once __DIR__ . '/../../WhiteboardMenu/core/whiteboard_print_api.php';
      whiteboard_print_api::printWhiteboardMenu ();
   }

   echo '<div align="center">';
   echo '<hr size="1" width="100%" />';

   processContent ();

   echo '</div>';
   html_page_bottom ();
}

function processContent ()
{
   /** head */
   printHeadTable ();

   /** main */
   mainTableOpen ();
   printTableHead ();
   printTableBody ();
   tableClose ();

   /** foot */
   printFootTable ();
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '</form>';
   }
}

function tableOpen ()
{
   if ( version_management_api::check_mantis_version_is_released () )
   {
      echo '<table class="width100">';
   }
   else
   {
      echo '<div class="table-container">';
      echo '<table>';
   }
}

function mainTableOpen ()
{
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<form action="' . plugin_page ( 'version_view_update' ) . '" method="post">';
   }
   if ( version_management_api::check_mantis_version_is_released () )
   {
      echo '<table id="version_view" class="width100">';
   }
   else
   {
      echo '<div class="table-container">';
      echo '<table id="version_view">';
   }
}

function printHeadTable ()
{
   tableOpen ();
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
   tableClose ();
}

function printTableHead ()
{
   echo '<thead>';
   echo '<tr class="row-category2">';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_version' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_released' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_obsolete' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_date' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_description' ) . '</th>';
   if ( version_management_api::check_document_management_is_installed () )
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

function printTableBody ()
{
   $current_project_id = helper_get_current_project ();
   $versions = version_get_all_rows_with_subs ( $current_project_id, null, getObsoleteValue () );

   echo '<tbody>';
   foreach ( $versions as $version_array )
   {
      $version = new vmVersion( $version_array[ 'id' ] );
      printRow ( $version );
      printNameColumn ( $version );
      printReleasedColumn ( $version );
      printObsoleteColumn ( $version );
      printDateOrderColumn ( $version );
      printDescriptionColumn ( $version );
      if ( version_management_api::check_document_management_is_installed () )
      {
         printDocumentTypeColumn ( $version );
      }
      printActionColumn ( $version );
      echo '</tr>';
   }
   echo '</tbody>';
}

function printNameColumn ( vmVersion $version )
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
      echo $version->getVersionName ();
   }
   echo '</td>';
}

function printReleasedColumn ( vmVersion $version )
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

function printObsoleteColumn ( vmVersion $version )
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

function printDateOrderColumn ( vmVersion $version )
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

function printDescriptionColumn ( vmVersion $version )
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

function printDocumentTypeColumn ( vmVersion $version )
{
   require_once ( __DIR__ . '/../../SpecManagement/core/specmanagement_database_api.php' );
   $specmanagement_database_api = new specmanagement_database_api();

   $type_id = $specmanagement_database_api->get_type_by_version ( $version->getVersionId () );
   $current_type = $specmanagement_database_api->get_type_string ( $type_id );

   echo '<td>';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<span class="select"><select ' . helper_get_tab_index () . ' id="proj-version-type" name="version-type[]">';
      echo '<option value=""></option>';
      $types_rows = $specmanagement_database_api->get_full_types ();
      foreach ( $types_rows as $types_row )
      {
         $available_type = $types_row[ 1 ];
         echo '<option value="' . $available_type . '"';
         check_selected ( string_attribute ( $current_type ), $available_type );
         echo '>' . $available_type . '</option>';
      }
      echo '</select></span>';
   }
   else
   {
      echo string_display ( $current_type );
   }
   echo '</td>';
}

function printActionColumn ( vmVersion $version )
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

function tableClose ()
{
   echo '</table>';
   if ( !version_management_api::check_mantis_version_is_released () )
   {
      echo '</div>';
   }
}

function printRow ( vmVersion $version )
{
   if ( !$version->checkVersionIsUsed () )
   {
      echo '<tr style="background-color: ' . plugin_config_get ( 'unused_version_row_color' ) . '">';
   }
   else
   {
      if ( version_management_api::check_mantis_version_is_released () )
      {
         echo '<tr ' . helper_alternate_class () . '>';
      }
      else
      {
         echo '<tr>';
      }
   }
}

function printFootTable ()
{
   tableOpen ();
   echo '<tbody>';
   echo '<tr>';
   if ( $_GET[ "edit" ] == 1 )
   {
      $current_project_id = helper_get_current_project ();
      $versions = version_get_all_rows_with_subs ( $current_project_id, null, getObsoleteValue () );
      $initial_row_count = ( count ( $versions ) );

      echo '<td class="left">';
      echo '<input type="button" value="+" onclick="add_version_row()" />&nbsp;';
      echo '<input type="button" value="-" onclick="del_version_row(' . $initial_row_count . ')" />&nbsp;';
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
   tableClose ();
}

function getObsoleteValue ()
{
   $obsolete = false;
   if ( $_GET[ 'obsolete' ] == 1 )
   {
      $obsolete = null;
   }

   return $obsolete;
}
