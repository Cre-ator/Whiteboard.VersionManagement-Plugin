<?php

/**
 * Class version_management_api
 *
 * Contains functions for the plugin specific content
 */
class version_management_api
{
   public static function check_specmanagement_is_installed ()
   {
      return plugin_is_installed ( 'SpecManagement' ) && file_exists ( config_get_global ( 'plugin_path' ) . 'SpecManagement' );
   }

   public static function check_mantis_version_is_released ()
   {
      return substr ( MANTIS_VERSION, 0, 4 ) == '1.2.';
   }

   /**
    * returns true, if there is a duplicate entry.
    *
    * @param $array
    * @return bool
    */
   public static function check_array_for_duplicates ( $array )
   {
      return count ( $array ) !== count ( array_unique ( $array ) );
   }
}
