<?php
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmApi.php' );
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmHtmlApi.php' );
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmVersionManager.php' );
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmVersion.php' );

processPage ();

function processPage ()
{
   # print page top
   html_page_top1 ( plugin_lang_get ( 'menu_title' ) );
   vmHtmlApi::htmlInitializeRessources ();
   html_page_top2 ();

   # print whiteboard menu bar
   vmHtmlApi::htmlPluginTriggerWhiteboardMenu ();

   echo '<div align="center">';
   processContent ();
   echo '</div>';

   # print page bottom
   html_page_bottom ();
}

function processContent ()
{
   /** head */
   vmHtmlApi::htmlVersionViewHeadTable ();

   /** main */
   vmHtmlApi::htmlVersionViewMainTableOpen ();
   vmHtmlApi::htmlVersionViewMainTableHead ();
   $currentProjectId = helper_get_current_project ();
   $versions = version_get_all_rows_with_subs ( $currentProjectId, null, vmApi::getObsoleteValue () );

   foreach ( $versions as $versionArray )
   {
      $version = new vmVersion( $versionArray[ 'id' ] );
      vmHtmlApi::htmlVersionViewRowOpen ( $version );
      vmHtmlApi::htmlVersionViewNameColumn ( $version );
      vmHtmlApi::htmlVersionViewReleasedColumn ( $version );
      vmHtmlApi::htmlVersionViewObsoleteColumn ( $version );
      vmHtmlApi::htmlVersionViewDateOrderColumn ( $version );
      vmHtmlApi::htmlVersionViewDescriptionColumn ( $version );
      if ( vmApi::checkDMManagementPluginIsInstalled () )
      {
         vmHtmlApi::htmlVersionViewDocumentTypeColumn ( $version );
      }
      vmHtmlApi::htmlVersionViewActionColumn ( $version );
      echo '</tr>';
   }
   echo '</table>';

   /** foot */
   vmHtmlApi::htmlVersionViewFootTable ();
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '</form>';
   }
}