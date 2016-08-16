<?php
require_once ( __DIR__ . '/../core/vmApi.php' );
require_once ( __DIR__ . '/../core/vmHtmlApi.php' );
require_once ( __DIR__ . '/../core/vmVersionManager.php' );
require_once ( __DIR__ . '/../core/vmVersion.php' );

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
   echo '<hr size="1" width="100%" />';
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
   echo '<tbody>';
   generateContent ();
   echo '</tbody>';
   vmHtmlApi::htmlVersionViewTableClose ();

   /** foot */
   vmHtmlApi::htmlVersionViewFootTable ();
   if ( $_GET[ "edit" ] == 1 )
   {
      echo '</form>';
   }
}

function generateContent ()
{
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
}