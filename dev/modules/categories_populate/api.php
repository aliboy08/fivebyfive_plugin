<?php
namespace FF\Plugin\Categories_Populate;

class API {
    
    function get_data($payload){
        foreach( $payload['items'] as &$item ) {
            $item['id'] = $this->get_post_id($item['title'], $payload['post_type']);
        }
        return $payload['items'];
    }

    function update_items($payload){

        $res = [];

        $taxonomy = $payload['taxonomy'];
    
        foreach( $payload['items'] as $item ) {
            
            $term_ids = $this->get_term_ids($item, $taxonomy);
            if( $term_ids ) {
                wp_set_post_terms($item['id'], $term_ids, $taxonomy);
            }

            $res[] = [
                'item' => $item,
                'terms' => $term_ids,
            ];
        }

        return $res;
    }

    function get_term_ids($item, $taxonomy){
        $term_ids = [];
        foreach( $item['categories'] as $term_name ) {
            $term_ids[] = $this->get_term_id($term_name, $taxonomy);
        }
        return $term_ids;
    }

    function get_term_id($term_name, $taxonomy, $create = true){
        $term = get_term_by( 'name', $term_name, $taxonomy);
        if( $term ) {
            return $term->term_id;
        } else if( $create ) {
            $new_term = wp_create_term( $term_name, $taxonomy );
            return $new_term['term_id'];
        }
        return null;
    }

    function get_post_id($title, $post_type='post'){
        $q = new \WP_Query([
            'post_type' => $post_type,
            's' => $title,
            'showposts' => 1,
            'fields' => 'ids',
            'no_found_rows' => true,
        ]);
        if( $q->posts ) return $q->posts[0];
        return null;
    }

}