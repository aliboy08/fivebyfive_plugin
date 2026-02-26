<?php
class FF_Plugin_Base_API {

    public $data_key = '';
    public $dir = '';
    public $repo_endpoint = '';

    function get_items($payload){
        
        $repo_items = $this->get_repo_items($payload);

        $site_items = $this->get_site_items();

        $items = [];

        foreach( $repo_items as $repo_item ) {
            
            $item = $repo_item;

            $item['active'] = false;
            $item['installed'] = false;

            if( $site_items ) {
                foreach( $site_items as $site_item ) {
                    if( $item['slug'] === $site_item['slug'] ) {
                        $item['installed'] = true;
                        $item['active'] = $site_item['active'];
                        break;
                    }
                }
            }
            
            $items[] = $item;
        }

        return $items;
    }

    function get_repo_items($payload){

        $refresh = $payload['refresh'] ?? false;

        $cache_key = $this->data_key.'/repo_data';

        if( !$refresh ) {
            $cache = get_transient($cache_key);
            if( $cache ) return $cache;
        }
        
        $request = wp_remote_get( $this->repo_endpoint.'?t='.time(), [
            'timeout' => 10,
            'headers' => [
                'Accept' => 'application/json',
                'Key' => 'N8nFybEdxaeCKDxJTtkY3RSnuiSR3s4a1as',
            ],
        ]);

        $repo_data = json_decode($request['body'], true);

        set_transient($cache_key, $repo_data);

        return $repo_data;
    }

    function get_site_items(){
        $items = get_option($this->data_key);
        if( !$items ) $items = [];
        return $items;
    }

    function get_item($slug){
        foreach( $this->get_site_items() as $item ) {
            if( $item['slug'] === $slug ) return $item;
        }
        return null;
    }

    function create_dirs(){
        if( is_dir( $this->dir ) ) return;
        mkdir($this->dir, 0755, true);
        mkdir($this->dir.'temp', 0755, true);
    }

    function item_add($item){

        $items = $this->get_site_items();
        
        foreach( $items as $module ) {
            if( $module['slug'] === $item['slug'] ) return; // already added
        }

        $item['active'] = false;
        $items[] = $item;

        update_option($this->data_key, $items);
    }

    function item_remove($item){

        $items = [];

        foreach( $this->get_site_items() as $site_item ) {
            if( $site_item['slug'] == $item['slug'] ) continue; 
            $items[] = $site_item;
        }

        update_option($this->data_key, $items);
    }

    function item_update( $new_item ){

        $items = $this->get_site_items();

        foreach( $items as &$item ) {
            if( $item['slug'] == $new_item['slug'] ) {
                $item = $new_item;
            }
        }
        
        update_option($this->data_key, $items);
    }

    function download($file_url) {
        
        $file_path = $this->dir .'temp/'.pathinfo($file_url)['basename'];

        $success = file_put_contents($file_path, fopen($file_url.'?t='.time(), 'r'));
        if( !$success ) return false;
        
        WP_Filesystem();
        $extract_res = unzip_file($file_path, $this->dir);
        
        if( !is_bool($extract_res) && $extract_res->errors ) {
            
            $zip = new \ZipArchive();
            if ($zip && $zip->open($file_path) === TRUE) {
                $zip->extractTo($this->dir);
                $zip->close();
            } else {
                // extract failed
                return false;
            }
        
        }

        // delete zip file after extract
        unlink($file_path);
        
        return $success;
    }
    
    function remove_directory($src) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    $this->remove_directory($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }

}