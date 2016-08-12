<?php

class VersionManagementPlugin extends MantisPlugin
{
   function register ()
   {
      $this->name = 'VersionManagement';
      $this->description = 'Extended view and more options for the MantisBT version management';
      $this->page = 'config_page';

      $this->version = '1.0.8';
      $this->requires = array
      (
         'MantisCore' => '1.2.0, <= 1.3.99'
      );

      $this->author = 'Stefan Schwarz';
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

   function init ()
   {
      require_once ( __DIR__ . '/core/constant_api.php' );
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

   function get_user_has_level ()
   {
      $project_id = helper_get_current_project ();
      $user_id = auth_get_current_user_id ();

      return user_get_access_level ( $user_id, $project_id ) >= plugin_config_get ( 'access_level', ADMINISTRATOR );
   }

   function footer ()
   {
      if ( plugin_config_get ( 'show_footer' ) && $this->get_user_has_level () )
      {
         return '<address>' . $this->name . '&nbsp;' . $this->version . '&nbsp;Copyright&nbsp;&copy;&nbsp;2016&nbsp;by&nbsp;' . $this->author . '</address>';
      }
      return null;
   }

   function menu ()
   {
      if ( ( !plugin_is_installed ( 'WhiteboardMenu' ) || !file_exists ( config_get_global ( 'plugin_path' ) . 'WhiteboardMenu' ) )
         && plugin_config_get ( 'show_menu' ) && $this->get_user_has_level ()
      )
      {
         return '<a href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=0&amp;obsolete=0">' . plugin_lang_get ( 'menu_title' ) . '</a >';
      }
      return null;
   }
}
