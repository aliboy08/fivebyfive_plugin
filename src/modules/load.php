<?php
$modules = get_option('ff_plugin/modules');
if( !$modules ) return;

$exclude = apply_filters('ff_plugin/modules/exclude', []);

try {
    foreach( $modules as $module ) {
        if( !$module['active'] ) continue;
        if( in_array($module['slug'], $exclude) ) continue;
        $file = FF_MODULES_DIR.$module['slug'].'/'.$module['slug'].'.php';
        if( !file_exists($file) ) continue;
        include_once $file;
    }
}
catch (Error $e) {
    echo $e;
}
