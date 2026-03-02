<?php
namespace FF\Plugin\Vite;

define('FF_PLUGIN_VITE_DIST_DIR', __DIR__ .'/dist');
define('FF_PLUGIN_VITE_DIST_URL', plugins_url() .'/fivebyfive/dist');
define('FF_PLUGIN_VITE_MODE', get_mode());
define('FF_PLUGIN_VITE_MANIFEST', get_manifest());
define('FF_PLUGIN_VITE_DEV_SERVER', get_dev_server_origin());

function load_asset($key){
    
    if( !isset(FF_PLUGIN_VITE_MANIFEST->$key) ) return;
    
    if( FF_PLUGIN_VITE_MODE === 'dev' ) {
        load_asset_dev($key);
    } else {
        load_asset_build($key);
    }
}

function load_asset_dev($key){
    $src = FF_PLUGIN_VITE_DEV_SERVER."/".FF_PLUGIN_VITE_MANIFEST->$key;
    echo "<script defer type='module' src='{$src}'></script>";
}

function load_asset_build($key){

    $asset = FF_PLUGIN_VITE_MANIFEST->$key;
    
    load_css($key, $asset);
    
    $src = FF_PLUGIN_VITE_DIST_URL."/".$asset->file;
    
    echo "<script defer type='module' src='{$src}'></script>";
}

function load_css($key, $asset){

    if( !($asset->css ?? null) ) return;
    
    $i = 0;
    foreach( $asset->css as $src ) { $i++;
        $css_handle = "{$key}-css-{$i}";
        $css_src = FF_PLUGIN_VITE_DIST_URL ."/". $src;
        wp_enqueue_style($css_handle, $css_src);
    }
}

function get_mode(){

    if( $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' ) {
        return 'build';
    }

    if( file_exists(FF_PLUGIN_VITE_DIST_DIR."/mode.dev") ) {
        return 'dev';
    }

    return 'build';
}

function get_manifest(){

    $path = FF_PLUGIN_VITE_DIST_DIR."/wp-manifest.json";

    if( FF_PLUGIN_VITE_MODE === 'dev' ) {
        $path = __DIR__."/vite-entrypoints.json";
    }

    return wp_json_file_decode($path);
}

function get_dev_server_origin(){

    $file = FF_PLUGIN_VITE_DIST_DIR."/vite-dev-server.json";
    if( !file_exists( $file ) ) {
        return 'https://localhost:5420';
    }

    $server_config = wp_json_file_decode( $file );
    if( !$server_config ) return 'https://localhost:5420';

    return $server_config->origin;
}