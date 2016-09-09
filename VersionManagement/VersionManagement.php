<?php

class VersionManagementPlugin extends MantisPlugin
{
   private $shortName = null;

   function register ()
   {
      $this->shortName = 'VersionManagement';
      $this->name = 'Whiteboard.' . $this->shortName;
      $this->description = 'Extended view and more options for the MantisBT version management';
      $this->page = 'config_page';

      $this->version = '1.0.21';
      $this->requires = array
      (
         'MantisCore' => '1.2.0, <= 1.3.99'
      );

      $this->author = 'cbb software GmbH (Rainer Dierck, Stefan Schwarz)';
      $this->contact = '';
      $this->url = 'https://github.com/Cre-ator';
   }

   function hooks ()
   {
      $hooks = array
      (
         'EVENT_LAYOUT_PAGE_FOOTER' => 'footer',
         'EVENT_MENU_MAIN' => 'menu'
      );
      return $hooks;
   }

   function config ()
   {
      return array
      (
         'show_menu' => ON,
         'show_footer' => ON,
         'unused_version_row_color' => '#908b2d',
         'access_level' => ADMINISTRATOR
      );
   }

   function schema ()
   {
      require_once ( __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmApi.php' );
      $tableArray = array ();

      $whiteboardMenuTable = array
      (
         'CreateTableSQL', array ( plugin_table ( 'menu', 'whiteboard' ), "
            id                   I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
            plugin_name          C(250)  DEFAULT '',
            plugin_access_level  I       UNSIGNED,
            plugin_show_menu     I       UNSIGNED,
            plugin_menu_path     C(250)  DEFAULT ''
            " )
      );

      $boolArray = vmApi::checkWhiteboardTablesExist ();
      # add whiteboardmenu table if it does not exist
      if ( !$boolArray[ 0 ] )
      {
         array_push ( $tableArray, $whiteboardMenuTable );
      }

      return $tableArray;
   }

   function checkUserHasLevel ()
   {
      $project_id = helper_get_current_project ();
      $user_id = auth_get_current_user_id ();

      return user_get_access_level ( $user_id, $project_id ) >= plugin_config_get ( 'access_level', ADMINISTRATOR );
   }

   function footer ()
   {
      if ( plugin_config_get ( 'show_footer' ) && $this->checkUserHasLevel () )
      {
         return '<address>' . $this->shortName . ' ' . $this->version . ' Copyright &copy; 2016 by ' . $this->author . '</address>';
      }
      return null;
   }

   function menu ()
   {
      require_once ( __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmApi.php' );
      if ( !vmApi::checkPluginIsRegisteredInWhiteboardMenu () )
      {
         vmApi::addPluginToWhiteboardMenu ();
      }

      if ( ( !plugin_is_installed ( 'WhiteboardMenu' ) || !file_exists ( config_get_global ( 'plugin_path' ) . 'WhiteboardMenu' ) )
         && plugin_config_get ( 'show_menu' ) && $this->checkUserHasLevel ()
      )
      {
         return '<a href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=0&amp;obsolete=0">' . plugin_lang_get ( 'menu_title' ) . '</a >';
      }
      return null;
   }

   function uninstall ()
   {
      require_once ( __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmApi.php' );
      vmApi::removePluginFromWhiteboardMenu ();
   }
}
