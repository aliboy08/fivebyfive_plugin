<?php
/**
 * Module Name: Data Feed
 * Version: 1.0.0
 */

// add_action( 'admin_menu', function(){
    
//     add_menu_page(  __( 'FF Export', 'fivebyfive' ), 'FF Export', 'manage_options', 'ff_export', function(){
//         echo '<textarea style="width:500px;height:500px;margin:20px;">';
//             print_r(json_encode(ff_data_feed()));
//         echo '</textarea>';
//     });
    
// });

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

    $data = [];
    if( $params['data'] ?? null ) {
        $data = explode(',', $params['data']);
    }

    $metas = null;
    if( $params['metas'] ?? null ) {
        $metas = explode(',', $params['metas']);
    }

    $post_fields = null;
    if( $params['post_fields'] ?? null ) {
        $post_fields = explode(',', $params['post_fields']);
    }
    
    $post_type = $params['post_type'] ?? 'post';
    $showposts = $params['showposts'] ?? -1;
    $offset = $params['offset'] ?? null;

    $res = [];

    $query_args = [
        'post_type' => $post_type,
        'showposts' => (int)$showposts,
        'no_found_rows' => true,
    ];
    
    if( $offset ) $query_args['offset'] = (int)$offset;

    $q = new WP_Query($query_args);

    foreach($q->posts as $post){

        $item_data = [
            'post_id' => $post->ID,
        ];

        if( in_array('post_data', $data) ) {
            $item_data['post_data'] = $post;
        }

        if( in_array('post_meta', $data) ) {
            $item_data['post_meta'] = get_post_meta($post->ID);
        }

        if( in_array('link', $data) ) {
            $item_data['link'] = get_permalink($post->ID);
        }

        if( $metas ) {
            $item_data['metas'] = [];
            foreach( $metas as $k ) {
                $item_data['metas'][$k] = get_post_meta($post->ID, $k, true);
            }
        }

        if( $post_fields ) {
            $item_data['post_fields'] = [];
            foreach( $post_fields as $k ) {
                $item_data['post_fields'][$k] = get_post_field($k, $post->ID);
            }
        }

        $res[] = $item_data;
    }

    return $res;
}