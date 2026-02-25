<?php
add_filter('ff/sub_menus', function($sub_menus){

    $sub_menus[] = [
        'slug' => 'modules_manager',
        'label' => 'Modules Manager',
        'render' => function(){

            ff_plugin_js_data('ff_plugin', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ff_plugin'),
            ]);

            echo '<div id="ff_modules_manager"><div class="spinner"></div></div>';

            echo '<button class="button-primary" id="modules_refresh">Refresh</button>';

            wp_enqueue_style('icomoon', get_stylesheet_directory_uri().'/assets/icomoon/style.css');
            ff_plugin_load_asset('manager');
        }
    ];

    return $sub_menus;
});

add_action('wp_ajax_ff_plugin_manager_action', 'ff_plugin_manager_action');
function ff_plugin_manager_action(){
    $payload = json_decode(file_get_contents('php://input'), true);
    if ( ! wp_verify_nonce( $payload['nonce'], 'ff_plugin' ) ) die();
    $res = include 'actions/'.$payload['action'].'.php';
    wp_send_json($res);
}

include 'load_modules.php';