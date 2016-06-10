<?php

class version_object
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
      $version_date_order = version_get_field ( $this->version_id, 'date_order' );
      return date_is_null ( $version_date_order ) ? '' : string_attribute ( date ( config_get ( 'calendar_date_format' ), $version_date_order ) );
   }

   public function get_version_description ()
   {
      return version_get_field ( $this->version_id, 'description' );
   }

   public function get_version_project_id ()
   {
      return version_get_field ( $this->version_id, 'project_id' );
   }

   public function delete_version ()
   {
      version_remove ( $this->version_id );
   }
}