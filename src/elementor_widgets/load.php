<?php
add_filter('ff/elementor_widgets', function($widgets){

    $custom_widgets = get_option('ff_plugin/elementor_widgets');
    if( !$custom_widgets ) return $widgets;

    foreach( $custom_widgets as $widget ) {

        if( !$widget['active'] ) continue;

        $file = $widget['slug'] .'/'. $widget['slug'] . '.php';

        if( !in_array($file, $widgets) ) {
            $widgets[] = $file;
        }
    }

    return $widgets;
});