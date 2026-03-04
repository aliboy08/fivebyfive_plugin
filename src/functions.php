<?php
function ff_plugin_js_data($var_name, $data){
    if ( is_string( $data ) ) {
        $data = html_entity_decode( $data, ENT_QUOTES, 'UTF-8' );
    } elseif ( is_array( $data ) ) {
        foreach ( $data as $key => $value ) {
            if ( !is_scalar( $value ) ) {
                continue;
            }
            $data[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
        }
    }
    ?><script type="text/javascript">/* <![CDATA[ */var <?=$var_name?> = <?=wp_json_encode($data)?>;/* ]]> */</script><?php
}

function ff_plugin_load_asset($key){
    FF\Plugin\Vite\load_asset($key);
}

function ff_plugin_load_module_asset($key){
    if( FF_PLUGIN_VITE_MODE === 'dev' ) {
        FF\Plugin\Vite\load_asset('module_'.$key);
    }
    else {
        ff_plugin_load_asset_dist_build($key);
    }
}

$ff_plugin_modules_assets = [];
function ff_plugin_load_asset_dist_build($key){
    
    // global $ff_plugin_modules_assets;
    // if( in_array($key, $ff_plugin_modules_assets) ) return;

    // $ff_plugin_modules_assets[] = $key;
    
    // $manifest_path = FF_MODULES_DIR.$key."/dist/manifest.json";

    // pre_debug([
    //     'manifest_path' => $manifest_path,
    // ]);

    // if( !file_exists($manifest_path) ) return;
    
    // $manifest = wp_json_file_decode( $manifest_path );
    // if( !$manifest ) return;
    
    // $dist_url = FF_MODULES_URL.$key."/dist/";

    // foreach( $manifest->css as $css_key ) {
    //     $css_src = $dist_url.$css_key;
    //     wp_enqueue_style($css_key, $css_src);
    // }

    // if( $settings['css_only'] ?? false ) return;
    
    // $js_src = $dist_url.$manifest->js;
    // echo "<script defer type='module' src='{$js_src}'></script>";
}


function ff_plugin_loading_icon(){
    return '<div class="spinner" style="float:none;visibility:visible;margin: 20px;"></div>';
}

function ff_plugin_admin_scripts(){

    ff_plugin_load_asset('admin_styles');

    ff_plugin_js_data('ff_plugin', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ff_plugin'),
    ]);
}