<?php
define('FF_MODULES_DIR', WP_PLUGIN_DIR.'/fivebyfive_modules/');
define('FF_MODULES_URL', WP_PLUGIN_URL.'/fivebyfive_modules/');

include 'functions.php';
include 'vite.php';

if( $_SERVER['REMOTE_ADDR'] === '127.0.0.1' ) {
    include FF_PLUGIN_DIR.'/dev/init.php';
}

include 'modules/modules.php';
include 'elementor_widgets/elementor_widgets.php';
include 'admin/init.php';