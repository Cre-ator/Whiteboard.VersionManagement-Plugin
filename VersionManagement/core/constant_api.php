<?php
// URL to VersionManagement plugin
define ( 'VERSIONMANAGEMENT_PLUGIN_URL', config_get_global ( 'path' ) . 'plugins/' . plugin_get_current () . '/' );

// Path to VersionManagement plugin folder
define ( 'VERSIONMANAGEMENT_PLUGIN_URI', config_get_global ( 'plugin_path' ) . plugin_get_current () . DIRECTORY_SEPARATOR );

// Path to VersionManagement core folder
define ( 'VERSIONMANAGEMENT_CORE_URI', VERSIONMANAGEMENT_PLUGIN_URI . 'core' . DIRECTORY_SEPARATOR );

define ( 'PLUGINS_VERSIONMANAGEMENT_THRESHOLD_LEVEL_DEFAULT', ADMINISTRATOR );