<?php
$tag = 'span';
$attr = '';

if( $item['link']['url'] ) {
    $tag = 'a';
    $attr = ' href="'. $item['link']['url'] .'"'. ( $item['link']['is_external'] ? ' target="_blank"' : '' );
}

echo '<div class="item item-default">';

    echo '<'. $tag . ' class="item-inner"' . $attr .'>';

        if( $item['text_1'] ) echo '<span class="e1">'. $item['text_1'] .'</span>';
        if( $item['text_2'] ) echo '<span class="e2">'. $item['text_2'] .'</span>';

    echo '</'. $tag .'>';

echo '</div>';