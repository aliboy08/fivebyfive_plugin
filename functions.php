<?php
function ff_plugin_js_data($var_name, $data){
    if ( is_string( $data ) ) {
        $data = html_entity_decode( $data, ENT_QUOTES, 'UTF-8' );
    } elseif ( is_array( $data ) ) {
        foreach ( $data as $key => $value ) {
            if ( !is_scalar( $value ) ) {
                continue;
            }
            $data[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
        }
    }
    ?><script type="text/javascript">/* <![CDATA[ */var <?=$var_name?> = <?=wp_json_encode($data)?>;/* ]]> */</script><?php
}

function ff_plugin_load_asset($key){
    FF\Plugin\Vite\load_asset($key);
}