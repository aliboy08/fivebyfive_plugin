<?php
$modules = get_option('ff_plugin/modules');
if( !$modules ) return;

try {
    foreach( $modules as $module ) {
        if( !$module['active'] ) continue;
        $file = FF_MODULES_DIR.$module['slug'].'/'.$module['slug'].'.php';
        if( !file_exists($file) ) continue;
        include_once $file;
    }
}
catch (Error $e) {
    echo $e;
}
