<?php
require_once ( __DIR__ . DIRECTORY_SEPARATOR . 'vmApi.php' );

/**
 * manages function which process multiple versions
 */
class vmVersionManager
{
   public static function getVersionIdsWithSubs ( $projectId )
   {
      $versions = version_get_all_rows_with_subs ( $projectId );
      $versionIds = array ();
      foreach ( $versions as $version )
      {
         array_push ( $versionIds, $version[ 'id' ] );
      }

      return $versionIds;
   }
}