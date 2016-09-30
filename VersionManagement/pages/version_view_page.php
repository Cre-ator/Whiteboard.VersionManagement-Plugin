<?php
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmApi.php' );
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmHtmlApi.php' );
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
   $getSort = NULL;
   if ( isset( $_GET[ 'sort' ] ) )
   {
      $getSort = $_GET[ 'sort' ];
   }

   $getEdit = 0;
   if ( isset( $_GET[ "edit" ] ) )
   {
      $getEdit = $_GET[ "edit" ];
   }

   /** head */
   vmHtmlApi::htmlVersionViewHeadTable ();

   /** main */
   vmHtmlApi::htmlVersionViewMainTableOpen ();
   vmHtmlApi::htmlVersionViewMainTableHead ();
   $currentProjectId = helper_get_current_project ();
   $userId = auth_get_current_user_id ();
   $versions = vmApi::versionGetAllRowsWithSubsIndSort ( $currentProjectId, vmApi::getObsoleteValue (), $getSort );
   $pluginReadAccessLevel = plugin_config_get ( 'r_access_level' );
   $pluginWriteAccessLevel = plugin_config_get ( 'access_level' );
   $showFootTable = FALSE;
   if ( 0 != count ( $versions ) )
   {
      foreach ( $versions as $versionArray )
      {
         $version = new vmVersion( $versionArray[ 'id' ] );
         $versionProjectId = $version->getProjectId ();
         $versionAccessLevel = vmApi::getProjectUserAccessLevel ( $versionProjectId, $userId );
         if ( ( $versionAccessLevel >= $pluginReadAccessLevel ) || ( user_is_administrator ( $userId ) ) )
         {
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
            if ( ( $versionAccessLevel >= $pluginWriteAccessLevel ) || ( user_is_administrator ( $userId ) ) )
            {
               vmHtmlApi::htmlVersionViewActionColumn ( $version );
               $showFootTable = TRUE;
            }
            else
            {
               echo '<td></td>';
            }
            echo '</tr>';
         }
      }
   }
   else
   {
      $versionAccessLevel = vmApi::getProjectUserAccessLevel ( $currentProjectId, $userId );
      if ( ( $versionAccessLevel >= $pluginWriteAccessLevel ) || ( user_is_administrator ( $userId ) ) )
      {
         $showFootTable = TRUE;
      }
   }
   echo '</table>';

   /** foot */
   if ( $showFootTable )
   {
      vmHtmlApi::htmlVersionViewFootTable ();
   }
   if ( $getEdit == 1 )
   {
      echo '</form>';
   }
}
