<?php
add_filter('ff/elementor_widgets', function($widgets){

    $custom_widgets = get_option('ff_plugin/elementor_widgets');
    if( !$custom_widgets ) return $widgets;

    $exclude = apply_filters('ff_plugin/elementor_widgets/exclude', []);

    foreach( $custom_widgets as $widget ) {

        if( !$widget['active'] ) continue;
        
        if( in_array($widget['slug'], $exclude) ) continue;

        $file = $widget['slug'] .'/'. $widget['slug'] . '.php';

        if( !in_array($file, $widgets) ) {
            $widgets[] = $file;
        }
    }

    return $widgets;
});