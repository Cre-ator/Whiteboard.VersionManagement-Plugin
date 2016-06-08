<?php

/**
 * Created by PhpStorm.
 * User: schwarz
 * Date: 03.06.2016
 * Time: 16:27
 */
class version_processor
{
   private $version_id;

   public function __construct ( $version_id )
   {
      $this->version_id = $version_id;
   }

   public function get_version_id ()
   {
      return $this->version_id;
   }

   public function get_version_name ()
   {
      return version_get_field ( $this->version_id, 'version' );
   }

   public function get_version_full_name ()
   {
      return version_full_name ( $this->version_id );
   }

   public function get_version_released ()
   {
      return version_get_field ( $this->version_id, 'released' );
   }

   public function get_version_obsolete ()
   {
      return version_get_field ( $this->version_id, 'obsolete' );
   }

   public function get_version_date_order ()
   {
      $version = version_get ( $this->version_id );
      return date_is_null ( $version->date_order ) ? '' : string_attribute ( date ( config_get ( 'calendar_date_format' ), $version->date_order ) );
   }

   public function get_version_description ()
   {
      return version_get_field ( $this->version_id, 'description' );
   }
}