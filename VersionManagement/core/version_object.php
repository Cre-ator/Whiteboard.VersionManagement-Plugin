<?php
require_once ( __DIR__ . '/version_management_api.php' );

class version_object
{
    private $mysqli;
    private $dbPath;
    private $dbUser;
    private $dbPass;
    private $dbName;

    private $version_id;
    private $version_project_id;
    private $version_name;
    private $version_description;
    private $version_date_order;
    private $version_realeased;
    private $version_obsolete;

    private $version_document_type;

    function __construct ( $version_id )
    {
        $this->version_id = $version_id;

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

    public function get_version_project_id ()
    {
        return $this->version_project_id;
    }

    public function set_version_project_id ( $version_project_id )
    {
        $this->version_project_id = $version_project_id;
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

    public function get_version_document_type ()
    {
        return $this->version_document_type;
    }

    public function set_version_document_type ( $version_document_type )
    {
        $this->version_document_type = $version_document_type;
    }

    /**
     * adds a new version
     *
     * @param $project_id
     * @return int
     */
    public function insert_version ( $project_id )
    {
        return version_add ( $project_id, $this->version_name );
    }

    /**
     * delete current version
     */
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
    public function update_version_db_value ( $field, $version_value )
    {
        $this->mysqli->query ( "SET SQL_SAFE_UPDATES = 0" );

        $query = "UPDATE mantis_project_version_table
         SET $field = '" . $version_value . "'
         WHERE id = " . $this->version_id;

        $this->mysqli->query ( $query );

        $this->mysqli->query ( "SET SQL_SAFE_UPDATES = 1" );
    }

    /**
     * gets a specific (fied) value from the mantis project version table
     *
     * @param $field
     * @return mixed
     */
    public function get_version_db_value ( $field )
    {
        $query = "SELECT $field FROM mantis_project_version_table
          WHERE id = " . $this->version_id;

        $result = $this->mysqli->query ( $query );

        $db_value = mysqli_fetch_row ( $result )[ 0 ];

        return $db_value;
    }
}