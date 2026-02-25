<?php
$repo_data = include 'get_repo_data.php';

$site_modules = get_option('ff_modules_manager');

$modules = [];
foreach( $repo_data as $module ) {
    
    $module['active'] = false;
    $module['installed'] = false;
    $module['outdated'] = false;

    if( $site_modules ) {
        foreach( $site_modules as $site_module ) {
            if( $site_module['slug'] === $module['slug'] ) {
                $module['installed'] = true;
                $module['active'] = $site_module['active'];
                $module['outdated'] = $module['version'] != $site_module['version'];
                $module['old_version'] = $site_module['version'];
                break;
            }
        }
    }
    
    $modules[] = $module;
}

return $modules;