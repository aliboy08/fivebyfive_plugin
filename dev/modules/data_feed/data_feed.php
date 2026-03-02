<?php
/**
 * Module Name: Data Feed
 * Version: 1.0.1
 */

// /wp-json/ff/v1/data_feed/?post_type=post&showposts=10
// /wp-json/ff/v1/data_feed/?post_type=post&showposts=10&data=link,post_meta,post_data
// /wp-json/ff/v1/data_feed/?post_type=post&showposts=10&post_fields=post_title
// /wp-json/ff/v1/data_feed/?post_type=post&showposts=10&post_fields=post_title&metas=meta_key_here

add_action( 'rest_api_init', function () {
    register_rest_route( 'ff/v1', '/data_feed/', array(
        'methods' => 'GET',
        'callback' => 'ff_data_feed',
    ) );
} );

function ff_data_feed($request){

    $params = $request->get_params();

    $args = [
        'data' => [],
    ];

    if( $params['data'] ?? null ) {
        $args['data'] = explode(',', $params['data']);
    }

    if( $params['metas'] ?? null ) {
        $args['metas'] = explode(',', $params['metas']);
    }

    if( $params['acf'] ?? null ) {
        $args['acf'] = explode(',', $params['acf']);
    }

    if( $params['post_fields'] ?? null ) {
        $args['post_fields'] = explode(',', $params['post_fields']);
    }

    if( $params['post_id'] ?? null ) {
        return ff_data_feed_post_data($params['post_id'], $args);
    }
    
    $post_type = $params['post_type'] ?? 'post';
    $showposts = $params['showposts'] ?? -1;
    $offset = $params['offset'] ?? null;

    $res = [];

    $query_args = [
        'post_type' => $post_type,
        'showposts' => (int)$showposts,
        'no_found_rows' => true,
        'fields' => 'ids',
    ];
    
    if( $offset ) $query_args['offset'] = (int)$offset;

    $q = new WP_Query($query_args);

    foreach($q->posts as $post_id){
        $res[] = ff_data_feed_post_data($post_id, $args);
    }

    return $res;
}

function ff_data_feed_post_data($post_id, $args){

    $item_data = [
        'id' => $post_id,
    ];

    if( in_array('post_data', $args['data']) ) {
        $item_data['post_data'] = get_post($post_id);
    }

    if( in_array('post_meta', $args['data']) ) {
        $item_data['post_meta'] = get_post_meta($post_id);
    }

    if( in_array('link', $args['data']) ) {
        $item_data['link'] = get_permalink($post_id);
    }

    if( $args['metas'] ?? false ) {
        $item_data['metas'] = [];
        foreach( $args['metas'] as $k ) {
            $item_data['metas'][$k] = get_post_meta($post_id, $k, true);
        }
    }

    if( $args['post_fields'] ?? false ) {
        $item_data['post_fields'] = [];
        foreach( $args['post_fields'] as $k ) {
            $item_data['post_fields'][$k] = get_post_field($k, $post_id);
        }
    }

    if( $args['acf'] ?? false ) {
        $item_data['acf'] = [];
        foreach( $args['acf'] as $k ) {
            $item_data['acf'][$k] = get_field($k, $post_id);
        }
    }

    return $item_data;
}

// add_filter('ff/sub_menus', function($sub_menus){
//     $sub_menus[] = [
//         'slug' => 'data_feed',
//         'label' => 'Data Feed',
//         'render' => function(){
//             echo '<textarea style="width:500px;height:500px;margin:20px;">';
//                 print_r(json_encode(ff_data_feed()));
//             echo '</textarea>';
//         }
//     ];
//     return $sub_menus;
// });

