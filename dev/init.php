<?php
add_filter('ff_plugin/modules/exclude', function($exclude){
    $exclude[] = 'categories_populate';
    return $exclude;
});

include 'modules/categories_populate/categories_populate.php';