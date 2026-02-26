<?php
namespace FF\Plugin\Vite;

$dist_dir = __DIR__ .'/dist';
$dist_url = plugins_url() .'/fivebyfive/dist';
$mode = get_mode();
$manifest = get_manifest();
$dev_server_origin = get_dev_server_origin();
$is_admin = is_admin();

function load_asset($key){

    global $manifest, $mode, $dist_url;
    
    if( !isset($manifest->$key) ) return;
    
    if( $mode === 'dev' ) {
        load_asset_dev($key);
    } else {
        load_asset_build($key);
    }
}

function load_asset_dev($key){
    global $manifest, $dev_server_origin;
    $src = "{$dev_server_origin}/{$manifest->$key}";
    echo "<script defer type='module' src='{$src}'></script>";
}

function load_asset_build($key){

    global $manifest, $dist_url;

    $asset = $manifest->$key;
    
    load_css($key, $asset);
    
    $src = "{$dist_url}/{$asset->file}";
    
    echo "<script defer type='module' src='{$src}'></script>";
}

function load_css($key, $asset){

    global $dist_url, $is_admin;
    
    if( !($asset->css ?? null) ) return;
    
    $i = 0;
    foreach( $asset->css as $src ) { $i++;
        $css_handle = "{$key}-css-{$i}";
        $css_src = "{$dist_url}/{$src}";
        wp_enqueue_style($css_handle, $css_src);
    }
}

function get_mode(){

    global $dist_dir;

    if( $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' ) {
        return 'build';
    }

    if( file_exists("{$dist_dir}/mode.dev") ) {
        return 'dev';
    }

    return 'build';
}

function get_manifest(){

    global $mode, $dist_dir;

    $path = "{$dist_dir}/wp-manifest.json";

    if( $mode === 'dev' ) {
        $path = __DIR__."/vite-entrypoints.json";
    }

    return wp_json_file_decode($path);
}

function get_dev_server_origin(){

    global $mode, $dist_dir;

    $file = "{$dist_dir}/vite-dev-server.json";
    if( !file_exists( $file ) ) {
        return 'https://localhost:5420';
    }

    $server_config = wp_json_file_decode( $file );
    if( !$server_config ) return 'https://localhost:5420';

    return $server_config->origin;
}