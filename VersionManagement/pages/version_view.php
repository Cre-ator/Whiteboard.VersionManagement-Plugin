<?php

require_once VERSIONMANAGEMENT_CORE_URI . 'version_processor.php';
require_once VERSIONMANAGEMENT_CORE_URI . 'project_processor.php';

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
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_version' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_released' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_obsolete' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_date' ) . '</th>';
   echo '<th>' . plugin_lang_get ( 'version_view_table_head_description' ) . '</th>';
   echo '</tr>';
   echo '</thead>';
}

function print_table_body ()
{
   echo '<tbody>';

   $current_project_id = helper_get_current_project ();
   $relevant_project_ids = project_hierarchy_get_all_subprojects ( $current_project_id );
   array_push ( $relevant_project_ids, $current_project_id );

   foreach ( $relevant_project_ids as $relevant_project_id )
   {
      $project_proc = new project_processor( $relevant_project_id );
      $relevant_versions = $project_proc->get_project_assigned_versions ();

      foreach ( $relevant_versions as $relevant_version )
      {
         $version_prov = new version_processor( $relevant_version[ 'id' ] );
         echo '<tr>';
         echo '<td>' . $version_prov->get_version_name () . '</td>';
         echo '<td>' . $version_prov->get_version_released () . '</td>';
         echo '<td>' . $version_prov->get_version_obsolete () . '</td>';
         echo '<td>' . $version_prov->get_version_date_order () . '</td>';
         echo '<td>' . $version_prov->get_version_description () . '</td>';
         echo '</tr>';
      }
   }

   echo '</tbody>';
}

function close_table ()
{
   echo '</table>';
   if ( substr ( MANTIS_VERSION, 0, 4 ) != '1.2.' )
   {
      echo '</div>';
   }
}
