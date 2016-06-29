<?php

require_once ( __DIR__ . '/../core/version_management_api.php' );
require_once ( __DIR__ . '/../core/version_object.php' );

process_page ();

function process_page ()
{

    html_page_top1 ( plugin_lang_get ( 'menu_title' ) );
    echo '<script type="text/javascript" src="plugins/VersionManagement/files/version_management.js"></script>';
    echo '<link rel="stylesheet" href="plugins/VersionManagement/files/version_management.css"/>';
    html_page_top2 ();

    if ( plugin_is_installed ( 'WhiteboardMenu' ) &&
        file_exists ( config_get_global ( 'plugin_path' ) . 'WhiteboardMenu' )
    )
    {
        require_once __DIR__ . '/../../WhiteboardMenu/core/whiteboard_print_api.php';
        whiteboard_print_api::printWhiteboardMenu ();
    }

    echo '<div align="center">';
    echo '<hr size="1" width="100%" />';

    process_table ();

    echo '</div>';
    html_page_bottom ();
}

function process_table ()
{
    /** head */
    print_head_table ();

    /** main */
    open_main_table ();
    print_table_head ();
    print_table_body ();
    close_table ();

    /** foot */
    print_foot_table ();
    if ( $_GET[ "edit" ] == 1 )
    {
        echo '</form>';
    }
}

function open_table ()
{
    if ( version_management_api::check_mantis_version_is_released () )
    {
        echo '<table class="width100">';
    }
    else
    {
        echo '<div class="table-container">';
        echo '<table>';
    }
}

function open_main_table ()
{
    if ( $_GET[ "edit" ] == 1 )
    {
        echo '<form action="' . plugin_page ( 'version_view_update' ) . '" method="post">';
    }
    if ( version_management_api::check_mantis_version_is_released () )
    {
        echo '<table id="version_view" class="width100">';
    }
    else
    {
        echo '<div class="table-container">';
        echo '<table id="version_view">';
    }
}

function print_head_table ()
{
    open_table ();
    echo '<thead>';
    echo '<tr>';
    echo '<td class="form-title">';
    echo plugin_lang_get ( 'version_view_title' );
    echo '</td>';
    echo '<td class="right">';
    if ( $_GET[ "obsolete" ] == 1 )
    {
        echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=' . $_GET[ "edit" ] . '&amp;obsolete=0">';
        echo '<span class="input">';
        echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_head_hide_obsolete' ) . '" />';
    }
    else
    {
        echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=' . $_GET[ "edit" ] . '&amp;obsolete=1">';
        echo '<span class="input">';
        echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_head_show_obsolete' ) . '" />';
    }
    echo '</span>';
    echo '</a>';
    echo '</td>';
    echo '</tr>';
    echo '</thead>';
    close_table ();
}

function print_table_head ()
{
    echo '<thead>';
    echo '<tr class="row-category2">';
    echo '<th>' . plugin_lang_get ( 'version_view_table_head_version' ) . '</th>';
    echo '<th>' . plugin_lang_get ( 'version_view_table_head_released' ) . '</th>';
    echo '<th>' . plugin_lang_get ( 'version_view_table_head_obsolete' ) . '</th>';
    echo '<th>' . plugin_lang_get ( 'version_view_table_head_date' ) . '</th>';
    echo '<th>' . plugin_lang_get ( 'version_view_table_head_description' ) . '</th>';
    if ( version_management_api::check_document_management_is_installed () )
    {
        echo '<th>' . plugin_lang_get ( 'version_view_table_head_type' ) . '</th>';
    }
    if ( $_GET[ "edit" ] == 0 )
    {
        echo '<th>' . plugin_lang_get ( 'version_view_table_head_action' ) . '</th>';
    }
    echo '</tr>';
    echo '</thead>';
}

function print_table_body ()
{
    $current_project_id = helper_get_current_project ();
    $versions = version_get_all_rows_with_subs ( $current_project_id, null, get_obsolete_value () );

    echo '<tbody>';
    foreach ( $versions as $version )
    {
        $version_object = new version_object( $version[ 'id' ] );
        $version_object->set_version_name ( $version[ 'version' ] );
        $version_object->set_version_description ( $version[ 'description' ] );
        $version_object->set_version_date_order ( $version[ 'date_order' ] );
        $version_object->set_version_released ( $version[ 'released' ] );
        $version_object->set_version_obsolete ( $version[ 'obsolete' ] );
        printRow ( $version_object );
        print_version_name_column ( $version_object );
        print_version_released_column ( $version_object );
        print_version_obsolete_column ( $version_object );
        print_version_date_order_column ( $version_object );
        print_version_description_column ( $version_object );
        if ( version_management_api::check_document_management_is_installed () )
        {
            print_document_type_column ( $version_object );
        }
        print_version_action_column ( $version_object );
        echo '</tr>';
    }
    echo '</tbody>';
}

function print_version_name_column ( version_object $version_object )
{
    echo '<td>';
    echo '<input type="hidden" name="version_id[]" value="' . $version_object->get_version_id () . '"/>';
    if ( $_GET[ "edit" ] == 1 )
    {
        echo '<label for="proj-version-new-version">';
        echo '<span class="input" style="width:100%;">';
        echo '<input type="text" id="proj-version-new-version" name="version_name[]" 
         style="width:100%;" maxlength="64" value="' .
            string_attribute ( $version_object->get_version_name () ) . '" />';
        echo '</span>';
        echo '</label>';
    }
    else
    {
        echo $version_object->get_version_name ();
    }
    echo '</td>';
}

function print_version_released_column ( version_object $version_object )
{
    echo '<td>';
    if ( $_GET[ "edit" ] == 1 )
    {
        echo '<span class="checkbox">';
        echo '<input type="checkbox" id="proj-version-released" name="version_released[]" value="' .
            $version_object->get_version_id () . '"';
        check_checked ( $version_object->get_version_released (), ON );
        echo '/>';
        echo '</span>';
    }
    else
    {
        echo trans_bool ( $version_object->get_version_released () );
    }
    echo '</td>';
}

function print_version_obsolete_column ( version_object $version_object )
{
    echo '<td>';
    if ( $_GET[ "edit" ] == 1 )
    {
        echo '<span class="checkbox">';
        echo '<input type="checkbox" id="proj-version-obsolete" name="version_obsolete[]" value="' .
            $version_object->get_version_id () . '"';
        check_checked ( $version_object->get_version_obsolete (), ON );
        echo '/>';
        echo '</span>';
    }
    else
    {
        echo trans_bool ( $version_object->get_version_obsolete () );
    }
    echo '</td>';
}

function print_version_date_order_column ( version_object $version_object )
{
    echo '<td>';
    if ( $_GET[ "edit" ] == 1 )
    {
        echo '<label for="proj-version-date-order">';
        echo '<span class="input">';
        echo '<input type="text" id="proj-version-date-order" name="version_date_order[]"
         class="datetime" size="15" value="' .
            ( date_is_null ( $version_object->get_version_date_order () ) ?
                '' : string_attribute ( date ( config_get ( 'calendar_date_format' ), $version_object->get_version_date_order () ) ) ) . '" />';
        echo '</span>';
        echo '</label>';
    }
    else
    {
        echo date_is_null ( $version_object->get_version_date_order () ) ?
            '' : string_attribute ( date ( config_get ( 'calendar_date_format' ), $version_object->get_version_date_order () ) );
    }
    echo '</td>';
}

function print_version_description_column ( version_object $version_object )
{
    echo '<td width="100">';
    if ( $_GET[ "edit" ] == 1 )
    {
        echo '<span class="text">';
        echo '<input type="text" id="proj-version-description" name="version_description[]" value="' .
            string_attribute ( $version_object->get_version_description () ) . '"/>';
        echo '</span>';
    }
    else
    {
        echo string_display ( $version_object->get_version_description () );
    }
    echo '</td>';
}

function print_document_type_column ( version_object $version_object )
{
    require_once ( __DIR__ . '/../../SpecManagement/core/specmanagement_database_api.php' );
    $specmanagement_database_api = new specmanagement_database_api();

    $type_id = $specmanagement_database_api->get_type_by_version ( $version_object->get_version_id () );
    $current_type = $specmanagement_database_api->get_type_string ( $type_id );

    echo '<td>';
    if ( $_GET[ "edit" ] == 1 )
    {
        echo '<span class="select"><select ' . helper_get_tab_index () . ' id="proj-version-type" name="version-type[]">';
        echo '<option value=""></option>';
        $types_rows = $specmanagement_database_api->get_full_types ();
        foreach ( $types_rows as $types_row )
        {
            $available_type = $types_row[ 1 ];
            echo '<option value="' . $available_type . '"';
            check_selected ( string_attribute ( $current_type ), $available_type );
            echo '>' . $available_type . '</option>';
        }
        echo '</select></span>';
    }
    else
    {
        echo string_display ( $current_type );
    }
    echo '</td>';
}

function print_version_action_column ( version_object $version_object )
{
    $version_id = $version_object->get_version_id ();

    if ( $_GET[ "edit" ] == 0 )
    {
        echo '<td>';
        echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_delete' ) .
            '&amp;version_id=' . $version_id . '">';
        echo '<span class="input">';
        echo '<input type="button" value="' . lang_get ( 'delete_link' ) . '" />';
        echo '</span>';
        echo '</a>';
        echo '</td>';
    }
}

function close_table ()
{
    echo '</table>';
    if ( !version_management_api::check_mantis_version_is_released () )
    {
        echo '</div>';
    }
}

function printRow ( version_object $version_object )
{
    if ( ( $version_object->check_version_is_used () == false ) )
    {
        echo '<tr style="background-color: ' . plugin_config_get ( 'unused_version_row_color' ) . '">';
    }
    else
    {
        if ( version_management_api::check_mantis_version_is_released () )
        {
            echo '<tr ' . helper_alternate_class () . '>';
        }
        else
        {
            echo '<tr>';
        }
    }
}

function print_foot_table ()
{
    open_table ();
    echo '<tbody>';
    echo '<tr>';
    if ( $_GET[ "edit" ] == 1 )
    {
        $current_project_id = helper_get_current_project ();
        $versions = version_get_all_rows_with_subs ( $current_project_id, null, get_obsolete_value () );
        $initial_row_count = ( count ( $versions ) );

        echo '<td class="left">';
        echo '<input type="button" value="+" onclick="add_version_row()" />&nbsp;';
        echo '<input type="button" value="-" onclick="del_version_row(' . $initial_row_count . ')" />&nbsp;';
        echo '</td>';
    }

    echo '<td colspan="5" class="center">';
    if ( $_GET[ "edit" ] == 1 )
    {
        echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_update' ) . '&amp;edit=1&amp;obsolete=' . $_GET[ 'obsolete' ] . '">';
        echo '<span class="input">';
        echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_foot_edit_done' ) . '" />';
        echo '</span>';
        echo '</a>';
    }
    else
    {
        echo '<a style="text-decoration: none;" href="' . plugin_page ( 'version_view_page' ) . '&amp;edit=1&amp;obsolete=' . $_GET[ 'obsolete' ] . '">';
        echo '<span class="input">';
        echo '<input type="submit" value="' . plugin_lang_get ( 'version_view_table_foot_edit' ) . '" />';
        echo '</span>';
        echo '</a>';
    }
    echo '</td>';
    echo '</tr>';
    echo '</tbody>';
    close_table ();
}

function get_obsolete_value ()
{
    $obsolete = false;
    if ( $_GET[ 'obsolete' ] == 1 )
    {
        $obsolete = null;
    }

    return $obsolete;
}
