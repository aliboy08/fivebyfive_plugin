<?php
$repo_data = include 'get_repo_data.php';

$site_modules = get_option('ff_modules_manager');

$exclude = [
    'ff-vaultre',
    'data-migration',
    'ff-db',
    'lead-tracker',
    'retina',
    'data-push',
    'ff-ajax',
    'ff-au-post-integration',
    'ff-quiz',
];

$modules = [];
foreach( $repo_data as $module ) {

    if( !$module['download_url'] ) continue;
    if( in_array( $module['slug'], $exclude) ) continue;

    $module['active'] = false;
    $module['installed'] = false;
    $module['outdated'] = false;

    if( $site_modules ) {
        foreach( $site_modules as $site_module ) {
            if( $site_module['slug'] === $module['slug'] ) {
                $module['installed'] = true;
                $module['active'] = $site_module['active'];
                $module['outdated'] = $module['version'] != $site_module['version'];
                break;
            }
        }
    }
    
    $modules[] = $module;
}

return $modules;