<?php
include 'functions.php';
include 'vite.php';
include 'src/modules/modules.php';
include 'src/elementor_widgets/elementor_widgets.php';

add_action( 'admin_menu', function(){
    
    $icon = 'data:image/svg+xml;base64,DQogICAgICAgIDxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI5MzEiIGhlaWdodD0iMTAyNCIgdmlld0JveD0iMCAwIDkzMSAxMDA2Ij4NCiAgICAgICAgPHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iIzljYTJhNyIgLz4gDQogICAgICAgIDxwYXRoIGQ9Ik01MjYuODE5IDc2MC44NjhoMTU2LjI0NWMtMjcuMjU3LTE0Ni43MzItMTM0LjcxMy0yNjUuNjMyLTI3NS4wNjQtMzA5LjIxOHYtNzkuMzUyaDI3NC41NDl2LTEyNC4wOThoLTQzNC4zNDl2MzM3LjYwOGg0NC4wNDJjMTEwLjI1NSAxLjIyIDIwMy41IDc0LjYwNCAyMzQuNTc3IDE3NS4wNjF6Ij48L3BhdGg+DQogICAgICAgIDxwYXRoIGQ9Ik0xMjQuMjIxIDg4Mi40NzN2LTc1OC4zNzJoNjgyLjQyOXY3NTguMzcyaC02ODIuNDI5ek05MzAuNzc4IDBoLTkzMC43NDd2MTI0LjFoMC4wOTJ2NzU4LjM3MmgtMC4xMjN2MTI0LjFoOTMwLjc0OXYtODgyLjQ3M2gwLjAyOXYtMTI0LjF6Ij48L3BhdGg+DQogICAgICAgIDwvc3ZnPg==';

    $main_key = 'fivebyfive';
    $capability = 'manage_options';

    add_menu_page(  __( 'Five by Five', 'ff' ), 'Five by Five', $capability, $main_key, function(){
        echo '<svg width="80" height="80" viewBox="0 0 280 280"><path class="cls-1" d="M88.91,54.61l-8.1,49.24c-.24,1.44.87,2.74,2.33,2.74h80.57c25.6,0,46.35-20.75,46.35-46.35v-5.25c0-1.3-1.06-2.36-2.36-2.36h-116.45c-1.16,0-2.14.84-2.33,1.98Z"></path><path class="cls-1" d="M78.01,120.85l-8.03,48.82c-.23,1.42.85,2.69,2.28,2.74,35.14,1.17,65.14,22.91,78.36,53.53.37.87,1.22,1.43,2.16,1.43h51.98c1.54,0,2.69-1.46,2.29-2.95-15.23-57.46-65.55-100.77-126.53-105.55-1.22-.1-2.32.77-2.51,1.98Z"></path></svg>';
    }, $icon, 100 );

    $sub_menus = apply_filters('ff/sub_menus', []);
    foreach( $sub_menus as $sub_menu ) {
        add_submenu_page( $main_key, $sub_menu['label'], $sub_menu['label'], $capability, $sub_menu['slug'], function() use ($sub_menu){
            if( $sub_menu['render'] ?? false ) {
                $sub_menu['render']();
            }
            else if( $sub_menu['file'] ?? false ) {
                include $sub_menu['file'];
            }
        });
    }
    
    do_action('ff/admin_menu');
});
