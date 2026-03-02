<?php
// $tag = 'span';
// $attr = '';

$link_attr = ff_el_link_attr($item['link']);

echo '<div class="item item-image-accordion'. ( $is_current ? ' current transition_complete' : '' ) .'">';

    echo '<div class="item_inner">';

        echo '<div class="image">';
            echo wp_get_attachment_image($item['image']['id'], $settings['image_size'] );
        echo '</div>';

        echo '<div class="content_con">';

        if( $item['heading'] ) {
            echo '<'. $heading_tag .' class="heading">'. $item['heading'] .'</'. $heading_tag .'>';
        }

        if( $item['description'] ) {
            echo '<div class="description">'. $item['description'] .'</div>';
        }

        if( $item['button_text'] ) {
            echo '<a class="button"'. $link_attr .'>'. $item['button_text'] .'</a>';
        }

        echo '</div>';

    echo '</div>';

echo '</div>';