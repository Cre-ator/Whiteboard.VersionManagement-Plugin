<?php

require_once VERSIONMANAGEMENT_CORE_URI . 'version_processor.php';
require_once VERSIONMANAGEMENT_CORE_URI . 'project_processor.php';

$version_view_edit = $_GET[ "edit" ];
process_page ();

function process_page ()
{
   html_page_top1 ( plugin_lang_get ( 'menu_title' ) );
   html_page_top2 ();

   if ( plugin_is_installed ( 'WhiteboardMenu' ) && file_exists ( config_get_global ( 'plugin_path' ) . 'WhiteboardMenu' ) )
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
   print_table_body ();
   close_table ();
}

function open_table ()
{
   if ( substr ( MANTIS_VERSION, 0, 4 ) == '1.2.' )
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
   $versions = version_get_all_rows_with_subs ( $current_project_id, null, null );

   $index = 1;
   foreach ( $versions as $version )
   {
      $version_proc = new version_processor( $version[ 'id' ] );
      printRow ();
      print_version_name_column ( $version_proc );
      print_version_released_column ( $version_proc, $index );
      print_version_obsolete_column ( $version_proc, $index );
      print_version_date_order_column ( $version_proc );
      print_version_description_column ( $version_proc );
      print_version_action_column ( $version_proc );
      $index++;
   }
   echo '</tr>';
   print_foot_row ();

   echo '</tbody>';
}

function print_version_name_column ( version_processor $version_proc )
{
   echo '<td>';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<label for="proj-version-new-version">';
      echo '<span class="input" style="width:100%;">';
      echo '<input type="text" id="proj-version-new-version" name="version[]"
                      style="width:100%;" maxlength="64" value="' . string_attribute ( $version_proc->get_version_name () ) . '" />';
      echo '</span>';
      echo '</label>';
   }
   else
   {
      echo $version_proc->get_version_name ();
   }
   echo '</td>';
}

function print_version_released_column ( version_processor $version_proc, $index )
{
   echo '<td>';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<label for="proj-version-released-' . $index . '">';
      echo '<span class="checkbox">';
      echo '<input type="checkbox" id="proj-version-released-' . $index . '" name="released-' . $index . '"';
      check_checked ( (int) $version_proc->get_version_released (), ON );
      echo '/>';
      echo '</span>';
      echo '</label>';
   }
   else
   {
      echo trans_bool ( $version_proc->get_version_released () );
   }
   echo '</td>';
}

function print_version_obsolete_column ( version_processor $version_proc, $index )
{
   echo '<td>';
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<label for="proj-version-obsolete-' . $index . '">';
      echo '<span class="checkbox">';
      echo '<input type="checkbox" id="proj-version-obsolete-' . $index . '" name="obsolete-' . $index . '"';
      check_checked ( (int) $version_proc->get_version_obsolete (), ON );
      echo '/>';
      echo '</span>';
      echo '</label>';
   }
   else
   {
      echo trans_bool ( $version_proc->get_version_obsolete () );
   }
   echo '</td>';
}

function print_version_date_order_column ( version_processor $version_proc )
{
   echo '<td>' . $version_proc->get_version_date_order () . '</td>';
}

function print_version_description_column ( version_processor $version_proc )
{
   echo '<td>' . $version_proc->get_version_description () . '</td>';
}

function print_version_action_column ( version_processor $version_proc )
{
   $version_id = $version_proc->get_version_id ();
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '<td>button folgt</td>';
   }
}

function close_table ()
{
   echo '</table>';
   if ( substr ( MANTIS_VERSION, 0, 4 ) != '1.2.' )
   {
      echo '</div>';
   }
}

function printRow ()
{
   if ( substr ( MANTIS_VERSION, 0, 4 ) == '1.2.' )
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
   echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view' ) . '&amp;edit=1">';
   echo '<span class="input">';
   echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_foot_edit' ) . '">';
   echo '</span>';
   echo '</a>';
   echo '</td>';
   echo '</tr>';
}
