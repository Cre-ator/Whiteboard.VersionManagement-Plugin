<?php

require_once ( __DIR__ . '/../core/version_management_api.php' );
require_once ( __DIR__ . '/../core/version_object.php' );

process_update ();

/**
 * authenticates a user and updates a version if user has level to do
 */
function process_update ()
{
    if ( isset( $_POST[ 'version_id' ] ) )
    {
        auth_reauthenticate ();
        $version_ids = $_POST[ 'version_id' ];
        $version_names = $_POST[ 'version_name' ];

        $version_valid_hash_array = version_management_api::get_version_hash_array ( $version_ids, $version_names );
        $version_obsolete_hash_array = version_management_api::get_version_obsolete_hash_array ();
        $version_hash_array = array_merge ( $version_valid_hash_array, $version_obsolete_hash_array );

        if ( !version_management_api::check_array_for_duplicates ( $version_hash_array ) &&
            version_management_api::check_version_data_is_valid ( $version_ids, $version_names )
        )
        {
            /** update all version names with temp values */
            version_management_api::set_temp_version_name ( $version_ids );

            /** update all versions with new values */
            version_management_api::set_version_data ( $version_ids, $version_names );
        }
        else
        {
            echo 'duplikate in den versionsnamen enthalten';
        }

        version_management_api::set_new_version_data ( $version_ids, $version_names );

        /** redirect to view page */
        print_successful_redirect ( plugin_page ( 'version_view_page', true ) . '&edit=0&obsolete=0' );
    }
}