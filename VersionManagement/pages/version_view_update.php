<?php
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmApi.php' );
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmVersion.php' );

update ();

/**
 * authenticates a user and updates a version if user has level to do
 */
function update ()
{
   $versionNames = $_POST[ 'version_name' ];
   if ( isset( $_POST[ 'version_id' ] ) )
   {
      auth_reauthenticate ();
      $versionIds = $_POST[ 'version_id' ];

      $versionValidHashArray = vmApi::getVersionValidHashArray ( $versionIds, $versionNames );
      $versionObsoleteHashArray = vmApi::getVersionObsoleteHashArray ();
      $versionHashArray = array_merge ( $versionValidHashArray, $versionObsoleteHashArray );

      if (
         !vmApi::checkArrayDuplicates ( $versionHashArray ) &&
         vmApi::checkVersionDataIsValid ( $versionIds, $versionNames )
      )
      {
         /** update all version names with temp values */
         vmApi::setTmpVersionName ( $versionIds );

         /** update all versions with new values */
         vmApi::setVersionData ( $versionIds, $versionNames );
      }
      else
      {
         echo 'duplikate in den versionsnamen enthalten';
      }

      vmApi::setNewVersionData ( $versionIds, $versionNames );

   }
   else
   {
      vmApi::setNewVersionData ( null, $versionNames );
   }
   /** redirect to view page */
   print_successful_redirect ( plugin_page ( 'version_view_page', true ) . '&edit=0&obsolete=0' );
}