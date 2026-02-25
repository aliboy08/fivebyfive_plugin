<?php
$refresh = $payload['refresh'] ?? false;

if( !$refresh ) {
    $cache = get_transient('ff_modules/repo_data');
    if( $cache ) return $cache;
}

$request = wp_remote_get( 'https://devlibrary2021.wpengine.com/wp-json/ff/v1/plugins/?t='.time(), [
    'timeout' => 10,
    'headers' => [
        'Accept' => 'application/json',
        'Key' => 'N8nFybEdxaeCKDxJTtkY3RSnuiSR3s4a1as',
    ],
]);

$repo_data = json_decode($request['body'], true);

set_transient('ff_modules/repo_data', $repo_data);

return $repo_data;
