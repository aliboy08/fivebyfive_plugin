<?php
$refresh = $payload['refresh'] ?? false;

$cache_key = 'fivebyfive/manager/modules/repo_data';

if( !$refresh ) {
    $cache = get_transient($cache_key);
    if( $cache ) return $cache;
}

// $url = 'https://devlibrary2021.wpengine.com/wp-json/ff/v1/plugins/?t='.time();
$url = 'https://devlibrary2021.wpengine.com/fivebyfive/modules/modules.json?t='.time();

$request = wp_remote_get( $url, [
    'timeout' => 10,
    'headers' => [
        'Accept' => 'application/json',
        'Key' => 'N8nFybEdxaeCKDxJTtkY3RSnuiSR3s4a1as',
    ],
]);

$repo_data = json_decode($request['body'], true);

set_transient($cache_key, $repo_data);

return $repo_data;
