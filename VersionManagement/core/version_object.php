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
   private $project_id;
   private $version_name;
   private $version_description;
   private $version_date_order;
   private $version_realeased;
   private $version_obsolete;

   public function __construct ( $version_id, $project_id, $version_name, $version_description, $version_date_order, $version_realeased, $version_obsolete )
   {
      $this->version_id = $version_id;
      $this->project_id = $project_id;
      $this->version_name = $version_name;
      $this->version_description = $version_description;
      $this->version_date_order = $version_date_order;
      $this->version_realeased = $version_realeased;
      $this->version_obsolete = $version_obsolete;

      $this->dbPath = config_get ( 'hostname' );
      $this->dbUser = config_get ( 'db_username' );
      $this->dbPass = config_get ( 'db_password' );
      $this->dbName = config_get ( 'database_name' );

      $this->mysqli = new mysqli( $this->dbPath, $this->dbUser, $this->dbPass, $this->dbName );
   }

   public function get_version_id ()
   {
      return $this->version_id;
   }

   public function set_version_id ( $version_id )
   {
      $this->version_id = $version_id;
   }

   public function get_version_name ()
   {
      return $this->version_name;
   }

   public function set_version_name ( $version_name )
   {
      $this->version_name = $version_name;
   }

   public function get_version_released ()
   {
      return $this->version_realeased;
   }

   public function set_version_released ( $version_released )
   {
      $this->version_released = $version_released;
   }

   public function get_version_obsolete ()
   {
      return $this->version_obsolete;
   }

   public function set_version_obsolete ( $version_obsolete )
   {
      $this->version_obsolete = $version_obsolete;
   }

   public function get_version_date_order ()
   {
      return $this->version_date_order;
   }

   public function set_version_date_order ( $version_date_order )
   {
      $this->version_date_order = $version_date_order;
   }

   public function get_version_description ()
   {
      return $this->version_description;
   }

   public function set_version_description ( $version_description )
   {
      $this->version_description = $version_description;
   }

   public function get_version_project_id ()
   {
      return $this->project_id;
   }

   public function set_version_project_id ( $project_id )
   {
      $this->project_id = $project_id;
   }

   /**
    * returns the mantis project version database table
    *
    * @return string
    */
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

   public function delete_version ()
   {
      version_remove ( $this->version_id );
   }

   /**
    * updates a specific (field) value in the mantis project version table
    *
    * @param $field
    * @param $version_value
    */
   public function update_version_value ( $field, $version_value )
   {
      $old_version_value = version_get_field ( $this->version_id, $field );

      $version_table = $this->get_version_table ();

      $this->mysqli->query ( "SET SQL_SAFE_UPDATES = 0" );

      $query = "UPDATE $version_table
         SET $field = '" . $version_value . "'
         WHERE id = " . $this->version_id;

      if ( $field == 'released' )
      {
         var_dump ( $query );
      }
      $this->mysqli->query ( $query );

      $this->mysqli->query ( "SET SQL_SAFE_UPDATES = 1" );
   }

   /**
    * adds a new version
    *
    * @return int
    */
   public function insert_version ()
   {
      return version_add ( helper_get_current_project (), $this->version_name );
   }
}