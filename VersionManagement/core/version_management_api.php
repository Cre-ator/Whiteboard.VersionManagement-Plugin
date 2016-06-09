<?php
/**
 * Created by PhpStorm.
 * User: stefan.schwarz
 * Date: 09.06.2016
 * Time: 13:16
 */

function check_specmanagement_is_installed ()
{
   return plugin_is_installed ( 'SpecManagement' ) && file_exists ( config_get_global ( 'plugin_path' ) . 'SpecManagement' );
}

function check_mantis_version_is_released ()
{
   return substr ( MANTIS_VERSION, 0, 4 ) == '1.2.';
}