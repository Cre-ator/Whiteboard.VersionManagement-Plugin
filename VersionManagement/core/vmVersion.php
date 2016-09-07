<?php
require_once ( __DIR__ . '/vmApi.php' );

/**
 * Class vmVersion - represents a version object with
 */
class vmVersion
{
   /**
    * @var integer
    */
   private $versionId;
   /**
    * @var integer
    */
   private $projectId;
   /**
    * @var string
    */
   private $versionName;
   /**
    * @var string
    */
   private $versionOldName;
   /**
    * @var string
    */
   private $description;
   /**
    * @var integer
    */
   private $released;
   /**
    * @var integer
    */
   private $obsolete;
   /**
    * @var integer
    */
   private $dateOrder;
   /**
    * @var integer
    */
   private $documentType;
   /**
    * @var boolean
    */
   private $versionIsUsed;
   /**
    * @var integer
    */
   private $targetVersionIssueCount;

   /**
    * vmVersion constructor.
    * @param null $versionId
    */
   function __construct ( $versionId = null )
   {
      if ( $versionId != null )
      {
         $this->versionId = $versionId;
         $this->dbInitVersionById ();
      }
   }

   /**
    * vmVersion destructor.
    */
   function __destruct ()
   {
      // TODO: Implement __destruct() method.
   }

   /**
    * @return int
    */
   public function getVersionId ()
   {
      return $this->versionId;
   }

   /**
    * @param int $versionId
    */
   public function setVersionId ( $versionId )
   {
      $this->versionId = $versionId;
   }

   /**
    * @return int
    */
   public function getProjectId ()
   {
      return $this->projectId;
   }

   /**
    * @param int $projectId
    */
   public function setProjectId ( $projectId )
   {
      $this->projectId = $projectId;
   }

   /**
    * @return string
    */
   public function getVersionName ()
   {
      return $this->versionName;
   }

   /**
    * @param string $versionName
    */
   public function setVersionName ( $versionName )
   {
      $this->versionName = $versionName;
   }

   /**
    * @return string
    */
   public function getDescription ()
   {
      return $this->description;
   }

   /**
    * @param string $description
    */
   public function setDescription ( $description )
   {
      $this->description = $description;
   }

   /**
    * @return int
    */
   public function getDateOrder ()
   {
      return $this->dateOrder;
   }

   /**
    * @param int $dateOrder
    */
   public function setDateOrder ( $dateOrder )
   {
      $this->dateOrder = $dateOrder;
   }

   /**
    * @return int
    */
   public function getReleased ()
   {
      return $this->released;
   }

   /**
    * @param int $released
    */
   public function setReleased ( $released )
   {
      $this->released = $released;
   }

   /**
    * @return int
    */
   public function getObsolete ()
   {
      return $this->obsolete;
   }

   /**
    * @param int $obsolete
    */
   public function setObsolete ( $obsolete )
   {
      $this->obsolete = $obsolete;
   }

   /**
    * @return int
    */
   public function getDocumentType ()
   {
      return $this->documentType;
   }

   /**
    * @param int $documentType
    */
   public function setDocumentType ( $documentType )
   {
      $this->documentType = $documentType;
   }

   /**
    * @return boolean
    */
   public function isVersionIsUsed ()
   {
      $this->checkVersionIsUsed ();
      return $this->versionIsUsed;
   }

   /**
    * @param $status
    * @return int
    */
   public function getTargetVersionIssueCount ( $status )
   {
      $this->checkTargetVersionIssueCount ( $status );
      return $this->targetVersionIssueCount;
   }

   /**
    * insert object data into new database row
    */
   public function triggerInsertIntoDb ()
   {
      if (
         ( $this->projectId != null ) &&
         is_numeric ( $this->projectId ) &&
         ( $this->versionName != null ) &&
         is_numeric ( $this->released ) &&
         is_numeric ( $this->obsolete ) &&
         ( $this->dateOrder != null ) &&
         is_numeric ( $this->dateOrder )
      )
      {
         $this->dbInsertVersion ();
      }
   }

   /**
    * update selected database row with object data
    */
   public function triggerUpdateInDb ()
   {
      if (
         ( $this->versionId != null ) &&
         is_numeric ( $this->versionId ) &&
         ( $this->projectId != null ) &&
         is_numeric ( $this->projectId ) &&
         ( $this->versionName != null ) &&
         is_numeric ( $this->released ) &&
         is_numeric ( $this->obsolete ) &&
         ( $this->dateOrder != null ) &&
         is_numeric ( $this->dateOrder )
      )
      {
         $this->dbUpdateVersion ();
      }
   }

   /**
    * remove selected database row
    */
   public function triggerDeleteFromDb ()
   {
      if (
         ( $this->versionId != null ) &&
         is_numeric ( $this->versionId )
      )
      {
         $this->dbDeleteVersion ();
      }
   }

   /**
    * initializes a version object with database data
    */
   private function dbInitVersionById ()
   {
      $mysqli = vmApi::initializeDbConnection ();

      $query = /** @lang sql */
         'SELECT * FROM mantis_project_version_table WHERE id=' . $this->versionId;

      $result = $mysqli->query ( $query );
      $dbVersionRow = mysqli_fetch_row ( $result );
      $mysqli->close ();

      $this->projectId = $dbVersionRow[ 1 ];
      $this->versionName = $dbVersionRow[ 2 ];
      $this->versionOldName = $dbVersionRow[ 2 ];
      $this->description = $dbVersionRow[ 3 ];
      $this->released = $dbVersionRow[ 4 ];
      $this->obsolete = $dbVersionRow[ 5 ];
      $this->dateOrder = $dbVersionRow[ 6 ];
   }

   /**
    * insert new version roe
    */
   private function dbInsertVersion ()
   {
      $mysqli = vmApi::initializeDbConnection ();

      $query = /** @lang sql */
         'INSERT INTO mantis_project_version_table (id,project_id,version,description,released,obsolete,date_order)
         SELECT null,' . $this->projectId . ',\'' . $this->versionName . '\',\'' . $this->description . '\',
         ' . $this->released . ',' . $this->obsolete . ',' . $this->dateOrder . '
         FROM DUAL WHERE NOT EXISTS (
         SELECT 1 FROM mantis_project_version_table
         WHERE version=\'' . $this->versionName . '\')';

      $mysqli->query ( $query );
      $this->versionId = $mysqli->insert_id;
      $mysqli->close ();
   }

   /**
    * update version row
    */
   private function dbUpdateVersion ()
   {
      $mysqli = vmApi::initializeDbConnection ();

      $query = /** @lang sql */
         'UPDATE mantis_project_version_table
         SET project_id=' . $this->projectId . ',version=\'' . $this->versionName . '\', 
         description=\'' . $this->description . '\',released=' . $this->released . ', 
         obsolete=' . $this->obsolete . ',date_order=' . $this->dateOrder . '
         WHERE id=' . $this->versionId;

      $mysqli->query ( $query );
      $mysqli->close ();

      $this->dbTriggerUpdateBugVersion ( 'version' );
      $this->dbTriggerUpdateBugVersion ( 'fixed_in_version' );
      $this->dbTriggerUpdateBugVersion ( 'target_version' );
      $this->dbTriggerUpdateBugHistory ( 'old_value' );
      $this->dbTriggerUpdateBugHistory ( 'new_value' );
   }

   /**
    * update given value of bug history entrys
    *
    * @param $value
    */
   private function dbTriggerUpdateBugHistory ( $value )
   {
      $affectedHistoryIds = $this->dbGetValueSpecHistoryIds ( $value );

      foreach ( $affectedHistoryIds as $affectedHistoryId )
      {
         $mysqli = vmApi::initializeDbConnection ();

         $query = /** @lang sql */
            'UPDATE mantis_bug_history_table
            SET ' . $value . ' =\'' . $this->versionName . '\'
            WHERE id=' . $affectedHistoryId;

         $mysqli->query ( $query );
         $mysqli->close ();
      }
   }

   /**
    * get ids from bug history table which have old version name used in given value
    *
    * @param $value
    * @return array
    */
   private function dbGetValueSpecHistoryIds ( $value )
   {
      $affectedBugIds = $this->dbGetProjectSpecBugIds ();
      $filterString = implode ( ',', $affectedBugIds );

      $mysqli = vmApi::initializeDbConnection ();

      $query = /** @lang sql */
         'SELECT id FROM mantis_bug_history_table WHERE field_name IN (\'version\', \'fixed_in_version\', \'target_version\')
          bug_id IN (' . $filterString . ') AND ' . $value . ' = \'' . $this->versionOldName . '\'';

      var_dump ( $query );

      $result = $mysqli->query ( $query );
      $mysqli->close ();

      $historyIds = array ();
      if ( $result->num_rows != 0 )
      {
         while ( $row = $result->fetch_row () )
         {
            $historyIds[] = $row[ 0 ];
         }
      }

      return $historyIds;
   }

   /**
    * update given version type of bug entry
    *
    * @param $versionType
    */
   private function dbTriggerUpdateBugVersion ( $versionType )
   {
      $affectedBugIds = $this->dbGetVersionSpecBugIds ( $versionType );

      foreach ( $affectedBugIds as $affectedBugId )
      {
         $mysqli = vmApi::initializeDbConnection ();

         $query = /** @lang sql */
            'UPDATE mantis_bug_table
            SET ' . $versionType . ' =\'' . $this->versionName . '\'
            WHERE id=' . $affectedBugId;

         $mysqli->query ( $query );
         $mysqli->close ();
      }
   }

   /**
    * get ids from bug table which have old version name used in given version type
    *
    * @param $versionType
    * @return array
    */
   private function dbGetVersionSpecBugIds ( $versionType )
   {
      $mysqli = vmApi::initializeDbConnection ();

      $query = /** @lang sql */
         'SELECT id FROM mantis_bug_table WHERE ' . $versionType . ' = \'' . $this->versionOldName . '\'
         AND project_id = ' . $this->projectId;

      $result = $mysqli->query ( $query );
      $mysqli->close ();

      $bugIds = array ();
      if ( $result->num_rows != 0 )
      {
         while ( $row = $result->fetch_row () )
         {
            $bugIds[] = $row[ 0 ];
         }
      }

      return $bugIds;
   }

   /**
    * get bug ids by version spec project id
    *
    * @return array
    */
   private function dbGetProjectSpecBugIds ()
   {
      $mysqli = vmApi::initializeDbConnection ();

      $query = /** @lang sql */
         'SELECT id FROM mantis_bug_table WHERE project_id= ' . $this->projectId;

      $result = $mysqli->query ( $query );
      $mysqli->close ();

      $bugIds = array ();
      if ( $result->num_rows != 0 )
      {
         while ( $row = $result->fetch_row () )
         {
            $bugIds[] = $row[ 0 ];
         }
      }

      return $bugIds;
   }

   /**
    * delete version row
    */
   private function dbDeleteVersion ()
   {
      $mysqli = vmApi::initializeDbConnection ();

      $query = /** @lang sql */
         'DELETE FROM mantis_project_version_table WHERE id=' . $this->versionId;

      $mysqli->query ( $query );
      $mysqli->close ();
   }

   /**
    * check if the version is used in any bug
    */
   private function checkVersionIsUsed ()
   {
      $mysqli = vmApi::initializeDbConnection ();

      $query = /** @lang sql */
         'SELECT COUNT(id) FROM mantis_bug_table
         WHERE version=\'' . $this->versionName . '\'';

      $result = $mysqli->query ( $query );

      $idCount = mysqli_fetch_row ( $result )[ 0 ];

      $query = /** @lang sql */
         'SELECT COUNT(id) FROM mantis_bug_table
         WHERE fixed_in_version=\'' . $this->versionName . '\'';

      $result = $mysqli->query ( $query );

      $idCount += mysqli_fetch_row ( $result )[ 0 ];

      $query = /** @lang sql */
         'SELECT COUNT(id) FROM mantis_bug_table
         WHERE target_version=\'' . $this->versionName . '\'';

      $result = $mysqli->query ( $query );

      $idCount += mysqli_fetch_row ( $result )[ 0 ];
      $mysqli->close ();

      $this->versionIsUsed = ( $idCount > 0 );
   }

   private function checkTargetVersionIssueCount ( $status )
   {
      $mysqli = vmApi::initializeDbConnection ();

      $query = /** @lang sql */
         'SELECT COUNT(id) FROM mantis_bug_table
         WHERE target_version=\'' . $this->versionName . '\'
         AND status=' . $status;

      $result = $mysqli->query ( $query );
      $this->targetVersionIssueCount = mysqli_fetch_row ( $result )[ 0 ];
      $mysqli->close ();
   }
}