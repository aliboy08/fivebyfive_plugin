<?php
add_filter('ff/sub_menus', function($sub_menus){

    $sub_menus[] = [
        'slug' => 'modules_manager',
        'label' => 'Modules Manager',
        'render' => function(){
            
            ff_plugin_admin_scripts();

            echo '<h3>5x5 Modules</h3>';
            echo '<div id="ff_modules_manager" class="ff_plugin_manager_items">'. ff_plugin_loading_icon() .'</div>';
            
            ff_plugin_load_asset('modules_manager');
        }
    ];

    return $sub_menus;
});

add_action('wp_ajax_ff_plugin_modules_api', 'ff_plugin_modules_api');
function ff_plugin_modules_api(){
    $payload = json_decode(file_get_contents('php://input'), true);
    if ( ! wp_verify_nonce( $payload['nonce'], 'ff_plugin' ) ) die();
    include_once 'api.php';
    $api = new FF_Plugin_Modules_API();
    $action = $payload['action'];
    wp_send_json($api->$action($payload));
}

include 'load.php';