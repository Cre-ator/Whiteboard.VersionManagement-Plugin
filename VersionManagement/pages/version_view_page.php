<?php

require_once VERSIONMANAGEMENT_CORE_URI . 'version_management_api.php';
require_once VERSIONMANAGEMENT_CORE_URI . 'version_object.php';

process_page ();

function process_page ()
{
   html_page_top1 ( plugin_lang_get ( 'menu_title' ) );
   html_page_top2 ();

   if ( plugin_is_installed ( 'WhiteboardMenu' ) &&
      file_exists ( config_get_global ( 'plugin_path' ) . 'WhiteboardMenu' )
   )
   {
      require_once WHITEBOARDMENU_CORE_URI . 'whiteboard_print_api.php';
      $whiteboard_print_api = new whiteboard_print_api();
      $whiteboard_print_api->printWhiteboardMenu ();
   }

   echo '<div align="center">';
   echo '<hr size="1" width="100%" />';

   process_table ();
}

function process_table ()
{
   open_table ();
   print_table_head ();
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<form action="' . plugin_page ( 'version_view_update' ) . '" method="post">';
   }
   print_table_body ();
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '</form>';
   }
   close_table ();
}

function open_table ()
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

function print_table_head ()
{
   echo '<thead>';

   echo '<tr>';
   echo '<td class="form-title" colspan="4">';
   echo plugin_lang_get ( 'version_view_title' );
   echo '</td>';
   echo '<td>';
   if ( $_GET[ "obsolete" ] == 1 )
   {
      echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=' . $_GET[ "edit" ] . '&amp;obsolete=0">';
      echo '<span class="input">';
      echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_head_hide_obsolete' ) . '">';
   }
   else
   {
      echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=' . $_GET[ "edit" ] . '&amp;obsolete=1">';
      echo '<span class="input">';
      echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_head_show_obsolete' ) . '">';
   }
   echo '</span>';
   echo '</a>';
   echo '</td>';
   echo '</tr>';

   echo '<tr class="row-category2">';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_version' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_released' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_obsolete' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_date' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_description' ) . '</th>';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<th>' . plugin_lang_get ( 'version_view_table_head_action' ) . '</th>';
   }
   echo '</tr>';
   echo '</thead>';
}

function print_table_body ()
{
   echo '<tbody>';

   $current_project_id = helper_get_current_project ();
   $versions = version_get_all_rows_with_subs ( $current_project_id, null, get_obsolete_value () );

   foreach ( $versions as $version )
   {
      $version_object = new version_object( $version[ 'id' ] );
      printRow ();
      echo '<input type="hidden" name="version_id[]" value="' . $version[ 'id' ] . '"/>';
      print_version_name_column ( $version_object );
      print_version_released_column ( $version_object );
      print_version_obsolete_column ( $version_object );
      print_version_date_order_column ( $version_object );
      print_version_description_column ( $version_object );
      print_version_action_column ( $version_object );
      echo '</tr>';
   }
   print_foot_row ();

   echo '</tbody>';
}

function print_version_name_column ( version_object $version_object )
{
   echo '<td>';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<label for="proj-version-new-version">';
      echo '<span class="input" style="width:100%;">';
      echo '<input type="text" id="proj-version-new-version" name="version_name[]" 
         style="width:100%;" maxlength="64" value="' .
         string_attribute ( $version_object->get_version_name () ) . '" />';
      echo '</span>';
      echo '</label>';
   }
   else
   {
      echo $version_object->get_version_name ();
   }
   echo '</td>';
}

function print_version_released_column ( version_object $version_object )
{
   echo '<td>';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<span class="checkbox">';
      echo '<input type="checkbox" id="proj-version-released" name="version_released[]" value="' .
         $version_object->get_version_id () . '"';
      check_checked ( $version_object->get_version_released (), ON );
      echo '/>';
      echo '</span>';
   }
   else
   {
      echo trans_bool ( $version_object->get_version_released () );
   }
   echo '</td>';
}

function print_version_obsolete_column ( version_object $version_object )
{
   echo '<td>';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<span class="checkbox">';
      echo '<input type="checkbox" id="proj-version-obsolete" name="version_obsolete[]" value="' .
         $version_object->get_version_id () . '"';
      check_checked ( $version_object->get_version_obsolete (), ON );
      echo '/>';
      echo '</span>';
   }
   else
   {
      echo trans_bool ( $version_object->get_version_obsolete () );
   }
   echo '</td>';
}

function print_version_date_order_column ( version_object $version_object )
{
   echo '<td>';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<label for="proj-version-date-order">';
      echo '<span class="input">';
      echo '<input type="text" id="proj-version-date-order" name="version_date_order[]"
         class="datetime" size="15" value="' .
         $version_object->get_version_date_order () . '" />';
      echo '</span>';
      echo '</label>';
   }
   else
   {
      echo $version_object->get_version_date_order ();
   }
   echo '</td>';
}

function print_version_description_column ( version_object $version_object )
{
   echo '<td width="100">';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<span class="text">';
      echo '<input type="text" id="proj-version-description" name="version_description[]" value="' .
         string_attribute ( $version_object->get_version_description () ) . '"/>';
      echo '</span>';
   }
   else
   {
      echo string_display ( $version_object->get_version_description () );
   }
   echo '</td>';
}

function print_version_action_column ( version_object $version_object )
{
   $version_id = $version_object->get_version_id ();
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<td>';
      echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_delete' ) .
         '&amp;version_id=' . $version_id . '">';
      echo '<span class="input">';
      echo '<input type="button" value="' . lang_get ( 'delete_link' ) . '" />';
      echo '</span>';
      echo '</a>';
      echo '</td>';
   }
}

function close_table ()
{
   echo '</table>';
   if ( !version_management_api::check_mantis_version_is_released () )
   {
      echo '</div>';
   }
}

function printRow ()
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

function print_foot_row ()
{
   echo '<tr>';
   echo '<td colspan="5" class="center">';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_update' ) . '&amp;edit=1&amp;obsolete=' . $_GET[ 'obsolete' ] . '">';
      echo '<span class="input">';
      echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_foot_edit_done' ) . '">';
   }
   else
   {
      echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=1&amp;obsolete=' . $_GET[ 'obsolete' ] . '">';
      echo '<span class="input">';
      echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_foot_edit' ) . '">';
   }
   echo '</span>';
   echo '</a>';
   echo '</td>';
   echo '</tr>';
}

function get_obsolete_value ()
{
   $obsolete = false;
   if ( $_GET[ 'obsolete' ] == 1 )
   {
      $obsolete = null;
   }

   return $obsolete;
}
