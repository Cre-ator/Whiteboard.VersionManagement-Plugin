<?php
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmApi.php' );
require_once ( __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'vmHtmlApi.php' );

auth_reauthenticate ();
access_ensure_global_level ( plugin_config_get ( 'access_level' ) );

html_page_top1 ( plugin_lang_get ( 'config_page_title' ) );
html_page_top2 ();
print_manage_menu ();

echo '<script type="text/javascript" src="plugins/VersionManagement/files/jscolor/jscolor.js"></script>';
echo '<link rel="stylesheet" href="plugins/VersionManagement/files/version_management.css"/>';

echo '<br/>';
echo '<form action="' . plugin_page ( 'config_update' ) . '" method="post">';
echo '<table align="center" class="width50" cellspacing="1">';
/** General configuration */
echo '<tr><td class="form-title" colspan="2">' . plugin_lang_get ( 'menu_title' ) . ':&nbsp;' . plugin_lang_get ( 'config_page_general' ) . '</td></tr>';
/** Access level */
vmHtmlApi::htmlConfigTableRow ();
echo '<td class="category">';
echo '<span class="required">*</span>' . plugin_lang_get ( 'config_page_read_access_level' );
echo '</td>';
echo '<td width="100px" colspan="2">';
echo '<select name="r_access_level">';
print_enum_string_option_list ( 'access_levels', plugin_config_get ( 'r_access_level', ADMINISTRATOR ) );
echo '</select>';
echo '</td>';
echo '</tr>';
vmHtmlApi::htmlConfigTableRow ();
echo '<td class="category">';
echo '<span class="required">*</span>' . plugin_lang_get ( 'config_page_write_access_level' );
echo '</td>';
echo '<td width="100px" colspan="2">';
echo '<select name="access_level">';
print_enum_string_option_list ( 'access_levels', plugin_config_get ( 'access_level', ADMINISTRATOR ) );
echo '</select>';
echo '</td>';
echo '</tr>';
/** Show menu */
vmHtmlApi::htmlConfigTableRow ();
vmHtmlApi::htmlConfigCategoryColumn ( 1, 1, 'config_page_show_menu' );
vmHtmlApi::htmlConfigRadioButton ( 1, 'show_menu' );
echo '</tr>';
/** Show plugin information in footer */
vmHtmlApi::htmlConfigTableRow ();
vmHtmlApi::htmlConfigCategoryColumn ( 1, 1, 'config_page_show_footer' );
vmHtmlApi::htmlConfigRadioButton ( 1, 'show_footer' );
echo '</tr>';

/** Version Management Overview */
vmHtmlApi::htmlConfigTableTitleRow ( 2, 'config_page_version_overview' );
/** Unused version highlighting */
vmHtmlApi::htmlConfigTableRow ();
vmHtmlApi::htmlConfigCategoryColumn ( 1, 1, 'config_page_unused_version_row_color' );
vmHtmlApi::htmlConfigColorPicker ( 1, 'unused_version_row_color', '#908b2d' );
echo '</tr>';

echo '<tr class="footer">';
echo '<td class="center" colspan="5">';
echo '<input type="submit" name="config_change" class="button" value="' . lang_get ( 'update_prefs_button' ) . '"/>' . ' ';
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</form>';
html_page_bottom1 ();