<?php
class FF_Plugin_Admin_API {

    public $data_key = 'ff_plugin/admin';

    function check_updates(){
       
        $request = wp_remote_get('https://devlibrary2021.wpengine.com/fivebyfive/data.json?t='.time());

        $repo_data = json_decode($request['body'], true);
        
        return [
            'repo_data' => json_decode($request['body'], true),
            'site_data' => get_option('ff_plugin/admin'),
        ];
    }

    function update_dist($payload){
        
        $temp_dir = FF_PLUGIN_DIR.'/temp';
        if( !is_dir($temp_dir) ) {
            mkdir($temp_dir, 0755, true);
        }
        
        $res = $this->download_dist();
        
        return [
            'update_dist' => $payload,
            'res' => $res,
        ];
    }

    function download_dist() {

        $file_url = 'https://devlibrary2021.wpengine.com/fivebyfive/dist.zip';

        $temp_path = FF_PLUGIN_DIR.'/temp';
        $extract_path = FF_PLUGIN_DIR . '/dist';
        
        $file_path = $temp_path.'/'.pathinfo($file_url)['basename'];
        
        $success = file_put_contents($file_path, fopen($file_url.'?t='.time(), 'r'));
        
        if( !$success ) return false;

        $this->remove_directory($extract_path);
        
        WP_Filesystem();
        $extract_res = unzip_file($file_path, $extract_path);
        
        if( !is_bool($extract_res) && $extract_res->errors ) {
            $zip = new \ZipArchive();
            if ($zip && $zip->open($file_path) === TRUE) {
                $zip->extractTo($extract_path);
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