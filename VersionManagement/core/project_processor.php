<?php

/**
 * Created by PhpStorm.
 * User: schwarz
 * Date: 03.06.2016
 * Time: 16:56
 */
class project_processor
{
   private $project_id;

   public function __construct ( $project_id )
   {
      $this->project_id = $project_id;
   }

   public function get_project_id ()
   {
      return $this->project_id;
   }

   public function get_project_name ()
   {
      return project_get_name ( $this->project_id );
   }

   public function get_project_assigned_versions ()
   {
      return version_get_all_rows ( $this->project_id );
   }
}