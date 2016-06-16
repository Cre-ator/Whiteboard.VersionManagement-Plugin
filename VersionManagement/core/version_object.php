<?php
require_once VERSIONMANAGEMENT_CORE_URI . 'version_management_api.php';

class version_object
{
   private $mysqli;
   private $dbPath;
   private $dbUser;
   private $dbPass;
   private $dbName;

   private $version_id;

   public function __construct ( $version_id )
   {
      $this->version_id = $version_id;

      $this->dbPath = config_get ( 'hostname' );
      $this->dbUser = config_get ( 'db_username' );
      $this->dbPass = config_get ( 'db_password' );
      $this->dbName = config_get ( 'database_name' );

      $this->mysqli = new mysqli( $this->dbPath, $this->dbUser, $this->dbPass, $this->dbName );
   }

   private function get_version_table ()
   {
      if ( version_management_api::check_mantis_version_is_released () )
      {
         $version_table = db_get_table ( 'mantis_project_version_table' );
      }
      else
      {
         $version_table = db_get_table ( 'project_version' );
      }

      return $version_table;
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

   public function update_version_value ( $field, $version_value )
   {
      $old_version_value = version_get_field ( $this->version_id, $field );

      $version_table = $this->get_version_table ();

      $this->mysqli->query ( "SET SQL_SAFE_UPDATES = 0" );

      $query = "UPDATE $version_table
         SET $field = '" . $version_value . "'
         WHERE id = " . $this->version_id;
      $this->mysqli->query ( $query );

      $this->mysqli->query ( "SET SQL_SAFE_UPDATES = 1" );
   }
}